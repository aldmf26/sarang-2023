<?php

namespace App\Http\Controllers;

use App\Imports\PenutupImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PenutupController extends Controller
{
    public function index(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        // $gaji = DB::select()
        $data = [
            'title' => 'Data Gaji Penutup',
            'gaji' => DB::table('tb_gaji_penutup')->get(),
        ];
        return view('data_master.penutup.index',$data);
    }

    public function import(Request $r)
    {
        $r->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        Excel::import(new PenutupImport, $r->file('file'));
        return redirect()->route('penutup.index')->with('sukses', 'Data berhasil import');

    }
}
