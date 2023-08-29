<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        $data = [
            'title' => 'Dashboard',
            'tgl1' => '2023-08-08',
            'tgl2' => '2023-09-09',
        ];
        return view('dashboard.dashboard',$data);
    }
}
