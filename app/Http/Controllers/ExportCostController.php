<?php

namespace App\Http\Controllers;

use App\Models\SummaryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use stdClass;

class ExportCostController extends Controller
{
    public function export()
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
        $sheet4 = $spreadsheet->getActiveSheet();
        $sheet4->setTitle('Bk Sinta');

        $sheet4->getStyle("A1:J1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'No');
        $sheet4->setCellValue('B1', 'bulan kerja');
        $sheet4->setCellValue('C1', 'nama partai');
        $sheet4->setCellValue('D1', 'grade');
        $sheet4->setCellValue('E1', 'pcs diambil');
        $sheet4->setCellValue('F1', 'gr diambil');
        $sheet4->setCellValue('G1', 'Cost Bk');
        $sheet4->setCellValue('H1', 'Cost operasional');
        $sheet4->setCellValue('I1', 'Cost berjalan');
        $sheet4->setCellValue('J1', 'rata2');


        $bk_sinta = SummaryModel::summarybk2();
        $cost_op = DB::selectOne("SELECT sum(a.total_operasional) as total FROM oprasional as a");

        $ttl_gr = sumBk($bk_sinta, 'gr_bk');
        $ttl_rp_cost = sumBk($bk_sinta, 'cost_cabut_dulu') + sumBk($bk_sinta, 'cost_sortir_dulu') + sumBk($bk_sinta, 'cost_cetak_dulu') + sumBk($bk_sinta, 'cost_eo_dulu');

        $rp_gr = ($cost_op->total - $ttl_rp_cost) / $ttl_gr;




        $kolom = 2;
        foreach ($bk_sinta  as $no => $b) {
            $sheet4->setCellValue('A' . $kolom, $no + 1);
            $sheet4->setCellValue('B' . $kolom, date('F Y', strtotime('01-' . $b->bulan . '-' . $b->tahun)));
            $sheet4->setCellValue('C' . $kolom, $b->nm_partai);
            $sheet4->setCellValue('D' . $kolom, $b->grade);
            $sheet4->setCellValue('E' . $kolom, $b->pcs_bk);
            $sheet4->setCellValue('F' . $kolom, $b->gr_bk);
            $sheet4->setCellValue('G' . $kolom, $b->cost_bk);
            $sheet4->setCellValue('H' . $kolom, $b->cost_cabut_dulu + $b->cost_eo_dulu + $b->cost_cetak_dulu + $b->cost_sortir_dulu + ($rp_gr * $b->gr_bk));
            $sheet4->setCellValue('I' . $kolom, $b->cost_cabut_berjalan + $b->cost_cetak_berjalan + $b->cost_sortir_berjalan + $b->cost_eo_berjalan);
            $sheet4->setCellValue('J' . $kolom, ($b->cost_bk + $b->cost_cabut_dulu + $b->cost_cetak_dulu + $b->cost_sortir_dulu + $b->cost_eo_dulu + $b->cost_cabut_berjalan + $b->cost_cetak_berjalan + $b->cost_sortir_berjalan + $b->cost_eo_berjalan) / $b->gr_bk);

            $kolom++;
        }
        $sheet4->setCellValue('A' . $kolom, "Total");
        $sheet4->setCellValue('B' . $kolom, '');
        $sheet4->setCellValue('C' . $kolom, '');
        $sheet4->setCellValue('D' . $kolom, '');
        $sheet4->setCellValue('E' . $kolom, "=SUM(E2:E" . $kolom - 1 . ")");
        $sheet4->setCellValue('F' . $kolom, "=SUM(F2:F" . $kolom - 1 . ")");
        $sheet4->setCellValue('G' . $kolom, "=SUM(G2:G" . $kolom - 1 . ")");
        $sheet4->setCellValue('H' . $kolom, "=SUM(H2:H" . $kolom - 1 . ")");
        $sheet4->setCellValue('I' . $kolom, "=SUM(I2:I" . $kolom - 1 . ")");
        $sheet4->setCellValue('J' . $kolom, 0);


        $sheet4->getStyle('A2:J' . $kolom - 1)->applyFromArray($style);
        $sheet4->getStyle("A$kolom:J$kolom")->applyFromArray($style_atas);

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
