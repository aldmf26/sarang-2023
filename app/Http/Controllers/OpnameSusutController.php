<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use App\Models\CabutOpnameModel;
use App\Models\OpnameNewModel;
use Illuminate\Http\Request;

class OpnameSusutController extends Controller
{
    public function index(Request $request)
    {
        $pgws_cabut = CabutOpnameModel::cabut_susut();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.index', $data);
    }


    public function detail_cabut(Request $r)
    {
        $data = [
            'title' => 'Data Opname',
            'tipe' => $r->tipe,
            'box_stock' => CabutOpnameModel::cabut_susut_detail($r->id_pengawas, $r->tipe),
        ];
        return view('home.opnamesusut.detail_cabut', $data);
    }
}
