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
                ->leftJoin('uang_makan as d', 'a.id_uang_makan', 'd.id_uang_makan')
                ->where(function ($query) {
                    $query->where('b.posisi_id', '!=', 1)
                        ->orWhereNull('a.id_pengawas');
                })
                ->where('b.id', auth()->user()->id)
                ->orderBy('a.id_anak', 'DESC')
                ->get(),
            'pengawas' => User::with('posisi')->whereIn('posisi_id', [13, 14])->get(),
            'uang_makan' => DB::table('uang_makan')->where('aktiv', 'Y')->get()

        ];
        return view('data_master.pengawas.anak', $data);
    }

    public function create_anak(Request $r)
    {
        DB::table('tb_anak')->insert([
            'tgl_masuk' => $r->tgl_masuk,
            'nama' => $r->nama,
            'pembawa' => $r->pembawa,
            'periode' => $r->periode,
            'komisi' => $r->komisi,
            'tgl_dibayar' => $r->tgl_dibayar,
            'id_kelas' => $r->kelas,
            'id_pengawas' => $r->id_pengawas,
        ]);

        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function submit_ceklis(Request $r)
    {
        $submit = $r->submit;
        $id_anak = explode(',', $r->id_anak);
        
        if ($submit == 'berhenti') {
            DB::table('tb_anak')->whereIn('id_anak', $id_anak)->update(['berhenti' => 'Y']);
            return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
        }

        if ($submit == 'bayar') {
            $data = [
                'title' => 'Print Invoice',
                'anak' => DB::table('tb_anak')->whereIn('id_anak', $id_anak)->get(),
            ];

            return view('data_master.pengawas.create_invoice', $data);
        }
    }

    public function anak_detail($id)
    {
        $detail = DB::table('tb_anak')->where('id_anak', $id)->first();
        if (empty($detail)) {
            abort(404);
        }
        $data = [
            'detail' => $detail,
            'pengawas' => User::with('posisi')->whereIn('posisi_id', [13, 14])->get(),
            'uang_makan' => DB::table('uang_makan')->where('aktiv', 'Y')->get()

        ];
        return view("data_master.pengawas.anak_detail", $data);
    }

    public function update_anak(Request $r)
    {
        DB::table('tb_anak')->where('id_anak', $r->id)->update([
            'tgl_masuk' => $r->tgl_masuk,
            'nama' => $r->nama,
            'id_kelas' => $r->kelas,
            'id_pengawas' => $r->id_pengawas,
            'id_uang_makan' => $r->id_uang_makan
        ]);

        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
    }
    public function destroy_anak($id)
    {
        DB::table('tb_anak')->where('id_anak', $id)->delete();
        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil dihapus');
    }
}
