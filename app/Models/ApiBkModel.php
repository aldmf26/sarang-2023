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
        $result = DB::select("SELECT a.no_lot, sum(b.pcs_awal) as pcs_awal, sum(b.gr_awal) as gr_awal, sum(b.rupiah) as rupiah 
        FROM bk as a 
        left join cabut as b on b.no_box = a.no_box
        where a.no_lot = ?;
        ", [$no_lot]);

        return $result;
    }
}
