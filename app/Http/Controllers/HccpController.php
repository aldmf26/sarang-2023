<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HccpController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'HCCP',
        ];
        return view('hccp.index', $data);
    }

    public function sampleAdministrator()
    {
        $data = [
            'title' => 'Sampel Administrator',
        ];
        return view('hccp.hrga1_penerimaan.index', $data);
    }

    public function evaluasiKompetensiKaryawan()
    {
        $data = [
            'title' => 'Evaluasi Kompetensi Karyawan',
        ];
        return view('hccp.hrga2_evaluasi.index', $data);
    }
}
