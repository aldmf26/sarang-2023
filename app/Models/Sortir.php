<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sortir extends Model
{
    public static function queryRekapGroup($tgl1, $tgl2)
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
                                FROM sortir WHERE no_box != 9999 GROUP BY id_pengawas
                        ) as c ON c.id_pengawas = a.id_pengawas
                        LEFT JOIN (
                            SELECT penerima,no_box,sum(pcs_awal) as pcs_bk, sum(gr_awal) as gr_bk FROM `bk` WHERE kategori = 'sortir' GROUP BY penerima
                        ) as d ON d.penerima = a.id_pengawas
                        LEFT JOIN (
                            SELECT id_pengawas, COUNT(DISTINCT no_box) as ttl_box
                            FROM sortir WHERE no_box != 9999
                            GROUP BY id_pengawas
                        ) as e ON e.id_pengawas = a.id_pengawas
                        WHERE  a.no_box != 9999 AND a.penutup = 'T' 
                        GROUP BY a.id_pengawas");
        return $cabutGroup;
    }
}
