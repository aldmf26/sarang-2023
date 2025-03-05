<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AolApiController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'AOL API'
        ];
        return view('aol.index',$data);
    }
}
