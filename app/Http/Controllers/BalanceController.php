<?php

namespace App\Http\Controllers;

use App\Models\BalanceModel;
use App\Models\CocokanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BalanceController extends Controller
{

    public function index(Request $r)
    {
        $bulan = $r->bulan;
        $tahun = $r->tahun;

        $cabut = BalanceModel::cabut($bulan, $tahun);
        $cetak = BalanceModel::cetak($bulan, $tahun);
        $sortir = BalanceModel::sortir($bulan, $tahun);

        $dataBulan = DB::table('oprasional')->groupBy('bulan')->selectRaw('bulan, tahun')->get();
        $operasional = DB::table('oprasional')->where('bulan', $bulan)->where('tahun', $tahun)->first();
        $grading = BalanceModel::gradingOne($bulan, $tahun);

        $data = [
            'title' => 'Cost Gaji Proses',
            'dataBulan' => $dataBulan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'cabut' => $cabut,
            'cetak' => $cetak,
            'sortir' => $sortir,
            'operasional' => $operasional,
            'grading' => $grading
        ];
        return view('home.cocokan.balance.index', $data);
    }
    public function cost(Request $r)
    {
        $bulan = $r->bulan;
        $tahun = $r->tahun;
        $dataBulan = DB::table('oprasional')->groupBy('bulan')->selectRaw('bulan, tahun')->get();
        $grading = BalanceModel::grading($bulan, $tahun);
        $data = [
            'title' => 'Cost Operasional Beban Digrading',
            'dataBulan' => $dataBulan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'grading' => $grading,
        ];
        return view('home.cocokan.balance.cost', $data);
    }

    public function CostGajiProses()
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
        $dataBulan = DB::table('oprasional')->groupBy('bulan')->selectRaw('bulan, tahun')->get();


        foreach ($dataBulan as $d) {
            $title = formatTglGaji($d->bulan, $d->tahun);
            $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "$title");
            $sheet  = $spreadsheet->addSheet($worksheet);
            $sheet->getStyle("A1:H1")->applyFromArray($style_atas);
            $sheet->getStyle("A2:H5")->applyFromArray($style);
            $sheet->setCellValue('A1', 'Bulan');
            $sheet->setCellValue('B1', 'Kerja');
            $sheet->setCellValue('C1', 'Gaji');
            $sheet->setCellValue('D1', 'Cost Operasional');
            $sheet->setCellValue('E1', 'Total Gaji');
            $sheet->setCellValue('F1', 'Pcs Akhir');
            $sheet->setCellValue('G1', 'Gr Akhir');
            $sheet->setCellValue('H1', 'Rp/gr');

            $cabut = BalanceModel::cabut($d->bulan, $d->tahun);
            $cetak = BalanceModel::cetak($d->bulan, $d->tahun);
            $sortir = BalanceModel::sortir($d->bulan, $d->tahun);
            $operasional = DB::table('oprasional')->where('bulan', $d->bulan)->where('tahun', $d->tahun)->first();
            $grading = BalanceModel::gradingOne($d->bulan, $d->tahun);

            $sheet->setCellValue('A2', formatTglGaji($d->bulan, $d->tahun));
            $sheet->setCellValue('B2', 'Cabut');
            $sheet->setCellValue('C2', round($cabut->cost, 0));
            $sheet->setCellValue('D2', 0);
            $sheet->setCellValue('E2', 0);
            $sheet->setCellValue('F2', round($cabut->pcs, 0));
            $sheet->setCellValue('G2', round($cabut->gr, 0));
            $sheet->setCellValue('H2', round($cabut->cost / $cabut->gr, 0));

            $sheet->setCellValue('A3', formatTglGaji($d->bulan, $d->tahun));
            $sheet->setCellValue('B3', 'Cetak');
            $sheet->setCellValue('C3', round($cetak->cost_kerja, 0));
            $sheet->setCellValue('D3', 0);
            $sheet->setCellValue('E3', 0);
            $sheet->setCellValue('F3', round($cetak->pcs, 0));
            $sheet->setCellValue('G3', round($cetak->gr, 0));
            $sheet->setCellValue('H3', round($cetak->cost_kerja / $cetak->gr, 0));

            $sheet->setCellValue('A4', formatTglGaji($d->bulan, $d->tahun));
            $sheet->setCellValue('B4', 'Sortir');
            $sheet->setCellValue('C4', round($sortir->cost_kerja, 0));
            $sheet->setCellValue('D4', 0);
            $sheet->setCellValue('E4', 0);
            $sheet->setCellValue('F4', round($sortir->pcs, 0));
            $sheet->setCellValue('G4', round($sortir->gr, 0));
            $sheet->setCellValue('H4', round($sortir->cost_kerja / $sortir->gr, 0));

            $sheet->setCellValue('A5', formatTglGaji($d->bulan, $d->tahun));
            $sheet->setCellValue('B5', 'Grading');
            $sheet->setCellValue('C5', 0);
            $sheet->setCellValue('D5', round($operasional->total_operasional) ? 0 : number_format($operasional->total_operasional - $cabut->cost - $cetak->cost_kerja - $sortir->cost_kerja, 0));
            $sheet->setCellValue('E5', round($operasional->total_operasional ?? 0));
            $sheet->setCellValue('F5', round($grading->pcs, 0));
            $sheet->setCellValue('G5', round($grading->gr, 0));
            $sheet->setCellValue('H5', empty($operasional->total_operasional) ? 0 : round(($operasional->total_operasional - $cabut->cost - $cetak->cost_kerja - $sortir->cost_kerja) / $grading->gr, 0));
        }
        $title = formatTglGaji($d->bulan + 1, $d->tahun);
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "$title");
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("A1:H1")->applyFromArray($style_atas);
        $sheet->getStyle("A2:H5")->applyFromArray($style);
        $sheet->setCellValue('A1', 'Bulan');
        $sheet->setCellValue('B1', 'Kerja');
        $sheet->setCellValue('C1', 'Gaji');
        $sheet->setCellValue('D1', 'Cost Operasional');
        $sheet->setCellValue('E1', 'Total Gaji');
        $sheet->setCellValue('F1', 'Pcs Akhir');
        $sheet->setCellValue('G1', 'Gr Akhir');
        $sheet->setCellValue('H1', 'Rp/gr');

        $cabut = BalanceModel::cabut($d->bulan + 1, $d->tahun);
        $cetak = BalanceModel::cetak($d->bulan + 1, $d->tahun);
        $sortir = BalanceModel::sortir($d->bulan + 1, $d->tahun);
        $operasional = DB::table('oprasional')->where('bulan', $d->bulan + 1)->where('tahun', $d->tahun)->first();
        $grading = BalanceModel::gradingOne($d->bulan + 1, $d->tahun);

        $sheet->setCellValue('A2', formatTglGaji($d->bulan + 1, $d->tahun));
        $sheet->setCellValue('B2', 'Cabut');
        $sheet->setCellValue('C2', round($cabut->cost, 0));
        $sheet->setCellValue('D2', 0);
        $sheet->setCellValue('E2', 0);
        $sheet->setCellValue('F2', round($cabut->pcs, 0));
        $sheet->setCellValue('G2', round($cabut->gr, 0));
        $sheet->setCellValue('H2', round($cabut->cost / $cabut->gr, 0));

        $sheet->setCellValue('A3', formatTglGaji($d->bulan + 1, $d->tahun));
        $sheet->setCellValue('B3', 'Cetak');
        $sheet->setCellValue('C3', round($cetak->cost_kerja, 0));
        $sheet->setCellValue('D3', 0);
        $sheet->setCellValue('E3', 0);
        $sheet->setCellValue('F3', round($cetak->pcs, 0));
        $sheet->setCellValue('G3', round($cetak->gr, 0));
        $sheet->setCellValue('H3', round($cetak->cost_kerja / $cetak->gr, 0));

        $sheet->setCellValue('A4', formatTglGaji($d->bulan + 1, $d->tahun));
        $sheet->setCellValue('B4', 'Sortir');
        $sheet->setCellValue('C4', round($sortir->cost_kerja, 0));
        $sheet->setCellValue('D4', 0);
        $sheet->setCellValue('E4', 0);
        $sheet->setCellValue('F4', round($sortir->pcs, 0));
        $sheet->setCellValue('G4', round($sortir->gr, 0));
        $sheet->setCellValue('H4', round($sortir->cost_kerja / $sortir->gr, 0));

        $sheet->setCellValue('A5', formatTglGaji($d->bulan + 1, $d->tahun));
        $sheet->setCellValue('B5', 'Grading');
        $sheet->setCellValue('C5', 0);
        $sheet->setCellValue('D5', 0);
        $sheet->setCellValue('E5', round($operasional->total_operasional ?? 0));
        $sheet->setCellValue('F5', round($grading->pcs, 0));
        $sheet->setCellValue('G5', round($grading->gr, 0));
        $sheet->setCellValue('H5', empty($operasional->total_operasional) ? 0 : round(($operasional->total_operasional - $cabut->cost - $cetak->cost_kerja - $sortir->cost_kerja) / $grading->gr, 0));






        $namafile = "Cost Gaji Proses.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }


    public function CostOperasionalBebanDigrading()
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
        $dataBulan = DB::table('oprasional')->groupBy('bulan')->selectRaw('bulan, tahun')->get();


        foreach ($dataBulan as $d) {
            $title = formatTglGaji($d->bulan, $d->tahun);
            $grading = BalanceModel::grading($d->bulan, $d->tahun);
            $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "$title");
            $sheet  = $spreadsheet->addSheet($worksheet);
            $sheet->getStyle("A1:I1")->applyFromArray($style_atas);
            $sheet->setCellValue('A1', 'Bulan');
            $sheet->setCellValue('B1', 'Box Grading');
            $sheet->setCellValue('C1', 'Grade');
            $sheet->setCellValue('D1', 'Pcs');
            $sheet->setCellValue('E1', 'Gr');
            $sheet->setCellValue('F1', 'Cost Bk');
            $sheet->setCellValue('G1', 'Cost Op');
            $sheet->setCellValue('H1', 'Total');
            $sheet->setCellValue('I1', 'Rata2');

            $kolom = 2;
            foreach ($grading as $i => $d) {
                $sheet->setCellValue('A' . $kolom, formatTglGaji($d->bulan, $d->tahun));
                $sheet->setCellValue('B' . $kolom, 'P' . $d->box_grading);
                $sheet->setCellValue('C' . $kolom, strtoupper($d->grade));
                $sheet->setCellValue('D' . $kolom, round($d->pcs, 0));
                $sheet->setCellValue('E' . $kolom, round($d->gr, 0));
                $sheet->setCellValue('F' . $kolom, round($d->cost_bk, 0));
                $sheet->setCellValue('G' . $kolom, round($d->cost_op, 0));
                $sheet->setCellValue('H' . $kolom, round($d->cost_bk + $d->cost_op, 0));
                $sheet->setCellValue('I' . $kolom, round(($d->cost_bk + $d->cost_op) / $d->gr, 0));
                $kolom++;
            }
            $sheet->getStyle("A1:I$kolom")->applyFromArray($style);
        }
        $title = formatTglGaji($d->bulan + 1, $d->tahun);
        $grading = BalanceModel::grading($d->bulan + 1, $d->tahun);
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "$title");
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("A1:I1")->applyFromArray($style_atas);
        $sheet->setCellValue('A1', 'Bulan');
        $sheet->setCellValue('B1', 'Box Grading');
        $sheet->setCellValue('C1', 'Grade');
        $sheet->setCellValue('D1', 'Pcs');
        $sheet->setCellValue('E1', 'Gr');
        $sheet->setCellValue('F1', 'Cost Bk');
        $sheet->setCellValue('G1', 'Cost Op');
        $sheet->setCellValue('H1', 'Total');
        $sheet->setCellValue('I1', 'Rata2');

        $kolom = 2;
        foreach ($grading as $i => $d) {
            $sheet->setCellValue('A' . $kolom, formatTglGaji($d->bulan, $d->tahun));
            $sheet->setCellValue('B' . $kolom, 'P' . $d->box_grading);
            $sheet->setCellValue('C' . $kolom, strtoupper($d->grade));
            $sheet->setCellValue('D' . $kolom, round($d->pcs, 0));
            $sheet->setCellValue('E' . $kolom, round($d->gr, 0));
            $sheet->setCellValue('F' . $kolom, round($d->cost_bk, 0));
            $sheet->setCellValue('G' . $kolom, round($d->cost_op, 0));
            $sheet->setCellValue('H' . $kolom, round($d->cost_bk + $d->cost_op, 0));
            $sheet->setCellValue('I' . $kolom, round(($d->cost_bk + $d->cost_op) / $d->gr, 0));
            $kolom++;
        }
        $sheet->getStyle("A1:I$kolom")->applyFromArray($style);







        $namafile = "Cost Gaji Proses.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
