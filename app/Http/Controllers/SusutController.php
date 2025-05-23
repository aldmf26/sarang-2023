<?php

namespace App\Http\Controllers;

use App\Models\Susut;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SusutController extends Controller
{
    public function index_lama(Request $r)
    {
        $bulan = $r->bulan ?? date('m');
        $tahun = $r->tahun ?? date('Y');

        $cabutKeCetak = Susut::getSum('cabut');
        $cetakKeSortir = Susut::getSum('cetak');
        $sortirKeGrading = Susut::getSum('sortir');

        $divisiList = ['cabut', 'cetak', 'sortir'];
        $ttlSusut = [];

        foreach ($divisiList as $divisi) {
            $ttlSusut[$divisi] = Susut::selectRaw('sum(rambangan_1 + rambangan_2 + rambangan_3 + sapuan_lantai + sesetan + bulu + pasir + rontokan_bk) as ttl_sst_aktual')
                ->where('divisi', $divisi)->first()->ttl_sst_aktual;
        }

        list($ttlSusutCbt, $ttlSusutCetak, $ttlSusutSortir) = array_values($ttlSusut);

        // dimulai dari ini baru
        $data = [
            'title' => 'Cek Summary Susut',
            'bulan' => $r->bulan ?? date('m'),
            'tahun' => $r->tahun ?? date('Y'),
            'divisi' => $r->divisi ?? 'cabut',
            'cabutKeCetak' => Susut::getSum('cabut'),
            'cetakKeSortir' => Susut::getSum('cetak'),
            'ttlSusutCbt' => $ttlSusutCbt,
            'ttlSusutCetak' => $ttlSusutCetak,
            'ttlSusutSortir' => $ttlSusutSortir,
            'sortirKeGrading' => Susut::getSum('sortir'),
        ];

        return view('home.susut.index', $data);
    }

    public function index(Request $r)
    {
        $bulan = $r->bulan ?? date('m');
        $tahun = $r->tahun ?? date('Y');
        $divisi = $r->divisi ?? 'cetak';

        $pgwsBjm = DB::table('users')->get();

        // Array untuk menyimpan data susut untuk setiap pegawai
        $susutData = collect([]);
        $hasil = null;
        foreach ($pgwsBjm as $pegawai) {
            if ($divisi == 'eo') {
                // Query untuk mendapatkan total gr_awal dan gr_akhir untuk setiap pegawai
                $hasil = DB::table('eo as a')
                    ->join('formulir_sarang as b', 'a.no_box', '=', 'b.no_box')
                    ->where('b.kategori', 'cabut')
                    ->where('a.bulan_dibayar', $bulan)
                    ->where('a.id_pengawas', $pegawai->id) // Asumsikan ada id_pengawas di tabel cabut
                    ->select(
                        'a.id_pengawas',
                        DB::raw('SUM(a.gr_eo_awal) as gr_awal'),
                        DB::raw('SUM(a.gr_eo_akhir) as gr_akhir'),
                        DB::raw('SUM(a.gr_eo_awal - a.gr_eo_akhir) as sst_program'),
                    )
                    ->first();
            }

            if ($divisi == 'cetak') {
                // Query untuk mendapatkan total gr_awal dan gr_akhir untuk setiap pegawai
                $hasil = DB::table('cabut as a')
                    ->join('formulir_sarang as b', 'a.no_box', '=', 'b.no_box')
                    ->where('b.kategori', $divisi)
                    ->where('a.bulan_dibayar', $bulan)
                    ->where('a.id_pengawas', $pegawai->id) // Asumsikan ada id_pengawas di tabel cabut
                    ->select(
                        'a.id_pengawas',
                        DB::raw('SUM(a.gr_awal) as gr_awal'),
                        DB::raw('SUM(a.gr_akhir) as gr_akhir'),
                        DB::raw('SUM(a.gr_awal - a.gr_akhir) as sst_program'),
                    )
                    ->first();
            }
            if ($divisi == 'sortir') {
                $hasil = DB::table('cetak_new as a')
                    ->join('formulir_sarang as b', 'a.no_box', '=', 'b.no_box')
                    ->where('b.kategori', $divisi)
                    ->where('a.bulan_dibayar', $bulan)
                    ->where('a.id_pengawas', $pegawai->id) // Asumsikan ada id_pengawas di tabel cabut
                    ->select(
                        'a.id_pengawas',
                        DB::raw('SUM(a.gr_awal_ctk) as gr_awal'),
                        DB::raw('SUM(a.gr_akhir) as gr_akhir'),
                        DB::raw('SUM(a.gr_awal_ctk - a.gr_akhir) as sst_program'),
                    )
                    ->first();
            }
            if ($divisi == 'grade') {
                $hasil = DB::table('sortir as a')
                    ->join('formulir_sarang as b', 'a.no_box', '=', 'b.no_box')
                    ->where('b.kategori', $divisi)
                    ->where('a.bulan', $bulan)
                    ->where('a.id_pengawas', $pegawai->id) // Asumsikan ada id_pengawas di tabel cabut
                    ->select(
                        'a.id_pengawas',
                        DB::raw('SUM(a.gr_awal) as gr_awal'),
                        DB::raw('SUM(a.gr_akhir) as gr_akhir'),
                        DB::raw('SUM(a.gr_awal - a.gr_akhir) as sst_program'),
                    )
                    ->first();
            }

            if ($hasil->gr_awal > 0) {
                $susutData[$pegawai->name] = $hasil;
            }
        }

        // dimulai dari ini baru
        $data = [
            'title' => 'Cek Summary Susut',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'divisi' => $divisi,
            'susutData' => $susutData,
        ];

        return view('home.susut.index', $data);
    }

    public function store(Request $r)
    {
        $bulan = $r->bulan ?? date('m');
        $divisi = $r->divisi ?? 'cabut';
        DB::table('tb_susut')->where('divisi', $divisi)->where('bulan_dibayar', $bulan)->delete();

        $ttlAktual = [];
        foreach ($r->id_pemberi as $i => $id_pemberi) {
            $ttlAktual[$id_pemberi] = array_sum([
                $r->rambangan_1[$i] ?? 0,
                $r->rambangan_2[$i] ?? 0,
                $r->rambangan_3[$i] ?? 0,
                $r->sapuan_lantai[$i] ?? 0,
                $r->sesetan[$i] ?? 0,
                $r->bulu[$i] ?? 0,
                $r->pasir[$i] ?? 0,
                $r->rontokan_bk[$i] ?? 0,
                $r->flx[$i] ?? 0,
            ]);
        }

        foreach ($r->id_pemberi as $i => $id_pemberi) {
            if (isset($ttlAktual[$id_pemberi]) && $ttlAktual[$id_pemberi] > 0) {
                $data = [
                    'id_pemberi' => $id_pemberi,
                    'id_penerima' => 265,
                    'rambangan_1' => $r->rambangan_1[$i] ?? 0,
                    'rambangan_2' => $r->rambangan_2[$i] ?? 0,
                    'rambangan_3' => $r->rambangan_3[$i] ?? 0,
                    'sapuan_lantai' => $r->sapuan_lantai[$i] ?? 0,
                    'sesetan' => $r->sesetan[$i] ?? 0,
                    'bulu' => $r->bulu[$i] ?? 0,
                    'pasir' => $r->pasir[$i] ?? 0,
                    'rontokan_bk' => $r->rontokan_bk[$i] ?? 0,
                    'flx' => $r->flx[$i] ?? 0,
                    'tgl' => date('Y-m-d'),
                    'divisi' => $divisi,
                    'bulan_dibayar' => $bulan,
                    'ttl_aktual' => $ttlAktual[$id_pemberi],
                    'admin' => auth()->user()->name,
                ];

                DB::table('tb_susut')->insert($data);
            }
        }

        return redirect()->route('susut.index', [
            'divisi' => $divisi,
            'bulan' => $bulan,
        ])->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function detailSusut()
    {
        return $detailSusut = collect([
            'Rambangan 1',
            'Rambangan 2',
            'Rambangan 3',
            'Sapuan lantai',
            'Sesetan',
            'Bulu',
            'Pasir',
            'Rontokn bk',
        ]);
    }
    public function detail(Request $r)
    {
        // Mengambil definisi detail susut
        $detailSusut = collect([
            'Rambangan 1',
            'Rambangan 2',
            'Rambangan 3',
            'Sapuan lantai',
            'Sesetan',
            'Bulu',
            'Pasir',
            'Rontokn bk',
        ]);

        $datas = $r->all();
        $id_pengawas = $r->id_pengawas;
        $nama = User::find($id_pengawas)->name;
        $title = 'Cek Detail Susut';
        $detailSusut = $this->detailSusut();

        // Mencari data susut terakhir untuk pengawas ini
        $lastSusut = DB::table('tb_susut')
            ->where('id_pemberi', $id_pengawas)
            ->orderBy('tgl', 'desc')
            ->first();

        // Menyiapkan nilai default untuk setiap detail
        $defaultValues = [];
        if ($lastSusut) {
            $defaultValues = [
                $lastSusut->rambangan_1,
                $lastSusut->rambangan_2,
                $lastSusut->rambangan_3,
                $lastSusut->sapuan_lantai,
                $lastSusut->sesetan,
                $lastSusut->bulu,
                $lastSusut->pasir,
                $lastSusut->rontokan_bk
            ];
        } else {
            // Jika tidak ada data sebelumnya, isi dengan 0
            $defaultValues = array_fill(0, count($detailSusut), 0);
        }

        return view('home.susut.detail', compact(
            'title',
            'nama',
            'id_pengawas',
            'detailSusut',
            'lastSusut',
            'datas',
            'defaultValues'
        ));
    }
    public function createAktualSusut(Request $r)
    {
        // ID penerima tetap (SINTA)
        $idPenerima = 265;
        $tgl = $r->tgl;
        // Mapping indeks input ke nama kolom database
        $kolumSusut = [
            'rambangan_1',
            'rambangan_2',
            'rambangan_3',
            'sapuan_lantai',
            'sesetan',
            'bulu',
            'pasir',
            'rontokan_bk'
        ];
        // Menyiapkan data dasar
        $dataSusut = [
            'id_pemberi' => $r->id_pengawas,
            'id_penerima' => $idPenerima,
            'pcs_awal' => $r->pcs_awal ?? 0,
            'gr_awal' => $r->gr_awal,
            'divisi' => $r->divisi,
            'gr_akhir' => $r->gr_akhir,
            'sst_program' => $r->sst_program,
            'ttl_aktual' => array_sum($r->detailSusut),
            'admin' => auth()->user()->name,
            'tgl' => $tgl
        ];

        // Menambahkan detail susut ke data yang akan disimpan
        foreach ($r->detailSusut as $index => $nilai) {
            if (isset($kolumSusut[$index])) {
                $dataSusut[$kolumSusut[$index]] = $nilai;
            }
        }

        // Cek apakah data untuk hari ini dan id_pemberi yang sama sudah ada
        $dataExisting = DB::table('tb_susut')
            ->where('id_pemberi', $r->id_pengawas)
            ->where('tgl',  $tgl)
            ->first();

        if ($dataExisting) {
            // Update data yang sudah ada untuk mencegah duplikasi
            DB::table('tb_susut')
                ->where('id', $dataExisting->id)
                ->update($dataSusut);

            return redirect()->back()->with('success', 'Data susut berhasil diperbarui');
        } else {
            // Simpan data baru karena belum ada data dengan id_pemberi dan tanggal yang sama
            DB::table('tb_susut')->insert($dataSusut);

            return redirect()->back()->with('success', 'Data susut berhasil disimpan');
        }

        return redirect()->route('susut.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function print($id_penerima, $divisi)
    {
        $susut = Susut::with('pemberi')->orderBy('tgl', 'desc')->where([['id_pemberi', $id_penerima], ['divisi', $divisi]])->first();
        $title = 'Cek Detail Susut';
        $penerima = 'Sinta';
        return view('home.susut.print', compact('susut', 'title', 'penerima'));
    }
}
