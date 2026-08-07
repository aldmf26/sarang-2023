<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class pcsPatahController extends Controller
{
    private function firstPatahByBox($noBoxes, string $kategori)
    {
        if (empty($noBoxes)) {
            return collect();
        }

        return DB::table('tb_hancuran')
            ->where('kategori', $kategori)
            ->whereIn('no_box', $noBoxes)
            ->get(['no_box', 'pcs'])
            ->groupBy(fn ($row) => (string) $row->no_box)
            ->map(fn ($rows) => $rows->first()->pcs);
    }

    private function totalPatahByBox($noBoxes, array $kategori)
    {
        if (empty($noBoxes)) {
            return collect();
        }

        return DB::table('tb_hancuran')
            ->selectRaw('no_box, SUM(pcs) AS pcs')
            ->whereIn('kategori', $kategori)
            ->whereIn('no_box', $noBoxes)
            ->groupBy('no_box')
            ->pluck('pcs', 'no_box')
            ->mapWithKeys(fn ($pcs, $noBox) => [(string) $noBox => $pcs]);
    }

    private function replacePatah(Request $request, string $kategori): void
    {
        $rows = [];
        for ($i = 0; $i < count($request->no_box); $i++) {
            $rows[] = [
                'no_box' => $request->no_box[$i],
                'pcs' => $request->pcs_pth[$i],
                'kategori' => $kategori,
                'no_invoice' => $request->no_invoice,
            ];
        }

        DB::transaction(function () use ($request, $kategori, $rows) {
            DB::table('tb_hancuran')
                ->where('no_invoice', $request->no_invoice)
                ->where('kategori', $kategori)
                ->delete();

            foreach (array_chunk($rows, 300) as $chunk) {
                DB::table('tb_hancuran')->insert($chunk);
            }
        });
    }

    public function getHancuranCabut(Request $r)
    {
        $formulir = DB::table('formulir_sarang as a')
            ->leftJoin('bk as b', function ($join) {
                $join->on('b.no_box', '=', 'a.no_box')
                    ->where('b.kategori', '=', 'cabut');
            })
            ->leftJoin('cabut as c', 'c.no_box', '=', 'a.no_box')
            ->leftJoin('eo as d', 'd.no_box', '=', 'a.no_box')
            ->where('a.kategori', 'cetak')
            ->where('a.no_invoice', $r->no_invoice)
            ->selectRaw('a.id_formulir,a.sst_aktual,b.nm_partai, a.no_box, a.pcs_awal, a.gr_awal, b.tipe, b.ket, c.pcs_awal as pcs_cbt, c.gr_awal as gr_cbt, d.gr_eo_awal as gr_eo, c.ket_hcr')
            ->get();

        $patahCabut = $this->firstPatahByBox($formulir->pluck('no_box')->all(), 'cabut');
        $formulir->each(function ($row) use ($patahCabut) {
            $row->pcs_pth_cabut = $patahCabut->get((string) $row->no_box, 0);
        });

        $ket_formulir = DB::selectOne("SELECT a.tanggal,  b.name, c.name as penerima, d.nm_partai
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
        WHERE no_invoice = '$r->no_invoice' and a.kategori = 'cetak'");

        $data = [
            'title' => 'Gudang Sarang',
            'formulir' => $formulir,
            'no_invoice' => $r->no_invoice,
            'ket_formulir' => $ket_formulir
        ];
        return view('home.gudang_sarang/get_hancuran/cabut', $data);
    }
    public function savePthCabut(Request $r)
    {
        $this->replacePatah($r, 'cetak');

        return redirect()->route('gudangsarang.invoice')->with('sukses', 'Data berhasil disimpan');
    }

    public function getHancuranCetak(Request $r)
    {
        $halaman = DB::select("SELECT a.sst_aktual,a.id_pemberi, b.name, a.id_penerima, pemberi.name AS pemberi
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_penerima
        left join users as pemberi on pemberi.id = a.id_pemberi
        where a.no_invoice = ? and a.kategori = 'sortir'
        group by a.id_penerima
        ", [$r->no_invoice]);

        $detail = DB::table('formulir_sarang as a')
            ->leftJoin('bk as b', function ($join) {
                $join->on('b.no_box', '=', 'a.no_box')
                    ->where('b.kategori', '=', 'cabut');
            })
            ->leftJoin('cetak_new as c', 'c.no_box', '=', 'a.no_box')
            ->where('a.no_invoice', $r->no_invoice)
            ->where('a.kategori', 'sortir')
            ->selectRaw('a.id_formulir,a.id_penerima,a.sst_aktual,b.nm_partai, b.tipe, b.ket, a.no_box, a.pcs_awal, a.gr_awal,c.pcs_awal_ctk as pcs_cbt, c.gr_awal_ctk as gr_cbt')
            ->get();

        $noBoxes = $detail->pluck('no_box')->all();
        $patahCetak = $this->firstPatahByBox($noBoxes, 'cetak');
        $patahSortir = $this->firstPatahByBox($noBoxes, 'sortir');
        $detail->each(function ($row) use ($patahCetak, $patahSortir) {
            $row->pcs_pth_cabut = $patahCetak->get((string) $row->no_box, 0);
            $row->pcs_pth_cetak = $patahSortir->get((string) $row->no_box, 0);
        });

        $data = [
            'title' => 'Formulir Cetak Print',
            'halaman' => $halaman,
            'detailByPenerima' => $detail
                ->groupBy(fn ($row) => (string) $row->id_penerima)
                ->map(fn ($rows) => $rows->values()->all()),
            'no_invoice' => $r->no_invoice
        ];

        return view('home.gudang_sarang/get_hancuran/cetak', $data);
    }
    public function savePthCetak(Request $r)
    {
        $this->replacePatah($r, 'sortir');

        return redirect()->route('po.sortir')->with('sukses', 'Data berhasil disimpan');
    }

    public function getHancuranSortir(Request $r)
    {
        $formulir = DB::table('formulir_sarang as a')
            ->where([['a.no_invoice', $r->no_invoice], ['b.kategori', 'cabut'], ['a.kategori', 'grade']])
            ->join('bk as b', 'a.no_box', '=', 'b.no_box')
            ->leftJoin('sortir as c', 'a.no_box', '=', 'c.no_box')
            ->groupBy('a.no_box', 'a.kategori')
            ->selectRaw('a.sst_aktual,a.id_formulir,a.id_pemberi,a.id_penerima,a.tanggal,b.nm_partai,b.ket,b.tipe,a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(c.pcs_awal) as pcs_srt, sum(c.gr_awal) as gr_srt')
            ->get();

        $noBoxes = $formulir->pluck('no_box')->all();
        $patahSebelumnya = $this->totalPatahByBox($noBoxes, ['cetak', 'sortir']);
        $patahGrade = $this->firstPatahByBox($noBoxes, 'grade');
        $formulir->each(function ($row) use ($patahSebelumnya, $patahGrade) {
            $row->pcs_pth_sebelumnya = $patahSebelumnya->get((string) $row->no_box, 0);
            $row->pcs_pth_grade = $patahGrade->get((string) $row->no_box, 0);
        });

        $ket_formulir = DB::selectOne("SELECT  a.tanggal,b.name, c.name  as penerima,a.no_invoice
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        WHERE a.no_invoice = '$r->no_invoice' and a.kategori = 'grade'");
        $data = [
            'title' => 'Po Grading',
            'formulir' => $formulir,
            'no_invoice' => $r->no_invoice,
            'ket_formulir' => $ket_formulir
        ];
        return view('home.gudang_sarang/get_hancuran/grade', $data);
    }

    public function savePthSortir(Request $r)
    {
        $this->replacePatah($r, 'grade');

        return redirect()->route('gudangsarang.invoice_grade', ['kategori' => 'grade'])->with('sukses', 'Data berhasil disimpan');
    }

    public function getHancuranGrading(Request $r)
    {
        $formulir = DB::select("SELECT c.pgws,a.tanggal,b.nm_partai,b.ket,b.tipe,a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
        FROM formulir_sarang as a 
        join bk as b on b.no_box = a.no_box
        join (
            select a.no_box,b.name as pgws from formulir_sarang as a
            join users as b on a.id_pemberi = b.id
            where a.kategori = 'grade'
        ) as c on c.no_box = a.no_box
        where a.no_invoice = '$r->no_invoice' and b.kategori = 'cabut' and a.kategori = 'grading'
        group by a.no_box, a.kategori;");

        $noBoxes = collect($formulir)->pluck('no_box')->all();
        $patahSebelumnya = $this->totalPatahByBox($noBoxes, ['cetak', 'sortir', 'grade']);
        $patahGrading = $this->firstPatahByBox($noBoxes, 'grading');
        foreach ($formulir as $row) {
            $row->pcs_pth_sebelumnya = $patahSebelumnya->get((string) $row->no_box, 0);
            $row->pcs_pth_grading = $patahGrading->get((string) $row->no_box, 0);
        }

        $data = [
            'title' => 'Po Grading',
            'formulir' => $formulir,
            'no_invoice' => $r->no_invoice,
        ];

        return view('home.gudang_sarang/get_hancuran/grading', $data);
    }

    public function savePthGrading(Request $r)
    {
        $this->replacePatah($r, 'grading');

        return redirect()->route('gudangsarang.invoice_grading', ['kategori' => 'grade'])->with('sukses', 'Data berhasil disimpan');
    }
}
