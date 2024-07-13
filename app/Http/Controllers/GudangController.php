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

        ];
        return view('home.gudang.index', $data);
    }

    public function cetak(Request $r)
    {
        $data = [
            'title' => 'Data Gudang Cetak',
            'cabut_selesai' => CetakModel::cabut_selesai(0),
            'cetak_proses' => CetakModel::cetak_proses(0),
            'cetak_selesai' => CetakModel::cetak_selesai(0),
        ];
        return view('home.gudang.cetak', $data);
    }
    public function sortir(Request $r)
    {
        $id_user = auth()->user()->id;
        $data = [
            'title' => 'Data Gudang Sortir',
            'siap_sortir' => Sortir::siap_sortir($id_user),
            'sortir_proses' => Sortir::sortir_proses($id_user),
            'sortir_selesai' => Sortir::sortir_selesai($id_user),
        ];
        return view('home.gudang.sortir', $data);
    }
    public function grading(Request $r)
    {
        $data = [
            'title' => 'Data Gudang Sortir',
            'grading' => Grading::grading_stock(),
            'gradingbox' => Grading::gradingbox(),
            'gradingboxkirim' => Grading::gradingboxkirim(),
        ];
        return view('home.gudang.grading', $data);
    }
    public function pengiriman(Request $r)
    {
        $data = [
            'title' => 'Data Gudang Sortir',
            'grading' => Grading::grading_stock(),
            'gradingbox' => Grading::gradingbox(),
            'gradingboxkirim' => Grading::gradingboxkirim(),
        ];
        return view('home.gudang.pengiriman', $data);
    }
    public function totalan(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $id_user = auth()->user()->id;
        $gudang = Cabut::gudang($bulan, $tahun, $id_user);
        // dd(Grading::gradingbox());

        $data = [
            'title' => 'Data Totalan',
            'bk' => $gudang->bk,
            'cabut' => $gudang->cabut,
            'cabutSelesai' => $gudang->cabutSelesai,
            'eoSelesai' => $gudang->eoSelesai,

            'cabut_selesai' => CetakModel::cabut_selesai(0),
            'cetak_proses' => CetakModel::cetak_proses(0),
            'cetak_selesai' => CetakModel::cetak_selesai(0),

            'siap_sortir' => Sortir::siap_sortir($id_user),
            'sortir_proses' => Sortir::sortir_proses($id_user),
            'sortir_selesai' => Sortir::sortir_selesai($id_user),

            'grading' => Grading::grading_stock(),
            'gradingbox' => Grading::gradingbox(),
            'gradingboxkirim' => Grading::gradingboxkirim(),
        ];
        return view('home.gudang.totalan', $data);
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
        $sheet1->setTitle('Gudang Bk');


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
            $sheet1->setCellValue('G' . $kolom, round($d->hrga_satuan, 0));
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
            $sheet1->setCellValue('O' . $kolom2, round($d->hrga_satuan, 0));
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
            $sheet1->setCellValue('W' . $kolom3, round($d->hrga_satuan, 0));
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
            $sheet1->setCellValue('AE' . $kolom4, round($d->hrga_satuan, 0));
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

        $cetak_selesai = CetakModel::cetak_selesai(0);
        $kolom4 = 2;
        foreach ($cetak_selesai as $d) {
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

        $sortir_stock = Sortir::siap_sortir($id_user);
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

        $sortir_proses = Sortir::sortir_proses($id_user);
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

        // batas ke empat
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet4 = $spreadsheet->getActiveSheet(3);
        $sheet4->setTitle('Grading');

        $sheet4->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'Grading stock');
        $sheet4->setCellValue('B1', 'Pemilik');
        $sheet4->setCellValue('C1', 'Penerima');
        $sheet4->setCellValue('D1', 'Partai');
        $sheet4->setCellValue('E1', 'No Box');
        $sheet4->setCellValue('F1', 'Pcs');
        $sheet4->setCellValue('G1', 'Gr');
        $sheet4->setCellValue('H1', 'Rp/gr');

        $grading_stock = Grading::grading_stock();
        $kolom2 = 2;
        foreach ($grading_stock as $d) {
            $sheet4->setCellValue('B' . $kolom2, $d->pemilik);
            $sheet4->setCellValue('C' . $kolom2, $d->penerima);
            $sheet4->setCellValue('D' . $kolom2, $d->nm_partai);
            $sheet4->setCellValue('E' . $kolom2, $d->no_box_sortir);
            $sheet4->setCellValue('F' . $kolom2, $d->pcs);
            $sheet4->setCellValue('G' . $kolom2, $d->gr);
            $sheet4->setCellValue('H' . $kolom2, round(($d->cost_bk + $d->cost_cbt + $d->cost_ctk + $d->cost_eo + $d->cost_str) / $d->gr_awal, 0));
            $kolom2++;
        }
        $sheet4->getStyle('B2:H' . $kolom2 - 1)->applyFromArray($style);


        // batas ke lima
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(4);
        $sheet5 = $spreadsheet->getActiveSheet(4);
        $sheet5->setTitle('Pengiriman');


        $sheet5->getStyle("B1:G1")->applyFromArray($style_atas);
        $sheet5->setCellValue('A1', 'Box Belum Kirim');
        $sheet5->setCellValue('B1', 'Pengawas');
        $sheet5->setCellValue('C1', 'No Box Kirim');
        $sheet5->setCellValue('D1', 'Grade');
        $sheet5->setCellValue('E1', 'Pcs');
        $sheet5->setCellValue('F1', 'Gr');
        $sheet5->setCellValue('G1', 'Rp/gr');

        $gradingbox = Grading::gradingbox();
        $kolom3 = 2;
        foreach ($gradingbox as $d) {
            $sheet5->setCellValue('B' . $kolom3, $d->penerima);
            $sheet5->setCellValue('C' . $kolom3, $d->no_box_grading);
            $sheet5->setCellValue('D' . $kolom3, $d->nm_grade);
            $sheet5->setCellValue('E' . $kolom3, $d->pcs_grading);
            $sheet5->setCellValue('F' . $kolom3, $d->gr_grading);
            $sheet5->setCellValue('G' . $kolom3, round($d->ttl_rp / $d->gr_grading, 0));
            $kolom3++;
        }
        $sheet5->getStyle('B2:G' . $kolom3 - 1)->applyFromArray($style);

        $sheet5->getStyle("J1:O1")->applyFromArray($style_atas);
        $sheet5->setCellValue('I1', 'Box Selesai Kirim');
        $sheet5->setCellValue('J1', 'Pengawas');
        $sheet5->setCellValue('K1', 'No Box Kirim');
        $sheet5->setCellValue('L1', 'Grade');
        $sheet5->setCellValue('M1', 'Pcs');
        $sheet5->setCellValue('N1', 'Gr');
        $sheet5->setCellValue('O1', 'Rp/gr');

        $gradingboxkirim = Grading::gradingboxkirim();
        $kolom4 = 2;
        foreach ($gradingboxkirim as $d) {
            $sheet5->setCellValue('J' . $kolom4, $d->penerima);
            $sheet5->setCellValue('K' . $kolom4, $d->no_box_grading);
            $sheet5->setCellValue('L' . $kolom4, $d->nm_grade);
            $sheet5->setCellValue('M' . $kolom4, $d->pcs_grading);
            $sheet5->setCellValue('N' . $kolom4, $d->gr_grading);
            $sheet5->setCellValue('O' . $kolom4, round($d->ttl_rp / $d->gr_grading, 0));
            $kolom4++;
        }
        $sheet5->getStyle('J2:O' . $kolom4 - 1)->applyFromArray($style);

        // batas ke enam
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(5);
        $sheet6 = $spreadsheet->getActiveSheet(5);
        $sheet6->setTitle('Totalan');

        // box stock
        $sheet6->getStyle("B1:E1")->applyFromArray($style_atas);
        $sheet6->setCellValue('A1', 'Box Stock');
        $sheet6->setCellValue('B1', 'Ttl Box');
        $sheet6->setCellValue('C1', 'Pcs');
        $sheet6->setCellValue('D1', 'Gr');
        $sheet6->setCellValue('E1', 'Rp/gr');

        $cost_bk = 0;
        foreach ($gudang->bk as $d) {
            $cost_bk += $d->hrga_satuan * $d->gr;
        }
        $sheet6->setCellValue('B2', count($gudang->bk));
        $sheet6->setCellValue('C2', array_sum(array_column($gudang->bk, 'pcs')));
        $sheet6->setCellValue('D2', array_sum(array_column($gudang->bk, 'gr')));
        if (array_sum(array_column($gudang->bk, 'gr')) != 0) {
            $sheet6->setCellValue('E2', round($cost_bk / array_sum(array_column($gudang->bk, 'gr')), 0));
        } else {
            $sheet6->setCellValue('E2', 0);
        }
        $sheet6->getStyle('B2:E2')->applyFromArray($style);
        // box stock


        // box sedang Proses
        $sheet6->getStyle("H1:K1")->applyFromArray($style_atas);
        $sheet6->setCellValue('G1', 'Box Sedang Proses');
        $sheet6->setCellValue('H1', 'Ttl Box');
        $sheet6->setCellValue('I1', 'Pcs');
        $sheet6->setCellValue('J1', 'Gr');
        $sheet6->setCellValue('K1', 'Rp/gr');
        $cost_bk_proses = 0;
        foreach ($gudang->cabut as $d) {
            $cost_bk_proses += $d->hrga_satuan * $d->gr;
        }
        $sheet6->setCellValue('H2', count($gudang->cabut));
        $sheet6->setCellValue('I2', array_sum(array_column($gudang->cabut, 'pcs')));
        $sheet6->setCellValue('J2', array_sum(array_column($gudang->cabut, 'gr')));
        if (array_sum(array_column($gudang->cabut, 'gr')) != 0) {
            $sheet6->setCellValue('K2', round($cost_bk / array_sum(array_column($gudang->cabut, 'gr')), 0));
        } else {
            $sheet6->setCellValue('K2', 0);
        }
        $sheet6->getStyle('H2:K2')->applyFromArray($style);
        // box sedang Proses

        // box Selesai Siap Cetak
        $sheet6->getStyle("N1:Q1")->applyFromArray($style_atas);
        $sheet6->setCellValue('M1', 'Box Selesai Siap Cetak');
        $sheet6->setCellValue('N1', 'Ttl Box');
        $sheet6->setCellValue('O1', 'Pcs');
        $sheet6->setCellValue('P1', 'Gr');
        $sheet6->setCellValue('Q1', 'Rp/gr');
        $cost_cabutSelesai = 0;
        foreach ($gudang->cabutSelesai as $d) {
            $cost_cabutSelesai += $d->hrga_satuan * $d->gr;
        }
        $sheet6->setCellValue('N2', count($gudang->cabutSelesai));
        $sheet6->setCellValue('O2', array_sum(array_column($gudang->cabutSelesai, 'pcs')));
        $sheet6->setCellValue('P2', array_sum(array_column($gudang->cabutSelesai, 'gr')));
        if (array_sum(array_column($gudang->cabutSelesai, 'gr')) != 0) {
            $sheet6->setCellValue('Q2', round($cost_cabutSelesai / array_sum(array_column($gudang->cabutSelesai, 'gr')), 0));
        } else {
            $sheet6->setCellValue('Q2', 0);
        }
        $sheet6->getStyle('N2:Q2')->applyFromArray($style);
        // box Selesai Siap Cetak

        // box Selesai Siap Sortir
        $sheet6->getStyle("T1:W1")->applyFromArray($style_atas);
        $sheet6->setCellValue('S1', 'Box Selesai Siap Sortir');
        $sheet6->setCellValue('T1', 'Ttl Box');
        $sheet6->setCellValue('U1', 'Pcs');
        $sheet6->setCellValue('V1', 'Gr');
        $sheet6->setCellValue('W1', 'Rp/gr');
        $cost_eoSelesai = 0;
        foreach ($gudang->eoSelesai as $d) {
            $cost_eoSelesai += $d->hrga_satuan * $d->gr;
        }
        $sheet6->setCellValue('T2', count($gudang->eoSelesai));
        $sheet6->setCellValue('U2', array_sum(array_column($gudang->eoSelesai, 'pcs')));
        $sheet6->setCellValue('V2', array_sum(array_column($gudang->eoSelesai, 'gr')));
        if (array_sum(array_column($gudang->eoSelesai, 'gr')) != 0) {
            $sheet6->setCellValue('W2', round($cost_eoSelesai / array_sum(array_column($gudang->eoSelesai, 'gr')), 0));
        } else {
            $sheet6->setCellValue('W2', 0);
        }
        $sheet6->getStyle('T2:W2')->applyFromArray($style);
        // box Selesai Siap Sortir

        // cetak stock
        $sheet6->getStyle("B4:E4")->applyFromArray($style_atas);
        $sheet6->setCellValue('A4', 'Cetak Stock');
        $sheet6->setCellValue('B4', 'Ttl Box');
        $sheet6->setCellValue('C4', 'Pcs');
        $sheet6->setCellValue('D4', 'Gr');
        $sheet6->setCellValue('E4', 'Rp/gr');
        $cost_cabut_selesai = 0;
        foreach ($cetak_stock as $d) {
            $cost_cabut_selesai += $d->ttl_rp + $d->cost_cbt;
        }
        $sheet6->setCellValue('B5', count($cetak_stock));
        $sheet6->setCellValue('C5', array_sum(array_column($cetak_stock, 'pcs_awal')));
        $sheet6->setCellValue('D5', array_sum(array_column($cetak_stock, 'gr_awal')));
        if (array_sum(array_column($cetak_stock, 'gr_awal')) != 0) {
            $sheet6->setCellValue('E5', round($cost_cabut_selesai / array_sum(array_column($cetak_stock, 'gr_awal')), 0));
        } else {
            $sheet6->setCellValue('E5', 0);
        }
        $sheet6->getStyle('B5:E5')->applyFromArray($style);
        // cetak stock

        // cetak Proses
        $sheet6->getStyle("H4:K4")->applyFromArray($style_atas);
        $sheet6->setCellValue('G4', 'Cetak Proses');
        $sheet6->setCellValue('H4', 'Ttl Box');
        $sheet6->setCellValue('I4', 'Pcs');
        $sheet6->setCellValue('J4', 'Gr');
        $sheet6->setCellValue('K4', 'Rp/gr');
        $cost_cetak_proses = 0;
        foreach ($cetak_proses as $d) {
            $cost_cetak_proses += $d->ttl_rp + $d->cost_cbt;
        }
        $sheet6->setCellValue('H5', count($cetak_proses));
        $sheet6->setCellValue('I5', array_sum(array_column($cetak_proses, 'pcs_awal')));
        $sheet6->setCellValue('J5', array_sum(array_column($cetak_proses, 'gr_awal')));
        if (array_sum(array_column($cetak_proses, 'gr_awal')) != 0) {
            $sheet6->setCellValue('K5', round($cost_cabut_selesai / array_sum(array_column($cetak_proses, 'gr_awal')), 0));
        } else {
            $sheet6->setCellValue('K5', 0);
        }
        $sheet6->getStyle('H5:K5')->applyFromArray($style);
        // cetak Proses

        // cetak selesai
        $sheet6->getStyle("N4:Q4")->applyFromArray($style_atas);
        $sheet6->setCellValue('M4', 'Cetak Selesai Siap Sortir');
        $sheet6->setCellValue('N4', 'Ttl Box');
        $sheet6->setCellValue('O4', 'Pcs');
        $sheet6->setCellValue('P4', 'Gr');
        $sheet6->setCellValue('Q4', 'Rp/gr');
        $cost_cetak_selesai = 0;
        foreach ($cetak_selesai as $d) {
            $cost_cetak_selesai += $d->ttl_rp + $d->cost_cbt;
        }
        $sheet6->setCellValue('N5', count($cetak_selesai));
        $sheet6->setCellValue('O5', array_sum(array_column($cetak_selesai, 'pcs_awal')));
        $sheet6->setCellValue('P5', array_sum(array_column($cetak_selesai, 'gr_awal')));
        if (array_sum(array_column($cetak_selesai, 'gr_awal')) != 0) {
            $sheet6->setCellValue('Q5', round($cost_cetak_selesai / array_sum(array_column($cetak_selesai, 'gr_awal')), 0));
        } else {
            $sheet6->setCellValue('Q5', 0);
        }
        $sheet6->getStyle('N5:Q5')->applyFromArray($style);
        // cetak selesai

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
