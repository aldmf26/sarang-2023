<?php

namespace App\Http\Controllers;

use App\Models\OpnameNewModel;
use Illuminate\Http\Request;

class OpnameSusutController extends Controller
{
    public function index(OpnameNewModel $model)
    {
        $pgws_cabut = OpnameNewModel::cabut_susut2();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.index', $data);
    }
}
