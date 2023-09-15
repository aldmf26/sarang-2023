<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        return redirect()->route('cabut.rekap');
        // $datas = DB::select("SELECT * FROM bk as a 
        // left join ket_bk as b on b.id_ket_bk = a.id_ket 
        // left join warna as c on c.id_warna = a.id_warna
        // left join users as d on d.id = a.penerima
        // left join (
        // SELECT e.no_box as n_box, sum(e.pcs_awal) as pcs_cabut, sum(e.gr_awal) as gr_cabut,sum(e.rupiah) as rupiah
        //     FROM cabut as e 
        //     GROUP by e.no_box
        // ) as e on e.n_box = a.no_box
        // ");

        // $data = [
        //     'title' => 'Dashboard',
        //     'tgl1' => '2023-08-08',
        //     'tgl2' => '2023-09-09',
        //     'datas' => $datas,
        // ];
        // return view('dashboard.dashboard', $data);
    }

    public function detail($nobox = null)
    {
        if (empty($nobox)) {
            return redirect()->back();
        }
        // $detailNobox = [
        //     'no_lot' => 'alur',
        //     'no_box' => '3001',
        //     'tipe' => 'D',
        //     'ket' => 'KL',
        //     'warna' => 'S',
        // ];
        $detailNobox = DB::selectOne("SELECT * FROM bk as a
        left join ket_bk as b on b.id_ket_bk = a.id_ket
        left join warna as c on c.id_warna = a.id_warna
        left join users as d on d.id = a.penerima
        where a.no_box = $nobox ");

        $data = [
            'title' => 'Detail Gaji Box',
            'detail' => $detailNobox,
            'cabut' => DB::select("SELECT * FROM cabut as a 
            left join tb_anak as b on b.id_anak = a.id_anak 
            left join users as c on c.id = a.id_pengawas
            where a.no_box = $nobox"),

            'cetak' => DB::select("SELECT * FROM cetak as a 
            left join tb_anak as b on b.id_anak = a.id_anak 
            left join users as c on c.id = a.id_pengawas
            where a.no_box = $nobox"),

            'sortir' => DB::table('sortir as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->join('users as d', 'a.id_pengawas', 'd.id')
                ->join('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
                ->where('a.no_box', $nobox)
                ->orderBy('id_sortir', 'DESC')
                ->get(),

            'grading_bentuk' => DB::select("SELECT *
            FROM grading_serah as a
            LEFT JOIN tipe_grade as b on b.id_tipe = a.id_tipe_grade
            where  b.status = 'bentuk' and a.no_box = '$nobox'
            "),

            'grading_turun' => DB::select("SELECT *
            FROM grading_serah as a
            LEFT JOIN tipe_grade as b on b.id_tipe = a.id_tipe_grade
            where  b.status = 'turun' and a.no_box = '$nobox'
            "),
            'grade' => DB::select("SELECT * FROM grade as a 
            left join tb_anak as b on b.id_anak = a.id_penerima
            left join users as d on d.id = a.id_pengawas
            left join (
                SELECT c.no_box as box_grading, sum(c.pcs) as pcs_akhir, sum(c.gram) as gr_akhir
                FROM grading_serah as c 
                group by c.no_box
            ) as c on c.box_grading = a.no_box")

        ];
        return view('dashboard.detail', $data);
    }
}
