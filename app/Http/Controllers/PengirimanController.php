<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimanController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $data = [
            'title' => 'Pengiriman',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'pengiriman' => DB::table('pengiriman')->whereBetween('tgl_pengiriman', [$tgl1,$tgl2])->orderBy('id_pengiriman', 'DESC')->get()
        ];
        return view('home.pengiriman.index', $data);
    }

    public function add()
    {

        $data = [
            'title' => 'Tambah Pengiriman',
            'pengawas' => User::where('posisi_id', 13)->get(),
        ];
        return view('home.pengiriman.add', $data);
    }

    public function create(Request $r)
    {
        try {
            DB::beginTransaction();
            $admin = auth()->user()->name;
            $tgl_input = date('Y-m-d');
            $dataToInsert = [];
            for ($i = 0; $i < count($r->gr); $i++) {
                $dataToInsert[] = [
                    'tgl_pengiriman' => $r->tgl[$i],
                    'partai' => $r->partai[$i],
                    'tipe' => $r->tipe[$i],
                    'grade' => $r->grade[$i],
                    'pcs' => $r->pcs[$i],
                    'gr' => $r->gr[$i],
                    'no_box' => $r->no_box[$i],
                    'cek_akhir' => $r->cek_akhir[$i],
                    'ket' => $r->ket[$i],
                    'admin' => $admin,
                    'tgl_input' => $tgl_input,
                ];

            }

            DB::table('pengiriman')->insert($dataToInsert);

            DB::commit();
            return redirect()->route('pengiriman.index')->with('sukses', 'Data Berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengiriman.add')->with('error', 'Data Gagal input ulang');
        }
    }

    public function edit(Request $r)
    {
        $tbl = DB::table('pengiriman')->whereIn('id_pengiriman', $r->no_nota)->get();
        $data = [
            'title' => 'Edit Pengiriman',
            'pengawas' => User::where('posisi_id', 13)->get(),
            'tbl' => $tbl,
        ];
        return view('home.pengiriman.edit', $data);
    }

    public function update(Request $r)
    {
        try {
            DB::beginTransaction();
            $admin = auth()->user()->name;
            $tgl_input = date('Y-m-d');
            for ($i = 0; $i < count($r->id_pengiriman); $i++) {
                $dataToInsert = [
                    'tgl_pengiriman' => $r->tgl[$i],
                    'partai' => $r->partai[$i],
                    'tipe' => $r->tipe[$i],
                    'grade' => $r->grade[$i],
                    'pcs' => $r->pcs[$i],
                    'gr' => $r->gr[$i],
                    'no_box' => $r->no_box[$i],
                    'cek_akhir' => $r->cek_akhir[$i],
                    'ket' => $r->ket[$i],
                    'admin' => $admin,
                    'tgl_input' => $tgl_input,
                ];

                DB::table('pengiriman')->where('id_pengiriman', $r->id_pengiriman[$i])->update($dataToInsert);
            }


            DB::commit();
            return redirect()->route('pengiriman.index')->with('sukses', 'Data Berhasil diupdatekan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengiriman.index')->with('error', 'Data Gagal input ulang');
        }
    }
    public function delete(Request $r)
    {
        for ($i = 0; $i < count($r->no_nota); $i++) {
            DB::table('pengiriman')->where('id_pengiriman', $r->no_nota[$i])->delete();
        }

        return redirect("home/pengiriman")->with('sukses', 'Data berhasil dihapus');
    }
}
