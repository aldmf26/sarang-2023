<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QcController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Qc',
            'qc' => DB::select('SELECT a.*, b.grade
                FROM qc as a 
                left join (
                    SELECT b.box_pengiriman, b.grade
                    FROM grading_partai as b
                    group by b.box_pengiriman
                ) as b on b.box_pengiriman = a.box_pengiriman
                where a.wip2 = "T" and a.invoice_qc = 0
            ')
        ];
        return view('home.qc.index', $data);
    }

    public function save_invoice_qc(Request $r)
    {

        $invoice = DB::table('qc')->orderBy('invoice_qc', 'DESC')->value('invoice_qc');
        $invoice = empty($invoice) ? 1001 : $invoice + 1;


        foreach (explode(',', $r->no_box) as $d) {
            $data = [
                'invoice_qc' => $invoice,
            ];
            DB::table('qc')->where('box_pengiriman', $d)->update($data);
        }
        return redirect()->back()->with('sukses', 'Data Berhasil di simpan');
    }

    public function listqc()
    {
        $data = [
            'title' => 'Qc',
            'qc' => DB::select('SELECT a.invoice_qc, count(a.box_pengiriman) as total_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(a.gr_akhir) as gr_akhir, a.selesai
                FROM qc as a 
                where  a.invoice_qc != 0 and a.wip2 = "T"
                group by a.invoice_qc
                order by a.invoice_qc DESC
            ')
        ];
        return view('home.qc.list_qc', $data);
    }
    public function listboxqc(Request $r)
    {
        $data = [
            'title' => 'Qc',
            'qc' => DB::select("SELECT a.*, b.grade
                FROM qc as a 
                left join (
                    SELECT b.box_pengiriman, b.grade
                    FROM grading_partai as b
                    group by b.box_pengiriman
                ) as b on b.box_pengiriman = a.box_pengiriman
                where a.invoice_qc = '$r->invoice_qc'
            ")
        ];
        return view('home.qc.listboxqc', $data);
    }

    public function save_akhir(Request $r)
    {
        for ($i = 0; $i < count($r->box_pengiriman); $i++) {
            $data = [
                'gr_akhir' => $r->gr_akhir[$i],
                'selesai' => 'Y',
            ];
            DB::table('qc')->where('box_pengiriman', $r->box_pengiriman[$i])->update($data);

            // $box = $r->box_pengiriman[$i];

            // $grading_partai = DB::select("SELECT * FROM grading_partai where box_pengiriman = '$box'");

            // $susut = sumBk($grading_partai, 'gr') - $r->gr_akhir[$i];
            // $pembagian = $susut / count($grading_partai);


            // foreach ($grading_partai as $d) {
            //     $data = [
            //         'gr' => $d->gr - $pembagian,
            //     ];
            //     DB::table('grading_partai')->where('id_grading', $d->id_grading)->update($data);
            // }
            // if ($susut == 0) {
            // } else {
            //     $data2 = [
            //         'box_pengiriman' => '10000',
            //         'nm_partai' => 'susut ' . $r->box_pengiriman[$i],
            //         'gr' => $susut,
            //         'grade' => 'susut',
            //         'pcs' => 0,
            //         'tgl' => date('Y-m-d'),
            //         'no_invoice' => 0,
            //     ];
            //     DB::table('grading_partai')->insert($data2);
            // }
        }
        return redirect()->route('qc.listqc')->with('sukses', 'Data Berhasil di simpan');
    }

    public function po_wip(Request $r)
    {
        foreach (explode(',', $r->no_box) as $d) {
            $qc = DB::table('qc')->where('invoice_qc', $d)->get();
            $no_invoice = DB::table('formulir_sarang')->where('kategori', 'wip2')->orderBy('no_invoice', 'DESC')->value('no_invoice');
            $no_invoice = empty($no_invoice) ? 1001 : $no_invoice + 1;

            DB::table('qc')->where('invoice_qc', $d)->update(['wip2' => 'Y']);
            foreach ($qc as $q) {
                $dataToInsert[] = [
                    'no_invoice' => $no_invoice,
                    'no_box' => $q->box_pengiriman,
                    'id_pemberi' => 459,
                    'id_penerima' => 459,
                    'pcs_awal' => $q->pcs_awal,
                    'gr_awal' => $q->gr_akhir,
                    'tanggal' => date('Y-m-d'),
                    'kategori' => 'wip2',
                    'selesai' => 'Y',
                ];
            }
        }
        DB::table('formulir_sarang')->insert($dataToInsert);
        return redirect()->back()->with('sukses', 'Data Berhasil di simpan');
    }
}
