<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LaporanModel extends Model
{
    use HasFactory;

    public static function LaporanPerPartai()
    {
        $result = DB::select("SELECT a.nm_partai, a.no_box, a.pcs_awal, a.gr_awal,a.hrga_satuan,
        b.pcs_akhir as pcs_cbt, b.gr_akhir as gr_cbt, (((a.hrga_satuan * a.gr_awal) + b.ttl_rp ) / b.gr_akhir) as rp_gram_cbt, ((1-(b.gr_akhir / a.gr_awal)) * 100) as sst_cbt,
        c.pcs_akhir as pcs_ctk, c.gr_akhir as gr_ctk, (((a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp ) / c.gr_akhir) as rp_gram_ctk, ((1-((c.gr_akhir + c.gr_tdk_cetak) / a.gr_awal)) * 100) as sst_ctk,
        d.pcs_akhir as pcs_str, d.gr_akhir as gr_str, (((a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp + d.ttl_rp ) / c.gr_akhir) as rp_gram_str, ((1-(d.gr_akhir / a.gr_awal)) * 100) as sst_str,

        (a.hrga_satuan * a.gr_awal) as cost_bk, b.ttl_rp as cost_cbt, c.ttl_rp as cost_ctk, d.ttl_rp as cost_str
        FROM bk as a 
        left join cabut as b on b.no_box = a.no_box
        left join cetak_new as c on c.no_box = a.no_box 
        left join sortir as d on d.no_box = a.no_box
        where a.kategori = 'cabut' and a.baru = 'baru';");
        return $result;
    }

    public static function LaporanDetailCetak($partai, $bulan)
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai,
        a.tgl, d.name as pengawas,  c.nama, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_akhir, a.gr_akhir,
        a.rp_satuan, a.ttl_rp, a.bulan_dibayar
        FROM cetak_new as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai' and a.bulan_dibayar = '$bulan'
        order by a.tgl ASC;");

        return $result;
    }
    public static function LaporanDetailCabut($partai, $bulan)
    {
        $result = DB::select(
            "SELECT * , ((1 - (gr_akhir/gr_awal)) * 100 ) as susut
        FROM (
        SELECT a.no_box, d.name as pengawas, c.nama, a.tgl_terima as tgl, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp, 'cabut' as kerja, a.bulan_dibayar
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai' and a.bulan_dibayar = '$bulan'
        UNION ALL
        SELECT a.no_box, d.name as pengawas, c.nama, a.tgl_ambil as tgl, 0 as pcs_awal, a.gr_eo_awal as gr_awal, 0 as pcs_akhir, a.gr_eo_akhir as gr_akhir, 
        a.ttl_rp, 
        'eo' as kerja , a.bulan_dibayar
        FROM eo as a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai' and a.bulan_dibayar = '$bulan'
        ) AS combined_result
        order by  ((1 - (gr_akhir/gr_awal)) * 100) DESC ;"
        );

        return $result;
    }

    public static function LaporanDetailSortir($partai, $bulan)
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai,
        a.tgl, d.name as pengawas,  c.nama, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp, a.bulan
        FROM sortir as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai' and a.bulan = '$bulan'
        order by a.tgl ASC;");

        return $result;
    }

    public static function LaporanDetailBox($no_box)
    {
        $cabut = DB::selectOne("SELECT b.name as pgws, c.nama as nm_anak, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp,
        (d.hrga_satuan * d.gr_awal) as cost_bk
        FROM cabut as a 
        left join users as b on b.id = a.id_pengawas
        left join tb_anak as c on c.id_anak = a.id_anak
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
        where a.no_box = '$no_box'
        ");

        $cetak = DB::selectOne("SELECT b.name as pgws, c.nama as nm_anak, a.pcs_awal_ctk as pcs_awal, a.gr_awal_ctk as gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp,
        (d.hrga_satuan * d.gr_awal) as cost_bk, e.ttl_rp as cost_cbt
        FROM cetak_new as a
        left join users as b on b.id = a.id_pengawas
        left join tb_anak as c on c.id_anak = a.id_anak
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
        left join cabut as e on e.no_box = a.no_box
        where a.no_box = '$no_box'
        ");

        $sortir = DB::selectOne("SELECT b.name as pgws, c.nama as nm_anak, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp,
        (d.hrga_satuan * d.gr_awal) as cost_bk, e.ttl_rp as cost_cbt, f.ttl_rp as cost_ctk
        FROM sortir as a
        left join users as b on b.id = a.id_pengawas
        left join tb_anak as c on c.id_anak = a.id_anak
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
        left join cabut as e on e.no_box = a.no_box
        left join cetak_new as f on f.no_box = a.no_box
        where a.no_box = '$no_box'
        ");


        return (object)[

            'cabut' => $cabut,
            'cetak' => $cetak,
            'sortir' => $sortir

        ];
    }
}
