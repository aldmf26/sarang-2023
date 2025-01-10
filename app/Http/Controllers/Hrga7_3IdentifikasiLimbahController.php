<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Hrga7_3IdentifikasiLimbahController extends Controller
{
    public function index()
    {
        $datas = DB::select("SELECT month(a.tgl) as bulan,year(a.tgl) as tahun,a.jenis_sampah FROM hrga7_pembuangan_tps as a
        group BY month(a.tgl),a.jenis_sampah");

        $data = [
            'title' => 'Identifikasi limbah',
            'datas' => $datas
        ];
        return view('hccp.hrga7_pengelolaan_limbah.hrga3.index', $data);
    }

    public function print()
    {
        $limbah = DB::table('hrga7_identifikasi_limbah')->orderBy('id', 'desc')->get();
        $title = 'IDENTIFIKASI LIMBAH';
        $dok = 'FRM.HRGA.07.03, Rev.00';
        
        return view(
            'hccp.hrga7_pengelolaan_limbah.hrga3.print',
            compact(
                'limbah',
                'title',
                'dok'
            )
        );
    }
}
