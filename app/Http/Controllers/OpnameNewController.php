<?php

namespace App\Http\Controllers;

use App\Models\CocokanModel;
use App\Models\Grading;
use App\Models\OpnameNewModel;
use App\Models\SummaryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use stdClass;

class OpnameNewController extends Controller
{
    public function index(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => $model::bksisapgws(),
            'box_proses' => $model::bksedang_proses_sum(),
            'box_selesai' => $model::bksedang_selesai_sum(),

        ];
        return view('home.opnamenew.index', $data);
    }
    public function cetak(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => $model::cetak_stok(),
            'box_proses' => $model::cetak_proses(),
            'box_selesai' => $model::cetak_selesai(),

        ];
        return view('home.opnamenew.cetak', $data);
    }
    public function sortir(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => $model::sortir_stock(),
            'box_proses' => $model::sortir_proses(),
            'box_selesai' => $model::sortir_selesai(),

        ];
        return view('home.opnamenew.sortir', $data);
    }

    public function grading(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => DB::select("SELECT a.tgl_input, a.no_barcode, a.grade, sum(a.pcs) as pcs, sum(a.gr) as gr 
            FROM pengiriman as a 
            group by a.no_barcode;"),
            'box_proses' => DB::select("SELECT * FROM `grading_partai` WHERE `box_pengiriman` not in(SELECT a.no_box FROM pengiriman as a )"),
            'box_selesai' => $model::sortir_selesai(),

        ];
        return view('home.opnamenew.grading', $data);
    }

    public function export(OpnameNewModel $model)
    {
        $style_atas = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
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

        $spreadsheet->setActiveSheetIndex(0);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Gudang Cabut');

        $sheet1->getStyle("B1:L1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'Cabut sedang proses');
        $sheet1->setCellValue('B1', 'partai');
        $sheet1->setCellValue('C1', 'pengawas');
        $sheet1->setCellValue('D1', 'no box');
        $sheet1->setCellValue('E1', 'pcs');
        $sheet1->setCellValue('F1', 'gr');
        $sheet1->setCellValue('G1', 'ttl rp bk');
        $sheet1->setCellValue('H1', 'cost kerja');
        $sheet1->setCellValue('I1', 'cost cu dll');
        $sheet1->setCellValue('J1', 'cost operasional');
        $sheet1->setCellValue('K1', 'ttl rp');
        $sheet1->setCellValue('L1', 'rp/gr');

        $gudangbk = $model::bksedang_proses_sum();

        $kolom = 2;
        foreach ($gudangbk as $d) {
            $sheet1->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('C' . $kolom, $d->name);
            $sheet1->setCellValue('D' . $kolom, $d->no_box);
            $sheet1->setCellValue('E' . $kolom, $d->pcs);
            $sheet1->setCellValue('F' . $kolom, $d->gr);
            $sheet1->setCellValue('G' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('H' . $kolom, 0);
            $sheet1->setCellValue('I' . $kolom, 0);
            $sheet1->setCellValue('J' . $kolom, 0);
            $sheet1->setCellValue('K' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('L' . $kolom, $d->ttl_rp / $d->gr);
            $kolom++;
        }

        $sheet1->getStyle('B2:L' . $kolom - 1)->applyFromArray($style);

        $sheet1->getStyle("O1:Y1")->applyFromArray($style_atas);
        $sheet1->setCellValue('N1', 'Cabut sisa pengawas');
        $sheet1->setCellValue('O1', 'partai');
        $sheet1->setCellValue('P1', 'pengawas');
        $sheet1->setCellValue('Q1', 'no box');
        $sheet1->setCellValue('R1', 'pcs');
        $sheet1->setCellValue('S1', 'gr');
        $sheet1->setCellValue('T1', 'ttl rp bk');
        $sheet1->setCellValue('U1', 'cost kerja');
        $sheet1->setCellValue('V1', 'cost cu dll');
        $sheet1->setCellValue('W1', 'cost operasional');
        $sheet1->setCellValue('X1', 'ttl rp');
        $sheet1->setCellValue('Y1', 'rp/gr');

        $gudangbksisa = $model::bksisapgws();

        $kolom = 2;
        foreach ($gudangbksisa as $d) {
            $sheet1->setCellValue('O' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('P' . $kolom, $d->name);
            $sheet1->setCellValue('Q' . $kolom, $d->no_box);
            $sheet1->setCellValue('R' . $kolom, $d->pcs);
            $sheet1->setCellValue('S' . $kolom, $d->gr);
            $sheet1->setCellValue('T' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('U' . $kolom, 0);
            $sheet1->setCellValue('V' . $kolom, 0);
            $sheet1->setCellValue('W' . $kolom, 0);
            $sheet1->setCellValue('X' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('Y' . $kolom, $d->ttl_rp / $d->gr);
            $kolom++;
        }

        $sheet1->getStyle('O2:Y' . $kolom - 1)->applyFromArray($style);


        $sheet1->getStyle("AB1:AL1")->applyFromArray($style_atas);
        $sheet1->setCellValue('AA1', 'Cabut selesai siap cetak');
        $sheet1->setCellValue('AB1', 'partai');
        $sheet1->setCellValue('AC1', 'pengawas');
        $sheet1->setCellValue('AD1', 'no box');
        $sheet1->setCellValue('AE1', 'pcs');
        $sheet1->setCellValue('AF1', 'gr');
        $sheet1->setCellValue('AG1', 'ttl rp bk');
        $sheet1->setCellValue('AH1', 'cost kerja');
        $sheet1->setCellValue('AI1', 'cost cu dll');
        $sheet1->setCellValue('AJ1', 'cost operasional');
        $sheet1->setCellValue('AK1', 'ttl rp');
        $sheet1->setCellValue('AL1', 'rp/gr');

        $gudangbkselesai = $model::bksedang_selesai_sum();

        $kolom = 2;
        foreach ($gudangbkselesai as $d) {
            $sheet1->setCellValue('AB' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('AC' . $kolom, $d->name);
            $sheet1->setCellValue('AD' . $kolom, $d->no_box);
            $sheet1->setCellValue('AE' . $kolom, $d->pcs);
            $sheet1->setCellValue('AF' . $kolom, $d->gr);
            $sheet1->setCellValue('AG' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('AH' . $kolom, $d->cost_kerja);
            $sheet1->setCellValue('AI' . $kolom, 0);
            $sheet1->setCellValue('AJ' . $kolom, 0);
            $sheet1->setCellValue('AK' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet1->setCellValue('AL' . $kolom, empty($d->no_box) ? 0 : ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet1->getStyle('AB2:AL' . $kolom - 1)->applyFromArray($style);


        $this->datacetak($spreadsheet, $style_atas, $style, $model);
        $this->datasortir($spreadsheet, $style_atas, $style, $model);
        $this->gudang_grading($spreadsheet, $style_atas, $style, $model);
        $this->datapengiriman($spreadsheet, $style_atas, $style, $model);
        $this->rekap($spreadsheet, $style_atas, $style, $model);
        $this->bk_sinta($spreadsheet, $style_atas, $style, $model);
        $this->lis_pengiriman($spreadsheet, $style_atas, $style, $model);


        $namafile = "Opname Gudang.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }


    private function datacetak($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet(1);
        $sheet2->setTitle('Gudang Cetak');

        $sheet2->getStyle("B1:L1")->applyFromArray($style_atas);
        $sheet2->setCellValue('A1', 'Cetak sedang proses');
        $sheet2->setCellValue('B1', 'partai');
        $sheet2->setCellValue('C1', 'pengawas');
        $sheet2->setCellValue('D1', 'no box');
        $sheet2->setCellValue('E1', 'pcs');
        $sheet2->setCellValue('F1', 'gr');
        $sheet2->setCellValue('G1', 'ttl rp bk');
        $sheet2->setCellValue('H1', 'cost kerja');
        $sheet2->setCellValue('I1', 'cost cu dll');
        $sheet2->setCellValue('J1', 'cost operasional');
        $sheet2->setCellValue('K1', 'ttl rp');
        $sheet2->setCellValue('L1', 'rp/gr');

        $cetak_proses = $model::cetak_proses();
        // kena diatas dihapus 
        $kolom = 2;
        foreach ($cetak_proses  as $d) {
            $sheet2->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet2->setCellValue('C' . $kolom, $d->name);
            $sheet2->setCellValue('D' . $kolom, $d->no_box);
            $sheet2->setCellValue('E' . $kolom, $d->pcs);
            $sheet2->setCellValue('F' . $kolom, $d->gr);
            $sheet2->setCellValue('G' . $kolom, $d->ttl_rp);
            $sheet2->setCellValue('H' . $kolom, $d->cost_kerja);
            $sheet2->setCellValue('I' . $kolom, 0);
            $sheet2->setCellValue('J' . $kolom, 0);
            $sheet2->setCellValue('K' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet2->setCellValue('L' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet2->getStyle('B2:L' . $kolom - 1)->applyFromArray($style);

        $sheet2->getStyle("O1:Y1")->applyFromArray($style_atas);
        $sheet2->setCellValue('N1', 'Cetak sisa pengawas');
        $sheet2->setCellValue('O1', 'partai');
        $sheet2->setCellValue('P1', 'pengawas');
        $sheet2->setCellValue('Q1', 'no box');
        $sheet2->setCellValue('R1', 'pcs');
        $sheet2->setCellValue('S1', 'gr');
        $sheet2->setCellValue('T1', 'ttl rp bk');
        $sheet2->setCellValue('U1', 'cost kerja');
        $sheet2->setCellValue('V1', 'cost cu dll');
        $sheet2->setCellValue('W1', 'cost operasional');
        $sheet2->setCellValue('X1', 'ttl rp');
        $sheet2->setCellValue('Y1', 'rp/gr');

        $cetak_proses = $model::cetak_stok();
        $kolom = 2;
        foreach ($cetak_proses  as $d) {
            $sheet2->setCellValue('O' . $kolom, $d->nm_partai);
            $sheet2->setCellValue('P' . $kolom, $d->name);
            $sheet2->setCellValue('Q' . $kolom, $d->no_box);
            $sheet2->setCellValue('R' . $kolom, $d->pcs);
            $sheet2->setCellValue('S' . $kolom, $d->gr);
            $sheet2->setCellValue('T' . $kolom, $d->ttl_rp);
            $sheet2->setCellValue('U' . $kolom, $d->cost_kerja);
            $sheet2->setCellValue('V' . $kolom, 0);
            $sheet2->setCellValue('W' . $kolom, 0);
            $sheet2->setCellValue('X' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet2->setCellValue('Y' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet2->getStyle('O2:Y' . $kolom - 1)->applyFromArray($style);

        $sheet2->getStyle("AB1:AL1")->applyFromArray($style_atas);
        $sheet2->setCellValue('AA1', 'Cetak selesai siap sortir');
        $sheet2->setCellValue('AB1', 'partai');
        $sheet2->setCellValue('AC1', 'pengawas');
        $sheet2->setCellValue('AD1', 'no box');
        $sheet2->setCellValue('AE1', 'pcs');
        $sheet2->setCellValue('AF1', 'gr');
        $sheet2->setCellValue('AG1', 'ttl rp bk');
        $sheet2->setCellValue('AH1', 'cost kerja');
        $sheet2->setCellValue('AI1', 'cost cu dll');
        $sheet2->setCellValue('AJ1', 'cost operasional');
        $sheet2->setCellValue('AK1', 'ttl rp');
        $sheet2->setCellValue('AL1', 'rp/gr');

        $cetak_selesai = $model::cetak_selesai();
        $kolom = 2;
        foreach ($cetak_selesai  as $d) {
            $sheet2->setCellValue('AB' . $kolom, $d->nm_partai);
            $sheet2->setCellValue('AC' . $kolom, $d->name);
            $sheet2->setCellValue('AD' . $kolom, $d->no_box);
            $sheet2->setCellValue('AE' . $kolom, $d->pcs);
            $sheet2->setCellValue('AF' . $kolom, $d->gr);
            $sheet2->setCellValue('AG' . $kolom, $d->ttl_rp);
            $sheet2->setCellValue('AH' . $kolom, $d->cost_kerja);
            $sheet2->setCellValue('AI' . $kolom, 0);
            $sheet2->setCellValue('AJ' . $kolom, 0);
            $sheet2->setCellValue('AK' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet2->setCellValue('AL' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet2->getStyle('AB2:AL' . $kolom - 1)->applyFromArray($style);
    }
    private function datasortir($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet(2);
        $sheet3->setTitle('Gudang Sortir');

        $sheet3->getStyle("B1:L1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'Sortir sedang proses');
        $sheet3->setCellValue('B1', 'partai');
        $sheet3->setCellValue('C1', 'pengawas');
        $sheet3->setCellValue('D1', 'no box');
        $sheet3->setCellValue('E1', 'pcs');
        $sheet3->setCellValue('F1', 'gr');
        $sheet3->setCellValue('G1', 'ttl rp bk');
        $sheet3->setCellValue('H1', 'cost kerja');
        $sheet3->setCellValue('I1', 'cost cu dll');
        $sheet3->setCellValue('J1', 'cost operasional');
        $sheet3->setCellValue('K1', 'ttl rp');
        $sheet3->setCellValue('L1', 'rp/gr');

        $cetak_proses = $model::sortir_proses();
        $kolom = 2;
        foreach ($cetak_proses  as $d) {
            $sheet3->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet3->setCellValue('C' . $kolom, $d->name);
            $sheet3->setCellValue('D' . $kolom, $d->no_box);
            $sheet3->setCellValue('E' . $kolom, $d->pcs);
            $sheet3->setCellValue('F' . $kolom, $d->gr);
            $sheet3->setCellValue('G' . $kolom, $d->ttl_rp);
            $sheet3->setCellValue('H' . $kolom, $d->cost_kerja);
            $sheet3->setCellValue('I' . $kolom, 0);
            $sheet3->setCellValue('J' . $kolom, 0);
            $sheet3->setCellValue('K' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet3->setCellValue('L' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet3->getStyle('B2:L' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("O1:Y1")->applyFromArray($style_atas);
        $sheet3->setCellValue('N1', 'Sortir sisa pengawas');
        $sheet3->setCellValue('O1', 'partai');
        $sheet3->setCellValue('P1', 'pengawas');
        $sheet3->setCellValue('Q1', 'no box');
        $sheet3->setCellValue('R1', 'pcs');
        $sheet3->setCellValue('S1', 'gr');
        $sheet3->setCellValue('T1', 'ttl rp bk');
        $sheet3->setCellValue('U1', 'cost kerja');
        $sheet3->setCellValue('V1', 'cost cu dll');
        $sheet3->setCellValue('W1', 'cost operasional');
        $sheet3->setCellValue('X1', 'ttl rp');
        $sheet3->setCellValue('Y1', 'rp/gr');

        $cetak_proses = $model::sortir_stock();
        $kolom = 2;
        foreach ($cetak_proses  as $d) {
            $sheet3->setCellValue('O' . $kolom, $d->nm_partai);
            $sheet3->setCellValue('P' . $kolom, $d->name);
            $sheet3->setCellValue('Q' . $kolom, $d->no_box);
            $sheet3->setCellValue('R' . $kolom, $d->pcs);
            $sheet3->setCellValue('S' . $kolom, $d->gr);
            $sheet3->setCellValue('T' . $kolom, $d->ttl_rp);
            $sheet3->setCellValue('U' . $kolom, $d->cost_kerja);
            $sheet3->setCellValue('V' . $kolom, 0);
            $sheet3->setCellValue('W' . $kolom, 0);
            $sheet3->setCellValue('X' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet3->setCellValue('Y' . $kolom, empty($d->gr) ? 0 : ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet3->getStyle('O2:Y' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("AB1:AL1")->applyFromArray($style_atas);
        $sheet3->setCellValue('AA1', 'Sortir selesai siap grading');
        $sheet3->setCellValue('AB1', 'partai');
        $sheet3->setCellValue('AC1', 'pengawas');
        $sheet3->setCellValue('AD1', 'no box');
        $sheet3->setCellValue('AE1', 'pcs');
        $sheet3->setCellValue('AF1', 'gr');
        $sheet3->setCellValue('AG1', 'ttl rp bk');
        $sheet3->setCellValue('AH1', 'cost kerja');
        $sheet3->setCellValue('AI1', 'cost cu dll');
        $sheet3->setCellValue('AJ1', 'cost operasional');
        $sheet3->setCellValue('AK1', 'ttl rp');
        $sheet3->setCellValue('AL1', 'rp/gr');

        $cetak_selesai = $model::sortir_selesai();
        $kolom = 2;
        foreach ($cetak_selesai  as $d) {
            $sheet3->setCellValue('AB' . $kolom, $d->nm_partai);
            $sheet3->setCellValue('AC' . $kolom, $d->name);
            $sheet3->setCellValue('AD' . $kolom, $d->no_box);
            $sheet3->setCellValue('AE' . $kolom, $d->pcs);
            $sheet3->setCellValue('AF' . $kolom, $d->gr);
            $sheet3->setCellValue('AG' . $kolom, $d->ttl_rp);
            $sheet3->setCellValue('AH' . $kolom, $d->cost_kerja);
            $sheet3->setCellValue('AI' . $kolom, 0);
            $sheet3->setCellValue('AJ' . $kolom, 0);
            $sheet3->setCellValue('AK' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet3->setCellValue('AL' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet3->getStyle('AB2:AL' . $kolom - 1)->applyFromArray($style);
    }

    private function datapengiriman($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(4);
        $sheet3 = $spreadsheet->getActiveSheet(4);
        $sheet3->setTitle('Pengiriman');

        $sheet3->getStyle("B1:L1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'Pengiriman');
        $sheet3->setCellValue('B1', 'Tanggal pengiriman');
        $sheet3->setCellValue('C1', 'no pengiriman');
        $sheet3->setCellValue('D1', 'grade');
        $sheet3->setCellValue('E1', 'pcs');
        $sheet3->setCellValue('F1', 'gr');
        $sheet3->setCellValue('G1', 'ttl rp');
        $sheet3->setCellValue('H1', 'cost kerja');
        $sheet3->setCellValue('I1', 'cost cu');
        $sheet3->setCellValue('J1', 'cost operasional');
        $sheet3->setCellValue('K1', 'total rp');
        $sheet3->setCellValue('L1', 'rp/gr');

        $pengiriman = DB::select("SELECT a.tgl_input, a.no_barcode, a.grade, a.pcs as pcs, a.gr as gr , 
        sum(a.cost_bk) as total_rp, sum(a.cost_kerja) as cost_kerja, sum(a.cost_cu) as cost_cu, sum(a.cost_op) as cost_op FROM pengiriman as a 
        left join  (
        SELECT sum(b.pcs) as pcs, sum(b.gr) as gr, b.box_pengiriman
        FROM grading_partai as b
        group by b.box_pengiriman
        ) as b on b.box_pengiriman = a.no_box
        group by a.id_pengiriman;");
        $kolom = 2;
        foreach ($pengiriman  as $d) {
            $sheet3->setCellValue('B' . $kolom, $d->tgl_input);
            $sheet3->setCellValue('C' . $kolom, $d->no_barcode);
            $sheet3->setCellValue('D' . $kolom, $d->grade);
            $sheet3->setCellValue('E' . $kolom, $d->pcs);
            $sheet3->setCellValue('F' . $kolom, $d->gr);
            $sheet3->setCellValue('G' . $kolom, $d->total_rp);
            $sheet3->setCellValue('H' . $kolom, $d->cost_kerja);
            $sheet3->setCellValue('I' . $kolom, $d->cost_cu);
            $sheet3->setCellValue('J' . $kolom, $d->cost_op);
            $sheet3->setCellValue('K' . $kolom, $d->total_rp + $d->cost_kerja + $d->cost_cu + $d->cost_op);
            $sheet3->setCellValue('L' . $kolom, ($d->total_rp + $d->cost_kerja + $d->cost_cu + $d->cost_op)  / $d->gr);
            $kolom++;
        }
        $sheet3->getStyle('B2:L' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("O1:Y1")->applyFromArray($style_atas);
        $sheet3->setCellValue('N1', 'Sisa belum kirim');
        $sheet3->setCellValue('O1', 'box grading');
        $sheet3->setCellValue('P1', 'pengawas');
        $sheet3->setCellValue('Q1', 'grade');
        $sheet3->setCellValue('R1', 'pcs');
        $sheet3->setCellValue('S1', 'gr');
        $sheet3->setCellValue('T1', 'ttl rp');
        $sheet3->setCellValue('U1', 'cost kerja');
        $sheet3->setCellValue('V1', 'cost cu');
        $sheet3->setCellValue('W1', 'cost operasional');
        $sheet3->setCellValue('X1', 'total rp');
        $sheet3->setCellValue('Y1', 'rp/gr');

        $grading = DB::select("SELECT * FROM `grading_partai` WHERE sudah_kirim = 'T'");
        $kolom = 2;
        foreach ($grading  as $d) {
            $sheet3->setCellValue('O' . $kolom, $d->box_pengiriman);
            $sheet3->setCellValue('P' . $kolom, $d->admin);
            $sheet3->setCellValue('Q' . $kolom, $d->grade);
            $sheet3->setCellValue('R' . $kolom, $d->pcs);
            $sheet3->setCellValue('S' . $kolom, $d->gr);
            $sheet3->setCellValue('T' . $kolom, $d->cost_bk);
            $sheet3->setCellValue('U' . $kolom, $d->cost_kerja);
            $sheet3->setCellValue('V' . $kolom, $d->cost_cu);
            $sheet3->setCellValue('W' . $kolom, $d->cost_op);
            $sheet3->setCellValue('X' . $kolom, $d->ttl_rp);
            $sheet3->setCellValue('Y' . $kolom, empty($d->gr) ? 0 : $d->ttl_rp / $d->gr);
            $kolom++;
        }
        $sheet3->getStyle('O2:Y' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("AB1:AE1")->applyFromArray($style_atas);
        $sheet3->setCellValue('AA1', 'selisih');
        $sheet3->setCellValue('AB1', 'pcs');
        $sheet3->setCellValue('AC1', 'gr');
        $sheet3->setCellValue('AD1', 'ttl rp');
        $sheet3->setCellValue('AE1', 'rp/gr');

        $sa = CocokanModel::akhir_sortir();
        $p2suntik = $this->getSuntikan(42);
        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $sa->pcs + $p2suntik->pcs;
        $sortir_akhir->gr = $sa->gr + $p2suntik->gr;
        $sortir_akhir->ttl_rp = $sa->ttl_rp + $p2suntik->ttl_rp;

        $pengiriman = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr FROM pengiriman as a ");
        $grading = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr FROM grading_partai as a ");
        $opname = $this->getSuntikan(41);
        $belum_grading = OpnameNewModel::grading_sisa();

        $kolom = 2;

        $sheet3->setCellValue('AB' . $kolom, round($sortir_akhir->pcs + $opname->pcs - $grading->pcs - sumBk($belum_grading, 'pcs'), 0));
        $sheet3->setCellValue('AC' . $kolom, 0);
        $sheet3->setCellValue('AD' . $kolom, 0);
        $sheet3->setCellValue('AE' . $kolom, 0);
        $sheet3->getStyle('AB2:AE2')->applyFromArray($style);
    }
    private function sortir_selesai($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet4 = $spreadsheet->getActiveSheet(3);
        $sheet4->setTitle('Sortir selesai');

        $cetak_selesai = $model::sortir_selesai_akhir();
        $model_cost = new CocokanModel();
        $cost_kerja = $this->getCost($model_cost, 1);
        $cost_op = $this->getCost($model_cost, 'cost_op');
        $ttl_gr =  sumBk($cetak_selesai, 'gr');
        $cost_cu =  sumBk($cetak_selesai, 'cost_cu');
        $s5suntik = $this->getSuntikan(35);
        $p2suntik = $this->getSuntikan(41);
        $tl_gr = $ttl_gr + $s5suntik->gr + $p2suntik->gr;
        $ttl_rp_operasional = $cost_op -  $cost_kerja - $cost_cu;
        $rp_gr_operasional = $ttl_rp_operasional / $tl_gr;



        $sheet4->getStyle("B1:N1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'Sortir Selesai');
        $sheet4->setCellValue('B1', 'partai');
        $sheet4->setCellValue('C1', 'no box');
        $sheet4->setCellValue('D1', 'tipe');
        $sheet4->setCellValue('E1', 'ket');
        $sheet4->setCellValue('F1', 'pengawas');
        $sheet4->setCellValue('G1', 'pcs');
        $sheet4->setCellValue('H1', 'gr');
        $sheet4->setCellValue('I1', 'ttl rp bk');
        $sheet4->setCellValue('J1', 'cost kerja');
        $sheet4->setCellValue('K1', 'cost cu');
        $sheet4->setCellValue('L1', 'cost operasional');
        $sheet4->setCellValue('M1', 'total rp');
        $sheet4->setCellValue('N1', 'rp/gr');


        $kolom = 2;
        foreach ($cetak_selesai  as $d) {
            $sheet4->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet4->setCellValue('C' . $kolom, $d->no_box);
            $sheet4->setCellValue('D' . $kolom, $d->tipe);
            $sheet4->setCellValue('E' . $kolom, $d->ket);
            $sheet4->setCellValue('F' . $kolom, $d->name);
            $sheet4->setCellValue('G' . $kolom, $d->pcs);
            $sheet4->setCellValue('H' . $kolom, $d->gr);
            $sheet4->setCellValue('I' . $kolom, $d->ttl_rp);
            $sheet4->setCellValue('J' . $kolom, $d->cost_kerja);
            $sheet4->setCellValue('K' . $kolom,  $d->cost_cu);
            $sheet4->setCellValue('L' . $kolom, $rp_gr_operasional * $d->gr);
            $sheet4->setCellValue('M' . $kolom, $d->ttl_rp + $d->cost_kerja + $d->cost_cu + ($rp_gr_operasional * $d->gr));
            $sheet4->setCellValue('N' . $kolom, ($d->ttl_rp + $d->cost_kerja + $d->cost_cu + ($rp_gr_operasional * $d->gr)) / $d->gr);
            $kolom++;
        }

        $sheet4->setCellValue('B' . $kolom, 'partai suntik');
        $sheet4->setCellValue('C' . $kolom, 'suntik');
        $sheet4->setCellValue('D' . $kolom, '-');
        $sheet4->setCellValue('E' . $kolom, '-');
        $sheet4->setCellValue('F' . $kolom, '-');
        $sheet4->setCellValue('G' . $kolom,  $s5suntik->pcs);
        $sheet4->setCellValue('H' . $kolom,  $s5suntik->gr);
        $sheet4->setCellValue('I' . $kolom,  $s5suntik->ttl_rp);
        $sheet4->setCellValue('J' . $kolom, 0);
        $sheet4->setCellValue('K' . $kolom,  0);
        $sheet4->setCellValue('L' . $kolom, $rp_gr_operasional * $s5suntik->gr);
        $sheet4->setCellValue('M' . $kolom, $s5suntik->ttl_rp + ($rp_gr_operasional * $s5suntik->gr));
        $sheet4->setCellValue('N' . $kolom, ($s5suntik->ttl_rp + ($rp_gr_operasional * $s5suntik->gr)) / $s5suntik->gr);




        $sheet4->setCellValue('B' . $kolom + 1, 'partai suntik pengiriman');
        $sheet4->setCellValue('C' . $kolom + 1, 'suntik');
        $sheet4->setCellValue('D' . $kolom + 1, '-');
        $sheet4->setCellValue('E' . $kolom + 1, '-');
        $sheet4->setCellValue('F' . $kolom + 1, '-');
        $sheet4->setCellValue('G' . $kolom + 1,  $p2suntik->pcs);
        $sheet4->setCellValue('H' . $kolom + 1,  $p2suntik->gr);
        $sheet4->setCellValue('I' . $kolom + 1,  $p2suntik->ttl_rp);
        $sheet4->setCellValue('J' . $kolom + 1, 0);
        $sheet4->setCellValue('K' . $kolom + 1,  0);
        $sheet4->setCellValue('L' . $kolom + 1, $rp_gr_operasional * $p2suntik->gr);
        $sheet4->setCellValue('M' . $kolom + 1, $p2suntik->ttl_rp + ($rp_gr_operasional * $p2suntik->gr));
        $sheet4->setCellValue('N' . $kolom + 1, ($p2suntik->ttl_rp + ($rp_gr_operasional * $p2suntik->gr)) / $p2suntik->gr);

        $sheet4->getStyle('B2:N' . $kolom + 1)->applyFromArray($style);
    }
    private function gudang_grading($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet4 = $spreadsheet->getActiveSheet(3);
        $sheet4->setTitle('Gudang grading');

        $sheet4->getStyle("B1:N1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'Sisa belum grading');
        $sheet4->setCellValue('B1', 'partai');
        $sheet4->setCellValue('C1', 'no box');
        $sheet4->setCellValue('D1', 'tipe');
        $sheet4->setCellValue('E1', 'ket');
        $sheet4->setCellValue('F1', 'pcs');
        $sheet4->setCellValue('G1', 'gr');
        $sheet4->setCellValue('H1', 'ttl rp bk');
        $sheet4->setCellValue('I1', 'cost kerja');
        $sheet4->setCellValue('J1', 'rp/gr');

        $belum_grading = OpnameNewModel::grading_sisa();
        $kolom = 2;
        foreach ($belum_grading  as $d) {
            $sheet4->setCellValue('B' . $kolom, $d->nm_partai ?? '-');
            $sheet4->setCellValue('C' . $kolom, $d->no_box_sortir);
            $sheet4->setCellValue('D' . $kolom, $d->tipe);
            $sheet4->setCellValue('E' . $kolom, $d->ket);
            $sheet4->setCellValue('F' . $kolom, $d->pcs);
            $sheet4->setCellValue('G' . $kolom, $d->gr);
            $sheet4->setCellValue('H' . $kolom, $d->cost_bk);
            $sheet4->setCellValue('I' . $kolom, $d->cost_kerja);
            $sheet4->setCellValue('J' . $kolom, ($d->cost_kerja + $d->cost_bk) / $d->gr);
            $kolom++;
        }
        $sheet4->getStyle('B2:J' . $kolom - 1)->applyFromArray($style);
    }

    private function rekap($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(5);
        $sheet4 = $spreadsheet->getActiveSheet(5);
        $sheet4->setTitle('Rekap');

        $sheet4->getStyle("A1:H1")->applyFromArray($style_atas);
        $sheet4->setCellValue('B1', 'pcs');
        $sheet4->setCellValue('C1', 'gr');
        $sheet4->setCellValue('D1', 'rp');
        $sheet4->setCellValue('E1', 'cost kerja');
        $sheet4->setCellValue('F1', 'cost cu');
        $sheet4->setCellValue('G1', 'cost operasional');
        $sheet4->setCellValue('H1', 'ttl rp');


        $sheet4->getStyle("A2:H14")->applyFromArray($style);
        $sheet4->setCellValue('A2', 'Cabut sedang proses');
        $sheet4->setCellValue('A3', 'Cabut sisa pengawas');
        $sheet4->setCellValue('A4', 'Cabut selesai siap cetak');
        $sheet4->setCellValue('A5', 'Cetak sedang proses');
        $sheet4->setCellValue('A6', 'Cetak sisa pengawas');
        $sheet4->setCellValue('A7', 'Cetak selesai siap sortir');
        $sheet4->setCellValue('A8', 'Sortir sedang proses');
        $sheet4->setCellValue('A9', 'Sortir sisa pengawas');
        $sheet4->setCellValue('A10', 'Sortir selesai siap grading');
        $sheet4->setCellValue('A11', 'Sisa belum grading');
        $sheet4->setCellValue('A12', 'Pengiriman');
        $sheet4->setCellValue('A13', 'Sisa belum kirim');
        $sheet4->setCellValue('A14', 'Selisih');

        $sheet4->setCellValue('B2', "=SUM('Gudang Cabut'!E:E)");
        $sheet4->setCellValue('B3', "=SUM('Gudang Cabut'!R:R)");
        $sheet4->setCellValue('B4', "0");
        $sheet4->setCellValue('B5', "=SUM('Gudang Cetak'!E:E)");
        $sheet4->setCellValue('B6', "=SUM('Gudang Cetak'!R:R)");
        $sheet4->setCellValue('B7', "=SUM('Gudang Cetak'!AE:AE)");
        $sheet4->setCellValue('B8', "=SUM('Gudang Sortir'!E:E)");
        $sheet4->setCellValue('B9', "=SUM('Gudang Sortir'!R:R)");
        $sheet4->setCellValue('B10', "=SUM('Gudang Sortir'!AE:AE)");
        $sheet4->setCellValue('B11', "=SUM('Gudang grading'!F:F)");
        $sheet4->setCellValue('B12', "=SUM('Pengiriman'!E:E)");
        $sheet4->setCellValue('B13', "=SUM('Pengiriman'!R:R)");
        $sheet4->setCellValue('B14', "=SUM(Pengiriman!AB:AB)");

        $sheet4->setCellValue('C2', "=SUM('Gudang Cabut'!F:F)");
        $sheet4->setCellValue('C3', "=SUM('Gudang Cabut'!S:S)");
        $sheet4->setCellValue('C4', "0");
        $sheet4->setCellValue('C5', "=SUM('Gudang Cetak'!F:F)");
        $sheet4->setCellValue('C6', "=SUM('Gudang Cetak'!S:S)");
        $sheet4->setCellValue('C7', "=SUM('Gudang Cetak'!AF:AF)");
        $sheet4->setCellValue('C8', "=SUM('Gudang Sortir'!F:F)");
        $sheet4->setCellValue('C9', "=SUM('Gudang Sortir'!S:S)");
        $sheet4->setCellValue('C10', "=SUM('Gudang Sortir'!AF:AF)");
        $sheet4->setCellValue('C11', "=SUM('Gudang grading'!G:G)");
        $sheet4->setCellValue('C12', "=SUM('Pengiriman'!F:F)");
        $sheet4->setCellValue('C13', "=SUM('Pengiriman'!S:S)");
        $sheet4->setCellValue('C14', "0");

        $sheet4->setCellValue('D2', "=SUM('Gudang Cabut'!G:G)");
        $sheet4->setCellValue('D3', "=SUM('Gudang Cabut'!T:T)");
        $sheet4->setCellValue('D4', "0");
        $sheet4->setCellValue('D5', "=SUM('Gudang Cetak'!G:G)");
        $sheet4->setCellValue('D6', "=SUM('Gudang Cetak'!T:T)");
        $sheet4->setCellValue('D7', "=SUM('Gudang Cetak'!AG:AG)");
        $sheet4->setCellValue('D8', "=SUM('Gudang Sortir'!G:G)");
        $sheet4->setCellValue('D9', "=SUM('Gudang Sortir'!T:T)");
        $sheet4->setCellValue('D10', "=SUM('Gudang Sortir'!AG:AG)");
        $sheet4->setCellValue('D11', "=SUM('Gudang grading'!H:H)");
        $sheet4->setCellValue('D12', "=SUM('Pengiriman'!G:G)");
        $sheet4->setCellValue('D13', "=SUM('Pengiriman'!T:T)");
        $sheet4->setCellValue('D14', "0");

        $sheet4->setCellValue('E2', "=SUM('Gudang Cabut'!H:H)");
        $sheet4->setCellValue('E3', "=SUM('Gudang Cabut'!U:U)");
        $sheet4->setCellValue('E4', "");
        $sheet4->setCellValue('E5', "=SUM('Gudang Cetak'!H:H) +  SUM('Gudang Cabut'!AH:AH)");
        $sheet4->setCellValue('E6', "=SUM('Gudang Cetak'!U:U)");
        $sheet4->setCellValue('E7', "=SUM('Gudang Cetak'!AH:AH)");
        $sheet4->setCellValue('E8', "=SUM('Gudang Sortir'!H:H)");
        $sheet4->setCellValue('E9', "=SUM('Gudang Sortir'!U:U)");
        $sheet4->setCellValue('E10', "=SUM('Gudang Sortir'!AG:AG)");
        $sheet4->setCellValue('E11', "=SUM('Gudang grading'!I:I)");
        $sheet4->setCellValue('E12', "=SUM('Pengiriman'!H:H)");
        $sheet4->setCellValue('E13', "=SUM('Pengiriman'!U:U)");
        $sheet4->setCellValue('E14', "0");

        $sheet4->setCellValue('F12', "=SUM('Pengiriman'!I:I)");
        $sheet4->setCellValue('F13', "=SUM('Pengiriman'!V:V)");
        $sheet4->setCellValue('G12', "=SUM('Pengiriman'!J:J)");
        $sheet4->setCellValue('G13', "=SUM('Pengiriman'!W:W)");

        $sheet4->setCellValue('H2', "=D2+E2+F2+G2");
        $sheet4->setCellValue('H3', "=D3+E3+F3+G3");
        $sheet4->setCellValue('H4', "0");
        $sheet4->setCellValue('H5', "=D5+E5+F5+G5");
        $sheet4->setCellValue('H6', "=D6+E6+F6+G6");
        $sheet4->setCellValue('H7', "=D7+E7+F7+G7");
        $sheet4->setCellValue('H8', "=D8+E8+F8+G8");
        $sheet4->setCellValue('H9', "=D9+E9+F9+G9");
        $sheet4->setCellValue('H10', "=D10+E10+F10+G10");
        $sheet4->setCellValue('H11', "=D11+E11+F11+G11");
        $sheet4->setCellValue('H12', "=D12+E12+F12+G12");
        $sheet4->setCellValue('H13', "=D13+E13+F13+G13");
        $sheet4->setCellValue('H14', "=D14+E14+F14+G14");

        $sheet4->getStyle("A15:H15")->applyFromArray($style_atas);
        $sheet4->setCellValue('A15', "Total");
        $sheet4->setCellValue('B15', "=SUM(B2:B14)");
        $sheet4->setCellValue('C15', "=SUM(C2:C14)");
        $sheet4->setCellValue('D15', "=SUM(D2:D14)");
        $sheet4->setCellValue('E15', "=SUM(E2:E14)");
        $sheet4->setCellValue('F15', "=SUM(F2:F14)");
        $sheet4->setCellValue('G15', "=SUM(G2:G14)");
        $sheet4->setCellValue('H15', "=SUM(H2:H14)");
    }
    private function bk_sinta($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(6);
        $sheet4 = $spreadsheet->getActiveSheet(6);
        $sheet4->setTitle('Bk Sinta');

        $sheet4->getStyle("A1:S1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'No');
        $sheet4->setCellValue('B1', 'bulan kerja');
        $sheet4->setCellValue('C1', 'nama partai');
        $sheet4->setCellValue('D1', 'grade');
        $sheet4->setCellValue('E1', 'pcs bk');
        $sheet4->setCellValue('F1', 'gr bk');
        $sheet4->setCellValue('G1', 'total rp bk');
        $sheet4->setCellValue('H1', 'rata2');
        $sheet4->setCellValue('I1', 'pcs diambil');
        $sheet4->setCellValue('J1', 'gr diambil');
        $sheet4->setCellValue('K1', 'ttl rp sudah diambil');
        $sheet4->setCellValue('L1', 'rata2');

        $sheet4->setCellValue('M1', 'pcs susut');
        $sheet4->setCellValue('N1', 'gr susut');
        $sheet4->setCellValue('O1', 'susut%');

        $sheet4->setCellValue('P1', 'pcs di sinta');
        $sheet4->setCellValue('Q1', 'gr di sinta');
        $sheet4->setCellValue('R1', 'total rp');
        $sheet4->setCellValue('S1', 'rata2');


        $bk_sinta = SummaryModel::summarybk();

        $kolom = 2;
        foreach ($bk_sinta  as $no => $b) {
            $sheet4->setCellValue('A' . $kolom, $no + 1);
            $sheet4->setCellValue('B' . $kolom, date('F Y', strtotime('01-' . $b->bulan . '-' . $b->tahun)));
            $sheet4->setCellValue('C' . $kolom, $b->nm_partai);
            $sheet4->setCellValue('D' . $kolom, $b->grade);
            $sheet4->setCellValue('E' . $kolom, $b->pcs);
            $sheet4->setCellValue('F' . $kolom, $b->gr);
            $sheet4->setCellValue('G' . $kolom, $b->ttl_rp);
            $sheet4->setCellValue('H' . $kolom, $b->ttl_rp / $b->gr);
            $sheet4->setCellValue('I' . $kolom, $b->pcs_bk);
            $sheet4->setCellValue('J' . $kolom, $b->gr_bk);
            $sheet4->setCellValue('K' . $kolom, $b->cost_bk);
            $sheet4->setCellValue('L' . $kolom, $b->cost_bk / $b->gr_bk);

            $sheet4->setCellValue('M' . $kolom, is_null($b->pcs_susut) ? 'belum selesai' : $b->pcs_susut);
            $sheet4->setCellValue('N' . $kolom, is_null($b->gr_susut) ? 'belum selesai' : $b->gr_susut);
            $sheet4->setCellValue('O' . $kolom, is_null($b->pcs_susut) ? 'belum selesai' : (1 - ($b->gr / $b->gr_bk)) * 100);





            $sheet4->setCellValue('P' . $kolom, "=IF(M$kolom =" . '"belum selesai"' . ",E$kolom-I$kolom,0)");
            $sheet4->setCellValue('Q' . $kolom, "=IF(N$kolom =" . '"belum selesai"' . ",F$kolom-J$kolom,0)");
            $sheet4->setCellValue('R' . $kolom, "=IF(O$kolom =" . '"belum selesai"' . ",G$kolom-K$kolom,0)");
            $sheet4->setCellValue('S' . $kolom, "=IF(M$kolom=" . '"belum selesai"' . ",R$kolom/Q$kolom,0)");
            $kolom++;
        }
        $sheet4->getStyle('A2:S' . $kolom - 1)->applyFromArray($style);

        // $style2 = [
        //     'fill' => [
        //         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //         'startColor' => [
        //             'argb' => 'FFFF00', // Contoh warna kuning
        //         ],
        //     ],
        // ];

        // $sheet4->getStyle('I1:P' . $kolom - 1)->applyFromArray($style2);
    }
    private function lis_pengiriman($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(7);
        $sheet4 = $spreadsheet->getActiveSheet(7);
        $sheet4->setTitle('List Pengiriman');

        $sheet4->getStyle("A1:I1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'No');
        $sheet4->setCellValue('B1', 'tgl kirim');
        $sheet4->setCellValue('C1', 'no packing list');
        $sheet4->setCellValue('D1', 'nama packing list');
        $sheet4->setCellValue('E1', 'tujuan');
        $sheet4->setCellValue('F1', 'box');
        $sheet4->setCellValue('G1', 'pcs');
        $sheet4->setCellValue('H1', 'gr');
        $sheet4->setCellValue('I1', 'gr + kadar');

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
            $kolom++;
        }
        $sheet4->getStyle('A2:I' . $kolom - 1)->applyFromArray($style);
    }




    public function getSuntikan($index)
    {
        $datas = [
            11 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_cbt_awal'"),
            14  => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_siap_cetak_diserahkan'"),
            16  => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_eo_diserahkan'"),
            26 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_serah'"),
            21 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_awal_stock' and opname = 'Y'"),
            22 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_awal_stock' "),
            23 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_awal_stock' and opname = 'T'"),
            24 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_selesai_siap_sortir_diserahkan' and opname = 'T'"),
            27 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_sisa'"),
            31 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_stok_awal' and opname = 'Y'"),
            32 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_stok_awal' and opname = 'T'"),
            35 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_selesai_diserahkan'"),
            41 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'grading' and opname = 'Y'"),
            42 => DB::selectOne("SELECT sum(pcs) as pcs, sum(gr) as gr, sum(ttl_rp) as ttl_rp FROM `opname_suntik` WHERE ket ='grading' and opname = 'T';"),
            // 43 => DB::selectOne("SELECT sum(pcs) as pcs, sum(gr) as gr, sum(ttl_rp) as ttl_rp FROM `opname_suntik` WHERE ket ='cetak_selesai' and opname = 'T';"),
        ];
        if (array_key_exists($index, $datas)) {
            return $datas[$index];
        } else {
            return false;
        }
    }

    public function getCost(CocokanModel $model, $index)
    {
        $a14suntik = $this->getSuntikan(14);
        $a16suntik = $this->getSuntikan(16);
        $a12 = $model::bkselesai_siap_ctk_diserahkan_sum();

        $bk_akhir = new stdClass();
        $bk_akhir->pcs = $a12->pcs + $a14suntik->pcs + $a16suntik->pcs;
        $bk_akhir->gr = $a12->gr + $a14suntik->gr + $a16suntik->gr;
        $bk_akhir->ttl_rp = $a12->ttl_rp + $a14suntik->ttl_rp + $a16suntik->ttl_rp;
        $bk_akhir->cost_kerja = $a12->cost_kerja;

        $ca16suntik = $this->getSuntikan(26);
        $ca16 = $model::cetak_selesai();
        $cetak_akhir = new stdClass();
        $cetak_akhir->pcs = $ca16->pcs + $ca16suntik->pcs;
        $cetak_akhir->gr = $ca16->gr + $ca16suntik->gr;
        $cetak_akhir->ttl_rp = $ca16->ttl_rp + $ca16suntik->ttl_rp;
        $cetak_akhir->cost_kerja = $ca16->cost_kerja;


        $s3 = $model::sortir_akhir();
        $s5suntik = $this->getSuntikan(35);

        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
        $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp;

        $gr_akhir_all = $a12->gr + $a14suntik->gr + $a16suntik->gr + $ca16->gr + $ca16suntik->gr + $s3->gr + $s5suntik->gr;
        $ttl_cost_kerja = $a12->cost_kerja  +  $ca16->cost_kerja +  $s3->cost_kerja;



        $uang_cost = DB::select("SELECT a.* FROM oprasional as a");
        $ttl_cost_op = sumBk($uang_cost, 'total_operasional');





        $cost_dll = DB::selectOne("SELECT sum(`dll`) as dll, max(bulan_dibayar) as bulan FROM `tb_gaji_penutup`");
        $bulan = $cost_dll->bulan;
        $cost_cu = DB::selectOne("SELECT sum(a.ttl_rp) as cost_cu
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori ='CU' and a.bulan_dibayar BETWEEN '6' and '$bulan';");
        $denda = DB::selectOne("SELECT sum(`nominal`) as ttl_denda FROM `tb_denda` WHERE `bulan_dibayar` BETWEEN '6' and '$bulan';");

        $ttl_semua = $ttl_cost_kerja + $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda;
        $dll = $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda;
        $cost_op = $ttl_cost_op - $ttl_semua;


        $datas = [
            1 => $ttl_cost_kerja,
            'ttl_gr' => $gr_akhir_all,
            'dll' => $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda,
            'cost_op' => $ttl_cost_op
        ];
        if (array_key_exists($index, $datas)) {
            return $datas[$index];
        } else {
            return false;
        }
    }
}
