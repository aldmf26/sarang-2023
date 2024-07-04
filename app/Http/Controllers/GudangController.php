<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use App\Models\CetakModel;
use App\Models\Grading;
use App\Models\Sortir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GudangController extends Controller
{
    public function index(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $id_user = auth()->user()->id;
        $gudang = Cabut::gudang($bulan, $tahun, $id_user);

        $data = [
            'title' => 'Data Gudang Awal',
            'bk' => $gudang->bk,
            'cabut' => $gudang->cabut,
            'cabutSelesai' => $gudang->cabutSelesai,
            'eoSelesai' => $gudang->eoSelesai,
            'cabut_selesai' => CetakModel::cabut_selesai(0),
            'cetak_proses' => CetakModel::cetak_proses(0),
            'cetak_selesai' => CetakModel::cetak_selesai(0),
            'siap_sortir' => Sortir::siap_sortir(),
            'sortir_proses' => Sortir::sortir_proses(),
            'sortir_selesai' => Sortir::sortir_selesai($id_user),
        ];
        return view('home.gudang.index', $data);
    }

    function export(Request $r)
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
        $sheet1->setTitle('Box Stock');


        $sheet1->getStyle("B1:G1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'Box Stock');
        $sheet1->setCellValue('B1', 'Pemilik');
        $sheet1->setCellValue('C1', 'Partai');
        $sheet1->setCellValue('D1', 'No Box');
        $sheet1->setCellValue('E1', 'Pcs');
        $sheet1->setCellValue('F1', 'Gr');
        $sheet1->setCellValue('G1', 'Rp/gr');

        $sheet1->getStyle("J1:O1")->applyFromArray($style_atas);
        $sheet1->setCellValue('I1', 'Box sedang proses');
        $sheet1->setCellValue('J1', 'Pemilik');
        $sheet1->setCellValue('K1', 'Partai');
        $sheet1->setCellValue('L1', 'No Box');
        $sheet1->setCellValue('M1', 'Pcs');
        $sheet1->setCellValue('N1', 'Gr');
        $sheet1->setCellValue('O1', 'Rp/gr');

        $sheet1->getStyle("R1:W1")->applyFromArray($style_atas);
        $sheet1->setCellValue('Q1', 'Box Selesai siap ctk');
        $sheet1->setCellValue('R1', 'Pemilik');
        $sheet1->setCellValue('S1', 'Partai');
        $sheet1->setCellValue('T1', 'No Box');
        $sheet1->setCellValue('U1', 'Pcs');
        $sheet1->setCellValue('V1', 'Gr');
        $sheet1->setCellValue('W1', 'Rp/gr');

        $sheet1->getStyle("Z1:AE1")->applyFromArray($style_atas);
        $sheet1->setCellValue('Y1', 'Box Selesai siap sortir');
        $sheet1->setCellValue('Z1', 'Pemilik');
        $sheet1->setCellValue('AA1', 'Partai');
        $sheet1->setCellValue('AB1', 'No Box');
        $sheet1->setCellValue('AC1', 'Pcs');
        $sheet1->setCellValue('AD1', 'Gr');
        $sheet1->setCellValue('AE1', 'Rp/gr');

        $kolom = 2;
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $id_user = auth()->user()->id;
        $gudang = Cabut::gudang($bulan, $tahun, $id_user);

        foreach ($gudang->bk as $d) {
            $sheet1->setCellValue('B' . $kolom, $d->penerima);
            $sheet1->setCellValue('C' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('D' . $kolom, $d->no_box);
            $sheet1->setCellValue('E' . $kolom, $d->pcs);
            $sheet1->setCellValue('F' . $kolom, $d->gr);
            $sheet1->setCellValue('G' . $kolom, $d->hrga_satuan);
            $kolom++;
        }
        $sheet1->getStyle('A2:G' . $kolom - 1)->applyFromArray($style);

        $kolom2 = 2;
        foreach ($gudang->cabut as $d) {
            $sheet1->setCellValue('J' . $kolom2, $d->penerima);
            $sheet1->setCellValue('K' . $kolom2, $d->nm_partai);
            $sheet1->setCellValue('L' . $kolom2, $d->no_box);
            $sheet1->setCellValue('M' . $kolom2, $d->pcs);
            $sheet1->setCellValue('N' . $kolom2, $d->gr);
            $sheet1->setCellValue('O' . $kolom2, $d->hrga_satuan);
            $kolom2++;
        }
        $sheet1->getStyle('J2:O' . $kolom2 - 1)->applyFromArray($style);

        $kolom3 = 2;
        foreach ($gudang->cabutSelesai as $d) {
            $sheet1->setCellValue('R' . $kolom3, $d->pengawas);
            $sheet1->setCellValue('S' . $kolom3, $d->nm_partai);
            $sheet1->setCellValue('T' . $kolom3, $d->no_box);
            $sheet1->setCellValue('U' . $kolom3, $d->pcs);
            $sheet1->setCellValue('V' . $kolom3, $d->gr);
            $sheet1->setCellValue('W' . $kolom3, $d->hrga_satuan);
            $kolom3++;
        }
        $sheet1->getStyle('R2:W' . $kolom3 - 1)->applyFromArray($style);

        $kolom4 = 2;
        foreach ($gudang->eoSelesai as $d) {
            $sheet1->setCellValue('Z' . $kolom4, $d->pengawas);
            $sheet1->setCellValue('AA' . $kolom4, $d->nm_partai);
            $sheet1->setCellValue('AB' . $kolom4, $d->no_box);
            $sheet1->setCellValue('AC' . $kolom4, 0);
            $sheet1->setCellValue('AD' . $kolom4, $d->gr);
            $sheet1->setCellValue('AE' . $kolom4, $d->hrga_satuan);
            $kolom4++;
        }
        $sheet1->getStyle('Z2:AE' . $kolom4 - 1)->applyFromArray($style);

        // batas pertama

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet(1);
        $sheet2->setTitle('Gudang Cetak');

        $sheet2->getStyle("B1:G1")->applyFromArray($style_atas);
        $sheet2->setCellValue('A1', 'Cetak Stock');
        $sheet2->setCellValue('B1', 'Pemilik');
        $sheet2->setCellValue('C1', 'Partai');
        $sheet2->setCellValue('D1', 'No Box');
        $sheet2->setCellValue('E1', 'Pcs');
        $sheet2->setCellValue('F1', 'Gr');
        $sheet2->setCellValue('G1', 'Rp/gr');

        $sheet2->getStyle("J1:P1")->applyFromArray($style_atas);
        $sheet2->setCellValue('I1', 'Cetak sedang Proses');
        $sheet2->setCellValue('J1', 'Pemilik');
        $sheet2->setCellValue('K1', 'Pengawas');
        $sheet2->setCellValue('L1', 'Partai');
        $sheet2->setCellValue('M1', 'No Box');
        $sheet2->setCellValue('N1', 'Pcs');
        $sheet2->setCellValue('O1', 'Gr');
        $sheet2->setCellValue('P1', 'Rp/gr');

        $sheet2->getStyle("S1:Y1")->applyFromArray($style_atas);
        $sheet2->setCellValue('R1', 'Cetak selesai siap sortir');
        $sheet2->setCellValue('S1', 'Pemilik');
        $sheet2->setCellValue('T1', 'Pengawas');
        $sheet2->setCellValue('U1', 'Partai');
        $sheet2->setCellValue('V1', 'No Box');
        $sheet2->setCellValue('W1', 'Pcs');
        $sheet2->setCellValue('X1', 'Gr');
        $sheet2->setCellValue('Y1', 'Rp/gr');

        $cetak_stock = CetakModel::cabut_selesai(0);
        $kolom2 = 2;
        foreach ($cetak_stock as $d) {
            $sheet2->setCellValue('B' . $kolom2, $d->name);
            $sheet2->setCellValue('C' . $kolom2, $d->nm_partai);
            $sheet2->setCellValue('D' . $kolom2, $d->no_box);
            $sheet2->setCellValue('E' . $kolom2, $d->pcs_awal);
            $sheet2->setCellValue('F' . $kolom2, $d->gr_awal);
            $sheet2->setCellValue('G' . $kolom2, round(($d->ttl_rp + $d->cost_cbt) / $d->gr_awal, 0));
            $kolom2++;
        }
        $sheet2->getStyle('B2:G' . $kolom2 - 1)->applyFromArray($style);

        $cetak_proses = CetakModel::cetak_proses(0);
        $kolom3 = 2;
        foreach ($cetak_proses as $d) {
            $sheet2->setCellValue('J' . $kolom3, $d->name);
            $sheet2->setCellValue('K' . $kolom3, $d->pgws);
            $sheet2->setCellValue('L' . $kolom3, $d->nm_partai);
            $sheet2->setCellValue('M' . $kolom3, $d->no_box);
            $sheet2->setCellValue('N' . $kolom3, $d->pcs_awal);
            $sheet2->setCellValue('O' . $kolom3, $d->gr_awal);
            $sheet2->setCellValue('P' . $kolom3, round(($d->ttl_rp + $d->cost_cbt) / $d->gr_awal, 0));
            $kolom3++;
        }
        $sheet2->getStyle('J2:P' . $kolom3 - 1)->applyFromArray($style);

        $cetak_proses = CetakModel::cetak_selesai(0);
        $kolom4 = 2;
        foreach ($cetak_proses as $d) {
            $sheet2->setCellValue('S' . $kolom4, $d->name);
            $sheet2->setCellValue('T' . $kolom4, $d->pgws);
            $sheet2->setCellValue('U' . $kolom4, $d->nm_partai);
            $sheet2->setCellValue('V' . $kolom4, $d->no_box);
            $sheet2->setCellValue('W' . $kolom4, $d->pcs_awal);
            $sheet2->setCellValue('X' . $kolom4, $d->gr_awal);
            $sheet2->setCellValue('Y' . $kolom4, round(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk) / $d->gr_awal, 0));
            $kolom4++;
        }
        $sheet2->getStyle('S2:Y' . $kolom4 - 1)->applyFromArray($style);

        // Batas kedua

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet(2);
        $sheet3->setTitle('Gudang Sortir');

        $sheet3->getStyle("B1:G1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'Sortir stock');
        $sheet3->setCellValue('B1', 'Pemilik');
        $sheet3->setCellValue('C1', 'Partai');
        $sheet3->setCellValue('D1', 'No Box');
        $sheet3->setCellValue('E1', 'Pcs');
        $sheet3->setCellValue('F1', 'Gr');
        $sheet3->setCellValue('G1', 'Rp/gr');

        $sortir_stock = Sortir::siap_sortir();
        $kolom2 = 2;
        foreach ($sortir_stock as $d) {
            $sheet3->setCellValue('B' . $kolom2, $d->name);
            $sheet3->setCellValue('C' . $kolom2, $d->nm_partai);
            $sheet3->setCellValue('D' . $kolom2, $d->no_box);
            $sheet3->setCellValue('E' . $kolom2, $d->pcs_awal);
            $sheet3->setCellValue('F' . $kolom2, $d->gr_awal);
            $sheet3->setCellValue('G' . $kolom2, round(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo) / $d->gr_awal, 0));
            $kolom2++;
        }
        $sheet3->getStyle('B2:G' . $kolom2 - 1)->applyFromArray($style);


        $sheet3->getStyle("J1:O1")->applyFromArray($style_atas);
        $sheet3->setCellValue('I1', 'Sortir sedang proses');
        $sheet3->setCellValue('J1', 'Pemilik');
        $sheet3->setCellValue('K1', 'Partai');
        $sheet3->setCellValue('L1', 'No Box');
        $sheet3->setCellValue('M1', 'Pcs');
        $sheet3->setCellValue('N1', 'Gr');
        $sheet3->setCellValue('O1', 'Rp/gr');

        $sortir_proses = Sortir::sortir_proses();
        $kolom3 = 2;
        foreach ($sortir_proses as $d) {
            $sheet3->setCellValue('J' . $kolom3, $d->name);
            $sheet3->setCellValue('K' . $kolom3, $d->nm_partai);
            $sheet3->setCellValue('L' . $kolom3, $d->no_box);
            $sheet3->setCellValue('M' . $kolom3, $d->pcs_awal);
            $sheet3->setCellValue('N' . $kolom3, $d->gr_awal);
            $sheet3->setCellValue('O' . $kolom3, round(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo) / $d->gr_awal, 0));
            $kolom3++;
        }
        $sheet3->getStyle('J2:O' . $kolom3 - 1)->applyFromArray($style);

        $sheet3->getStyle("R1:W1")->applyFromArray($style_atas);
        $sheet3->setCellValue('Q1', 'Sortir selesai siap grading');
        $sheet3->setCellValue('R1', 'Pemilik');
        $sheet3->setCellValue('S1', 'Partai');
        $sheet3->setCellValue('T1', 'No Box');
        $sheet3->setCellValue('U1', 'Pcs');
        $sheet3->setCellValue('V1', 'Gr');
        $sheet3->setCellValue('W1', 'Rp/gr');

        $sortir_selesai = Sortir::sortir_selesai($id_user);
        $kolom4 = 2;
        foreach ($sortir_selesai as $d) {
            $sheet3->setCellValue('R' . $kolom4, $d->name);
            $sheet3->setCellValue('S' . $kolom4, $d->nm_partai);
            $sheet3->setCellValue('T' . $kolom4, $d->no_box);
            $sheet3->setCellValue('U' . $kolom4, $d->pcs_awal);
            $sheet3->setCellValue('V' . $kolom4, $d->gr_awal);
            $sheet3->setCellValue('W' . $kolom4, round(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_str + $d->cost_eo) / $d->gr_awal, 0));
            $kolom4++;
        }
        $sheet3->getStyle('R2:W' . $kolom4 - 1)->applyFromArray($style);



        $namafile = "Opname Gudang.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
