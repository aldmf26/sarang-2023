<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TotalanModel extends Model
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
        $result = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp_cbt + a.ttl_rp) as ttl_rp
        FROM (
        SELECT 
        a.pengawas, a.no_box, a.nama, sum(a.pcs_akhir) as pcs, sum(a.gr_akhir) as gr, min(a.selesai) as selesai, sum(a.ttl_rp) as ttl_rp_cbt,
        (b.hrga_satuan * b.gr_awal) as ttl_rp, b.nm_partai, sum((a.ttl_rp + (b.hrga_satuan * b.gr_awal)) /  a.gr_akhir) as hrga_satuan
        FROM ( 
            SELECT a.id_cabut, a.id_pengawas, c.name as pengawas, a.no_box, b.nama, a.pcs_akhir, a.gr_akhir, a.selesai, a.ttl_rp
            FROM cabut AS a 
            LEFT JOIN tb_anak AS b ON b.id_anak = a.id_anak
            LEFT JOIN users AS c ON c.id = a.id_pengawas
            WHERE a.formulir = 'T' and  a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'cetak') and a.pcs_akhir != 0
        ) AS a
        join bk as b on b.no_box = a.no_box and b.kategori = 'cabut' AND b.baru = 'baru'
        GROUP BY a.id_pengawas, a.no_box 
        HAVING min(a.selesai) = 'Y'  AND a.no_box != 9999 
        ORDER BY a.no_box ASC
        ) as a
        group by a.nm_partai
        HAVING a.nm_partai = '$nm_partai';
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
        and a.no_box not in (SELECT c.no_box FROM formulir_sarang as c where c.kategori = 'sortir')
        
