<?php

namespace App\Http\Controllers;

use App\Models\BalanceModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceController extends Controller
{

    public function index(Request $r)
    {
        $bulan = $r->bulan;
        $tahun = $r->tahun;

        $cabut = BalanceModel::cabut($bulan, $tahun);
        $cetak = BalanceModel::cetak($bulan, $tahun);
        $sortir = BalanceModel::sortir($bulan, $tahun);
        
        $dataBulan = DB::table('oprasional')->groupBy('bulan')->selectRaw('bulan, tahun')->get();
        $data = [
            'title' => 'Detail Gaji Balancesheet',
            'dataBulan' => $dataBulan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'cabut' => $cabut,
            'cetak' => $cetak,
            'sortir' => $sortir,
        ];
        return view('home.cocokan.balance.index',$data);
    }
    public function gaji()
    {
        return 1;
    }
}
