<?php

namespace App\Http\Controllers;

use App\Models\PengirimanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackingListController extends Controller
{
    public function index(Request $r)
    {
        $pengiriman = DB::table("pengiriman as a")
            ->leftJoin('pengiriman_packing_list as b', 'a.id_pengiriman', '=', 'b.id_pengiriman')
            ->whereNull('b.id_pengiriman')
            ->select('a.id_pengiriman', 'a.no_barcode as no_box', 'a.grade', 'a.pcs', 'a.gr')
            ->get();

        $data = [
            'title' => 'Packing list',
            'pengiriman' => $pengiriman
        ];

        return view('home.packinglist.index', $data);
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

            DB::table('pengiriman_packing_list')->insert([
                'tgl' => $r->tgl,
                'nm_packing' => $r->nm_packing,
                'pgws_cek' => auth()->user()->name,
                'id_pengiriman' => $d,
                'gr_naik' => $cekGr * 0.10,
                'no_nota' => $no_nota
            ]);
        }
        return redirect()->route('packinglist.index', ['kategori' => 'packing'])->with('sukses', 'Data Berhasil dimasukan');
    }
    public function pengiriman(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        
        $packing = DB::select("SELECT 
        a.no_nota,
        a.no_invoice_manual as no_invoice,
        a.nm_packing,
        a.tgl,
        count(*) as ttl_box,
        sum(b.pcs) as pcs,
        sum(b.gr + (b.gr / a.kadar)) as gr 
        FROM `pengiriman_packing_list` as a
        JOIN pengiriman as b on a.id_pengiriman = b.id_pengiriman
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY a.no_nota
        ORDER BY a.no_nota DESC");

        $data = [
            'title' => 'Pengiriman',

            'packing' => $packing,
        ];

        return view('home.packinglist.pengiriman', $data);
    }

    public function tbh_invoice(Request $r)
    {
        for ($i = 0; $i < count($r->no_nota); $i++) {
            DB::table('pengiriman_packing_list')->where('no_nota', $r->no_nota[$i])->update(['no_invoice_manual' => $r->no_invoice[$i]]);
        }
        return redirect()->route('packinglist.pengiriman')->with('sukses', 'Data Berhasil diubah');
    }

    public function getDetailPrint($no_nota)
    {
        $no_nota = $no_nota;
        $detailPacking = DB::table('pengiriman_packing_list')->where('no_nota', $no_nota)->first();
        $id_pengiriman = DB::table('pengiriman_packing_list')->where('no_nota', $no_nota)->pluck('id_pengiriman')->toArray();
        $id_pengiriman = implode(',', $id_pengiriman);
        $detail = DB::select("SELECT 
        a.grade,
        sum(a.pcs) as pcs, 
        sum(a.gr + (a.gr / c.kadar)) as gr, 
        count(*) as box
        FROM `pengiriman` as a 
        JOIN pengiriman_packing_list as c on a.id_pengiriman = c.id_pengiriman
        WHERE a.id_pengiriman in ($id_pengiriman)
        GROUP BY a.grade ORDER BY a.grade ASC");

        $pengirimanBox = DB::select("SELECT 
        a.grade,
        sum(b.pcs) as pcs,
        sum(b.gr) as gr,
        a.no_box,
        a.cek_qc as cek_akhir,
        a.admin,
        b.tipe,
        b.nm_partai
        FROM `pengiriman` as a
        JOIN grading_partai as b on a.no_box = b.box_pengiriman
        WHERE a.id_pengiriman  in ($id_pengiriman)
        GROUP BY b.box_pengiriman
        ORDER by a.grade DESC");

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
        return view('home.packinglist.detail', $this->getDetailPrint($r->no_nota));
    }

    public function print($no_nota)
    {
        return view('home.packinglist.print', $this->getDetailPrint($no_nota));
    }

    public function delete($no_nota)
    {
        DB::table('pengiriman')->where('no_nota', $no_nota)->delete();
        DB::table('pengiriman_packing_list')->where('no_nota', $no_nota)->delete();

        return redirect()->route('packinglist.pengiriman')->with('sukses', 'Data Berhasil dihapus');
    }
}
