<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'title' => 'Tambah Divisi BK',
            'pengawas' => User::where('posisi_id', 13)->get()
        ];
        return view('home.bk.create',$data);
    }
}
