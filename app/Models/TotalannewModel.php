<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TotalannewModel extends Model
{
    use HasFactory;
    public static function bksinta()
    {
        $result = DB::select("SELECT a.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a 
        WHERE a.baru = 'baru' and a.kategori = 'cabut'
        GROUP by a.nm_partai
        ORDER by a.nm_partai ASC;");

        return $result;
    }
    public static function bkstock($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr , sum(a.gr_awal * a.hrga_satuan) as ttl_rp
            FROM bk as a 
            where a.kategori ='cabut' and a.baru ='baru' 
            AND NOT EXISTS (SELECT 1 FROM cabut AS b WHERE b.no_box = a.no_box) 
            AND NOT EXISTS (SELECT 1 FROM eo AS c WHERE c.no_box = a.no_box)
            and a.nm_partai = '$nm_partai'
            GROUP by a.nm_partai;");

        return $result;
    }
    public static function bksedang_proses($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp
        FROM (
        SELECT a.no_box, a.pcs_awal as pcs, a.gr_awal as gr , b.hrga_satuan, (a.gr_awal * b.hrga_satuan) as ttl_rp,
                c.name as penerima, b.nm_partai
        FROM cabut as a
        left join bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        WHERE a.selesai = 'T'  AND a.no_box != 9999 and b.baru = 'baru'     
        UNION ALL
        SELECT d.no_box, 0, d.gr_eo_awal as gr, e.hrga_satuan, (d.gr_eo_awal * e.hrga_satuan) as ttl_rp, f.name as penerima, e.nm_partai
        FROM eo as d
        left join bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
        left join users as f on f.id = d.id_pengawas
        WHERE d.selesai = 'T'  AND d.no_box != 9999  and e.baru = 'baru'
        ) as a 
        group by a.nm_partai
        HAVING a.nm_partai = '$nm_partai';
        ");

        return $result;
    }
    public static function bkselesai_siap_ctk($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai, sum(a.pcs_akhir) as pcs, sum(a.gr_akhir) as gr, sum(COALESCE(b.hrga_satuan * b.gr_awal,0) + COALESCE(a.ttl_rp,0) + COALESCE(z.cost_cu,0)) as ttl_rp, sum(b.hrga_satuan * b.gr_awal) as cost_bk, sum(a.ttl_rp) as cost_cbt, sum(c.rp_gr * a.gr_akhir) as cost_op_cbt,
        sum(z.cost_cu) as cost_cu
        FROM cabut as a 
        left join (
                SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                FROM cetak_new as a 
                left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                where b.kategori = 'CU'
                group by a.no_box
            ) as z on z.no_box = a.no_box
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join oprasional as c on c.bulan = a.bulan_dibayar
        where b.nm_partai = '$nm_partai' and a.selesai = 'Y' and a.formulir = 'T' and a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'cetak') and a.pcs_awal != 0
        group by b.nm_partai;
        ");

        return $result;
    }
    public static function bkselesai_siap_str($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.gr) as gr, sum(a.hrga_satuan * a.gr) as ttl_rp
FROM (
SELECT b.nm_partai, c.name as pengawas, a.no_box, (b.hrga_satuan * b.gr_awal) as ttl_rp, a.gr_eo_akhir as gr, a.ttl_rp as ttl_rp_cbt, (((b.hrga_satuan * b.gr_awal) + a.ttl_rp) /  a.gr_eo_akhir) as hrga_satuan
        FROM eo as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        where a.selesai ='Y' and b.baru = 'baru'
        
        
UNION ALL
SELECT b.nm_partai, c.name as pengawas, a.no_box, (b.hrga_satuan * b.gr_awal) as ttl_rp, a.gr_akhir as gr, a.ttl_rp as ttl_rp_cbt, (((b.hrga_satuan * b.gr_awal) + a.ttl_rp) /  a.gr_akhir) as hrga_satuan
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        where a.selesai ='Y' and b.baru = 'baru' and a.pcs_akhir = 0
        
        
 ORDER by no_box
) as a
GROUP by a.nm_partai 
HAVING a.nm_partai = '$nm_partai';
        ");

        return $result;
    }
    public static function cetak_stok($nm_partai)
    {
        $result = DB::selectOne("SELECT a.no_box, b.name, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as pcs, sum((c.hrga_satuan  * c.gr_awal) + d.ttl_rp) as ttl_rp, c.nm_partai
                FROM formulir_sarang as a 
                left join users as b on b.id = a.id_pemberi
                left join bk as c on c.no_box = a.no_box
                left join cabut as d on d.no_box = a.no_box
                left join users as e on e.id = a.id_pemberi
                WHERE a.kategori = 'cetak'  
                and a.id_pemberi is not null 
                and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 0) and c.nm_partai = '$nm_partai'
                group by c.nm_partai;
        ");

        return $result;
    }
    public static function cetak_proses($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs_awal) as pcs , sum(a.gr_awal) as gr , sum(a.ttl_rp + a.cost_cbt) as ttl_rp
            FROM (
            SELECT a.no_box, c.name, a.pcs_awal_ctk as pcs_awal, a.gr_awal_ctk as gr_awal, (d.gr_awal * d.hrga_satuan) as ttl_rp , e.name as pgws, f.ttl_rp as cost_cbt, d.nm_partai
            FROM cetak_new as a 
            left join formulir_sarang as b on b.no_box = a.no_box and b.kategori = 'cetak'
            left join users as c on c.id = b.id_pemberi
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join users as e on e.id = a.id_pengawas
            left join cabut as f on f.no_box = b.no_box
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            where a.selesai = 'T' and a.id_anak != 0  and g.kategori = 'CTK'
            order by a.no_box ASC

            ) as a 
            group by a.nm_partai
            HAVING a.nm_partai = '$nm_partai'
        ");

        return $result;
    }
    public static function cetak_selesai($nm_partai)
    {
        $result = DB::selectOne("SELECT e.nm_partai, sum(a.pcs_akhir + a.pcs_tdk_cetak) as pcs, sum(a.gr_akhir + a.gr_tdk_cetak) as gr, sum(e.gr_awal * e.hrga_satuan) as cost_bk, sum(a.ttl_rp) as cost_ctk, sum(f.ttl_rp) as cost_cbt, sum((e.gr_awal * e.hrga_satuan) + a.ttl_rp + f.ttl_rp + COALESCE(z.cost_cu,0)) as ttl_rp, sum( COALESCE(f.gr_akhir * h.rp_gr,0) + COALESCE(a.gr_akhir * i.rp_gr,0)) as cost_op, sum(z.cost_cu) as cost_cu
        FROM cetak_new as a 
        left join (
                SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                FROM cetak_new as a 
                left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                where b.kategori = 'CU'
                group by a.no_box
            ) as z on z.no_box = a.no_box
        left join oprasional as i on i.bulan = a.bulan_dibayar
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        left join cabut as f on f.no_box = a.no_box
        left join oprasional as h on h.bulan = f.bulan_dibayar
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        where a.selesai = 'Y' and g.kategori = 'CTK'
        and a.formulir = 'T'  and a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'sortir') and e.nm_partai = '$nm_partai'
        group by e.nm_partai;
        ");

        return $result;
    }
    public static function stock_sortir($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr , sum((b.gr_awal * b.hrga_satuan) + COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0)) as ttl_rp, sum(COALESCE(f.rp_gr * c.gr_akhir,0) + COALESCE(e.cost_op_ctk,0)) as cost_op
        FROM formulir_sarang as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join cabut as c on c.no_box = a.no_box
        left join oprasional as f on f.bulan = c.bulan_dibayar
        left join eo as d on d.no_box = a.no_box
        left join (
                    SELECT d.no_box, d.ttl_rp , (h.rp_gr * d.gr_akhir) as cost_op_ctk
                    FROM cetak_new as d 
                    left join oprasional as f on f.bulan = d.bulan_dibayar
                    left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                    where h.kategori = 'CTK'
        ) as e on e.no_box = a.no_box
        where b.baru = 'baru' and a.kategori='sortir' and a.no_box not in(SELECT b.no_box FROM sortir as b where b.id_anak != 0) and b.nm_partai = '$nm_partai'
        group by b.nm_partai;
        ");

        return $result;
    }
    public static function sortir_proses($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai,  a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum((b.hrga_satuan * b.gr_awal) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(g.ttl_rp,0)) as ttl_rp
                FROM sortir as a 
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                left join cabut as d on d.no_box = a.no_box
                left join (
                    SELECT d.no_box, d.ttl_rp 
                    FROM cetak_new as d 
                    left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                    where h.kategori = 'CTK'
                ) as e on e.no_box = a.no_box
                left join users as f on f.id = a.id_pengawas
                left join eo as g on g.no_box = a.no_box
                WHERE a.selesai = 'T' and a.id_anak != 0 and b.nm_partai ='$nm_partai'
                group by b.nm_partai;
        ");

        return $result;
    }
    public static function sortir_selesai($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai, a.no_box, sum(a.pcs_akhir) as pcs, sum(a.gr_akhir) as gr, sum((b.hrga_satuan * b.gr_awal) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(a.ttl_rp,0) + COALESCE(g.ttl_rp,0)) as ttl_rp
                FROM sortir as a 
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                left join cabut as d on d.no_box = a.no_box
                left join (
                    SELECT d.no_box, d.ttl_rp 
                    FROM cetak_new as d 
                    left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                    where h.kategori = 'CTK'
                ) as e on e.no_box = a.no_box
                
                left join users as f on f.id = a.id_pengawas
                left join eo as g on g.no_box = a.no_box
                WHERE a.no_box not in (SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'grade') and a.selesai = 'Y' and b.nm_partai = '$nm_partai'
                group by b.nm_partai;
        ");

        return $result;
    }
    public static function grading_stock($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai, sum(COALESCE(a.pcs_awal,0) - COALESCE(c.pcs_grading,0)) as pcs, sum(COALESCE(a.gr_awal,0) - COALESCE(c.gr_grading,0)) as gr, 
            
            sum((b.gr_awal * b.hrga_satuan) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) + COALESCE(g.ttl_rp,0) + COALESCE(h.ttl_rp,0) + COALESCE(z.cost_cu,0) ) as ttl_rp , 
            sum(b.hrga_satuan * b.gr_awal) as cost_bk, 
            sum(e.ttl_rp) as cost_cbt, 
            sum(h.ttl_rp) as cost_eo, 
            sum(f.ttl_rp) as cost_ctk, 
            sum(g.ttl_rp) as cost_str,
            sum(COALESCE(i.rp_gr * e.gr_akhir,0) + COALESCE(f.cost_op_ctk,0) + COALESCE(g.gr_akhir * j.rp_gr,0) + COALESCE(h.gr_eo_akhir * k.rp_gr,0)) as cost_op, sum(z.cost_cu) as cost_cu
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

            where a.kategori ='grade' and b.nm_partai = '$nm_partai';
        
        ");

        return $result;
    }
    public static function box_belum_kirim($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai, a.no_box_sortir, sum(a.pcs) as pcs, sum(a.gr) as gr , sum((b.gr_awal * b.hrga_satuan) + COALESCE(c.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) + COALESCE(g.ttl_rp,0)) as ttl_rp
         FROM 
        ( SELECT a.no_box_sortir, sum(a.pcs) as pcs, sum(a.gr) as gr
         FROM grading as a 
         where a.no_box_grading is not null
            group by a.no_box_sortir
        ) as a 
        left join bk as b on b.no_box = a.no_box_sortir and b.kategori = 'cabut' 
        left join cabut as c on c.no_box = a.no_box_sortir
        left join eo as e on e.no_box = a.no_box_sortir
        left join (
          SELECT d.no_box, d.ttl_rp 
          FROM cetak_new as d 
          left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
          where h.kategori = 'CTK'
        ) as f on f.no_box = a.no_box_sortir
        left join sortir as g on g.no_box = a.no_box_sortir
        where  b.nm_partai = '$nm_partai'
        GROUP by b.nm_partai;
        ");

        return $result;
    }
}
