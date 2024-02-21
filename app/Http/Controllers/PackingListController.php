<?php

namespace App\Http\Controllers;

use App\Models\PengirimanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackingListController extends Controller
{
    public function getDataMaster($jenis)
    {

        $arr = [
            'gudangkirim' => DB::select("SELECT grade, sum(pcs) as pcs, sum(gr) as gr, sum(gr * rp_gram) as ttl_rp, sum(pcs_kredit) as pcs_kredit, sum(gr_kredit) as gr_kredit, sum(gr_kredit * rp_gram_kredit) as ttl_rp_kredit
                        FROM `siapkirim_list_grading` 
                        GROUP BY grade 
                        HAVING pcs - pcs_kredit <> 0 OR gr - gr_kredit <> 0"),
            'pengawas' => DB::table('users')->where('posisi_id', 13)->get()
        ];
        return $arr[$jenis];
    }
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $kategori = $r->kategori;
        $data = [
            'title' => 'Packing list',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'kategori' => $kategori,
            'pengiriman' => DB::table('pengiriman as a')
                ->select('a.*')
                ->leftJoin('pengiriman_packing_list as b', 'b.id_pengiriman', 'a.id_pengiriman')
                ->where([['a.no_nota_packing_list', '']])
                ->orderBy('a.id_pengiriman', 'DESC')->get(),
            'packing' => DB::select("SELECT a.no_invoice_manual as no_invoice,a.no_nota,a.tgl,a.nm_packing,a.pgws_cek,count(*) as ttl_box, b.pcs, b.gr
            FROM `pengiriman_packing_list` as a
            JOIN (
                SELECT no_nota_packing_list as nota_packing,sum(pcs_akhir) as pcs,sum(gr_akhir) + sum(gr_naik) as gr 
                FROM `pengiriman` GROUP BY no_nota_packing_list
            ) as b on a.no_nota = b.nota_packing
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
            GROUP BY a.no_nota
            ORDER BY a.no_nota DESC;"),
            'box_kirim' => DB::select("SELECT a.* FROM `pengiriman`as a
            LEFT JOIN tb_grade as b on a.grade = b.nm_grade
            LEFT JOIN pengiriman_packing_list as c on a.no_nota_packing_list = c.no_nota
            WHERE a.tgl_pengiriman BETWEEN '$tgl1' and '$tgl2' AND a.no_nota_packing_list = ''
            ORDER BY b.urutan asc;")
        ];

        return view('home.packing.index', $data);
    }

    public function load_tbh()
    {
        $data = [
            'title' => 'asd',
            'pengiriman' => DB::table('pengiriman')->orderBy('id_pengiriman', 'DESC')->get()
        ];
        return view('home.packing.load_tbh', $data);
    }

    public function create(Request $r)
    {
        $id_pengiriman = $r->id_pengiriman;

        $new_array = [];
        foreach ($id_pengiriman as $key => $value) {
            $new_array = array_merge($new_array, explode(',', $value));
        }

        $no_nota = DB::table('pengiriman_packing_list')->orderBy('id_packing', 'DESC')->first();
        $no_nota = empty($no_nota) ? 1001 : $no_nota->no_nota + 1;
        foreach ($new_array as $d) {
            $tblPengiriman = DB::table('pengiriman')->where('id_pengiriman', $d);
            $cekGr = $tblPengiriman->first()->gr;
            $tblPengiriman->update([
                'no_nota_packing_list' => $no_nota
            ]);
            DB::table('pengiriman_packing_list')->insert([
                'tgl' => $r->tgl,
                'nm_packing' => $r->nm_packing,
                'pgws_cek' => auth()->user()->name,
                'id_pengiriman' => $d,
                'no_nota' => $no_nota
            ]);
        }
        return redirect()->route('packinglist.index', ['kategori' => 'packing'])->with('sukses', 'Data Berhasil dimasukan');
    }

    public function tbh_invoice(Request $r)
    {
        for ($i = 0; $i < count($r->no_nota); $i++) {
            DB::table('pengiriman_packing_list')->where('no_nota', $r->no_nota[$i])->update(['no_invoice_manual' => $r->no_invoice[$i]]);
        }
        return redirect()->route('packinglist.index')->with('sukses', 'Data Berhasil diubah');
    }
    public function getDetailPrint($no_nota)
    {
        $no_nota = $no_nota;

        $detailPacking = DB::table('pengiriman_packing_list')->where('no_nota', $no_nota)->first();

        $detail = DB::select("SELECT a.grade,sum(a.pcs_akhir) as pcs, sum(a.gr_akhir)  as gr,sum(a.gr_naik)as gr_naik, count(*) as box
        FROM `pengiriman` as a 
        LEFT join tb_grade as b on a.grade = b.nm_grade
        WHERE a.no_nota_packing_list = '$no_nota'
        GROUP BY a.grade ORDER BY b.urutan ASC");

        $pengirimanBox = DB::select("SELECT 
        a.partai,
        a.tipe,
        b.nm_grade as grade,
        a.pcs_akhir,
        a.gr_akhir,
        a.no_box,
        a.cek_akhir,
        a.admin
        FROM `pengiriman` as a
        LEFT JOIN tb_grade as b on a.grade = b.nm_grade
        WHERE a.no_nota_packing_list  = '$no_nota'
        ORDER by b.urutan ASC");

        $data = [
            "title" => 'detail',
            'no_nota' => $no_nota,
            'detail' => $detail,
            'detailPacking' => $detailPacking,
            'pengirimanBox' => $pengirimanBox,
        ];
        return $data;
    }

    public function detail(Request $r)
    {
        return view('home.packing.detail', $this->getDetailPrint($r->no_nota));
    }

    public function print($no_nota)
    {
        return view('home.packing.print', $this->getDetailPrint($no_nota));
    }

    public function delete($no_nota)
    {
        DB::table('pengiriman_packing_list')->where('no_nota', $no_nota)->delete();
        DB::table('pengiriman')->where('no_nota_packing_list', $no_nota)->update([
            'no_nota_packing_list' => ''
        ]);
        return redirect()->route('packinglist.index', ['kategori' => 'packing'])->with('sukses', 'Data Berhasil dihapus');
    }

    public function add_box_kirim(Request $r)
    {
        $data = [
            'title' => 'Tambah Box Kirim',
            'pengiriman' => $this->getDataMaster('gudangkirim')
        ];
        return view('home.packing.add_box_kirim', $data);
    }

    public function create_box_kirim(Request $r)
    {
        try {
            DB::beginTransaction();
            $admin = auth()->user()->name;
            $tgl_input = date('Y-m-d');
            $no_nota = DB::table('pengiriman')->orderBy('id_pengiriman', 'DESC')->first();
            $no_nota = empty($no_nota) ? 1001 : $no_nota->no_nota + 1;

            $dataToInsert = [];
            for ($i = 0; $i < count($r->gr); $i++) {
                if ($r->pcs[$i] != 0) {
                    $rp_gram = PengirimanModel::pengirimanPerGrade($r->grade[$i]);


                    $dataToInsert[] = [
                        'tgl_pengiriman' => $r->tgl[$i],
                        'partai' => $r->partai[$i],
                        'tipe' => $r->tipe[$i],
                        'grade' => $r->grade[$i],
                        'pcs' => $r->pcs[$i],
                        'gr' => $r->gr[$i],
                        'gr_naik' => $r->gr[$i] * 0.10,
                        'no_box' => $r->no_box[$i],
                        'cek_akhir' => $r->cek_akhir[$i],
                        'ket' => $r->ket[$i],
                        'admin' => $admin,
                        'tgl_input' => $tgl_input,
                        'no_nota' => $no_nota,
                        'rp_gram' => ($rp_gram->ttl_rp - $rp_gram->ttl_rp_ambil) / ($rp_gram->gr_awal - $rp_gram->gr_ambil)
                    ];
                }
            }

            DB::table('pengiriman')->insert($dataToInsert);

            DB::commit();
            return redirect()->route('packinglist.index', ['kategori' => 'box'])->with('sukses', 'Data Berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('packinglist.add_box_kirim')->with('error', "Data Gagal input ulang");
        }
    }
    public function edit(Request $r)
    {
        $tbl = DB::table('pengiriman')->whereIn('id_pengiriman', $r->no_nota)->get();
        $data = [
            'title' => 'Edit Pengiriman',
            'pengawas' => User::where('posisi_id', 13)->get(),
            'tbl' => $tbl,
        ];
        return view('home.packing.edit', $data);
    }

    public function update(Request $r)
    {
        try {
            DB::beginTransaction();
            $admin = auth()->user()->name;
            $tgl_input = date('Y-m-d');
            for ($i = 0; $i < count($r->id_pengiriman); $i++) {
                $dataToInsert = [
                    'tgl_pengiriman' => $r->tgl[$i],
                    'partai' => $r->partai[$i],
                    'tipe' => $r->tipe[$i],
                    'grade' => $r->grade[$i],
                    'pcs' => $r->pcs[$i],
                    'gr' => $r->gr[$i],
                    'pcs_akhir' => $r->pcs_akhir[$i],
                    'gr_akhir' => $r->gr_akhir[$i],
                    'gr_naik' => $r->gr_akhir[$i] * 0.10,
                    'no_box' => $r->no_box[$i],
                    'cek_akhir' => $r->cek_akhir[$i],
                    'ket' => $r->ket[$i],
                    'admin' => $admin,
                    'tgl_input' => $tgl_input,
                ];

                DB::table('pengiriman')->where('id_pengiriman', $r->id_pengiriman[$i])->update($dataToInsert);
            }


            DB::commit();
            return redirect()->route('packinglist.index', ['kategori' => 'box'])->with('sukses', 'Data Berhasil diupdatekan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('packinglist.index')->with('error', 'Data Gagal input ulang');
        }
    }

    public function gudangKirim(Request $r)
    {
        $data = [
            'title'  => 'Grading Bj Siap kirim',
            'gudangkirim' => PengirimanModel::Pengiriman(),
            'kategori' => 'gudang'
        ];
        return view('home.siapkirim.gudangkirim', $data);
    }
}
