<?php

namespace App\Http\Controllers;

use App\Exports\CetakExport;
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

    public function add_target(Request $r)
    {
        for ($x = 0; $x < count($r->no_box); $x++) {
            $data = [
                'no_box' => $r->no_box[$x],
                'id_anak' => $r->id_anak[$x],
                'target' => $r->target[$x],
                'rp_pcs' => $r->rp_pcs[$x],
                'pcs_awal' => $r->pcs_awal[$x],
                'gr_awal' => $r->gr_awal[$x],
                'grade' => $r->grade[$x],
                'tgl' => $r->tgl[$x],
                'id_pengawas' => auth()->user()->id
            ];
            DB::table('cetak')->insert($data);
        }
        return redirect()->route('cetak.index')->with('sukses', 'Berhasil tambah Data');
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
}