UNION ALL
SELECT b.nm_partai, c.name as pengawas, a.no_box, (b.hrga_satuan * b.gr_awal) as ttl_rp, a.gr_akhir as gr, a.ttl_rp as ttl_rp_cbt, (((b.hrga_satuan * b.gr_awal) + a.ttl_rp) /  a.gr_akhir) as hrga_satuan
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        where a.selesai ='Y' and b.baru = 'baru' and a.pcs_akhir = 0
        and a.no_box not in (SELECT c.no_box FROM formulir_sarang as c where c.kategori = 'sortir')
        
 ORDER by no_box
) as a
GROUP by a.nm_partai 
HAVING a.nm_partai = '$nm_partai';
        ");

        return $result;
    }
    public static function cetak_stok($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr , sum(a.ttl_rp + cost_cbt) as ttl_rp
        FROM (
        SELECT a.no_box, b.name, a.pcs_awal, a.gr_awal, (c.hrga_satuan  * c.gr_awal) as ttl_rp, e.name as pgws,
                    d.ttl_rp as cost_cbt, c.nm_partai
                FROM formulir_sarang as a 
                left join users as b on b.id = a.id_pemberi
                left join bk as c on c.no_box = a.no_box
                left join cabut as d on d.no_box = a.no_box
                left join users as e on e.id = a.id_pemberi
                WHERE a.kategori = 'cetak'  
                and a.id_pemberi is not null 
                and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 0)
        ) as a 
        group by a.nm_partai
        HAVING a.nm_partai = '$nm_partai';
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
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs_awal) pcs , sum(a.gr_awal) as gr, sum(a.ttl_rp + a.cost_ctk + cost_cbt) as ttl_rp
            FROM (
            SELECT a.id_cetak, c.name, d.name as pgws, a.no_box, (a.pcs_akhir + a.pcs_tdk_cetak) as pcs_awal, (a.gr_akhir + a.gr_tdk_cetak) as gr_awal, (e.gr_awal * e.hrga_satuan) as ttl_rp, a.ttl_rp as cost_ctk, f.ttl_rp as cost_cbt, e.nm_partai
                        FROM cetak_new as a 
                        left join formulir_sarang as b on b.no_box = a.no_box and b.kategori = 'cetak'
                        left join users as c on c.id = b.id_pemberi
                        left join users as d on d.id = a.id_pengawas
                        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
                        left join cabut as f on f.no_box = a.no_box
                        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
                        where a.selesai = 'Y' and g.kategori = 'CTK'
                        and  b.id_pemberi is not null 
                        and a.formulir = 'T'  and a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'sortir')
                        order by a.no_box ASC
            ) as a 
            group by a.nm_partai
            HAVING a.nm_partai = '$nm_partai';
        ");

        return $result;
    }
    public static function stock_sortir($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr , sum((b.gr_awal * b.hrga_satuan) + COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0)) as ttl_rp
        FROM formulir_sarang as a 
        left join bk as b on b.no_box = a.no_box
        left join cabut as c on c.no_box = a.no_box
        left join eo as d on d.no_box = a.no_box
        left join (
                    SELECT d.no_box, d.ttl_rp 
                    FROM cetak_new as d 
                    left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                    where h.kategori = 'CTK'
        ) as e on e.no_box = a.no_box
        where b.baru = 'baru' and b.kategori ='cabut' and a.kategori='sortir' and a.no_box not in(SELECT b.no_box FROM sortir as b where b.id_anak != 0) and b.nm_partai = '$nm_partai'
        group by b.nm_partai;
        ");

        return $result;
    }
    public static function sortir_proses($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr , sum(COALESCE(a.ttl_rp,0) + COALESCE(a.cost_cbt,0) + COALESCE(a.cost_ctk,0) + COALESCE(a.cost_eo,0)) as ttl_rp
        FROM (
        SELECT b.nm_partai,  a.no_box, a.pcs_awal, a.gr_awal, (b.hrga_satuan * b.gr_awal) as ttl_rp,
                d.ttl_rp as cost_cbt, e.ttl_rp as cost_ctk, f.name, g.ttl_rp as cost_eo
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
                WHERE a.selesai = 'T' and a.id_anak != 0
        ) as a 
        group by a.nm_partai
        Having a.nm_partai = '$nm_partai';
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
        $result = DB::selectOne("SELECT  c.nm_partai, sum(b.gr_awal) as gr_awal, sum(b.pcs_awal - j.pcs_ambil) as pcs, sum(COALESCE(b.gr_awal,0) - COALESCE(j.gr_ambil,0) - COALESCE(k.gr,0)) as gr,
                    sum((((c.gr_awal * c.hrga_satuan) + COALESCE(d.ttl_rp ,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) + COALESCE(g.ttl_rp,0)) / COALESCE(b.gr_awal)) *  (COALESCE(b.gr_awal,0) - COALESCE(j.gr_ambil,0) - COALESCE(k.gr,0))) as ttl_rp
                            FROM grading as a
                            left join formulir_sarang as b on b.no_box = a.no_box_sortir and b.kategori ='grade'
                            left join bk as c on c.no_box = a.no_box_sortir and c.kategori = 'cabut'
                            left join cabut as d on d.no_box =  a.no_box_sortir
                            left join eo as e on e.no_box = a.no_box_sortir
                            left join (
                                SELECT d.no_box, d.ttl_rp 
                                FROM cetak_new as d 
                                left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                                where h.kategori = 'CTK'
                            ) as f on f.no_box = a.no_box_sortir
                            left join sortir as g on g.no_box = a.no_box_sortir
                            left join (
                                SELECT j.no_box_sortir, sum(j.pcs) as pcs_ambil, sum(j.gr) as gr_ambil
                                FROM grading as j 
                                GROUP by j.no_box_sortir 
                            ) as j on j.no_box_sortir = a.no_box_sortir
                            left join grading_selisih as k on k.no_box = a.no_box_sortir
                            where a.gr = 0 and (COALESCE(b.gr_awal,0) - COALESCE(j.gr_ambil,0) - COALESCE(k.gr,0)) != 0 and c.nm_partai = '$nm_partai'
                            group by c.nm_partai;
        ");

        return $result;
    }
    public static function box_belum_kirim($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai, a.no_box_sortir, sum(a.pcs) as pcs, sum(a.gr) as gr , sum((((b.gr_awal * b.hrga_satuan) + COALESCE(c.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) + COALESCE(g.ttl_rp,0)) / h.gr_awal) * a.gr) as ttl_rp
        FROM (
        SELECT a.no_box_sortir, sum(a.pcs) as pcs, sum(a.gr) as gr
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
        left join formulir_sarang as h on h.no_box = a.no_box_sortir and h.kategori = 'grade'
        where   b.nm_partai = '$nm_partai'
        GROUP by b.nm_partai;
        ");

        return $result;
    }
}
