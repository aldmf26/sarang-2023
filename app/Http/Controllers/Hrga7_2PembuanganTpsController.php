<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Hrga7_2PembuanganTpsController extends Controller
{
    public function index()
    {
        $datas = DB::select("SELECT month(a.tgl) as bulan,year(a.tgl) as tahun,a.jenis_sampah FROM hrga7_pembuangan_tps as a
        group BY month(a.tgl),a.jenis_sampah");

        $data = [
            'title' => 'Pembuangan Tps',
            'datas' => $datas
        ];
        return view('hccp.hrga7_pengelolaan_limbah.hrga2.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Pembuangan Tps'
        ];
        return view('hccp.hrga7_pengelolaan_limbah.hrga2.create', $data);
    }

    public function print(Request $r)
    {
        $jenis_limbah = $r->jenis_limbah;
        $daysInMonth = Carbon::create(2023, $r->bulan)->daysInMonth;
        $jamList = [
            ['time' => '07:00:00', 'label' => 'AM'],
        ];
        $nm_bulan = DB::table('bulan')->where('id_bulan', $r->bulan)->first();
        $data = [
            'title' => 'LAPORAN PENGELUARAN LIMBAH DARI PERUSAHAAN',
            'dok' => 'FRM.HRGA.07.02, Rev.00',
            'nm_bulan' => $nm_bulan->nm_bulan,
            'selectedBulan' => $nm_bulan->bulan,
            'jenis_limbah' => $jenis_limbah,
            'daysInMonth' => $daysInMonth,
            'jamList' => $jamList,
            'tbl' => 'hrga7_pembuangan_sampah',

        ];
        return view('hccp.hrga7_pengelolaan_limbah.hrga2.print', $data);
    }   
}
