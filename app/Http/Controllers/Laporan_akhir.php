<?php

namespace App\Http\Controllers;

use App\Models\CetakModel;
use App\Models\LaporanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Laporan_akhir extends Controller
{
    public function index(Request $r)
    {
        $data = [
            'title' => 'Laporan Partai',
            'partai' => LaporanModel::LaporanPerPartai(),
        ];
        return view('home.laporan.lapPerpartai', $data);
    }

    public function get_bk_akhir(Request $r)
    {
        $data = [
            'partai' => $r->partai,
            'bk_akhir' => DB::table('bk_akhir')->where('nm_partai', $r->partai)->first()
        ];
        return view('home.laporan.bk_akhir', $data);
    }

    public function save_bk_akhir(Request $r)
    {
        DB::table('bk_akhir')->where('nm_partai', $r->partai)->delete();
        $data = [
            'nm_partai' => $r->partai,
            'pcs' => $r->pcs_akhir,
            'gr' => $r->gr_akhir,
        ];
        DB::table('bk_akhir')->insert($data);
        return redirect()->back()->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function get_detail_cetak(Request $r)
    {
        $data = [
            'title' => 'Detail Cetak',
            'detail' => LaporanModel::LaporanDetailCetak($r->partai)
        ];
        return view('home.laporan.detail_cetak', $data);
    }
    public function get_detail_cabut(Request $r)
    {
        $data = [
            'title' => 'Detail Cabut',
            'detail' => LaporanModel::LaporanDetailCabut($r->partai)
        ];
        return view('home.laporan.detail_cabut', $data);
    }
    public function get_detail_sortir(Request $r)
    {
        $data = [
            'title' => 'Detail Cabut',
            'detail' => LaporanModel::LaporanDetailSortir($r->partai)
        ];
        return view('home.laporan.detail_sortir', $data);
    }

    public function summaryCetak(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $id_pengawas = '0';
        $summary = CetakModel::summary_cetak($bulan, $tahun);
        $data = [
            'title' => 'Summary Cetak',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'summary' => $summary,
        ];
        return view('home.laporan.summary', $data);
    }
}
