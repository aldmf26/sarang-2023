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
            'bulan' => DB::table('bulan')->get()
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
            'cetak' => DB::select("SELECT a.id_cetak, a.selesai, c.name, d.name as pgws, b.nama as nm_anak , a.no_box, a.grade,a.tgl, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_awal_ctk as pcs_awal_ctk, a.gr_awal_ctk, a.pcs_akhir, a.gr_akhir, a.rp_satuan
            From cetak_new as a  
            LEFT join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pemberi
            left join users as d on d.id = a.id_pengawas
            where a.tgl between '$tgl1' and '$tgl2'
            order by a.pcs_akhir ASC , a.id_cetak DESC
            ;"),
            'tgl1' => $tgl1,

        ];
        return view('home.cetak_new.getdata', $data);
    }

    public function load_tambah_data(Request $r)
    {
        $data = [
            'tb_anak' => DB::table('tb_anak')->where('id_pengawas', auth()->user()->id)->get(),
            'paket' => DB::table('kelas_cetak')->orderBy('id_kelas_cetak', 'DESC')->get()
        ];
        return view('home.cetak_new.load_tambah_data', $data);
    }

    public function tambah_baris(Request $r)
    {
        $data = [
            'tb_anak' => DB::table('tb_anak')->where('id_pengawas', auth()->user()->id)->get(),
            'count' => $r->count,
            'paket' => DB::table('kelas_cetak')->orderBy('id_kelas_cetak', 'DESC')->get()
        ];
        return view('home.cetak_new.tambah_baris', $data);
    }

    public function save_target(Request $r)
    {
        for ($x = 0; $x < count($r->no_box); $x++) {

            $rp_satuan = DB::table('kelas_cetak')->where('id_kelas_cetak', $r->id_paket[$x])->first();
            $data = [
                'id_pemberi' => $r->id_pemberi,
                'id_pengawas' => auth()->user()->id,
                'no_box' => $r->no_box[$x],
                'tgl' => $r->tgl[$x],
                'id_anak' => $r->id_anak[$x],
                'pcs_awal' => $r->pcs_awal[$x],
                'gr_awal' => $r->gr_awal[$x],
                'pcs_awal_ctk' => $r->pcs_awal[$x],
                'gr_awal_ctk' => $r->gr_awal[$x],
                'id_kelas_cetak' => $r->id_paket[$x],
                'rp_satuan' => $rp_satuan->rp_pcs,
                'bulan_dibayar' => $r->bulan_dibayar
            ];
            DB::table('cetak_new')->insert($data);
        }
    }

    public function save_akhir(Request $r)
    {
        $data = [
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
            'ttl_rp' => $r->gr_akhir * $r->rp_satuan
        ];
        DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->update($data);
    }

    public function getRowData(Request $r)
    {
        $data = [
            'c' => DB::selectOne("SELECT a.id_cetak, a.selesai, c.name, d.name as pgws, b.nama as nm_anak , a.no_box, a.grade,a.tgl, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_awal_ctk as pcs_awal_ctk, a.gr_awal_ctk, a.pcs_akhir, a.gr_akhir, a.rp_satuan
            From cetak_new as a  
            LEFT join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pemberi
            left join users as d on d.id = a.id_pengawas
            where a.id_cetak = $r->id_cetak;"),
            'no' => $r->no
        ];

        return view('home.cetak_new.getRowData', $data);
    }

    public function save_selesai(Request $r)
    {
        DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->update(['selesai' => 'Y']);
    }
    public function cancel_selesai(Request $r)
    {
        DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->update(['selesai' => 'T']);
    }

    public function history(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');

        $history = DB::select("SELECT 
        a.id_anak,
        b.id_kelas as kelas,
        count(DISTINCT a.tgl) as ttl_hari,
         b.nama,
         sum(a.pcs_awal) as pcs_awal,
         sum(a.pcs_akhir) as pcs_akhir,
         sum(a.gr_awal) as gr_awal,
         sum(a.gr_akhir) as gr_akhir,
         sum(a.ttl_rp) as ttl_rp
         FROM `cetak_new` as a
        JOIN tb_anak as b on a.id_anak = b.id_anak
        WHERE a.bulan_dibayar = $bulan AND a.tahun = $tahun
        GROUP BY a.id_anak;");

        $data = [
            'title' => 'History Cetak',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'history' => $history
        ];
        return view('home.cetak_new.history',$data);
    }
}
