<?php

namespace App\Http\Controllers;

use App\Models\ApiBkModel;
use Illuminate\Http\Request;

class ApiBkController extends Controller
{
    public function sarang(Request $r)
    {
        $cabut = ApiBkModel::datacabut($r->no_lot);
        $response = [
            'status' => 'success',
            'message' => 'Data Sarang berhasil diambil',
            'data' => [
                'cabut' => $cabut,
            ],

        ];
        return response()->json($response);
    }
}
