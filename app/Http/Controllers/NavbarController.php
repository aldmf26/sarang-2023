<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class NavbarController extends Controller
{
    public function queryNavbar($jenis)
    {
        $id_user = auth()->user()->id;

        $data = DB::table('sub_navbar as a')
            ->join('permission_navbar as b', 'a.id_sub_navbar', 'b.id_sub_navbar')
            ->where([
                ['a.navbar', $jenis],
                ['b.id_user', $id_user],
            ])
            ->orderBy('a.urutan', 'ASC')
            ->get();
        return $data;
    }
    public function data_master()
    {

        $data = $this->queryNavbar(1);

        $title = 'Data Master';
        return view('navbar.data_master', compact(['data', 'title']));
    }

    public function home()
    {
        $data = $this->queryNavbar(2);

        $title = 'Data Master';
        $sbw = Http::get("https://gudangsarang.ptagafood.com/api/sbw/sbw_kotor");
        $sbw = json_decode($sbw, TRUE);



        $sbw = $sbw['data']['sbw'];

        DB::table('sbw_kotor')->truncate();

        foreach ($sbw as $s) {
            DB::table('sbw_kotor')->insert([
                'grade_id' => $s['grade_id'],
                'rwb_id' => $s['rwb_id'],
                'nm_partai' => $s['nm_partai'],
                'no_invoice' => $s['no_invoice'],
                'pcs' => $s['pcs'],
                'kg' => $s['kg'],
                'no_kendaraan' => $s['no_kendaraan'],
                'pengemudi' => $s['pengemudi'],
                'tgl' => $s['tgl'],
            ]);
        }
        return view('navbar.data_master', compact(['data', 'title']));
    }
    public function summary()
    {
        $data = $this->queryNavbar(3);

        $title = 'Data Summary';
        return view('navbar.data_master', compact(['data', 'title']));
    }


    public function testing(Request $r)
    {
        return view('testing');
    }
}
