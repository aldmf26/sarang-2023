<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga6_4CeklisFootBathController extends Controller
{
    public function index(Request $r)
    {
        $datas = DB::select("SELECT month(a.tgl) as bulan,year(a.tgl) as tahun,b.lokasi,b.id as id_lokasi FROM foothbath_ceklis as a
        left join lokasi as b on b.id = a.id_lokasi group BY month(a.tgl),b.lokasi");

        $data = [
            'title' => 'Ceklis Foothbath',
            'datas' => $datas,
        ];
        return view('hccp.hrga6_sanitasi.hrga4.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Ceklis Foothbath'
        ];
        return view('hccp.hrga6_sanitasi.hrga4.create', $data);
    }

    public function print(Request $r)
    {
        $datas = DB::table('foothbath_ceklis as a')
            ->leftJoin('foothbath_template as b', 'b.id', '=', 'a.id_frekuensi')
            ->where('a.id_lokasi', $r->id_lokasi)
            ->whereMonth('a.tgl', $r->buloan)
            ->groupBy('a.id_frekuensi')
            ->selectRaw('a.id_frekuensi, b.frekuensi,b.item,a.tgl,a.paraf_petugas,a.verifikator, count(a.tgl) as ttl')
            ->get();

        $foothbathTemplate = DB::table('foothbath_template as a')
            ->selectRaw('a.id, a.item, a.frekuensi, 
                (SELECT COUNT(*) FROM foothbath_ceklis as b 
                    WHERE b.id_frekuensi = a.id AND b.status = 1) as ttl_status_1,
                (SELECT COUNT(*) FROM foothbath_ceklis as b 
                    WHERE b.id_frekuensi = a.id AND b.status = 2) as ttl_status_2')
            ->get();
        $adminSanitasi = DB::table('admin_sanitasi as a')
            ->join('users as b', 'b.id', '=', 'a.id')
            ->selectRaw('a.id, b.name, a.posisi')
            ->get()
            ->groupBy('posisi');


        $daysInMonth = Carbon::create(2023, $r->bulan)->daysInMonth;
        $area = DB::table('lokasi')->where('id', $r->id_lokasi)->first()->lokasi;
        $nm_bulan = DB::table('bulan')->where('id_bulan', $r->bulan)->first()->nm_bulan;
        $data = [
            'title' => 'Footh Bath',
            'dok' => 'FRM.HRGA.06.04, Rev.01',
            'itemSanitasi' => $datas,
            'daysInMonth' => $daysInMonth,
            'id_lokasi' => $r->id_lokasi,
            'bulan' => $r->bulan,
            'area' => $area,
            'nm_bulan' => $nm_bulan,
            'foothbathTemplate' => $foothbathTemplate,
            'adminSanitasi' => $adminSanitasi,
            'tahun' => $r->tahun
        ];
        return view('hccp.hrga6_sanitasi.hrga4.print', $data);
    }
}
