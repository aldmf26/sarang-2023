<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga8_1Program_peratawan extends Controller
{
    public function index()
    {
        $tahun = 2025;
        $data = [
            'title' => 'Perawatan Mesin',
            'bulan' => DB::table('bulan')->get(),
            'pemeliharaan' => DB::table('program_perawatan')
                ->leftJoin('item_perawatan', 'item_perawatan.id', '=', 'program_perawatan.id_item')
                ->leftJoin('lokasi', 'item_perawatan.lokasi_id', '=', 'lokasi.id')
                ->select('program_perawatan.*', 'item_perawatan.nama', 'item_perawatan.merk', 'item_perawatan.no_identifikasi', 'lokasi.lokasi')
                ->whereYear('tgl_mulai', $tahun)->get(),
            'lokasi' => DB::table('lokasi')->get(),
            'tahun' => $tahun
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga1.index', $data);
    }

    public function store(Request $r)
    {
        $data = [
            'id_item' => $r->id_item,
            'frekuensi_perawatan' => $r->frekuensi_perawatan,
            'tgl_mulai' => $r->tgl_mulai,
            'penanggung_jawab' => $r->penanggung_jawab,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        DB::table('program_perawatan')->insert($data);

        return redirect()->route('hrga8.index', ['tahun' => $r->tahun])->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function print()
    {
        $tahun = 2025;
        $data = [
            'title' => 'Perawatan Mesin',
            'bulan' => DB::table('bulan')->get(),
            'pemeliharaan' => DB::table('program_perawatan')
                ->leftJoin('item_perawatan', 'item_perawatan.id', '=', 'program_perawatan.id_item')
                ->leftJoin('lokasi', 'item_perawatan.lokasi_id', '=', 'lokasi.id')
                ->select('program_perawatan.*', 'item_perawatan.nama', 'item_perawatan.merk', 'item_perawatan.no_identifikasi', 'lokasi.lokasi')
                ->whereYear('tgl_mulai', $tahun)->get(),
            'lokasi' => DB::table('lokasi')->get(),
            'tahun' => $tahun
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga1.print', $data);
    }
    public function get_item(Request $r)
    {
        $item = DB::table('item_perawatan')->where('lokasi_id', $r->id)->where('mesin', 'Y')->get();



        foreach ($item as $key => $value) {
            $no_identifikasi =  empty($value->no_identifikasi) ? '' : "($value->no_identifikasi)";
            $nama =  $value->nama . ' ' . $value->merk . ' ' . $no_identifikasi;
            echo '<option value="' . $value->id . '">' . $nama . '</option>';
        }
    }
}
