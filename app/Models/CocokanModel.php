<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CocokanModel extends Model
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
    SELECT sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr ,sum(b.gr_awal * b.hrga_satuan) as ttl_rp, sum(if(a.ttl_rp < 0 , 0 , a.ttl_rp)) as cost_kerja
    FROM cabut as a
    LEFT JOIN bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
    WHERE a.selesai = 'T' AND a.no_box != 9999 and b.baru = 'baru'
    
    UNION ALL
    
    SELECT 0 as pcs, sum(d.gr_eo_awal) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp, sum(if(d.ttl_rp < 0 , 0 , d.ttl_rp)) as cost_kerja
    FROM eo as d
    LEFT JOIN bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
    WHERE d.selesai = 'T' AND d.no_box != 9999 and e.baru = 'baru'
    ) as sub;");

        return $result;
    }

    public static function bksisapgws()
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
        $result = DB::selectOne("SELECT sum(a.pcs_awal_ctk) as pcs, sum(a.gr_awal_ctk) as gr , sum(COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0)) as cost_kerja, sum(b.gr_awal + b.hrga_satuan) as ttl_rp
        FROM (
            SELECT a.no_box, a.pcs_awal_ctk, a.gr_awal_ctk FROM cetak_new as a 
            LEFT join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where a.selesai = 'T' and a.id_anak != 0  and b.kategori = 'CTK'
            group by a.no_box
        ) as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join cabut as c on c.no_box = a.no_box
        left join eo as d on d.no_box = a.no_box
        where b.baru = 'baru';
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
        $result = DB::selectOne("SELECT a.no_box, c.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, 
        sum(c.hrga_satuan  * c.gr_awal) as ttl_rp, 
        sum(COALESCE(d.ttl_rp,0) + COALESCE(f.ttl_rp,0)) as cost_kerja
        FROM 
        (   SELECT a.no_box, a.pcs_awal, a.gr_awal
        	FROM formulir_sarang as a
         	where a.kategori = 'cetak' and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 				0) and a.no_box != 0
         	group by a.no_box
        ) as a 
        left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
        left join cabut as d on d.no_box = a.no_box
        left join eo as f on f.no_box = a.no_box
        where c.baru = 'baru';
        ");

        return $result;
    }

    public static function sortir_proses_balance()
    {
        $result = DB::selectOne("SELECT a.no_box, b.nm_partai, g.name, SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr, SUM(b.hrga_satuan * b.gr_awal) as ttl_rp, 
        sum( COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) ) as cost_kerja,
        sum(z.ttl_rp) as cu
            FROM sortir as a 
            LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            JOIN formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
            
            left join cabut as d on d.no_box = a.no_box
            
            
            
			left join eo as e on e.no_box = a.no_box
            
left join (
	SELECT a.no_box, sum(a.ttl_rp) as ttl_rp
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
    		
            where b.kategori = 'CTK'
            group by a.no_box
) as f on f.no_box = a.no_box
left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    where b.kategori = 'CU'
                    group by a.no_box
        ) as z on z.no_box = a.no_box
left join users as g on g.id = a.id_pengawas
            
            WHERE a.selesai = 'T' AND a.id_anak != 0
            order by g.name ASC;
        ");

        return $result;
    }


    public static function sortir_stock_balance()
    {
        $result = DB::selectOne("SELECT a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, SUM(b.gr_awal * b.hrga_satuan) as ttl_rp, sum(COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0)) as cost_kerja
        FROM (
            SELECT a.no_box, a.pcs_awal, a.gr_awal
            FROM formulir_sarang as a
            where a.kategori ='sortir' and  a.no_box NOT IN (SELECT b.no_box FROM sortir as b WHERE b.id_anak != 0)
            group by a.no_box
        ) as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join cabut as c on c.no_box = a.no_box 
        left join eo as d on d.no_box = a.no_box
        left join (
                    SELECT a.no_box, sum(a.ttl_rp) as ttl_rp
                            FROM cetak_new as a 
                            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                            where b.kategori = 'CTK' and a.selesai = 'Y'
                            group by a.no_box
                ) as e on e.no_box = a.no_box
                
        where b.baru ='baru';
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
        where a.grade != 'susut'
        ");
    }

    public static function gradingSisaOne()
    {
        return  DB::selectOne("SELECT sum(a.pcs_awal) pcs , 
        sum(b.gr_awal * b.hrga_satuan) as bk_rp,
        sum(a.gr_awal) as gr, sum(COALESCE(b.gr_awal * b.hrga_satuan,0) + COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0)  ) as cost_bk, sum(b.gr_awal * b.hrga_satuan) as modal
        FROM formulir_sarang as a 
        left join bk as b on b.no_box = a.no_box and b.kategori ='cabut'
        left join cabut as c on c.no_box = a.no_box
        left join eo as d on d.no_box = a.no_box
        left join sortir as e on e.no_box = a.no_box
        left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp 
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori = 'CTK'
            group by a.no_box
        ) as f on f.no_box = a.no_box

        where a.no_box not in ( SELECT a.no_box_sortir FROM grading as a where a.no_invoice is not null   ) and a.kategori ='grade';");
    }
}
