<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Divisi Cabut'
        ];
        return view('home.cabut.index', $data);
    }
}
