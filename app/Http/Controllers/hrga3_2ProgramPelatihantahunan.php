<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga3_2ProgramPelatihantahunan extends Controller
{
    public function index(Request $r)
    {
        $data = [
            'title' => 'Program Pelatihan Tahunan',
            'program' => DB::table('program_pelatihan_tahunan')->orderBy('id_program_pelatihan', 'desc')->get(),
            'tahun' => DB::select("select distinct YEAR(tgl_rencana) as tahun from `program_pelatihan_tahunan`"),
        ];
        return view('hccp.hrga3_pelatihan.hrga2.index', $data);
    }

    public function store(Request $r)
    {
        DB::table('program_pelatihan_tahunan')->insert([
            'materi_pelatihan' => $r->materi_pelatihan,
            'narasumber' => $r->narasumber,
            'sasaran_peserta' => $r->sasaran_peserta,
            'tgl_rencana' => $r->tgl_rencana,
            'tgl_realisasi' => $r->tgl_realisasi,
            'i' => $r->i,

        ]);
        return redirect()->route('hrga3_2.index')->with('success', 'Data tawaran pelatihan berhasil disimpan');
    }

    public function print(Request $r)
    {
        $data = [
            'title' => 'Informasi Tawaran Pelatihan',
            'program' => DB::select("SELECT * FROM program_pelatihan_tahunan as a WHERE YEAR(a.tgl_rencana) = $r->tahun order by a.id_program_pelatihan desc"),
            'tahun' => $r->tahun,
            'bulan' => DB::table('bulan')->get(),

        ];
        return view('hccp.hrga3_pelatihan.hrga2.print', $data);
    }
}
