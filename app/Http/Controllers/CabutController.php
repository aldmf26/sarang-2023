<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CabutController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Divisi Cabut'
        ];
        return view('home.cabut.index',$data);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'pengawas' => User::where('posisi_id', 13)->get(),
            'anak' => User::where('posisi_id', 14)->get()
        ];
        return view('home.cabut.create',$data);
    }
}
