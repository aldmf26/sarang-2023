<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class GudangSarangController extends Controller
{

    public function home(Request $r)
    {
        $data = [
            'title' => 'Home',
        ];
        return view('home.gudang_sarang.home', $data);
    }
    public function index(Request $r)
    {
        $id_pengawas = auth()->user()->id;
        $data = [
            'title' => 'Gudang Cabut Selesai',
            'cabut' => DB::select("SELECT a.id_cabut, a.no_box, b.nama, a.pcs_akhir, a.gr_akhir, a.selesai
            FROM cabut as a 
            left join tb_anak as b on b.id_anak = a.id_anak
            WHERE  a.formulir = 'T' and a.id_pengawas = '$id_pengawas'
            order by a.selesai DESC , a.id_cabut ASC
            "),
            'pengawas' => DB::table('users')->where('posisi_id', '14')->get()
        ];
        return view('home.gudang_sarang.index', $data);
    }

    public function get_formulir(Request $r)
    {
        $data = [
            'title' => 'Gudang Sarang',
            'id_cabut' => $r->id_cabut
        ];
        return view('home.gudang_sarang.get_formulir', $data);
    }

    public function save_formulir(Request $r)
    {
        $id_cabut = $r->id_cabut;
        $no_invoice = strtoupper(Str::random(5));

        for ($x = 0; $x < count($id_cabut); $x++) {
            $data = [
                'formulir' => 'Y',
                'invoice' => 'FS-' . $no_invoice,

            ];
            DB::table('cabut')->where('id_cabut', $id_cabut[$x])->update($data);

            $cabut = DB::table('cabut')->where('id_cabut', $id_cabut[$x])->first();

            $data = [
                'no_invoice' => 'FS-' . $no_invoice,
                'no_box' => $cabut->no_box,
                'id_pemberi' => $cabut->id_pengawas,
                'id_penerima' => $r->id_pengawas,
                'tanggal' => $r->tgl,
                'pcs_awal' => $cabut->pcs_akhir,
                'gr_awal' => $cabut->gr_akhir,
                'kategori' => 'cetak'
            ];
            DB::table('formulir_sarang')->insert($data);
        }
        return redirect("home/gudangsarang/print_formulir?no_invoice=FS-$no_invoice")->with('sukses', 'Data berhasil ditambahkan');
    }

    public function print_formulir(Request $r)
    {
        $formulir = DB::table('formulir_sarang')->where('no_invoice', $r->no_invoice)->get();
        $ket_formulir = DB::selectOne("SELECT  b.name, c.name as penerima
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        WHERE no_invoice = '$r->no_invoice'");
        $data = [
            'title' => 'Gudang Sarang',
            'formulir' => $formulir,
            'ket_formulir' => $ket_formulir
        ];
        return view('home.gudang_sarang/print_formulir', $data);
    }

    public function invoice(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $formulir = DB::select("SELECT a.id_formulir, a.no_invoice, a.tanggal, b.name as pemberi, c.name as penerima, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
        FROM formulir_sarang as a
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        WHERE a.kategori = 'cetak' and a.tanggal between '$tgl1' and '$tgl2' and a.kategori = 'cetak'
        group by a.no_invoice
        order by a.id_formulir DESC
        ");
        $data = [
            'title' => 'Invoice Awal Cetak',
            'formulir' => $formulir
        ];
        return view('home.gudang_sarang.invoice', $data);
    }

    // bk ke cabut

    public function cabut(Request $r)
    {
        $id_pengawas = auth()->user()->id;
        $data = [
            'title' => 'Gudang Cabut',
            'cabut' => DB::select("SELECT a.tgl, a.no_box, a.tipe, a.ket, a.warna, a.pcs_awal, a.pcs_awal, a.gr_awal, if(b.pcs_cabut is null ,0,b.pcs_cabut) as pcs_cabut, if(b.gr_cabut is null ,0,b.gr_cabut) as gr_cabut
            FROM bk as a 
            left join (
                SELECT b.no_box, sum(b.pcs_awal) as pcs_cabut, sum(b.gr_awal) as gr_cabut
                FROM cabut as b
                group by b.no_box
            ) as b on b.no_box =  a.no_box
            where a.penerima = '$id_pengawas' and a.formulir  = 'T' and a.kategori = 'cabut' and a.gr_awal - if(b.gr_cabut is null ,0,b.gr_cabut) != 0;")
        ];

        return view('home.gudang_sarang.cabut', $data);
    }

    public function get_formulircabut(Request $r)
    {
        $data = [
            'title' => 'Gudang Sarang',
            'no_box' => $r->no_box
        ];
        return view('home.gudang_sarang.get_formulircabut', $data);
    }

    public function save_formulir_cabut(Request $r)
    {
        $no_box = $r->no_box;
        $no_invoice = strtoupper(Str::random(5));

        for ($x = 0; $x < count($no_box); $x++) {
            $data = [
                'formulir' => 'Y',
                'invoice_formulir' => 'FS-' . $no_invoice,

            ];
            DB::table('bk')->where('no_box', $no_box[$x])->update($data);
        }
        return redirect("home/gudangsarang/print_cabut?no_invoice=FS-$no_invoice")->with('sukses', 'Data berhasil ditambahkan');
    }

    public function print_cabut(Request $r)
    {
        $data = [
            'title' => 'Gudang Cabut',
            'cabut' => DB::select("SELECT a.tgl, a.no_box, a.tipe, a.ket, a.warna, a.pcs_awal, a.pcs_awal, a.gr_awal, if(b.pcs_cabut is null ,0,b.pcs_cabut) as pcs_cabut, if(b.gr_cabut is null ,0,b.gr_cabut) as gr_cabut
            FROM bk as a 
            left join (
                SELECT b.no_box, sum(b.pcs_awal) as pcs_cabut, sum(b.gr_awal) as gr_cabut
                FROM cabut as b
                group by b.no_box
            ) as b on b.no_box =  a.no_box
            where a.invoice_formulir = '$r->no_invoice'"),

            'nama' => auth()->user()->name
        ];

        return view('home.gudang_sarang.printcabut', $data);
    }
}
