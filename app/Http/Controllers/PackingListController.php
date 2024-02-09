<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackingListController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $data = [
            'title' => 'Packing list',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'pengiriman' => DB::table('pengiriman as a')
                ->select('a.*')
                ->leftJoin('pengiriman_packing_list as b', 'b.id_pengiriman', 'a.id_pengiriman')
                ->whereNotNull('a.no_box')
                ->orderBy('a.id_pengiriman', 'DESC')->get(),
            'packing' => DB::select("SELECT a.no_nota,a.tgl,a.nm_packing,a.pgws_cek,count(*) as ttl_box, b.pcs, b.gr
            FROM `pengiriman_packing_list` as a
            JOIN (
                SELECT no_nota_packing_list as nota_packing,sum(pcs_akhir) as pcs,sum(gr_akhir) + sum(gr_naik) as gr 
                FROM `pengiriman` GROUP BY no_nota_packing_list
            ) as b on a.no_nota = b.nota_packing
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
            GROUP BY a.no_nota
            ORDER BY a.no_nota DESC;")
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
        return redirect()->route('packinglist.index')->with('sukses', 'Data Berhasil dimasukan');
    }

    public function getDetailPrint($no_nota)
    {
        $no_nota = $no_nota;

        $detailPacking = DB::table('pengiriman_packing_list')->where('no_nota', $no_nota)->first();

        $detail = DB::select("SELECT a.grade,sum(a.pcs_akhir) as pcs, sum(a.gr_akhir)  as gr,sum(a.gr_naik)as gr_naik, count(*) as box
        FROM `pengiriman` as a 
        join tb_grade as b on a.grade = b.nm_grade
        WHERE a.no_nota_packing_list = '$no_nota'
        ORDER BY b.id_grade
        GROUP BY a.grade;");

        $data = [
            "title" => 'detail',
            'no_nota' => $no_nota,
            'detail' => $detail,
            'detailPacking' => $detailPacking,
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
        return redirect()->route('packinglist.index')->with('sukses', 'Data Berhasil dihapus');

        
    }
}
