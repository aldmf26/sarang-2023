<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HccpController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'HCCP',
        ];
        return view('hccp.hccp.index', $data);
    }

    public function sampleAdministrator()
    {
        $data = [
            'title' => 'Sampel Administrator',
        ];
        return view('hccp.hccp.sample', $data);
    }
}
