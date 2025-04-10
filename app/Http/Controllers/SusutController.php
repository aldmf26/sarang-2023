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

        $title = 'Cek Summary Susut';
        
        return view('home.susut.index', compact(
            'title',
            'bulan',
            'tahun',
            'cabutKeCetak',
            'cetakKeSortir',
            'sortirKeGrading'
        ));
    }
}
