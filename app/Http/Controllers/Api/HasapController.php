<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasapController extends Controller
{
    public function index()
    {
        $data = DB::select("SELECT c.no_invoice, c.tanggal, b.name, sum(c.pcs_awal) as pcs, sum(c.gr_awal) as gr_awal
        FROM cabut as a 
        left join users as b on b.id = a.id_pengawas
        join (
            SELECT c.no_invoice, c.no_box, c.pcs_awal, c.gr_awal, c.tanggal
            FROM formulir_sarang as c 
            where c.kategori ='cabut'
        ) as c on c.no_box = a.no_box
        group by c.no_invoice
        order by c.no_invoice DESC");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function detail($no_invoice)
    {
        $data = DB::select("SELECT d.nama, c.no_invoice, a.no_box, e.tipe, a.pcs_awal, a.gr_awal
FROM cabut as a 
left join users as b on b.id = a.id_pengawas
left join tb_anak as d on d.id_anak = a.id_anak
join (
	SELECT c.no_invoice, c.no_box, c.pcs_awal, c.gr_awal, c.tanggal
    FROM formulir_sarang as c 
    where c.kategori ='cabut'
	group by c.no_box
) as c on c.no_box = a.no_box
left join (
	SELECT e.no_box, e.tipe
    FROM bk as e
    where e.kategori = 'cabut'
    group by e.no_box
) as e on e.no_box = a.no_box

where c.no_invoice='$no_invoice';");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
}
