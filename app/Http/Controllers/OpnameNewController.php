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

        $sheet1->getStyle("B1:M1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'Cabut sedang proses');
        $sheet1->setCellValue('B1', 'partai');
        $sheet1->setCellValue('C1', 'pengawas');
        $sheet1->setCellValue('D1', 'no box');
        $sheet1->setCellValue('E1', 'grade');
        $sheet1->setCellValue('F1', 'pcs');
        $sheet1->setCellValue('G1', 'gr');
        $sheet1->setCellValue('H1', 'ttl rp bk');
        $sheet1->setCellValue('I1', 'cost kerja');
        $sheet1->setCellValue('J1', 'cost cu dll');
        $sheet1->setCellValue('K1', 'cost operasional');
        $sheet1->setCellValue('L1', 'ttl rp');
        $sheet1->setCellValue('M1', 'rp/gr');

        $gudangbk = $model::bksedang_proses_sum();

        $kolom = 2;
        foreach ($gudangbk as $d) {
            $sheet1->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('C' . $kolom, $d->name);
            $sheet1->setCellValue('D' . $kolom, $d->no_box);
            $sheet1->setCellValue('E' . $kolom, $d->grade);
            $sheet1->setCellValue('F' . $kolom, $d->pcs);
            $sheet1->setCellValue('G' . $kolom, $d->gr);
            $sheet1->setCellValue('H' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('I' . $kolom, 0);
            $sheet1->setCellValue('J' . $kolom, 0);
            $sheet1->setCellValue('K' . $kolom, 0);
            $sheet1->setCellValue('L' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('M' . $kolom, $d->ttl_rp / $d->gr);
            $kolom++;
        }

        $sheet1->getStyle('B2:M' . $kolom - 1)->applyFromArray($style);

        $sheet1->getStyle("O1:Z1")->applyFromArray($style_atas);
        $sheet1->setCellValue('N1', 'Cabut sisa pengawas');
        $sheet1->setCellValue('O1', 'partai');
        $sheet1->setCellValue('P1', 'pengawas');
        $sheet1->setCellValue('Q1', 'no box');
        $sheet1->setCellValue('R1', 'grade');
        $sheet1->setCellValue('S1', 'pcs');
        $sheet1->setCellValue('T1', 'gr');
        $sheet1->setCellValue('U1', 'ttl rp bk');
        $sheet1->setCellValue('V1', 'cost kerja');
        $sheet1->setCellValue('W1', 'cost cu dll');
        $sheet1->setCellValue('X1', 'cost operasional');
        $sheet1->setCellValue('Y1', 'ttl rp');
        $sheet1->setCellValue('Z1', 'rp/gr');

        $gudangbksisa = $model::bksisapgws();

        $kolom = 2;
        foreach ($gudangbksisa as $d) {
            $sheet1->setCellValue('O' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('P' . $kolom, $d->name ?? 'sinta');
            $sheet1->setCellValue('Q' . $kolom, $d->no_box);
            $sheet1->setCellValue('R' . $kolom, $d->grade);
            $sheet1->setCellValue('S' . $kolom, $d->pcs);
            $sheet1->setCellValue('T' . $kolom, $d->gr);
            $sheet1->setCellValue('U' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('V' . $kolom, 0);
            $sheet1->setCellValue('W' . $kolom, 0);
            $sheet1->setCellValue('X' . $kolom, 0);
            $sheet1->setCellValue('Y' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('Z' . $kolom, $d->ttl_rp / $d->gr);
            $kolom++;
        }

        $sheet1->getStyle('O2:Z' . $kolom - 1)->applyFromArray($style);


        $sheet1->getStyle("AB1:AM1")->applyFromArray($style_atas);
        $sheet1->setCellValue('AA1', 'Cabut selesai siap cetak');
        $sheet1->setCellValue('AB1', 'partai');
        $sheet1->setCellValue('AC1', 'pengawas');
        $sheet1->setCellValue('AD1', 'no box');
        $sheet1->setCellValue('AE1', 'grade');
        $sheet1->setCellValue('AF1', 'pcs');
        $sheet1->setCellValue('AG1', 'gr');
        $sheet1->setCellValue('AH1', 'ttl rp bk');
        $sheet1->setCellValue('AI1', 'cost kerja');
        $sheet1->setCellValue('AJ1', 'cost cu dll');
        $sheet1->setCellValue('AK1', 'cost operasional');
        $sheet1->setCellValue('AL1', 'ttl rp');
        $sheet1->setCellValue('AM1', 'rp/gr');

        $gudangbkselesai = $model::bksedang_selesai_sum();

        $kolom = 2;
        foreach ($gudangbkselesai as $d) {
            $sheet1->setCellValue('AB' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('AC' . $kolom, $d->name);
            $sheet1->setCellValue('AD' . $kolom, $d->no_box);
            $sheet1->setCellValue('AE' . $kolom, $d->grade);
            $sheet1->setCellValue('AF' . $kolom, $d->pcs);
            $sheet1->setCellValue('AG' . $kolom, $d->gr);
            $sheet1->setCellValue('AH' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('AI' . $kolom, $d->cost_kerja);
            $sheet1->setCellValue('AJ' . $kolom, 0);
            $sheet1->setCellValue('AK' . $kolom, 0);
            $sheet1->setCellValue('AL' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet1->setCellValue('AM' . $kolom, empty($d->no_box) ? 0 : ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet1->getStyle('AB2:AM' . $kolom - 1)->applyFromArray($style);


        $this->datacetak($spreadsheet, $style_atas, $style, $model);
        $this->datasortir($spreadsheet, $style_atas, $style, $model);

        $this->gudang_grading($spreadsheet, $style_atas, $style, $model);
        $this->datapengiriman($spreadsheet, $style_atas, $style, $model);
        $this->rekap($spreadsheet, $style_atas, $style, $model);
        $this->bk_sinta($spreadsheet, $style_atas, $style, $model);
        $this->lis_pengiriman($spreadsheet, $style_atas, $style, $model);
        $this->rekapPengawas($spreadsheet, $style_atas, $style, $model);

        $this->sortir_selesai($spreadsheet, $style_atas, $style, $model);

        $namafile = "Opname Gudang.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save('php://output');
        exit();
    }


    private function datacetak($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet(1);
        $sheet2->setTitle('Gudang Cetak');

        $sheet2->getStyle("B1:M1")->applyFromArray($style_atas);
        $sheet2->setCellValue('A1', 'Cetak sedang proses');
        $sheet2->setCellValue('B1', 'partai');
        $sheet2->setCellValue('C1', 'pengawas');
        $sheet2->setCellValue('D1', 'no box');
        $sheet2->setCellValue('E1', 'grade');
        $sheet2->setCellValue('F1', 'pcs');
        $sheet2->setCellValue('G1', 'gr');
        $sheet2->setCellValue('H1', 'ttl rp bk');
        $sheet2->setCellValue('I1', 'cost kerja');
        $sheet2->setCellValue('J1', 'cost cu dll');
        $sheet2->setCellValue('K1', 'cost operasional');
        $sheet2->setCellValue('L1', 'ttl rp');
        $sheet2->setCellValue('M1', 'rp/gr');

        $cetak_proses = $model::cetak_proses();
        // kena diatas dihapus 
        $kolom = 2;
        foreach ($cetak_proses  as $d) {
            $sheet2->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet2->setCellValue('C' . $kolom, $d->name);
            $sheet2->setCellValue('D' . $kolom, $d->no_box);
            $sheet2->setCellValue('E' . $kolom, $d->grade);
            $sheet2->setCellValue('F' . $kolom, $d->pcs);
            $sheet2->setCellValue('G' . $kolom, $d->gr);
            $sheet2->setCellValue('H' . $kolom, $d->ttl_rp);
            $sheet2->setCellValue('I' . $kolom, $d->cost_kerja);
            $sheet2->setCellValue('J' . $kolom, 0);
            $sheet2->setCellValue('K' . $kolom, 0);
            $sheet2->setCellValue('L' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet2->setCellValue('M' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet2->getStyle('B2:M' . $kolom - 1)->applyFromArray($style);

        $sheet2->getStyle("O1:Z1")->applyFromArray($style_atas);
        $sheet2->setCellValue('N1', 'Cetak sisa pengawas');
        $sheet2->setCellValue('O1', 'partai');
        $sheet2->setCellValue('P1', 'pengawas');
        $sheet2->setCellValue('Q1', 'no box');
        $sheet2->setCellValue('R1', 'grade');
        $sheet2->setCellValue('S1', 'pcs');
        $sheet2->setCellValue('T1', 'gr');
        $sheet2->setCellValue('U1', 'ttl rp bk');
        $sheet2->setCellValue('V1', 'cost kerja');
        $sheet2->setCellValue('W1', 'cost cu dll');
        $sheet2->setCellValue('X1', 'cost operasional');
        $sheet2->setCellValue('Y1', 'ttl rp');
        $sheet2->setCellValue('Z1', 'rp/gr');

        $cetak_proses = $model::cetak_stok();
        $kolom = 2;
        foreach ($cetak_proses  as $d) {
            $sheet2->setCellValue('O' . $kolom, $d->nm_partai);
            $sheet2->setCellValue('P' . $kolom, $d->name);
            $sheet2->setCellValue('Q' . $kolom, $d->no_box);
            $sheet2->setCellValue('R' . $kolom, $d->grade);
            $sheet2->setCellValue('S' . $kolom, $d->pcs);
            $sheet2->setCellValue('T' . $kolom, $d->gr);
            $sheet2->setCellValue('U' . $kolom, $d->ttl_rp);
            $sheet2->setCellValue('V' . $kolom, $d->cost_kerja);
            $sheet2->setCellValue('W' . $kolom, 0);
            $sheet2->setCellValue('X' . $kolom, 0);
            $sheet2->setCellValue('Y' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet2->setCellValue('Z' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet2->getStyle('O2:Z' . $kolom - 1)->applyFromArray($style);

        $sheet2->getStyle("AB1:AM1")->applyFromArray($style_atas);
        $sheet2->setCellValue('AA1', 'Cetak selesai siap sortir');
        $sheet2->setCellValue('AB1', 'partai');
        $sheet2->setCellValue('AC1', 'pengawas');
        $sheet2->setCellValue('AD1', 'no box');
        $sheet2->setCellValue('AE1', 'grade');
        $sheet2->setCellValue('AF1', 'pcs');
        $sheet2->setCellValue('AG1', 'gr');
        $sheet2->setCellValue('AH1', 'ttl rp bk');
        $sheet2->setCellValue('AI1', 'cost kerja');
        $sheet2->setCellValue('AJ1', 'cost cu dll');
        $sheet2->setCellValue('AK1', 'cost operasional');
        $sheet2->setCellValue('AL1', 'ttl rp');
        $sheet2->setCellValue('AM1', 'rp/gr');

        $cetak_selesai = $model::cetak_selesai();
        $kolom = 2;
        foreach ($cetak_selesai  as $d) {
            $sheet2->setCellValue('AB' . $kolom, $d->nm_partai);
            $sheet2->setCellValue('AC' . $kolom, $d->name);
            $sheet2->setCellValue('AD' . $kolom, $d->no_box);
            $sheet2->setCellValue('AE' . $kolom, $d->grade);
            $sheet2->setCellValue('AF' . $kolom, $d->pcs);
            $sheet2->setCellValue('AG' . $kolom, $d->gr);
            $sheet2->setCellValue('AH' . $kolom, $d->ttl_rp);
            $sheet2->setCellValue('AI' . $kolom, $d->cost_kerja);
            $sheet2->setCellValue('AJ' . $kolom, 0);
            $sheet2->setCellValue('AK' . $kolom, 0);
            $sheet2->setCellValue('AL' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet2->setCellValue('AM' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet2->getStyle('AB2:AM' . $kolom - 1)->applyFromArray($style);
    }
    private function datasortir($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet(2);
        $sheet3->setTitle('Gudang Sortir');

        $sheet3->getStyle("B1:M1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'Sortir sedang proses');
        $sheet3->setCellValue('B1', 'partai');
        $sheet3->setCellValue('C1', 'pengawas');
        $sheet3->setCellValue('D1', 'no box');
        $sheet3->setCellValue('E1', 'grade');
        $sheet3->setCellValue('F1', 'pcs');
        $sheet3->setCellValue('G1', 'gr');
        $sheet3->setCellValue('H1', 'ttl rp bk');
        $sheet3->setCellValue('I1', 'cost kerja');
        $sheet3->setCellValue('J1', 'cost cu dll');
        $sheet3->setCellValue('K1', 'cost operasional');
        $sheet3->setCellValue('L1', 'ttl rp');
        $sheet3->setCellValue('M1', 'rp/gr');

        $cetak_proses = $model::sortir_proses();
        $kolom = 2;
        foreach ($cetak_proses  as $d) {
            $sheet3->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet3->setCellValue('C' . $kolom, $d->name);
            $sheet3->setCellValue('D' . $kolom, $d->no_box);
            $sheet3->setCellValue('E' . $kolom, $d->grade);
            $sheet3->setCellValue('F' . $kolom, $d->pcs);
            $sheet3->setCellValue('G' . $kolom, $d->gr);
            $sheet3->setCellValue('H' . $kolom, $d->ttl_rp);
            $sheet3->setCellValue('I' . $kolom, $d->cost_kerja);
            $sheet3->setCellValue('J' . $kolom, 0);
            $sheet3->setCellValue('K' . $kolom, 0);
            $sheet3->setCellValue('L' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet3->setCellValue('M' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet3->getStyle('B2:M' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("O1:Z1")->applyFromArray($style_atas);
        $sheet3->setCellValue('N1', 'Sortir sisa pengawas');
        $sheet3->setCellValue('O1', 'partai');
        $sheet3->setCellValue('P1', 'pengawas');
        $sheet3->setCellValue('Q1', 'no box');
        $sheet3->setCellValue('R1', 'grade');
        $sheet3->setCellValue('S1', 'pcs');
        $sheet3->setCellValue('T1', 'gr');
        $sheet3->setCellValue('U1', 'ttl rp bk');
        $sheet3->setCellValue('V1', 'cost kerja');
        $sheet3->setCellValue('W1', 'cost cu dll');
        $sheet3->setCellValue('X1', 'cost operasional');
        $sheet3->setCellValue('Y1', 'ttl rp');
        $sheet3->setCellValue('Z1', 'rp/gr');

        $cetak_proses = $model::sortir_stock();
        $kolom = 2;
        foreach ($cetak_proses  as $d) {
            $sheet3->setCellValue('O' . $kolom, $d->nm_partai);
            $sheet3->setCellValue('P' . $kolom, $d->name);
            $sheet3->setCellValue('Q' . $kolom, $d->no_box);
            $sheet3->setCellValue('R' . $kolom, $d->grade);
            $sheet3->setCellValue('S' . $kolom, $d->pcs);
            $sheet3->setCellValue('T' . $kolom, $d->gr);
            $sheet3->setCellValue('U' . $kolom, $d->ttl_rp);
            $sheet3->setCellValue('V' . $kolom, $d->cost_kerja);
            $sheet3->setCellValue('W' . $kolom, 0);
            $sheet3->setCellValue('X' . $kolom, 0);
            $sheet3->setCellValue('Y' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet3->setCellValue('Z' . $kolom, empty($d->gr) ? 0 : ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet3->getStyle('O2:Z' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("AB1:AM1")->applyFromArray($style_atas);
        $sheet3->setCellValue('AA1', 'Sortir selesai siap grading');
        $sheet3->setCellValue('AB1', 'partai');
        $sheet3->setCellValue('AC1', 'pengawas');
        $sheet3->setCellValue('AD1', 'no box');
        $sheet3->setCellValue('AE1', 'grade');
        $sheet3->setCellValue('AF1', 'pcs');
        $sheet3->setCellValue('AG1', 'gr');
        $sheet3->setCellValue('AH1', 'ttl rp bk');
        $sheet3->setCellValue('AI1', 'cost kerja');
        $sheet3->setCellValue('AJ1', 'cost cu dll');
        $sheet3->setCellValue('AK1', 'cost operasional');
        $sheet3->setCellValue('AL1', 'ttl rp');
        $sheet3->setCellValue('AM1', 'rp/gr');

        $cetak_selesai = $model::sortir_selesai();
        $kolom = 2;
        foreach ($cetak_selesai  as $d) {
            $sheet3->setCellValue('AB' . $kolom, $d->nm_partai);
            $sheet3->setCellValue('AC' . $kolom, $d->name);
            $sheet3->setCellValue('AD' . $kolom, $d->no_box);
            $sheet3->setCellValue('AE' . $kolom, $d->grade);
            $sheet3->setCellValue('AF' . $kolom, $d->pcs);
            $sheet3->setCellValue('AG' . $kolom, $d->gr);
            $sheet3->setCellValue('AH' . $kolom, $d->ttl_rp);
            $sheet3->setCellValue('AI' . $kolom, $d->cost_kerja);
            $sheet3->setCellValue('AJ' . $kolom, 0);
            $sheet3->setCellValue('AK' . $kolom, 0);
            $sheet3->setCellValue('AL' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet3->setCellValue('AM' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr);
            $kolom++;
        }
        $sheet3->getStyle('AB2:AM' . $kolom - 1)->applyFromArray($style);
    }

    private function datapengiriman($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(4);
        $sheet3 = $spreadsheet->getActiveSheet(4);
        $sheet3->setTitle('Pengiriman');

        $sheet3->getStyle("B1:L1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'Pengiriman');
        $sheet3->setCellValue('B1', 'nama partai');
        $sheet3->setCellValue('C1', 'box pengiriman');
        $sheet3->setCellValue('D1', 'grade');
        $sheet3->setCellValue('E1', 'pcs');
        $sheet3->setCellValue('F1', 'gr');
        $sheet3->setCellValue('G1', 'ttl rp');
        $sheet3->setCellValue('H1', 'cost kerja');
        $sheet3->setCellValue('I1', 'cost cu');
        $sheet3->setCellValue('J1', 'cost operasional');
        $sheet3->setCellValue('K1', 'total rp');
        $sheet3->setCellValue('L1', 'rp/gr');

        $pengiriman = Grading::pengirimanBalanceDetails();
        $kolom = 2;

        foreach ($pengiriman as $b) {
            $sheet3->setCellValue('B' . $kolom, $b->nm_partai);
            $sheet3->setCellValue('C' . $kolom, $b->no_box);
            $sheet3->setCellValue('D' . $kolom, $b->grade);
            $sheet3->setCellValue('E' . $kolom, $b->pcs);
            $sheet3->setCellValue('F' . $kolom, $b->gr);
            $sheet3->setCellValue('G' . $kolom, $b->cost_bk);
            $sheet3->setCellValue('H' . $kolom, $b->cost_kerja);
            $sheet3->setCellValue('I' . $kolom, $b->cost_cu);
            $sheet3->setCellValue('J' . $kolom, $b->cost_op);

            $ttlRp = $b->cost_bk + $b->cost_kerja + $b->cost_cu + $b->cost_op;

            $sheet3->setCellValue('K' . $kolom, $ttlRp);
            $sheet3->setCellValue('L' . $kolom, empty($b->gr) ? 0 : $ttlRp / $b->gr);
            $kolom++;
        }
        $sheet3->getStyle('B2:L' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("O1:Y1")->applyFromArray($style_atas);
        $sheet3->setCellValue('N1', 'Sisa belum kirim');
        $sheet3->setCellValue('O1', 'nama partai');
        $sheet3->setCellValue('P1', 'no box pengiriman');
        $sheet3->setCellValue('Q1', 'grade');
        $sheet3->setCellValue('R1', 'pcs');
        $sheet3->setCellValue('S1', 'gr');
        $sheet3->setCellValue('T1', 'ttl rp');
        $sheet3->setCellValue('U1', 'cost kerja');
        $sheet3->setCellValue('V1', 'cost cu');
        $sheet3->setCellValue('W1', 'cost operasional');
        $sheet3->setCellValue('X1', 'total rp');
        $sheet3->setCellValue('Y1', 'rp/gr');

        $grading = DB::select("SELECT nm_partai, box_pengiriman, grade, sum(pcs) as pcs, sum(gr) as gr, sum(ttl_rp) as ttl_rp, sum(cost_bk) as cost_bk, sum(cost_kerja) as cost_kerja, sum(cost_cu) as cost_cu, sum(cost_op) as cost_op FROM `grading_partai` 
                WHERE sudah_kirim = 'T' and grade != 'susut'  
                group by nm_partai,box_pengiriman;");
        $kolom = 2;
        foreach ($grading  as $d) {
            $sheet3->setCellValue('O' . $kolom, $d->nm_partai);
            $sheet3->setCellValue('P' . $kolom, $d->box_pengiriman);
            $sheet3->setCellValue('Q' . $kolom, $d->grade);
            $sheet3->setCellValue('R' . $kolom, $d->pcs);
            $sheet3->setCellValue('S' . $kolom, $d->gr);
            $sheet3->setCellValue('T' . $kolom, $d->cost_bk);
            $sheet3->setCellValue('U' . $kolom, $d->cost_kerja);
            $sheet3->setCellValue('V' . $kolom, $d->cost_cu);
            $sheet3->setCellValue('W' . $kolom, $d->cost_op);
            $sheet3->setCellValue('X' . $kolom, $d->cost_bk + $d->cost_kerja + $d->cost_cu + $d->cost_op);
            $sheet3->setCellValue('Y' . $kolom, empty($d->gr) ? 0 : ($d->cost_bk + $d->cost_kerja + $d->cost_cu + $d->cost_op) / $d->gr);
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
        $pengiriman = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr FROM pengiriman as a ");
        $grading = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr FROM grading_partai as a ");
        $belum_grading = CocokanModel::gradingSisaDetails();

        $kolom = 2;

        $sheet3->setCellValue('AB' . $kolom, round($sa->pcs - $grading->pcs - sumBk($belum_grading, 'pcs'), 0));
        $sheet3->setCellValue('AC' . $kolom, 0);
        $sheet3->setCellValue('AD' . $kolom, 0);
        $sheet3->setCellValue('AE' . $kolom, 0);
        $sheet3->getStyle('AB2:AE2')->applyFromArray($style);
    }
    private function sortir_selesai($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(9);
        $sheet4 = $spreadsheet->getActiveSheet(9);
        $sheet4->setTitle('Sortir selesai');

        $cetak_selesai = $model::sortir_selesai_akhir();
        $sheet4->getStyle("B1:O1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'Sortir Selesai');
        $sheet4->setCellValue('B1', 'partai');
        $sheet4->setCellValue('C1', 'no box');
        $sheet4->setCellValue('D1', 'tipe');
        $sheet4->setCellValue('E1', 'ket');
        $sheet4->setCellValue('F1', 'pengawas');
        $sheet4->setCellValue('G1', 'pcs');
        $sheet4->setCellValue('H1', 'gr awal');
        $sheet4->setCellValue('I1', 'gr akhir');
        $sheet4->setCellValue('J1', 'ttl rp bk');
        $sheet4->setCellValue('K1', 'cost kerja');
        $sheet4->setCellValue('L1', 'cost cu');
        $sheet4->setCellValue('M1', 'cost operasional');
        $sheet4->setCellValue('N1', 'total rp');
        $sheet4->setCellValue('O1', 'rp/gr');


        $kolom = 2;
        foreach ($cetak_selesai  as $d) {
            $sheet4->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet4->setCellValue('C' . $kolom, $d->no_box);
            $sheet4->setCellValue('D' . $kolom, $d->tipe);
            $sheet4->setCellValue('E' . $kolom, $d->ket);
            $sheet4->setCellValue('F' . $kolom, $d->name);
            $sheet4->setCellValue('G' . $kolom, $d->pcs);
            $sheet4->setCellValue('H' . $kolom, $d->gr_awal);
            $sheet4->setCellValue('I' . $kolom, $d->gr);
            $sheet4->setCellValue('J' . $kolom, $d->ttl_rp);
            $sheet4->setCellValue('K' . $kolom, $d->cost_kerja);
            $sheet4->setCellValue('L' . $kolom,  $d->cost_cu);
            $sheet4->setCellValue('M' . $kolom, 0);
            $sheet4->setCellValue('N' . $kolom, 0);
            $sheet4->setCellValue('O' . $kolom, 0);
            $kolom++;
        }

        $sheet4->getStyle('B2:O' . $kolom + 1)->applyFromArray($style);
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

        $belum_grading = CocokanModel::gradingSisaDetails();
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

        $sheet4->setCellValue('B2', "=SUM('Gudang Cabut'!F:F)");
        $sheet4->setCellValue('B3', "=SUM('Gudang Cabut'!S:S)");
        $sheet4->setCellValue('B4', "=SUM('Gudang Cabut'!AF:AF)");
        $sheet4->setCellValue('B5', "=SUM('Gudang Cetak'!F:F)");
        $sheet4->setCellValue('B6', "=SUM('Gudang Cetak'!S:S)");
        $sheet4->setCellValue('B7', "=SUM('Gudang Cetak'!AF:AF)");
        $sheet4->setCellValue('B8', "=SUM('Gudang Sortir'!F:F)");
        $sheet4->setCellValue('B9', "=SUM('Gudang Sortir'!S:S)");
        $sheet4->setCellValue('B10', "=SUM('Gudang Sortir'!AF:AF)");
        $sheet4->setCellValue('B11', "=SUM('Gudang grading'!F:F)");
        $sheet4->setCellValue('B12', "=SUM('Pengiriman'!E:E)");
        $sheet4->setCellValue('B13', "=SUM('Pengiriman'!R:R)");
        $sheet4->setCellValue('B14', "=SUM(Pengiriman!AB:AB)");

        $sheet4->setCellValue('C2', "=SUM('Gudang Cabut'!G:G)");
        $sheet4->setCellValue('C3', "=SUM('Gudang Cabut'!T:T)");
        $sheet4->setCellValue('C4', "=SUM('Gudang Cabut'!AG:AG)");
        $sheet4->setCellValue('C5', "=SUM('Gudang Cetak'!G:G)");
        $sheet4->setCellValue('C6', "=SUM('Gudang Cetak'!T:T)");
        $sheet4->setCellValue('C7', "=SUM('Gudang Cetak'!AG:AG)");
        $sheet4->setCellValue('C8', "=SUM('Gudang Sortir'!G:G)");
        $sheet4->setCellValue('C9', "=SUM('Gudang Sortir'!T:T)");
        $sheet4->setCellValue('C10', "=SUM('Gudang Sortir'!AG:AG)");
        $sheet4->setCellValue('C11', "=SUM('Gudang grading'!G:G)");
        $sheet4->setCellValue('C12', "=SUM('Pengiriman'!F:F)");
        $sheet4->setCellValue('C13', "=SUM('Pengiriman'!S:S)");
        $sheet4->setCellValue('C14', "0");

        $sheet4->setCellValue('D2', "=SUM('Gudang Cabut'!H:H)");
        $sheet4->setCellValue('D3', "=SUM('Gudang Cabut'!U:U)");
        $sheet4->setCellValue('D4', "=SUM('Gudang Cabut'!AH:AH)");
        $sheet4->setCellValue('D5', "=SUM('Gudang Cetak'!H:H)");
        $sheet4->setCellValue('D6', "=SUM('Gudang Cetak'!U:U)");
        $sheet4->setCellValue('D7', "=SUM('Gudang Cetak'!AH:AH)");
        $sheet4->setCellValue('D8', "=SUM('Gudang Sortir'!H:H)");
        $sheet4->setCellValue('D9', "=SUM('Gudang Sortir'!U:U)");
        $sheet4->setCellValue('D10', "=SUM('Gudang Sortir'!AH:AH)");
        $sheet4->setCellValue('D11', "=SUM('Gudang grading'!H:H)");
        $sheet4->setCellValue('D12', "=SUM('Pengiriman'!G:G)");
        $sheet4->setCellValue('D13', "=SUM('Pengiriman'!T:T)");
        $sheet4->setCellValue('D14', "0");

        $sheet4->setCellValue('E2', "=SUM('Gudang Cabut'!I:I)");
        $sheet4->setCellValue('E3', "=SUM('Gudang Cabut'!V:V)");
        $sheet4->setCellValue('E4', "=SUM('Gudang Cabut'!AI:AI)");
        $sheet4->setCellValue('E5', "=SUM('Gudang Cetak'!I:I) ");
        $sheet4->setCellValue('E6', "=SUM('Gudang Cetak'!V:V)");
        $sheet4->setCellValue('E7', "=SUM('Gudang Cetak'!AI:AI)");
        $sheet4->setCellValue('E8', "=SUM('Gudang Sortir'!I:I)");
        $sheet4->setCellValue('E9', "=SUM('Gudang Sortir'!V:V)");
        $sheet4->setCellValue('E10', "=SUM('Gudang Sortir'!AI:AI)");
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
        $sheet4->setCellValue('H4', "=D4+E4+F4+G4");
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


        $bk_sinta = SummaryModel::summarybkDitutup();

        $kolom = 2;
        foreach ($bk_sinta  as $no => $b) {
            $sheet4->setCellValue('A' . $kolom, $no + 1);
            $sheet4->setCellValue('B' . $kolom, date('F Y', strtotime('01-' . $b->bulan . '-' . $b->tahun)));
            $sheet4->setCellValue('C' . $kolom, $b->nm_partai);
            $sheet4->setCellValue('D' . $kolom, $b->grade);
            $sheet4->setCellValue('E' . $kolom, $b->pcs);
            $sheet4->setCellValue('F' . $kolom, $b->gr);
            $sheet4->setCellValue('G' . $kolom, $b->ttl_rp);
            $sheet4->setCellValue('H' . $kolom, empty($b->gr) ? 0 : $b->ttl_rp / $b->gr);
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
        $sheet4->setCellValue('A' . $kolom, "Total");
        $sheet4->setCellValue('B' . $kolom, '');
        $sheet4->setCellValue('C' . $kolom, '');
        $sheet4->setCellValue('D' . $kolom, '');
        $sheet4->setCellValue('E' . $kolom, "=SUM(E2:E" . $kolom - 1 . ")");
        $sheet4->setCellValue('F' . $kolom, "=SUM(F2:F" . $kolom - 1 . ")");
        $sheet4->setCellValue('G' . $kolom, "=SUM(G2:G" . $kolom - 1 . ")");
        $sheet4->setCellValue('H' . $kolom, 0);
        $sheet4->setCellValue('I' . $kolom, "=SUM(I2:I" . $kolom - 1 . ")");
        $sheet4->setCellValue('J' . $kolom, "=SUM(J2:J" . $kolom - 1 . ")");
        $sheet4->setCellValue('K' . $kolom, "=SUM(K2:K" . $kolom - 1 . ")");
        $sheet4->setCellValue('L' . $kolom, 0);

        $sheet4->setCellValue('M' . $kolom, "=SUM(M2:M" . $kolom - 1 . ")");
        $sheet4->setCellValue('N' . $kolom, "=SUM(N2:N" . $kolom - 1 . ")");
        $sheet4->setCellValue('O' . $kolom, "=SUM(O2:O" . $kolom - 1 . ")");
        $sheet4->setCellValue('P' . $kolom, "=SUM(P2:P" . $kolom - 1 . ")");
        $sheet4->setCellValue('Q' . $kolom, "=SUM(Q2:Q" . $kolom - 1 . ")");
        $sheet4->setCellValue('R' . $kolom, "=SUM(R2:R" . $kolom - 1 . ")");
        $sheet4->setCellValue('S' . $kolom, 0);

        $sheet4->getStyle('A2:S' . $kolom - 1)->applyFromArray($style);
        $sheet4->getStyle("A$kolom:S$kolom")->applyFromArray($style_atas);

        $style2 = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF00', // Contoh warna kuning
                ],
            ],
        ];

        $sheet4->getStyle("A$kolom:S$kolom")->applyFromArray($style2);
    }


    private function lis_pengiriman($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(7);
        $sheet4 = $spreadsheet->getActiveSheet(7);
        $sheet4->setTitle('List Pengiriman');

        $sheet4->getStyle("A1:K1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'No');
        $sheet4->setCellValue('B1', 'bulan');
        $sheet4->setCellValue('C1', 'tgl kirim');
        $sheet4->setCellValue('D1', 'no packing list');
        $sheet4->setCellValue('E1', 'nama packing list');
        $sheet4->setCellValue('F1', 'tujuan');
        $sheet4->setCellValue('G1', 'box');
        $sheet4->setCellValue('H1', 'pcs');
        $sheet4->setCellValue('I1', 'gr');
        $sheet4->setCellValue('J1', 'gr + kadar');
        $sheet4->setCellValue('K1', 'Total Rp');

        $packing_list = Grading::list_pengiriman_sum();

        $kolom = 2;

        foreach ($packing_list  as $no => $d) {
            $sheet4->setCellValue('A' . $kolom, $no + 1);
            $sheet4->setCellValue('B' . $kolom, date('F', strtotime($d->tgl)));
            $sheet4->setCellValue('C' . $kolom, tanggal($d->tgl));
            $sheet4->setCellValue('D' . $kolom, $d->no_nota);
            $sheet4->setCellValue('E' . $kolom, ucwords($d->nm_packing));
            $sheet4->setCellValue('F' . $kolom, strtoupper($d->tujuan));
            $sheet4->setCellValue('G' . $kolom, $d->ttl_box);
            $sheet4->setCellValue('H' . $kolom, $d->pcs);
            $sheet4->setCellValue('I' . $kolom, $d->gr);
            $sheet4->setCellValue('J' . $kolom, $d->gr_naik);
            $totalRp = $d->cost_bk + $d->cost_kerja + $d->cost_cu + $d->cost_op;

            $sheet4->setCellValue('K' . $kolom, $totalRp);
            $kolom++;
        }
        $sheet4->getStyle('A2:K' . $kolom - 1)->applyFromArray($style);
    }

    private function rekapPengawas($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(8);
        $sheet4 = $spreadsheet->getActiveSheet(8);
        $sheet4->setTitle('Rekap Opname Pgws');

        $sheet4->mergeCells('C1:D1');
        $sheet4->mergeCells('E1:F1');
        $sheet4->mergeCells('G1:H1');
        $sheet4->mergeCells('I1:J1');
        $sheet4->mergeCells('K1:N1');

        $sheet4->mergeCells('B1:B2');

        $sheet4->getStyle("B1:O2")->applyFromArray($style_atas);

        $sheet4->setCellValue('A1', 'Cabut');
        $sheet4->setCellValue('B1', 'Nama pengawas');
        $sheet4->setCellValue('C1', 'Cabut proses');
        $sheet4->setCellValue('E1', 'Cabut sisa pengawas');
        $sheet4->setCellValue('G1', 'Cabut selesai siap cetak');
        $sheet4->setCellValue('I1', 'Total');
        $sheet4->setCellValue('K1', 'Susut');

        $sheet4->setCellValue('C2', 'pcs');
        $sheet4->setCellValue('D2', 'gr');
        $sheet4->setCellValue('E2', 'pcs');
        $sheet4->setCellValue('F2', 'gr');
        $sheet4->setCellValue('G2', 'pcs');
        $sheet4->setCellValue('H2', 'gr');
        $sheet4->setCellValue('I2', 'pcs');
        $sheet4->setCellValue('J2', 'gr');
        $sheet4->setCellValue('K2', 'nama posisi');
        $sheet4->setCellValue('L2', 'kelas');
        $sheet4->setCellValue('M2', 'gr');
        $sheet4->setCellValue('N2', 'gr');
        $sheet4->setCellValue('O2', '%');


        $pgws_cabut = OpnameNewModel::cabut_susut2();

        $style_persen = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $style_susut = [
            'font' => [
                'color' => ['rgb' => 'FF0000'],
            ],
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





        $kolom = 3;
        $nama_terisi = [];
        foreach ($pgws_cabut  as $no => $b) {
            if (!in_array($b->name, $nama_terisi)) {
                // Jika belum, tambahkan nama ke sheet
                $sheet4->setCellValue('B' . $kolom, $b->name);
                $sheet4->setCellValue('C' . $kolom, '=SUMIF(\'Gudang Cabut\'!$C:$C,\'Rekap Opname Pgws\'!B' . $kolom . ',\'Gudang Cabut\'!$F:$F)');
                $sheet4->setCellValue('D' . $kolom, '=SUMIF(\'Gudang Cabut\'!$C:$C,\'Rekap Opname Pgws\'!B' . $kolom . ',\'Gudang Cabut\'!$G:$G)');
                $sheet4->setCellValue('E' . $kolom, '=SUMIF(\'Gudang Cabut\'!$P:$P,\'Rekap Opname Pgws\'!B' . $kolom . ',\'Gudang Cabut\'!$S:$S)');
                $sheet4->setCellValue('F' . $kolom, '=SUMIF(\'Gudang Cabut\'!$P:$P,\'Rekap Opname Pgws\'!B' . $kolom . ',\'Gudang Cabut\'!$T:$T)');
                $sheet4->setCellValue('G' . $kolom, '=SUMIF(\'Gudang Cabut\'!$AC:$AC,\'Rekap Opname Pgws\'!B' . $kolom . ',\'Gudang Cabut\'!$AF:$AF)');
                $sheet4->setCellValue('H' . $kolom, '=SUMIF(\'Gudang Cabut\'!$AC:$AC,\'Rekap Opname Pgws\'!B' . $kolom . ',\'Gudang Cabut\'!$AG:$AG)');

                // Simpan nama ke dalam array $nama_terisi untuk melacaknya
                $nama_terisi[] = $b->name;
            }


            $sheet4->setCellValue('I' . $kolom, "=C$kolom+E$kolom+G$kolom");
            $sheet4->setCellValue('J' . $kolom, "=D$kolom+F$kolom+H$kolom");
            $sheet4->setCellValue('K' . $kolom, 'Cabut ' . $b->name);
            $susut = round((1 - ($b->gr_akhir / $b->gr_awal)) * 100, 0);

            if ($susut > $b->batas_susut) {
                $stylesst = $style_susut;
            } else {
                $stylesst = $style;
            }
            $sheet4->setCellValue('L' . $kolom, $b->tipe);
            $sheet4->setCellValue('M' . $kolom, $b->gr_awal - $b->gr_akhir);
            $sheet4->setCellValue('N' . $kolom, "=IF(B$kolom=\"\",\"\",SUMIF(\$K:\$K,K$kolom,\$M:\$M))");
            $sheet4->setCellValue('O' . $kolom, $susut . '%');
            $sheet4->getStyle("L$kolom:O$kolom")->applyFromArray($stylesst);

            // $sheet4->setCellValue('O' . $kolom, $b->gr_awal);
            // $sheet4->setCellValue('P' . $kolom, $b->gr_akhir);

            $kolom++;
        }
        $kolom_sinta = $kolom;

        $sheet4->setCellValue('B' . $kolom_sinta, 'sinta');
        $sheet4->setCellValue('C' . $kolom_sinta, '=SUMIF(\'Gudang Cabut\'!$C:$C,\'Rekap Opname Pgws\'!B' . $kolom_sinta . ',\'Gudang Cabut\'!$F:$F)');
        $sheet4->setCellValue('D' . $kolom_sinta, '=SUMIF(\'Gudang Cabut\'!$C:$C,\'Rekap Opname Pgws\'!B' . $kolom_sinta . ',\'Gudang Cabut\'!$G:$G)');
        $sheet4->setCellValue('E' . $kolom_sinta, '=SUMIF(\'Gudang Cabut\'!$P:$P,\'Rekap Opname Pgws\'!B' . $kolom_sinta . ',\'Gudang Cabut\'!$S:$S)');
        $sheet4->setCellValue('F' . $kolom_sinta, '=SUMIF(\'Gudang Cabut\'!$P:$P,\'Rekap Opname Pgws\'!B' . $kolom_sinta . ',\'Gudang Cabut\'!$T:$T)');
        $sheet4->setCellValue('G' . $kolom_sinta, '=SUMIF(\'Gudang Cabut\'!$AC:$AC,\'Rekap Opname Pgws\'!B' . $kolom_sinta . ',\'Gudang Cabut\'!$AF:$AF)');
        $sheet4->setCellValue('H' . $kolom_sinta, '=SUMIF(\'Gudang Cabut\'!$AC:$AC,\'Rekap Opname Pgws\'!B' . $kolom_sinta . ',\'Gudang Cabut\'!$AG:$AG)');

        $sheet4->setCellValue('I' . $kolom_sinta, "=C$kolom_sinta+E$kolom_sinta+G$kolom_sinta");
        $sheet4->setCellValue('J' . $kolom_sinta, "=D$kolom_sinta+F$kolom_sinta+H$kolom_sinta");





        $sheet4->getStyle('B3:O' . $kolom_sinta)->applyFromArray($style);

        $kolom2 = $kolom + 3;

        $sheet4->mergeCells("C$kolom2:D$kolom2");
        $sheet4->mergeCells("E$kolom2:F$kolom2");
        $sheet4->mergeCells("G$kolom2:H$kolom2");
        $sheet4->mergeCells("I$kolom2:J$kolom2");
        $sheet4->mergeCells("K$kolom2:O$kolom2");

        $sheet4->mergeCells("B$kolom2:B" . $kolom2 + 1);

        $sheet4->getStyle("B$kolom2:O" . $kolom2 + 1)->applyFromArray($style_atas);

        $sheet4->setCellValue('A' . $kolom2, 'Cetak');
        $sheet4->setCellValue('B' . $kolom2, 'Nama pengawas');
        $sheet4->setCellValue('C' . $kolom2, 'Cetak proses');
        $sheet4->setCellValue('E' . $kolom2, 'Cetak sisa pengawas');
        $sheet4->setCellValue('G' . $kolom2, 'Cetak selesai siap sortir');
        $sheet4->setCellValue('I' . $kolom2, 'Total');
        $sheet4->setCellValue('K' . $kolom2, 'Susut');

        $sheet4->setCellValue('C' . $kolom2 + 1, 'pcs');
        $sheet4->setCellValue('D' . $kolom2 + 1, 'gr');
        $sheet4->setCellValue('E' . $kolom2 + 1, 'pcs');
        $sheet4->setCellValue('F' . $kolom2 + 1, 'gr');
        $sheet4->setCellValue('G' . $kolom2 + 1, 'pcs');
        $sheet4->setCellValue('H' . $kolom2 + 1, 'gr');
        $sheet4->setCellValue('I' . $kolom2 + 1, 'pcs');
        $sheet4->setCellValue('J' . $kolom2 + 1, 'gr');
        $sheet4->setCellValue('K' . $kolom2 + 1, 'nama posisi');
        $sheet4->setCellValue('L' . $kolom2 + 1, 'kelas');
        $sheet4->setCellValue('M' . $kolom2 + 1, 'gr');
        $sheet4->setCellValue('N' . $kolom2 + 1, 'gr');
        $sheet4->setCellValue('O' . $kolom2 + 1, '%');

        $pgws_cetak = OpnameNewModel::cetak_susut2();

        $kolom_ctk = $kolom2 + 2;
        $nama_terisi = [];
        foreach ($pgws_cetak  as $no => $b) {
            if (!in_array($b->name, $nama_terisi)) {
                // Jika belum, tambahkan nama ke sheet
                $sheet4->setCellValue('B' . $kolom_ctk, $b->name);

                // Simpan nama ke dalam array $nama_terisi untuk melacaknya
                $nama_terisi[] = $b->name;
            }
            $sheet4->setCellValue('C' . $kolom_ctk, '=SUMIF(\'Gudang Cetak\'!$C:$C,\'Rekap Opname Pgws\'!B' . $kolom_ctk . ',\'Gudang Cetak\'!$F:$F)');
            $sheet4->setCellValue('D' . $kolom_ctk, '=SUMIF(\'Gudang Cetak\'!$C:$C,\'Rekap Opname Pgws\'!B' . $kolom_ctk . ',\'Gudang Cetak\'!$G:$G)');
            $sheet4->setCellValue('E' . $kolom_ctk, '=SUMIF(\'Gudang Cetak\'!$P:$P,\'Rekap Opname Pgws\'!B' . $kolom_ctk . ',\'Gudang Cetak\'!$S:$S)');
            $sheet4->setCellValue('F' . $kolom_ctk, '=SUMIF(\'Gudang Cetak\'!$P:$P,\'Rekap Opname Pgws\'!B' . $kolom_ctk . ',\'Gudang Cetak\'!$T:$T)');
            $sheet4->setCellValue('G' . $kolom_ctk, '=SUMIF(\'Gudang Cetak\'!$AC:$AC,\'Rekap Opname Pgws\'!B' . $kolom_ctk . ',\'Gudang Cetak\'!$AF:$AF)');
            $sheet4->setCellValue('H' . $kolom_ctk, '=SUMIF(\'Gudang Cetak\'!$AC:$AC,\'Rekap Opname Pgws\'!B' . $kolom_ctk . ',\'Gudang Cetak\'!$AG:$AG)');
            $sheet4->setCellValue('I' . $kolom_ctk, "=C$kolom_ctk+E$kolom_ctk+G$kolom_ctk");
            $sheet4->setCellValue('J' . $kolom_ctk, "=D$kolom_ctk+F$kolom_ctk+H$kolom_ctk");
            $susut_ctk = round((1 - ($b->gr_akhir / $b->gr_awal)) * 100, 0);
            if ($susut_ctk > $b->batas_susut) {
                $stylesst = $style_susut;
            } else {
                $stylesst = $style;
            }
            $sheet4->setCellValue('K' . $kolom_ctk, 'Cetak ' . $b->name);
            $sheet4->setCellValue('L' . $kolom_ctk, $b->kelas);
            $sheet4->setCellValue('M' . $kolom_ctk, $b->gr_awal - $b->gr_akhir);
            $sheet4->setCellValue('N' . $kolom_ctk, "=IF(B$kolom_ctk=\"\",\"\",SUMIF(\$K:\$K,K$kolom_ctk,\$M:\$M))");
            $sheet4->setCellValue('O' . $kolom_ctk, round((1 - ($b->gr_akhir / $b->gr_awal)) * 100, 0) . '%');
            $sheet4->getStyle("L$kolom_ctk:O$kolom_ctk")->applyFromArray($stylesst);

            // $sheet4->setCellValue('O' . $kolom_ctk, $b->gr_awal);
            // $sheet4->setCellValue('P' . $kolom_ctk, $b->gr_awal);
            $kolom_ctk++;
        }
        $sheet4->getStyle("B$kolom2:O" . $kolom_ctk - 1)->applyFromArray($style);


        $kolom3 = $kolom_ctk + 1;
        $sheet4->mergeCells("C$kolom3:D$kolom3");
        $sheet4->mergeCells("E$kolom3:F$kolom3");
        $sheet4->mergeCells("G$kolom3:H$kolom3");
        $sheet4->mergeCells("I$kolom3:J$kolom3");
        $sheet4->mergeCells("K$kolom3:O$kolom3");

        $sheet4->mergeCells("B$kolom3:B" . $kolom3 + 1);

        $sheet4->getStyle("B$kolom3:O" . $kolom3 + 1)->applyFromArray($style_atas);

        $sheet4->setCellValue('A' . $kolom3, 'Sortir');
        $sheet4->setCellValue('B' . $kolom3, 'Nama pengawas');
        $sheet4->setCellValue('C' . $kolom3, 'Sortir proses');
        $sheet4->setCellValue('E' . $kolom3, 'Sortir sisa pengawas');
        $sheet4->setCellValue('G' . $kolom3, 'Sortir selesai siap grade');
        $sheet4->setCellValue('I' . $kolom3, 'Total');
        $sheet4->setCellValue('K' . $kolom3, 'Susut');

        $sheet4->setCellValue('C' . $kolom3 + 1, 'pcs');
        $sheet4->setCellValue('D' . $kolom3 + 1, 'gr');
        $sheet4->setCellValue('E' . $kolom3 + 1, 'pcs');
        $sheet4->setCellValue('F' . $kolom3 + 1, 'gr');
        $sheet4->setCellValue('G' . $kolom3 + 1, 'pcs');
        $sheet4->setCellValue('H' . $kolom3 + 1, 'gr');
        $sheet4->setCellValue('I' . $kolom3 + 1, 'pcs');
        $sheet4->setCellValue('J' . $kolom3 + 1, 'gr');
        $sheet4->setCellValue('K' . $kolom3 + 1, 'nama posisi');
        $sheet4->setCellValue('L' . $kolom3 + 1, 'kelas');
        $sheet4->setCellValue('M' . $kolom3 + 1, 'gr');
        $sheet4->setCellValue('N' . $kolom3 + 1, 'gr');
        $sheet4->setCellValue('O' . $kolom3 + 1, '%');

        $pgws_sortir = OpnameNewModel::sortir_susut2();

        $kolom_sortir = $kolom3 + 2;
        $nama_terisi = [];
        foreach ($pgws_sortir  as $no => $b) {
            if (!in_array($b->name, $nama_terisi)) {
                // Jika belum, tambahkan nama ke sheet
                $sheet4->setCellValue('B' . $kolom_sortir, $b->name);

                // Simpan nama ke dalam array $nama_terisi untuk melacaknya
                $nama_terisi[] = $b->name;
            }

            $sheet4->setCellValue('C' . $kolom_sortir, '=SUMIF(\'Gudang Sortir\'!$C:$C,\'Rekap Opname Pgws\'!B' . $kolom_sortir . ',\'Gudang Sortir\'!$F:$F)');
            $sheet4->setCellValue('D' . $kolom_sortir, '=SUMIF(\'Gudang Sortir\'!$C:$C,\'Rekap Opname Pgws\'!B' . $kolom_sortir . ',\'Gudang Sortir\'!$G:$G)');
            $sheet4->setCellValue('E' . $kolom_sortir, '=SUMIF(\'Gudang Sortir\'!$P:$P,\'Rekap Opname Pgws\'!B' . $kolom_sortir . ',\'Gudang Sortir\'!$S:$S)');
            $sheet4->setCellValue('F' . $kolom_sortir, '=SUMIF(\'Gudang Sortir\'!$P:$P,\'Rekap Opname Pgws\'!B' . $kolom_sortir . ',\'Gudang Sortir\'!$T:$T)');
            $sheet4->setCellValue('G' . $kolom_sortir, '=SUMIF(\'Gudang Sortir\'!$AC:$AC,\'Rekap Opname Pgws\'!B' . $kolom_sortir . ',\'Gudang Sortir\'!$AF:$AF)');
            $sheet4->setCellValue('H' . $kolom_sortir, '=SUMIF(\'Gudang Sortir\'!$AC:$AC,\'Rekap Opname Pgws\'!B' . $kolom_sortir . ',\'Gudang Sortir\'!$AG:$AG)');
            $sheet4->setCellValue('I' . $kolom_sortir, "=C$kolom_sortir+E$kolom_sortir+G$kolom_sortir");
            $sheet4->setCellValue('J' . $kolom_sortir, "=D$kolom_sortir+F$kolom_sortir+H$kolom_sortir");
            $sheet4->setCellValue('K' . $kolom_sortir, 'Sortir ' . $b->name);
            $susut_str = round((1 - ($b->gr_akhir / $b->gr_awal)) * 100, 0);
            if ($susut_str > $b->bts_denda_sst) {
                $stylesst = $style_susut;
            } else {
                $stylesst = $style;
            }
            $sheet4->setCellValue('L' . $kolom_sortir, $b->kelas);
            $sheet4->setCellValue('M' . $kolom_sortir, $b->gr_awal - $b->gr_akhir);
            $sheet4->setCellValue('N' . $kolom_sortir, "=IF(B$kolom_sortir=\"\",\"\",SUMIF(\$K:\$K,K$kolom_sortir,\$M:\$M))");
            $sheet4->setCellValue('O' . $kolom_sortir, round((1 - ($b->gr_akhir / $b->gr_awal)) * 100, 0) . "%");
            $sheet4->getStyle("L$kolom_sortir:O$kolom_sortir")->applyFromArray($stylesst);

            // $sheet4->setCellValue('O' . $kolom_sortir, $b->gr_awal);
            // $sheet4->setCellValue('P' . $kolom_sortir, $b->gr_awal);
            $kolom_sortir++;
        }
        $sheet4->getStyle("B$kolom3:O" . $kolom_sortir - 1)->applyFromArray($style);


        $kolom4 = $kolom_sortir + 1;
        $sheet4->mergeCells("C$kolom4:D$kolom4");
        $sheet4->mergeCells("E$kolom4:F$kolom4");
        $sheet4->mergeCells("G$kolom4:H$kolom4");
        $sheet4->mergeCells("I$kolom4:J$kolom4");
        $sheet4->mergeCells("K$kolom4:O$kolom4");

        $sheet4->mergeCells("B$kolom4:B" . $kolom4 + 1);

        $sheet4->getStyle("B$kolom4:O" . $kolom4 + 1)->applyFromArray($style_atas);

        $sheet4->setCellValue('A' . $kolom4, 'Grading');
        $sheet4->setCellValue('B' . $kolom4, 'Nama pengawas');
        $sheet4->setCellValue('C' . $kolom4, 'Sisa belum grading');
        $sheet4->setCellValue('E' . $kolom4, 'Sisa belum kirim');
        $sheet4->setCellValue('G' . $kolom4, '');
        $sheet4->setCellValue('I' . $kolom4, 'Total');
        $sheet4->setCellValue('K' . $kolom4, 'Susut');

        $sheet4->setCellValue('C' . $kolom4 + 1, 'pcs');
        $sheet4->setCellValue('D' . $kolom4 + 1, 'gr');
        $sheet4->setCellValue('E' . $kolom4 + 1, 'pcs');
        $sheet4->setCellValue('F' . $kolom4 + 1, 'gr');
        $sheet4->setCellValue('G' . $kolom4 + 1, 'pcs');
        $sheet4->setCellValue('H' . $kolom4 + 1, 'gr');
        $sheet4->setCellValue('I' . $kolom4 + 1, 'pcs');
        $sheet4->setCellValue('J' . $kolom4 + 1, 'gr');
        $sheet4->setCellValue('K' . $kolom4 + 1, 'nama posisi');
        $sheet4->setCellValue('L' . $kolom4 + 1, 'kelas');
        $sheet4->setCellValue('M' . $kolom4 + 1, 'gr');
        $sheet4->setCellValue('N' . $kolom4 + 1, 'gr');
        $sheet4->setCellValue('O' . $kolom4 + 1, '%');



        $kolom_grade = $kolom4 + 2;

        $susut_grading = DB::selectOne("SELECT sum(a.gr) as gr_susut FROM grading_partai as a where a.grade = 'susut'");
        $gr_grading = DB::selectOne("SELECT sum(a.gr) as gr_susut FROM grading_partai as a where a.grade != 'susut'");

        $sheet4->setCellValue('B' . $kolom_grade, 'Siti Fatimah');
        $sheet4->setCellValue('C' . $kolom_grade, "=SUM('Gudang grading'!F:F)");
        $sheet4->setCellValue('D' . $kolom_grade, "=SUM('Gudang grading'!G:G)");
        $sheet4->setCellValue('E' . $kolom_grade, "=SUM(Pengiriman!R:R)");
        $sheet4->setCellValue('F' . $kolom_grade, "=SUM(Pengiriman!S:S)");
        $sheet4->setCellValue('G' . $kolom_grade, 0);
        $sheet4->setCellValue('H' . $kolom_grade, 0);
        $sheet4->setCellValue('I' . $kolom_grade, "=C$kolom_grade+E$kolom_grade+G$kolom_grade");
        $sheet4->setCellValue('J' . $kolom_grade, "=D$kolom_grade+F$kolom_grade+H$kolom_grade");
        $sheet4->setCellValue('K' . $kolom_grade, "-");
        $sheet4->setCellValue('L' . $kolom_grade, "-");
        $sheet4->setCellValue('M' . $kolom_grade, $susut_grading->gr_susut);
        $sheet4->setCellValue('N' . $kolom_grade, $susut_grading->gr_susut);
        $sheet4->setCellValue('O' . $kolom_grade, "=ROUND((1- (J$kolom_grade/(M$kolom_grade+J$kolom_grade))) * 100,0)");


        $sheet4->getStyle("B$kolom4:O" . $kolom_grade)->applyFromArray($style);
        $sheet4->getStyle("O2:O" . $kolom_grade)->applyFromArray($style_persen);



        $kolom_sifa = $kolom_grade + 2;
        $sheet4->setCellValue('B' . $kolom_sifa, 'grading');
        $sheet4->setCellValue('C' . $kolom_sifa, 0);
        $sheet4->setCellValue('D' . $kolom_sifa, 0);

        $sheet4->setCellValue('B' . $kolom_sifa + 1, 'wip');
        $sheet4->setCellValue('C' . $kolom_sifa + 1, 0);
        $sheet4->setCellValue('D' . $kolom_sifa + 1, 0);

        $sheet4->setCellValue('B' . $kolom_sifa + 2, 'qc');
        $sheet4->setCellValue('C' . $kolom_sifa + 2, 0);
        $sheet4->setCellValue('D' . $kolom_sifa + 2, 0);

        $sheet4->setCellValue('B' . $kolom_sifa + 3, 'total');
        $kolomsifa2 = $kolom_sifa + 2;
        $sheet4->setCellValue('C' . $kolom_sifa + 3, "=SUM(C$kolom_sifa:C$kolomsifa2)");
        $sheet4->setCellValue('D' . $kolom_sifa + 3, "=SUM(D$kolom_sifa:D$kolomsifa2)");

        $style2 = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF00', // Contoh warna kuning
                ],
            ],
        ];



        $sheet4->getStyle("C" . $kolom_sifa + 3 . ":D" . $kolom_sifa + 3)->applyFromArray($style2);


        $kolom5 = $kolom_sifa + 5;
        $sheet4->mergeCells("C$kolom5:D$kolom5");
        $sheet4->mergeCells("E$kolom5:F$kolom5");
        $sheet4->mergeCells("G$kolom5:H$kolom5");
        $sheet4->mergeCells("I$kolom5:J$kolom5");

        $sheet4->mergeCells("B$kolom5:B" . $kolom5 + 1);

        $sheet4->getStyle("B$kolom5:J" . $kolom5 + 1)->applyFromArray($style_atas);

        $sheet4->setCellValue('A' . $kolom5, 'Pengiriman');
        $sheet4->setCellValue('B' . $kolom5, 'Nama pengawas');
        $sheet4->setCellValue('C' . $kolom5, 'Pengiriman');
        $sheet4->setCellValue('E' . $kolom5, '');
        $sheet4->setCellValue('G' . $kolom5, '');
        $sheet4->setCellValue('I' . $kolom5, 'Total');

        $sheet4->setCellValue('C' . $kolom5 + 1, 'pcs');
        $sheet4->setCellValue('D' . $kolom5 + 1, 'gr');
        $sheet4->setCellValue('E' . $kolom5 + 1, 'pcs');
        $sheet4->setCellValue('F' . $kolom5 + 1, 'gr');
        $sheet4->setCellValue('G' . $kolom5 + 1, 'pcs');
        $sheet4->setCellValue('H' . $kolom5 + 1, 'gr');
        $sheet4->setCellValue('I' . $kolom5 + 1, 'pcs');
        $sheet4->setCellValue('J' . $kolom5 + 1, 'gr');

        $kolom_pengiriman = $kolom5 + 2;

        $sheet4->setCellValue('B' . $kolom_pengiriman, 'Ratna');
        $sheet4->setCellValue('C' . $kolom_pengiriman, "=SUM(Pengiriman!E:E)");
        $sheet4->setCellValue('D' . $kolom_pengiriman, "=SUM(Pengiriman!F:F)");
        $sheet4->setCellValue('E' . $kolom_pengiriman, 0);
        $sheet4->setCellValue('F' . $kolom_pengiriman, 0);
        $sheet4->setCellValue('G' . $kolom_pengiriman, 0);
        $sheet4->setCellValue('H' . $kolom_pengiriman, 0);
        $sheet4->setCellValue('I' . $kolom_pengiriman, "=C$kolom_pengiriman+E$kolom_pengiriman+G$kolom_pengiriman");
        $sheet4->setCellValue('J' . $kolom_pengiriman, "=D$kolom_pengiriman+F$kolom_pengiriman+H$kolom_pengiriman");

        $sheet4->getStyle("B$kolom5:J" . $kolom_pengiriman)->applyFromArray($style);

        $last_kolom = $kolom_pengiriman + 3;


        $sheet4->setCellValue('H' . $last_kolom, 'Total');
        $sheet4->setCellValue('I' . $last_kolom, "=SUM(I3:I$kolom_pengiriman)");
        $sheet4->setCellValue('J' . $last_kolom, "=SUM(J3:J$kolom_pengiriman)");

        $sheet4->getStyle("H$last_kolom:J$last_kolom")->applyFromArray($style);

        $sheet4->setCellValue('L' . $last_kolom, 'Total');
        $sheet4->setCellValue('M' . $last_kolom, "=SUM(M3:M$kolom_pengiriman)");
        $sheet4->setCellValue('N' . $last_kolom, "=SUM(N3:N$kolom_pengiriman)");

        $sheet4->getStyle("L$last_kolom:N$last_kolom")->applyFromArray($style);
    }



}
