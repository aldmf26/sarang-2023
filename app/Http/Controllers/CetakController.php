<?php

namespace App\Http\Controllers;

use App\Exports\CabutRekapExport;
use App\Exports\CetakExport;
use App\Exports\CetakRekapExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class CetakController extends Controller
{

    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }
    public function getStokBk($no_box = null)
    {
        $id_user = auth()->user()->id;
        $query = !empty($no_box) ? "selectOne" : 'select';
        $noBoxAda = !empty($no_box) ? "a.no_box = '$no_box' AND" : '';

        return DB::$query("SELECT a.no_box, a.pcs_awal,b.pcs_awal as pcs_cabut,a.gr_awal,b.gr_awal as gr_cabut FROM `bk` as a
        LEFT JOIN (
            SELECT max(no_box) as no_box,sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal  FROM `cetak` GROUP BY no_box,id_pengawas
        ) as b ON a.no_box = b.no_box WHERE  $noBoxAda a.penerima = '$id_user'");
    }
    public function index(Request $r)
    {

        $id = auth()->user()->id;

        $tgl = tanggalFilter($r);
        $tgl1 =  $tgl['tgl1'];
        $tgl2 =  $tgl['tgl2'];

        $data = [
            'title' => 'Divisi Cetak',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cetak' => DB::select("SELECT *
            FROM cetak as a
            LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
            where a.id_pengawas = '$id' and a.tgl between '$tgl1' and '$tgl2'
            "),
            'anakNoPengawas' => $this->getAnak(1),
            'anak' => $this->getAnak(),
            'cabut' => DB::select("SELECT a.no_box, (a.pcs_awal - IFNULL(b.pcs_awal_ctk, 0)) as pcs, (a.gr_awal - IFNULL(b.gr_awal_ctk, 0)) as gr
            FROM cabut as a 
            LEFT JOIN (
                SELECT b.no_box, SUM(b.pcs_awal) as pcs_awal_ctk, SUM(b.gr_awal) as gr_awal_ctk
                FROM cetak as b
                GROUP BY b.no_box
            ) as b ON b.no_box = a.no_box
            WHERE a.selesai = 'Y' AND (a.pcs_awal - IFNULL(b.pcs_awal_ctk, 0)) != 0")
        ];
        return view('home.cetak.index', $data);
    }

    function get_cetak(Request $r)
    {
        $id = auth()->user()->id;

        $tgl = tanggalFilter($r);
        $tgl1 =  $tgl['tgl1'];
        $tgl2 =  $tgl['tgl2'];
        $data = [
            'cetak' => DB::select("SELECT *
            FROM cetak as a
            LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
            left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
            where a.id_pengawas = '$id' and a.tgl between '$tgl1' and '$tgl2'
            "),
        ];
        return view('home.cetak.get', $data);
    }



    function get_box(Request $r)
    {
        $no_box = DB::table('bk')->where('no_box', $r->no_box)->first();

        $data = [
            'pcs' => $no_box->pcs_awal,
            'gr' => $no_box->gr_awal,
        ];
        echo json_encode($data);
    }

    function input_akhir()
    {
        $cetak = DB::select("SELECT *
        FROM cetak as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
        where a.selesai = 'T' and a.status = 'akhir'
        ");

        $data = [
            'cetak' => $cetak,
            'bulan' => DB::table('bulan')->get()
        ];
        return view('home.cetak.input_akhir', $data);
    }
    public function getTotalAnak()
    {
        $tgl = date('Y-m-d');
        $id = auth()->user()->id;

        $totalAnak = DB::table('absen as a')
            ->leftJoin('tb_anak as b', 'b.id_anak', '=', 'a.id_anak')
            ->where('a.tgl', $tgl)
            ->where('b.id_pengawas', $id)
            ->count();

        return response()->json(['total_anak' => $totalAnak]);
    }

    function ambil_awal()
    {
        $id = auth()->user()->id;
        $cetak = DB::select("SELECT *
        FROM cetak as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join users as c on c.id = a.id_pengawas
        where a.status = 'awal' and a.id_pengawas = '$id'
        ");


        $data = [
            'cetak' => $cetak,
            'bk' => $this->getStokBk(),
            'kelas' => DB::select("SELECT *
            FROM kelas_cetak as a
            left join paket_cabut as b on b.id_paket = a.id_paket
            left join tipe_cabut as c on c.id_tipe = a.tipe
            ")
        ];
        return view('home.cetak.ambil_awal', $data);
    }

    function delete_awal_cetak(Request $r)
    {
        DB::table('cetak')->where('id_cetak', $r->id_cetak)->delete();
    }

    function load_anak_kerja_belum(Request $r)
    {
        $tgl = date('Y-m-d');
        $id = auth()->user()->id;
        $absen = DB::select("SELECT a.id_anak, b.nama, b.id_kelas
        From absen as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        where a.tgl = '$tgl' and b.id_pengawas = $id
        group by a.id_anak");

        $data = [
            'anak' => $absen,
            'status' => 'awal'
        ];
        return view('home.cetak.get_anak', $data);
    }

    function save_kerja(Request $r)
    {
        for ($x = 0; $x < count($r->id_anak); $x++) {
            $data = [
                'id_anak' => $r->id_anak[$x],
                'id_pengawas' => auth()->user()->id
            ];
            DB::table('cetak')->insert($data);
        }
    }

    function selesai_cetak(Request $r)
    {
        DB::table('cetak')->where('id_cetak', $r->id_cetak)->update(['selesai' => 'Y']);
    }

    public function add_target(Request $r)
    {
        for ($x = 0; $x < count($r->no_box); $x++) {
            $data = [
                'tgl' => $r->tgl[$x],
                'no_box' => $r->no_box[$x],
                'id_kelas' => $r->id_kelas_cetak[$x],
                'pcs_awal' => $r->pcs_awal[$x],
                'gr_awal' => $r->gr_awal[$x],
                'rp_pcs' => $r->rp_pcs[$x],
                'status' => 'akhir'
            ];
            DB::table('cetak')->where('id_cetak', $r->id_cetak[$x])->update($data);
        }
        return redirect()->route('cetak.index')->with('sukses', 'Berhasil tambah Data');
    }

    function get_kelas(Request $r)
    {
        $kelas = DB::table('kelas_cetak')->where('id_kelas_cetak', $r->id_kelas_cetak)->first();

        echo $kelas->rp_pcs;
    }


    public function akhir(Request $r)
    {
        $data = [
            'cetak' => DB::table('cetak as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where('a.id_cetak', $r->id_cetak)
                ->first(),
        ];
        return view('home.cetak.akhir', $data);
    }

    function save_akhir(Request $r)
    {
        DB::table('cetak')->where([['id_cetak', $r->id_cetak]])->update([
            'pcs_tidak_ctk' => $r->pcs_tidak_ctk,
            'gr_tidak_ctk' => $r->gr_tidak_ctk,
            'pcs_awal_ctk' => $r->pcs_awal_ctk,
            'gr_awal_ctk' => $r->gr_awal_ctk,
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
            'pcs_hcr' => $r->pcs_hcr,
            'bulan_dibayar' => $r->bulan_dibayar,
        ]);
    }

    function load_row(Request $r)
    {
        $cetak = DB::selectOne("SELECT *
        FROM cetak as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
        where a.id_cetak = $r->id
        ");

        $data = [
            'c' => $cetak,
            'bulan' => DB::table('bulan')->get()
        ];
        return view('home.cetak.load_row', $data);
    }

    public function add_akhir(Request $r)
    {
        $data = [
            'pcs_tidak_ctk' => $r->pcs_tidak_ctk,
            'gr_tidak_ctk' => $r->gr_tidak_ctk,
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
        ];

        DB::table('cetak')->where('id_cetak', $r->id_cetak)->update($data);
        return redirect()->route('cetak.index')->with('sukses', 'Berhasil tambah Data');
    }

    public function selesai(Request $r)
    {
        DB::table('cetak')->where('id_cetak', $r->id_cetak)->update(['selesai' => 'Y']);
        return redirect()->route('cetak.index')->with('sukses', 'Data telah diselesaikan');
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'count' => $r->count,
            'anakNoPengawas' => $this->getAnak(1),
            'anak' => $this->getAnak(),
            'cabut' => DB::select("SELECT a.no_box, (a.pcs_awal - IFNULL(b.pcs_awal_ctk, 0)) as pcs, (a.gr_awal - IFNULL(b.gr_awal_ctk, 0)) as gr
            FROM cabut as a 
            LEFT JOIN (
                SELECT b.no_box, SUM(b.pcs_awal) as pcs_awal_ctk, SUM(b.gr_awal) as gr_awal_ctk
                FROM cetak as b
                GROUP BY b.no_box
            ) as b ON b.no_box = a.no_box
            WHERE a.selesai = 'Y' AND (a.pcs_awal - IFNULL(b.pcs_awal_ctk, 0)) != 0")
        ];
        return view('home.cetak.tbh_baris', $data);
    }

    public function export(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.cetak.export';

        $tbl = DB::select("SELECT *
            FROM cetak as a
            LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
            where a.tgl between '$tgl1' and '$tgl2'
            ");

        $totalrow = count($tbl) + 1;

        return Excel::download(new CetakExport($tbl, $totalrow, $view), 'Export CETAK.xlsx');
    }

    public function queryRekap($tgl1, $tgl2)
    {
        $id = auth()->user()->id;
        $posisi = auth()->user()->posisi_id;
        $pengawas = $posisi == 13 ? "AND a.id_pengawas = '$id'" : '';

        return DB::select("SELECT
                    MAX(a.no_box) as no_box,
                    MAX(a.tgl) as tgl,
                    a.pcs_awal,
                    a.gr_awal,
                    a.gr_akhir,
                    a.gr_tidak_ctk,
                    a.rp_pcs,
                    b.name,
                    c.pcs_akhir as cabut_pcs_akhir,
                    c.gr_akhir as cabut_gr_akhir
                FROM cetak as a
                LEFT JOIN users as b ON a.id_pengawas = b.id
                LEFT JOIN (
                    SELECT no_box, SUM(pcs_akhir) as pcs_akhir, SUM(gr_akhir) as  gr_akhir
                    FROM cabut
                    GROUP BY no_box
                ) as c ON a.no_box = c.no_box
                WHERE a.selesai = 'Y' AND a.tgl BETWEEN '$tgl1' AND '$tgl2' $pengawas
                GROUP BY a.pcs_awal, a.gr_awal, b.name, c.pcs_akhir, c.gr_akhir;
            ");
    }

    public function rekap(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 =  $tgl['tgl1'];
        $tgl2 =  $tgl['tgl2'];
        $datas = $this->queryRekap($tgl1, $tgl2);

        $data = [
            'title' => 'Rekap Summary Cetak',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'datas' => $datas,
        ];
        return view('home.cetak.rekap', $data);
    }

    public function export_rekap(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.cetak.export_rekap';
        $tbl = $this->queryRekap($tgl1, $tgl2);

        return Excel::download(new CetakRekapExport($tbl, $view), 'Export REKAP CETAK.xlsx');
    }
}
