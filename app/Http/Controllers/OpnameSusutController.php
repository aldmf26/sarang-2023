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
        $pgws_cabut = CabutOpnameModel::cabut_susut();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.cetak', $data);
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
        ];
        return view('home.opnamesusut.getcost_partai', $data);
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
}
