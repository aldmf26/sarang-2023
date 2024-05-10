<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiGudangGradingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiAldiController extends Controller
{
    public function gudang_grading()
    {
        $cetak = ApiGudangGradingModel::dataCetak();
        $cabut_selesai = ApiGudangGradingModel::dataCetak();
        $suntikan = ApiGudangGradingModel::suntikan();
        $grading_selesai = ApiGudangGradingModel::grade_selesai();
        $gudangBj = ApiGudangGradingModel::gudangBj();
        $gudangBj = ApiGudangGradingModel::gudangBj();
        $historyBoxKecil = ApiGudangGradingModel::historyBoxKecil();

        $data = [
            'cetak' => $cetak,
            'cabut_selesai' => $cabut_selesai,
            'suntikan' => $suntikan,
            'grading_selesai' => $grading_selesai,
            'gudangBj' => $gudangBj,
            'historyBoxKecil' => $historyBoxKecil,
        ];
        return response()->json($data);
    }

    public function saveSuntikanGrading(Request $r)
    {
        DB::beginTransaction();
        try {
            // $rules = [
            //     'nm_partai' => 'required|string|max:10'
            // ];
            // $validator = Validator::make($r->all(), $rules);

            // if ($validator->fails()) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => $validator->errors()->first()
            //     ]);
            // }
            $d = $r->input();
            for ($i = 0; $i < count($d['nm_partai']); $i++) {
                DB::table('grading_suntikan')->insert([
                    'nm_partai' => $d['nm_partai'][$i],
                    'tipe' => $d['tipe'][$i],
                    'no_box' => $d['no_box'][$i],
                    'pcs' => $d['pcs'][$i],
                    'gr' => $d['gr'][$i],
                    'ttl_rp' => $d['ttl_rp'][$i],
                    'cost_cabut' => $d['cost_cbt'][$i],
                    'cost_cetak' => $d['cost_ctk'][$i],
                    'tgl' => $d['tgl'][$i],
                    'admin' => $d['admin'][$i],
                ]);
            }

            DB::commit();
            return response()->json(
                [
                    'status' => 'sukses',
                    'message' => 'Berhasil tambah data'
                ]
            );
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $th->getMessage()
                ]
            );
        }
    }

    public function saveSuntikanSelesaiGrading(Request $r)
    {
        DB::beginTransaction();
        try {

            $d = $r->input();
            for ($i = 0; $i < count($d['grade']); $i++) {
                DB::table('pengiriman_list_gradingbj')->insert([
                    'no_grading' => 9999,
                    'tgl_grading' => $d['tgl_grading'][$i],
                    'grade' => $d['grade'][$i],
                    'pcs' => $d['pcs'][$i],
                    'gr' => $d['gr'][$i],
                    'admin' => $d['admin'][$i],
                    'rp_gram' => $d['rp_gram'][$i],
                ]);
            }

            DB::commit();
            return response()->json(
                [
                    'status' => 'sukses',
                    'message' => 'Berhasil tambah data'
                ]
            );
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $th->getMessage()
                ]
            );
        }
    }
}
