<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        $tgl = $r->tgl ?? date('Y-m-d');
        $data = [
            'title' => 'Form Absensi',
            'tgl' => $tgl,
            'bulan' => DB::table('bulan')->get(),
            'pengawas' => DB::table('users as a')->join('tb_anak as b', 'a.id', 'b.id_pengawas')->groupBy('a.id')->get()
        ];
        return view('home.absen.index', $data);
    }
    public function getQueryDetail($id_pengawas, $bulan, $tahun)
    {
        $pengawas = $id_pengawas == 'all' ? '' : "AND a.id_pengawas = '$id_pengawas'";
        $absen = DB::select("SELECT b.name,c.nama,a.id_anak FROM `absen` as a 
       JOIN users as b on a.id_pengawas = b.id
       JOIN tb_anak as c on a.id_anak = c.id_anak
       WHERE a.tgl BETWEEN '$bulan' and '$tahun' $pengawas GROUP BY a.id_anak;");


        return $absen;
    }

    public function detailSum(Request $r)
    {
        $tgl1 = Carbon::parse("$r->tgl1");
        $tgl2 = Carbon::parse("$r->tgl2");
        $period = CarbonPeriod::create($tgl1, $tgl2);

        // Mendapatkan bulan dan tahun saat ini
        $absen = $this->getQueryDetail($r->id_pengawas, $r->tgl1, $r->tgl2);

        // $jumlahHari = Carbon::create($r->tahun, $r->bulan, 1)->daysInMonth;
        $data = [
            'title' => 'Detail Absensi',
            'period' => $period,
            'bulan' => DB::table('bulan')->get(),
            'absen' => $absen,
            'id_pengawas' => $r->id_pengawas,
            'pengawas' => DB::table('users as a')->join('tb_anak as b', 'a.id', 'b.id_pengawas')->groupBy('a.id')->get()
        ];
        return view('home.absen.detail_sum', $data);
    }

    public function exportDetail($bulan, $tahun, $id_pengawas)
    {
        $jumlahHari = Carbon::create($tahun, $bulan, 1)->daysInMonth;
        $absen = $this->getQueryDetail($id_pengawas, $bulan, $tahun);

        $jumlahHari = Carbon::create($tahun, $bulan, 1)->daysInMonth;
        $data = [
            'title' => 'Detail Absensi',
            'jumlahHari' => $jumlahHari,
            'bulan' => DB::table('bulan')->get(),
            'absen' => $absen,
            'bulanGet' => $bulan,
            'id_pengawas' => $id_pengawas,
            'tahunGet' => $tahun,
        ];

        return view('home.absen.export_detail', $data);
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
        $pngwas = DB::table('users')->where('id', $r->id_pengawas)->first();

        $data = [
            'absen' => $absen,
            'bulanGet' => $bulan,
            'tahunGet' => $tahun,
            'id_pengawas' => $r->id_pengawas,
            'nama' => $pngwas->name,
        ];
        return view('home.absen.detail_absen', $data);
    }

    public function create(Request $r)
    {
        DB::table('absen')->where('tgl', $r->tgl)->delete();

        for ($i = 0; $i < count($r->id_anak); $i++) {

            DB::table('absen')->insert([
                'id_anak' => $r->id_anak[$i],
                'id_pengawas' => $r->id_pengawas[$i],
                'tgl' => $r->tgl,
                'ket' => '',
                'id_kerja' => 0
            ]);
        }
    }
    

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
