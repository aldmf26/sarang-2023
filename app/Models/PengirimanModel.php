<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PengirimanModel extends Model
{
    public static function Pengiriman()
    {
        $result = DB::select("SELECT a.grade, a.no_box, sum(a.pcs_kredit) as pcs_awal, sum(a.gr_kredit) as gr_awal, sum(a.gr_kredit * a.rp_gram_kredit) as ttl_rp, sum(b.pcs_akhir) as pcs_akhir, sum(b.gr_akhir) as gr_akhir, sum(b.ttl_rp_sortir) as ttl_rp_sortir, c.pcs_ambil, c.gr_ambil
        FROM pengiriman_list_gradingbj as a
        left join (
             SELECT b.no_box, sum(b.pcs_akhir) as pcs_akhir, sum(b.gr_akhir) as gr_akhir, sum(b.ttl_rp) as 		ttl_rp_sortir
            FROM sortir as b 
            GROUP by b.no_box
         ) as b on b.no_box = a.no_box
        left join (
        SELECT c.grade, sum(c.pcs) as pcs_ambil, sum(c.gr) as gr_ambil
            FROM pengiriman as c 
            GROUP by c.grade
        ) as c on c.grade = a.grade
        where a.no_box is not null
        GROUP by a.grade;
        ");

        return $result;
    }
    public static function pengirimanPerGrade($grade)
    {
        $result = DB::select("SELECT a.grade, a.no_box, sum(a.pcs_kredit) as pcs_awal, sum(a.gr_kredit) as gr_awal, sum(a.gr_kredit * a.rp_gram_kredit) as ttl_rp, sum(b.pcs_akhir) as pcs_akhir, sum(b.gr_akhir) as gr_akhir, sum(b.ttl_rp_sortir) as ttl_rp_sortir, c.pcs_ambil, c.gr_ambil
        FROM pengiriman_list_gradingbj as a
        left join (
             SELECT b.no_box, sum(b.pcs_akhir) as pcs_akhir, sum(b.gr_akhir) as gr_akhir, sum(b.ttl_rp) as ttl_rp_sortir
            FROM sortir as b 
            GROUP by b.no_box
         ) as b on b.no_box = a.no_box
        left join (
        SELECT c.grade, sum(c.pcs) as pcs_ambil, sum(c.gr) as gr_ambil
            FROM pengiriman as c 
            GROUP by c.grade
        ) as c on c.grade = a.grade
        where a.no_box is not null and a.grade = '$grade'
        GROUP by a.grade;
        ");

        return $result;
    }
}
