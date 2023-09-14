<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenController extends Controller
{
    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }
    public function index(Request $r)
    {
        if (empty($r->tgl)) {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = $r->tgl;
        }

        $data = [
            'title' => 'Absensi',
            'anak' => $this->getAnak(),
            'tanggal' => $tanggal
        ];
        return view('home.absen.index', $data);
    }

    public function tabelAbsen(Request $r)
    {
        if (empty($r->tgl)) {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = $r->tgl;
        }

        $data = [
            'title' => 'Absensi',
            'anak' => $this->getAnak(),
            'tanggal' => $tanggal
        ];
        return view('home.absen.table_absen', $data);
    }

    public function SaveAbsen(Request $r)
    {
        DB::table('absen')->where([['id_anak', $r->id_anak], ['tgl', $r->tgl]])->delete();
        $data = [
            'tgl' => $r->tgl,
            'id_anak' => $r->id_anak,
            'nilai' => $r->ket,
            'id_pengawas' => auth()->user()->id
        ];
        DB::table('absen')->insert($data);
    }
    public function delete_absen(Request $r)
    {
        DB::table('absen')->where('id_absen', $r->id_absen)->delete();
    }
}
