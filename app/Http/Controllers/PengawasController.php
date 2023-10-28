<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengawasController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Pengawas',
            'user' => User::with('posisi')->where('posisi_id', 13)->get(),
            'posisi' => Posisi::all()
        ];
        return view('data_master.pengawas.index', $data);
    }
    public function anak()
    {
        $data = [
            'title' => 'Data Anak',
            'user' => DB::table('tb_anak as a')
                ->leftJoin('users as b', 'a.id_pengawas', 'b.id')
                ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
                ->where(function ($query) {
                    $query->where('b.posisi_id', 13)
                        ->orWhereNull('a.id_pengawas');
                })
                ->orderBy('a.id_anak', 'DESC')
                ->get(),
            'pengawas' => User::with('posisi')->where('posisi_id', 13)->get(),

        ];
        return view('data_master.pengawas.anak', $data);
    }

    public function create_anak(Request $r)
    {

        DB::table('tb_anak')->insert([
            'tgl_masuk' => $r->tgl_masuk,
            'nama' => $r->nama,
            'id_kelas' => $r->kelas,
            'id_pengawas' => $r->id_pengawas,
        ]);

        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function anak_detail($id)
    {
        $detail = DB::table('tb_anak')->where('id_anak', $id)->first();
        if (empty($detail)) {
            abort(404);
        }
        $data = [
            'detail' => $detail,
            'pengawas' => User::with('posisi')->where('posisi_id', 13)->get(),
        ];
        return view("data_master.pengawas.anak_detail", $data);
    }
    public function update_anak(Request $r)
    {
        DB::table('tb_anak')->where('id_anak', $r->id)->update([
            'tgl_masuk' => $r->tgl_masuk,
            'nama' => $r->nama,
            'kelas' => $r->kelas,
            'id_pengawas' => $r->id_pengawas,
        ]);

        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
    }
    public function destroy_anak($id)
    {
        DB::table('tb_anak')->where('id_anak', $id)->delete();
        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil dihapus');
    }
}
