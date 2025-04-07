<?php

namespace App\Http\Controllers;

use App\Models\Susut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SusutController extends Controller
{
    public function index()
    {
        $bulan = $r->bulan ?? date('m');
        $tahun = $r->tahun ?? date('Y');

        $cabutKeCetak = Susut::getSum('cabut');
        $cetakKeSortir = Susut::getSum('cetak');
        $sortirKeGrading = Susut::getSum('sortir');

        $data = [
            'title' => 'Cek Summary Susut',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'cabutKeCetak' => $cabutKeCetak,
            'cetakKeSortir' => $cetakKeSortir,
            'sortirKeGrading' => $sortirKeGrading
        ];
        return view('home.susut.index', $data);
    }
}
