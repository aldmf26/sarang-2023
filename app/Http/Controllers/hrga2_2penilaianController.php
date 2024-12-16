<?php

namespace App\Http\Controllers;

use App\Models\HasilWawancaraModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga2_2penilaianController extends Controller
{
    public function index()
    {
        $karyawans = HasilWawancaraModel::with('divisi')->get();
        $data = [
            'title' => 'Hrga 2.2 Penilaian Kompetensi',
            'karyawans' => $karyawans
        ];
        return view('hccp.hrga2_penilaian.hrga2.index', $data);
    }

    public function penilaian($id)
    {
        $model = HasilWawancaraModel::with(['divisi', 'anak'])->where('id', $id)->first();
        $data = [
            'title' => 'Penilaian Kompetensi',
            'karyawan' => $model
        ];
        return view('hccp.hrga2_penilaian.hrga2.penilaian', $data);
    }
}
