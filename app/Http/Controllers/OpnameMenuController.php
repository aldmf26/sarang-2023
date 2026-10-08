<?php

namespace App\Http\Controllers;

class OpnameMenuController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Opname',
        ];

        return view('home.opname_menu.index', $data);
    }
}
