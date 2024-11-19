<?php

namespace App\Http\Controllers;

use App\Models\BalanceModel;
use App\Models\Cabut;
use App\Models\CabutOpnameModel;
use App\Models\OpnameNewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OpnameSusutController extends Controller
{
    public function index(Request $request)
    {
        $pgws_cabut = CabutOpnameModel::cabut_susut();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.index', $data);
    }


    public function detail_cabut(Request $r)
    {
        $data = [
            'title' => 'Data Opname',
            'tipe' => $r->tipe,
            'nm_pengawas' => DB::table('users')->where('id', $r->id_pengawas)->first()->name,
            'box_stock' => CabutOpnameModel::cabut_susut_detail($r->id_pengawas, $r->tipe),
        ];
        return view('home.opnamesusut.detail_cabut', $data);
    }

    public function cetak(Request $request)
    {
        $pgws_cabut = CabutOpnameModel::cetak_susut_tampilan();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.cetak', $data);
    }
    public function sortir(Request $request)
    {
        $pgws_cabut = CabutOpnameModel::sortir_tampilan();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.sortir', $data);
    }


    public function costPartai(Request $r)
    {
        $bk = DB::select("SELECT a.nm_partai FROM bk as a  where a.kategori = 'cabut' and a.baru = 'baru' and a.no_box != 9999 group by a.nm_partai order by a.nm_partai ASC;");
        $data = [
            'title' => 'Cost Partai',
            'partai' => $bk,
        ];
        return view('home.opnamesusut.cost_partai', $data);
    }


    public function getCostpartai(Request $r)
    {
        $bk = DB::selectOne("SELECT a.nm_partai, a.tipe, a.ket, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a 
        where a.kategori = 'cabut' and a.baru = 'baru' and a.nm_partai = '$r->partai';");

        $data = [
            'bk' => $bk,
            'cabut' => CabutOpnameModel::cabutPartai($r->partai),
            'eo' => CabutOpnameModel::eotPartai($r->partai),
            'cetak' => CabutOpnameModel::cetakPartai($r->partai),
            'sortir' => CabutOpnameModel::sortirPartai($r->partai),
            'grading' => CabutOpnameModel::gradingPartai($r->partai),
            'gradingsusut' => CabutOpnameModel::gradingPartai_susut($r->partai),
        ];
        return view('home.opnamesusut.getcost_partai', $data);
    }

    public function detailGrade(Request $r)
    {
        $data = [
            'grade' => CabutOpnameModel::table_grade($r->nm_partai),
            'partai' => $r->nm_partai
        ];
        return view('home.opnamesusut.detail_grade', $data);
    }

    public function exportCostpartai(Request $r)
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
        $bk = DB::select("SELECT a.nm_partai, a.tipe, a.ket, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a 
        where a.kategori = 'cabut' and a.baru = 'baru' and a.no_box != 9999 group by a.nm_partai ");



        $title = 'Cost Partai';
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "$title");
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("A1:L1")->applyFromArray($style_atas);
        $sheet->setCellValue('A1', 'Partai');
        $sheet->setCellValue('B1', 'Ket');
        $sheet->setCellValue('C1', 'Grade');
        $sheet->setCellValue('D1', 'Pcs');
        $sheet->setCellValue('E1', 'Gr');
        $sheet->setCellValue('F1', 'Rp');
        $sheet->setCellValue('G1', 'Pcs tidak cetak');
        $sheet->setCellValue('H1', 'Gr tidak cetak');
        $sheet->setCellValue('I1', 'Pcs akhir');
        $sheet->setCellValue('J1', 'Gr akhir');
        $sheet->setCellValue('K1', 'Cost Rp');
        $sheet->setCellValue('L1', 'Rp/gr');

        $kolom = 2;
        foreach ($bk as $i => $d) {






            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'bk');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, round($d->pcs_awal, 0));
            $sheet->setCellValue('E' . $kolom, round($d->gr_awal, 0));
            $sheet->setCellValue('F' . $kolom, round($d->ttl_rp, 0));
            $sheet->setCellValue('G' . $kolom, 0);
            $sheet->setCellValue('H' . $kolom, 0);
            $sheet->setCellValue('I' . $kolom, 0);
            $sheet->setCellValue('J' . $kolom, 0);
            $sheet->setCellValue('K' . $kolom, round($d->ttl_rp, 0));
            $sheet->setCellValue('L' . $kolom, empty($d->gr_awal) ? 0 : round($d->ttl_rp / $d->gr_awal, 0));

            $kolom++;

            $cabut = CabutOpnameModel::cabutPartai($d->nm_partai);
            $eo = CabutOpnameModel::eotPartai($d->nm_partai);

            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'cabut');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, 0);
            $sheet->setCellValue('E' . $kolom, 0);
            $sheet->setCellValue('F' . $kolom, 0);
            $sheet->setCellValue('G' . $kolom, 0);
            $sheet->setCellValue('H' . $kolom, 0);
            $gr_cabut =  $cabut->gr ?? 0;
            $ttl_rp_cabut = $cabut->ttl_rp ?? 0;
            $gr_eo = $eo->gr ?? 0;
            $ttl_rp_eo = $eo->ttl_rp ?? 0;

            $sheet->setCellValue('I' . $kolom, round($cabut->pcs ?? 0, 0));
            $sheet->setCellValue('J' . $kolom, round($gr_cabut + $gr_eo, 0));
            $sheet->setCellValue('K' . $kolom, round($ttl_rp_cabut + $ttl_rp_eo, 0));
            $sheet->setCellValue('L' . $kolom, $ttl_rp_cabut + $ttl_rp_eo == 0 ? 0 : round(($ttl_rp_cabut + $ttl_rp_eo) / ($gr_cabut + $gr_eo), 0));
            $kolom++;

            $cetak = CabutOpnameModel::cetakPartai($d->nm_partai);
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'cetak');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, 0);
            $sheet->setCellValue('E' . $kolom, 0);
            $sheet->setCellValue('F' . $kolom, 0);
            $sheet->setCellValue('G' . $kolom, round($cetak->pcs_tdk, 0));
            $sheet->setCellValue('H' . $kolom, round($cetak->gr_tdk, 0));
            $sheet->setCellValue('I' . $kolom, round($cetak->pcs ?? 0, 0));
            $sheet->setCellValue('J' . $kolom, round($cetak->gr ?? 0, 0));
            $sheet->setCellValue('K' . $kolom, round($cetak->ttl_rp, 0));
            $sheet->setCellValue('L' . $kolom, empty($cetak->gr) ? 0 : round($cetak->ttl_rp / $cetak->gr, 0));
            $kolom++;

            $sortir = CabutOpnameModel::sortirPartai($d->nm_partai);
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'sortir');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, 0);
            $sheet->setCellValue('E' . $kolom, 0);
            $sheet->setCellValue('F' . $kolom, 0);
            $sheet->setCellValue('G' . $kolom, 0);
            $sheet->setCellValue('H' . $kolom, 0);
            $sheet->setCellValue('I' . $kolom, round($sortir->pcs ?? 0, 0));
            $sheet->setCellValue('J' . $kolom, round($sortir->gr ?? 0, 0));
            $sheet->setCellValue('K' . $kolom, round($sortir->ttl_rp ?? 0, 0));
            $sheet->setCellValue('L' . $kolom, empty($sortir->gr) ? 0 : round($sortir->ttl_rp / $sortir->gr, 0));
            $kolom++;

            $grading = CabutOpnameModel::gradingPartai($d->nm_partai);
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'grading');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, 0);
            $sheet->setCellValue('E' . $kolom, 0);
            $sheet->setCellValue('F' . $kolom, 0);
            $sheet->setCellValue('G' . $kolom, 0);
            $sheet->setCellValue('H' . $kolom, 0);
            $sheet->setCellValue('I' . $kolom, round($grading->pcs ?? 0, 0));
            $sheet->setCellValue('J' . $kolom, round($grading->gr ?? 0, 0));
            $sheet->setCellValue('K' . $kolom, round($grading->ttl_rp ?? 0, 0));
            $sheet->setCellValue('L' . $kolom, empty($grading->gr) ? 0 : round($grading->ttl_rp / $grading->gr, 0));
            $kolom++;
        }
        $sheet->getStyle("A1:L" . $kolom - 1)->applyFromArray($style);
        $namafile = "Cost Per Partai.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
    public function exportCostperpartai(Request $r)
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
        $style_footer = [
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
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
        $spreadsheet = new Spreadsheet();
        $dataBulan = DB::table('oprasional')->groupBy('bulan')->selectRaw('bulan, tahun')->get();

        $bk = DB::select("SELECT a.nm_partai, a.tipe, a.ket, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a 
        where a.kategori = 'cabut' and a.baru = 'baru' and a.no_box != 9999 and a.nm_partai = '$r->partai'
        group by a.nm_partai ");



        $title = 'Cost Partai';
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "$title");
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("A1:M1")->applyFromArray($style_atas);
        $sheet->setCellValue('A1', 'Partai');
        $sheet->setCellValue('B1', 'Ket');
        $sheet->setCellValue('C1', 'Grade');
        $sheet->setCellValue('D1', 'Pcs');
        $sheet->setCellValue('E1', 'Gr');
        $sheet->setCellValue('F1', 'Rp');
        $sheet->setCellValue('G1', 'Pcs tidak cetak');
        $sheet->setCellValue('H1', 'Gr tidak cetak');
        $sheet->setCellValue('I1', 'Pcs akhir');
        $sheet->setCellValue('J1', 'Gr akhir');
        $sheet->setCellValue('K1', 'Susut');
        $sheet->setCellValue('L1', 'Cost Rp');
        $sheet->setCellValue('M1', 'Rp/gr');

        $kolom = 2;
        foreach ($bk as $i => $d) {
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'bk');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, round($d->pcs_awal, 0));
            $sheet->setCellValue('E' . $kolom, round($d->gr_awal, 0));
            $sheet->setCellValue('F' . $kolom, round($d->ttl_rp, 0));
            $sheet->setCellValue('G' . $kolom, 0);
            $sheet->setCellValue('H' . $kolom, 0);
            $sheet->setCellValue('I' . $kolom, 0);
            $sheet->setCellValue('J' . $kolom, 0);
            $sheet->setCellValue('K' . $kolom, 0);
            $sheet->setCellValue('L' . $kolom, round($d->ttl_rp, 0));
            $sheet->setCellValue('M' . $kolom, empty($d->gr_awal) ? 0 : round($d->ttl_rp / $d->gr_awal, 0));

            $kolom++;

            $cabut = CabutOpnameModel::cabutPartai($d->nm_partai);
            $eo = CabutOpnameModel::eotPartai($d->nm_partai);

            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'cabut');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, 0);
            $sheet->setCellValue('E' . $kolom, 0);
            $sheet->setCellValue('F' . $kolom, 0);
            $sheet->setCellValue('G' . $kolom, 0);
            $sheet->setCellValue('H' . $kolom, 0);
            $ttl_rp_cabut = $cabut->ttl_rp ?? 0;
            $ttl_rp_eo = $eo->ttl_rp ?? 0;

            $gr_eo = $eo->gr ?? 0;
            $gr_cabut =  $cabut->gr ?? 0;
            $gr_awal_cabut = $cabut->gr_awal ?? 0;
            $gr_eo_awal = $eo->gr_eo_awal ?? 0;

            $sheet->setCellValue('I' . $kolom, round($cabut->pcs ?? 0, 0));
            $sheet->setCellValue('J' . $kolom, round($gr_cabut + $gr_eo, 0));
            $sheet->setCellValue('K' . $kolom, round((1 - ($gr_cabut + $gr_eo) / ($gr_awal_cabut + $gr_eo_awal)) * 100, 0));
            $sheet->setCellValue('L' . $kolom, round($ttl_rp_cabut + $ttl_rp_eo, 0));
            $sheet->setCellValue('M' . $kolom, "=SUM(L2:L3)/J3");
            $kolom++;

            $cetak = CabutOpnameModel::cetakPartai($d->nm_partai);
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'cetak');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, 0);
            $sheet->setCellValue('E' . $kolom, 0);
            $sheet->setCellValue('F' . $kolom, 0);
            $sheet->setCellValue('G' . $kolom, round($cetak->pcs_tdk, 0));
            $sheet->setCellValue('H' . $kolom, round($cetak->gr_tdk, 0));
            $sheet->setCellValue('I' . $kolom, round($cetak->pcs ?? 0, 0));
            $sheet->setCellValue('J' . $kolom, round($cetak->gr ?? 0, 0));
            $sheet->setCellValue('K' . $kolom, round((1 - $cetak->gr / $cetak->gr_awal) * 100, 0));
            $sheet->setCellValue('L' . $kolom, round($cetak->ttl_rp, 0));
            $sheet->setCellValue('M' . $kolom, "=SUM(L2:L4)/J4");
            $kolom++;

            $sortir = CabutOpnameModel::sortirPartai($d->nm_partai);
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'sortir');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, 0);
            $sheet->setCellValue('E' . $kolom, 0);
            $sheet->setCellValue('F' . $kolom, 0);
            $sheet->setCellValue('G' . $kolom, 0);
            $sheet->setCellValue('H' . $kolom, 0);
            $sheet->setCellValue('I' . $kolom, round($sortir->pcs ?? 0, 0));
            $gr_awal_sortir = $sortir->gr_awal ?? 0;
            $gr_akhir_sortir = $sortir->gr ?? 0;
            $sheet->setCellValue('J' . $kolom, round($sortir->gr ?? 0, 0));
            $sheet->setCellValue('K' . $kolom, round((1 - $gr_akhir_sortir / $gr_awal_sortir) * 100, 0));
            $sheet->setCellValue('L' . $kolom, round($sortir->ttl_rp ?? 0, 0));
            $sheet->setCellValue('M' . $kolom, "=SUM(L2:L5)/J5");
            $kolom++;

            $grading = CabutOpnameModel::gradingPartai($d->nm_partai);
            $gradingsusut = CabutOpnameModel::gradingPartai_susut($d->nm_partai);

            $gr_susut = $gradingsusut->gr ?? 0;
            $gr_grading = $grading->gr ?? 0;
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, 'grading');
            $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
            $sheet->setCellValue('D' . $kolom, 0);
            $sheet->setCellValue('E' . $kolom, 0);
            $sheet->setCellValue('F' . $kolom, 0);
            $sheet->setCellValue('G' . $kolom, 0);
            $sheet->setCellValue('H' . $kolom, 0);
            $sheet->setCellValue('I' . $kolom, round($grading->pcs ?? 0, 0));
            $sheet->setCellValue('J' . $kolom, round($grading->gr ?? 0, 0));
            $sheet->setCellValue('K' . $kolom, round((1 - $grading->gr / ($gr_susut + $gr_grading)) * 100, 0));
            $sheet->setCellValue('L' . $kolom, round($grading->ttl_rp ?? 0, 0));
            $sheet->setCellValue('M' . $kolom, "=SUM(L2:L6)/J6");
            $kolom++;
        }
        $sheet->getStyle("A1:M" . $kolom - 1)->applyFromArray($style);
        $sheet->setCellValue('A' . $kolom, 'Total');
        $sheet->setCellValue('B' . $kolom, '');
        $sheet->setCellValue('C' . $kolom, '');
        $sheet->setCellValue('D' . $kolom, "=SUM(D2:D6)");
        $sheet->setCellValue('E' . $kolom, "=SUM(E2:E6)");
        $sheet->setCellValue('F' . $kolom, "=SUM(F2:F6)");
        $sheet->setCellValue('G' . $kolom, "=SUM(G2:G6)");
        $sheet->setCellValue('H' . $kolom, "=SUM(H2:H6)");
        $sheet->setCellValue('I' . $kolom, "=I6");
        $sheet->setCellValue('J' . $kolom, "=J6");
        $sheet->setCellValue('K' . $kolom, 0);
        $sheet->setCellValue('L' . $kolom, "=SUM(K2:K6)");
        $sheet->setCellValue('M' . $kolom, "=K7/J7");
        $sheet->getStyle("A$kolom:M$kolom")->applyFromArray($style_footer);



        $sheet->getStyle("A" . $kolom + 2 . ":C" .  $kolom + 2)->applyFromArray($style_atas);
        $kolom2 = $kolom + 2;
        $sheet->setCellValue('A' . $kolom2, 'Grade');
        $sheet->setCellValue('B' . $kolom2, 'Pcs');
        $sheet->setCellValue('C' . $kolom2, 'Gr');

        $grade = CabutOpnameModel::table_grade($r->partai);

        $kolom_awal = $kolom2 + 1;
        $kolom3 = $kolom2 + 1;
        foreach ($grade as $d) {
            $sheet->setCellValue('A' . $kolom3, $d->grade);
            $sheet->setCellValue('B' . $kolom3, round($d->pcs, 0));
            $sheet->setCellValue('C' . $kolom3, round($d->gr, 0));
            $kolom3++;
        }
        $sheet->setCellValue('A' . $kolom3, "Total");
        $sheet->setCellValue('B' . $kolom3, "=sum(B$kolom_awal:B$kolom3)");
        $sheet->setCellValue('C' . $kolom3, "=sum(C$kolom_awal:C$kolom3)");

        $sheet->getStyle("A" . $kolom3 . ":C" .  $kolom3)->applyFromArray($style_footer);
        $sheet->getStyle("A" . $kolom + 3 . ":C" .  $kolom3 - 1)->applyFromArray($style);






        $namafile = "Cost Per Partai.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
