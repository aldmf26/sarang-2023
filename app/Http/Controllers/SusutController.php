<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SusutController extends Controller
{
    public function index()
    {
        $bulan = $r->bulan ?? date('m');
        $tahun = $r->tahun ?? date('Y');

        $data = [
            'title' => 'Cek Summary Susut',
            'bulan' => $bulan,
            'tahun' => $tahun,
        ];
        return view('home.susut.index',$data);
    }
}
