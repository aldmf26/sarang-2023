<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga5_3PermintaanPerbaikan extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Permintaan Perbaikan',
            'permintaan' => DB::table('hrga5_3permintaan')
                ->leftJoin('lokasi', 'hrga5_3permintaan.lokasi_id', '=', 'lokasi.id')
                ->leftJoin('item_perawatan', 'hrga5_3permintaan.item_id', '=', 'item_perawatan.id')
                ->select('hrga5_3permintaan.*', 'item_perawatan.nama', 'item_perawatan.merk', 'item_perawatan.no_identifikasi', 'lokasi.lokasi')
                ->orderBy('id', 'desc')->get(),
            'lokasi' => DB::table('lokasi')->get(),
        ];
        return view('hccp.hrga5_pemeliharaan.hrga3.index', $data);
    }

    public function get_item(Request $r)
    {
        $item = DB::table('item_perawatan')->where('lokasi_id', $r->id)->get();



        foreach ($item as $key => $value) {
            $no_identifikasi =  empty($value->no_identifikasi) ? '' : "($value->no_identifikasi)";
            $nama =  $value->nama . ' ' . $value->merk . ' ' . $no_identifikasi;
            echo '<option value="' . $value->id . '">' . $nama . '</option>';
        }
    }
    public function get_merk(Request $r)
    {
        $item = DB::table('item_perawatan')->where('id', $r->id)->first();

        $data = [
            'merk' => $item->merk ?? 'kosong',
            'no_identifikasi' => $item->no_identifikasi ?? 'kosong',
        ];

        return response()->json($data);
    }

    public function store(Request $r)
    {

        $data = [
            'item_id' => $r->item_id,
            'tgl' => $r->tanggal,
            'lokasi_id' => $r->lokasi_id,
            'no_identifikasi' => $r->no_identifikasi,
            'diajukan_oleh' => $r->diajukan_oleh,
            'deskripsi_masalah' => $r->deskripsi,
            'selesai' => 'T',
            'time' => now(),
        ];
        DB::table('hrga5_3permintaan')->insert($data);
        return redirect()->route('hrga5_3.index')->with('sukses', 'Data Berhasil ditambahkan');
    }
    public function store2(Request $r)
    {

        $data = [
            'id_permintaan' => $r->id,
            'detail_perbaikan' => $r->detail,
            'verifikasi_user' => $r->verifikasi,
            'time' => now(),
        ];
        DB::table('detail_perbaikan')->insert($data);

        $data2 = [
            'selesai' => 'Y',
        ];
        DB::table('hrga5_3permintaan')->where('id', $r->id)->update($data2);
        return redirect()->route('hrga5_3.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function print($id)
    {
        $data = [
            'title' => 'Permintaan Perbaikan',
            'permintaan' => DB::table('hrga5_3permintaan')
                ->leftJoin('lokasi', 'hrga5_3permintaan.lokasi_id', '=', 'lokasi.id')
                ->leftJoin('item_perawatan', 'hrga5_3permintaan.item_id', '=', 'item_perawatan.id')
                ->select('hrga5_3permintaan.*', 'item_perawatan.nama', 'item_perawatan.merk', 'item_perawatan.no_identifikasi', 'lokasi.lokasi')
                ->where('hrga5_3permintaan.id', $id)->first(),
            'detail_perbaikan' => DB::table('detail_perbaikan')->where('id_permintaan', $id)->first()
        ];
        return view('hccp.hrga5_pemeliharaan.hrga3.print', $data);
    }
}
