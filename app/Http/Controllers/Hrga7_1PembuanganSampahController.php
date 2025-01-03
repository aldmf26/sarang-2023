<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Hrga7_1PembuanganSampahController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Pembuangan Sampah',
        ];
        return view('hccp.hrga7_pengelolaan_limbah.hrga1.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Pembuangan Sampah'
        ];
        return view('hccp.hrga7_pengelolaan_limbah.hrga1.create', $data);
    }
}
