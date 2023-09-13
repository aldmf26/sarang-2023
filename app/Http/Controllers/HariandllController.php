<?php

namespace App\Http\Controllers;

use App\Exports\HariandllExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HariandllController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $data = [
            'title' => 'Harian DLL',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'anak' => DB::table('tb_anak')->get(),
            'datas' => DB::table('tb_hariandll  as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->whereBetween('a.tgl', [$tgl1, $tgl2])
                ->orderBy('a.id_hariandll', 'DESC')
                ->get()
        ];
        return view('home.hariandll.index', $data);
    }
    public function tbh_baris(Request $r)
    {
        $data = [
            'anak' => DB::table('tb_anak')->get(),
            'count' => $r->count,
        ];
        return view('home.hariandll.tbh_baris', $data);
    }

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->id_anak); $i++) {
            DB::table('tb_hariandll')->insert([
                'tgl' => $r->tgl,
                'id_anak' => $r->id_anak[$i],
                'ket' => $r->ket[$i],
                'rupiah' => $r->rupiah[$i],
                'lokasi' => $r->lokasi[$i],
            ]);
        }
        return redirect()->route('hariandll.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function edit_load($id)
    {
        $data = [
            'detail' => DB::table('tb_hariandll')->where('id_hariandll', $id)->first(),
            'anak' => DB::table('tb_anak')->get(),
        ];
        return view('home.hariandll.edit_load', $data);
    }

    public function update(Request $r)
    {
        DB::table('tb_hariandll')->where('id_hariandll', $r->id_hariandll)->update([
            'tgl' => $r->tgl,
            'id_anak' => $r->id_anak,
            'ket' => $r->ket,
            'rupiah' => $r->rupiah,
            'lokasi' => $r->lokasi,
        ]);
        return redirect()->route('hariandll.index')->with('sukses', 'Data Berhasil diubah');
    }

    public function delete(Request $r)
    {
        DB::table('tb_hariandll')->where('id_hariandll', $r->urutan)->delete();
        return redirect()->route('hariandll.index')->with('sukses', 'Data Berhasil dihapus');
    }

    public function export(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.hariandll.export';
        $tbl = DB::table('tb_hariandll  as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->whereBetween('a.tgl', [$tgl1, $tgl2])
            ->orderBy('a.id_hariandll', 'DESC')
            ->get();

        return Excel::download(new HariandllExport($tbl, $view), 'Export HARIAN DLL.xlsx');
    }
}
