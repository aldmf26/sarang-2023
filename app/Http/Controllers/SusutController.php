<?php

namespace App\Http\Controllers;

use App\Models\Susut;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SusutController extends Controller
{
    public function index()
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
        $title = 'Cek Summary Susut';

        return view('home.susut.index', compact(
            'title',
            'bulan',
            'tahun',
            'cabutKeCetak',
            'cetakKeSortir',
            'ttlSusutCbt',
            'ttlSusutCetak',
            'ttlSusutSortir',
            'sortirKeGrading'
        ));
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
            'pcs_awal' => $r->pcs_awal,
            'gr_awal' => $r->gr_awal,
            'divisi' => $r->divisi,
            'gr_akhir' => $r->gr_akhir,
            'sst_program' => $r->sst_program,
            'admin' => auth()->user()->name,
            'tgl' => date('Y-m-d')
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
            ->where('tgl', date('Y-m-d'))
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

    public function print($id_penerima)
    {
        $susut = Susut::with('pemberi')->orderBy('tgl', 'desc')->where('id_pemberi', $id_penerima)->get();
        $title = 'Cek Detail Susut';
        $penerima = 'Sinta';
        return view('home.susut.print', compact('susut', 'title', 'penerima'));
    }
}
