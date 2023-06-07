<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Stock_telurController extends Controller
{
    protected $tgl1, $tgl2, $period;
    public function __construct(Request $r)
    {
        if (empty($r->period)) {
            $this->tgl1 = date('Y-m-01');
            $this->tgl2 = date('Y-m-t');
        } elseif ($r->period == 'daily') {
            $this->tgl1 = date('Y-m-d');
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'weekly') {
            $this->tgl1 = date('Y-m-d', strtotime("-6 days"));
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'mounthly') {
            $bulan = $r->bulan;
            $tahun = $r->tahun;
            $tgl = "$tahun" . "-" . "$bulan" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tgl));
            $this->tgl2 = date('Y-m-t', strtotime($tgl));
        } elseif ($r->period == 'costume') {
            $this->tgl1 = $r->tgl1;
            $this->tgl2 = $r->tgl2;
        } elseif ($r->period == 'years') {
            $tahun = $r->tahunfilter;
            $tgl_awal = "$tahun" . "-" . "01" . "-" . "01";
            $tgl_akhir = "$tahun" . "-" . "12" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tgl_awal));
            $this->tgl2 = date('Y-m-t', strtotime($tgl_akhir));
        }
    }
    public function index(Request $r)
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;

        $data =  [
            'title' => 'Stok Telur',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'stok' => DB::select("SELECT a.id_stok_telur, a.tgl, b.nm_kandang, c.nm_telur, a.pcs, a.kg, a.admin 
            FROM stok_telur as a 
            left join kandang as b on b.id_kandang = a.id_kandang
            left join telur_produk as c on c.id_produk_telur = a.id_telur
            order by a.id_stok_telur DESC
            ")

        ];
        return view('stok_telur.index', $data);
    }

    public function tbh_stok_telur(Request $r)
    {
        $data =  [
            'title' => 'Tambah Stok Telur',

        ];
        return view('stok_telur.add', $data);
    }

    public function load_menu_telur(Request $r)
    {
        $data = [
            'title' => 'load menu telur',
            'kandang' => DB::table('kandang')->get(),
            'produk' => DB::table('telur_produk')->get(),
        ];
        return view('stok_telur.load', $data);
    }
    public function tbh_baris_telur(Request $r)
    {
        $data = [
            'title' => 'load menu telur',
            'kandang' => DB::table('kandang')->get(),
            'produk' => DB::table('telur_produk')->get(),
            'count' => $r->count
        ];
        return view('stok_telur.tambah', $data);
    }

    public function save_stok_telur(Request $r)
    {
        for ($x = 0; $x < count($r->id_kandang); $x++) {
            $data = [
                'id_kandang' => $r->id_kandang[$x],
                'id_telur' => $r->id_produk_telur[$x],
                'tgl' => $r->tgl,
                'pcs' => $r->pcs[$x],
                'kg' => $r->kg[$x],
                'admin' => Auth::user()->name,
                'id_gudang' => '1'
            ];
            DB::table('stok_telur')->insert($data);
        }
        return redirect()->route('stok_telur')->with('sukses', 'Data berhasil ditambahkan');
    }

    public function transfer_stok_telur(Request $r)
    {
        $data = [
            'title' => 'Transfer Stock'
        ];
        return view('stok_telur.transfer', $data);
    }

    public function load_transfer_telur(Request $r)
    {
        $data = [
            'title' => 'Transfer Telur',
            'produk' => DB::table('telur_produk')->get(),
        ];
        return view('stok_telur.transfer_telur', $data);
    }
    public function tbh_baris_transfer(Request $r)
    {
        $data = [
            'title' => 'Transfer Telur',
            'produk' => DB::table('telur_produk')->get(),
            'count' => $r->count
        ];
        return view('stok_telur.tbh_transfer_telur', $data);
    }

    public function save_transfer_stok_telur(Request $r)
    {
        # code...
    }
}