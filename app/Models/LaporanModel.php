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
        $result = DB::select("SELECT a.nm_partai, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.hrga_satuan) as hrga_satuan,
        sum(a.gr_awal * a.hrga_satuan) as ttl_rp, if(b.cost_cbt is null ,0 ,b.cost_cbt) as cost_cbt , if(d.cost_eo is null,0,d.cost_eo) as cost_eo , if(c.cost_ctk is null,0,c.cost_ctk) as cost_ctk, e.pcs as pcs_akhir, e.gr as gr_akhir, if(f.cost_sortir is null,0,f.cost_sortir) as cost_sortir
        FROM bk as a
        left join (
        SELECT c.nm_partai,  sum(b.ttl_rp) as cost_cbt
            FROM cabut as b 
            left join bk as c on c.no_box = b.no_box and c.kategori = 'cabut'
            group by c.nm_partai
        ) as b on b.nm_partai = a.nm_partai
        
        left join (
            SELECT e.nm_partai, sum(d.ttl_rp) as cost_ctk
            FROM cetak_new as d
            left join bk as e on e.no_box = d.no_box and e.kategori = 'cabut'
            group by e.nm_partai
        ) as c on c.nm_partai = a.nm_partai
        
        left join (
            SELECT g.nm_partai, sum(f.ttl_rp) as cost_eo
            FROM eo as f
            left join bk as g on f.no_box = g.no_box and g.kategori = 'cabut'
            group by g.nm_partai
        ) as d on d.nm_partai = a.nm_partai

        left join bk_akhir as e on e.nm_partai = a.nm_partai

        left join (
            SELECT i.nm_partai, sum(h.ttl_rp) as cost_sortir
            FROM sortir as h
            left join bk as i on h.no_box = i.no_box and i.kategori = 'cabut'
            group by i.nm_partai
        ) as f on f.nm_partai = a.nm_partai
        
        where a.kategori = 'cabut'
        GROUP by a.nm_partai;");

        return $result;
    }

    public static function LaporanDetailCetak($partai)
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai,
        a.tgl, d.name as pengawas,  c.nama, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_akhir, a.gr_akhir,
        a.rp_satuan, a.ttl_rp
        FROM cetak_new as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai'
        order by a.tgl ASC;");

        return $result;
    }
    public static function LaporanDetailCabut($partai)
    {
        $result = DB::select(
            "SELECT * , ((1 - (gr_akhir/gr_awal)) * 100 ) as susut
        FROM (
        SELECT a.no_box, d.name as pengawas, c.nama, a.tgl_terima as tgl, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp, 'cabut' as kerja, a.bulan_dibayar
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai'
        UNION ALL
        SELECT a.no_box, d.name as pengawas, c.nama, a.tgl_ambil as tgl, 0 as pcs_awal, a.gr_eo_awal as gr_awal, 0 as pcs_akhir, a.gr_eo_akhir as gr_akhir, 
        a.ttl_rp, 
        'eo' as kerja , a.bulan_dibayar
        FROM eo as a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai'
        ) AS combined_result
        order by  ((1 - (gr_akhir/gr_awal)) * 100) DESC ;"
        );

        return $result;
    }

    public static function LaporanDetailSortir($partai)
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai,
        a.tgl, d.name as pengawas,  c.nama, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_akhir, a.gr_akhir,
        a.rp_satuan, a.ttl_rp
        FROM cetak_new as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai'
        order by a.tgl ASC;");

        return $result;
    }
}
