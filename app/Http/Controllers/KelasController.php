<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function index()
    {
        $rot = request()->route()->getName();
        $rotRemove = str()->remove('kelas.', $rot);
        $data = [
            'title' => 'Data Paket',
            'route' => $rot,
            'routeRemove' => $rotRemove,
            'lokasi' => ['alpa', 'mtd', 'sby'],
            'datas' => DB::table($rotRemove == 'index' ? 'tb_kelas' : "tb_kelas_$rotRemove")->orderBy('kelas', 'ASC')->get()
        ];
        return view("data_master.kelas.index", $data);
    }

    public function create(Request $r)
    {
        $data = $r->all();
        unset($data['_token']);
        unset($data['routeRemove']);

        DB::table($r->routeRemove == 'index' ? 'tb_kelas' : "tb_kelas_$r->routeRemove")->insert($data);
        return redirect()->route("kelas.$r->routeRemove")->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function update(Request $r)
    {
        $data = $r->all();
        
        unset($data['_token']);
        unset($data['routeRemove']);

        DB::table($r->routeRemove == 'index' ? 'tb_kelas' : "tb_kelas_$r->routeRemove")->where('id_kelas', $r->id_kelas)->update($data);
        return redirect()->route("kelas.$r->routeRemove")->with('sukses', 'Data Berhasil diubah');
    }

    public function delete(Request $r)
    {
        $parts = explode("_", $r->urutan);
        $id = $parts[0];
        $route = $parts[1];

        DB::table($route == 'index' ? 'tb_kelas' : "tb_kelas_$route")
        ->where('id_kelas', $id)
        ->delete();
        return redirect()->route("kelas.$route")->with('sukses', 'Data Berhasil dihapus');
    }
}
