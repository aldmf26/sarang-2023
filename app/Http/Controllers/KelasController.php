<?php

namespace App\Http\Controllers;

use App\Exports\PaketTemplateExport;
use App\Imports\PaketImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Nonaktif;

class KelasController extends Controller
{
    public function index(Request $r)
    {
        $jenis = 2;
        $datas = DB::select("SELECT * FROM tb_kelas 
                    WHERE nonaktif = 'T' AND jenis = $jenis AND id_kategori != 3 
                    ORDER BY id_kelas DESC");
        $data = [
            'title' => 'Data Paket Cabut',
            'jenis' => $jenis,
            'lokasi' => ['alpa', 'mtd', 'sby'],
            'tipe' => DB::table('tipe_cabut')->get(),
            'kategori' => DB::table('paket_cabut')->get(),
            'datas' => $datas
        ];
        if($jenis == '2') {
            return view("data_master.kelas.gr", $data);
        } else {
            return view("data_master.kelas.index", $data);
        }
    }

    public function importExcel(Request $r)
    {
        try {
            // Load Excel file
            $file = $r->file('file'); // Pastikan file di-upload melalui form
            $data = Excel::toArray([], $file);
            
            $kelasData = [];

            // Memproses setiap baris di Excel
            foreach ($data[0] as $row) {
                // Pecah 'paket' menjadi 'kelas' dan 'tipe'
                preg_match('/^(\d+)\s*(\w+)/', $row[0], $matches);
                $kelas = $matches[1] ?? '';
                $tipe = $matches[2] ?? '';

                $kelasData[] = [
                    'kelas' => $kelas,
                    'paket' => $tipe,
                    'lokasi' => $row[1],
                    'gr' => $row[2],
                    'rp' => $row[3],
                    'denda_susut' => str_replace('%', '', $row[4]),
                    'batas_susut' => $row[5],
                    'bonus_susut' => $row[6],
                    'rp_bonus' => $row[7],
                    'batas_eot' => $row[8],
                    'eot' => $row[9],
                    'denda_hcr' => $row[10]
                ];
            }

            // Kirim array $kelasData ke function create_gr
            $this->create_gr_from_excel($kelasData);

            return redirect()->back()->with('success', 'Data berhasil di-import');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create_gr_from_excel(array $data)
    {
        try {
            DB::beginTransaction();

            foreach ($data as $row) {
                DB::table('tb_kelas')->insert([
                    'kelas' => $row['kelas'],
                    'tipe' => $row['paket'],
                    'lokasi' => $row['lokasi'],
                    'id_tipe_brg' => 33,
                    'id_paket' => 2,
                    'id_kategori' => 2,
                    'jenis' => 2,
                    'pcs' => 0,
                    'gr' => $row['gr'] ?? 0,
                    'rupiah' => $row['rp'],
                    'rp_bonus' => $row['rp_bonus'],
                    'batas_susut' => $row['batas_susut'],
                    'denda_susut_persen' => $row['denda_susut'],
                    'bonus_susut' => $row['bonus_susut'],
                    'batas_eot' => $row['batas_eot'],
                    'eot' => $row['eot'],
                    'denda_hcr' => $row['denda_hcr'],
                ]);
            }

            DB::commit();
            return redirect()->back()->with('sukses', 'Data berhasil dimasukkan');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create_gr(Request $r)
    {
        try {
            DB::beginTransaction();
            for ($i = 0; $i < count($r->gr); $i++) {
                DB::table('tb_kelas')->insert([
                    'kelas' => $r->kelas[$i],
                    'tipe' => $r->paket[$i],
                    'lokasi' => $r->lokasi[$i],
                    'id_tipe_brg' => 33,
                    'id_paket' => 2,
                    'id_kategori' => 2,
                    'jenis' => 2,
                    'pcs' => 0,
                    'gr' => $r->gr[$i] ?? 0,
                    'rupiah' => $r->rp[$i],
                    'rp_bonus' => $r->rp_bonus[$i],
                    'batas_susut' => $r->batas_susut[$i],
                    'denda_susut_persen' => $r->denda_susut[$i],
                    'bonus_susut' => $r->bonus_susut[$i],
                    'batas_eot' => $r->batas_eot[$i],
                    'eot' => $r->eot[$i],
                    'denda_hcr' => $r->denda_hcr[$i],
                ]);
            }
            DB::commit();
            return redirect()->back()->with('sukses', 'berhasil tambah kelas');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
        
    }

    public function template_import()
    {
        return Excel::download(new PaketTemplateExport, 'Paket Sarang.xlsx');
    }

    public function import(Request $r)
    {
        try {
            DB::beginTransaction();
            DB::table('tb_kelas')
                ->where('nonaktif', 'T')
                ->update(['edit_import' => now(), 'nonaktif' => 'Y']);
    
            DB::table('tb_kelas_sortir')
                ->where('nonaktif', 'T')
                ->update(['edit_import' => now(), 'nonaktif' => 'Y']);
    
            Excel::import(new PaketImport, $r->file);
            DB::commit();
            return redirect()->back()->with('sukses', 'berhasil tambah kelas');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update_gr($id, Request $r)
    {
        try {
            DB::beginTransaction();
            DB::table('tb_kelas')->where('id_kelas', $id)->update([
                'kelas' => $r->kelas,
                'tipe' => $r->paket,
                'lokasi' => $r->lokasi,
                'id_tipe_brg' => 33,
                'id_paket' => 2,
                'id_kategori' => 2,
                'jenis' => 2,
                'pcs' => 0,
                'gr' => $r->gr ?? 0,
                'rupiah' => $r->rp,
                'rp_bonus' => $r->rp_bonus,
                'batas_susut' => $r->batas_susut,
                'denda_susut_persen' => $r->denda_susut,
                'bonus_susut' => $r->bonus_susut,
                'batas_eot' => $r->batas_eot,
                'eot' => $r->eot,
                'denda_hcr' => $r->denda_hcr,
            ]);
            DB::commit();
            return redirect()->back()->with('sukses', 'berhasil tambah kelas');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
        
    }
    public function delete_gr($id)
    {
        DB::table('tb_kelas')->where('id_kelas', $id)->delete();
        return redirect()->back()->with('sukses', 'berhasil hapus kelas');
    }

    public function cabutCreate(Request $r)
    {
        $buang = [
            'rupiah', 'pcs', 'gr', 'rp_bonus', 'bonus_susut', 'batas_susut', 'denda_susut_persen', 'batas_eot', 'eot', 'denda_hcr',
            'rupiah_tambah', 'pcs_tambah', 'gr_tambah', 'rp_bonus_tambah', 'bonus_susut_tambah', 'batas_susut_tambah', 'denda_susut_persen_tambah', 'batas_eot_tambah', 'eot_tambah', 'denda_hcr_tambah'
        ];
        foreach ($buang as $d) {
            $r->$d = str()->remove(',', $r->$d);
        }

        if (!empty($r->rupiah_tambah[0])) {
            for ($i = 0; $i < count($r->rupiah_tambah); $i++) {
                DB::table('tb_kelas')->insert([
                    'kelas' => $r->kelas_tambah[$i],
                    'gr' => $r->gr_tambah[$i] ?? 0,
                    'pcs' => $r->pcs_tambah[$i] ?? 0,
                    'rupiah' => $r->rupiah_tambah[$i],
                    'rp_bonus' => $r->rp_bonus_tambah[$i],
                    'batas_susut' => $r->batas_susut_tambah[$i],
                    'denda_susut_persen' => $r->denda_susut_persen_tambah[$i],
                    'bonus_susut' => $r->bonus_susut_tambah[$i],
                    'batas_eot' => $r->batas_eot_tambah[$i],
                    'eot' => $r->eot_tambah[$i],
                    'denda_hcr' => $r->denda_hcr_tambah[$i],
                    'id_kategori' => $r->id_kategori_tambah[$i],
                    'id_paket' => $r->id_kategori_tambah[$i],
                    'id_tipe_brg' => $r->id_tipe_brg_tambah[$i],
                    'jenis' => $r->jenis,
                ]);
            }
        }

        if (!empty($r->rupiah[0])) {
            for ($i = 0; $i < count($r->rupiah); $i++) {
                DB::table('tb_kelas')->where('id_kelas', $r->id_kelas[$i])->update([
                    'kelas' => $r->kelas[$i],
                    'jenis' => $r->jenis,
                    'pcs' => $r->pcs[$i] ?? 0,
                    'gr' => $r->gr[$i] ?? 0,
                    'rupiah' => $r->rupiah[$i],
                    'rp_bonus' => $r->rp_bonus[$i],
                    'id_kategori' => $r->id_kategori[$i],
                    'id_paket' => $r->id_kategori[$i],
                    'id_tipe_brg' => $r->id_tipe_brg[$i],
                    'denda_susut_persen' => $r->denda_susut_persen[$i],
                    'batas_susut' => $r->batas_susut[$i],
                    'bonus_susut' => $r->bonus_susut[$i],
                    'batas_eot' => $r->batas_eot[$i],
                    'eot' => $r->eot[$i],
                    'denda_hcr' => $r->denda_hcr[$i],
                ]);
            }
        }
        return redirect()->route('kelas.index', ['jenis' => $r->jenis == 2 ? 'gr' : ''])->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function info($id_kelas)
    {
        $detail = DB::table('tb_kelas')->where('id_kelas', $id_kelas)->first();
        $view = [
            1 => 'cabut',
            2 => 'spesial',
            3 => 'eo',
        ];
        $data = [
            'detail' => $detail,
            'jenis' => $view[$detail->id_kategori]
        ];

        return view('data_master.kelas.info_' . $data['jenis'], $data);
    }
    public function deleteCabut(Request $r)
    {
        foreach ($r->datas as $d) {
            Nonaktif::delete('tb_kelas', 'id_kelas', $d);
            // DB::table('tb_kelas')->where('id_kelas', $d)->delete();
        }
        return '2323';
    }

    public function deleteSortir(Request $r)
    {
        foreach ($r->datas as $d) {
            Nonaktif::delete('tb_kelas_sortir', 'id_kelas', $d);
        }
        return '2323';
    }

    public function spesial(Request $r)
    {
        $jenis = empty($r->jenis) ? 1 : 2;

        $data = [
            'title' => 'Kelas Spesial',
            'jenis' => $jenis,
            'paket' => DB::table('paket_cabut')->get(),
            'tipe' => DB::table('tipe_cabut')->get(),
            'datas' => DB::table('tb_kelas')->where([['id_kategori', 2], ['jenis', '!=', 2]])->where('nonaktif', 'T')->orderBy('id_kategori', 'ASC')->get()
        ];
        return view('data_master.kelas.spesial_index', $data);
    }
    public function spesialCreate(Request $r)
    {
        $buang = [
            'rupiah', 'pcs', 'pcs_xbayar',
            'rupiah_tambah', 'pcs_tambah', 'pcs_xbayar_tambah'
        ];
        foreach ($buang as $d) {
            $r->$d = str()->remove(',', $r->$d);
        }

        if (!empty($r->rupiah_tambah[0])) {
            for ($i = 0; $i < count($r->rupiah_tambah); $i++) {
                DB::table('tb_kelas')->insert([
                    'kelas' => $r->kelas[$i],
                    'jenis' => 1,
                    'pcs' => $r->pcs_tambah[$i] ?? 0,
                    'pcs_xbayar' => $r->pcs_xbayar_tambah[$i] ?? 0,
                    'rupiah' => $r->rupiah_tambah[$i],
                    'id_kategori' => 2,
                    'id_tipe_brg' => $r->id_tipe_brg_tambah[$i],
                    'id_paket' => $r->id_paket_tambah[$i],
                ]);
            }
        }

        if (!empty($r->rupiah[0])) {
            for ($i = 0; $i < count($r->rupiah); $i++) {
                // $cek = DB::table('tb_kelas')->where('id_kelas', $r->id_kelas[$i])->first();

                // DB::table('tb_kelas')->where('id_kelas', $r->id_kelas[$i])->update([
                //     'kelas' => $r->kelas[$i],
                //     'jenis' => 1,
                //     'pcs' => $r->pcs[$i] ?? 0,
                //     'pcs_xbayar' => $r->pcs_xbayar[$i] ?? 0,
                //     'rupiah' => $r->rupiah[$i],
                //     'id_kategori' => 2,
                //     'id_tipe_brg' => $r->id_tipe_brg[$i],
                //     'id_paket' => $r->id_paket[$i],
                // ]);
                $data = [
                    'kelas' => $r->kelas[$i],
                    'jenis' => 1,
                    'pcs' => $r->pcs[$i] ?? 0,
                    'pcs_xbayar' => $r->pcs_xbayar[$i] ?? 0,
                    'rupiah' => $r->rupiah[$i],
                    'id_kategori' => 2,
                    'id_tipe_brg' => $r->id_tipe_brg[$i],
                    'id_paket' => $r->id_paket[$i],
                ];
                Nonaktif::edit('tb_kelas', 'id_kelas', $r->id_kelas[$i], $data);
            }
        }
        return redirect()->route('kelas.spesial')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function tambahPaketSelect2(Request $r)
    {
        $existingRecord = DB::table($r->database . '_cabut')->where($r->database, $r->ket)->first();

        if (!$existingRecord) {
            // Jika $r->ket belum ada dalam tabel, maka lakukan insert.
            $lastId = DB::table($r->database . '_cabut')->insertGetId([
                $r->database => $r->ket
            ]);
            return json_encode([
                'id' => $lastId,
                'teks' => $r->ket
            ]);
        }
    }
    public function eo(Request $r)
    {
        $data = [
            'title' => 'Kelas Spesial',
            'paket' => DB::table('paket_cabut')->get(),
            'tipe' => DB::table('tipe_cabut')->get(),
            'datas' => DB::table('tb_kelas')->where([['id_kategori', 3], ['nonaktif', 'T']])->orderBy('id_kategori', 'ASC')->get()
        ];
        return view('data_master.kelas.eo_index', $data);
    }
    public function eoCreate(Request $r)
    {
        $buang = [
            'rupiah',
            'rupiah_tambah'
        ];
        foreach ($buang as $d) {
            $r->$d = str()->remove(',', $r->$d);
        }

        if (!empty($r->rupiah_tambah[0])) {
            for ($i = 0; $i < count($r->rupiah_tambah); $i++) {
                DB::table('tb_kelas')->insert([
                    // 'id_paket' => $r->id_paket_tambah[$i],
                    'kelas' => $r->kelas_tambah[$i],
                    'id_tipe_brg' => $r->id_tipe_brg_tambah[$i],
                    'rupiah' => $r->rupiah_tambah[$i],
                    'jenis' => 2,
                    'id_kategori' => 3,
                ]);
            }
        }

        if (!empty($r->rupiah[0])) {
            for ($i = 0; $i < count($r->rupiah); $i++) {
                DB::table('tb_kelas')->where('id_kelas', $r->id_kelas[$i])->update([
                    // 'id_paket' => $r->id_paket[$i],
                    'kelas' => $r->kelas[$i],
                    'id_tipe_brg' => $r->id_tipe_brg[$i],
                    'rupiah' => $r->rupiah[$i],
                    'jenis' => 2,
                    'id_kategori' => 3,
                ]);
            }
        }
        return redirect()->route('kelas.eo')->with('sukses', 'Data Berhasil ditambahkan');
    }
    public function getTipe(Request $r)
    {
        return response()->json($r->database == 'paket' ? DB::table('paket_cabut')->get() : DB::table('tipe_cabut')->get());
    }

    function cetak(Request $r)
    {
        $data = [
            'title' => 'Data Paket Cetak',
            'kelas' => DB::table('kelas_cetak')->get(),
            'kategori' => DB::table('paket_cabut')->get(),
            'tipe' => DB::table('tipe_cabut')->get()
        ];
        return view("data_master.kelas.cetak", $data);
    }

    function cetakCreate(Request $r)
    {
        $buang = [
            'rupiah',
            'rupiah_tambah',
            'denda_hcr',
            'denda_susut',
            'rp_gaji'
        ];
        foreach ($buang as $d) {
            $r->$d = str()->remove(',', $r->$d);
        }

        for ($x = 0; $x < count($r->id_paket_tambah); $x++) {
            $data = [
                'id_paket' => $r->id_paket_tambah[$x],
                'kelas' => $r->kelas_tambah[$x],
                'tipe' => $r->id_tipe_brg_tambah[$x],
                'tipe' => $r->id_tipe_brg_tambah[$x],
                'rp_pcs' => $r->rupiah_tambah[$x],
                'denda_hcr' => $r->denda_hcr[$x],
                'batas_susut' => $r->batas_susut[$x],
                'denda_susut' => $r->denda_susut[$x],
                'rp_gaji' => $r->rp_gaji[$x],
            ];
            DB::table('kelas_cetak')->insert($data);
        }

        for ($x = 0; $x < count($r->id_kelas_cetak); $x++) {
            # code...
        }

        return redirect()->route('kelas.cetak')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function sortir()
    {
        $data = [
            'kelas' => DB::table('tb_kelas_sortir')->where('nonaktif', 'T')->get(),
            'title' => 'Kelas Sortir',
        ];
        return view('data_master.kelas.sortir', $data);
    }

    public function cetakSortir(Request $r)
    {
        $buang = [
            'gr',
            'gr_tambah',
            'rupiah',
            'rupiah_tambah',
            'denda_susut',
            'denda_susut_tambah',
            'denda',
            'denda_tambah',
        ];
        foreach ($buang as $d) {
            $r->$d = str()->remove(',', $r->$d);
        }

        if (!empty($r->rupiah_tambah[0])) {
            for ($i = 0; $i < count($r->rupiah_tambah); $i++) {
                DB::table('tb_kelas_sortir')->insert([
                    'kelas' => $r->kelas_tambah[$i],
                    'gr' => $r->gr_tambah[$i],
                    'rupiah' => $r->rupiah_tambah[$i],
                    'denda_susut' => $r->denda_susut_tambah[$i],
                    'denda' => $r->denda_tambah[$i],
                ]);
            }
        }

        if (!empty($r->rupiah[0])) {
            for ($i = 0; $i < count($r->rupiah); $i++) {
                DB::table('tb_kelas_sortir')->where('id_kelas', $r->id_kelas[$i])->update([
                    'kelas' => $r->kelas[$i],
                    'gr' => $r->gr[$i],
                    'rupiah' => $r->rupiah[$i],
                    'denda_susut' => $r->denda_susut[$i],
                    'denda' => $r->denda[$i],
                ]);
            }
        }
        return redirect()->route('kelas.sortir')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function grade()
    {
        $data = [
            'title' => 'Grade',
            'grade' => DB::table('tb_grade')->get(),
        ];
        return view('data_master.kelas.grade', $data);
    }
    public function create_grade(Request $r)
    {
        foreach($r->nm_grade as $d){
            DB::table('tb_grade')->insert([
                'nm_grade' => $d,
                'status' => 'bentuk',
                'tipe' => $d,
                'urutan' => 1,
            ]);
        }
        return redirect()->back();
    }
}
