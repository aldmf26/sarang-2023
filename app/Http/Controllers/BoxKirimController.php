<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BoxKirimController extends Controller
{

    public function index()
    {
        $deleteToken = bin2hex(random_bytes(32));
        Session::put('delete_token', $deleteToken);

        $boxKirim = DB::table('pengiriman')->select('no_box', 'grade', 'pcs', 'gr', 'no_barcode', 'id_pengiriman')->get();
        $data = [
            'title' => 'Box Kirim',
            'boxKirim' => $boxKirim,
            'deleteToken' => $deleteToken,
        ];
        return view('home.pengiriman.index', $data);
    }

    public function add(Request $r)
    {
        $data = [
            'title' => 'Buat Box Pengiriman',
        ];
        return view('home.pengiriman.add', $data);
    }

    public function create(Request $r)
    {
        if (!$r->gr) {
            return redirect()->back()->with('error', 'Data Belum Lengkap');
        }
        DB::beginTransaction();
        try {
            $admin = auth()->user()->name;
            $tgl_input = date('Y-m-d');
            $no_nota = DB::table('pengiriman')->orderBy('id_pengiriman', 'DESC')->first();
            $no_nota = empty($no_nota) ? 1001 : $no_nota->no_nota + 1;

            $dataToInsert = [];
            for ($i = 0; $i < count($r->gr); $i++) {
                if ($r->pcs[$i] != 0) {

                    $grade = DB::table('grading as a')
                        ->join('tb_grade as b', 'a.id_grade', '=', 'b.id_grade')
                        ->where('a.no_box_grading', $r->no_grading[$i])
                        ->first()
                        ->nm_grade;

                    $dataToInsert[] = [
                        'no_box' => $r->no_grading[$i],
                        'pcs' => $r->pcs[$i],
                        'gr' => $r->gr[$i],
                        'cek_qc' => $r->cek_qc[$i],
                        'no_barcode' => $r->no_barcode[$i],
                        'admin' => $admin,
                        'grade' => $grade,
                        'tgl_input' => $tgl_input,
                        'no_nota' => $no_nota,
                    ];
                }
            }

            DB::table('pengiriman')->insert($dataToInsert);

            DB::commit();
            return redirect()->route('pengiriman.index')->with('sukses', 'Data Berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengiriman.index')->with('error', $e->getMessage());
        }
    }



    public function edit(Request $r)
    {
        $token = $r->token;
        if (!$token || $token !== Session::get('delete_token')) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid token.');
        }
        // Dapatkan dan validasi id_pengiriman
        $id_pengiriman = explode(',', $r->id_pengiriman);
        if (empty($id_pengiriman)) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid id_pengiriman.');
        }
        $id_pengiriman = array_map('intval', $id_pengiriman);

        $boxKirim = DB::table('pengiriman')
            ->select('tgl_input', 'cek_qc', 'no_box', 'grade', 'pcs', 'gr', 'no_barcode', 'id_pengiriman')
            ->whereIn('id_pengiriman', $id_pengiriman)
            ->get();
        $data = [
            'title' => 'Edit Box Kirim',
            'boxKirim' => $boxKirim
        ];
        return view('home.pengiriman.edit', $data);
    }

    public function update(Request $r)
    {
        $token = $r->token;
        if (!$token || $token !== Session::get('delete_token')) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid token.');
        }
        // Dapatkan dan validasi id_pengiriman
        $id_pengiriman = explode(',', $r->id_pengiriman);
        if (empty($id_pengiriman)) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid id_pengiriman.');
        }

        DB::beginTransaction();
        try {
            $admin = auth()->user()->name;

            for ($i = 0; $i < count($r->gr); $i++) {
                if ($r->pcs[$i] != 0) {

                    $grade = DB::table('grading as a')
                        ->join('tb_grade as b', 'a.id_grade', '=', 'b.id_grade')
                        ->where('a.no_box_grading', $r->no_grading[$i])
                        ->first()
                        ->nm_grade;

                    $dataToInsert = [
                        'no_box' => $r->no_grading[$i],
                        'pcs' => $r->pcs[$i],
                        'gr' => $r->gr[$i],
                        'cek_qc' => $r->cek_qc[$i],
                        'no_barcode' => $r->no_barcode[$i],
                        'admin' => $admin,
                        'grade' => $grade,
                        'tgl_input' => $r->tgl_input[$i],
                    ];
                    DB::table('pengiriman')->where('id_pengiriman', $r->id_pengiriman[$i])->update($dataToInsert);
                }
            }


            DB::commit();
            return redirect()->route('pengiriman.index')->with('sukses', 'Data Berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengiriman.index')->with('error', $e->getMessage());
        }
    }

    public function delete(Request $r)
    {
        $token = $r->token;
        if (!$token || $token !== Session::get('delete_token')) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid token.');
        }
        // Dapatkan dan validasi id_pengiriman
        $id_pengiriman = explode(',', $r->id_pengiriman);
        if (empty($id_pengiriman)) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid id_pengiriman.');
        }

        // Hapus data
        DB::table('pengiriman')->whereIn('id_pengiriman', $id_pengiriman)->delete();
        return redirect()->route('pengiriman.index')->with('success', 'Data dihapus');
    }


    public function kirim(Request $r)
    {
        $admin = auth()->user()->name;
        $tgl_input = date('Y-m-d');
        $no_nota = DB::table('pengiriman')->orderBy('id_pengiriman', 'DESC')->first();
        $no_nota = empty($no_nota) ? 1001 : $no_nota->no_nota + 1;
        foreach (explode(',', $r->no_box) as $d) {
            $grade = DB::table('grading as a')
                ->join('tb_grade as b', 'a.id_grade', '=', 'b.id_grade')
                ->where('a.no_box_grading', $d)
                ->first()
                ->nm_grade;
            $ambilBox = DB::selectOne("SELECT sum(pcs) as pcs, sum(gr) as gr FROM `grading` as a
                    where a.no_box_grading = $d
                    group by a.no_box_grading");
            $dataToInsert[] = [
                'no_box' => $d,
                'pcs' => $ambilBox->pcs,
                'gr' => $ambilBox->gr,
                'admin' => $admin,
                'grade' => $grade,
                'tgl_input' => $tgl_input,
                'no_nota' => $no_nota,
            ];
        }
        DB::table('pengiriman')->insert($dataToInsert);
        return redirect()->route('pengiriman.po', $no_nota)->with('sukses', 'data sudah masuk po');
    }

    public function po($no_nota)
    {
        $po = DB::selectOne("SELECT a.tgl_input as tanggal,a.no_nota,sum(pcs) as pcs, sum(gr) as gr, count(*) as ttl FROM `pengiriman` as a
                WHERE a.no_nota = $no_nota GROUP by a.no_nota;");
        
        $pengiriman = DB::table('pengiriman')->where('no_nota', $no_nota)->get();
        $data = [
            'title' => 'Po Pengiriman',
            'po' => $po,
            'no_nota' => $no_nota,
            'pengiriman' => $pengiriman
        ];
        return view('home.pengiriman.po', $data);
    }

    public function save_po(Request $r)
    {
        try {
            DB::beginTransaction();
            $no_invoice = $r->no_nota;
            $tgl = $r->tgl;
            $getFormulir = DB::table('pengiriman')->where('no_nota', $no_invoice)->get();
            foreach ($getFormulir as $d) {
                $data[] = [
                    'id_pengiriman' => $d->id_pengiriman,
                    'tgl' => $tgl,
                    'no_nota' => $no_invoice,
                    'nm_packing' => $r->nm_packing,
                    'kadar' => $r->kadar,
                ];
            }
            DB::table('pengiriman_packing_list')->insert($data);
            DB::commit();
            return redirect()->route('packinglist.pengiriman')->with('sukses', 'Data Berhasil di selesaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function selesai_grade(Request $r)
    {
        try {
            DB::beginTransaction();
            $no_invoice = $r->no_invoice;
            $tgl = date('Y-m-d');
            $getFormulir = DB::table('pengiriman')->where('no_nota', $no_invoice)->get();
            foreach ($getFormulir as $d) {
                $data[] = [
                    'id_pengiriman' => $d->id_pengiriman,
                    'tgl' => $tgl,
                    'no_nota' => $d->no_nota,
                ];
            }
            DB::table('pengiriman_packing_list')->insert($data);
            DB::commit();
            return redirect()->back()->with('sukses', 'Data Berhasil di selesaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function print_formulir_grade(Request $r)
    {
        $formulir = DB::table('pengiriman_packing_list as a')
            ->join('pengiriman as b', 'a.id_pengiriman', '=', 'b.id_pengiriman')
            ->where('a.no_nota', $r->no_invoice)
            ->select('b.no_box', 'b.pcs', 'b.gr', 'b.grade','a.tgl')
            ->get();
        $data = [
            'title' => 'Gudang Sarang',
            'formulir' => $formulir,
        ];
        return view('home.pengiriman.print_po', $data);
    }

    public function batal(Request $r)
    {
        DB::table('pengiriman')->where('no_nota', $r->no_invoice)->delete();
        return redirect()->route('gradingbj.gudang_siap_kirim')->with('sukses', 'Data Berhasil di selesaikan');
    }

    public function gudang(Request $r)
    {
        $selesai = DB::table('pengiriman_packing_list as a')
                        ->join('pengiriman as b', 'a.id_pengiriman', '=', 'b.id_pengiriman')
                        ->select('b.no_box', 'b.pcs', 'b.gr', 'b.grade')
                        ->get();

        $querydilaporan = DB::select("SELECT 
            a.no_box,
            d.pcs_akhir as pcs_str, 
            d.gr_akhir as gr_str, 
            (
                (
                (a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp + d.ttl_rp
                ) / c.gr_akhir
            ) as rp_gram_str, 
            (
                (
                1 -(d.gr_akhir / a.gr_awal)
                ) * 100
            ) as sst_str
            FROM 
            bk as a 
            left join cabut as b on b.no_box = a.no_box 
            left join (
                SELECT 
                c.no_box, 
                c.pcs_akhir, 
                c.gr_akhir, 
                c.gr_tdk_cetak, 
                c.ttl_rp, 
                (e.rp_gr * c.gr_akhir) as oprasional_ctk 
                FROM 
                cetak_new as c 
                left join kelas_cetak as d on d.id_kelas_cetak = c.id_kelas_cetak 
                left join oprasional as e on e.bulan = c.bulan_dibayar 
                where 
                d.kategori = 'CTK'
            ) as c on c.no_box = a.no_box 
            left join sortir as d on d.no_box = a.no_box 
            
            where 
            a.kategori = 'cabut' 
            and a.baru = 'baru'
            GROUP BY e.no_box_sortir;");
                            
        $data = [
            'title' => 'Gudang Pengiriman',
            'gudang' => Grading::siapKirim(),
            'selesai' => $selesai
        ];
        return view('home.pengiriman.gudang', $data);
    }
}
