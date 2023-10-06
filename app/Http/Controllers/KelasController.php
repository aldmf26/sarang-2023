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
            'datas' => DB::table($rotRemove == 'index' ? 'tb_kelas' : "tb_kelas_$rotRemove")->orderBy('kategori', 'ASC')->get()
        ];
        return view("data_master.kelas.index", $data);
    }

    public function cabutCreate(Request $r)
    {
        $buang = [
            'rupiah', 'pcs', 'gr', 'rp_bonus', 'bonus_susut', 'batas_susut', 'eot', 'denda_hcr',
            'rupiah_tambah', 'pcs_tambah', 'gr_tambah', 'rp_bonus_tambah', 'bonus_susut_tambah', 'batas_susut_tambah', 'eot_tambah', 'denda_hcr_tambah'
        ];
        foreach ($buang as $d) {
            $r->$d = str()->remove(',', $r->$d);
        }

        if (!empty($r->rupiah_tambah[0])) {
            for ($i = 0; $i < count($r->rupiah_tambah); $i++) {
                DB::table('tb_kelas')->insert([
                    'kelas' => $r->kelas_tambah[$i],
                    'tipe' => $r->tipe_tambah[$i],
                    'gr' => $r->gr_tambah[$i],
                    'pcs' => $r->pcs_tambah[$i],
                    'rupiah' => $r->rupiah_tambah[$i],
                    'rp_bonus' => $r->rp_bonus_tambah[$i],
                    'batas_susut' => $r->batas_susut_tambah[$i],
                    'bonus_susut' => $r->bonus_susut_tambah[$i],
                    'eot' => $r->eot_tambah[$i],
                    'denda_hcr' => $r->denda_hcr_tambah[$i],
                    'ket' => $r->ket_tambah[$i],
                    'kategori' => $r->kategori_tambah[$i],
                ]);
            }
        }

        if (!empty($r->rupiah[0])) {
            for ($i = 0; $i < count($r->rupiah); $i++) {
                DB::table('tb_kelas')->where('id_kelas', $r->id_kelas[$i])->update([
                    'kelas' => $r->kelas[$i],
                    'tipe' => $r->tipe[$i],
                    'pcs' => $r->pcs[$i],
                    'gr' => $r->gr[$i],
                    'rupiah' => $r->rupiah[$i],
                    'rp_bonus' => $r->rp_bonus[$i],
                    'ket' => $r->ket[$i],
                    'kategori' => $r->kategori[$i],
                    'batas_susut' => $r->batas_susut[$i],
                    'bonus_susut' => $r->bonus_susut[$i],
                    'eot' => $r->eot[$i],
                    'denda_hcr' => $r->denda_hcr[$i],
                ]);
            }
        }
        return redirect()->route('kelas.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function info($id_kelas)
    {
        $detail = DB::table('tb_kelas')->where('id_kelas', $id_kelas)->first();
        $view = [
            1 => 'cabut',
            2 => 'spesial',
            3 => 'eo',
        ];
        $data = [
            'detail' => $detail,
            'jenis' => $view[$detail->kategori]
        ];

        return view('data_master.kelas.info_' . $data['jenis'], $data);
    }
    public function deleteCabut(Request $r)
    {
        foreach($r->datas as $d){
            DB::table('tb_kelas')->where('id_kelas', $d)->delete();
        }
        return redirect()->route('kelas.index')->with('sukses', 'Data Berhasil dihapus');
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
