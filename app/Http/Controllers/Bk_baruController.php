<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Bk_baruController extends Controller
{
    public function index(Request $r)
    {
        $id_user = auth()->user()->id;
        $posisi = auth()->user()->posisi_id;

        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $id_user = auth()->user()->id;
        $where = "a.tgl between ? and ? and a.kategori LIKE ? and a.selesai = 'T' ";
        $params = [$tgl1, $tgl2, "cabut"];
        if (!in_array(auth()->user()->posisi_id, [1, 12])) {
            $where .= " and a.penerima = ?";
            $params[] = $id_user;
        }

        if ($posisi == 1 || $posisi == 12) {
            $bk = DB::table('bk')->where('formulir', 'T')->where('kategori', 'cabut')->orderBy('id_bk', 'DESC')->get();
        } else {
            $bk = DB::select("SELECT a.tgl_input,a.pgws_grade, a.susut, a.nm_partai,a.id_bk,a.selesai,a.no_lot,a.no_box,a.tipe,a.ket,a.warna,a.tgl,a.pengawas,a.penerima,a.pcs_awal,a.gr_awal,d.name FROM bk as a 
            left join users as d on d.id = a.penerima 
            WHERE $where ORDER BY a.id_bk DESC", $params);
        }


        $data = [
            'title' => 'Box Terakhir',
            'bk' => $bk,
            'users' => DB::table('users')->whereNotIn('posisi_id', [1, 15, 16, 14])->get(),
            'bk_terakhir' => $this->getNoBoxTambah()['nobox2'],
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
        ];
        return view('home.bkbaru.index', $data);
    }

    public function add(Request $r)
    {


        $data = [
            'title' => 'Tambah Divisi BK',
            'pengawas' => User::where('posisi_id', 13)->get(),
            'noBoxTerakhir' => DB::table('bk')->where('kategori', $r->kategori)->orderBy('id_bk', 'DESC')->first()->no_box ?? 5000,
            'id_pengawas' => auth()->user()->id
            // 'gudangBk' => $gudangBk
        ];

        return view('home.bkbaru.create', $data);
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
                    $nobox = $this->getNoBoxTambah()['nobox'];

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
                        'penerima' => '0',
                        'pcs_awal' => $pcs_awal,
                        'gr_awal' => $gr_awal,
                        'tgl' => $r->tgl_terima[$x],
                        'pgws_grade' => $r->pgws_grade[$x],
                        'kategori' => 'cabut',
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
            return redirect("home/bkbaru")->with('sukses', 'Data berhasil ditambahkan');
        } catch (\Exception  $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getNoBoxTambah()
    {
        $cekBox = DB::selectOne("SELECT CAST(no_box AS UNSIGNED) as no_box FROM `bk` WHERE kategori like '%cabut%' and baru = 'baru' ORDER BY CAST(no_box AS UNSIGNED) DESC LIMIT 1;");
        $nobox = isset($cekBox->no_box) ? $cekBox->no_box + 1 : 101;
        $nobox2 = isset($cekBox->no_box) ? $cekBox->no_box  : 101;
        return [
            'nobox' => $nobox,
            'nobox2' => $nobox2
        ];
    }

    public function save_formulir(Request $r)
    {
        $cekBox = DB::selectOne("SELECT no_invoice FROM `formulir_sarang` WHERE kategori = 'cabut' ORDER by no_invoice DESC limit 1;");
        $no_invoice = isset($cekBox->no_invoice) ? $cekBox->no_invoice + 1 : 1001;

        $no_box = explode(',', $r->no_box[0]);

        foreach ($no_box as $d) {
            $data = [
                'formulir' => 'Y'
            ];
            DB::table('bk')->where('no_box', $d)->where('kategori', 'cabut')->update($data);

            $bk = DB::table('bk')->where('no_box', $d)->where('kategori', 'cabut')->first();

            $data = [
                'no_invoice' => $no_invoice,
                'no_box' => $d,
                'id_pemberi' => auth()->user()->id,
                'id_penerima' => $r->id_penerima,
                'pcs_awal' => $bk->pcs_awal,
                'gr_awal' => $bk->gr_awal,
                'tanggal' => $r->tgl,
                'kategori' => 'cabut',
            ];
            DB::table('formulir_sarang')->insert($data);
        }
        return redirect("home/bkbaru")->with('sukses', 'Data berhasil ditambahkan');
    }


    public function invoice(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $kategori = $r->kategori ?? 'cabut';
        $route = request()->route()->getName();
        $routeSekarang = "gudangsarang.invoice";
        $id_user = auth()->user()->id;

        $role = DB::selectOne("SELECT posisi_id FROM users WHERE id = '$id_user'");

        $cek_rol = ($role->posisi_id == '1' || $role->posisi_id == '12') ? '' : ' AND a.id_penerima = ' . $id_user;

        $formulir = DB::select("SELECT count(a.no_box) as ttlbox, group_concat(a.no_box) as no_box, a.id_formulir, a.no_invoice, a.tanggal, b.name as pemberi, c.name as penerima, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, d.penerima as penerima_bk
        FROM formulir_sarang as a
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
        WHERE a.kategori = '$kategori' $cek_rol
        group by a.no_invoice
        order by a.id_formulir DESC
        ");



        $data = [
            'title' => 'Po ' . ucwords($kategori),
            'formulir' => $formulir,
            'kategori' => $kategori,
            'route' => $route,
            'routeSekarang' => $routeSekarang,
            'role' => $role->posisi_id,
        ];
        return view('home.bkbaru.invoice', $data);
    }

    public function print_formulir(Request $r)
    {
        $formulir = DB::table('formulir_sarang as a')
            ->leftJoin('bk as b', function ($join) {
                $join->on('b.no_box', '=', 'a.no_box')
                    ->where('b.kategori', '=', 'cabut');
            })
            ->where('a.kategori', 'cabut')
            ->where('a.no_invoice', $r->no_invoice)
            ->select('b.nm_partai', 'a.no_box', 'a.pcs_awal', 'a.gr_awal', 'b.tipe', 'b.ket', 'b.warna')
            ->groupBy('b.no_box')
            ->get();

        // $formulir = DB::select("SELECT b.nm_partai, a.no_box, a.pcs_awal, a.gr_awal, b.tipe, b.ket, b.warna FROM formulir_sarang as a
        //     left join bk as b on b.no_box = a.no_box
        //     where a.kategori = 'cabut' and a.no_invoice = '$r->no_invoice'
        // ");




        $ket_formulir = DB::selectOne("SELECT a.tanggal,  b.name, c.name as penerima, d.nm_partai
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_pemberi
        left join users as c on c.id = a.id_penerima
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
        WHERE no_invoice = '$r->no_invoice' and a.kategori = 'cabut'");
        $data = [
            'title' => 'Gudang Sarang',
            'formulir' => $formulir,
            'no_invoice' => $r->no_invoice,
            'ket_formulir' => $ket_formulir
        ];
        return view('home.bkbaru.print_formulir', $data);
    }
    public function print_label(Request $r)
    {
        $formulir =  DB::select("SELECT a.no_box, a.pcs_awal, a.gr_awal, c.no_invoice, c.tgl , b.nm_partai, c.grade_id, c.rwb_id FROM formulir_sarang as a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join sbw_kotor as c on c.nm_partai = b.nm_partai
        where a.no_invoice = $r->no_invoice and a.kategori = 'cabut'
        ");

        $grades = [];

        foreach ($formulir as $f) {
            $idg = $f->grade_id;

            if (!isset($grades[$idg])) {
                $res = Http::get("https://ptagrikagatyaarum.com/api/apikodesbw/detail_grade_sbw?id=$idg");
                $res = json_decode($res, true);
                $grades[$idg] = $res['data']; // simpan grade sesuai ID
            }
        }
        $rm_walet = [];

        foreach ($formulir as $f) {
            $idrm = $f->rwb_id;

            if (!isset($rm_walet[$idg])) {
                $res = Http::get("https://ptagrikagatyaarum.com/api/apikodesbw/detail_rumah_walet?id=$idrm");
                $res = json_decode($res, true);
                $rm_walet[$idrm] = $res['data']; // simpan grade sesuai ID
            }
        }

        $data = [
            'title' => 'Gudang Sarang',
            'formulir' => $formulir,
            'no_invoice' => $r->no_invoice,
            'grades'   => $grades,
            'rm_walet' => $rm_walet
            // 'ket_formulir' => $ket_formulir
        ];
        return view('home.bkbaru.print_label', $data);
    }

    public function batal(Request $r)
    {
        $invoice = DB::table('formulir_sarang')->where('no_invoice', $r->no_invoice)->where('kategori', 'cabut')->get();
        foreach ($invoice as $k) {
            DB::table('bk')->where('no_box', $k->no_box)->update([
                'formulir' => 'T'
            ]);
            DB::table('formulir_sarang')->where('no_box', $k->no_box)->where('kategori', 'cabut')->delete();
        }

        return redirect()->back()->with('sukses', 'Data Berhasil di hapus');
    }
    public function load_edit_invoice(Request $r)
    {
        $id_user = auth()->user()->id;
        $no_invoice = $r->no_invoice;
        $kategori = 'cabut';

        $cabutSelesai = DB::table('bk')->where('formulir', 'T')->where('kategori', 'cabut')->orderBy('id_bk', 'DESC')->get();
        $formulir = DB::table('formulir_sarang')->where([['kategori', $kategori], ['no_invoice', $no_invoice]])->get();
        $data = [
            'title' => 'Gudang Sarang',
            'cabutSelesai' => $cabutSelesai,
            'formulir' => $formulir,
        ];
        return view('home.bkbaru.load_edit_invoice_grade', $data);
    }

    public function update_invoice(Request $r)
    {
        $no_invoice = $r->no_invoice;
        if (!$r->no_box[0]) {
            return redirect()->route('gudangsarang.invoice')->with('error', 'No Box / Penerima Kosong !');
        }
        DB::table('formulir_sarang')->where([['no_invoice', $no_invoice], ['kategori', 'cabut']])->delete();
        $no_box = explode(',', $r->no_box[0]);

        foreach ($no_box as $d) {
            $ambil = DB::selectOne("SELECT 
                        sum(pcs_awal) as pcs_awal, sum(gr_awal) as gr_awal 
                        FROM bk 
                        WHERE no_box = $d  GROUP BY no_box ");

            $pcs = $ambil->pcs_awal;
            $gr = $ambil->gr_awal;

            $data[] = [
                'no_invoice' => $no_invoice,
                'no_box' => $d,
                'id_pemberi' => auth()->user()->id,
                'id_penerima' => $r->id_penerima,
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'tanggal' => $r->tgl,
                'kategori' => 'cabut',
            ];

            DB::table('bk')->where('no_box', $d)->update(['formulir' => 'Y']);
        }

        DB::table('formulir_sarang')->insert($data);
        return redirect()->route('bkbaru.invoice')->with('sukses', 'Data Berhasil');
    }

    public function selesai(Request $r)
    {
        $invoice = DB::table('formulir_sarang')->where('no_invoice', $r->no_invoice)->where('kategori', 'cabut')->get();

        foreach ($invoice as $k) {
            DB::table('bk')->where('no_box', $k->no_box)->update([
                'penerima' => $k->id_penerima
            ]);
        }
        return redirect()->route('bkbaru.invoice')->with('sukses', 'Data Berhasil di selesaikan');
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
        return view('home.bkbaru.edit', $data);
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
                    'pcs_awal' => $r->pcs_awal[$x],
                    'gr_awal' => $r->gr_awal[$x],
                    'tgl' => $r->tgl_terima[$x],
                    'susut' => $r->susut[$x]
                ];
                DB::table('bk')->where('id_bk', $r->id_bk[$x])->update($data);
            }
        }
        return redirect("home/bkbaru")->with('sukses', 'Data berhasil ditambahkan');
    }
}
