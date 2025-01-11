<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga8_2Ceklistperawatanmesin extends Controller
{
    public function index()
    {
        $tahun = 2025;
        $data = [
            'title' => 'CEKLIST PERAWATAN MESIN PROSES PRODUKSI',
            'bulan' => DB::table('bulan')->get(),
            'pemeliharaan' => DB::table('program_perawatan')
                ->leftJoin('item_perawatan', 'item_perawatan.id', '=', 'program_perawatan.id_item')
                ->leftJoin('lokasi', 'item_perawatan.lokasi_id', '=', 'lokasi.id')
                ->select('program_perawatan.*', 'item_perawatan.nama', 'item_perawatan.merk', 'item_perawatan.no_identifikasi', 'lokasi.lokasi')
                ->whereYear('tgl_mulai', $tahun)->get(),
            'lokasi' => DB::table('lokasi')->get(),
            'tahun' => $tahun
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga2.index', $data);
    }

    public function tambah_baris(Request $r)
    {
        $data = [
            'count' => $r->count
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga2.tambah_baris', $data);
    }

    public function print($id)
    {
        $data = [
            'title' => "CEKLIST PERAWATAN MESIN PROSES PRODUKSI",
            'permintaan' => DB::table('program_perawatan')
                ->leftJoin('item_perawatan', 'item_perawatan.id', '=', 'program_perawatan.id_item')
                ->leftJoin('lokasi', 'item_perawatan.lokasi_id', '=', 'lokasi.id')
                ->select('program_perawatan.*', 'item_perawatan.nama', 'item_perawatan.merk', 'item_perawatan.no_identifikasi', 'lokasi.lokasi')
                ->where('program_perawatan.id', $id)->first(),
            'ceklis' => DB::table('ceklis_perawatan_mesin')->where('id_perawatan', $id)->get()
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga2.print', $data);
    }

    public function store(Request $r)
    {

        $kriteria = $r->kriteria;

        for ($i = 0; $i < count($kriteria); $i++) {
            $data = [
                'id_perawatan' => $r->id,
                'tanggal' => $r->tanggal,
                'status' => $r->status[$i],

                'kriteria_pemeriksaan' => $kriteria[$i],
                'metode' => $r->metode[$i],
                'hasil_pemeriksaan' => $r->hasil_pemeriksaan[$i],
                'ket' => $r->ket[$i]

            ];

            DB::table('ceklis_perawatan_mesin')->insert($data);
        }

        return redirect()->back()->with('sukses', 'Data Berhasil ditambahkan');
    }
}
