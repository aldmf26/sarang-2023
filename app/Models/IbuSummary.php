<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IbuSummary extends Model
{
    use HasFactory;


    public static function bkstockawal_sum()
    {
        $result = DB::selectOne("SELECT 
        sum(a.pcs_awal) as pcs, 
        sum(a.gr_awal) as gr , 
        sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a
        where a.kategori ='cabut' and a.baru ='baru' 
        AND a.baru = 'baru'
            ");
        return $result;
    }


    public static function bkstock_sum()
    {
        $result = DB::selectOne("SELECT sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, 
        sum(a.gr_awal * a.hrga_satuan) as ttl_rp
            FROM bk as a 
            where a.kategori ='cabut' and a.baru ='baru' 
            AND NOT EXISTS (SELECT 1 FROM cabut AS b WHERE b.no_box = a.no_box) 
            AND NOT EXISTS (SELECT 1 FROM eo AS c WHERE c.no_box = a.no_box)
            AND a.baru = 'baru'
            ");
        return $result;
    }


    public static function bksedang_proses_sum()
    {
        $result = DB::selectOne("SELECT 
    SUM(sub.pcs) as pcs, 
    SUM(sub.gr) as gr, 
    SUM(sub.ttl_rp) as ttl_rp
FROM (
    SELECT sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr ,sum(b.gr_awal * b.hrga_satuan) as ttl_rp
    FROM cabut as a
    LEFT JOIN bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
    WHERE a.selesai = 'T' AND a.no_box != 9999 and b.baru = 'baru'
    
    UNION ALL
    
    SELECT 0 as pcs, sum(d.gr_eo_awal) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp
    FROM eo as d
    LEFT JOIN bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
    WHERE d.selesai = 'T' AND d.no_box != 9999 and e.baru = 'baru'
) as sub;");

        return $result;
    }


    public static function bkselesai_siap_ctk_sum()
    {
        $result = DB::selectOne("SELECT sum(a.ttl_rp) as cost_kerja,a.no_box, b.nm_partai, sum(a.pcs_akhir) as pcs, sum(a.gr_akhir) as gr, sum(COALESCE(b.hrga_satuan * b.gr_awal,0)) as ttl_rp
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        where a.selesai = 'Y' and a.formulir = 'T' and a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'cetak') and a.pcs_awal != 0 and b.baru = 'baru'
       
        ");

        return $result;
    }


    public static function bkselesai_siap_ctk_diserahkan_sum()
    {
        $result = DB::selectOne("SELECT sum(a.ttl_rp) as cost_kerja,a.no_box,sum(a.pcs_akhir) as pcs, sum(a.gr_akhir) as gr, sum(COALESCE(b.hrga_satuan * b.gr_awal,0)) as ttl_rp
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        where a.selesai = 'Y'  and a.no_box  in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'cetak') and a.pcs_awal != 0 and b.baru = 'baru'
        ");

        return $result;
    }


    public static function bkselesai_siap_str_sum()
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
                                    AND a.no_box NOT IN (
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
                                    AND a.no_box NOT IN (
                                        SELECT c.no_box 
                                        FROM formulir_sarang as c 
                                        WHERE c.kategori = 'sortir'
                                    )
                                ) as a;
        ");

        return $result;
    }


    public static function bkselesai_siap_str_diserahkan_sum()
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

    public static function cetak_stok()
    {
        $result = DB::selectOne("SELECT sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(c.hrga_satuan  * c.gr_awal) as ttl_rp
                FROM formulir_sarang as a 
                left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
                WHERE a.kategori = 'cetak'   
                and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 0) and a.no_box != 0
        ");

        return $result;
    }
    public static function cetak_stok_awal()
    {
        $result = DB::selectOne("SELECT a.no_box, b.name, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(c.hrga_satuan  * c.gr_awal) as ttl_rp, e.name as pgws,
                    d.ttl_rp as cost_cbt, c.nm_partai, c.pcs_awal as pcs_bk, (d.gr_akhir * f.rp_gr) as cost_op, z.cost_cu
                FROM formulir_sarang as a 
                left join users as b on b.id = a.id_penerima
                left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
                left join cabut as d on d.no_box = a.no_box
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
        $result = DB::selectOne("SELECT sum(a.ttl_rp) as cost_kerja,sum(a.pcs_awal_ctk) as pcs, sum(a.gr_awal_ctk) as gr, sum(d.gr_awal * d.hrga_satuan) as ttl_rp
            FROM cetak_new as a 
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            where a.selesai = 'T' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            order by a.no_box ASC;
        ");

        return $result;
    }

    public static function cetak_selesai()
    {
        $result = DB::selectOne("SELECT sum(a.pcs_akhir + a.pcs_tdk_cetak) as pcs, sum(a.gr_akhir + a.gr_tdk_cetak) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp, sum(a.ttl_rp) as cost_kerja
        FROM cetak_new as a 
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        where a.selesai = 'Y' and g.kategori = 'CTK' and e.baru = 'baru'
        and a.formulir = 'T'  and a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'sortir');
        ");

        return $result;
    }
    public static function tdk_cetak_selesai_diserahkan()
    {
        $result = DB::selectOne("SELECT sum(a.pcs_tdk_cetak) as pcs, sum(a.gr_tdk_cetak) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp, sum(a.ttl_rp) as cost_kerja
        FROM cetak_new as a 
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        
        where a.selesai = 'Y' and g.kategori = 'CTK' and e.baru = 'baru' and (a.pcs_tdk_cetak + a.gr_tdk_cetak) != 0
        and a.formulir = 'T'  and a.no_box  in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'sortir') 
        ");

        return $result;
    }
    public static function cetak_selesai_diserahkan()
    {
        $result = DB::selectOne("SELECT sum(a.pcs_akhir) as pcs, sum(a.gr_akhir ) as gr,sum(e.gr_awal * e.hrga_satuan) as ttl_rp, sum(a.ttl_rp) as cost_kerja
        FROM cetak_new as a 
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        where a.selesai = 'Y' and g.kategori = 'CTK' and e.baru = 'baru' 
        and a.formulir = 'T'  and a.no_box  in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'sortir') 
        ");

        return $result;
    }

    public static function stock_sortir()
    {
        $result = DB::selectOne("SELECT 
                    SUM(a.pcs_awal) as pcs, 
                    SUM(a.gr_awal) as gr, 
                    SUM(b.gr_awal * b.hrga_satuan) as ttl_rp
                FROM 
                    formulir_sarang as a 
                LEFT JOIN 
                    bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                WHERE 
                    b.baru = 'baru' 
                    AND b.kategori = 'cabut' 
                    AND a.kategori = 'sortir' 
                    AND a.no_box NOT IN (SELECT b.no_box FROM sortir as b WHERE b.id_anak != 0)
                

        ");

        return $result;
    }
    public static function stock_sortir_awal()
    {
        $result = DB::selectOne("SELECT 
    SUM(a.pcs_awal) as pcs, 
    SUM(a.gr_awal) as gr, 
    SUM(b.gr_awal * b.hrga_satuan) as ttl_rp
FROM 
    formulir_sarang as a 
LEFT JOIN 
    bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
WHERE 
    b.baru = 'baru' 
    AND a.kategori = 'sortir';
        ");

        return $result;
    }


    public static function sortir_proses()
    {
        $result = DB::selectOne("SELECT 
                SUM(a.pcs_awal) as pcs, 
                SUM(a.gr_awal) as gr, 
                SUM(b.hrga_satuan * b.gr_awal) as ttl_rp
            FROM 
                sortir as a 
            LEFT JOIN 
                bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            JOIN 
                formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
            WHERE 
                a.selesai = 'T' 
                AND a.id_anak != 0;
        ");

        return $result;
    }


    public static function sortir_selesai()
    {
        $result = DB::selectOne("SELECT 
                    SUM(a.pcs_akhir) as pcs, 
                    SUM(a.gr_akhir) as gr, 
                    SUM(b.hrga_satuan * b.gr_awal) as ttl_rp,
                    sum(a.ttl_rp) as cost_kerja
                FROM 
                    sortir as a 
                LEFT JOIN 
                    bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                JOIN 
                    formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                WHERE 
                    a.no_box NOT IN (SELECT b.no_box FROM formulir_sarang as b WHERE b.kategori = 'grade') 
                    AND a.selesai = 'Y' 
                    AND b.baru = 'baru'
        ");

        return $result;
    }
    public static function sortir_selesai_diserahkan()
    {
        $result = DB::selectOne("SELECT 
                        SUM(a.pcs_akhir) as pcs, 
                        SUM(a.gr_akhir) as gr, 
                        SUM(b.hrga_satuan * b.gr_awal) as ttl_rp,
                        sum(a.ttl_rp) as cost_kerja
                    FROM 
                        sortir as a 
                    LEFT JOIN 
                        bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                    JOIN 
                        formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                    WHERE 
                        a.no_box IN (SELECT b.no_box FROM formulir_sarang as b WHERE b.kategori = 'grade')
                        AND a.selesai = 'Y' 
                        AND b.baru = 'baru';
        ");

        return $result;
    }


    public static function grading_stock()
    {
        $result = DB::selectOne("SELECT b.nm_partai, a.no_box, l.name, sum(COALESCE(a.pcs_awal,0)) as pcs, sum(COALESCE(a.gr_awal,0)) as gr, 
            
            sum((b.gr_awal * b.hrga_satuan) ) as ttl_rp , 
            sum(b.hrga_satuan * b.gr_awal) as cost_bk, 
            sum(e.ttl_rp) as cost_cbt, 
            sum(h.ttl_rp) as cost_eo, 
            sum(f.ttl_rp) as cost_ctk, 
            sum(g.ttl_rp) as cost_str,
            sum(COALESCE(i.rp_gr * e.gr_akhir,0) + COALESCE(f.cost_op_ctk,0) + COALESCE(g.gr_akhir * j.rp_gr,0) + COALESCE(h.gr_eo_akhir * k.rp_gr,0)) as cost_op, z.cost_cu
            FROM formulir_sarang as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            left join (
            SELECT c.no_box_sortir as no_box, sum(c.pcs) as pcs_grading, sum(c.gr) as gr_grading
                FROM grading as c
                group by c.no_box_sortir
            ) as c on c.no_box = a.no_box

            left join cabut as e on e.no_box = a.no_box
            left join oprasional as i on i.bulan = e.bulan_dibayar
            

            left join (
                SELECT d.no_box, d.ttl_rp, (d.gr_akhir * i.rp_gr) as cost_op_ctk
                FROM cetak_new as d 
                left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                left join oprasional as i on i.bulan = d.bulan_dibayar 
                where h.kategori = 'CTK'
            ) as f on f.no_box = a.no_box

            left join (
                    SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    where b.kategori = 'CU'
                    group by a.no_box
                ) as z on z.no_box = a.no_box

            left join sortir as g on g.no_box = a.no_box
            left join oprasional as j on j.bulan = g.bulan
            
            left join eo as h on h.no_box = a.no_box
            left join oprasional as k on k.bulan = h.bulan_dibayar
            left join users as l on l.id = a.id_penerima

            where a.kategori ='grade';
        
        ");

        return $result;
    }
    public static function pengiriman()
    {
        $result = DB::selectOne("SELECT sum(a.pcs) as pcs , sum(a.gr) as gr , sum(a.gr * a.rp_gram) as total_rp
                FROM pengiriman as a;");

        return $result;
    }
}
