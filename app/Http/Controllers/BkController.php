<?php

namespace App\Http\Controllers;

use App\Exports\BkExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BkController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $data = [
            'title' => 'Divisi BK',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'bk' => DB::select("SELECT * FROM bk as a 
            left join ket_bk as b on b.id_ket_bk = a.id_ket 
            left join warna as c on c.id_warna = a.id_warna WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'")
        ];
        return view('home.bk.index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Divisi BK',
            'pengawas' => User::where('posisi_id', 13)->get(),
            'ket_bk' => DB::table('ket_bk')->get(),
            'warna' => DB::table('warna')->get(),
        ];
        return view('home.bk.create', $data);
    }

    public function create(Request $r)
    {
        for ($x = 0; $x < count($r->no_lot); $x++) {
            $data = [
                'no_lot' => $r->no_lot[$x],
                'no_box' => $r->no_box[$x],
                'tipe' => $r->tipe[$x],
                'id_ket' => $r->id_ket[$x],
                'id_warna' => $r->id_warna[$x],
                'pengawas' => $r->pgws[$x],
                'penerima' => $r->nama[$x],
                'pcs_awal' => $r->pcs_awal[$x],
                'gr_awal' => $r->gr_awal[$x],
                'tgl' => $r->tgl_terima[$x],
            ];
            DB::table('bk')->insert($data);
        }
        return redirect('home/bk');
    }

    public function print(Request $r)
    {
        $data = [
            'no_nota' => $r->no_nota,
            'title' => 'Print Bk'
        ];
        return view('home.bk.print', $data);
    }

    public function export(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.bk.export';
        $tbl = DB::select("SELECT * FROM bk as a 
        left join ket_bk as b on b.id_ket_bk = a.id_ket 
        left join warna as c on c.id_warna = a.id_warna WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'");
        $totalrow = count($tbl) + 1;

        return Excel::download(new BkExport($tbl, $totalrow, $view), 'Export BK.xlsx');
       
    }
}
