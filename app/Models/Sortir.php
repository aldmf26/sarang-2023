<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sortir extends Model
{
    public static function queryRekapGroup($bulan, $tahun)
    {
        $cabutGroup = DB::select("SELECT 
                        max(b.name) as pengawas, 
                        e.ttl_box,
                        a.id_pengawas,
                        c.pcs_awal,
                        c.gr_awal,
                        c.gr_akhir,
                        c.pcs_akhir,
                        d.gr_bk,
                        d.pcs_bk,
                        c.ttl_rp,
                        sum((1 - c.gr_akhir / c.gr_awal) * 100) as susut,
                        c.rp_target
                        FROM sortir as a 
                        left join users as b on b.id = a.id_pengawas 
                        LEFT JOIN (
                            SELECT 
                                id_pengawas,no_box, 
                                sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal, 
                                sum(gr_akhir) as gr_akhir, sum(pcs_akhir) as pcs_akhir,
                                SUM(rp_target) as rp_target,
                                SUM(ttl_rp) as ttl_rp
                                FROM sortir WHERE no_box != 9999 AND penutup = 'T' AND bulan = '$bulan' AND YEAR(tgl) = '$tahun' GROUP BY id_pengawas
                        ) as c ON c.id_pengawas = a.id_pengawas
                        LEFT JOIN (
                            SELECT a.penerima,a.no_box,sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk FROM bk as a
                            JOIN (
                                SELECT no_box,id_pengawas FROM sortir where bulan = '$bulan' AND YEAR(tgl) = '$tahun' GROUP BY no_box
                            ) as b on a.no_box = b.no_box
                            WHERE a.kategori LIKE '%sortir%' and a.selesai = 'T'
                            GROUP by a.penerima
                        ) as d ON d.penerima = a.id_pengawas
                        LEFT JOIN (
                            SELECT id_pengawas, COUNT(DISTINCT no_box) as ttl_box
                            FROM sortir WHERE no_box != 9999 AND penutup = 'T' AND bulan = '$bulan' AND YEAR(tgl) = '$tahun'
                            GROUP BY id_pengawas
                        ) as e ON e.id_pengawas = a.id_pengawas
                        WHERE  a.no_box != 9999 AND a.penutup = 'T' AND a.bulan = '$bulan' AND YEAR(a.tgl) = '$tahun'
                        GROUP BY a.id_pengawas");
        return $cabutGroup;
    }

    public static function queryRekap($id_pengawas = null, $bulan = null, $tahun = null)
    {
        $where = $id_pengawas == 'all' ? '' : "AND a.id_pengawas = $id_pengawas";

        return DB::select("SELECT c.kategori,max(b.name) as pengawas, max(a.tgl) as tgl, a.no_box, 
        SUM(a.pcs_awal) as pcs_awal , sum(a.gr_awal) as gr_awal,
        SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, c.pcs_bk, c.gr_bk,
         sum(a.rp_target) as rp_target,sum(a.ttl_rp) as rupiah,sum((1 - a.gr_akhir / a.gr_awal) * 100) as susut
        FROM sortir as a
        left join users as b on b.id = a.id_pengawas
        LEFT JOIN (
            SELECT no_box,penerima, kategori, sum(pcs_awal) as pcs_bk, sum(gr_awal) as gr_bk FROM bk WHERE kategori LIKE '%sortir%' AND selesai = 'T' GROUP BY no_box,penerima
        ) as c on c.no_box = a.no_box and c.penerima = a.id_pengawas
        WHERE  a.no_box != 9999 AND a.penutup = 'T' $where AND a.bulan = '$bulan' AND YEAR(a.tgl) = '$tahun'
        GROUP by a.no_box,a.id_pengawas
        ");
    }
}
