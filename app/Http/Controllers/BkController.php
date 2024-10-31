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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpParser\Node\Stmt\TryCatch;

class BkController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $kategori = $r->kategori ?? 'cabut';


        $id_user = auth()->user()->id;
        $where = "a.tgl between ? and ? and a.kategori LIKE ? and a.selesai = 'T' ";
        $params = [$tgl1, $tgl2, "%$kategori%"];
        if (!in_array(auth()->user()->posisi_id, [1, 12])) {
            $where .= " and a.penerima = ?";
            $params[] = $id_user;
        }

        $bk = DB::select("SELECT a.tgl_input,a.pgws_grade, a.susut, a.nm_partai,a.id_bk,a.selesai,a.no_lot,a.no_box,a.tipe,a.ket,a.warna,a.tgl,a.pengawas,a.penerima,a.pcs_awal,a.gr_awal,d.name FROM bk as a 
        left join users as d on d.id = a.penerima 
        WHERE $where ORDER BY a.id_bk DESC", $params);

        $data = [
            'title' => 'Divisi BK',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'kategori' => $kategori,
            'bk' => $bk,
        ];
        return view('home.bk.index', $data);
    }

    public function add(Request $r)
    {
        // $response = Http::get("https://jurnals.ptagafood.com/api/apibk");
        // $gudang = $response['data']['gudang'];
        // $gudangBk = json_decode(json_encode($gudang));

        $data = [
            'title' => 'Tambah Divisi BK',
            'pengawas' => User::where('posisi_id', 13)->get(),
            'noBoxTerakhir' => DB::table('bk')->where('kategori', $r->kategori)->orderBy('id_bk', 'DESC')->first()->no_box ?? 5000,
            'kategori' => $r->kategori,
            'id_pengawas' => auth()->user()->id
            // 'gudangBk' => $gudangBk
        ];
        if ($r->kategori == 'cetak') {
            return view('home.bk.ambil_cetak', $data);
        }
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

    public function getNoBoxTambah()
    {
        $cekBox = DB::selectOne("SELECT CAST(no_box AS UNSIGNED) as no_box FROM `bk` WHERE kategori like '%cabut%' and baru = 'baru' ORDER BY CAST(no_box AS UNSIGNED) DESC LIMIT 1;");
        $nobox = isset($cekBox->no_box) ? $cekBox->no_box + 1 : 1001;
        return $nobox;
    }

    public function create(Request $r)
    {
        DB::beginTransaction();
        try {
            for ($x = 0; $x < count($r->pcs_awal); $x++) {
                if (!empty($r->pcs_awal[$x]) || !empty($r->gr_awal[$x])) {
                    $pcs_awal = str()->remove(' ', $r->pcs_awal[$x]);
                    $gr_awal = str()->remove(' ', $r->gr_awal[$x]);
                    // $nobox = $r->no_box[$x];
                    $nobox = $this->getNoBoxTambah();

                    // $selectedValue = $r->no_lot[$x];
                    // list($noLot, $ket) = explode('-', $selectedValue);

                    $data = [
                        // 'no_lot' => $selectedValue,
                        'nm_partai' => $r->nm_partai[$x],
                        'no_box' => $nobox,
                        'tipe' => $r->tipe[$x],
                        'ket' => $r->ket[$x],
                        'warna' => $r->warna[$x],
                        'pengawas' => $r->pgws[$x],
                        'penerima' => $r->nama[$x],
                        'pcs_awal' => $pcs_awal,
                        'gr_awal' => $gr_awal,
                        'tgl' => $r->tgl_terima[$x],
                        'pgws_grade' => $r->pgws_grade[$x],
                        'kategori' => $r->kategori,
                        'tgl_input' => date('Y-m-d'),
                    ];
                    // if ($cekBox) {
                    //     return redirect("home/bk?kategori=$r->kategori")->with('error', "No box : $nobox SUDAH ADA DI BK CABUT");
                    // } else {
                    // }
                    DB::table('bk')->insert($data);
                }
            }
            session()->put('id_user', auth()->user()->id);
            session()->put('waktu', date('Y-m-d'));

            DB::commit();
            return redirect("home/bk?kategori=$r->kategori")->with('sukses', 'Data berhasil ditambahkan');
        } catch (\Exception  $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function template()
    {
        $tbl = DB::select("SELECT * FROM users as a where a.posisi_id = '13'");
        $totalrow = count($tbl) + 1;
        return Excel::download(new BkTemplateExport($tbl, $totalrow), 'Export Template BK.xlsx');
    }

    public function import(Request $r)
    {
        if ($r->kategori == 'sortir') {
            $this->importSortir($r);
            return redirect()->route('bk.index', ['kategori' => 'sortir'])->with('sukses', 'Data berhasil import');
        } else {

            $file = $r->file('file');
            $spreadsheet = IOFactory::load($file);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            DB::beginTransaction();
            try {
                foreach (array_slice($sheetData, 1) as $row) {
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $partai = $row[0];
                    $partai = $row[0];
                    $tgl = $row[6];

                    // $cekBox = DB::table('bk')->where([['kategori', 'LIKE', '%cabut%'], ['no_box', $nobox]])->first();

                    if (
                        // $cekBox || 
                        empty($row[0]) ||
                        empty($row[7]) ||
                        empty($row[8])
                        // empty($row[9]) ||
                        // empty($row[10])
                    ) {
                        $pesan = [
                            // empty($row[0]) => "NO LOT TIDAK BOLEH KOSONG",
                            empty($row[0]) => "NAMA PARTAI TIDAK BOLEH KOSONG",
                            // empty($row[6]) => "PENGAWAS TIDAK BOLEH KOSONG",
                            empty($row[7]) => "GR TIDAK BOLEH KOSONG",
                            empty($row[8]) => "KATEGORI TIDAK BOLEH KOSONG",
                            // $cekBox ? "NO BOX : $nobox SUDAH ADA" : false,
                        ];
                        DB::rollBack();
                        return redirect()->route('bk.index')->with('error', "ERROR! " . $pesan[true]);
                    } else {
                        if (is_numeric($tgl)) {
                            // Jika nilai berupa angka, konversi ke format tanggal
                            $tanggalExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                            $tanggalFormatted = $tanggalExcel->format('Y-m-d');
                        } else {
                            // Jika nilai sudah dalam format tanggal, pastikan formatnya adalah 'Y-m-d'
                            $tanggalFormatted = date('Y-m-d', strtotime($tgl));
                        }
                        $nobox = $this->getNoBoxTambah();
                        // $nobox = $row[9];

                        DB::table('bk')->insert([
                            'no_lot' => '0',
                            'nm_partai' => $row[0],
                            'no_box' => $nobox,
                            'tipe' => $row[1],
                            'ket' => $row[2],
                            'warna' => $row[3],
                            'tgl' => date('Y-m-d'),
                            'pengawas' => 'sinta',
                            // 'penerima' => $row[4],
                            'pgws_grade' => $row[4],
                            'pcs_awal' => $row[5],
                            'gr_awal' => $row[6],
                            'kategori' => 'cabut',
                        ]);
                    }
                }
                DB::commit();
                return redirect()->route('bkbaru.index')->with('sukses', 'Data berhasil import');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
    }

    public function importSortir($r)
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

                DB::table('bk')->insert([
                    'no_lot' => '0',
                    'nm_partai' => $row[0],
                    'no_box' => $row[1],
                    'tipe' => $row[2],
                    'ket' => $row[3],
                    'warna' => $row[4],
                    'tgl' => date('Y-m-d'),
                    'pengawas' => 'siti fatimah',
                    'penerima' => $row['7'],
                    'pcs_awal' => $row[5],
                    'gr_awal' => $row[6],
                    'kategori' => 'sortir',
                ]);
            }
            DB::commit();
            return redirect()->route('bk.index', ['kategori' => 'sortir'])->with('sukses', 'Data berhasil import');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
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
        for ($x = 0; $x < count($r->nm_partai); $x++) {
            if (!empty($r->no_box[$x])) {
                $data = [
                    'nm_partai' => $r->nm_partai[$x],
                    'no_box' => $r->no_box[$x],
                    'tipe' => $r->tipe[$x],
                    'ket' => $r->ket[$x],
                    'warna' => $r->warna[$x],
                    'pengawas' => $r->pgws[$x],
                    'penerima' => $r->nama[$x],
                    'pcs_awal' => $r->pcs_awal[$x],
                    'gr_awal' => $r->gr_awal[$x],
                    'tgl' => $r->tgl_terima[$x],
                    'susut' => $r->susut[$x]
                ];
                DB::table('bk')->where('id_bk', $r->id_bk[$x])->update($data);
            }
        }
        return redirect("home/bk?kategori=$r->kategori")->with('sukses', 'Data berhasil ditambahkan');
    }

    public function delete(Request $r)
    {
        for ($i = 0; $i < count($r->no_nota); $i++) {
            DB::table('bk')->where('id_bk', $r->no_nota[$i])->delete();
        }

        return redirect("home/bk?kategori=$r->kategori")->with('sukses', 'Data berhasil dihapus');
    }

    public function selesai(Request $r)
    {
        for ($i = 0; $i < count($r->no_nota); $i++) {
            DB::table('bk')->where('id_bk', $r->no_nota[$i])->update(['selesai' => 'Y']);
        }

        return redirect("home/bk?kategori=$r->kategori")->with('sukses', 'Data berhasil diselesaikan');
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

    public function create_ambil_cetak(Request $r)
    {
        try {
            DB::beginTransaction();
            $datas = [];
            for ($x = 0; $x < count($r->gr_akhir); $x++) {
                $datas[] = [
                    'nm_partai' => $r->partai_h[$x],
                    'no_box' => $r->no_box[$x],
                    'tipe' => $r->tipe[$x],
                    'pengawas' => $r->admin,
                    'penerima' => $r->penerima,
                    'pcs_awal' => $r->pcs_akhir[$x],
                    'gr_awal' => $r->gr_akhir[$x],
                    'tgl' => $r->tgl,
                    'kategori' => 'cetak',
                    'pengawas' => $r->admin,
                    'ttl_rp' => $r->ttl_rp[$x]
                ];
            }
            DB::table('bk')->insert($datas);
            DB::commit();
            return redirect("home/bk?kategori=cetak")->with('sukses', 'Data berhasil ditambahkan');
        } catch (\Exception  $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
