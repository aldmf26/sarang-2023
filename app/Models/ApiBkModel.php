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
    public static function bk_cabut_cabut($no_lot, $nm_partai,$limit = 10)
    {
        $whereLimit = $limit == 'ALL' ? '' : "LIMIT $limit";
        $result = DB::select("SELECT a.tipe,a.no_lot, a.nm_partai, a.no_box, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, b.name
        FROM bk as a
        left join users as b on b.id = a.penerima
        WHERE a.no_lot = ? AND a.nm_partai = ? AND a.kategori ='cabut'
        GROUP BY a.no_box $whereLimit", [$no_lot, $nm_partai]);
        return $result;
    }
    public static function datacabutperbox($no_box)
    {
        $result = DB::selectOne("SELECT 
        sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(a.gr_flx) as gr_flx , a.no_box,  c.eot as eot_rp, 
        c.batas_eot, sum(a.rupiah) as rupiah, sum(a.ttl_rp) as ttl_rp
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori ='cabut'
        left join tb_kelas as c on c.id_kelas = a.id_kelas
        where a.no_box = ?
        group by a.no_box
        ;", [$no_box]);

        return $result;
    }
    public static function bk_cabut_sum($nm_partai)
    {
        $result = DB::selectOne("SELECT a.no_lot, a.nm_partai, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal
        FROM bk as a
        WHERE a.nm_partai = '$nm_partai' AND a.kategori ='cabut'
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
}
