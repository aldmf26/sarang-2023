<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class hrga2_2penilaianController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Harga 2.2 Penilaian',
        ];
        return view('hccp.hrga2_penilaian.hrga2.index', $data);
    }
}
