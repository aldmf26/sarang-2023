<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Grading extends Model
{
    public static function dapatkanStokBox($jenis, $noBox = null)
    {
        $whereBox = $noBox ? "AND b.no_box in ($noBox) " : '';
        $formulir = DB::select("SELECT 
        b.no_box, b.tanggal, e.tipe, c.name as pemberi, b.no_invoice, sum(b.pcs_awal - d.pcs) as pcs_awal, sum(b.gr_awal - d.gr) as gr_awal
        FROM grading as a 
        JOIN formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade'
        JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'sortir'
        $whereBox
        LEFT JOIN(
            select no_box_sortir as no_box,sum(pcs) as pcs,sum(gr) as gr
            from grading 
            group by no_box_sortir
        ) as d on d.no_box = b.no_box
        JOIN users as c on c.id = b.id_pemberi
        GROUP BY b.no_box
        HAVING sum(b.pcs_awal - d.pcs) > 0 OR sum(b.gr_awal - d.gr) > 0
        ORDER BY b.tanggal DESC");

        $arr = [
            'formulir' => $formulir,
            'pengawas' => DB::table('users')->where('posisi_id', 13)->get()
        ];
        return $arr[$jenis];
    }
}
