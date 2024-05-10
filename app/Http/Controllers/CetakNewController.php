<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakNewController extends Controller
{
    public function index(Request $r)
    {

        if (empty($r->tgl1)) {
            $tgl1 = date('Y-m-d');
            $tgl2 = date('Y-m-d');
        } else {
            $tgl1 = $r->tgl1;
            $tgl2 = $r->tgl2;
        }
        $data = [
            'title' => 'Cetak',
            'users' => DB::table('users')->where('posisi_id', '13')->get(),
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,


        ];
        return view('home.cetak_new.index', $data);
    }


    public function get_cetak(Request $r)
    {
        if (empty($r->tgl1)) {
            $tgl1 = date('Y-m-d');
            $tgl2 = date('Y-m-t');
        } else {
            $tgl1 = $r->tgl1;
            $tgl2 = $r->tgl2;
        }
        $data = [
            'cetak' => DB::select("SELECT c.name, d.name as pgws, b.nama as nm_anak , a.no_box, a.grade,a.tgl, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_awal_ctk as pcs_awal_ctk, a.gr_awal_ctk, a.pcs_akhir, a.gr_akhir, a.rp_satuan
            From cetak_new as a  
            LEFT join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pemberi
            left join users as d on d.id = a.id_pengawas
            where a.tgl between '$tgl1' and '$tgl2';"),
            'tgl1' => $tgl1
        ];
        return view('home.cetak_new.getdata', $data);
    }

    public function load_tambah_data(Request $r)
    {
        $data = [
            'tb_anak' => DB::table('tb_anak')->where('id_pengawas', auth()->user()->id)->get()
        ];
        return view('home.cetak_new.load_tambah_data', $data);
    }

    public function tambah_baris(Request $r)
    {
        $data = [
            'tb_anak' => DB::table('tb_anak')->where('id_pengawas', auth()->user()->id)->get(),
            'count' => $r->count
        ];
        return view('home.cetak_new.tambah_baris', $data);
    }

    public function save_target(Request $r)
    {
        for ($x = 0; $x < count($r->no_box); $x++) {
            $data = [
                'id_pemberi' => $r->id_pemberi,
                'id_pengawas' => auth()->user()->id,
                'no_box' => $r->no_box[$x],
                'grade' => 'd flex',
                'tgl' => date('Y-m-d'),
                'id_anak' => $r->id_anak[$x],
                'pcs_awal' => $r->pcs_awal[$x],
                'gr_awal' => $r->gr_awal[$x],
                'pcs_awal_ctk' => $r->pcs_awal[$x],
                'gr_awal_ctk' => $r->gr_awal[$x],
                'rp_satuan' => 900
            ];
            DB::table('cetak_new')->insert($data);
        }
    }
}
