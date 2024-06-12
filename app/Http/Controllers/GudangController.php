<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use App\Models\CetakModel;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $id_user = auth()->user()->id;
        $gudang = Cabut::gudang($bulan, $tahun, $id_user);
        $data = [
            'title' => 'Data Gudang Awal',
            'bk' => $gudang->bk,
            'cabut' => $gudang->cabut,
            'cabutSelesai' => $gudang->cabutSelesai,
            'cabut_selesai' => CetakModel::cabut_selesai(0),
            'cetak_proses' => CetakModel::cetak_proses(0),
            'cetak_selesai' => CetakModel::cetak_selesai(0),
        ];
        return view('home.gudang.index', $data);
    }
}
