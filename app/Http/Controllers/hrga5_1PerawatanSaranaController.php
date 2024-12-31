<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga5_1PerawatanSaranaController extends Controller
{
    public function index()
    {
        $tahun = 2025;
        return view(
            'hccp.hrga5_pemeliharaan.hrga1.index',
            [
                'title' => 'Perawatan Sarana',
                'bulan' => DB::table('bulan')->get(),
                'pemeliharaan' => DB::table('hrga5_1pemeliharaan')->whereYear('tanggal_mulai', $tahun)->get(),
                'tahun' => $tahun
            ]
        );
    }
    public function print()
    {
        $tahun = 2025;
        return view(
            'hccp.hrga5_pemeliharaan.hrga1.print',
            [
                'title' => 'Perawatan Sarana',
                'bulan' => DB::table('bulan')->get(),
                'pemeliharaan' => DB::table('hrga5_1pemeliharaan')->whereYear('tanggal_mulai', $tahun)->get(),
                'tahun' => $tahun
            ]
        );
    }

    public function store(Request $r)
    {
        $data = [
            'nama_sarana' => $r->nama_sarana,
            'merek' => $r->merek,
            'no_identifikasi' => $r->no_identifikasi,
            'lokasi' => $r->lokasi,
            'frekuensi_perawatan' => $r->frekuensi_perawatan,
            'tanggal_mulai' => $r->tgl_mulai,
            'penanggung_jawab' => $r->penanggung_jawab,
        ];
        DB::table('hrga5_1pemeliharaan')->insert($data);
        return redirect()->route('hrga5_1.index')->with('sukses', 'Data Berhasil ditambahkan');
    }
}
