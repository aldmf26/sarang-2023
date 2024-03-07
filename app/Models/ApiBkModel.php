<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiBkModel extends Model
{
    use HasFactory;
    public static function datacabut($no_lot, $nm_partai)
    {
        $result = DB::select("SELECT 
        a.pcs_awal, a.gr_awal, a.gr_flx , a.no_box, a.gr_akhir, c.eot as eot_rp, a.pcs_akhir,
        c.batas_eot, a.rupiah, a.ttl_rp, c.batas_susut,c.bonus_susut,c.rp_bonus,a.pcs_hcr,c.denda_hcr, a.eot, c.gr as gr_kelas, a.selesai
        
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori ='cabut'
        left join tb_kelas as c on c.id_kelas = a.id_kelas
        where b.no_lot =? and b.nm_partai=? 
        ;", [$no_lot, $nm_partai]);

        return $result;
    }
    public static function datacabutsum($nm_partai)
    {
        $result = DB::select("SELECT 
        a.pcs_awal, a.gr_awal, a.gr_flx , a.gr_akhir, c.eot as eot_rp, a.pcs_akhir,
        c.batas_eot, a.rupiah, c.batas_susut,c.bonus_susut,c.rp_bonus,a.pcs_hcr,c.denda_hcr, a.eot, c.gr as gr_kelas, a.selesai
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori ='cabut'
        left join tb_kelas as c on c.id_kelas = a.id_kelas 
        where b.nm_partai=? 
        ;", [$nm_partai]);

        return $result;
    }
    public static function datacetak($no_lot, $nm_partai)
    {
        $result = DB::select("SELECT a.*, b.*, c.*
        FROM cetak as a
        left join bk as d on d.no_box = a.no_box and d.kategori ='cetak'
        LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
        left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
        where  d.no_lot =? and d.nm_partai=? 
        order by a.selesai ASC;
        ", [$no_lot, $nm_partai]);

        return $result;
    }
    public static function datasortir($no_lot, $nm_partai)
    {
        $result = DB::select("SELECT a.*, b.*, c.*
        FROM sortir as a
        left join bk as d on d.no_box = a.no_box and d.kategori ='sortir'
        LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
        left join tb_kelas_sortir as c on c.id_kelas = a.id_kelas
        where  d.no_lot = ? and d.nm_partai= ? 
        order by a.selesai ASC;
        ", [$no_lot, $nm_partai]);

        return $result;
    }

    public static function bk_cabut_tes($no_lot, $nm_partai)
    {
        $result = DB::selectOne("SELECT a.no_lot, a.nm_partai, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal
        FROM bk as a
        WHERE a.no_lot = ? AND a.nm_partai = ? AND a.kategori ='cabut'
        GROUP BY a.no_lot, a.nm_partai;", [$no_lot, $nm_partai]);
        return $result;
    }
    public static function bk_cabut_cabutLama($no_lot, $nm_partai, $limit = 10)
    {
        $whereLimit = $limit == 'ALL' ? '' : "LIMIT $limit";
        $result = DB::select("SELECT a.tipe,a.no_lot, a.nm_partai, a.no_box, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, b.name
        FROM bk as a
        left join users as b on b.id = a.penerima
        WHERE a.no_lot = ? AND a.nm_partai = ? AND a.kategori ='cabut'
        GROUP BY a.no_box $whereLimit", [$no_lot, $nm_partai]);
        return $result;
    }
    public static function bk_cabut_cabut($nm_partai, $limit = 10)
    {
        $whereLimit = $limit == 'ALL' ? '' : "LIMIT $limit";
        $result = DB::select("SELECT a.no_box, a.tipe,a.no_lot, a.nm_partai, a.no_box, sum(a.pcs_awal) as pcs_awal_bk, sum(a.gr_awal) as gr_awal_bk, b.name,
        c.pcs_awal, c.gr_awal,c.pcs_akhir, c.gr_akhir,c.gr_flx,c.eot_rp,c.batas_eot,c.rupiah,c.ttl_rp, 
        d.gr_eo_awal, d.gr_eo_akhir, d.ttl_rp_eo
        FROM bk as a
        left join users as b on b.id = a.penerima
        LEFT JOIN (
            SELECT 
                sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(a.gr_flx) as gr_flx , a.no_box,  c.eot as eot_rp, 
                c.batas_eot, sum(a.rupiah) as rupiah, sum(a.ttl_rp) as ttl_rp
                FROM cabut as a
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                left join tb_kelas as c on c.id_kelas = a.id_kelas
                group by a.no_box
        ) as c on c.no_box = a.no_box
        
        left join (
        SELECT d.no_box, sum(d.gr_eo_awal) as gr_eo_awal, sum(d.gr_eo_akhir) as gr_eo_akhir, sum(d.ttl_rp) as ttl_rp_eo 
            FROM eo as d 
            group by d.no_box
        ) as d on d.no_box = a.no_box

        WHERE  a.nm_partai = '$nm_partai' AND a.kategori in('cabut','eo')
        GROUP BY a.no_box $whereLimit");

        return $result;
    }
    public static function datacabutperbox($no_box)
    {
        $result = DB::selectOne("SELECT 
        sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(a.gr_flx) as gr_flx , a.no_box,  c.eot as eot_rp, 
        c.batas_eot, sum(a.rupiah) as rupiah, sum(a.ttl_rp) as ttl_rp,
        sum(if(a.selesai = 'T' , 0 , (1 - (a.gr_akhir / a.gr_awal)) * 100)) as susut
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori LIKE '%cabut%'
        left join tb_kelas as c on c.id_kelas = a.id_kelas
        where a.no_box = ?
        group by a.no_box
        ;", [$no_box]);

        return $result;
    }
    public static function datacabutsum2($nm_partai, $bulan_dibayar)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs_awal) as pcs_bk , sum(a.gr_awal) as gr_awal_bk,
        b.pcs_awal, b.gr_awal, b.eot, b.gr_flx, b.pcs_akhir, b.gr_akhir, b.ttl_rp, c.gr_awal_eo, c.gr_eo_akhir, c.ttl_rp_eo, a.selesai
        FROM bk as a 
        left join (
        SELECT b.nm_partai,
        sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.eot) as eot , sum(a.gr_flx) as gr_flx, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(if(a.selesai = 'T', a.rupiah, a.ttl_rp)) as ttl_rp
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori in('cabut','eo')
        where a.bulan_dibayar = '2'
        group by b.nm_partai
        ) as b on b.nm_partai = a.nm_partai
        
        left join (
         SELECT d.nm_partai, sum(c.gr_eo_awal) as gr_awal_eo, sum(c.gr_eo_akhir) as gr_eo_akhir, sum(c.ttl_rp) as ttl_rp_eo
         FROM eo as c 
         left join bk as d on d.no_box = c.no_box
         where c.bulan_dibayar = '2'
         GROUP by d.nm_partai
        ) as c on c.nm_partai = a.nm_partai
                
        where a.kategori in ('cabut','eo') and a.nm_partai = ?
        group by a.nm_partai;", [$nm_partai]);

        return $result;
    }
    public static function bk_cabut_sum($nm_partai)
    {
        $result = DB::selectOne("SELECT a.no_lot, a.nm_partai, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, a.selesai
        FROM bk as a
        WHERE a.nm_partai = '$nm_partai' AND a.kategori in('cabut','eo')
        GROUP BY a.nm_partai;");

        return $result;
    }

    public static function export($no_lot, $nm_partai)
    {
        $result = DB::select("SELECT a.no_box, a.no_lot, a.nm_partai, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, a.tipe, a.ket, a.warna
        FROM bk as a
        WHERE a.no_lot = ? AND a.nm_partai = ? AND a.kategori ='cabut'
        GROUP BY a.id_bk;", [$no_lot, $nm_partai]);
        return $result;
    }

    public static function datacabut_export($no_box)
    {
        $result = DB::selectOne("SELECT 
        a.pcs_awal, a.gr_awal, a.gr_flx , a.gr_akhir, c.eot as eot_rp, a.pcs_akhir,
        c.batas_eot, a.rupiah, c.batas_susut,c.bonus_susut,c.rp_bonus,a.pcs_hcr,c.denda_hcr, a.eot, c.gr as gr_kelas, a.selesai
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori ='cabut'
        left join tb_kelas as c on c.id_kelas = a.id_kelas 
        where b.no_box = ?
        ;", [$no_box]);

        return $result;
    }
    public static function datacetak_export($no_box)
    {
        $result = DB::selectOne("SELECT a.*, b.*, c.*
        FROM cetak as a
        left join bk as d on d.no_box = a.no_box and d.kategori ='cetak'
        LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
        left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
        where  d.no_box = ?
        order by a.selesai ASC;
        ", [$no_box]);

        return $result;
    }

    public static function bk_sortir_sum($nm_partai, $kategori)
    {
        $result = DB::selectOne("SELECT a.no_lot, a.nm_partai, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal
        FROM bk as a
        WHERE a.nm_partai = '$nm_partai' AND a.kategori = '$kategori'
        GROUP BY a.nm_partai;");

        return $result;
    }
    public static function data_sortir_sum($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(if(a.selesai = 'T' , 0,a.ttl_rp)) as ttl_rp, sum(if(a.selesai = 'T' , 0 , 1 - (a.gr_akhir / a.gr_awal))) as susut
        FROM (
        SELECT a.selesai, a.no_box, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(if(a.selesai = 'T' , 0,a.ttl_rp)) as ttl_rp, sum(if(a.selesai = 'T' , 0 , 1 - (a.gr_akhir / a.gr_awal))) as susut
            FROM sortir as a 
        group by a.no_box
        ) as a
        left join (
            SELECT b.no_box, b.nm_partai
            FROM bk as b 
            group by b.no_box
        ) as b on b.no_box = a.no_box
        where b.nm_partai = '$nm_partai'
                group by b.nm_partai;");

        return $result;
    }
    public static function data_cetak_sum($nm_partai)
    {
        $result = DB::selectOne("SELECT b.nm_partai, sum(a.pcs_awal_ctk) as pcs_awal, sum(a.gr_awal_ctk) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(if(a.selesai = 'T', 0, 1 - ((a.gr_akhir + a.gr_cu) / a.gr_awal_ctk))) as susut,
        sum(if(a.selesai = 'T', 0, a.pcs_akhir * a.rp_pcs)) as ttl_rp
        FROM cetak as a 
        left join bk as b on b.no_box = a.no_box
        where b.nm_partai = '$nm_partai'
        group by b.nm_partai;");

        return $result;
    }


    public static function bk_sortir_box($nm_partai, $limit = 10)
    {
        $whereLimit = $limit == 'ALL' ? '' : "LIMIT $limit";
        $result = DB::select("SELECT a.nm_partai, a.tipe, a.no_box, b.name, a.pcs_awal, a.gr_awal, c.pcs_awal_sortir, c.gr_awal_sortir, c.pcs_akhir_sortir, c.gr_akhir_sortir, c.ttl_rp
        FROM bk as a
        left join users as b on b.id = a.penerima
        LEFT JOIN (
            SELECT a.no_box, sum(a.pcs_awal) as pcs_awal_sortir, sum(a.gr_awal) as gr_awal_sortir, sum(a.pcs_akhir) as 				pcs_akhir_sortir, sum(a.gr_akhir) as gr_akhir_sortir , sum(a.ttl_rp) as ttl_rp
            FROM sortir as a
            left join tb_kelas as c on c.id_kelas = a.id_kelas
            group by a.no_box
        ) as c on c.no_box = a.no_box
        WHERE  a.nm_partai = '$nm_partai' AND a.kategori = 'sortir'
        GROUP BY a.no_box $whereLimit");

        return $result;
    }

    public static function cabut_selesai()
    {
        $result = DB::select("SELECT * FROM (
            SELECT b.nm_partai, a.no_box, b.tipe, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(a.ttl_rp) as ttl_rp, 'cabut' as ket
            FROM cabut AS a 
            LEFT JOIN bk AS b ON b.no_box = a.no_box AND b.kategori = 'cabut'
            WHERE a.selesai = 'Y'
            group by a.no_box
            UNION ALL
        
            SELECT b.nm_partai, c.no_box, b.tipe, 0 as pcs_akhir, sum(c.gr_eo_akhir) as gr_akhir, sum(c.ttl_rp) as ttl_rp, 'eo' as ket
            FROM eo as c
            LEFT JOIN bk AS b ON b.no_box = c.no_box AND b.kategori = 'cabut'
            WHERE c.selesai = 'Y'
            group by c.no_box
        ) AS combined_result;");
        return $result;
    }
}
