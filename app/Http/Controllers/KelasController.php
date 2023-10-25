<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nonaktif;

class KelasController extends Controller
{
    public $tipe, $paket;
    public function __construct()
    {
        $this->tipe = DB::table('tipe_cabut')->get();
        $this->paket = DB::table('paket_cabut')->get();
    }
    public function index(Request $r)
    {
        $jenis = empty($r->jenis) ? 1 : 2;
        $data = [
            'title' => 'Data Paket Cabut',
            'jenis' => $jenis,
            'lokasi' => ['alpa', 'mtd', 'sby'],
            'tipe' => $this->tipe,
            'kategori' => DB::table('paket_cabut')->get(),
            'datas' => DB::table('tb_kelas')->where([['jenis', $jenis], ['nonaktif', 'T'], ['id_kategori', '!=', 3]])->orderBy('id_kategori', 'ASC')->get()
        ];
        return view("data_master.kelas.index", $data);
    }

    public function cabutCreate(Request $r)
    {
        $buang = [
            'rupiah', 'pcs', 'gr', 'rp_bonus', 'bonus_susut', 'batas_susut', 'denda_susut_persen', 'eot', 'denda_hcr',
            'rupiah_tambah', 'pcs_tambah', 'gr_tambah', 'rp_bonus_tambah', 'bonus_susut_tambah', 'batas_susut_tambah', 'denda_susut_persen_tambah', 'eot_tambah', 'denda_hcr_tambah'
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
                    'eot' => $r->eot_tambah[$i],
                    'denda_hcr' => $r->denda_hcr_tambah[$i],
                    'id_kategori' => $r->id_kategori_tambah[$i],
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
                    'id_tipe_brg' => $r->id_tipe_brg[$i],
                    'denda_susut_persen' => $r->denda_susut_persen[$i],
                    'batas_susut' => $r->batas_susut[$i],
                    'bonus_susut' => $r->bonus_susut[$i],
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

    public function spesial(Request $r)
    {
        $jenis = empty($r->jenis) ? 1 : 2;

        $data = [
            'title' => 'Kelas Spesial',
            'jenis' => $jenis,
            'paket' => $this->paket,
            'tipe' => $this->tipe,
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
            'paket' => $this->paket,
            'tipe' => $this->tipe,
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
                    'id_paket' => $r->id_paket_tambah[$i],
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
                    'id_paket' => $r->id_paket[$i],
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
        return response()->json($r->database == 'paket' ? $this->paket : $this->tipe);
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
        for ($x = 0; $x < count($r->id_paket); $x++) {
            $data = [
                'id_paket' => $r->id_paket_tambah[$x],
                'kelas' => $r->kelas_tambah[$x],
                'tipe' => $r->id_tipe_brg_tambah[$x],
                'tipe' => $r->id_tipe_brg_tambah[$x],
                'rp_pcs' => $r->rupiah_tambah[$x],
                'denda_hcr' => $r->denda_hcr[$x],
                'batas_susut' => $r->batas_susut[$x],
                'denda_susut' => $r->denda_susut[$x],
            ];
            DB::table('kelas_cetak')->insert($data);
        }
        return redirect()->route('kelas.cetak')->with('sukses', 'Data Berhasil ditambahkan');
    }
}
