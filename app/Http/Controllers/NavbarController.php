<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NavbarController extends Controller
{
    public function data_master()
    {
        $data = DB::table('sub_navbar')->where('navbar', 1)->get();
     
        $title = 'Data Master';
        return view('navbar.data_master', compact(['data', 'title']));
    }

    public function home()
    {
        $data = DB::table('sub_navbar')->where('navbar', 2)->get();

        $title = 'Data Master';
        return view('navbar.data_master', compact(['data', 'title']));
    }

    public function testing(Request $r)
    {
        return view('testing');
    }
}
