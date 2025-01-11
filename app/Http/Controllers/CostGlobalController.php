<?php

namespace App\Http\Controllers;

use App\Models\CostGlobalModel;
use Illuminate\Http\Request;

class CostGlobalController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Cost Global',
            'global' => CostGlobalModel::costGlobal()
        ];
        return view('home.costglobal.index', $data);
    }
}
