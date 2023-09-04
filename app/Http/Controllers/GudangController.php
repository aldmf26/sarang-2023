<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Gudang Awal',
        ];
        return view('home.gudang.index', $data);
    }
}
