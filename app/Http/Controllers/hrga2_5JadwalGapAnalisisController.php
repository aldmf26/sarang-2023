<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga2_5JadwalGapAnalisisController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Jadwal Gap Analisis',
            'jadwal_gap' => DB::table('jadwal_gap')->leftJoin('divisis', 'divisis.id', '=', 'jadwal_gap.id_divisi')->get(),
            'divisis' => DB::table('divisis')->get(),
            'bulan' => DB::table('bulan')->get(),
            'tahun' => DB::select("SELECT DISTINCT tahun FROM jadwal_gap ORDER BY tahun DESC"),
        ];
        return view('hccp.hrga2_penilaian.hrga5.index', $data);
    }

    public function save_jadwal(Request $r)
    {
        $data = [
            'id_divisi' => $r->id_divisi,
            'bulan' => $r->bulan,
            'tahun' => $r->tahun,
            'tgl_awal_realisasi' => $r->tgl_awal_realisasi,
            'tgl_akhir_realisasi' => $r->tgl_akhir_realisasi,
        ];
        DB::table('jadwal_gap')->insert($data);
        return redirect()->back()->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function print(Request $r)
    {
        $data = [
            'title' => 'Jadwal Gap Analisis',
            'jadwal_gap' => DB::table('jadwal_gap')->leftJoin('divisis', 'divisis.id', '=', 'jadwal_gap.id_divisi')->where('tahun', $r->tahun)->get(),
            'divisis' => DB::table('divisis')->get(),
            'bulan' => DB::table('bulan')->get(),
            'tahun' => $r->tahun,
        ];
        return view('hccp.hrga2_penilaian.hrga5.print', $data);
    }
}
