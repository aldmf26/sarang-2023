<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PengirimanModel extends Model
{
    public static function Pengiriman()
    {
        $result = DB::select("SELECT a.grade, a.no_box, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.gr * a.rp_gram) as ttl_rp, if(c.pcs_ambil is null,0,c.pcs_ambil) as pcs_ambil, if(c.gr_ambil is null,0,c.gr_ambil) as gr_ambil, if(c.ttl_rp_ambil is null,0,c.ttl_rp_ambil) as ttl_rp_ambil
        FROM siapkirim_list_grading as a
        left join (
        SELECT c.grade, sum(c.pcs) as pcs_ambil, sum(c.gr) as gr_ambil, sum(c.gr * c.rp_gram) as ttl_rp_ambil
            FROM pengiriman as c 
            GROUP by c.grade
        ) as c on c.grade = a.grade
        -- where a.no_box is not null 
        GROUP by a.grade
        HAVING pcs - pcs_ambil <> 0 OR gr - gr_ambil <> 0
        ");

        return $result;
    }
    public static function pengirimanPerGrade($grade)
    {
        $result = DB::selectOne("SELECT a.grade, a.no_box, sum(a.pcs) as pcs_awal, sum(a.gr) as gr_awal, sum(a.gr * a.rp_gram) as ttl_rp, if(c.pcs_ambil is null,0,c.pcs_ambil) as pcs_ambil, if(c.gr_ambil is null,0,c.gr_ambil) as gr_ambil, if(c.ttl_rp_ambil is null,0,c.ttl_rp_ambil) as ttl_rp_ambil
        FROM siapkirim_list_grading as a
        left join (
        SELECT c.grade, sum(c.pcs) as pcs_ambil, sum(c.gr) as gr_ambil, sum(c.gr * c.rp_gram) as ttl_rp_ambil
            FROM pengiriman as c 
            GROUP by c.grade
        ) as c on c.grade = a.grade
        where  a.grade = '$grade'
        GROUP by a.grade;
        ");

        return $result;
    }
}
