<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BkController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Divisi BK'
        ];
        return view('home.bk.index',$data);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Divisi BK'
        ];
        return view('home.bk.create',$data);
    }
}
