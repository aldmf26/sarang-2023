<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Eo extends Model
{
    public static function queryRekap($id_pengawas = null)
    {
        return DB::select("SELECT a.no_box,b.gr_bk, sum(a.gr_eo_awal) as gr_eo_awal, sum(a.gr_eo_akhir) as gr_eo_akhir, (b.gr_bk - sum(a.gr_eo_awal)) as gr_sisa, sum(a.ttl_rp) as rupiah,
        ((1 - (sum(a.gr_eo_akhir) / sum(a.gr_eo_awal))) * 100) as susut
        FROM `eo` as a 
        JOIN (
            SELECT no_box,penerima, sum(gr_awal) as gr_bk FROM bk GROUP BY no_box,penerima
        ) as b on a.id_pengawas = b.penerima AND a.no_box = b.no_box
        WHERE a.id_pengawas = '$id_pengawas' AND a.no_box != 9999 GROUP BY a.no_box;");
    }
    
}
