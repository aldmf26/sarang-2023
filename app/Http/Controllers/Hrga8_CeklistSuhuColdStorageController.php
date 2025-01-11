<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Hrga8_CeklistSuhuColdStorageController extends Controller
{
    public function index()
    {
        $datas = DB::select("SELECT month(tgl) as bulan,year(tgl) as tahun,standar_suhu,ruangan FROM hrga8_ceklist_suhu_cold_storage as a
        group BY month(tgl),standar_suhu,ruangan");
        
        $data = [
            'title' => 'Ceklist Suhu Cold Storage',
            'datas' => $datas
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga6.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Ceklist Suhu Cold Storage'
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga6.create', $data);
    }
    public function print(Request $r)
    {
        $title = 'CEKLIST SUHU COLD STORAGE';
        $dok = 'FRM.HRGA.08.06, Rev.00';
        $ruangan = $r->ruangan;
        $standard = $r->standardSuhu;
        $tahun = $r->tahun;
        $bulan = $r->bulan;
        $daysInMonth = Carbon::create(2023, $bulan)->daysInMonth;
        $nm_bulan = DB::table('bulan')->where('id_bulan', $r->bulan)->first()->nm_bulan;

        return view(
            'hccp.hrga8_perawatan_perbaikan_mesin.hrga6.print',
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
