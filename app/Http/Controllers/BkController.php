<?php

namespace App\Http\Controllers;

use App\Exports\BkExport;
use App\Exports\BkTemplateExport;
use App\Imports\BkImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;


class BkController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        if (empty($r->kategori)) {
            $kategori = 'cabut';
        } else {
            $kategori = $r->kategori;
        }
        $data = [
            'title' => 'Divisi BK',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'kategori' => $kategori,
            'bk' => DB::select("SELECT a.no_lot,a.no_box,a.tipe,a.ket,a.warna,a.tgl,a.pengawas,a.penerima,a.pcs_awal,a.gr_awal,d.name FROM bk as a 
            left join users as d on d.id = a.penerima 
            WHERE a.kategori LIKE '%$kategori%' ORDER BY a.id_bk DESC"),

        ];
        return view('home.bk.index', $data);
    }

    public function add(Request $r)
    {
        $response = Http::get("https://jurnals.ptagafood.com/api/apibk");
        $gudang = $response['data']['gudang'];
        $gudangBk = json_decode(json_encode($gudang));

        $data = [
            'title' => 'Tambah Divisi BK',
            'pengawas' => User::where('posisi_id', 13)->get(),
            'noBoxTerakhir' => DB::table('bk')->where('kategori', $r->kategori)->orderBy('id_bk', 'DESC')->first()->no_box ?? 5000,
            'kategori' => $r->kategori,
            'gudangBk' => $gudangBk
        ];
        return view('home.bk.create', $data);
    }
    public function load_select(Request $r)
    {
        $elemen = $r->elemen;
        $count = $r->count;

        // Mendapatkan data dari database
        $tipe = DB::table('tipe_cabut')->get();
        $ket_bk = DB::table('ket_bk')->get();
        $warna = DB::table('warna')->get();

        // Inisialisasi variabel $data
        $data = [];

        // Menentukan data berdasarkan elemen yang dipilih
        if ($elemen === 'tipe') {
            $data = $tipe;
        } elseif ($elemen === 'ket') {
            $data = $ket_bk;
        } elseif ($elemen === 'warna') {
            $data = $warna;
        }

        // Membangun opsi select berdasarkan data
        $tdContent = '<td><select name="' . $elemen . '[]" id="" pilihan="' . $elemen . '" count="' . $count . '" class="selectTipe select2-tipe">';
        foreach ($data as $item) {
            if ($elemen === 'tipe') {
                $tdContent .= '<option value="' . $item->id_tipe . '">' . strtoupper($item->tipe) . '</option>';
            } elseif ($elemen === 'ket') {
                $tdContent .= '<option value="' . $item->id_ket_bk . '">' . strtoupper($item->ket_bk) . '</option>';
            } elseif ($elemen === 'warna') {
                $tdContent .= '<option value="' . $item->id_warna . '">' . strtoupper($item->nm_warna) . '</option>';
            }
        }

        // Tambahkan opsi "Tambah Baru" di akhir setiap select
        $tdContent .= '<option value="tambah">+ Baru</option>';
        $tdContent .= '</select></td>';

        return $tdContent;
    }

    public function create_select(Request $r)
    {
        $pilihanArr = [
            'ket' => 'ket_bk',
            'warna' => 'nm_warna',
            'tipe' => 'tipe_cabut',
        ];
        $pilihan = $pilihanArr[$r->pilihan];

        DB::table($pilihan)->insert([
            $pilihan == 'tipe_cabut' ? 'tipe' : $pilihan => $r->ket
        ]);
    }

    public function create(Request $r)
    {
        for ($x = 0; $x < count($r->pcs_awal); $x++) {
            if (!empty($r->pcs_awal[$x]) || !empty($r->gr_awal[$x])) {
                $pcs_awal = str()->remove(' ', $r->pcs_awal[$x]);
                $gr_awal = str()->remove(' ', $r->gr_awal[$x]);

                // $selectedValue = $r->no_lot[$x];
                // list($noLot, $ket) = explode('-', $selectedValue);

                $data = [
                    'no_lot' => $r->no_lot[$x],
                    // 'nm_partai' => $ket,
                    'no_box' => $r->no_box[$x],
                    'tipe' => $r->tipe[$x],
                    'ket' => $r->ket[$x],
                    'warna' => $r->warna[$x],
                    'pengawas' => $r->pgws[$x],
                    'penerima' => $r->nama[$x],
                    'pcs_awal' => $pcs_awal,
                    'gr_awal' => $gr_awal,
                    'tgl' => $r->tgl_terima[$x],
                    'kategori' => $r->kategori
                ];
                DB::table('bk')->insert($data);
            }
        }
        return redirect("home/bk?kategori=$r->kategori")->with('sukses', 'Data berhasil ditambahkan');
    }

    public function template()
    {
        $tbl = DB::select("SELECT * FROM users as a where a.posisi_id = '13'");
        $totalrow = count($tbl) + 1;
        return Excel::download(new BkTemplateExport($tbl, $totalrow), 'Export Template BK.xlsx');
    }

    public function import(Request $r)
    {
        Excel::import(new BkImport, $r->file('file'));
        return redirect()->route('bk.index')->with('sukses', 'Data berhasil import');
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
        $kategori = $r->kategori;
        $tbl = DB::select("SELECT a.nm_partai,a.no_lot,a.no_box,a.tipe,a.ket,a.warna,a.tgl,a.pengawas,a.penerima,a.pcs_awal,a.gr_awal,d.name FROM bk as a 
        left join users as d on d.id = a.penerima 
        WHERE a.kategori LIKE '%$kategori%' ORDER BY a.id_bk DESC");
        $totalrow = count($tbl) + 1;

        return Excel::download(new BkExport($tbl, $totalrow, $view), 'Export BK.xlsx');
    }

    public function edit(Request $r)
    {
        $data = [
            'title' => 'Edit Divisi BK',
            'pengawas' => User::where('posisi_id', 13)->get(),
            'ket_bk' => DB::table('ket_bk')->get(),
            'warna' => DB::table('warna')->get(),
            'no_nota' => $r->no_nota,
            'id_pengawas' => $r->id_pengawas,
            'kategori' => $r->kategori,
        ];
        return view('home.bk.edit', $data);
    }

    public function update(Request $r)
    {
        for ($x = 0; $x < count($r->no_lot); $x++) {
            if (!empty($r->no_lot[$x])) {
                $data = [
                    'no_lot' => $r->no_lot[$x],
                    'no_box' => $r->no_box[$x],
                    'tipe' => $r->tipe[$x],
                    'ket' => $r->ket[$x],
                    'warna' => $r->warna[$x],
                    'pengawas' => $r->pgws[$x],
                    'penerima' => $r->nama[$x],
                    'pcs_awal' => $r->pcs_awal[$x],
                    'gr_awal' => $r->gr_awal[$x],
                    'tgl' => $r->tgl_terima[$x],
                ];
                DB::table('bk')->where('id_bk', $r->id_bk[$x])->update($data);
            }
        }
        return redirect('home/bk')->with('sukses', 'Data berhasil ditambahkan');
    }

    public function delete(Request $r)
    {
        foreach ($r->no_nota as $n) {
            DB::table('bk')->where('no_box', $n)->delete();
        }
        return redirect('home/bk')->with('sukses', 'Data berhasil ditambahkan');
    }


    function cetak(Request $r)
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
            left join warna as c on c.id_warna = a.id_warna 
            left join users as d on d.id = a.penerima 
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.kategori = 'cetak'")
        ];
        return view('home.bk.index', $data);
    }
}
