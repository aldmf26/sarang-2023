<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabutController extends Controller
{
    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
                ->join('tb_kelas as b', 'a.id_kelas', 'b.id_kelas')
                ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
                ->get();
    }
    public function index()
    {   
        $data = [
            'title' => 'Divisi Cabut',
            'anakNoPengawas' => $this->getAnak(1),
            'anak' => $this->getAnak(),
            'cabut' => DB::table('cabut as a')
                            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                            ->where('a.id_pengawas', auth()->user()->id)
                            ->get()
        ];
        return view('home.cabut.index',$data);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => DB::table('bk')->where('penerima', auth()->user()->id)->get(),
            'anak' => $this->getAnak(),
        ];
        return view('home.cabut.create',$data);
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => DB::table('bk')->where('penerima', auth()->user()->id)->get(),
            'anak' => $this->getAnak(),
            'count' => $r->count,

        ];
        return view('home.cabut.tbh_baris',$data);
    }

    public function get_box_sinta(Request $r)
    {
        $bk = DB::table('bk')->where('no_box', $r->no_box)->first();
        $data = [
            'pcs_awal' => $bk->pcs_awal,
            'gr_awal' => $bk->gr_awal,
        ];
        return json_encode($data);
        
    }

    public function get_kelas_anak(Request $r)
    {
        $bk = DB::table('tb_kelas')->where('id_kelas', $r->id_kelas)->first();
        $data = [
            'gr' => $bk->gr,
            'rupiah' => $bk->rupiah,
            'lokasi' => $bk->lokasi,
        ];
        return json_encode($data);
        
    }

    public function create(Request $r)
    {
        for ($i=0; $i < count($r->no_box); $i++) { 
            DB::table('cabut')->insert([
                'no_box' => $r->no_box[$i],
                'id_pengawas' => $r->id_pengawas[$i],
                'id_anak' => $r->id_anak[$i],
                'tgl_terima' => $r->tgl_terima[$i],
                'pcs_awal' => $r->pcs_awal[$i],
                'gr_awal' => $r->gr_awal[$i],
                'rupiah' => $r->rupiah[$i],
            ]);
        }

        return redirect()->route('cabut.index')->with('sukses', 'Berhasil tambah Data');
    }
    
}
