<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        // $datas = [
        //     [
        //         'nobox' => '1098',
        //         'pcs_awal_bk' => '128',
        //         'gr_awal_bk' => '1000',
        //         'bulan' => 'JULI 2023',
        //         'pengawas' => 'JENAH',
        //         'pcs_awal_kerja' => '128',
        //         'gr_awal_kerja' => '1000',
        //         'rupiah' => '1137536',
        //     ],
        //     [
        //         'nobox' => '1106',
        //         'pcs_awal_bk' => '135',
        //         'gr_awal_bk' => '1000',
        //         'bulan' => 'JULI 2023',
        //         'pengawas' => 'YULI',
        //         'pcs_awal_kerja' => '135',
        //         'gr_awal_kerja' => '1000',
        //         'rupiah' => '1126114',
        //     ],
        //     [
        //         'nobox' => '1126',
        //         'pcs_awal_bk' => '117',
        //         'gr_awal_bk' => '895',
        //         'bulan' => 'JULI 2023',
        //         'pengawas' => 'FATIMAH',
        //         'pcs_awal_kerja' => '117',
        //         'gr_awal_kerja' => '895',
        //         'rupiah' => '1009217',
        //     ],
        // ];
        $datas = DB::select("SELECT * FROM bk as a 
        left join ket_bk as b on b.id_ket_bk = a.id_ket 
        left join warna as c on c.id_warna = a.id_warna;");

        $data = [
            'title' => 'Dashboard',
            'tgl1' => '2023-08-08',
            'tgl2' => '2023-09-09',
            'datas' => $datas,
        ];
        return view('dashboard.dashboard', $data);
    }

    public function detail($nobox = null)
    {
        if (empty($nobox)) {
            return redirect()->back();
        }
        // $detailNobox = [
        //     'no_lot' => 'alur',
        //     'no_box' => '3001',
        //     'tipe' => 'D',
        //     'ket' => 'KL',
        //     'warna' => 'S',
        // ];
        $detailNobox = DB::selectOne("SELECT * FROM bk as a
        left join ket_bk as b on b.id_ket_bk = a.id_ket
        left join warna as c on c.id_warna = a.id_warna
        where a.no_box = $nobox ");

        $data = [
            'title' => 'Detail Gaji Box',
            'detail' => $detailNobox,
            'cabut' => DB::select("SELECT * FROM cabut as a 
            left join tb_anak as b on b.id_anak = a.id_anak 
            left join users as c on c.id = a.id_pengawas
            where a.no_box = $nobox"),
            'cetak' => DB::select("SELECT * FROM cetak as a 
            left join tb_anak as b on b.id_anak = a.id_anak 
            left join users as c on c.id = a.id_pengawas
            where a.no_box = $nobox")
        ];
        return view('dashboard.detail', $data);
    }
}
