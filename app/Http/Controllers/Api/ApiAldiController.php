<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiGudangGradingModel;
use Illuminate\Http\Request;

class ApiAldiController extends Controller
{
    public function gudang_grading()
    {
        $cetak = ApiGudangGradingModel::dataCetak();
        $cabut_selesai = ApiGudangGradingModel::dataCetak();
        $suntikan = ApiGudangGradingModel::suntikan();
        $grading_selesai = ApiGudangGradingModel::grade_selesai();
        $gudangBj = ApiGudangGradingModel::gudangBj();

        $data = [
            'cetak' => $cetak,
            'cabut_selesai' => $cabut_selesai,
            'suntikan' => $suntikan,
            'grading_selesai' => $grading_selesai,
            'gudangBj' => $gudangBj,
        ];
        return response()->json($data);
    }
}
