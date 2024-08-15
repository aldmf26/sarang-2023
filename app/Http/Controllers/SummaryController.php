<?php

namespace App\Http\Controllers;

use App\Models\gudangcekModel;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function index(Request $r)
    {
        $cabut = [
            'judul' => ['Awal', 1, 'box stock awal bk'],
            ['opname', 2, 'box stock cabut sedang proses'],
            ['opname', 3, 'box selesai cabut siap cetak belum serah'],
            ['proses', 4, 'box selesai cabut siap cetak diserahkan'],
            ['opname', 5, 'box selesai cabut siap sortir belum serah'],
            ['proses', 6, 'box selesai cabut siap sortir belum diserahkan'],
            ['opname', 7, 'box cbt sisa pengawas'],
        ];
        $cetak = [
            'judul' => ['Awal', 8, 'cetak opname'],
            ['awal', 9, 'cetak stock awal'],
            ['opname', 10, 'cetak sedang proses'],
            ['opname', 11, 'cetak selesai siap sortir belum serah'],
            ['proses', 12, 'tidak cetak diserahkan'],
            ['proses', 13, 'cetak selesai siap sortir diserahkan'],
            ['opname', 14, 'cetak sisa pengawas'],
        ];
        $sortir = [
            'judul' => ['Awal', 15, 'sortir opname'],
            ['awal', 9, 'sortir stock awal'],
            ['opname', 10, 'sortir sedang proses'],
            ['opname', 11, 'sortir selesai siap grading belum serah'],
            ['proses', 12, 'sortir selesai siap grading diserahkan'],
            ['opname', 14, 'sortir sisa pengawas'],
        ];
        $kirim = [
            'judul' => ['Awal', 8, 'siap kirim opname'],
            ['awal', 9, 'grading stock awal'],
            ['opname', 10, 'box belum kirim gudang wip'],
            ['opname', 11, 'box selesai pengiriman'],
        ];
        $data = [
            'title' => 'Data Gudang Awal',
            'cabut' => $cabut,
            'cetak' => $cetak,
            'sortir' => $sortir,

            'box_awal' => gudangcekModel::bkstockawal(),
        ];
        return view('home.summary.index', $data);
    }
}
