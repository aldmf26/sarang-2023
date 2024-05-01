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
        d.gr_eo_awal, d.gr_eo_akhir, d.ttl_rp_eo, e.nama as nm_anak_cabut, f.nama as nm_anak_eo
        FROM bk as a
        left join users as b on b.id = a.penerima
        LEFT JOIN (
            SELECT a.id_anak,
                sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(a.gr_flx) as gr_flx , a.no_box,  c.eot as eot_rp, 
                c.batas_eot, sum(a.rupiah) as rupiah, sum(a.ttl_rp) as ttl_rp
                FROM cabut as a
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                left join tb_kelas as c on c.id_kelas = a.id_kelas
                group by a.no_box
        ) as c on c.no_box = a.no_box
        
        left join (
        SELECT d.id_anak, d.no_box, sum(d.gr_eo_awal) as gr_eo_awal, sum(d.gr_eo_akhir) as gr_eo_akhir, sum(d.ttl_rp) as ttl_rp_eo 
            FROM eo as d 
            group by d.no_box
        ) as d on d.no_box = a.no_box

        left join tb_anak as e on e.id_anak = c.id_anak
        left join tb_anak as f on f.id_anak = d.id_anak

        WHERE  a.nm_partai = '$nm_partai' AND a.kategori in('cabut','eo')
        GROUP BY a.no_box 
        ORDER BY (1 - ((IF(c.gr_akhir IS NULL, 0, c.gr_akhir) + IF(d.gr_eo_akhir IS NULL, 0, d.gr_eo_akhir)) / (IF(c.gr_awal IS NULL, 0, c.gr_awal) + IF(d.gr_eo_awal IS NULL, 0, d.gr_eo_awal))) * 100) DESC
        $whereLimit");

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
    public static function datacabutsum2($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs_awal) as pcs_bk , sum(a.gr_awal) as gr_awal_bk,
        b.pcs_awal, b.gr_awal, b.eot, b.gr_flx, b.pcs_akhir, b.gr_akhir, b.ttl_rp, c.gr_awal_eo, c.gr_eo_akhir, c.ttl_rp_eo, a.selesai
        FROM bk as a 
        left join (
        SELECT b.nm_partai,
        sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.eot) as eot , sum(a.gr_flx) as gr_flx, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(a.ttl_rp) as ttl_rp
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori in('cabut','eo')
        group by b.nm_partai
        ) as b on b.nm_partai = a.nm_partai
        
        left join (
         SELECT d.nm_partai, sum(c.gr_eo_awal) as gr_awal_eo, sum(c.gr_eo_akhir) as gr_eo_akhir, sum(c.ttl_rp) as ttl_rp_eo
         FROM eo as c 
         left join bk as d on d.no_box = c.no_box and d.kategori in('cabut','eo')
         GROUP by d.nm_partai
        ) as c on c.nm_partai = a.nm_partai
                
        where a.kategori in ('cabut','eo') and a.nm_partai = ?
        group by a.nm_partai;", [$nm_partai]);

        return $result;
    }
    public static function datacabutsum2backup($nm_partai, $bulan, $tahun)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs_awal) as pcs_bk , sum(a.gr_awal) as gr_awal_bk,
        b.pcs_awal, b.gr_awal, b.eot, b.gr_flx, b.pcs_akhir, b.gr_akhir, b.ttl_rp, c.gr_awal_eo, c.gr_eo_akhir, c.ttl_rp_eo, a.selesai
        FROM bk as a 
        left join (
        SELECT b.nm_partai,
        sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.eot) as eot , sum(a.gr_flx) as gr_flx, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(if(a.selesai = 'T', a.rupiah, a.ttl_rp)) as ttl_rp
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori in('cabut','eo')
        where a.bulan_dibayar = '$bulan' and YEAR(a.tgl_terima) = '$tahun'
        group by b.nm_partai
        ) as b on b.nm_partai = a.nm_partai
        
        left join (
         SELECT d.nm_partai, sum(c.gr_eo_awal) as gr_awal_eo, sum(c.gr_eo_akhir) as gr_eo_akhir, sum(c.ttl_rp) as ttl_rp_eo
         FROM eo as c 
         left join bk as d on d.no_box = c.no_box
         where c.bulan_dibayar = '$bulan' and YEAR(c.tgl_ambil) = '$tahun'
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

    public static function cetak_sum_selesai($nm_partai)
    {
        $result = DB::selectOne("SELECT a.no_lot, a.nm_partai, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal,
        b.pcs_awal_ctk, b.gr_awal_ctk, b.pcs_akhir_ctk, b.gr_akhir_ctk, b.pcs_cu, b.gr_cu, b.ttl_rp_cetak
        FROM bk as a
        left join (
        SELECT c.nm_partai, sum(b.pcs_awal) as pcs_awal_ctk, sum(b.gr_awal) as gr_awal_ctk,
            sum(b.pcs_cu) as pcs_cu, sum(b.gr_cu) as gr_cu,
            sum(b.pcs_akhir) as pcs_akhir_ctk, sum(b.gr_akhir) as gr_akhir_ctk,
            sum((b.pcs_akhir * b.rp_pcs) + b.rp_harian) as ttl_rp_cetak
            FROM cetak as b
            left join bk as c on c.no_box = b.no_box and c.kategori = 'cetak'
            where b.bulan_dibayar !='0'
            GROUP by c.nm_partai
        ) as b on b.nm_partai = a.nm_partai
        WHERE a.nm_partai = '$nm_partai' and a.kategori = 'cetak'
        GROUP BY a.nm_partai;");
        return $result;
    }
    public static function cetak_detail($no_box)
    {
        $result = DB::selectOne("SELECT a.no_box, a.pcs_awal, a.gr_awal, a.pcs_awal_ctk, a.gr_awal_ctk,
        a.pcs_tidak_ctk, a.gr_tidak_ctk, a.pcs_cu, a.gr_cu, a.pcs_akhir, a.gr_akhir, (a.rp_pcs * a.pcs_akhir) as rp_ctk,
        a.bulan_dibayar
        FROM cetak as a
        where a.no_box = ? and a.selesai = 'Y'
        group by a.no_box
        ", [$no_box]);
        return $result;
    }
    public static function cetak_detail_export()
    {
        $result = DB::select("SELECT a.no_box, a.pcs_awal, a.gr_awal, a.pcs_awal_ctk, a.gr_awal_ctk,
        a.pcs_tidak_ctk, a.gr_tidak_ctk, a.pcs_cu, a.gr_cu, a.pcs_akhir, a.gr_akhir, (a.rp_pcs * a.pcs_akhir) as rp_ctk,
        a.bulan_dibayar
        FROM cetak as a
        where  a.selesai = 'Y'
        group by a.no_box
        ");
        return $result;
    }

    public static function cabut_selesai_new()
    {
        $result = DB::select("SELECT * FROM (
            SELECT b.nm_partai, a.no_box, b.tipe, b.ket, b.warna, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.ttl_rp) as ttl_rp, 'cabut' as kategori, a.tgl_terima as tgl_terima, c.nama as nama_anak, c.id_kelas as kelas, e.name as pengawas
            FROM cabut AS a 
            LEFT JOIN bk AS b ON b.no_box = a.no_box AND b.kategori = 'cabut'
    		LEFT join tb_anak as c on c.id_anak = a.id_anak
    		left join users as e on e.id = a.id_pengawas
            where a.selesai = 'T'
            group by a.no_box
            UNION ALL
            SELECT b.nm_partai, c.no_box, b.tipe, b.ket, b.warna, 0 as pcs_awal, sum(c.gr_eo_awal) as gr_awal,sum(c.ttl_rp) as ttl_rp, 'eo' as kategori,  c.tgl_ambil as tgl_terima, d.nama as nama_anak, d.id_kelas as kelas, e.name as pengawas
            FROM eo as c
            LEFT JOIN bk AS b ON b.no_box = c.no_box AND b.kategori = 'cabut'
    		LEFT join tb_anak as d on d.id_anak = c.id_anak
    		left join users as e on e.id = c.id_pengawas
            where c.selesai = 'T'
            group by c.no_box
        ) AS combined_result;");
        return $result;
    }

    public static function cabut_laporan()
    {
        $result = DB::select("SELECT a.nm_partai, a.no_box, a.tipe, a.pcs_awal, a.gr_awal, if(b.pcs_awal_cbt is null , 0 , b.pcs_awal_cbt) as pcs_awal_cbt, if(b.gr_awal_cbt is null ,0 , b.gr_awal_cbt) as gr_awal_cbt , if(b.pcs_akhir_cbt is null,0,b.pcs_akhir_cbt) as pcs_akhir_cbt, if(b.gr_akhir_cbt is null,0,b.gr_akhir_cbt) as gr_akhir_cbt, if(c.gr_eo_awal is null ,0,c.gr_eo_awal) as gr_eo_awal, if(c.gr_eo_akhir is null,0,c.gr_eo_akhir) as gr_eo_akhir, a.pengawas, d.name, e.nama as anak_cbt, e.id_kelas as kelas_cbt, f.nama as anak_eo, f.id_kelas as kelas_eo, b.cost_cabut, c.cost_eo, b.eot, b.flx
        FROM bk as a
        left join (
            SELECT b.no_box, sum(b.pcs_awal) as pcs_awal_cbt, sum(b.gr_awal) as gr_awal_cbt, sum(b.pcs_akhir) as pcs_akhir_cbt, sum(b.gr_akhir) as gr_akhir_cbt, b.id_anak, sum(b.ttl_rp) as cost_cabut, sum(b.eot) as eot, sum(b.gr_flx) as flx
            FROM cabut as b 
            group by b.no_box
        ) as b on b.no_box = a.no_box
        
        left join (
            SELECT c.no_box, sum(c.gr_eo_awal) as gr_eo_awal, sum(c.gr_eo_akhir) as gr_eo_akhir, c.id_anak, sum(c.ttl_rp) as cost_eo
            FROM eo as c
            group by c.no_box
        ) as c on c.no_box = a.no_box
        
        left join users as d on d.id = a.penerima
        left join tb_anak as e on e.id_anak = b.id_anak
        left join tb_anak as f on f.id_anak = c.id_anak
         WHERE a.kategori = 'cabut' and a.selesai = 'T';");

        return $result;
    }

    public static function cetak_partai($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_partai, a.no_box, 
        sum(b.pcs_awal_ambil_ctk) as pcs_awal_ambil_ctk,
        sum(b.gr_awal_ambil_ctk) as gr_awal_ambil_ctk,
        sum(b.pcs_tdk_ctk) as pcs_tdk_ctk,
        sum(b.gr_tdk_ctk) as gr_tdk_ctk,
        sum(b.pcs_ctk) as pcs_ctk,
        sum(b.gr_ctk) as gr_ctk,
        sum(b.pcs_cu) as pcs_cu,
        sum(b.gr_cu) as gr_cu,
        sum(b.pcs_akhir_ctk) as pcs_akhir_ctk,
        sum(b.gr_akhir_ctk) as gr_akhir_ctk,
        sum(b.cost_ctk) as cost_ctk,
        sum(a.ttl_rp) as cost_bk_ctk
        FROM bk as a
        left join (
        SELECT b.no_box, 
            sum(b.pcs_awal) as pcs_awal_ambil_ctk, 
            sum(b.gr_awal) as gr_awal_ambil_ctk,
            sum(b.pcs_tidak_ctk) as pcs_tdk_ctk,
            sum(b.gr_tidak_ctk) as gr_tdk_ctk,
            sum(b.pcs_awal_ctk) as pcs_ctk,
            sum(b.gr_awal_ctk) as gr_ctk,
            sum(b.pcs_cu) as pcs_cu,
            sum(b.gr_cu) as gr_cu,
            sum(b.pcs_akhir) as pcs_akhir_ctk,
            sum(b.gr_akhir) as gr_akhir_ctk,
            sum(b.pcs_akhir * b.rp_pcs) as cost_ctk
            FROM cetak as b
            GROUP by b.no_box
        ) as b on b.no_box = a.no_box
        where a.kategori = 'cetak' and a.nm_partai = ?
        group by a.nm_partai;
        ", [$nm_partai]);

        return $result;
    }


    public static function cabut_detail($nm_partai, $limit = 10)
    {
        $whereLimit = $limit == 'ALL' ? '' : "LIMIT $limit";
        $result = DB::select("SELECT * , ((1 - (gr_akhir/gr_awal)) * 100 ) as susut
                FROM (
                    SELECT b.nm_partai, a.no_box, b.tipe, b.ket, b.warna, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir,
                    sum(a.ttl_rp) as ttl_rp, 'cabut' as kategori, a.tgl_terima as tgl_terima, c.nama as nama_anak, c.id_kelas as kelas, e.name as pengawas, sum(a.eot) as eot, sum(a.gr_flx) as gr_flx
                    FROM cabut AS a 
                    LEFT JOIN bk AS b ON b.no_box = a.no_box AND b.kategori = 'cabut'
                    LEFT join tb_anak as c on c.id_anak = a.id_anak
                    left join users as e on e.id = a.id_pengawas
                    where b.nm_partai = '$nm_partai'
                    group by a.id_cabut
                    UNION ALL
                    SELECT b.nm_partai, c.no_box, b.tipe, b.ket, b.warna, 0 as pcs_awal, sum(c.gr_eo_awal) as gr_awal, 0, sum(c.gr_eo_akhir) as gr_akhir ,sum(c.ttl_rp) as ttl_rp, 'eo' as kategori,  c.tgl_ambil as tgl_terima, d.nama as nama_anak, d.id_kelas as kelas, e.name as pengawas, 0 , 0
                    FROM eo as c
                    LEFT JOIN bk AS b ON b.no_box = c.no_box AND b.kategori = 'cabut'
                    LEFT join tb_anak as d on d.id_anak = c.id_anak
                    left join users as e on e.id = c.id_pengawas
                    where b.nm_partai = '$nm_partai'
                    group by c.id_eo
                ) AS combined_result
                order by  ((1 - (gr_akhir/gr_awal)) * 100) DESC
                $whereLimit
                ;");

        return $result;
    }
    public static function cabut_selesai_g_cetak()
    {
        $result = DB::select("SELECT b.nm_partai, b.tipe, c.nm_partai as nm_partai2, a.*
        FROM (
        SELECT c.name, b.nama, b.id_kelas, a.no_box, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(a.ttl_rp) as ttl_rp, 'cabut' as kerja
            FROM cabut as a
            left join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pengawas
            WHERE a.selesai = 'Y'
            group by a.no_box
            
        UNION ALL   
        
        SELECT c.name, b.nama, b.id_kelas, e.no_box, 0, sum(e.gr_eo_akhir) as gr_akhir, sum(e.ttl_rp) as ttl_rp, 'eo' as kerja
            FROM eo as e
            left join tb_anak as b on b.id_anak = e.id_anak
            left join users as c on c.id = b.id_pengawas
            where e.selesai ='Y'
            group by e.no_box
            
        ) AS a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join bk as c on c.no_box = a.no_box and c.kategori = 'cetak'
        
        where c.nm_partai is null;");

        return $result;
    }

    public static function cabut_selesai_g_cetak_nota($no_box)
    {
        $result = DB::selectOne("SELECT b.nm_partai, b.tipe, c.nm_partai as nm_partai2, a.*
        FROM (
        SELECT c.name, b.nama, b.id_kelas, a.no_box, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(a.ttl_rp) as ttl_rp, 'cabut' as kerja
            FROM cabut as a
            left join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pengawas
            WHERE a.selesai = 'Y' 
            group by a.no_box
            
        UNION ALL   
        
        SELECT c.name, b.nama, b.id_kelas, e.no_box, 0, sum(e.gr_eo_akhir) as gr_akhir, sum(e.ttl_rp) as ttl_rp, 'eo' as kerja
            FROM eo as e
            left join tb_anak as b on b.id_anak = e.id_anak
            left join users as c on c.id = b.id_pengawas
            where e.selesai ='Y' 
            group by e.no_box
        ) AS a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join bk as c on c.no_box = a.no_box and c.kategori = 'cetak'
        
        where c.nm_partai is null and a.no_box = '$no_box'");

        return $result;
    }

    public static function cetak_pgws()
    {
        $result = DB::select("SELECT a.nm_partai, a.no_box, a.tipe, a.ket, a.warna, a.tgl, a.pengawas, c.name, a.pcs_awal, a.gr_awal, b.pcs_awal_ctk, b.gr_awal_ctk
        FROM bk as a 
        left join(
            SELECT b.no_box, 
            sum(if(b.pcs_awal is null,0,b.pcs_awal)) as pcs_awal_ctk, 
            sum(if(b.gr_awal is null,0,b.gr_awal)) as gr_awal_ctk
            FROM cetak as b
            GROUP by b.no_box
        ) as b on b.no_box = a.no_box
        left join users as c on c.id = a.penerima
        where a.kategori = 'cetak' and (a.pcs_awal - if(b.pcs_awal_ctk is null ,0 ,b.pcs_awal_ctk)) != 0 ");

        return $result;
    }

    public static function cetak_belum_selesai()
    {
        $result = DB::select("SELECT b.nm_partai, b.tipe, b.ket,b.warna, b.tgl, c.name, d.nama, d.id_kelas, a.no_box, 
        sum(a.pcs_awal) as pcs_ambil, sum(a.gr_awal) as gr_ambil,
        sum(a.pcs_tidak_ctk) as pcs_tdk_ctk, sum(a.gr_tidak_ctk) as gr_tdk_ctk, sum(a.pcs_awal_ctk) as pcs_awal_ctk,
        sum(a.gr_awal_ctk) as gr_awal_ctk, sum(a.pcs_cu) as pcs_cu, sum(a.gr_cu) as gr_cu , 
        sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir
        FROM cetak as a
        left JOIN bk as b on b.no_box and b.kategori = 'cetak'
        left join users as c on c.id = b.penerima
        left join tb_anak as d on d.id_anak = a.id_anak
        WHERE a.selesai = 'T'
        GROUP by a.no_box;");

        return $result;
    }
    public static function cetak_laporan()
    {
        $result = DB::select("SELECT a.nm_partai, a.no_box, a.tipe, c.name, a.pcs_awal, a.gr_awal, b.*
        FROM bk as a 
        left join(
        SELECT b.no_box as box2, sum(b.pcs_awal) as pcs_awal_ambil, sum(b.gr_awal) as gr_awal_ambil,
            sum(b.pcs_tidak_ctk) as pcs_tdk_ctk, sum(b.gr_tidak_ctk) as gr_tdk_ctk,
            sum(b.pcs_awal_ctk) as pcs_awal_ctk, sum(b.gr_awal_ctk) as gr_awal_ctk,
            sum(b.pcs_cu) as pcs_cu, sum(b.gr_cu) as gr_cu,
            sum(b.pcs_akhir) as pcs_akhir, sum(b.gr_akhir) as gr_akhir,
            sum(if(b.selesai = 'Y',b.pcs_akhir * b.rp_pcs,0)) as ttl_rp,
            b.penutup
            FROM cetak as b
            group by b.no_box
        ) as b on b.box2 = a.no_box
        left join users as c on c.id = a.penerima
        where a.kategori ='cetak' and b.penutup = 'T';");

        return $result;
    }

    public static function grading_bj()
    {
        $result =  DB::select("SELECT grade, sum(pcs) as pcs, sum(gr) as gr, sum(gr * rp_gram) as ttl_rp, sum(pcs_kredit) as pcs_kredit, sum(gr_kredit) as gr_kredit, sum(gr_kredit * rp_gram_kredit) as ttl_rp_kredit
        FROM `pengiriman_list_gradingbj` 
        GROUP BY grade 
        HAVING pcs - pcs_kredit <> 0 OR gr - gr_kredit <> 0");

        return $result;
    }
    public static function bk_sortir()
    {
        $result =  DB::select("SELECT a.no_box, a.tipe, a.ket, a.warna, a.pengawas, c.name, a.pcs_awal, a.gr_awal, b.pcs_awal_str, b.gr_awal_str, b.pcs_akhir_str, b.gr_akhir_str, b.ttl_rp
        FROM bk as a 
        
        left join (
        SELECT b.no_box, b.id_pengawas, sum(b.pcs_awal) as pcs_awal_str, sum(b.gr_awal) as gr_awal_str,
            sum(b.pcs_akhir) as pcs_akhir_str, sum(b.gr_akhir) as gr_akhir_str, sum(b.ttl_rp) as ttl_rp
        FROM sortir as b
            GROUP by b.no_box , b.id_pengawas
        ) as b on b.no_box = a.no_box and b.id_pengawas = a.penerima
        left join users as c on c.id = a.penerima
        
        where a.kategori ='sortir' and a.gr_awal - b.gr_awal_str != 0;");

        return $result;
    }
}
