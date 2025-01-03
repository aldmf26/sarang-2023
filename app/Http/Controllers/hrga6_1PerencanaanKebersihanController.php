<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga6_1PerencanaanKebersihanController extends Controller
{
    public function index(Request $r)
    {
        $area = $r->area ?? 'Wc';

        $lokasi = DB::table('lokasi')->get();
        $getIdLokasi = DB::table('lokasi')->where('lokasi', $area)->first()->id;

        $datas = DB::table('hrga6_perencanaan_sanitasi')->where('id_lokasi', $getIdLokasi)->get();
        $data = [
            'title' => 'Perencanaan Kebersihan / Sanitasi',
            'area' => $area,
            'datas' => $datas,
            'lokasi' => $lokasi,
            'id_lokasi' => $getIdLokasi,
        ];
        return view('hccp.hrga6_sanitasi.hrga1.index', $data);
    }

    public function store(Request $r)
    {
        try {
            DB::beginTransaction();

            for ($i = 0; $i < count($r->nm_alat); $i++) {
                $data[] = [
                    'id_lokasi' => $r->id_lokasi[$i],
                    'nm_alat' => $r->nm_alat[$i],
                    'identifikasi_alat' => $r->identifikasi_alat[$i],
                    'metode' => $r->metode[$i],
                    'penanggung_jawab' => $r->penanggung_jawab[$i],
                    'frekuensi' => $r->frekuensi[$i],
                    'sarana_cleaning' => $r->sarana_cleaning[$i],
                    'sanitizer' => $r->sanitizer[$i],
                    'tgl' => now(),
                    'admin' => auth()->user()->name
                ];
            }
            DB::table('hrga6_perencanaan_sanitasi')->insert($data);

            DB::commit();
            return redirect()->back()->with('sukses', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function print(Request $r)
    {
        $datas = DB::table('hrga6_perencanaan_sanitasi')->where('id_lokasi', $r->id_lokasi)->get();

        $data = [
            'title' => 'Perencanaan Kebersihan / Sanitasi',
            'area' => $r->area,
            'dok' => 'FRM.HRGA.06.01, Rev.00',
            'datas' => $datas
        ];

        return view('hccp.hrga6_sanitasi.hrga1.print', $data);
    }

}
