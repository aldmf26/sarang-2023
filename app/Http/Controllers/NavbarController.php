<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NavbarController extends Controller
{
    public function data_master()
    {
        $data = [
            [
                'judul' => 'Data User',
                'route' => 'user.index',
                'img' => 'team.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'Data Pengawas',
                'route' => 'user.index',
                'img' => 'pengawascabut.png',
                'deskripsi' => 'ini adalah data user',
            ],
        ];
        $title = 'Data Master';
        return view('navbar.data_master', compact(['data', 'title']));
    }

    public function home()
    {
        $data = [
            [
                'judul' => 'BK',
                'route' => 'bk.index',
                'img' => 'warehouse.png',
                'deskripsi' => 'Divisi BK',
            ],
            [
                'judul' => 'CABUT',
                'route' => 'user.index',
                'img' => 'team.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'CETAK',
                'route' => 'user.index',
                'img' => 'team.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'SORTIR',
                'route' => 'user.index',
                'img' => 'team.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'GRADE',
                'route' => 'user.index',
                'img' => 'team.png',
                'deskripsi' => 'ini adalah data user',
            ],
        ];
        $title = 'Data Master';
        return view('navbar.data_master', compact(['data', 'title']));
    }

    public function testing(Request $r)
    {
        return view('testing');
    }
}
