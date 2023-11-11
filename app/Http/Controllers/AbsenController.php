<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        session(['tgl1' => $tgl1, 'tgl2' => $tgl2]);
        $data = [
            'title' => 'Form Absensi',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'pengawas' => DB::table('users as a')->join('tb_anak as b', 'a.id', 'b.id_pengawas')->groupBy('a.id')->get()
        ];
        return view('home.absen.index', $data);
    }

    public function detailAbsen(Request $r)
    {
        $bulan = $r->bulan ?? (int) date('m');
        $tahun = $r->tahun ?? date('Y');

        $absen = DB::select("SELECT *,count(*) as ttl FROM absen AS a
        JOIN users AS b ON a.id_pengawas = b.id
        JOIN tb_anak AS c ON a.id_anak = c.id_anak
        WHERE a.id_pengawas = '$r->id_pengawas' AND MONTH(a.tgl) = '$bulan' AND YEAR(a.tgl) = '$tahun'
        group BY a.id_anak");
        

        $data = [
            'absen' => $absen,
            'bulanGet' => $bulan,
            'tahunGet' => $tahun,
        ];
        return view('home.absen.detail_absen',$data);
    }

    public function create(Request $r)
    {
        DB::table('absen')->where([['tgl', $r->tgl],['id_pengawas', auth()->user()->id]])->delete();

        for ($i=0; $i < count($r->id_anak); $i++) { 
            
            DB::table('absen')->insert([
                'id_anak' => $r->id_anak[$i],
                'id_pengawas' => $r->id_pengawas[$i],
                'tgl' => $r->tgl,
                'ket' => '',
                'id_kerja' => 0
            ]);
        }
    }
    // public function index(Request $r)
    // {
    //     $tgl = tanggalFilter($r);
    //     $tgl1 = $tgl['tgl1'];
    //     $tgl2 = $tgl['tgl2'];
    //     session(['tgl1' => $tgl1, 'tgl2' => $tgl2]);

    //         $absen = DB::select("SELECT f.countStgh,a.id_anak,b.count,c.ttl_absen,d.nama,e.kelas,(c.ttl_absen - f.countStgh) as ttlBub FROM `absen` as a
    //         JOIN tb_anak as d ON a.id_anak = d.id_anak
    //         JOIN tb_kelas as e ON d.id_kelas = e.id_kelas
    //         LEFT JOIN (
    //             SELECT id_anak,count(*) as count FROM `absen` GROUP BY id_anak,tgl
    //         ) as b ON a.id_anak = b.id_anak
    //         LEFT JOIN (
    //             SELECT id_anak,count(*) as ttl_absen FROM `absen` WHERE ket != 'stgh hari' GROUP BY id_anak
    //         ) as c ON a.id_anak = c.id_anak
    //         LEFT JOIN (
    //             SELECT id_anak,count(*) / 2 as countStgh FROM `absen` WHERE ket = 'stgh hari' GROUP BY id_anak
    //         ) as f ON f.id_anak = a.id_anak
    //         WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' GROUP BY a.id_anak ORDER BY e.kelas DESC, d.nama;");
    //     $data = [
    //         'title' => 'Rekap Absensi',
    //         'anak' => $this->getAnak(),
    //         'tgl1' => $tgl1,
    //         'tgl2' => $tgl2,
    //         'absen' => $absen
    //     ];
    //     return view('home.absen.rekap', $data);
    // }

    public function detail($id_anak)
    {
        $tgl1 = session('tgl1');
        $tgl2 = session('tgl2');
        $absen = DB::table('absen as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'b.id_kelas', 'c.id_kelas')
            ->where('a.id_anak', $id_anak)->whereBetween('a.tgl', [$tgl1, $tgl2])->get();
        $data = [
            'absen' => $absen
        ];
        return view('home.absen.rekap_detail', $data);
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'anak' => $this->getAnak(),
            'count' => $r->count
        ];
        return view('home.absen.tbh_baris', $data);
    }
    public function create_stgh_hari(Request $r)
    {
        for ($i = 0; $i < count($r->id_anak); $i++) {
            DB::table('absen')->insert([
                'id_anak' => $r->id_anak[$i],
                'tgl' => $r->tgl[$i],
                'ket' => 'stgh hari'
            ]);
        }
        return redirect()->route('absen.index')->with('sukses', 'Data Berhasil ditambahkan');
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
        DB::table('absen')->where([['id_anak', $r->id_anak], ['tgl', $r->tgl]])->delete();
    }
}
