<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpnameNewController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Opname',

        ];
        return view('home.opname.index', $data);
    }
}
