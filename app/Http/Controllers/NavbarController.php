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
                'route' => 'pengawas.index',
                'img' => 'pengawascabut.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'Data Anak',
                'route' => 'pengawas.anak',
                'img' => 'anak.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'Data Denda',
                'route' => 'denda.index',
                'img' => 'denda.png',
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
                'judul' => 'Gudang Awal',
                'route' => 'gudang.index',
                'img' => 'warehouse.png',
                'deskripsi' => 'Gudang Herry',
            ],
            [
                'judul' => 'BK',
                'route' => 'bk.index',
                'img' => 'warehouse.png',
                'deskripsi' => 'Divisi BK',
            ],
            [
                'judul' => 'CABUT',
                'route' => 'cabut.index',
                'img' => 'warehouse.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'CABUT SPESIAL',
                'route' => 'cabutSpesial.index',
                'img' => 'warehouse.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'EO',
                'route' => 'eo.index',
                'img' => 'warehouse.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'CETAK',
                'route' => 'cetak.index',
                'img' => 'warehouse.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'SORTIR',
                'route' => 'sortir.index',
                'img' => 'warehouse.png',
                'deskripsi' => 'ini adalah data user',
            ],
            [
                'judul' => 'GRADE',
                'route' => 'grading.index',
                'img' => 'warehouse.png',
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
