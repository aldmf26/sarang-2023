<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CocokanModel extends Model
{
    use HasFactory;

    /**
     * Satu baris biaya untuk setiap no_box.
     *
     * Semua tabel transaksi diringkas sebelum di-join agar modal BK dan gaji
     * tidak berlipat ketika satu box mempunyai lebih dari satu transaksi.
     * Grading dan proses sesudahnya tidak masuk karena penambahan modal berhenti
     * pada sortir.
     */
    private static function boxCostSql(): string
    {
        return "
            SELECT
                bk.no_box,
                bk.pcs_awal,
                bk.gr_awal,
                bk.modal_bk,
                COALESCE(cabut.gaji_cabut, 0) AS gaji_cabut,
                COALESCE(eo.gaji_eo, 0) AS gaji_eo,
                COALESCE(cetak.gaji_cetak, 0) AS gaji_cetak,
                COALESCE(sortir.gaji_sortir, 0) AS gaji_sortir,
                COALESCE(cabut.gaji_cabut, 0)
                    + COALESCE(eo.gaji_eo, 0)
                    + COALESCE(cetak.gaji_cetak, 0)
                    + COALESCE(sortir.gaji_sortir, 0) AS cost_kerja,
                bk.modal_bk
                    + COALESCE(cabut.gaji_cabut, 0)
                    + COALESCE(eo.gaji_eo, 0)
                    + COALESCE(cetak.gaji_cetak, 0)
                    + COALESCE(sortir.gaji_sortir, 0) AS total_modal
            FROM (
                SELECT
                    no_box,
                    SUM(pcs_awal) AS pcs_awal,
                    SUM(gr_awal) AS gr_awal,
                    SUM(gr_awal * hrga_satuan) AS modal_bk
                FROM bk
                WHERE kategori = 'cabut'
                  AND baru = 'baru'
                  AND no_box != 9999
                GROUP BY no_box
            ) AS bk
            LEFT JOIN (
                SELECT no_box, SUM(GREATEST(COALESCE(ttl_rp, 0), 0)) AS gaji_cabut
                FROM cabut
                GROUP BY no_box
            ) AS cabut ON cabut.no_box = bk.no_box
            LEFT JOIN (
                SELECT no_box, SUM(GREATEST(COALESCE(ttl_rp, 0), 0)) AS gaji_eo
                FROM eo
                GROUP BY no_box
            ) AS eo ON eo.no_box = bk.no_box
            LEFT JOIN (
                SELECT
                    cn.no_box,
                    SUM(GREATEST(COALESCE(cn.ttl_rp, 0), 0)) AS gaji_cetak
                FROM cetak_new AS cn
                INNER JOIN kelas_cetak AS kc
                    ON kc.id_kelas_cetak = cn.id_kelas_cetak
                   AND kc.kategori = 'CTK'
                GROUP BY cn.no_box
            ) AS cetak ON cetak.no_box = bk.no_box
            LEFT JOIN (
                SELECT no_box, SUM(GREATEST(COALESCE(ttl_rp, 0), 0)) AS gaji_sortir
                FROM sortir
                GROUP BY no_box
            ) AS sortir ON sortir.no_box = bk.no_box
        ";
    }
    public static function bkstockawal_sum()
    {
        $result = DB::selectOne("SELECT 
        sum(a.pcs_awal) as pcs, 
        sum(a.gr_awal) as gr , 
        sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a
        where a.kategori ='cabut' and a.baru ='baru' 
            ");
        return $result;
    }
    public static function bksedang_proses_sum()
    {
        $result = DB::selectOne("SELECT 
    SUM(sub.pcs) as pcs, 
    SUM(sub.gr) as gr, 
    SUM(sub.ttl_rp) as ttl_rp,
    sum(sub.cost_kerja) as cost_kerja
    FROM (
    SELECT sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr ,sum(b.gr_awal * b.hrga_satuan) as ttl_rp,
           sum(if(a.ttl_rp < 0, 0, a.ttl_rp)) as cost_kerja
    FROM cabut as a
    LEFT JOIN bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
    WHERE a.selesai = 'T' AND a.no_box != 9999 and b.baru = 'baru'
    
    UNION ALL
    
    SELECT 0 as pcs, sum(d.gr_eo_awal) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp,
           sum(if(d.ttl_rp < 0, 0, d.ttl_rp)) as cost_kerja
    FROM eo as d
    LEFT JOIN bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
    WHERE d.selesai = 'T' AND d.no_box != 9999 and e.baru = 'baru'
    ) as sub;");

        return $result;
    }

    public static function bksisapgws()
    {
        $result = DB::selectOne("SELECT 
            SUM(fs.pcs_awal) as pcs, 
            SUM(fs.gr_awal) as gr, 
            SUM(fs.gr_awal * bk.hrga_satuan) as ttl_rp
        FROM formulir_sarang fs
        INNER JOIN bk ON bk.no_box = fs.no_box
        WHERE fs.kategori = 'cabut'
          AND bk.baru = 'baru'
          AND NOT EXISTS (SELECT 1 FROM cabut WHERE cabut.no_box = fs.no_box)
          AND NOT EXISTS (SELECT 1 FROM eo WHERE eo.no_box = fs.no_box)
    ");

        return $result;
    }

    public static function bkakhir()
    {
        $result = DB::selectOne("SELECT 
                                SUM(cost) as cost_kerja, 
                                SUM(gr) as gr, 
                                SUM(ttl_rp) as ttl_rp
                            FROM (
                                SELECT 
                                    a.ttl_rp as cost, 
                                    a.gr_eo_akhir as gr, 
                                    (b.hrga_satuan * b.gr_awal) as ttl_rp
                                FROM eo as a 
                                LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                                WHERE a.selesai = 'Y' 
                                AND b.baru = 'baru'
                                AND a.no_box IN (
                                    SELECT c.no_box 
                                    FROM formulir_sarang as c 
                                    WHERE c.kategori = 'sortir'
                                )
                                
                                UNION ALL
                                
                                SELECT 
                                    a.ttl_rp as cost, 
                                    a.gr_akhir as gr, 
                                    (b.hrga_satuan * b.gr_awal) as ttl_rp
                                FROM cabut as a 
                                LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                                WHERE a.selesai = 'Y' 
                                AND b.baru = 'baru' 
                                AND a.pcs_akhir = 0
                                AND a.no_box IN (
                                    SELECT c.no_box 
                                    FROM formulir_sarang as c 
                                    WHERE c.kategori = 'sortir'
                                )
                            ) as a;
        ");

        return $result;
    }

    public static function bkselesai_siap_ctk_diserahkan_sum()
    {
        $result = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp, sum(a.cost) as cost_kerja
        FROM (
            SELECT a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_akhir as gr, (b.hrga_satuan * b.gr_awal) as ttl_rp
            FROM cabut as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal != 0

            UNION ALL

            SELECT a.ttl_rp as cost, 0 as pcs, a.gr_eo_akhir as gr, (b.hrga_satuan * b.gr_awal) as ttl_rp
            FROM eo as a 
            LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            WHERE a.selesai = 'Y' AND b.baru = 'baru'

            UNION ALL 
            SELECT a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_akhir as gr, (b.hrga_satuan * b.gr_awal) as ttl_rp
            FROM cabut as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal = 0

        ) as a;

        ");

        return $result;
    }

    public static function cetak_stok_awal()
    {
        $result = DB::selectOne("SELECT a.no_box, b.name, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(c.hrga_satuan  * c.gr_awal) as ttl_rp, e.name as pgws,
                    sum(COALESCE(d.ttl_rp,0) + COALESCE(g.ttl_rp,0)) as cost_kerja, 
                    c.nm_partai, c.pcs_awal as pcs_bk, (d.gr_akhir * f.rp_gr) as cost_op, z.cost_cu
                FROM formulir_sarang as a 
                left join users as b on b.id = a.id_penerima
                left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
                left join cabut as d on d.no_box = a.no_box
                left join eo as g on g.no_box = a.no_box
                left join oprasional as f on f.bulan = d.bulan_dibayar
                left join users as e on e.id = a.id_pemberi
                left join (
                        SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CU'
                        group by a.no_box
                    ) as z on z.no_box = a.no_box
                WHERE a.kategori = 'cetak'   
                
        ");

        return $result;
    }

    public static function cetak_proses()
    {
        $result = DB::selectOne("SELECT sum(a.ttl_rp) as cost_kerja,sum(a.pcs_awal_ctk) as pcs, sum(a.gr_awal_ctk) as gr, 
        sum(COALESCE(d.gr_awal * d.hrga_satuan,0) + COALESCE(c.ttl_rp,0) + COALESCE(e.ttl_rp,0)) as ttl_rp, sum(a.ttl_rp) as cost_kerja
            FROM cetak_new as a 
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            left join cabut as c on c.no_box = a.no_box
            left join eo as e on e.no_box = a.no_box
            where a.selesai = 'T' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            order by a.no_box ASC;
        ");

        return $result;
    }

    public static function cetak_stok()
    {
        $result = DB::selectOne("SELECT sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(COALESCE(c.hrga_satuan  * c.gr_awal,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0)) as ttl_rp
                FROM formulir_sarang as a 
                left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
                left join cabut as d on d.no_box = a.no_box and a.kategori = 'cetak'
                left join eo as e on e.no_box = a.no_box and a.kategori = 'cetak'
                WHERE a.kategori = 'cetak'   
                and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 0) and a.no_box != 0
        ");

        return $result;
    }

    public static function cetak_selesai()
    {
        $result = DB::selectOne("SELECT sum(COALESCE(a.pcs_akhir,0) + COALESCE(a.pcs_tdk_cetak,0)) as pcs, sum(COALESCE(a.gr_akhir,0) + COALESCE(a.gr_tdk_cetak,0)) as gr, sum(COALESCE(e.gr_awal * e.hrga_satuan,0) + COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0)) as ttl_rp, sum(a.ttl_rp) as cost_kerja
        FROM cetak_new as a 
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        join formulir_sarang as b on b.no_box = a.no_box and b.kategori = 'cetak'
        left join cabut as c on c.no_box = a.no_box
        left join eo as d on d.no_box = a.no_box
        where a.selesai = 'Y' and g.kategori = 'CTK' and e.baru = 'baru';
        ");

        return $result;
    }

    public static function stock_sortir_awal()
    {
        $result = DB::selectOne("SELECT SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr, 
        SUM(COALESCE(b.gr_awal * b.hrga_satuan,0) + COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp)) as ttl_rp
        FROM formulir_sarang as a 
        LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join cabut as c on c.no_box = a.no_box
        left join eo as d on d.no_box = a.no_box
        
        left join(
        SELECT a.no_box, sum(a.ttl_rp) as ttl_rp
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    where b.kategori = 'CTK'
                    group by a.no_box
        ) as e on e.no_box = a.no_box
        
        
        WHERE b.baru = 'baru' AND a.kategori = 'sortir';
        ");

        return $result;
    }
    public static function sortir_proses()
    {
        $result = DB::selectOne("SELECT SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr, 
        SUM(COALESCE(b.hrga_satuan * b.gr_awal,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0)) as ttl_rp, 
        sum(a.ttl_rp) as cost_kerja
            FROM sortir as a 
            LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            JOIN formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'

            left join cabut as d on d.no_box = a.no_box
            left join eo as e on e.no_box = a.no_box
            
            left join(
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CTK'
                        group by a.no_box
            ) as f on f.no_box = a.no_box


            WHERE a.selesai = 'T' AND a.id_anak != 0;
        ");

        return $result;
    }

    public static function stock_sortir()
    {
        $result = DB::selectOne("SELECT SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr, SUM(COALESCE(b.gr_awal * b.hrga_satuan) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0)) as ttl_rp
                FROM formulir_sarang as a 
                LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                
                left join cabut as d on d.no_box = a.no_box
                left join eo as e on e.no_box = a.no_box
                
                left join(
                SELECT a.no_box, sum(a.ttl_rp) as ttl_rp
                            FROM cetak_new as a 
                            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                            where b.kategori = 'CTK'
                            group by a.no_box
                ) as f on f.no_box = a.no_box


                WHERE b.baru = 'baru' AND b.kategori = 'cabut'  AND a.kategori = 'sortir' AND a.no_box NOT IN (SELECT b.no_box FROM sortir as b WHERE b.id_anak != 0)
        ");

        return $result;
    }

    public static function sortir_akhir()
    {
        $result = DB::selectOne("SELECT SUM(a.pcs_akhir) as pcs, SUM(a.gr_akhir) as gr, 
        sum(b.hrga_satuan * b.gr_awal) as cost_bk,
        SUM(COALESCE(b.hrga_satuan * b.gr_awal,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0)) as ttl_rp,
        sum(a.ttl_rp) as cost_kerja
                    FROM sortir as a 
                    LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                    JOIN formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                    left join cabut as d on d.no_box = a.no_box
                    left join eo as e on e.no_box = a.no_box
                    left join(
                    SELECT a.no_box, sum(a.ttl_rp) as ttl_rp
                                FROM cetak_new as a 
                                left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                                where b.kategori = 'CTK'
                                group by a.no_box
                    ) as f on f.no_box = a.no_box
                    WHERE  a.selesai = 'Y' AND b.baru = 'baru' and a.no_box in (SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'grade');
        ");

        return $result;
    }

    public static function akhir_sortir()
    {
        $result = DB::selectOne("SELECT b.nm_partai, a.no_box,
        sum(COALESCE(a.pcs_awal,0)) as pcs, 
        sum(COALESCE(a.gr_awal,0)) as gr, 
        sum((b.gr_awal * b.hrga_satuan) ) as ttl_rp 
            
            FROM formulir_sarang as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            where a.kategori ='grade';
        
        ");

        return $result;
    }

    public static function cetak_proses_balance()
    {
        $boxCost = self::boxCostSql();
        $result = DB::selectOne("
            SELECT
                SUM(a.pcs) AS pcs,
                SUM(a.gr) AS gr,
                SUM(cost.modal_bk) AS ttl_rp,
                SUM(cost.gaji_cabut + cost.gaji_eo + cost.gaji_cetak) AS cost_kerja
            FROM (
                SELECT
                    cn.no_box,
                    SUM(cn.pcs_awal_ctk) AS pcs,
                    SUM(cn.gr_awal_ctk) AS gr
                FROM cetak_new AS cn
                INNER JOIN kelas_cetak AS kc
                    ON kc.id_kelas_cetak = cn.id_kelas_cetak
                   AND kc.kategori = 'CTK'
                WHERE cn.selesai = 'T'
                  AND cn.id_anak != 0
                GROUP BY cn.no_box
            ) AS a
            INNER JOIN ($boxCost) AS cost ON cost.no_box = a.no_box
        ");

        return $result;
    }

    public static function bksedang_selesai_sum()
    {
        $result = DB::selectOne("SELECT  c.name, a.no_box, b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_akhir) as gr ,sum(b.gr_awal * b.hrga_satuan) as ttl_rp, 
        sum(a.ttl_rp) as cost_kerja
        FROM cabut as a
        LEFT JOIN bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        WHERE a.selesai = 'Y' and a.formulir = 'Y' and a.no_box not in(SELECT a.no_box FROM formulir_sarang as a group by a.no_box) AND a.no_box != 9999 and b.baru = 'baru'
        group by a.no_box
        
        UNION ALL
        
        SELECT c.name, d.no_box, e.nm_partai, 0 as pcs, sum(d.gr_eo_akhir) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp,
        sum(d.ttl_rp) as cost_kerja
        FROM eo as d
        LEFT JOIN bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
        left join users as c on c.id = d.id_pengawas
        WHERE d.selesai = 'Y' and d.no_box not in(SELECT a.no_box FROM formulir_sarang as a group by a.no_box) AND d.no_box != 9999 and e.baru = 'baru'
    ");

        return $result;
    }

    public static function cetak_stok_balance()
    {
        $boxCost = self::boxCostSql();
        $result = DB::selectOne("
            SELECT
                SUM(a.pcs) AS pcs,
                SUM(a.gr) AS gr,
                SUM(cost.modal_bk) AS ttl_rp,
                SUM(cost.gaji_cabut + cost.gaji_eo) AS cost_kerja
            FROM (
                SELECT
                    fs.no_box,
                    SUM(fs.pcs_awal) AS pcs,
                    SUM(fs.gr_awal) AS gr
                FROM formulir_sarang AS fs
                WHERE fs.kategori = 'cetak'
                  AND fs.no_box != 0
                  AND NOT EXISTS (
                      SELECT 1
                      FROM cetak_new AS cn
                      WHERE cn.no_box = fs.no_box
                        AND cn.id_anak != 0
                  )
                GROUP BY fs.no_box
            ) AS a
            INNER JOIN ($boxCost) AS cost ON cost.no_box = a.no_box
        ");

        return $result;
    }

    public static function sortir_proses_balance()
    {
        $boxCost = self::boxCostSql();
        $result = DB::selectOne("
            SELECT
                SUM(a.pcs) AS pcs,
                SUM(a.gr) AS gr,
                SUM(cost.modal_bk) AS ttl_rp,
                SUM(cost.cost_kerja) AS cost_kerja,
                0 AS cu
            FROM (
                SELECT
                    s.no_box,
                    SUM(s.pcs_awal) AS pcs,
                    SUM(s.gr_awal) AS gr
                FROM sortir AS s
                WHERE s.selesai = 'T'
                  AND s.id_anak != 0
                GROUP BY s.no_box
            ) AS a
            INNER JOIN ($boxCost) AS cost ON cost.no_box = a.no_box
        ");

        return $result;
    }


    public static function sortir_stock_balance()
    {
        $boxCost = self::boxCostSql();
        $result = DB::selectOne("
            SELECT
                SUM(a.pcs) AS pcs,
                SUM(a.gr) AS gr,
                SUM(cost.modal_bk) AS ttl_rp,
                SUM(cost.gaji_cabut + cost.gaji_eo + cost.gaji_cetak) AS cost_kerja
            FROM (
                SELECT
                    fs.no_box,
                    SUM(fs.pcs_awal) AS pcs,
                    SUM(fs.gr_awal) AS gr
                FROM formulir_sarang AS fs
                WHERE fs.kategori = 'sortir'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM sortir AS s
                      WHERE s.no_box = fs.no_box
                        AND s.id_anak != 0
                  )
                GROUP BY fs.no_box
            ) AS a
            INNER JOIN ($boxCost) AS cost ON cost.no_box = a.no_box
        ");

        return $result;
    }


    public static function grading_sisa()
    {
        $return =  DB::selectOne("SELECT a.no_box_sortir, sum(b.pcs_awal - d.pcs) as pcs , sum(b.gr_awal - d.gr) as gr , sum(g.ttl_rp) as cost_bk, sum(COALESCE(g.cost_cbt,0) + COALESCE(g.cost_eo,0) + COALESCE(g.cost_ctk,0) + COALESCE(g.cost_str,0) ) as cost_kerja
        FROM grading as a 
        left join formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade' 
        JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'cabut' 
        LEFT JOIN( select no_box_sortir as no_box,sum(pcs) as pcs,sum(gr) as gr from grading group by no_box_sortir ) as d on d.no_box = a.no_box_sortir 
        left join(
        SELECT a.no_box, (a.gr_awal * a.hrga_satuan) as ttl_rp, b.ttl_rp as cost_cbt, c.ttl_rp as cost_eo, d.cost_ctk, e.ttl_rp as cost_str, f.cost_cu
            FROM bk as a 
            left JOIN cabut as b on b.no_box = a.no_box
            left JOIN eo as c on c.no_box = a.no_box
            left join (
                SELECT a.no_box, sum(a.ttl_rp) as cost_ctk 
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CTK'
                        group by a.no_box
            ) as d on d.no_box = a.no_box
            left join sortir as e on e.no_box = a.no_box
            left join (
                SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CU'
                        group by a.no_box
            ) as f on f.no_box = a.no_box
            where a.baru = 'baru' and a.kategori ='cabut'
            group by a.no_box
        ) as g on g.no_box = a.no_box_sortir
        WHERE a.selesai = 'T' 
        having gr != 0;
        ");
        return $return;
    }

    public static function gradingOne()
    {
        return  DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp,sum(a.pcs) as pcs, sum(a.gr) as gr ,
        sum(a.cost_bk) as cost_bk, sum(a.cost_kerja) as cost_kerja, sum(a.cost_cu) as cost_cu, sum(a.cost_op) as cost_op
        FROM grading_partai as a 
        where a.grade != 'susut' and a.formulir = 'Y'
        ");
    }
    public static function gradingProsesOne()
    {
        return  DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp,sum(a.pcs) as pcs, sum(a.gr) as gr ,
        sum(a.cost_bk) as cost_bk, sum(a.cost_kerja) as cost_kerja, sum(a.cost_cu) as cost_cu, sum(a.cost_op) as cost_op
        FROM grading_partai as a 
        where a.grade != 'susut' and a.formulir = 'T'
        ");
    }

    public static function gradingSisaOne()
    {
        $boxCost = self::boxCostSql();
        return DB::selectOne("
            SELECT
                SUM(a.pcs) AS pcs,
                SUM(a.gr) AS gr,
                SUM(cost.modal_bk) AS bk_rp,
                SUM(cost.total_modal) AS cost_bk,
                SUM(cost.modal_bk) AS modal,
                SUM(cost.cost_kerja) AS cost_kerja
            FROM (
                SELECT
                    fs.no_box,
                    SUM(fs.pcs_awal) AS pcs,
                    SUM(fs.gr_awal) AS gr
                FROM formulir_sarang AS fs
                WHERE fs.kategori = 'grade'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM grading AS g
                      WHERE g.no_box_sortir = fs.no_box
                        AND g.no_invoice IS NOT NULL
                  )
                GROUP BY fs.no_box
            ) AS a
            INNER JOIN ($boxCost) AS cost ON cost.no_box = a.no_box
        ");
    }

    public static function sisa_belum_wip1()
    {
        return DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(COALESCE(a.cost_bk,0) + COALESCE(a.cost_kerja,0) + COALESCE(a.cost_op,0)) as ttl_rp , sum(a.cost_bk) as modal
FROM grading_partai as a 
where a.formulir ='Y' and a.cek_qc = 'T';");
    }
    public static function wip1_akhir()
    {
        return DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(COALESCE(a.cost_bk,0) + COALESCE(a.cost_kerja,0) + COALESCE(a.cost_op,0)) as ttl_rp
FROM grading_partai as a 
where a.formulir ='Y' and a.cek_qc = 'Y';");
    }
    public static function sisa_belum_qc()
    {
        return DB::selectOne("SELECT a.box_pengiriman, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(b.ttl_rp) as ttl_rp, sum(b.cost_bk) as cost_bk
FROM qc as a 
left join (
	SELECT b.box_pengiriman, sum(COALESCE(b.cost_bk,0) + COALESCE(b.cost_kerja,0) + COALESCE(b.cost_op,0)) as ttl_rp, sum(b.cost_bk) as cost_bk
    FROM grading_partai as b 
    group by b.box_pengiriman
) as b on b.box_pengiriman = a.box_pengiriman
where a.wip2 ='T';");
    }
    public static function qc_akhir()
    {
        return DB::selectOne("SELECT a.box_pengiriman, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr_awal, sum(a.gr_akhir) as gr, sum(b.ttl_rp) as ttl_rp
        FROM qc as a 
        left join (
            SELECT b.box_pengiriman, sum(COALESCE(b.cost_bk,0) + COALESCE(b.cost_kerja,0) + COALESCE(b.cost_op,0)) as ttl_rp
            FROM grading_partai as b 
            group by b.box_pengiriman
        ) as b on b.box_pengiriman = a.box_pengiriman
        where a.wip2 ='Y';");
    }
    public static function wip2proses()
    {
        return DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(COALESCE(a.cost_bk,0) + COALESCE(a.cost_kerja,0) + COALESCE(a.cost_op,0)) as ttl_rp, sum(a.cost_bk) as modal
        FROM grading_partai as a 
        
        join (
            SELECT c.no_box
                FROM formulir_sarang as c 
                where c.selesai = 'Y' and c.kategori = 'wip2'
                group by c.no_box
        ) as c on c.no_box = a.box_pengiriman
        where a.formulir ='Y' and a.cek_qc = 'Y' and a.sudah_kirim = 'T' and a.box_pengiriman not in (SELECT d.no_box FROM pengiriman as d group by d.no_box);");
    }
    public static function wip2akhir()
    {
        return DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(COALESCE(a.cost_bk,0) + COALESCE(a.cost_kerja,0) + COALESCE(a.cost_op,0)) as ttl_rp
        FROM grading_partai as a 
        where a.formulir ='Y' and a.cek_qc = 'Y' and a.sudah_kirim = 'Y' ;");
    }
    public static function pengiriman_proses()
    {
        return DB::selectOne("SELECT  a.cost_op,a.cost_cu,sum(a.pcs) as pcs, sum(a.gr) as gr,sum(COALESCE(b.cost_bk,0) + COALESCE(b.cost_kerja,0) + COALESCE(b.cost_op,0)) as ttl_rp, sum(b.cost_bk) as cost_bk, sum(b.cost_kerja) as cost_kerja, sum(a.cost_cu) as cost_cu, sum(b.cost_op) as cost_op
        FROM pengiriman as a
        left join (
            SELECT b.box_pengiriman , sum(b.cost_bk) as cost_bk, sum(b.cost_op) as cost_op, sum(b.cost_kerja) as cost_kerja
            FROM grading_partai as b 
            where b.sudah_kirim = 'T'
            group by b.box_pengiriman
        ) as b on b.box_pengiriman = a.no_box
        where a.selesai ='T';
        ");
    }
    public static function summarybk_sisa()
    {
        return DB::selectOne("SELECT sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(a.gr_awal * a.hrga_satuan) as ttl_rp FROM bk as a
        WHERE a.penerima = 0 and a.kategori = 'cabut' and a.formulir = 'T';
        ");
    }
}
