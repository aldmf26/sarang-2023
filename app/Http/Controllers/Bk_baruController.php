<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Bk_baruController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Bk',
            'bk' => DB::table('bk')->where('penerima', 0)->where('kategori', 'cabut')->orderBy('id_bk', 'DESC')->get(),
            'users' => DB::table('users')->whereNotIn('posisi_id', [1, 15, 16, 14])->get(),
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
        $nobox = isset($cekBox->no_box) ? $cekBox->no_box + 1 : 1001;
        return $nobox;
    }

    public function save_formulir(Request $r)
    {
        $cekBox = DB::selectOne("SELECT no_invoice FROM `formulir_sarang` WHERE kategori = 'cabut' ORDER by no_invoice DESC limit 1;");
        $no_invoice = isset($cekBox->no_invoice) ? $cekBox->no_invoice + 1 : 1001;

        $no_box = explode(',', $r->no_box[0]);

        foreach ($no_box as $d) {
            $data = [
                'penerima' => $r->id_penerima
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

    public function invoice()
    {
        $data = [
            'title' => 'Invoice',
        ];
        return view('home.bkbaru.invoice', $data);
    }
}
