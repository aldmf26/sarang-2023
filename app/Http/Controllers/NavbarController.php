<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NavbarController extends Controller
{
    public function queryNavbar($jenis)
    {
        $id_user = auth()->user()->id;

        $data = DB::table('sub_navbar as a')
            ->join('permission_navbar as b', 'a.id_sub_navbar', 'b.id_sub_navbar')
            ->where([
                ['a.navbar', $jenis],
                ['b.id_user', $id_user],
            ])
            ->orderBy('a.urutan', 'ASC')
            ->get();
        return $data;
    }
    public function data_master()
    {

        $data = $this->queryNavbar(1);

        $title = 'Data Master';
        return view('navbar.data_master', compact(['data', 'title']));
    }

    public function home()
    {
        $data = $this->queryNavbar(2);

        $title = 'Data Master';
        return view('navbar.data_master', compact(['data', 'title']));
    }
    public function summary()
    {
        $data = $this->queryNavbar(3);

        $title = 'Data Summary';
        return view('navbar.data_master', compact(['data', 'title']));
    }


    public function testing(Request $r)
    {
        return view('testing');
    }
}
