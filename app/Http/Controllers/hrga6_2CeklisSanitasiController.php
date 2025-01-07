<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class hrga6_2CeklisSanitasiController extends Controller
{
    public function index(Request $r)
    {
        $datas = DB::select("SELECT month(a.tgl) as bulan,year(a.tgl) as tahun,b.lokasi,b.id as id_lokasi FROM sanitasi as a
        left join lokasi as b on b.id = a.id_lokasi group BY month(a.tgl),b.lokasi");
        $data = [
            'title' => 'Ceklis Sanitasi',
            'datas' => $datas
        ];
        return view('hccp.hrga6_sanitasi.hrga2.index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Ceklis Sanitasi'
        ];
        return view('hccp.hrga6_sanitasi.hrga2.add', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Ceklis Sanitasi'
        ];
        return view('hccp.hrga6_sanitasi.hrga2.create', $data);
    }

    public function print(Request $r)
    {
        $datas = DB::table('sanitasi as a')
            ->leftJoin('item_pembersihan as b', 'b.id_item', '=', 'a.id_item')
            ->where('a.id_lokasi', $r->id_lokasi)
            ->whereMonth('a.tgl', $r->bulan)
            ->groupBy('a.id_item')
            ->selectRaw('a.id_item, b.nama_item,a.tgl,a.paraf_petugas,a.verifikator')
            ->get();

        $daysInMonth = Carbon::create(2023, $r->bulan)->daysInMonth;
        $area = DB::table('lokasi')->where('id', $r->id_lokasi)->first()->lokasi;
        $nm_bulan = DB::table('bulan')->where('id_bulan', $r->bulan)->first()->nm_bulan;

        $data = [
            'title' => 'CEKLIST SANITASI',
            'dok' => 'FRM.HRGA.06.02, Rev.00',
            'itemSanitasi' => $datas,
            'daysInMonth' => $daysInMonth,
            'id_lokasi' => $r->id_lokasi,
            'bulan' => $r->bulan,
            'area' => $area,
            'nm_bulan' => $nm_bulan,
            
        ];
        return view('hccp.hrga6_sanitasi.hrga2.print', $data);
    }
}
