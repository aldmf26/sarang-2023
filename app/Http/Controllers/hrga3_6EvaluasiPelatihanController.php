<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga3_6EvaluasiPelatihanController extends Controller
{
    public function index(Request $r)
    {
        $karyawans = DB::table('hasil_wawancara')
            ->leftJoin('divisis', 'divisis.id', 'hasil_wawancara.id_divisi')
            ->select('hasil_wawancara.*', 'divisis.divisi')
            ->where('id_divisi', $r->divisi)
            ->orderBy('id', 'desc')
            ->get();
        $data = [
            'title' => 'Evaluasi pelatihan',
            'karyawans' => $karyawans,
            'divisi' => $r->divisi

        ];

        return view('hccp.hrga3_pelatihan.hrga6.index', $data);
    }

    public function print(Request $r)
    {
        $karyawans = DB::table('hasil_wawancara')
            ->leftJoin('divisis', 'divisis.id', '=', 'hasil_wawancara.id_divisi')
            ->where('hasil_wawancara.id', $r->id)->first();

        $data = [
            'title' => 'Evaluasi pelatihan',
            'karyawans' => $karyawans,
        ];
        return view('hccp.hrga3_pelatihan.hrga6.print', $data);
    }
}
