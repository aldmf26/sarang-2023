<?php

namespace App\Http\Controllers;

use App\Models\hrga3HasilEvaluasiKaryawan as hrga3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga3HasilEvaluasiKaryawanController extends Controller
{
    public function index()
    {
        $model = hrga3::all();
        $data = [
            'title' => 'Harga 3 Hasil Evaluasi Karyawan',
        ];
        return view('hccp.hrga3.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Hasil Evaluasi Karyawan',
            'karyawans' => DB::table('hasil_wawancara')->where('keputusan', 'dilanjutkan')->get(),
        ];
        return view('hccp.hrga3.create', $data);
    }

    public function getKaryawan(Request $r)
    {
        $id = $r->id;
        $karyawan = DB::table('hasil_wawancara')->where('id', $id)->first();
        $data = [
            'usia' => Umur($karyawan->tgl_lahir, $karyawan->created_at),
            'j_kelamin' => $karyawan->j_kelamin,
            'posisi' => $karyawan->posisi
        ];
        return response()->json($data);
    }
}
