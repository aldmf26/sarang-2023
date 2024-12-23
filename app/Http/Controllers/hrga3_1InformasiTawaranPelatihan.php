<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga3_1InformasiTawaranPelatihan extends Controller
{
    public function index(Request $r)
    {
        $data = [
            'title' => 'Informasi Tawaran Pelatihan',
            'tawaran' => DB::table('tawaran_pelatihan')
                ->leftJoin('divisis', 'divisis.id', 'tawaran_pelatihan.id_divisi')
                ->where('tawaran_pelatihan.id_divisi', $r->divisi)
                ->orderBy('id_tawaran_pelatihan', 'desc')->get(),
            'id_divisi' => $r->divisi
        ];
        return view('hccp.hrga3_pelatihan.hrga1.index', $data);
    }

    public function store(Request $r)
    {
        DB::table('tawaran_pelatihan')->insert([
            'id_divisi' => $r->id_divisi,
            'tgl_informasi' => $r->tanggal_informasi,
            'jenis_pelatihan' => $r->jenis_pelatihan,
            'sasaran_pelatihan' => $r->sasaran_pelatihan,
            'tema_pelatihan' => $r->tema_pelatihan,
            'sumber_informasi' => $r->sumber_informasi,
            'personil_penghubung' => $r->personil_penghubung,
            'no_telp' => $r->no_telp,
            'email' => $r->email,
        ]);
        return redirect()->route('hrga3_1.index', ['divisi' => $r->id_divisi])->with('success', 'Data tawaran pelatihan berhasil disimpan');
    }

    public function print(Request $r)
    {
        $data = [
            'title' => 'Informasi Tawaran Pelatihan',
            'tawaran' => DB::table('tawaran_pelatihan')
                ->leftJoin('divisis', 'divisis.id', 'tawaran_pelatihan.id_divisi')
                ->where('tawaran_pelatihan.id_divisi', $r->divisi)
                ->orderBy('id_tawaran_pelatihan', 'desc')->get(),
            'id_divisi' => $r->divisi
        ];
        return view('hccp.hrga3_pelatihan.hrga1.print', $data);
    }
}
