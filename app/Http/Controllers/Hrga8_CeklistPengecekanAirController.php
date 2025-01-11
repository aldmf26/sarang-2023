<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Hrga8_CeklistPengecekanAirController extends Controller
{
    public function index()
    {
        $datas = DB::select("SELECT month(a.tgl) as bulan,year(a.tgl) as tahun,a.jenis_mesin FROM hrga8_ceklist_pengecekan_air as a
        group BY month(a.tgl),a.jenis_mesin");
        
        $data = [
            'title' => 'Ceklist Pengecekan Air',
            'datas' => $datas
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga7.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Ceklist Pengecekan Air'
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga7.create', $data);
    }

    public function print(Request $r)
    {
        $limbah = DB::table('hrga8_ceklist_pengecekan_air')->orderBy('id', 'desc')->get();
        $title = 'CEKLIST  WATER TREATMENT';
        $dok = 'FRM.HRGA.08.07, Rev.00';
        $jenis_mesin = $r->jenis_mesin;
        $tahun = $r->tahun;
        $bulan = $r->bulan;
        $daysInMonth = Carbon::create(2023, $bulan)->daysInMonth;
        $nm_bulan = DB::table('bulan')->where('id_bulan', $r->bulan)->first()->nm_bulan;

        return view(
            'hccp.hrga8_perawatan_perbaikan_mesin.hrga7.print',
            compact(
                'limbah',
                'title',
                'dok',
                'daysInMonth',
                'jenis_mesin',
                'tahun',
                'bulan',
                'nm_bulan'
            )
        );
    }
}
