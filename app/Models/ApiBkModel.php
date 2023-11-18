<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApiBkModel extends Model
{
    use HasFactory;
    public static function datacabut($no_lot)
    {
        $result = DB::selectOne("SELECT a.no_lot, sum(b.pcs_awal) as pcs_awal, sum(b.gr_awal) as gr_awal, sum(b.rupiah) as rupiah 
        FROM bk as a 
        left join cabut as b on b.no_box = a.no_box
        where a.no_lot = ? and b.selesai = 'T' and a.kategori = 'cabut';
        ", [$no_lot]);

        return $result;
    }
    public static function datacetak($no_lot)
    {
        $result = DB::selectOne("SELECT a.no_lot, sum(b.pcs_awal) as pcs_awal, sum(b.gr_awal) as gr_awal, sum(b.rp_pcs * b.pcs_awal ) as rupiah 
        FROM bk as a 
        left join cetak as b on b.no_box = a.no_box
        where a.no_lot = ? and b.selesai = 'T'and a.kategori = 'cetak';
        ", [$no_lot]);

        return $result;
    }
    public static function datasortir($no_lot)
    {
        $result = DB::selectOne("SELECT a.no_lot, sum(b.pcs_awal) as pcs_awal, sum(b.gr_awal) as gr_awal, sum(b.rp_target ) as rupiah 
        FROM bk as a 
        left join sortir as b on b.no_box = a.no_box
        where a.no_lot = ? and b.selesai = 'T' and a.kategori = 'sortir';
        ", [$no_lot]);

        return $result;
    }
}
