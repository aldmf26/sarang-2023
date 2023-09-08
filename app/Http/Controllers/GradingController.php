<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradingController extends Controller
{
    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }
    public function index(Request $r)
    {
        $id = auth()->user()->id;

        $data = [
            'title' => 'Divisi Grade',
            'anak' => $this->getAnak(),
            'tipe' => DB::table('tipe_grade')->get(),
            'grade' => DB::select("SELECT * FROM grade as a 
            left join tb_anak as b on b.id_anak = a.id_penerima
             ")
        ];
        return view('home.grade.index', $data);
    }

    public function add_target(Request $r)
    {
        for ($x = 0; $x < count($r->no_box); $x++) {
            $data = [
                'tgl' => $r->tgl[$x],
                'id_pengawas' => auth()->user()->id,
                'id_penerima' => $r->id_anak[$x],
                'pcs_awal' => $r->pcs_awal[$x],
                'gr_awal' => $r->gr_awal[$x],
                'no_box' => $r->no_box[$x]
            ];
            DB::table('grade')->insert($data);
        }
        return redirect()->route('grading.index')->with('sukses', 'Berhasil tambah Data');
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'count' => $r->count,
            'tipe' => DB::table('tipe_grade')->get(),
        ];
        return view('home.grade.tbh_baris', $data);
    }
}
