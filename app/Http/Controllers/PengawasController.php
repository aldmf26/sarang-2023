<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengawasController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Pengawas',
            'user' => User::with('posisi')->where('posisi_id', 13)->get(),
            'posisi' => Posisi::all()
        ];
        return view('data_master.pengawas.index', $data);
    }
    public function anak()
    {
        $data = [
            'title' => 'Data Anak',
            'user' => DB::table('tb_anak as a')->join('users as b', 'a.id_pengawas', 'b.id')->where('b.posisi_id', 13)->get(),
            'pengawas' => User::with('posisi')->where('posisi_id', 13)->get(),

        ];
        return view('data_master.pengawas.anak', $data);
    }
}
