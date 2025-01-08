<?php

namespace App\Http\Controllers;

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
            'title' => 'Tambah Pembuangan Tps'
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.hrga7.create', $data);
    }
}
