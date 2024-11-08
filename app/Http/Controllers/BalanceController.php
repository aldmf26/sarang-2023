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
        $operasional = DB::table('oprasional')->where('bulan', $bulan)->where('tahun', $tahun)->first();
        $grading = BalanceModel::gradingOne($bulan, $tahun);


        $data = [
            'title' => 'Cost Gaji Cabut Cetak Sortir',
            'dataBulan' => $dataBulan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'cabut' => $cabut,
            'cetak' => $cetak,
            'sortir' => $sortir,
            'operasional' => $operasional,
            'grading' => $grading
        ];
        return view('home.cocokan.balance.index', $data);
    }
    public function cost(Request $r)
    {
        $bulan = $r->bulan;
        $tahun = $r->tahun;
        $dataBulan = DB::table('oprasional')->groupBy('bulan')->selectRaw('bulan, tahun')->get();
        $grading = BalanceModel::grading($bulan, $tahun);
        $data = [
            'title' => 'Cost Operasional Beban Digrading',
            'dataBulan' => $dataBulan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'grading' => $grading,
        ];
        return view('home.cocokan.balance.cost', $data);
    }
}
