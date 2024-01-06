<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Eo extends Model
{
    public static function queryRekap($id_pengawas = null,$bulan = null, $tahun = null)
    {
        return DB::select("SELECT a.no_box,b.gr_bk, sum(a.gr_eo_awal) as gr_eo_awal, sum(a.gr_eo_akhir) as gr_eo_akhir, (b.gr_bk - sum(a.gr_eo_awal)) as gr_sisa, sum(a.ttl_rp) as rupiah,
        ((1 - (sum(a.gr_eo_akhir) / sum(a.gr_eo_awal))) * 100) as susut
        FROM `eo` as a 
        JOIN (
            SELECT no_box,penerima, sum(gr_awal) as gr_bk FROM bk where selesai = 'T' GROUP BY no_box,penerima
        ) as b on a.id_pengawas = b.penerima AND a.no_box = b.no_box
        WHERE a.id_pengawas = '$id_pengawas' AND a.no_box != 9999 AND a.penutup = 'T' AND a.bulan_dibayar = '$bulan' AND YEAR(a.tgl_serah) = '$tahun' GROUP BY a.no_box;");
    }

    public static function queryRekapGroup($bulan, $tahun)
    {
        $cabutGroup = DB::select("SELECT 
        max(b.name) as pengawas, 
        e.ttl_box,
        a.id_pengawas,
        c.gr_awal,
        c.gr_akhir,
        d.gr_bk,
        c.ttl_rp,
        sum((1 - c.gr_akhir / c.gr_awal) * 100) as susut
        FROM eo as a
        JOIN users as b on a.id_pengawas = b.id
        LEFT JOIN (
            select id_pengawas,sum(gr_eo_awal) as gr_awal, sum(gr_eo_akhir) as gr_akhir, sum(ttl_rp) as ttl_rp 
            from eo where no_box != 9999 and penutup = 'T' AND bulan_dibayar = '$bulan' AND YEAR(tgl_input) = '$tahun' GROUP BY id_pengawas
        ) as c on a.id_pengawas = c.id_pengawas
        LEFT JOIN (
            SELECT a.penerima,a.no_box,sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk FROM bk as a
            JOIN (
                SELECT no_box FROM eo where bulan_dibayar = '$bulan' AND YEAR(tgl_input) = '$tahun' GROUP BY no_box
            ) as b on a.no_box = b.no_box
            WHERE a.kategori LIKE '%cabut%'
            GROUP by a.penerima
        ) as d on a.id_pengawas = d.penerima
        LEFT JOIN (
            SELECT id_pengawas, COUNT(DISTINCT no_box) as ttl_box
            FROM eo WHERE no_box != 9999 AND penutup = 'T' AND bulan_dibayar = '$bulan' AND YEAR(tgl_input) = '$tahun'
            GROUP BY id_pengawas
        ) as e ON e.id_pengawas = a.id_pengawas
        WHERE  a.no_box != 9999 AND a.penutup = 'T' AND a.bulan_dibayar = '$bulan' AND YEAR(a.tgl_input) = '$tahun'
        GROUP BY a.id_pengawas;");
        return $cabutGroup;
    }

    
}
