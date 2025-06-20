<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class pcsPatahController extends Controller
{
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
        DB::table('tb_hancuran')->where('no_invoice', $r->no_invoice)->where('kategori', 'cetak')->delete();
        for ($i = 0; $i < count($r->no_box); $i++) {
            $data = [
                'no_box' => $r->no_box[$i],
                'pcs' => $r->pcs_pth[$i],
                'kategori' => 'cetak',
                'no_invoice' => $r->no_invoice,
            ];
            DB::table('tb_hancuran')->insert($data);
        }
        return redirect()->route('gudangsarang.invoice')->with('sukses', 'Data berhasil disimpan');
    }

    public function getHancuranCetak(Request $r)
    {
        $halaman = DB::select("SELECT a.sst_aktual,a.id_pemberi, b.name, a.id_penerima
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_penerima
        where a.no_invoice = '$r->no_invoice' and a.kategori = 'sortir'
        group by a.id_penerima
        ");
        $data = [
            'title' => 'Formulir Cetak Print',
            'halaman' => $halaman,
            'no_invoice' => $r->no_invoice
        ];

        return view('home.gudang_sarang/get_hancuran/cetak', $data);
    }
    public function savePthCetak(Request $r)
    {
        DB::table('tb_hancuran')->where('no_invoice', $r->no_invoice)->where('kategori', 'sortir')->delete();
        for ($i = 0; $i < count($r->no_box); $i++) {
            $data = [
                'no_box' => $r->no_box[$i],
                'pcs' => $r->pcs_pth[$i],
                'kategori' => 'sortir',
                'no_invoice' => $r->no_invoice,
            ];
            DB::table('tb_hancuran')->insert($data);
        }
        return redirect()->route('gudangsarang.invoice_sortir', ['kategori' => 'sortir'])->with('sukses', 'Data berhasil disimpan');
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
        DB::table('tb_hancuran')->where('no_invoice', $r->no_invoice)->where('kategori', 'grade')->delete();
        for ($i = 0; $i < count($r->no_box); $i++) {
            $data = [
                'no_box' => $r->no_box[$i],
                'pcs' => $r->pcs_pth[$i],
                'kategori' => 'grade',
                'no_invoice' => $r->no_invoice,
            ];
            DB::table('tb_hancuran')->insert($data);
        }
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

        $data = [
            'title' => 'Po Grading',
            'formulir' => $formulir,
            'no_invoice' => $r->no_invoice,
        ];

        return view('home.gudang_sarang/get_hancuran/grading', $data);
    }

    public function savePthGrading(Request $r)
    {
        DB::table('tb_hancuran')->where('no_invoice', $r->no_invoice)->where('kategori', 'grading')->delete();
        for ($i = 0; $i < count($r->no_box); $i++) {
            $data = [
                'no_box' => $r->no_box[$i],
                'pcs' => $r->pcs_pth[$i],
                'kategori' => 'grading',
                'no_invoice' => $r->no_invoice,
            ];
            DB::table('tb_hancuran')->insert($data);
        }
        return redirect()->route('gudangsarang.invoice_grading', ['kategori' => 'grade'])->with('sukses', 'Data berhasil disimpan');
    }
}
