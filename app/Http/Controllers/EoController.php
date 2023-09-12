<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EoController extends Controller
{
    public function getStokBk($no_box = null)
    {
        $id_user = auth()->user()->id;
        $query = !empty($no_box) ? "selectOne" : 'select';
        $noBoxAda = !empty($no_box) ? "a.no_box = '$no_box' AND" : '';

        return DB::$query("SELECT a.no_box, a.pcs_awal,a.gr_awal FROM `bk` as a
         ");
    }

    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }

    public function index()
    {
        $data = [
            'title' => 'Data EO',
            'eo' => DB::table('eo as a')
                        ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                        ->join('tb_kelas_eo as c', 'a.id_kelas', 'c.id_kelas')
                        ->orderBy('a.id_eo', 'DESC')->get(),
            'nobox' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'kelas' => DB::table('tb_kelas_eo')->get()
        ];
        return view('home.eo.index', $data);
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'nobox' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'kelas' => DB::table('tb_kelas_eo')->get(),
            'count' => $r->count,
        ];
        return view('home.eo.tbh_baris', $data);
    }

    public function create(Request $r)
    {
        for ($i=0; $i < count($r->id_anak); $i++) { 
            DB::table('eo')->insert([
                'tgl_input' => date('Y-m-d'),
                'id_pengawas' => auth()->user()->id,
                'id_anak' => $r->id_anak[$i],
                'no_box' => $r->no_box,
                'id_kelas' => $r->id_kelas[$i],
                'tgl_ambil' => $r->tgl_ambil[$i],
                'gr_eo_awal' => $r->gr_eo_awal[$i],
            ]);
        }
        return redirect()->route('eo.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('eo as a')
            ->select('a.id_kelas as id_kelas', 'b.nama','a.*')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['a.id_anak', $r->id_anak], ['a.no_box', $r->no_box]])
            ->first();
        $data = [
            'detail' => $detail
        ];
        return view('home.eo.load_modal_akhir', $data);
    }

    public function input_akhir(Request $r)
    {
        $getKelas = DB::table('tb_kelas_eo')->where('id_kelas', $r->id_kelas)->first();
        $ttl_rp = $getKelas->rupiah * $r->gr_eo_akhir;
        DB::table('eo')->where('id_eo', $r->id_eo)->update([
            'gr_eo_akhir' => $r->gr_eo_akhir,
            'tgl_serah' => $r->tgl_serah,
            'ttl_rp' => $ttl_rp,
        ]);

        return redirect()->route('eo.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

}
