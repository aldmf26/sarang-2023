<?php

namespace App\Http\Controllers;

use App\Models\ApiBkModel;
use Illuminate\Http\Request;

class ApiBkController extends Controller
{
    public function sarang(Request $r)
    {
        $cabut = ApiBkModel::datacabut($r->no_lot);
        $cetak = ApiBkModel::datacetak($r->no_lot);
        $sortir = ApiBkModel::datasortir($r->no_lot);

        $response = [
            'status' => 'success',
            'message' => 'Data Sarang berhasil diambil',
            'data' => [
                'cabut' => $cabut,
                'cetak' => $cetak,
                'sortir' => $sortir
            ],
        ];
        return response()->json($response);
    }
}
