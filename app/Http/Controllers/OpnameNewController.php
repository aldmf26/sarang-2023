<?php

namespace App\Http\Controllers;

use App\Models\OpnameNewModel;
use Illuminate\Http\Request;

class OpnameNewController extends Controller
{
    public function index(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => $model::bksisapgws(),
            'box_proses' => $model::bksedang_proses_sum(),
            'box_selesai' => $model::bksedang_selesai_sum(),

        ];
        return view('home.opnamenew.index', $data);
    }
    public function cetak(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => $model::cetak_stok(),
            'box_proses' => $model::cetak_proses(),
            'box_selesai' => $model::cetak_selesai(),

        ];
        return view('home.opnamenew.cetak', $data);
    }
    public function sortir(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => $model::sortir_stock(),
            'box_proses' => $model::sortir_proses(),
            'box_selesai' => $model::sortir_selesai(),

        ];
        return view('home.opnamenew.sortir', $data);
    }
}
