<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga3_3UsulanController extends Controller
{
    public function index(Request $r)
    {
        $data = [
            'title' => 'Usulan dan IDentifikasi Kebutuhan Pelatihan',
            'usulan' => DB::table('usulan_identifikasi')->leftJoin('hasil_wawancara', 'usulan_identifikasi.id_karyawan', '=', 'hasil_wawancara.id')
                ->where('usulan_identifikasi.id_divisi', $r->divisi)
                ->orderBy('usulan_identifikasi.id', 'desc')->get(),
            'tahun' => DB::select("select distinct YEAR(tgl_rencana) as tahun from `program_pelatihan_tahunan`"),
            'hasil_wawancara' => DB::table('hasil_wawancara')->where('id_divisi', $r->divisi)->get(),
            'divisi' => $r->divisi,
        ];
        return view('hccp.hrga3_pelatihan.hrga3.index', $data);
    }


    public function store(Request $r)
    {
        for ($i = 0; $i < count($r->id_karyawan); $i++) {
            DB::table('usulan_identifikasi')->insert([
                'id_karyawan' => $r->id_karyawan[$i],
                'id_divisi' => $r->id_divisi,
                'pengusul' => $r->pengusul,
                'usulan' => $r->usulan,
                'waktu' => $r->waktu,
                'alasan' => $r->alasan,

            ]);
        }
        return redirect()->route('hrga3_3.index', ['divisi' => $r->id_divisi])->with('success', 'Data tawaran pelatihan berhasil disimpan');
    }



    public function print(Request $r)
    {
        $data = [
            'title' => 'Informasi Tawaran Pelatihan',
            'usulan' => DB::table('usulan_identifikasi')->leftJoin('hasil_wawancara', 'usulan_identifikasi.id_karyawan', '=', 'hasil_wawancara.id')
                ->where('usulan_identifikasi.id_divisi', $r->divisi)
                ->orderBy('usulan_identifikasi.id', 'desc')->get(),

        ];
        return view('hccp.hrga3_pelatihan.hrga3.print', $data);
    }
}
