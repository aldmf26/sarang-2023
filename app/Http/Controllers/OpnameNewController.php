<?php

namespace App\Http\Controllers;

use App\Models\CocokanModel;
use App\Models\OpnameNewModel;
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
            $sheet1->setCellValue('AI' . $kolom, $d->cost_dll);
            $sheet1->setCellValue('AJ' . $kolom, $d->cost_op);
            $sheet1->setCellValue('AK' . $kolom, $d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op);
            $sheet1->setCellValue('AL' . $kolom, ($d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op) / $d->gr);
            $kolom++;
        }
        $sheet1->getStyle('AB2:AL' . $kolom - 1)->applyFromArray($style);


        $this->datacetak($spreadsheet, $style_atas, $style, $model);
        $this->datasortir($spreadsheet, $style_atas, $style, $model);
        $this->datapengiriman($spreadsheet, $style_atas, $style, $model);


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
        $kolom = 2;
        foreach ($cetak_proses  as $d) {
            $sheet2->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet2->setCellValue('C' . $kolom, $d->name);
            $sheet2->setCellValue('D' . $kolom, $d->no_box);
            $sheet2->setCellValue('E' . $kolom, $d->pcs);
            $sheet2->setCellValue('F' . $kolom, $d->gr);
            $sheet2->setCellValue('G' . $kolom, $d->ttl_rp);
            $sheet2->setCellValue('H' . $kolom, $d->cost_kerja);
            $sheet2->setCellValue('I' . $kolom, $d->cost_dll);
            $sheet2->setCellValue('J' . $kolom, $d->cost_op);
            $sheet2->setCellValue('K' . $kolom, $d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op);
            $sheet2->setCellValue('L' . $kolom, ($d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op) / $d->gr);
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
            $sheet2->setCellValue('V' . $kolom, $d->cost_dll);
            $sheet2->setCellValue('W' . $kolom, $d->cost_op);
            $sheet2->setCellValue('X' . $kolom, $d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op);
            $sheet2->setCellValue('Y' . $kolom, ($d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op) / $d->gr);
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
            $sheet2->setCellValue('AI' . $kolom, $d->cost_dll);
            $sheet2->setCellValue('AJ' . $kolom, $d->cost_op);
            $sheet2->setCellValue('AK' . $kolom, $d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op);
            $sheet2->setCellValue('AL' . $kolom, ($d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op) / $d->gr);
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
            $sheet3->setCellValue('I' . $kolom, $d->cost_dll);
            $sheet3->setCellValue('J' . $kolom, $d->cost_op);
            $sheet3->setCellValue('K' . $kolom, $d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op);
            $sheet3->setCellValue('L' . $kolom, ($d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op) / $d->gr);
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
            $sheet3->setCellValue('V' . $kolom, $d->cost_dll);
            $sheet3->setCellValue('W' . $kolom, $d->cost_op);
            $sheet3->setCellValue('X' . $kolom, $d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op);
            $sheet3->setCellValue('Y' . $kolom, ($d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op) / $d->gr);
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
            $sheet3->setCellValue('AI' . $kolom, $d->cost_dll);
            $sheet3->setCellValue('AJ' . $kolom, $d->cost_op);
            $sheet3->setCellValue('AK' . $kolom, $d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op);
            $sheet3->setCellValue('AL' . $kolom, ($d->ttl_rp + $d->cost_kerja + $d->cost_dll + $d->cost_op) / $d->gr);
            $kolom++;
        }
        $sheet3->getStyle('AB2:AL' . $kolom - 1)->applyFromArray($style);
    }

    private function datapengiriman($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet3 = $spreadsheet->getActiveSheet(3);
        $sheet3->setTitle('Gudang grading & pengiriman');

        $sheet3->getStyle("B1:L1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'Pengiriman');
        $sheet3->setCellValue('B1', 'Tanggal pengiriman');
        $sheet3->setCellValue('C1', 'no pengiriman');
        $sheet3->setCellValue('D1', 'grade');
        $sheet3->setCellValue('E1', 'pcs');
        $sheet3->setCellValue('F1', 'gr');
        $sheet3->setCellValue('G1', 'ttl rp bk');
        $sheet3->setCellValue('H1', 'cost kerja');
        $sheet3->setCellValue('I1', 'cost cu dll');
        $sheet3->setCellValue('J1', 'cost operasional');
        $sheet3->setCellValue('K1', 'ttl rp');
        $sheet3->setCellValue('L1', 'rp/gr');

        $pengiriman = DB::select("SELECT a.tgl_input, a.no_barcode, a.grade, sum(a.pcs) as pcs, sum(a.gr) as gr 
        FROM pengiriman as a 
        group by a.id_pengiriman;");
        $kolom = 2;
        foreach ($pengiriman  as $d) {
            $sheet3->setCellValue('B' . $kolom, $d->tgl_input);
            $sheet3->setCellValue('C' . $kolom, $d->no_barcode);
            $sheet3->setCellValue('D' . $kolom, $d->grade);
            $sheet3->setCellValue('E' . $kolom, $d->pcs);
            $sheet3->setCellValue('F' . $kolom, $d->gr);
            $sheet3->setCellValue('G' . $kolom, 0);
            $sheet3->setCellValue('H' . $kolom, 0);
            $sheet3->setCellValue('I' . $kolom, 0);
            $sheet3->setCellValue('J' . $kolom, 0);
            $sheet3->setCellValue('K' . $kolom, 0);
            $sheet3->setCellValue('L' . $kolom, 0);
            $kolom++;
        }
        $sheet3->getStyle('B2:L' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("O1:Y1")->applyFromArray($style_atas);
        $sheet3->setCellValue('N1', 'Sisa grading');
        $sheet3->setCellValue('O1', 'box grading');
        $sheet3->setCellValue('P1', 'pengawas');
        $sheet3->setCellValue('Q1', 'grade');
        $sheet3->setCellValue('R1', 'pcs');
        $sheet3->setCellValue('S1', 'gr');
        $sheet3->setCellValue('T1', 'ttl rp bk');
        $sheet3->setCellValue('U1', 'cost kerja');
        $sheet3->setCellValue('V1', 'cost cu dll');
        $sheet3->setCellValue('W1', 'cost operasional');
        $sheet3->setCellValue('X1', 'ttl rp');
        $sheet3->setCellValue('Y1', 'rp/gr');

        $grading = DB::select("SELECT * FROM `grading_partai` WHERE `box_pengiriman` not in(SELECT a.no_box FROM pengiriman as a )");
        $kolom = 2;
        foreach ($grading  as $d) {
            $sheet3->setCellValue('O' . $kolom, $d->box_pengiriman);
            $sheet3->setCellValue('P' . $kolom, $d->admin);
            $sheet3->setCellValue('Q' . $kolom, $d->grade);
            $sheet3->setCellValue('R' . $kolom, $d->pcs);
            $sheet3->setCellValue('S' . $kolom, $d->gr);
            $sheet3->setCellValue('T' . $kolom, 0);
            $sheet3->setCellValue('U' . $kolom, 0);
            $sheet3->setCellValue('V' . $kolom, 0);
            $sheet3->setCellValue('W' . $kolom, 0);
            $sheet3->setCellValue('X' . $kolom, 0);
            $sheet3->setCellValue('Y' . $kolom, 0);
            $kolom++;
        }
        $sheet3->getStyle('O2:Y' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("AB1:AI1")->applyFromArray($style_atas);
        $sheet3->setCellValue('AA1', 'selisih');
        $sheet3->setCellValue('AB1', 'pcs');
        $sheet3->setCellValue('AC1', 'gr');
        $sheet3->setCellValue('AD1', 'ttl rp bk');
        $sheet3->setCellValue('AE1', 'cost kerja');
        $sheet3->setCellValue('AF1', 'cost cu dll');
        $sheet3->setCellValue('AG1', 'cost operasional');
        $sheet3->setCellValue('AH1', 'ttl rp');
        $sheet3->setCellValue('AI1', 'rp/gr');

        $sa = CocokanModel::akhir_sortir();
        $p2suntik = $this->getSuntikan(42);
        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $sa->pcs + $p2suntik->pcs;
        $sortir_akhir->gr = $sa->gr + $p2suntik->gr;
        $sortir_akhir->ttl_rp = $sa->ttl_rp + $p2suntik->ttl_rp;

        $pengiriman = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr FROM pengiriman as a ");
        $grading = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr FROM grading_partai as a ");
        $opname = $this->getSuntikan(41);

        $kolom = 2;

        $sheet3->setCellValue('AB' . $kolom, round($sortir_akhir->pcs + $opname->pcs - $grading->pcs, 0));
        $sheet3->setCellValue('AC' . $kolom, round($sortir_akhir->gr + $opname->gr - $grading->gr, 0));
        $sheet3->setCellValue('AD' . $kolom, 0);
        $sheet3->setCellValue('AE' . $kolom, 0);
        $sheet3->setCellValue('AF' . $kolom, 0);
        $sheet3->setCellValue('AG' . $kolom, 0);
        $sheet3->setCellValue('AH' . $kolom, 0);
        $sheet3->setCellValue('AI' . $kolom, 0);


        $sheet3->getStyle('AB2:AI2')->applyFromArray($style);
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
}
