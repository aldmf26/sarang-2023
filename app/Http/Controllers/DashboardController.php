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

    public function detail($kategori, $nobox = null)
    {
        if (empty($nobox)) {
            return redirect()->back();
        }

        switch ($kategori) {
            case 'cabut':
                $query = DB::select("SELECT 
                a.no_box,
                b.name as pgws, 
                c.nama as nm_anak,
                a.bulan_dibayar as bulan,
                a.tahun_dibayar as tahun,
                a.pcs_awal,
                a.gr_awal,
                a.pcs_akhir,
                a.gr_akhir,
                a.gr_flx,
                a.eot,
                a.ttl_rp
                FROM `cabut` as a 
                JOIN users as b on a.id_pengawas = b.id
                JOIN tb_anak as c on a.id_anak = c.id_anak
                where a.no_box = '$nobox';");
                $view = 'detail';
                break;
            case 'eo':
                $query = DB::select("SELECT 
                a.no_box,
                b.name as pgws, 
                c.nama as nm_anak,
                a.bulan_dibayar as bulan,
                YEAR(a.tgl_input) as tahun,
                a.gr_eo_awal as gr_awal,
                a.gr_eo_akhir as gr_akhir,
                a.ttl_rp
                FROM `eo` as a 
                JOIN users as b on a.id_pengawas = b.id
                JOIN tb_anak as c on a.id_anak = c.id_anak
                where a.no_box = '$nobox';");
                $view = 'detail2';
                break;
            case 'sortir':
                $query = DB::select("SELECT 
                a.no_box,
                b.name as pgws, 
                c.nama as nm_anak,
                a.bulan,
                YEAR(a.tgl_input) as tahun,
                a.pcs_awal,
                a.gr_awal,
                a.pcs_akhir,
                a.gr_akhir,
                a.ttl_rp
                FROM `sortir` as a 
                JOIN users as b on a.id_pengawas = b.id
                JOIN tb_anak as c on a.id_anak = c.id_anak
                where a.no_box = '$nobox';");
                $view = 'detail2';
                break;
            default:
                
                break;
        }
        $data = [
            'title'   => "Detail Box $kategori - $nobox",
            'query' => $query,
            'kategori' => $kategori
        ];
        return view("dashboard.$view", $data);
    }

}
