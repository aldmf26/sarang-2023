<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Hrga8_CeklistSuhuRuanganController extends Controller
{
    public function index()
    {
        $datas = DB::select("SELECT month(tgl) as bulan,year(tgl) as tahun,standar_suhu,ruangan FROM hrga8_ceklist_suhu_ruangan as a
        group BY month(tgl),standar_suhu,ruangan");
        
        $data = [
            'title' => 'Ceklist Suhu Ruangan',
            'datas' => $datas
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga4.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Ceklist Suhu Ruangan'
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga4.create', $data);
    }
    public function print(Request $r)
    {
        $title = 'CEKLIST SUHU RUANGAN';
        $dok = 'FRM.HRGA.08.04, Rev.00';
        $ruangan = $r->ruangan;
        $standard = $r->standardSuhu;
        $tahun = $r->tahun;
        $bulan = $r->bulan;
        $daysInMonth = Carbon::create(2023, $bulan)->daysInMonth;
        $nm_bulan = DB::table('bulan')->where('id_bulan', $r->bulan)->first()->nm_bulan;

        return view(
            'hccp.hrga8_perawatan_perbaikan_mesin.hrga4.print',
            compact(
                'title',
                'ruangan',
                'standard',
                'dok',
                'daysInMonth',
                'tahun',
                'bulan',
                'nm_bulan'
            )
        );
    }
}
