<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakController extends Controller
{
    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }
    public function index()
    {
        $data = [
            'title' => 'Divisi Cetak',
            'cetak' => DB::table('cetak as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where('a.id_pengawas', auth()->user()->id)
                ->get(),
            'anakNoPengawas' => $this->getAnak(1),
            'anak' => $this->getAnak(),
        ];
        return view('home.cetak.index', $data);
    }

    public function add_target(Request $r)
    {
        for ($x = 0; $x < count($r->no_box); $x++) {
            $data = [
                'no_box' => $r->no_box[$x],
                'id_anak' => $r->id_anak[$x],
                'target' => $r->target[$x],
                'rp_pcs' => $r->rp_pcs[$x],
                'pcs_awal' => $r->pcs_awal[$x],
                'gr_awal' => $r->gr_awal[$x],
                'grade' => $r->grade[$x],
                'tgl' => $r->tgl[$x],
                'id_pengawas' => auth()->user()->id
            ];
            DB::table('cetak')->insert($data);
        }
        return redirect()->route('cetak.index')->with('sukses', 'Berhasil tambah Data');
    }

    public function akhir(Request $r)
    {
        $data = [
            'cetak' => DB::table('cetak as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where('a.id_cetak', $r->id_cetak)
                ->first(),
        ];
        return view('home.cetak.akhir', $data);
    }

    public function add_akhir(Request $r)
    {
        $data = [
            'pcs_tidak_ctk' => $r->pcs_tidak_ctk,
            'gr_tidak_ctk' => $r->gr_tidak_ctk,
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
        ];

        DB::table('cetak')->where('id_cetak', $r->id_cetak)->update($data);
        return redirect()->route('cetak.index')->with('sukses', 'Berhasil tambah Data');
    }

    public function selesai(Request $r)
    {
        DB::table('cetak')->where('id_cetak', $r->id_cetak)->update(['selesai' => 'Y']);
        return redirect()->route('cetak.index')->with('sukses', 'Data telah diselesaikan');
    }
}
