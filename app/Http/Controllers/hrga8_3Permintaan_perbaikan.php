<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga8_3Permintaan_perbaikan extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Permintaan Perbaikan',
            'permintaan' => DB::table('permintaan_perbaikan_mesin')
                ->leftJoin('item_perawatan', 'permintaan_perbaikan_mesin.id_item', '=', 'item_perawatan.id')
                ->leftJoin('lokasi', 'item_perawatan.lokasi_id', '=', 'lokasi.id')
                ->select('permintaan_perbaikan_mesin.*', 'item_perawatan.nama', 'item_perawatan.merk', 'item_perawatan.no_identifikasi', 'lokasi.lokasi')
                ->orderBy('id', 'desc')->get(),
            'lokasi' => DB::table('lokasi')->get(),

        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga3.index', $data);
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
    public function store(Request $r)
    {
        $data = [
            'id_item' => $r->id_item,
            'tgl_mulai' => $r->tgl_mulai,
            'deadline' => $r->deadline,
            'diajukan_oleh' => $r->diajukan_oleh,
            'deskripsi_masalah' => $r->deskripsi_masalah,
            'time' => now(),
            'selesai' => 'T',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        DB::table('permintaan_perbaikan_mesin')->insert($data);
        return redirect()->route('hrga8_3.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function print($id)
    {
        $data = [
            'title' => 'Permintaan Perbaikan Mesin',
            'permintaan' => DB::table('permintaan_perbaikan_mesin')
                ->leftJoin('item_perawatan', 'permintaan_perbaikan_mesin.id_item', '=', 'item_perawatan.id')
                ->leftJoin('lokasi', 'item_perawatan.lokasi_id', '=', 'lokasi.id')
                ->select('permintaan_perbaikan_mesin.*', 'item_perawatan.nama', 'item_perawatan.merk', 'item_perawatan.no_identifikasi', 'lokasi.lokasi')
                ->where('permintaan_perbaikan_mesin.id', $id)->first(),
            'detail_perbaikan' => DB::table('detail_perbaikan_mesin')->where('id_permintaan', $id)->first()
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga3.print', $data);
    }

    public function store2(Request $r)
    {

        $data = [
            'id_permintaan' => $r->id,
            'detail_perbaikan' => $r->detail,
            'verifikasi_user' => $r->verifikasi,
            'time' => now(),
        ];
        DB::table('detail_perbaikan_mesin')->insert($data);

        $data2 = [
            'selesai' => 'Y',
        ];
        DB::table('permintaan_perbaikan_mesin')->where('id', $r->id)->update($data2);
        return redirect()->route('hrga8_3.index')->with('sukses', 'Data Berhasil ditambahkan');
    }
}
