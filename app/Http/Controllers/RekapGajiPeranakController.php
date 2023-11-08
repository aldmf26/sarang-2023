<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapGajiPeranakController extends Controller
{
    public function index(Request $r)
    {
        $bulan = date('m');
        $tahun = date('Y');
        $pengawas = Cabut::getPengawasRekap($bulan, $tahun);
        $kategori = $r->kategori ?? 'cabut';
        $data = [
            'title' => 'Rekap Gaji Peranak',
            'kategori' => $kategori,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'pengawas' => $pengawas
        ];
        return view('home.rekap.rekap', $data);
    }

}
