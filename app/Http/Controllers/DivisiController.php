<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;

class DivisiController extends Controller
{
    public function index($divisi)
    {
        if (in_array(auth()->user()->posisi_id, [1, 12])) {
            $divisis = Divisi::orderBy('urutan')->get();
            $title = [
                'hrga1' => 'HRGA.01.01 - Permohonan Karyawan Baru',
                'hrga2' => 'HRGA.01.02 - Hasil Wawancara',
                'hrga3' => 'HRGA.01.03 - Hasil Evaluasi Karyawan',
                'hrga4' => 'HRGA.01.04 - Data Pegawai'
            ];
            $data = [
                'title' => $title[$divisi],
                'divisis' => $divisis,
                'divisi' => $divisi,
            ];
            return view('hccp.divisi', $data);
        } else {
            return redirect()->route("$divisi.index");
        }
    }
}
