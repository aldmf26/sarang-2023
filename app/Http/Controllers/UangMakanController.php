<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UangMakanController extends Controller
{
    public function index(Request $r)
    {
        $data = [
            'title' => 'Daftar Uang Makan',
            'uang_makan' => DB::table('uang_makan')->orderBy('id_uang_makan', 'DESC')->get(),

        ];
        return view('data_master.uang_makan.index', $data);
    }

    public function tambah_uang_makan(Request $r)
    {

        $data = [
            'nominal' => $r->nominal,
            'aktiv' => 'Y'
        ];

        DB::table('uang_makan')->insert($data);
        return redirect()->route('uang_makan.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function uang_makan_detail($id)
    {
        $detail = DB::table('uang_makan')->where('id_uang_makan', $id)->first();
        if (empty($detail)) {
            abort(404);
        }
        $data = [
            'detail' => $detail,

        ];
        return view("data_master.uang_makan.uang_makan_detail", $data);
    }

    public function update(Request $r)
    {

        $data = [
            'nominal' => $r->nominal,
            'aktiv' => $r->aktiv
        ];
        DB::table('uang_makan')->where('id_uang_makan', $r->id_uang_makan)->update($data);
        return redirect()->route('uang_makan.index')->with('sukses', 'Data Berhasil ditambahkan');
    }
}
