<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiapKirimController extends Controller
{
   public function index(Request $r)
   {
    $data = [
        'title' => 'Siap Kirim'
    ];
    return view('home.siapkirim.index',$data);
   }
}
