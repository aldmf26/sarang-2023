<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPegawaiController extends Controller
{
    public function index()
    {
        // Ambil semua data pegawai
        $dataPegawai = DB::table('hasil_wawancara as a')
                        ->leftJoin('divisis as b','a.id_divisi','b.id' )
                        ->leftJoin('tb_anak as c','a.id_anak','c.id_anak' )
                        ->selectRaw("
                        a.id as id_pegawai,
                        a.nama,
                        c.id_kelas as kelas_cbt,
                        a.nik,
                        a.tgl_lahir,
                        a.jenis_kelamin,
                        a.tgl_masuk,
                        b.divisi,
                        a.kesimpulan,
                        a.keputusan,
                        a.periode_masa_percobaan as periode,
                        a.keputusan_lulus as keputusan,
                        a.posisi2 as jabatan
                        ")
                        ->get();

        $datas = [
            'pegawai' => $dataPegawai,
            'total' => count($dataPegawai)
        ];
        return response()->json($datas, 200);
    }
}
