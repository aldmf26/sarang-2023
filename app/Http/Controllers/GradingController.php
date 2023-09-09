<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradingController extends Controller
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
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $id = auth()->user()->id;

        $data = [
            'title' => 'Divisi Grade',
            'anak' => $this->getAnak(),
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'tipe' => DB::table('tipe_grade')->where('status', 'bentuk')->get(),
            'tipe2' => DB::table('tipe_grade')->where('status', 'turun')->get(),
            'no_box' => DB::select("SELECT a.no_box, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir FROM sortir as a where a.selesai = 'Y' and a.no_box not in(SELECT b.no_box FROM grade as b )
            group by a.no_box"),
            'grade' => DB::select("SELECT * FROM grade as a 
            left join tb_anak as b on b.id_anak = a.id_penerima
            left join (
                SELECT c.no_box as box_grading, sum(c.pcs) as pcs_akhir, sum(c.gram) as gr_akhir
                FROM grading_serah as c 
                group by c.no_box
            ) as c on c.box_grading = a.no_box")
        ];
        return view('home.grade.index', $data);
    }

    public function add_target(Request $r)
    {
        for ($x = 0; $x < count($r->no_box); $x++) {
            $data = [
                'tgl' => $r->tgl[$x],
                'id_pengawas' => auth()->user()->id,
                'id_penerima' => $r->id_anak[$x],
                'pcs_awal' => $r->pcs_awal[$x],
                'gr_awal' => $r->gr_awal[$x],
                'no_box' => $r->no_box[$x]
            ];
            DB::table('grade')->insert($data);
        }
        return redirect()->route('grading.index')->with('sukses', 'Berhasil tambah Data');
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'count' => $r->count,
            'tipe' => DB::table('tipe_grade')->where('status', 'bentuk')->get(),
        ];
        return view('home.grade.tbh_baris', $data);
    }
    public function tbh_baris_turun(Request $r)
    {
        $data = [
            'count' => $r->count,
            'tipe' => DB::table('tipe_grade')->where('status', 'turun')->get(),
        ];
        return view('home.grade.tbh_baris', $data);
    }

    public function load_grade(Request $r)
    {
        $grade = DB::table('grade')->where('no_box', $r->no_box)->first();
        $data = [
            'no_box' => $r->no_box,
            'tgl' => date('d M y', strtotime($grade->tgl)),
            'pcs' => $grade->pcs_awal,
            'gr' => $grade->gr_awal
        ];
        echo json_encode($data);
    }

    public function add_grading(Request $r)
    {
        for ($x = 0; $x < count($r->grade); $x++) {
            $data = [
                'id_tipe_grade' => $r->grade[$x],
                'tgl' => date('Y-m-d'),
                'pcs' => $r->pcs[$x],
                'gram' => $r->gr[$x],
                'no_box' => $r->no_box
            ];

            DB::table('grading_serah')->insert($data);
        }
        return redirect()->route('grading.index')->with('sukses', 'Berhasil tambah Data');
    }

    public function load_detail_grading(Request $r)
    {
        $data = [
            'grade' => DB::selectOne("SELECT * FROM grade as a 
            left join tb_anak as b on b.id_anak = a.id_penerima
            where a.no_box = '$r->no_box'
            "),
            'grading_bentuk' => DB::select("SELECT *
            FROM grading_serah as a
            LEFT JOIN tipe_grade as b on b.id_tipe = a.id_tipe_grade
            where  b.status = 'bentuk' and a.no_box = '$r->no_box'
            "),
            'grading_turun' => DB::select("SELECT *
            FROM grading_serah as a
            LEFT JOIN tipe_grade as b on b.id_tipe = a.id_tipe_grade
            where  b.status = 'turun' and a.no_box = '$r->no_box'
            ")
        ];
        return view('home.grade.detail', $data);
    }
}
