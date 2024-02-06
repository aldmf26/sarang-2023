<?php

namespace App\Http\Controllers;

use App\Exports\PengirimanTemplateExport;
use App\Imports\PengirimanImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PengirimanController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $data = [
            'title' => 'Siap Sortir',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'pengiriman' => DB::table('pengiriman')->whereBetween('tgl_pengiriman', [$tgl1, $tgl2])->orderBy('id_pengiriman', 'DESC')->get()
        ];
        return view('home.pengiriman.index', $data);
    }

    public function add()
    {

        $data = [
            'title' => 'Tambah Siap Sortir',
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
            $no_nota = DB::table('pengiriman')->orderBy('id_pengiriman', 'DESC')->first();
            $no_nota = empty($no_nota) ? 1001 : $no_nota->no_nota + 1;

            $dataToInsert = [];
            for ($i = 0; $i < count($r->gr); $i++) {
                if ($r->pcs[$i] != 0) {
                    $dataToInsert[] = [
                        'tgl_pengiriman' => $r->tgl[$i],
                        'partai' => $r->partai[$i],
                        'tipe' => $r->tipe[$i],
                        'grade' => $r->grade[$i],
                        'pcs' => $r->pcs[$i],
                        'gr' => $r->gr[$i],
                        'gr_naik' => $r->gr[$i] * 0.10,
                        'no_box' => $r->no_box[$i],
                        'cek_akhir' => $r->cek_akhir[$i],
                        'ket' => $r->ket[$i],
                        'admin' => $admin,
                        'tgl_input' => $tgl_input,
                        'no_nota' => $no_nota,
                    ];
                }
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
                    'pcs_akhir' => $r->pcs_akhir[$i],
                    'gr_akhir' => $r->gr_akhir[$i],
                    'gr_naik' => $r->gr_akhir[$i] * 0.10,
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

    public function template()
    {
        $tbl = DB::select("SELECT * FROM users as a where a.posisi_id = '13'");
        $totalrow = count($tbl) + 1;
        return Excel::download(new PengirimanTemplateExport($tbl, $totalrow), 'Template Pengiriman.xlsx');
    }

    public function import(Request $r)
    {
        $file = $r->file('file');
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        DB::beginTransaction();
        try {
            foreach (array_slice($sheetData, 1) as $row) {
                if (empty(array_filter($row))) {
                    continue;
                }

               
                $no_nota = DB::table('pengiriman')->orderBy('id_pengiriman', 'DESC')->first();
                $no_nota = empty($no_nota) ? 1001 : $no_nota->no_nota + 1;

                DB::table('pengiriman')->insert([
                    'tgl_pengiriman' => date('Y-m-d'),
                    'tgl_input' => date('Y-m-d'),
                    'partai' => $row[0],
                    'grade' => $row[1],
                    'tipe' => $row[2],
                    'pcs' => $row[3],
                    'gr' => $row[4],
                    'pcs_akhir' => $row[5],
                    'gr_akhir' => $row[6],
                    'gr_naik' => $row[6] * 0.10,
                    'no_box' => $row[7],
                    'cek_akhir' => $row[8],
                    'ket' => $row[9],
                    'admin' => auth()->user()->name,
                    'no_nota' => $no_nota,
                ]);
            }
            DB::commit();
            return redirect()->route('pengiriman.index')->with('sukses', 'Data berhasil import');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
