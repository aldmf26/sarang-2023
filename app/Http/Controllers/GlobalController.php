<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $data = [
            'title' => 'Gaji Global',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
        ];
        return view('home.global.cabut',$data);
    }
}
