<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use App\Models\Sortir;
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

        $data = [
            'title' => 'gudang Cetak',
            'pengawas' => DB::table('users')->where('posisi_id', '14')->get(),
            'view_pengawas' => DB::select("SELECT a.id_pengawas, c.name
            FROM cabut as a 
            left join users as c on c.id = a.id_pengawas
            where a.formulir = 'T' and a.selesai = 'Y'
            group by a.id_pengawas
            ")
        ];
        return view('home.gudang_sarang.index', $data);
    }

    public function load_cabut_selesai(Request $r)
    {
        if (empty($r->id_pengawas)) {
            $cabut = DB::select("SELECT 
            a.pengawas, a.no_box, a.nama, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, min(a.selesai) as selesai
            FROM ( 
                SELECT a.id_cabut, c.name as pengawas, a.no_box, b.nama, a.pcs_akhir, a.gr_akhir, a.selesai
                FROM cabut AS a 
                LEFT JOIN tb_anak AS b ON b.id_anak = a.id_anak
                LEFT JOIN users AS c ON c.id = a.id_pengawas
                WHERE a.formulir = 'T'
            ) AS a
            GROUP BY a.pengawas, a.no_box 
            HAVING min(a.selesai) = 'Y'
            ORDER BY a.no_box ASC;
            ");
        } else {
            $cabut = DB::select("SELECT 
            a.pengawas, a.no_box, a.nama, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, min(a.selesai) as selesai
            FROM ( 
                SELECT a.id_pengawas, a.id_cabut, c.name as pengawas, a.no_box, b.nama, a.pcs_akhir, a.gr_akhir, a.selesai
                FROM cabut AS a 
                LEFT JOIN tb_anak AS b ON b.id_anak = a.id_anak
                LEFT JOIN users AS c ON c.id = a.id_pengawas
                WHERE a.formulir = 'T'
            ) AS a
            GROUP BY a.id_pengawas, a.no_box 
            HAVING min(a.selesai) = 'Y' AND a.id_pengawas = '$r->id_pengawas'
            ORDER BY a.no_box ASC;
            ");
        }

        $data = [
            'cabut' => $cabut,
        ];
        return view('home.gudang_sarang.getcetak', $data);
    }

    public function get_siap_cetak(Request $r)
    {
        $id_penerima =  auth()->user()->id;
        $cabut = DB::select("SELECT a.tanggal, c.name, a.no_box, a.pcs_awal, a.gr_awal, b.selesai
        FROM formulir_sarang as a 
        left join (
            SELECT b.no_box, min(b.selesai) as selesai
            FROM cetak_new as b
            group by b.no_box
        ) as b on b.no_box = a.no_box
        left join users as c on c.id = a.id_pemberi
        where a.kategori = 'cetak' and b.no_box is null and a.id_penerima = '$id_penerima' ;
        ");

        $data = [
            'cabut' => $cabut,
        ];
        return view('home.gudang_sarang.getsiap_cetak', $data);
    }
    public function get_cetak_proses(Request $r)
    {
        $id_penerima =  auth()->user()->id;
        $cabut = DB::select("SELECT a.tgl, b.nama, a.no_box, a.pcs_awal, a.gr_awal
        FROM cetak_new as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        WHERE a.id_pengawas = '$id_penerima' and a.selesai = 'T';
        ");

        $data = [
            'cabut' => $cabut,
        ];
        return view('home.gudang_sarang.getcetak_proses', $data);
    }

    public function get_formulir(Request $r)
    {
        $data = [
            'title' => 'Gudang Sarang',
            'no_box' => $r->no_box
        ];
        return view('home.gudang_sarang.get_formulir', $data);
    }

    public function save_formulir(Request $r)
    {
        $no_box = $r->no_box;
        $no_invoice = strtoupper(Str::random(5));


        $no_box = explode(',', $r->no_box[0]);

        foreach ($no_box as $d) {
            $data = [
                'formulir' => 'Y',
                'invoice' => 'FS-' . $no_invoice,
            ];
            DB::table('cabut')->where('no_box', $d)->update($data);

            $cabut = DB::table('cabut')->where('no_box', $d)->first();
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

            $data = [
                'id_pengawas' => $r->id_pengawas,
                'no_box' => $d,
                'tgl' => $r->tgl,
                'pcs_awal_ctk' => $cabut->pcs_akhir,
                'gr_awal_ctk' => $cabut->gr_akhir,
            ];
            DB::table('cetak_new')->insert($data);
        }
        return redirect("home/gudangsarang/print_formulir?no_invoice=FS-$no_invoice")->with('sukses', 'Data berhasil ditambahkan');
    }



    public function print_formulir(Request $r)
    {
        $formulir = DB::table('formulir_sarang as a')
            ->leftJoin('bk as b', function ($join) {
                $join->on('b.no_box', '=', 'a.no_box')
                    ->where('b.kategori', '=', 'cabut');
            })
            ->where('a.kategori', 'cetak')
            ->where('a.no_invoice', $r->no_invoice)
            ->select('b.nm_partai', 'a.no_box', 'a.pcs_awal', 'a.gr_awal')
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
        return view('home.gudang_sarang/print_formulir', $data);
    }

    public function load_edit_invoice_grade(Request $r)
    {
        $id_user = auth()->user()->id;
        $no_invoice = $r->no_invoice;
        $kategori = 'grade';

        $cabutSelesai = Sortir::sortir_selesai($id_user);
        $formulir = DB::table('formulir_sarang')->where([['kategori', $kategori], ['no_invoice', $no_invoice]])->get();
        $data = [
            'title' => 'Gudang Sarang',
            'cabutSelesai' => $cabutSelesai,
            'formulir' => $formulir,
        ];
        return view('home.gudang_sarang.load_edit_invoice_grade', $data);
    }

    public function invoice(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $kategori = $r->kategori ?? 'cetak';
        $route = request()->route()->getName();
        $routeSekarang = "gudangsarang.invoice";

        $formulir = DB::select("SELECT count(a.no_box) as ttlbox, group_concat(a.no_box) as no_box, a.id_formulir, a.no_invoice, a.tanggal, b.name as pemberi, c.name as penerima, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
        FROM formulir_sarang as a
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        WHERE a.kategori = '$kategori' 
        group by a.no_invoice
        order by a.id_formulir DESC
        ");

        $data = [
            'title' => 'Po ' . ucwords($kategori),
            'formulir' => $formulir,
            'kategori' => $kategori,
            'route' => $route,
            'routeSekarang' => $routeSekarang,
        ];
        return view('home.gudang_sarang.invoice', $data);
    }

    // bk ke cabut

    public function cabut(Request $r)
    {
        $id_pengawas = auth()->user()->id;
        $data = [
            'title' => 'Cabut Formulir',
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

    public function batal(Request $r)
    {
        $no_invoice = $r->no_invoice;
        $getFormulir = DB::table('formulir_sarang')->where([['no_invoice', $no_invoice], ['kategori', $r->kategori]]);
        foreach ($getFormulir->get() as $d) {
            DB::table('cabut')->where('no_box', $d->no_box)->update(['formulir' => 'T']);
        }
        $getFormulir->delete();
        return redirect()->back()->with('sukses', 'Data Berhasil di hapus');
    }
    public function batal_wip(Request $r)
    {
        $no_invoice = $r->no_invoice;
        $getFormulir = DB::table('formulir_sarang')->where([['no_invoice', $no_invoice], ['kategori', $r->kategori]]);
        $getFormulir->delete();
        return redirect()->back()->with('sukses', 'Data Berhasil di hapus');
    }

    public function selesai_grade(Request $r)
    {
        $no_invoice = $r->no_invoice;
        $getFormulir = DB::table('formulir_sarang')->where([['no_invoice', $no_invoice], ['kategori', $r->kategori]])->get();
        foreach ($getFormulir as $d) {
            $data[] = [
                'no_box_sortir' => $d->no_box,
                'tgl' => $d->tanggal,
                'pcs' => 0,
                'gr' => 0,
            ];
        }
        DB::table('grading')->insert($data);
        return redirect()->back()->with('sukses', 'Data Berhasil di selesaikan');
    }

    public function selesai_wip(Request $r)
    {
        $no_invoice = $r->no_invoice;
        $getFormulir = DB::table('formulir_sarang')->where([['no_invoice', $no_invoice], ['kategori', $r->kategori]])->get();
        foreach ($getFormulir as $d) {
            DB::table('grading_partai')->where('box_pengiriman', $d->no_box)->update(['formulir' => 'Y']);
        }

        return redirect()->back()->with('sukses', 'Data Berhasil di selesaikan');
    }

    public function selesai(Request $r)
    {
        $no_invoice = $r->no_invoice;
        $getFormulir = DB::table('formulir_sarang')->where([['no_invoice', $no_invoice], ['kategori', $r->kategori]])->get();
        foreach ($getFormulir as $d) {
            $data[] = [
                'id_pengawas' => $d->id_penerima,
                'no_box' => $d->no_box,
                'tgl' => $d->tanggal,
                'pcs_awal_ctk' => $d->pcs_awal,
                'gr_awal_ctk' => $d->gr_awal,
            ];
        }
        DB::table('cetak_new')->insert($data);
        return redirect('home/gudangsarang/invoice')->with('sukses', 'Data Berhasil di selesaikan');
    }

    public function load_edit_invoice(Request $r)
    {
        $id_user = auth()->user()->id;
        $no_invoice = $r->no_invoice;
        $kategori = $r->kategori;

        $cabutSelesai = Cabut::gudangCabutSelesai($id_user);
        $formulir = DB::table('formulir_sarang')->where([['kategori', $kategori], ['no_invoice', $no_invoice]])->get();
        $data = [
            'title' => 'Gudang Sarang',
            'cabutSelesai' => $cabutSelesai,
            'formulir' => $formulir,
        ];
        return view('home.gudang_sarang.load_edit_invoice', $data);
    }

    public function update_invoice(Request $r)
    {
        $no_invoice = $r->no_invoice;
        if (!$r->no_box[0]) {
            return redirect()->route('gudangsarang.invoice')->with('error', 'No Box / Penerima Kosong !');
        }
        DB::table('formulir_sarang')->where([['no_invoice', $no_invoice], ['kategori', 'cetak']])->delete();
        $no_box = explode(',', $r->no_box[0]);
        foreach ($no_box as $d) {
            $ambil = DB::selectOne("SELECT 
                        sum(pcs_akhir) as pcs_akhir, sum(gr_akhir) as gr_akhir 
                        FROM cabut 
                        WHERE no_box = $d AND selesai = 'Y' GROUP BY no_box ");

            $pcs = $ambil->pcs_akhir;
            $gr = $ambil->gr_akhir;

            $data[] = [
                'no_invoice' => $no_invoice,
                'no_box' => $d,
                'id_pemberi' => auth()->user()->id,
                'id_penerima' => $r->id_penerima,
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'tanggal' => $r->tgl,
                'kategori' => 'cetak',
            ];

            DB::table('cabut')->where('no_box', $d)->update(['formulir' => 'Y']);
        }

        DB::table('formulir_sarang')->insert($data);
        return redirect()->route('gudangsarang.invoice')->with('sukses', 'Data Berhasil');
    }

    public function update_invoice_grade(Request $r)
    {
        $no_invoice = $r->no_invoice;
        if (!$r->no_box[0]) {
            return redirect()->route('gudangsarang.invoice_grade', ['kategori' => 'grade'])->with('error', 'No Box / Penerima Kosong !');
        }
        DB::table('formulir_sarang')->where([['no_invoice', $no_invoice], ['kategori', 'grade']])->delete();
        $no_box = explode(',', $r->no_box[0]);
        foreach ($no_box as $d) {
            $ambil = DB::selectOne("SELECT 
                        sum(pcs_akhir) as pcs_akhir, sum(gr_akhir) as gr_akhir 
                        FROM sortir 
                        WHERE no_box = $d AND selesai = 'Y' GROUP BY no_box ");

            $pcs = $ambil->pcs_akhir;
            $gr = $ambil->gr_akhir;

            $data[] = [
                'no_invoice' => $no_invoice,
                'no_box' => $d,
                'id_pemberi' => auth()->user()->id,
                'id_penerima' => $r->id_penerima,
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'tanggal' => $r->tgl,
                'kategori' => 'grade',
            ];

            DB::table('cabut')->where('no_box', $d)->update(['formulir' => 'Y']);
        }

        DB::table('formulir_sarang')->insert($data);
        return redirect()->route('gudangsarang.invoice_grade', ['kategori' => 'grade'])->with('sukses', 'Data Berhasil');
    }

    public function invoice_sortir(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $kategori = $r->kategori ?? 'cetak';
        $route = request()->route()->getName();
        $routeSekarang = "gudangsarang.invoice_sortir";

        $formulir = DB::select("SELECT count(a.no_box) as ttl_box, a.id_formulir, a.no_invoice, a.tanggal, b.name as pemberi, c.name as penerima, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
        FROM formulir_sarang as a
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        WHERE a.kategori = 'sortir' 
        group by a.no_invoice
        order by a.id_formulir DESC
        ");

        $data = [
            'title' => 'Po Sortir',
            'formulir' => $formulir,
            'kategori' => $kategori,
            'route' => $route,
            'routeSekarang' => $routeSekarang,
        ];
        return view('home.gudang_sarang.invoice_sortir', $data);
    }
    public function invoice_grade(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $kategori = $r->kategori ?? 'cetak';
        $route = request()->route()->getName();
        $routeSekarang = "gudangsarang.invoice_grade";

        $formulir = DB::select("SELECT count(a.no_box) as ttl_box, a.id_formulir, a.no_invoice, a.tanggal, b.name as pemberi, c.name as penerima, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
        FROM formulir_sarang as a
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        WHERE a.kategori = 'grade' 
        group by a.no_invoice
        order by a.id_formulir DESC
        ");

        $data = [
            'title' => 'Po Sortir',
            'formulir' => $formulir,
            'kategori' => $kategori,
            'route' => $route,
            'routeSekarang' => $routeSekarang,
        ];
        return view('home.gudang_sarang.invoice_grade', $data);
    }

    public function invoice_wip(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $kategori = $r->kategori ?? 'cetak';
        $route = request()->route()->getName();
        $routeSekarang = "gudangsarang.invoice_grade";

        $formulir = DB::select("SELECT count(a.no_box) as ttl_box, a.id_formulir, a.no_invoice, a.tanggal, b.name as pemberi, c.name as penerima, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
        FROM formulir_sarang as a
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        WHERE a.kategori = 'wip' 
        group by a.no_invoice
        order by a.id_formulir DESC
        ");

        $data = [
            'title' => 'Po Sortir',
            'formulir' => $formulir,
            'kategori' => $kategori,
            'route' => $route,
            'routeSekarang' => $routeSekarang,
        ];
        return view('home.gudang_sarang.invoice_wip', $data);
    }

    public function print_formulir_grade(Request $r)
    {
        $formulir = DB::table('formulir_sarang as a')
            ->where([['a.no_invoice', $r->no_invoice], ['b.kategori', 'cabut'], ['a.kategori', 'grade']])
            ->join('bk as b', 'a.no_box', '=', 'b.no_box')
            ->groupBy('a.no_box', 'a.kategori')
            ->selectRaw('a.id_pemberi,a.id_penerima,a.tanggal,b.nm_partai,b.ket,b.tipe,a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr')
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
        return view('home.gudang_sarang/print_formulir_grade', $data);
    }

    public function print_formulir_wip(Request $r)
    {
        $formulir = DB::table('formulir_sarang as a')
            ->join('grading_partai as b', 'a.no_box', '=', 'b.box_pengiriman')
            ->where([['a.no_invoice', $r->no_invoice], ['a.kategori', 'wip']])
            ->selectRaw('b.grade,b.tipe,a.tanggal,a.no_box, a.pcs_awal, a.gr_awal, a.id_pemberi,a.id_penerima')
            ->get();

        // $ket_formulir = DB::selectOne("SELECT  a.tanggal,b.name, c.name as penerima
        // FROM formulir_sarang as a 
        // left join users as b on b.id = a.id_pemberi
        // left join users as c on c.id = a.id_penerima
        // WHERE a.no_invoice = '$r->no_invoice' and a.kategori = 'grade'");

        $data = [
            'title' => 'Gudang Sarang',
            'formulir' => $formulir,
            'no_invoice' => $r->no_invoice,
        ];
        return view('home.gudang_sarang.print_formulir_wip', $data);
    }
}
