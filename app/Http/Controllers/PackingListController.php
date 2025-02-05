<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        $packing = Grading::list_pengiriman_sum();


        $tgl1 = tglFormat($tgl1);
        $tgl2 = tglFormat($tgl2);
        $data = [
            'title' => 'Pengiriman ' . "$tgl1 ~ $tgl2",
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
            sum(a.gr + (a.gr / b.kadar)) as gr,
            c.box
            from pengiriman as a
            join (
                select no_nota,kadar from pengiriman_packing_list group by no_nota
            ) as b on a.no_nota = b.no_nota
            join (
                        SELECT grade, COUNT(DISTINCT no_barcode) AS box, SUM(pcs) AS sum_pcs, SUM(gr) AS sum_gr
                        FROM `pengiriman`
                        where no_nota = $no_nota
                        GROUP BY grade
                    ) as c on a.grade = c.grade
            where a.no_nota = $no_nota
            GROUP by a.grade");

        $pengirimanBox = DB::select("SELECT 
        a.grade as grade2,
        b.pcs,
        b.gr,
        a.grade,
        a.no_barcode,
        sum(a.pcs) as pcs2,
        sum(a.gr) as gr2,
        a.no_box,
        a.cek_qc as cek_akhir,
        a.admin,
        b.tipe,
        b.nm_partai
        FROM `pengiriman` as a
        JOIN (
            SELECT box_pengiriman,sum(pcs) as pcs,sum(gr) as gr,tipe,nm_partai FROM grading_partai group by box_pengiriman
        )  as b on a.no_box = b.box_pengiriman
        WHERE a.no_box  in ($id_pengiriman)
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
        $get = DB::table('pengiriman')->where('no_nota', $no_nota);
        $getBox = $get->pluck('no_box');
        $get->update(['selesai' => 'T']);
        DB::table('pengiriman_packing_list')->where('no_nota', $no_nota)->delete();
        DB::table('grading_partai')->whereIn('box_pengiriman', $getBox)->update(['sudah_kirim' => 'T']);

        return redirect()->route('packinglist.pengiriman')->with('sukses', 'Data Berhasil dihapus');
    }

    public function check_grade()
    {
        $cek = DB::select("SELECT 
                b.nm_partai,
                b.box_pengiriman as box_grading,
                b.grade,
                b.pcs,
                b.gr,
                a.grade as grade2,
                a.pcs as pcs2,
                a.gr as gr2 
                FROM pengiriman as a 
                join grading_partai as b on a.no_box = b.box_pengiriman 
                WHERE a.selesai = 'Y';");

        $data = [
            'title' => 'Check Grade Berubah',
            'cek' => $cek,
        ];
        return view('home.packinglist.check_grade', $data);
    }

    private function lis_pengiriman($spreadsheet, $style_atas, $style)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet4 = $spreadsheet->getActiveSheet(0);
        $sheet4->setTitle('List Pengiriman');

        $sheet4->getStyle("A1:J1")->applyFromArray($style_atas);

        $sheet4->setCellValue('A1', 'No');
        $sheet4->setCellValue('B1', 'tgl kirim');
        $sheet4->setCellValue('C1', 'no packing list');
        $sheet4->setCellValue('D1', 'nama packing list');
        $sheet4->setCellValue('E1', 'tujuan');
        $sheet4->setCellValue('F1', 'box');
        $sheet4->setCellValue('G1', 'pcs');
        $sheet4->setCellValue('H1', 'gr');
        $sheet4->setCellValue('I1', 'gr + kadar');
        $sheet4->setCellValue('J1', 'Total Rp');

        $packing_list = Grading::list_pengiriman_sum();

        $kolom = 2;

        foreach ($packing_list  as $no => $d) {
            $sheet4->setCellValue('A' . $kolom, $no + 1);
            $sheet4->setCellValue('B' . $kolom, tanggal($d->tgl));
            $sheet4->setCellValue('C' . $kolom, $d->no_nota);
            $sheet4->setCellValue('D' . $kolom, ucwords($d->nm_packing));
            $sheet4->setCellValue('E' . $kolom, strtoupper($d->tujuan));
            $sheet4->setCellValue('F' . $kolom, $d->ttl_box);
            $sheet4->setCellValue('G' . $kolom, $d->pcs);
            $sheet4->setCellValue('H' . $kolom, $d->gr);
            $sheet4->setCellValue('I' . $kolom, $d->gr_naik);

            $totalRp = $d->cost_bk + $d->cost_kerja + $d->cost_cu + $d->cost_op;

            $sheet4->setCellValue('J' . $kolom, $totalRp);
            $kolom++;
        }
        $sheet4->getStyle('A2:J' . $kolom - 1)->applyFromArray($style);
    }

    public function export()
    {
        $style_atas = array(
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
        );

        $style = [
            'borders' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];
        $spreadsheet = new Spreadsheet();

        $this->lis_pengiriman($spreadsheet, $style_atas, $style);

        $namafile = "History Pengiriman.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
