<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use App\Models\CetakModel;
use App\Models\Grading;
use App\Models\Sortir;
use App\Models\TotalanModel;
use App\Models\TotalannewModel;
use App\Models\gudangcekModel;
use App\Models\IbuSummary;
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
    public function totalan_new(Request $r)
    {
        $data = [
            'title' => 'Data Totalan',
            'bksinta' => TotalanModel::bksinta(),

        ];
        return view('home.gudang.totalan_new', $data);
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


        $sheet1->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'Box Stock');
        $sheet1->setCellValue('B1', 'Pemilik');
        $sheet1->setCellValue('C1', 'Partai');
        $sheet1->setCellValue('D1', 'No Box');
        $sheet1->setCellValue('E1', 'Pcs');
        $sheet1->setCellValue('F1', 'Gr');
        $sheet1->setCellValue('G1', 'Rp/gr');
        $sheet1->setCellValue('H1', 'Total Rp');

        $gudangbk = gudangcekModel::bkstock();


        $kolom = 2;
        foreach ($gudangbk as $d) {
            $sheet1->setCellValue('B' . $kolom, $d->name);
            $sheet1->setCellValue('C' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('D' . $kolom, $d->no_box);
            $sheet1->setCellValue('E' . $kolom, $d->pcs);
            $sheet1->setCellValue('F' . $kolom, $d->gr);
            $sheet1->setCellValue('G' . $kolom, round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $d->gr, 0));
            $sheet1->setCellValue('H' . $kolom, round($d->ttl_rp + $d->cost_cu, 0));
            $kolom++;
        }
        $sheet1->getStyle('A2:H' . $kolom - 1)->applyFromArray($style);

        $sheet1->getStyle("K1:Q1")->applyFromArray($style_atas);
        $sheet1->setCellValue('J1', 'Box sedang proses');
        $sheet1->setCellValue('K1', 'Pemilik');
        $sheet1->setCellValue('L1', 'Partai');
        $sheet1->setCellValue('M1', 'No Box');
        $sheet1->setCellValue('N1', 'Pcs');
        $sheet1->setCellValue('O1', 'Gr');
        $sheet1->setCellValue('P1', 'Rp/gr');
        $sheet1->setCellValue('Q1', 'Total Rp');

        $kolom2 = 2;
        $gudangbkproses = gudangcekModel::bksedang_proses();
        foreach ($gudangbkproses as $d) {
            $sheet1->setCellValue('K' . $kolom2, $d->name);
            $sheet1->setCellValue('L' . $kolom2, $d->nm_partai);
            $sheet1->setCellValue('M' . $kolom2, $d->no_box);
            $sheet1->setCellValue('N' . $kolom2, $d->pcs);
            $sheet1->setCellValue('O' . $kolom2, $d->gr);
            $sheet1->setCellValue('P' . $kolom2, round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $d->gr, 0));
            $sheet1->setCellValue('Q' . $kolom2, round($d->ttl_rp + $d->cost_cu, 0));
            $kolom2++;
        }
        $sheet1->getStyle('K2:Q' . $kolom2 - 1)->applyFromArray($style);

        $sheet1->getStyle("T1:W1")->applyFromArray($style_atas);
        $sheet1->setCellValue('S1', 'Box Selesai siap ctk');
        $sheet1->setCellValue('T1', 'Pemilik');
        $sheet1->setCellValue('U1', 'Partai');
        $sheet1->setCellValue('V1', 'No Box');
        $sheet1->setCellValue('W1', 'Pcs');
        $sheet1->setCellValue('X1', 'Gr');
        $sheet1->setCellValue('Y1', 'Rp/gr');
        $sheet1->setCellValue('Z1', 'Total Rp');

        $bkselesai_siap_ctk = gudangcekModel::bkselesai_siap_ctk();

        $kolom3 = 2;
        foreach ($bkselesai_siap_ctk as $d) {
            $sheet1->setCellValue('T' . $kolom3, $d->name);
            $sheet1->setCellValue('U' . $kolom3, $d->nm_partai);
            $sheet1->setCellValue('V' . $kolom3, $d->no_box);
            $sheet1->setCellValue('W' . $kolom3, $d->pcs);
            $sheet1->setCellValue('X' . $kolom3, $d->gr);
            $sheet1->setCellValue('Y' . $kolom3, round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $d->gr, 0));
            $sheet1->setCellValue('Z' . $kolom3, round($d->ttl_rp, 0));
            $kolom3++;
        }
        $sheet1->getStyle('T2:Z' . $kolom3 - 1)->applyFromArray($style);





        $sheet1->getStyle("AC1:AI1")->applyFromArray($style_atas);
        $sheet1->setCellValue('AB1', 'Box Selesai siap sortir');
        $sheet1->setCellValue('AC1', 'Pemilik');
        $sheet1->setCellValue('AD1', 'Partai');
        $sheet1->setCellValue('AE1', 'No Box');
        $sheet1->setCellValue('AF1', 'Pcs');
        $sheet1->setCellValue('AG1', 'Gr');
        $sheet1->setCellValue('AH1', 'Rp/gr');
        $sheet1->setCellValue('AI1', 'Total Rp');


        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $id_user = auth()->user()->id;

        $bkselesai_siap_str = gudangcekModel::bkselesai_siap_str();

        $kolom4 = 2;
        foreach ($bkselesai_siap_str as $d) {
            $sheet1->setCellValue('AC' . $kolom4, $d->name);
            $sheet1->setCellValue('AD' . $kolom4, $d->nm_partai);
            $sheet1->setCellValue('AE' . $kolom4, $d->no_box);
            $sheet1->setCellValue('AF' . $kolom4, 0);
            $sheet1->setCellValue('AG' . $kolom4, $d->gr);
            $ttl_rp_eo = $d->ttl_rp + $d->ttl_rp_cbt + $d->ttl_rp_eo + $d->cost_op_cbt + $d->cost_cu;
            $sheet1->setCellValue('AH' . $kolom4, round($ttl_rp_eo / $d->gr));
            $sheet1->setCellValue('AI' . $kolom4, round($d->ttl_rp + $d->ttl_rp_cbt + $d->ttl_rp_eo + $d->cost_op_cbt, 0));
            $kolom4++;
        }
        $sheet1->getStyle('AC2:AI' . $kolom4 - 1)->applyFromArray($style);

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
        $sheet2->setCellValue('H1', 'Total Rp');
        $cetak_stock = gudangcekModel::cetak_stok();
        $kolom2 = 2;
        foreach ($cetak_stock as $d) {
            $sheet2->setCellValue('B' . $kolom2, $d->name);
            $sheet2->setCellValue('C' . $kolom2, $d->nm_partai);
            $sheet2->setCellValue('D' . $kolom2, $d->no_box);
            $sheet2->setCellValue('E' . $kolom2, $d->pcs_awal);
            $sheet2->setCellValue('F' . $kolom2, $d->gr_awal);
            $ttl_rp_ctstok = $d->ttl_rp + $d->cost_cbt + $d->cost_op + $d->cost_cu;
            $sheet2->setCellValue('G' . $kolom2, round($ttl_rp_ctstok / $d->gr_awal, 0));
            $sheet2->setCellValue('H' . $kolom2, round($ttl_rp_ctstok, 0));
            $kolom2++;
        }
        $sheet2->getStyle('B2:H' . $kolom2 - 1)->applyFromArray($style);



        $sheet2->getStyle("K1:Q1")->applyFromArray($style_atas);
        $sheet2->setCellValue('J1', 'Cetak sedang Proses');
        $sheet2->setCellValue('K1', 'Pemilik');
        $sheet2->setCellValue('L1', 'Partai');
        $sheet2->setCellValue('M1', 'No Box');
        $sheet2->setCellValue('N1', 'Pcs');
        $sheet2->setCellValue('O1', 'Gr');
        $sheet2->setCellValue('P1', 'Rp/gr');
        $sheet2->setCellValue('Q1', 'Total Rp');

        $cetak_proses = gudangcekModel::cetak_proses();
        $kolom3 = 2;
        foreach ($cetak_proses as $d) {
            $sheet2->setCellValue('K' . $kolom3, $d->name);
            $sheet2->setCellValue('L' . $kolom3, $d->nm_partai);
            $sheet2->setCellValue('M' . $kolom3, $d->no_box);
            $sheet2->setCellValue('N' . $kolom3, $d->pcs_awal);
            $sheet2->setCellValue('O' . $kolom3, $d->gr_awal);
            $ttl_ctk_proses = $d->ttl_rp + $d->cost_cbt + $d->cost_op + $d->cost_cu;
            $sheet2->setCellValue('P' . $kolom3, round($ttl_ctk_proses / $d->gr_awal, 0));
            $sheet2->setCellValue('Q' . $kolom3, round($ttl_ctk_proses, 0));
            $kolom3++;
        }
        $sheet2->getStyle('K2:Q' . $kolom3 - 1)->applyFromArray($style);

        $sheet2->getStyle("T1:Z1")->applyFromArray($style_atas);
        $sheet2->setCellValue('S1', 'Cetak selesai siap sortir');
        $sheet2->setCellValue('T1', 'Pemilik');
        $sheet2->setCellValue('U1', 'Partai');
        $sheet2->setCellValue('V1', 'No Box');
        $sheet2->setCellValue('W1', 'Pcs');
        $sheet2->setCellValue('X1', 'Gr');
        $sheet2->setCellValue('Y1', 'Rp/gr');
        $sheet2->setCellValue('Z1', 'Total Rp');

        $cetak_selesai = gudangcekModel::cetak_selesai();
        $kolom4 = 2;
        foreach ($cetak_selesai as $d) {
            $sheet2->setCellValue('T' . $kolom4, $d->name);
            $sheet2->setCellValue('U' . $kolom4, $d->nm_partai);
            $sheet2->setCellValue('V' . $kolom4, $d->no_box);
            $sheet2->setCellValue('W' . $kolom4, $d->pcs);
            $sheet2->setCellValue('X' . $kolom4, $d->gr);
            $ttl_rpctk_selesai = $d->ttl_rp + $d->cost_op + $d->cost_cu;
            $sheet2->setCellValue('Y' . $kolom4, round($ttl_rpctk_selesai / $d->gr, 0));
            $sheet2->setCellValue('Z' . $kolom4, round($ttl_rpctk_selesai, 0));
            $kolom4++;
        }
        $sheet2->getStyle('T2:Z' . $kolom4 - 1)->applyFromArray($style);

        // Batas kedua

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet(2);
        $sheet3->setTitle('Gudang Sortir');

        $sheet3->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'Sortir stock');
        $sheet3->setCellValue('B1', 'Pemilik');
        $sheet3->setCellValue('C1', 'Partai');
        $sheet3->setCellValue('D1', 'No Box');
        $sheet3->setCellValue('E1', 'Pcs');
        $sheet3->setCellValue('F1', 'Gr');
        $sheet3->setCellValue('G1', 'Rp/gr');
        $sheet3->setCellValue('H1', 'Total Rp');

        $sortir_stock = gudangcekModel::stock_sortir();
        $kolom2 = 2;
        foreach ($sortir_stock as $d) {
            $sheet3->setCellValue('B' . $kolom2, $d->name);
            $sheet3->setCellValue('C' . $kolom2, $d->nm_partai);
            $sheet3->setCellValue('D' . $kolom2, $d->no_box);
            $sheet3->setCellValue('E' . $kolom2, $d->pcs);
            $sheet3->setCellValue('F' . $kolom2, $d->gr);
            $ttl_sortir_stock = $d->ttl_rp + $d->cost_op + $d->cost_cu;
            $sheet3->setCellValue('G' . $kolom2, round($ttl_sortir_stock / $d->gr, 0));
            $sheet3->setCellValue('H' . $kolom2, round($ttl_sortir_stock, 0));
            $kolom2++;
        }
        $sheet3->getStyle('B2:H' . $kolom2 - 1)->applyFromArray($style);


        $sheet3->getStyle("K1:Q1")->applyFromArray($style_atas);
        $sheet3->setCellValue('J1', 'Sortir sedang proses');
        $sheet3->setCellValue('K1', 'Pemilik');
        $sheet3->setCellValue('L1', 'Partai');
        $sheet3->setCellValue('M1', 'No Box');
        $sheet3->setCellValue('N1', 'Pcs');
        $sheet3->setCellValue('O1', 'Gr');
        $sheet3->setCellValue('P1', 'Rp/gr');
        $sheet3->setCellValue('Q1', 'Total Rp');

        $sortir_proses = gudangcekModel::sortir_proses();
        $kolom3 = 2;
        foreach ($sortir_proses as $d) {
            $sheet3->setCellValue('K' . $kolom3, $d->name);
            $sheet3->setCellValue('L' . $kolom3, $d->nm_partai);
            $sheet3->setCellValue('M' . $kolom3, $d->no_box);
            $sheet3->setCellValue('N' . $kolom3, $d->pcs_awal);
            $sheet3->setCellValue('O' . $kolom3, $d->gr_awal);
            $ttl_rp_sortir_proses = $d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo + $d->cost_op_cbt + $d->cost_op_ctk + $d->cost_op_eo + $d->cost_cu;
            $sheet3->setCellValue('P' . $kolom3, round($ttl_rp_sortir_proses / $d->gr_awal, 0));
            $sheet3->setCellValue('Q' . $kolom3, round($ttl_rp_sortir_proses, 0));
            $kolom3++;
        }
        $sheet3->getStyle('K2:Q' . $kolom3 - 1)->applyFromArray($style);

        $sheet3->getStyle("S1:W1")->applyFromArray($style_atas);
        $sheet3->setCellValue('S1', 'Sortir selesai siap grading');
        $sheet3->setCellValue('T1', 'Pemilik');
        $sheet3->setCellValue('U1', 'Partai');
        $sheet3->setCellValue('V1', 'No Box');
        $sheet3->setCellValue('W1', 'Pcs');
        $sheet3->setCellValue('X1', 'Gr');
        $sheet3->setCellValue('Y1', 'Rp/gr');
        $sheet3->setCellValue('Z1', 'Total Rp');

        $sortir_selesai = gudangcekModel::sortir_selesai();
        $kolom4 = 2;
        foreach ($sortir_selesai as $d) {
            $sheet3->setCellValue('T' . $kolom4, $d->name);
            $sheet3->setCellValue('U' . $kolom4, $d->nm_partai);
            $sheet3->setCellValue('V' . $kolom4, $d->no_box);
            $sheet3->setCellValue('W' . $kolom4, $d->pcs);
            $sheet3->setCellValue('X' . $kolom4, $d->gr);
            $ttl_rp_str_selesai = $d->ttl_rp + $d->cost_op + $d->cost_cu;
            $sheet3->setCellValue('Y' . $kolom4, round($ttl_rp_str_selesai / $d->gr, 0));
            $sheet3->setCellValue('Z' . $kolom4, round($ttl_rp_str_selesai, 0));
            $kolom4++;
        }
        $sheet3->getStyle('T2:Z' . $kolom4 - 1)->applyFromArray($style);

        // batas ke empat
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet4 = $spreadsheet->getActiveSheet(3);
        $sheet4->setTitle('Grading');

        $sheet4->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'Grading stock');
        $sheet4->setCellValue('B1', 'Pemilik');
        $sheet4->setCellValue('C1', 'Partai');
        $sheet4->setCellValue('D1', 'No Box');
        $sheet4->setCellValue('E1', 'Pcs');
        $sheet4->setCellValue('F1', 'Gr');
        $sheet4->setCellValue('G1', 'Rp/gr');
        $sheet4->setCellValue('H1', 'Total Rp');

        $grading_stock = gudangcekModel::grading_stock();
        $kolom2 = 2;
        foreach ($grading_stock as $d) {
            $sheet4->setCellValue('B' . $kolom2, $d->name);
            $sheet4->setCellValue('C' . $kolom2, $d->nm_partai);
            $sheet4->setCellValue('D' . $kolom2, $d->no_box);
            $sheet4->setCellValue('E' . $kolom2, $d->pcs);
            $sheet4->setCellValue('F' . $kolom2, $d->gr);
            $ttlrp_grading = $d->ttl_rp + $d->cost_op + $d->cost_cu;
            $sheet4->setCellValue('G' . $kolom2, round($ttlrp_grading / $d->gr, 0));
            $sheet4->setCellValue('H' . $kolom2, round($ttlrp_grading, 0));
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
        $sheet6->getStyle("A1:K1")->applyFromArray($style_atas);
        $sheet6->setCellValue('A1', 'No');
        $sheet6->setCellValue('B1', 'Nama Partai');
        $sheet6->setCellValue('C1', 'Pcs');
        $sheet6->setCellValue('D1', 'Gr');
        $sheet6->setCellValue('E1', 'Rp/gr');
        $sheet6->setCellValue('F1', 'Total Rp');
        $sheet6->setCellValue('G1', 'Pcs');
        $sheet6->setCellValue('H1', 'Gr');
        $sheet6->setCellValue('I1', 'Cost Bk');
        $sheet6->setCellValue('J1', 'Cek');
        $sheet6->setCellValue('K1', 'Sst');

        $sheet6->setCellValue('G2', 'SUMIF($P:$P,B2,$R:$R)');
        $sheet6->setCellValue('H2', 'SUMIF($P:$P,B2,$S:$S)');
        $sheet6->setCellValue('I2', 'SUMIF($P:$P,B2,$U:$U)');
        $sheet6->setCellValue('J2', 'I2-F2+C2-G2');
        $sheet6->setCellValue('K2', '1-(H2/D2)');


        $bk_sinta = TotalanModel::bksinta();

        $ttl_bk = 0;
        foreach ($bk_sinta as $no => $b) {
            $ttl_bk += $b->ttl_rp;
        }

        $op = DB::selectOne("SELECT sum(a.ttl_gaji) as ttl_gaji, c.rp_oprasional, b.nm_bulan
        FROM tb_gaji_penutup as a 
        left join bulan as b on b.bulan = a.bulan_dibayar
        left join oprasional as c on c.bulan = a.bulan_dibayar and a.tahun_dibayar = c.tahun
        ");

        $sheet6->setCellValue('M1', 'Ttl Rp');
        $sheet6->setCellValue('M2', $ttl_bk + $op->ttl_gaji + $op->rp_oprasional);;

        $sheet6->setCellValue('N1', 'Ttl Rp');
        $sheet6->setCellValue('N2', '=sum(AB:AB)');



        $kolom5 = 2;
        foreach ($bk_sinta as $no => $b) {
            $sheet6->setCellValue('A' . $kolom5, $no + 1);
            $sheet6->setCellValue('B' . $kolom5, $b->nm_partai);
            $sheet6->setCellValue('C' . $kolom5, $b->pcs);
            $sheet6->setCellValue('D' . $kolom5, $b->gr);
            $sheet6->setCellValue('E' . $kolom5, round($b->ttl_rp / $b->gr, 0));
            $sheet6->setCellValue('F' . $kolom5, $b->ttl_rp);

            $kolom5++;
        }
        $sheet6->getStyle('A2:K' . $kolom5 - 1)->applyFromArray($style);


        $sheet6->getStyle("P1:AB1")->applyFromArray($style_atas);
        $sheet6->setCellValue('P1', 'Nama Partai');
        $sheet6->setCellValue('Q1', 'Lokasi');
        $sheet6->setCellValue('R1', 'Pcs');
        $sheet6->setCellValue('S1', 'Gr');
        $sheet6->setCellValue('T1', 'Rp/gr');
        $sheet6->setCellValue('U1', 'Cost BK');
        $sheet6->setCellValue('V1', 'Cost Cabut');
        $sheet6->setCellValue('W1', 'Cost Eo');
        $sheet6->setCellValue('X1', 'Cost Cetak');
        $sheet6->setCellValue('Y1', 'Cost Sortir');
        $sheet6->setCellValue('Z1', 'Cost Cu');
        $sheet6->setCellValue('AA1', 'Cost Oprasional');
        $sheet6->setCellValue('AB1', 'Total Rp');

        $kolom6 = 2;
        foreach ($bk_sinta as $no => $b) {
            $bk_stock = TotalanModel::bkstock($b->nm_partai);
            $bk_proses = TotalanModel::bksedang_proses($b->nm_partai);
            $bk_selesai_siap_ctk = TotalannewModel::bkselesai_siap_ctk($b->nm_partai);
            $bk_selesai_siap_str = TotalanModel::bkselesai_siap_str($b->nm_partai);
            $cetak_stok = TotalanModel::cetak_stok($b->nm_partai);
            $cetak_proses2 = TotalanModel::cetak_proses($b->nm_partai);
            $cetak_selesai2 = TotalannewModel::cetak_selesai($b->nm_partai);
            $stock_sortir = TotalanModel::stock_sortir($b->nm_partai);
            $sortir_proses2 = TotalanModel::sortir_proses($b->nm_partai);
            $sortir_selesai2 = TotalanModel::sortir_selesai($b->nm_partai);
            $grading_stock2 = TotalannewModel::grading_stock($b->nm_partai);
            $box_belum_kirim = TotalanModel::box_belum_kirim($b->nm_partai);

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Box Stock');
            $sheet6->setCellValue('R' . $kolom6, round($bk_stock->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($bk_stock->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($bk_stock->gr) ? 0 : round($bk_stock->ttl_rp / $bk_stock->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($bk_stock->ttl_rp ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, 0);
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, round($bk_stock->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, 0);
            $sheet6->setCellValue('AB' . $kolom6, round($bk_stock->ttl_rp ?? 0, 0) + round($bk_stock->cost_cu ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Box sedang proses');
            $sheet6->setCellValue('R' . $kolom6, round($bk_proses->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($bk_proses->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($bk_proses->gr) ? 0 : round($bk_proses->ttl_rp / $bk_proses->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($bk_proses->ttl_rp ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, 0);
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, round($bk_proses->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, 0);
            $sheet6->setCellValue('AB' . $kolom6, round($bk_proses->ttl_rp ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Box selesai siap cetak');
            $sheet6->setCellValue('R' . $kolom6, round($bk_selesai_siap_ctk->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($bk_selesai_siap_ctk->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($bk_selesai_siap_ctk->gr) ? 0 : round($bk_selesai_siap_ctk->ttl_rp / $bk_selesai_siap_ctk->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($bk_selesai_siap_ctk->cost_bk ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, round($bk_selesai_siap_ctk->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, round($bk_selesai_siap_ctk->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($bk_selesai_siap_ctk->cost_op ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($bk_selesai_siap_ctk->ttl_rp ?? 0, 0) + round($bk_selesai_siap_ctk->cost_op_cbt ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Box selesai siap sortir');
            $sheet6->setCellValue('R' . $kolom6, round($bk_selesai_siap_str->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($bk_selesai_siap_str->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($bk_selesai_siap_str->gr) ? 0 : round($bk_selesai_siap_str->ttl_rp / $bk_selesai_siap_str->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($bk_selesai_siap_str->cost_bk ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, round($bk_selesai_siap_str->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($bk_selesai_siap_str->cost_eo ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, round($bk_selesai_siap_str->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($bk_selesai_siap_str->cost_op ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($bk_selesai_siap_str->ttl_rp ?? 0, 0) + round($bk_selesai_siap_str->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Cetak Stok');
            $sheet6->setCellValue('R' . $kolom6, round($cetak_stok->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($cetak_stok->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($cetak_stok->gr) ? 0 : round($cetak_stok->ttl_rp / $cetak_stok->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($cetak_stok->cost_bk ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, round($cetak_stok->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, round($cetak_stok->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($cetak_stok->cost_op ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($cetak_stok->ttl_rp ?? 0, 0) + round($cetak_stok->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Cetak sedang proses');
            $sheet6->setCellValue('R' . $kolom6, round($cetak_proses2->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($cetak_proses2->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($cetak_proses2->gr) ? 0 : round($cetak_proses2->ttl_rp / $cetak_proses2->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($cetak_proses2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, round($cetak_proses2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, round($cetak_proses2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($cetak_proses2->cost_op ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($cetak_proses2->ttl_rp ?? 0, 0) + round($cetak_proses2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Cetak selesai siap sortir');
            $sheet6->setCellValue('R' . $kolom6, round($cetak_selesai2->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($cetak_selesai2->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($cetak_selesai2->gr) ? 0 : round($cetak_selesai2->ttl_rp / $cetak_selesai2->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($cetak_selesai2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, round($cetak_selesai2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, round($cetak_selesai2->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, round($cetak_selesai2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($cetak_selesai2->cost_op ?? 0, 0));

            $sheet6->setCellValue('AB' . $kolom6, round($cetak_selesai2->ttl_rp ?? 0, 0) + round($cetak_selesai2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Sortir Stok');
            $sheet6->setCellValue('R' . $kolom6, round($stock_sortir->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($stock_sortir->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($stock_sortir->gr) ? 0 : round($stock_sortir->ttl_rp / $stock_sortir->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($stock_sortir->cost_bk ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, round($stock_sortir->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($stock_sortir->cost_eo ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, round($stock_sortir->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, round($stock_sortir->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($stock_sortir->cost_op ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($stock_sortir->ttl_rp ?? 0, 0) +  round($stock_sortir->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Sortir sedang proses');
            $sheet6->setCellValue('R' . $kolom6, round($sortir_proses2->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($sortir_proses2->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($sortir_proses2->gr) ? 0 : round($sortir_proses2->ttl_rp / $sortir_proses2->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($sortir_proses2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, round($sortir_proses2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($sortir_proses2->cost_eo ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, round($sortir_proses2->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, round($sortir_proses2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($sortir_proses2->cost_op ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($sortir_proses2->ttl_rp ?? 0, 0) + round($sortir_proses2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Sortir selesai siap grading');
            $sheet6->setCellValue('R' . $kolom6, round($sortir_selesai2->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($sortir_selesai2->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($sortir_selesai2->gr) ? 0 : round($sortir_selesai2->ttl_rp / $sortir_selesai2->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($sortir_selesai2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, round($sortir_selesai2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($sortir_selesai2->cost_eo ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, round($sortir_selesai2->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, round($sortir_selesai2->cost_str ?? 0, 0));
            $sheet6->setCellValue('Z' . $kolom6, round($sortir_selesai2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($sortir_selesai2->cost_op ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($sortir_selesai2->ttl_rp ?? 0, 0) + round($sortir_selesai2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Grading Stock');
            $sheet6->setCellValue('R' . $kolom6, round($grading_stock2->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($grading_stock2->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($grading_stock2->gr) ? 0 : round($grading_stock2->ttl_rp / $grading_stock2->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, round($grading_stock2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('V' . $kolom6, round($grading_stock2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($grading_stock2->cost_eo ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, round($grading_stock2->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, round($grading_stock2->cost_str ?? 0, 0));
            $sheet6->setCellValue('Z' . $kolom6, round($grading_stock2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($grading_stock2->cost_op ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($grading_stock2->ttl_rp ?? 0, 0) + round($grading_stock2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Box belum kirim');
            $sheet6->setCellValue('R' . $kolom6, round($box_belum_kirim->pcs ?? 0, 0));
            $sheet6->setCellValue('S' . $kolom6, round($box_belum_kirim->gr ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, empty($box_belum_kirim->gr) ? 0 : round($box_belum_kirim->ttl_rp / $box_belum_kirim->gr, 0));
            $sheet6->setCellValue('U' . $kolom6, 0);
            $sheet6->setCellValue('V' . $kolom6, 0);
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, 0);
            $sheet6->setCellValue('AB' . $kolom6, round($box_belum_kirim->ttl_rp ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('P' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('Q' . $kolom6, 'Box selesai kirim');
            $sheet6->setCellValue('R' . $kolom6, 0);
            $sheet6->setCellValue('S' . $kolom6, 0);
            $sheet6->setCellValue('T' . $kolom6, 0);
            $sheet6->setCellValue('U' . $kolom6, 0);
            $sheet6->setCellValue('V' . $kolom6, 0);
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, 0);
            $sheet6->setCellValue('AB' . $kolom6, 0);

            $kolom6++;
        }
        $sheet6->getStyle('P2:AB' . $kolom6 - 1)->applyFromArray($style);

        // box stock

        // batas ke lima
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(6);
        $sheet7 = $spreadsheet->getActiveSheet(6);
        $sheet7->setTitle('cost');

        $oprasional = DB::select("SELECT sum(a.ttl_gaji) as ttl_gaji, c.rp_oprasional, b.nm_bulan
        FROM tb_gaji_penutup as a 
        left join bulan as b on b.bulan = a.bulan_dibayar
        left join oprasional as c on c.bulan = a.bulan_dibayar and a.tahun_dibayar = c.tahun
        group by a.bulan_dibayar , a.tahun_dibayar;");


        $sheet7->getStyle("A1:B1")->applyFromArray($style_atas);
        $sheet7->setCellValue('A1', 'Bulan');
        $sheet7->setCellValue('B1', 'Rp Oprasional');

        $kolomcost = 2;
        foreach ($oprasional as $o) {
            $sheet7->setCellValue('A' . $kolomcost, $o->nm_bulan);
            $sheet7->setCellValue('B' . $kolomcost, $o->rp_oprasional + $o->ttl_gaji);
            $kolomcost++;
        }
        $sheet7->getStyle('A2:B' . $kolomcost - 1)->applyFromArray($style);




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

    function export2(gudangcekModel $model)
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

        $sheet1->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'Box Stock Awal');
        $sheet1->setCellValue('B1', 'Pemilik');
        $sheet1->setCellValue('C1', 'Partai');
        $sheet1->setCellValue('D1', 'No Box');
        $sheet1->setCellValue('E1', 'Pcs');
        $sheet1->setCellValue('F1', 'Gr');
        $sheet1->setCellValue('G1', 'Rp/gr');
        $sheet1->setCellValue('H1', 'Total Rp');

        $gudangbk = $model::bkstockawal();


        $kolom = 2;
        foreach ($gudangbk as $d) {
            $sheet1->setCellValue('B' . $kolom, $d->name);
            $sheet1->setCellValue('C' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('D' . $kolom, $d->no_box);
            $sheet1->setCellValue('E' . $kolom, $d->pcs);
            $sheet1->setCellValue('F' . $kolom, $d->gr);
            $sheet1->setCellValue('G' . $kolom, round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $d->gr, 0));
            $sheet1->setCellValue('H' . $kolom, round($d->ttl_rp, 0));
            $kolom++;
        }

        $stock_cbt_awal = $this->getSuntikan(11);
        $sheet1->setCellValue('B' . $kolom, 'suntik');
        $sheet1->setCellValue('C' . $kolom, 'partai suntik');
        $sheet1->setCellValue('D' . $kolom, '-');
        $sheet1->setCellValue('E' . $kolom, $stock_cbt_awal->pcs);
        $sheet1->setCellValue('F' . $kolom, $stock_cbt_awal->gr);
        $ttl_rp_ctstok = $stock_cbt_awal->ttl_rp;
        $sheet1->setCellValue('G' . $kolom, round($ttl_rp_ctstok / $stock_cbt_awal->gr, 0));
        $sheet1->setCellValue('H' . $kolom, round($ttl_rp_ctstok, 0));
        $sheet1->getStyle('B2:H' . $kolom - 1)->applyFromArray($style);

        $sheet1->getStyle("K1:Q1")->applyFromArray($style_atas);
        $sheet1->setCellValue('J1', 'Box sedang proses');
        $sheet1->setCellValue('K1', 'Pemilik');
        $sheet1->setCellValue('L1', 'Partai');
        $sheet1->setCellValue('M1', 'No Box');
        $sheet1->setCellValue('N1', 'Pcs');
        $sheet1->setCellValue('O1', 'Gr');
        $sheet1->setCellValue('P1', 'Rp/gr');
        $sheet1->setCellValue('Q1', 'Total Rp');

        $kolom2 = 2;
        $gudangbkproses = $model::bksedang_proses();
        foreach ($gudangbkproses as $d) {
            $sheet1->setCellValue('K' . $kolom2, $d->name);
            $sheet1->setCellValue('L' . $kolom2, $d->nm_partai);
            $sheet1->setCellValue('M' . $kolom2, $d->no_box);
            $sheet1->setCellValue('N' . $kolom2, $d->pcs);
            $sheet1->setCellValue('O' . $kolom2, $d->gr);
            $sheet1->setCellValue('P' . $kolom2, round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $d->gr, 0));
            $sheet1->setCellValue('Q' . $kolom2, round($d->ttl_rp, 0));
            $kolom2++;
        }
        $sheet1->getStyle('K2:Q' . $kolom2 - 1)->applyFromArray($style);

        $sheet1->getStyle("T1:W1")->applyFromArray($style_atas);
        $sheet1->setCellValue('S1', 'Box Selesai siap ctk belum serah');
        $sheet1->setCellValue('T1', 'Pemilik');
        $sheet1->setCellValue('U1', 'Partai');
        $sheet1->setCellValue('V1', 'No Box');
        $sheet1->setCellValue('W1', 'Pcs');
        $sheet1->setCellValue('X1', 'Gr');
        $sheet1->setCellValue('Y1', 'Rp/gr');
        $sheet1->setCellValue('Z1', 'Total Rp');

        $bkselesai_siap_ctk = $model::bkselesai_siap_ctk();

        $kolom3 = 2;
        foreach ($bkselesai_siap_ctk as $d) {
            $sheet1->setCellValue('T' . $kolom3, $d->name);
            $sheet1->setCellValue('U' . $kolom3, $d->nm_partai);
            $sheet1->setCellValue('V' . $kolom3, $d->no_box);
            $sheet1->setCellValue('W' . $kolom3, $d->pcs);
            $sheet1->setCellValue('X' . $kolom3, $d->gr);
            $sheet1->setCellValue('Y' . $kolom3, round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $d->gr, 0));
            $sheet1->setCellValue('Z' . $kolom3, round($d->ttl_rp, 0));
            $kolom3++;
        }
        $sheet1->getStyle('T2:Z' . $kolom3 - 1)->applyFromArray($style);

        $sheet1->getStyle("AC1:AI1")->applyFromArray($style_atas);
        $sheet1->setCellValue('AB1', 'Box Selesai siap ctk diserahkan');
        $sheet1->setCellValue('AC1', 'Pemilik');
        $sheet1->setCellValue('AD1', 'Partai');
        $sheet1->setCellValue('AE1', 'No Box');
        $sheet1->setCellValue('AF1', 'Pcs');
        $sheet1->setCellValue('AG1', 'Gr');
        $sheet1->setCellValue('AH1', 'Rp/gr');
        $sheet1->setCellValue('AI1', 'Total Rp');

        $bkselesai_siap_ctk_diserahkan = $model::bkselesai_siap_ctk_diserahkan();

        $kolom3 = 2;
        foreach ($bkselesai_siap_ctk_diserahkan as $d) {
            $sheet1->setCellValue('AC' . $kolom3, $d->name);
            $sheet1->setCellValue('AD' . $kolom3, $d->nm_partai);
            $sheet1->setCellValue('AE' . $kolom3, $d->no_box);
            $sheet1->setCellValue('AF' . $kolom3, $d->pcs);
            $sheet1->setCellValue('AG' . $kolom3, $d->gr);
            $sheet1->setCellValue('AH' . $kolom3, round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $d->gr, 0));
            $sheet1->setCellValue('AI' . $kolom3, round($d->ttl_rp, 0));
            $kolom3++;
        }
        $stock_siap_cetak_diserahkan = $this->getSuntikan(14);
        $sheet1->setCellValue('AC' . $kolom3, 'suntik');
        $sheet1->setCellValue('AD' . $kolom3, 'partai suntik');
        $sheet1->setCellValue('AE' . $kolom3, '-');
        $sheet1->setCellValue('AF' . $kolom3, $stock_siap_cetak_diserahkan->pcs);
        $sheet1->setCellValue('AG' . $kolom3, $stock_siap_cetak_diserahkan->gr);
        $sheet1->setCellValue('AH' . $kolom3, round($stock_siap_cetak_diserahkan->ttl_rp  / $stock_siap_cetak_diserahkan->gr, 0));
        $sheet1->setCellValue('AI' . $kolom3, round($stock_siap_cetak_diserahkan->ttl_rp, 0));

        $sheet1->getStyle('AC2:AI' . $kolom3)->applyFromArray($style);


        $sheet1->getStyle("AL1:AR1")->applyFromArray($style_atas);
        $sheet1->setCellValue('AK1', 'Box Selesai siap sortir belum serah');
        $sheet1->setCellValue('AL1', 'Pemilik');
        $sheet1->setCellValue('AM1', 'Partai');
        $sheet1->setCellValue('AN1', 'No Box');
        $sheet1->setCellValue('AO1', 'Pcs');
        $sheet1->setCellValue('AP1', 'Gr');
        $sheet1->setCellValue('AQ1', 'Rp/gr');
        $sheet1->setCellValue('AR1', 'Total Rp');

        $bkselesai_siap_str = $model::bkselesai_siap_str();

        $kolom4 = 2;
        foreach ($bkselesai_siap_str as $d) {
            $sheet1->setCellValue('AL' . $kolom4, $d->name);
            $sheet1->setCellValue('AM' . $kolom4, $d->nm_partai);
            $sheet1->setCellValue('AN' . $kolom4, $d->no_box);
            $sheet1->setCellValue('AO' . $kolom4, 0);
            $sheet1->setCellValue('AP' . $kolom4, $d->gr);
            $ttl_rp_eo = $d->ttl_rp + $d->ttl_rp_cbt + $d->ttl_rp_eo + $d->cost_op_cbt + $d->cost_cu;
            $sheet1->setCellValue('AQ' . $kolom4, round($ttl_rp_eo / $d->gr));
            $sheet1->setCellValue('AR' . $kolom4, round($d->ttl_rp, 0));
            $kolom4++;
        }
        $sheet1->getStyle('AL2:AR' . $kolom4 - 1)->applyFromArray($style);

        $sheet1->getStyle("AU1:BA1")->applyFromArray($style_atas);
        $sheet1->setCellValue('AT1', 'Box Selesai siap sortir diserahkan');
        $sheet1->setCellValue('AU1', 'Pemilik');
        $sheet1->setCellValue('AV1', 'Partai');
        $sheet1->setCellValue('AW1', 'No Box');
        $sheet1->setCellValue('AX1', 'Pcs');
        $sheet1->setCellValue('AY1', 'Gr');
        $sheet1->setCellValue('AZ1', 'Rp/gr');
        $sheet1->setCellValue('BA1', 'Total Rp');

        $bkselesai_siap_str_diserahkan = $model::bkselesai_siap_str_diserahkan();

        $kolom4 = 2;
        foreach ($bkselesai_siap_str_diserahkan as $d) {
            $sheet1->setCellValue('AU' . $kolom4, $d->name);
            $sheet1->setCellValue('AV' . $kolom4, $d->nm_partai);
            $sheet1->setCellValue('AW' . $kolom4, $d->no_box);
            $sheet1->setCellValue('AX' . $kolom4, 0);
            $sheet1->setCellValue('AY' . $kolom4, $d->gr);
            $ttl_rp_eo = $d->ttl_rp + $d->ttl_rp_cbt + $d->ttl_rp_eo + $d->cost_op_cbt + $d->cost_cu;
            $sheet1->setCellValue('AZ' . $kolom4, round($ttl_rp_eo / $d->gr));
            $sheet1->setCellValue('BA' . $kolom4, round($d->ttl_rp, 0));
            $kolom4++;
        }
        $stock_siap_sortir_diserahkan = $this->getSuntikan(16);
        $sheet1->setCellValue('AU' . $kolom4, 'suntik');
        $sheet1->setCellValue('AV' . $kolom4, 'partai disuntik');
        $sheet1->setCellValue('AW' . $kolom4, '-');
        $sheet1->setCellValue('AX' . $kolom4, 0);
        $sheet1->setCellValue('AY' . $kolom4, $stock_siap_sortir_diserahkan->gr);
        $ttl_rp_eo = $stock_siap_sortir_diserahkan->ttl_rp;
        $sheet1->setCellValue('AZ' . $kolom4, round($ttl_rp_eo / $stock_siap_sortir_diserahkan->gr));
        $sheet1->setCellValue('BA' . $kolom4, round($d->ttl_rp, 0));


        $sheet1->getStyle('AU2:BA' . $kolom4)->applyFromArray($style);


        $sheet1->getStyle("BD1:BJ1")->applyFromArray($style_atas);
        $sheet1->setCellValue('BC1', 'Box Sisa Pengawas');
        $sheet1->setCellValue('BD1', 'Pemilik');
        $sheet1->setCellValue('BE1', 'Partai');
        $sheet1->setCellValue('BF1', 'No Box');
        $sheet1->setCellValue('BG1', 'Pcs');
        $sheet1->setCellValue('BH1', 'Gr');
        $sheet1->setCellValue('BI1', 'Rp/gr');
        $sheet1->setCellValue('BJ1', 'Total Rp');

        $gudangbksisa = $model::bkstock();


        $kolom = 2;
        foreach ($gudangbksisa as $d) {
            $sheet1->setCellValue('BD' . $kolom, $d->name);
            $sheet1->setCellValue('BE' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('BF' . $kolom, $d->no_box);
            $sheet1->setCellValue('BG' . $kolom, $d->pcs);
            $sheet1->setCellValue('BH' . $kolom, $d->gr);
            $sheet1->setCellValue('BI' . $kolom, round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $d->gr, 0));
            $sheet1->setCellValue('BJ' . $kolom, round($d->ttl_rp, 0));
            $kolom++;
        }
        $sheet1->getStyle('BD2:BJ' . $kolom - 1)->applyFromArray($style);

        // batas pertama

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet(1);
        $sheet2->setTitle('Gudang Cetak');

        $sheet2->getStyle("B1:G1")->applyFromArray($style_atas);
        $sheet2->setCellValue('A1', 'Cetak Stock Awal');
        $sheet2->setCellValue('B1', 'Pemilik');
        $sheet2->setCellValue('C1', 'Partai');
        $sheet2->setCellValue('D1', 'No Box');
        $sheet2->setCellValue('E1', 'Pcs');
        $sheet2->setCellValue('F1', 'Gr');
        $sheet2->setCellValue('G1', 'Rp/gr');
        $sheet2->setCellValue('H1', 'Total Rp');
        $cetak_stok_awal = $model::cetak_stok_awal();
        $kolom2 = 2;
        foreach ($cetak_stok_awal as $d) {
            $sheet2->setCellValue('B' . $kolom2, $d->name);
            $sheet2->setCellValue('C' . $kolom2, $d->nm_partai);
            $sheet2->setCellValue('D' . $kolom2, $d->no_box);
            $sheet2->setCellValue('E' . $kolom2, $d->pcs_awal);
            $sheet2->setCellValue('F' . $kolom2, $d->gr_awal);
            $ttl_rp_ctstok = $d->ttl_rp;
            $sheet2->setCellValue('G' . $kolom2, round($ttl_rp_ctstok / $d->gr_awal, 0));
            $sheet2->setCellValue('H' . $kolom2, round($ttl_rp_ctstok, 0));
            $kolom2++;
        }
        $stock_cetak_awal = $this->getSuntikan(22);
        $sheet2->setCellValue('B' . $kolom2, 'suntik');
        $sheet2->setCellValue('C' . $kolom2, 'partai suntik');
        $sheet2->setCellValue('D' . $kolom2, '-');
        $sheet2->setCellValue('E' . $kolom2, $stock_cetak_awal->pcs);
        $sheet2->setCellValue('F' . $kolom2, $stock_cetak_awal->gr);
        $ttl_rp_ctstok = $stock_cetak_awal->ttl_rp;
        $sheet2->setCellValue('G' . $kolom2, round($ttl_rp_ctstok / $stock_cetak_awal->gr, 0));
        $sheet2->setCellValue('H' . $kolom2, round($ttl_rp_ctstok, 0));

        $sheet2->getStyle('B2:H' . $kolom2)->applyFromArray($style);
        $sheet2->getStyle("K1:Q1")->applyFromArray($style_atas);
        $sheet2->setCellValue('J1', 'Cetak sedang Proses');
        $sheet2->setCellValue('K1', 'Pemilik');
        $sheet2->setCellValue('L1', 'Partai');
        $sheet2->setCellValue('M1', 'No Box');
        $sheet2->setCellValue('N1', 'Pcs');
        $sheet2->setCellValue('O1', 'Gr');
        $sheet2->setCellValue('P1', 'Rp/gr');
        $sheet2->setCellValue('Q1', 'Total Rp');

        $cetak_proses = $model::cetak_proses();
        $kolom3 = 2;
        foreach ($cetak_proses as $d) {
            $sheet2->setCellValue('K' . $kolom3, $d->name);
            $sheet2->setCellValue('L' . $kolom3, $d->nm_partai);
            $sheet2->setCellValue('M' . $kolom3, $d->no_box);
            $sheet2->setCellValue('N' . $kolom3, $d->pcs_awal);
            $sheet2->setCellValue('O' . $kolom3, $d->gr_awal);
            $ttl_ctk_proses = $d->ttl_rp;
            $sheet2->setCellValue('P' . $kolom3, round($ttl_ctk_proses / $d->gr_awal, 0));
            $sheet2->setCellValue('Q' . $kolom3, round($ttl_ctk_proses, 0));
            $kolom3++;
        }
        $sheet2->getStyle('K2:Q' . $kolom3 - 1)->applyFromArray($style);

        $sheet2->getStyle("T1:Z1")->applyFromArray($style_atas);
        $sheet2->setCellValue('S1', 'Cetak selesai siap sortir belum serah');
        $sheet2->setCellValue('T1', 'Pemilik');
        $sheet2->setCellValue('U1', 'Partai');
        $sheet2->setCellValue('V1', 'No Box');
        $sheet2->setCellValue('W1', 'Pcs');
        $sheet2->setCellValue('X1', 'Gr');
        $sheet2->setCellValue('Y1', 'Rp/gr');
        $sheet2->setCellValue('Z1', 'Total Rp');

        $cetak_selesai = $model::cetak_selesai();
        $kolom4 = 2;
        foreach ($cetak_selesai as $d) {
            $sheet2->setCellValue('T' . $kolom4, $d->name);
            $sheet2->setCellValue('U' . $kolom4, $d->nm_partai);
            $sheet2->setCellValue('V' . $kolom4, $d->no_box);
            $sheet2->setCellValue('W' . $kolom4, $d->pcs);
            $sheet2->setCellValue('X' . $kolom4, $d->gr);
            $ttl_rpctk_selesai = $d->ttl_rp;
            $sheet2->setCellValue('Y' . $kolom4, round($ttl_rpctk_selesai / $d->gr, 0));
            $sheet2->setCellValue('Z' . $kolom4, round($ttl_rpctk_selesai, 0));
            $kolom4++;
        }
        $sheet2->getStyle('T2:Z' . $kolom4 - 1)->applyFromArray($style);


        $sheet2->getStyle("AD1:AJ1")->applyFromArray($style_atas);
        $sheet2->setCellValue('AC1', 'Cetak tidak cetak diserahkan');
        $sheet2->setCellValue('AD1', 'Pemilik');
        $sheet2->setCellValue('AE1', 'Partai');
        $sheet2->setCellValue('AF1', 'No Box');
        $sheet2->setCellValue('AG1', 'Pcs');
        $sheet2->setCellValue('AH1', 'Gr');
        $sheet2->setCellValue('AI1', 'Rp/gr');
        $sheet2->setCellValue('AJ1', 'Total Rp');

        $tdk_cetak_selesai_diserahkan = $model::tdk_cetak_selesai_diserahkan();
        $kolom4 = 2;
        foreach ($tdk_cetak_selesai_diserahkan as $d) {
            $sheet2->setCellValue('AD' . $kolom4, $d->name);
            $sheet2->setCellValue('AE' . $kolom4, $d->nm_partai);
            $sheet2->setCellValue('AF' . $kolom4, $d->no_box);
            $sheet2->setCellValue('AG' . $kolom4, $d->pcs_tdk_ctk);
            $sheet2->setCellValue('AH' . $kolom4, $d->gr_tdk_ctk);
            $ttl_rpctk_selesai = $d->ttl_rp;
            $sheet2->setCellValue('AI' . $kolom4, round($ttl_rpctk_selesai / $d->gr_tdk_ctk, 0));
            $sheet2->setCellValue('AJ' . $kolom4, round($ttl_rpctk_selesai, 0));
            $kolom4++;
        }
        $sheet2->getStyle('AD2:AJ' . $kolom4 - 1)->applyFromArray($style);

        $sheet2->getStyle("AM1:AS1")->applyFromArray($style_atas);
        $sheet2->setCellValue('AL1', 'Cetak selesai siap sortir diserahkan');
        $sheet2->setCellValue('AM1', 'Pemilik');
        $sheet2->setCellValue('AN1', 'Partai');
        $sheet2->setCellValue('AO1', 'No Box');
        $sheet2->setCellValue('AP1', 'Pcs');
        $sheet2->setCellValue('AQ1', 'Gr');
        $sheet2->setCellValue('AR1', 'Rp/gr');
        $sheet2->setCellValue('AS1', 'Total Rp');

        $cetak_selesai_diserahkan = $model::cetak_selesai_diserahkan();
        $kolom4 = 2;
        foreach ($cetak_selesai_diserahkan as $d) {
            $sheet2->setCellValue('AM' . $kolom4, $d->name);
            $sheet2->setCellValue('AN' . $kolom4, $d->nm_partai);
            $sheet2->setCellValue('AO' . $kolom4, $d->no_box);
            $sheet2->setCellValue('AP' . $kolom4, $d->pcs);
            $sheet2->setCellValue('AQ' . $kolom4, $d->gr);
            $ttl_rpctk_selesai = $d->ttl_rp;
            $sheet2->setCellValue('AR' . $kolom4, round($ttl_rpctk_selesai / $d->gr, 0));
            $sheet2->setCellValue('AS' . $kolom4, round($ttl_rpctk_selesai, 0));
            $kolom4++;
        }
        $suntik_ctk_diserahkan = $this->getSuntikan(26);
        $sheet2->setCellValue('AM' . $kolom4, 'suntik');
        $sheet2->setCellValue('AN' . $kolom4, 'partai suntik');
        $sheet2->setCellValue('AO' . $kolom4, '-');
        $sheet2->setCellValue('AP' . $kolom4,  $suntik_ctk_diserahkan->pcs);
        $sheet2->setCellValue('AQ' . $kolom4,  $suntik_ctk_diserahkan->gr);
        $sheet2->setCellValue('AR' . $kolom4, round($suntik_ctk_diserahkan->ttl_rp /  $suntik_ctk_diserahkan->gr, 0));
        $sheet2->setCellValue('AS' . $kolom4, round($suntik_ctk_diserahkan->ttl_rp, 0));
        $sheet2->getStyle('AM2:AS' . $kolom4)->applyFromArray($style);



        $sheet2->getStyle("AV1:BA1")->applyFromArray($style_atas);
        $sheet2->setCellValue('AU1', 'Cetak sisa pengawas');
        $sheet2->setCellValue('AV1', 'Pemilik');
        $sheet2->setCellValue('AW1', 'Partai');
        $sheet2->setCellValue('AX1', 'No Box');
        $sheet2->setCellValue('AY1', 'Pcs');
        $sheet2->setCellValue('AZ1', 'Gr');
        $sheet2->setCellValue('BA1', 'Rp/gr');
        $sheet2->setCellValue('BB1', 'Total Rp');
        $cetak_stock = $model::cetak_stok();
        $kolom2 = 2;
        foreach ($cetak_stock as $d) {
            $sheet2->setCellValue('AV' . $kolom2, $d->name);
            $sheet2->setCellValue('AW' . $kolom2, $d->nm_partai);
            $sheet2->setCellValue('AX' . $kolom2, $d->no_box);
            $sheet2->setCellValue('AY' . $kolom2, $d->pcs_awal);
            $sheet2->setCellValue('AZ' . $kolom2, $d->gr_awal);
            $ttl_rp_ctstok = $d->ttl_rp;
            $sheet2->setCellValue('BA' . $kolom2, round($ttl_rp_ctstok / $d->gr_awal, 0));
            $sheet2->setCellValue('BB' . $kolom2, round($ttl_rp_ctstok, 0));
            $kolom2++;
        }
        $suntik_ctk_sisa = $this->getSuntikan(27);
        $sheet2->setCellValue('AV' . $kolom2, 'suntik');
        $sheet2->setCellValue('AW' . $kolom2, 'partai suntik');
        $sheet2->setCellValue('AX' . $kolom2, '-');
        $sheet2->setCellValue('AY' . $kolom2, $suntik_ctk_sisa->pcs);
        $sheet2->setCellValue('AZ' . $kolom2, $suntik_ctk_sisa->gr);
        $sheet2->setCellValue('BA' . $kolom2, round($suntik_ctk_sisa->ttl_rp / $suntik_ctk_sisa->gr, 0));
        $sheet2->setCellValue('BB' . $kolom2, round($suntik_ctk_sisa->ttl_rp, 0));

        $sheet2->getStyle('AV2:BB' . $kolom2)->applyFromArray($style);

        // Batas kedua

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet(2);
        $sheet3->setTitle('Gudang Sortir ');

        $sheet3->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'Sortir stock awal');
        $sheet3->setCellValue('B1', 'Pemilik');
        $sheet3->setCellValue('C1', 'Partai');
        $sheet3->setCellValue('D1', 'No Box');
        $sheet3->setCellValue('E1', 'Pcs');
        $sheet3->setCellValue('F1', 'Gr');
        $sheet3->setCellValue('G1', 'Rp/gr');
        $sheet3->setCellValue('H1', 'Total Rp');

        $sortir_stock = $model::stock_sortir_awal();
        $kolom2 = 2;
        foreach ($sortir_stock as $d) {
            $sheet3->setCellValue('B' . $kolom2, $d->name);
            $sheet3->setCellValue('C' . $kolom2, $d->nm_partai);
            $sheet3->setCellValue('D' . $kolom2, $d->no_box);
            $sheet3->setCellValue('E' . $kolom2, $d->pcs);
            $sheet3->setCellValue('F' . $kolom2, $d->gr);
            $ttl_sortir_stock = $d->ttl_rp;
            $sheet3->setCellValue('G' . $kolom2, round($ttl_sortir_stock / $d->gr, 0));
            $sheet3->setCellValue('H' . $kolom2, round($ttl_sortir_stock, 0));
            $kolom2++;
        }
        $suntik_str_stock_awal = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_stok_awal'");
        $sheet3->setCellValue('B' . $kolom2, 'suntik');
        $sheet3->setCellValue('C' . $kolom2, 'partai suntik');
        $sheet3->setCellValue('D' . $kolom2, '-');
        $sheet3->setCellValue('E' . $kolom2, $suntik_str_stock_awal->pcs);
        $sheet3->setCellValue('F' . $kolom2, $suntik_str_stock_awal->gr);
        $ttl_sortir_stock = $suntik_str_stock_awal->ttl_rp;
        $sheet3->setCellValue('G' . $kolom2, round($ttl_sortir_stock / $suntik_str_stock_awal->gr, 0));
        $sheet3->setCellValue('H' . $kolom2, round($ttl_sortir_stock, 0));

        $sheet3->getStyle('B2:H' . $kolom2)->applyFromArray($style);


        $sheet3->getStyle("K1:Q1")->applyFromArray($style_atas);
        $sheet3->setCellValue('J1', 'Sortir sedang proses');
        $sheet3->setCellValue('K1', 'Pemilik');
        $sheet3->setCellValue('L1', 'Partai');
        $sheet3->setCellValue('M1', 'No Box');
        $sheet3->setCellValue('N1', 'Pcs');
        $sheet3->setCellValue('O1', 'Gr');
        $sheet3->setCellValue('P1', 'Rp/gr');
        $sheet3->setCellValue('Q1', 'Total Rp');

        $sortir_proses = $model::sortir_proses();
        $kolom3 = 2;
        foreach ($sortir_proses as $d) {
            $sheet3->setCellValue('K' . $kolom3, $d->name);
            $sheet3->setCellValue('L' . $kolom3, $d->nm_partai);
            $sheet3->setCellValue('M' . $kolom3, $d->no_box);
            $sheet3->setCellValue('N' . $kolom3, $d->pcs_awal);
            $sheet3->setCellValue('O' . $kolom3, $d->gr_awal);
            $ttl_rp_sortir_proses = $d->ttl_rp;
            $sheet3->setCellValue('P' . $kolom3, round($ttl_rp_sortir_proses / $d->gr_awal, 0));
            $sheet3->setCellValue('Q' . $kolom3, round($ttl_rp_sortir_proses, 0));
            $kolom3++;
        }
        $sheet3->getStyle('K2:Q' . $kolom3 - 1)->applyFromArray($style);

        $sheet3->getStyle("T1:Z1")->applyFromArray($style_atas);
        $sheet3->setCellValue('S1', 'Sortir selesai siap grading belum serah');
        $sheet3->setCellValue('T1', 'Pemilik');
        $sheet3->setCellValue('U1', 'Partai');
        $sheet3->setCellValue('V1', 'No Box');
        $sheet3->setCellValue('W1', 'Pcs');
        $sheet3->setCellValue('X1', 'Gr');
        $sheet3->setCellValue('Y1', 'Rp/gr');
        $sheet3->setCellValue('Z1', 'Total Rp');

        $sortir_selesai = $model::sortir_selesai();
        $kolom4 = 2;
        foreach ($sortir_selesai as $d) {
            $sheet3->setCellValue('T' . $kolom4, $d->name);
            $sheet3->setCellValue('U' . $kolom4, $d->nm_partai);
            $sheet3->setCellValue('V' . $kolom4, $d->no_box);
            $sheet3->setCellValue('W' . $kolom4, $d->pcs);
            $sheet3->setCellValue('X' . $kolom4, $d->gr);
            $ttl_rp_str_selesai = $d->ttl_rp;
            $sheet3->setCellValue('Y' . $kolom4, round($ttl_rp_str_selesai / $d->gr, 0));
            $sheet3->setCellValue('Z' . $kolom4, round($ttl_rp_str_selesai, 0));
            $kolom4++;
        }
        $sheet3->getStyle('T2:Z' . $kolom4 - 1)->applyFromArray($style);


        $sheet3->getStyle("AC1:AI1")->applyFromArray($style_atas);
        $sheet3->setCellValue('AB1', 'Sortir selesai siap grading diserahkan');
        $sheet3->setCellValue('AC1', 'Pemilik');
        $sheet3->setCellValue('AD1', 'Partai');
        $sheet3->setCellValue('AE1', 'No Box');
        $sheet3->setCellValue('AF1', 'Pcs');
        $sheet3->setCellValue('AG1', 'Gr');
        $sheet3->setCellValue('AH1', 'Rp/gr');
        $sheet3->setCellValue('AI1', 'Total Rp');

        $sortir_selesai_diserahkan = $model::sortir_selesai_diserahkan();
        $kolom4 = 2;
        foreach ($sortir_selesai_diserahkan as $d) {
            $sheet3->setCellValue('AC' . $kolom4, $d->name);
            $sheet3->setCellValue('AD' . $kolom4, $d->nm_partai);
            $sheet3->setCellValue('AE' . $kolom4, $d->no_box);
            $sheet3->setCellValue('AF' . $kolom4, $d->pcs);
            $sheet3->setCellValue('AG' . $kolom4, $d->gr);
            $ttl_rp_str_selesai = $d->ttl_rp;
            $sheet3->setCellValue('AH' . $kolom4, round($ttl_rp_str_selesai / $d->gr, 0));
            $sheet3->setCellValue('AI' . $kolom4, round($ttl_rp_str_selesai, 0));
            $kolom4++;
        }
        $suntik_str_stock_diserhkan = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_selesai_diserahkan' and a.opname = 'T'");
        $sheet3->setCellValue('AC' . $kolom4, 'suntik');
        $sheet3->setCellValue('AD' . $kolom4, 'partai suntik');
        $sheet3->setCellValue('AE' . $kolom4, '-');
        $sheet3->setCellValue('AF' . $kolom4, $suntik_str_stock_diserhkan->pcs);
        $sheet3->setCellValue('AG' . $kolom4, $suntik_str_stock_diserhkan->gr);
        $ttl_rp_str_selesai = $suntik_str_stock_diserhkan->ttl_rp;
        $sheet3->setCellValue('AH' . $kolom4, round($ttl_rp_str_selesai / $suntik_str_stock_diserhkan->gr, 0));
        $sheet3->setCellValue('AI' . $kolom4, round($ttl_rp_str_selesai, 0));

        $sheet3->getStyle('AC2:AI' . $kolom4)->applyFromArray($style);

        $sheet3->getStyle("AL1:AR1")->applyFromArray($style_atas);
        $sheet3->setCellValue('AK1', 'Sortir sisa pengawas');
        $sheet3->setCellValue('AL1', 'Pemilik');
        $sheet3->setCellValue('AM1', 'Partai');
        $sheet3->setCellValue('AN1', 'No Box');
        $sheet3->setCellValue('AO1', 'Pcs');
        $sheet3->setCellValue('AP1', 'Gr');
        $sheet3->setCellValue('AQ1', 'Rp/gr');
        $sheet3->setCellValue('AR1', 'Total Rp');

        $stock_sortir_sisa = $model::stock_sortir();
        $kolom4 = 2;
        foreach ($stock_sortir_sisa as $d) {
            $sheet3->setCellValue('AL' . $kolom4, $d->name);
            $sheet3->setCellValue('AM' . $kolom4, $d->nm_partai);
            $sheet3->setCellValue('AN' . $kolom4, $d->no_box);
            $sheet3->setCellValue('AO' . $kolom4, $d->pcs);
            $sheet3->setCellValue('AP' . $kolom4, $d->gr);
            $ttl_rp_str_selesai = $d->ttl_rp;
            $sheet3->setCellValue('AQ' . $kolom4, round($ttl_rp_str_selesai / $d->gr, 0));
            $sheet3->setCellValue('AR' . $kolom4, round($ttl_rp_str_selesai, 0));
            $kolom4++;
        }
        $sheet3->getStyle('AL2:AR' . $kolom4)->applyFromArray($style);

        // batas ke empat
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet4 = $spreadsheet->getActiveSheet(3);
        $sheet4->setTitle('Grading');

        $sheet4->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet4->setCellValue('A1', 'Grading stock');
        $sheet4->setCellValue('B1', 'Pemilik');
        $sheet4->setCellValue('C1', 'Partai');
        $sheet4->setCellValue('D1', 'No Box');
        $sheet4->setCellValue('E1', 'Pcs');
        $sheet4->setCellValue('F1', 'Gr');
        $sheet4->setCellValue('G1', 'Rp/gr');
        $sheet4->setCellValue('H1', 'Total Rp');

        $grading_stock = $model::grading_stock();
        $kolom2 = 2;
        foreach ($grading_stock as $d) {
            $sheet4->setCellValue('B' . $kolom2, $d->name);
            $sheet4->setCellValue('C' . $kolom2, $d->nm_partai);
            $sheet4->setCellValue('D' . $kolom2, $d->no_box);
            $sheet4->setCellValue('E' . $kolom2, $d->pcs);
            $sheet4->setCellValue('F' . $kolom2, $d->gr);
            $ttlrp_grading = $d->ttl_rp + $d->cost_op + $d->cost_cu;
            $sheet4->setCellValue('G' . $kolom2, round($ttlrp_grading / $d->gr, 0));
            $sheet4->setCellValue('H' . $kolom2, round($d->cost_bk, 0));
            $kolom2++;
        }

        $suntik_grading = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'grading'");
        $sheet4->setCellValue('B' . $kolom2, 'suntik');
        $sheet4->setCellValue('C' . $kolom2, 'partai suntik');
        $sheet4->setCellValue('D' . $kolom2, '-');
        $sheet4->setCellValue('E' . $kolom2, $suntik_grading->pcs);
        $sheet4->setCellValue('F' . $kolom2, $suntik_grading->gr);
        $ttlrp_grading = $suntik_grading->ttl_rp;
        $sheet4->setCellValue('G' . $kolom2, round($ttlrp_grading / $suntik_grading->gr, 0));
        $sheet4->setCellValue('H' . $kolom2, round($ttlrp_grading, 0));

        $sheet4->getStyle('B2:H' . $kolom2)->applyFromArray($style);


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
            $sheet5->setCellValue('O' . $kolom4, round($d->cost_bk, 0));
            $kolom4++;
        }
        $sheet5->getStyle('J2:O' . $kolom4 - 1)->applyFromArray($style);


        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(5);
        $sheet6 = $spreadsheet->getActiveSheet(5);
        $sheet6->setTitle('Totalan');

        // box stock
        $sheet6->getStyle("A1:K1")->applyFromArray($style_atas);
        $sheet6->setCellValue('A1', 'No');
        $sheet6->setCellValue('B1', 'Nama Partai');
        $sheet6->setCellValue('C1', 'Pcs');
        $sheet6->setCellValue('D1', 'Gr');
        $sheet6->setCellValue('E1', 'Rp/gr');
        $sheet6->setCellValue('F1', 'Total Rp');
        $sheet6->setCellValue('G1', 'Pcs');
        $sheet6->setCellValue('H1', 'Gr');
        $sheet6->setCellValue('I1', 'Cost Bk');
        $sheet6->setCellValue('J1', 'Cek');
        $sheet6->setCellValue('K1', 'Sst');

        $sheet6->setCellValue('G2', '=SUMIF($Q:$Q,B2,$S:$S)');
        $sheet6->setCellValue('H2', '=SUMIF($Q:$Q,B2,$T:$T)');
        $sheet6->setCellValue('I2', '=SUMIF($Q:$Q,B2,$V:$V)');
        $sheet6->setCellValue('J2', '=I2-F2+C2-G2');
        $sheet6->setCellValue('K2', '=1-(H2/D2)');


        $bk_sinta = TotalanModel::bksinta();

        $ttl_bk = 0;
        foreach ($bk_sinta as $no => $b) {
            $ttl_bk += $b->ttl_rp;
        }

        $op = DB::selectOne("SELECT sum(a.ttl_gaji) as ttl_gaji, sum(a.dll) as dll, c.rp_oprasional, b.nm_bulan
        FROM tb_gaji_penutup as a 
        left join bulan as b on b.bulan = a.bulan_dibayar
        left join oprasional as c on c.bulan = a.bulan_dibayar and a.tahun_dibayar = c.tahun
        ");

        $sheet6->setCellValue('M2', 'bahan baku');
        $sheet6->setCellValue('N1', 'Ttl Rp');
        $sheet6->setCellValue('N2', $ttl_bk);
        $sheet6->setCellValue('N3', $op->ttl_gaji + $op->rp_oprasional);

        $sheet6->setCellValue('O1', 'Ttl Rp');
        $sheet6->setCellValue('O2', '=sum(AC:AC)');
        $sheet6->setCellValue('O3', $op->dll);
        $sheet6->setCellValue('P3', 'gaji dll');

        $kolom5 = 2;
        foreach ($bk_sinta as $no => $b) {
            $sheet6->setCellValue('A' . $kolom5, $no + 1);
            $sheet6->setCellValue('B' . $kolom5, $b->nm_partai);
            $sheet6->setCellValue('C' . $kolom5, $b->pcs);
            $sheet6->setCellValue('D' . $kolom5, $b->gr);
            $sheet6->setCellValue('E' . $kolom5, round($b->ttl_rp / $b->gr, 0));
            $sheet6->setCellValue('F' . $kolom5, $b->ttl_rp);

            $kolom5++;
        }
        $sheet6->getStyle('A2:K' . $kolom5 - 1)->applyFromArray($style);


        $sheet6->getStyle("Q1:AC1")->applyFromArray($style_atas);
        $sheet6->setCellValue('Q1', 'Nama Partai');
        $sheet6->setCellValue('R1', 'Lokasi');
        $sheet6->setCellValue('S1', 'Pcs');
        $sheet6->setCellValue('T1', 'Gr');
        $sheet6->setCellValue('U1', 'Rp/gr');
        $sheet6->setCellValue('V1', 'Cost BK');
        $sheet6->setCellValue('W1', 'Cost Cabut');
        $sheet6->setCellValue('X1', 'Cost Eo');
        $sheet6->setCellValue('Y1', 'Cost Cetak');
        $sheet6->setCellValue('Z1', 'Cost Sortir');
        $sheet6->setCellValue('AA1', 'Cost Cu');
        $sheet6->setCellValue('AB1', 'Cost Oprasional');
        $sheet6->setCellValue('AC1', 'Total Rp');

        $kolom6 = 2;
        foreach ($bk_sinta as $no => $b) {
            $bk_stock = TotalanModel::bkstock($b->nm_partai);
            $bk_proses = TotalanModel::bksedang_proses($b->nm_partai);
            $bk_selesai_siap_ctk = TotalannewModel::bkselesai_siap_ctk($b->nm_partai);
            $bk_selesai_siap_str = TotalanModel::bkselesai_siap_str($b->nm_partai);
            $cetak_stok = TotalanModel::cetak_stok($b->nm_partai);
            $cetak_proses2 = TotalanModel::cetak_proses($b->nm_partai);
            $cetak_selesai2 = TotalannewModel::cetak_selesai($b->nm_partai);
            $stock_sortir = TotalanModel::stock_sortir($b->nm_partai);
            $sortir_proses2 = TotalanModel::sortir_proses($b->nm_partai);
            $sortir_selesai2 = TotalanModel::sortir_selesai($b->nm_partai);
            $grading_stock2 = TotalannewModel::grading_stock($b->nm_partai);
            $box_belum_kirim = TotalanModel::box_belum_kirim($b->nm_partai);

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Box Stock');
            $sheet6->setCellValue('S' . $kolom6, round($bk_stock->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($bk_stock->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($bk_stock->gr) ? 0 : round($bk_stock->ttl_rp / $bk_stock->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($bk_stock->ttl_rp ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, round($bk_stock->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, 0);
            $sheet6->setCellValue('AC' . $kolom6, round($bk_stock->ttl_rp ?? 0, 0) + round($bk_stock->cost_cu ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Box sedang proses');
            $sheet6->setCellValue('S' . $kolom6, round($bk_proses->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($bk_proses->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($bk_proses->gr) ? 0 : round($bk_proses->ttl_rp / $bk_proses->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($bk_proses->ttl_rp ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, round($bk_proses->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, 0);
            $sheet6->setCellValue('AC' . $kolom6, round($bk_proses->ttl_rp ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Box selesai siap cetak');
            $sheet6->setCellValue('S' . $kolom6, round($bk_selesai_siap_ctk->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($bk_selesai_siap_ctk->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($bk_selesai_siap_ctk->gr) ? 0 : round($bk_selesai_siap_ctk->ttl_rp / $bk_selesai_siap_ctk->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($bk_selesai_siap_ctk->cost_bk ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($bk_selesai_siap_ctk->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, round($bk_selesai_siap_ctk->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($bk_selesai_siap_ctk->cost_op ?? 0, 0));
            $sheet6->setCellValue('AC' . $kolom6, round($bk_selesai_siap_ctk->ttl_rp ?? 0, 0) + round($bk_selesai_siap_ctk->cost_op_cbt ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Box selesai siap sortir');
            $sheet6->setCellValue('S' . $kolom6, round($bk_selesai_siap_str->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($bk_selesai_siap_str->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($bk_selesai_siap_str->gr) ? 0 : round($bk_selesai_siap_str->ttl_rp / $bk_selesai_siap_str->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($bk_selesai_siap_str->cost_bk ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($bk_selesai_siap_str->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, round($bk_selesai_siap_str->cost_eo ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, round($bk_selesai_siap_str->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($bk_selesai_siap_str->cost_op ?? 0, 0));
            $sheet6->setCellValue('AC' . $kolom6, round($bk_selesai_siap_str->ttl_rp ?? 0, 0) + round($bk_selesai_siap_str->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Cetak Stok');
            $sheet6->setCellValue('S' . $kolom6, round($cetak_stok->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($cetak_stok->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($cetak_stok->gr) ? 0 : round($cetak_stok->ttl_rp / $cetak_stok->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($cetak_stok->cost_bk ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($cetak_stok->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, round($cetak_stok->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($cetak_stok->cost_op ?? 0, 0));
            $sheet6->setCellValue('AC' . $kolom6, round($cetak_stok->ttl_rp ?? 0, 0) + round($cetak_stok->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Cetak sedang proses');
            $sheet6->setCellValue('S' . $kolom6, round($cetak_proses2->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($cetak_proses2->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($cetak_proses2->gr) ? 0 : round($cetak_proses2->ttl_rp / $cetak_proses2->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($cetak_proses2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($cetak_proses2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, round($cetak_proses2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($cetak_proses2->cost_op ?? 0, 0));
            $sheet6->setCellValue('AC' . $kolom6, round($cetak_proses2->ttl_rp ?? 0, 0) + round($cetak_proses2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Cetak selesai siap sortir');
            $sheet6->setCellValue('S' . $kolom6, round($cetak_selesai2->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($cetak_selesai2->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($cetak_selesai2->gr) ? 0 : round($cetak_selesai2->ttl_rp / $cetak_selesai2->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($cetak_selesai2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($cetak_selesai2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, round($cetak_selesai2->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, round($cetak_selesai2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($cetak_selesai2->cost_op ?? 0, 0));

            $sheet6->setCellValue('AC' . $kolom6, round($cetak_selesai2->ttl_rp ?? 0, 0) + round($cetak_selesai2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Sortir Stok');
            $sheet6->setCellValue('S' . $kolom6, round($stock_sortir->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($stock_sortir->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($stock_sortir->gr) ? 0 : round($stock_sortir->ttl_rp / $stock_sortir->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($stock_sortir->cost_bk ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($stock_sortir->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, round($stock_sortir->cost_eo ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, round($stock_sortir->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, round($stock_sortir->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($stock_sortir->cost_op ?? 0, 0));
            $sheet6->setCellValue('AC' . $kolom6, round($stock_sortir->ttl_rp ?? 0, 0) +  round($stock_sortir->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Sortir sedang proses');
            $sheet6->setCellValue('S' . $kolom6, round($sortir_proses2->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($sortir_proses2->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($sortir_proses2->gr) ? 0 : round($sortir_proses2->ttl_rp / $sortir_proses2->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($sortir_proses2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($sortir_proses2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, round($sortir_proses2->cost_eo ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, round($sortir_proses2->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, round($sortir_proses2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($sortir_proses2->cost_op ?? 0, 0));
            $sheet6->setCellValue('AC' . $kolom6, round($sortir_proses2->ttl_rp ?? 0, 0) + round($sortir_proses2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Sortir selesai siap grading');
            $sheet6->setCellValue('S' . $kolom6, round($sortir_selesai2->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($sortir_selesai2->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($sortir_selesai2->gr) ? 0 : round($sortir_selesai2->ttl_rp / $sortir_selesai2->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($sortir_selesai2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($sortir_selesai2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, round($sortir_selesai2->cost_eo ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, round($sortir_selesai2->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Z' . $kolom6, round($sortir_selesai2->cost_str ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($sortir_selesai2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($sortir_selesai2->cost_op ?? 0, 0));
            $sheet6->setCellValue('AC' . $kolom6, round($sortir_selesai2->ttl_rp ?? 0, 0) + round($sortir_selesai2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Grading Stock');
            $sheet6->setCellValue('S' . $kolom6, round($grading_stock2->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($grading_stock2->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($grading_stock2->gr) ? 0 : round($grading_stock2->ttl_rp / $grading_stock2->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, round($grading_stock2->cost_bk ?? 0, 0));
            $sheet6->setCellValue('W' . $kolom6, round($grading_stock2->cost_cbt ?? 0, 0));
            $sheet6->setCellValue('X' . $kolom6, round($grading_stock2->cost_eo ?? 0, 0));
            $sheet6->setCellValue('Y' . $kolom6, round($grading_stock2->cost_ctk ?? 0, 0));
            $sheet6->setCellValue('Z' . $kolom6, round($grading_stock2->cost_str ?? 0, 0));
            $sheet6->setCellValue('AA' . $kolom6, round($grading_stock2->cost_cu ?? 0, 0));
            $sheet6->setCellValue('AB' . $kolom6, round($grading_stock2->cost_op ?? 0, 0));
            $sheet6->setCellValue('AC' . $kolom6, round($grading_stock2->ttl_rp ?? 0, 0) + round($grading_stock2->cost_op ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Box belum kirim');
            $sheet6->setCellValue('S' . $kolom6, round($box_belum_kirim->pcs ?? 0, 0));
            $sheet6->setCellValue('T' . $kolom6, round($box_belum_kirim->gr ?? 0, 0));
            $sheet6->setCellValue('U' . $kolom6, empty($box_belum_kirim->gr) ? 0 : round($box_belum_kirim->ttl_rp / $box_belum_kirim->gr, 0));
            $sheet6->setCellValue('V' . $kolom6, 0);
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, 0);
            $sheet6->setCellValue('AB' . $kolom6, 0);
            $sheet6->setCellValue('AC' . $kolom6, round($box_belum_kirim->ttl_rp ?? 0, 0));

            $kolom6++;

            $sheet6->setCellValue('Q' . $kolom6, $b->nm_partai);
            $sheet6->setCellValue('R' . $kolom6, 'Box selesai kirim');
            $sheet6->setCellValue('S' . $kolom6, 0);
            $sheet6->setCellValue('T' . $kolom6, 0);
            $sheet6->setCellValue('U' . $kolom6, 0);
            $sheet6->setCellValue('V' . $kolom6, 0);
            $sheet6->setCellValue('W' . $kolom6, 0);
            $sheet6->setCellValue('X' . $kolom6, 0);
            $sheet6->setCellValue('Y' . $kolom6, 0);
            $sheet6->setCellValue('Z' . $kolom6, 0);
            $sheet6->setCellValue('AA' . $kolom6, 0);
            $sheet6->setCellValue('AB' . $kolom6, 0);
            $sheet6->setCellValue('AC' . $kolom6, 0);

            $kolom6++;
        }
        $sheet6->getStyle('Q2:AC' . $kolom6 - 1)->applyFromArray($style);

        // box stock

        // batas ke lima
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(6);
        $sheet7 = $spreadsheet->getActiveSheet(6);
        $sheet7->setTitle('cost');

        $oprasional = DB::select("SELECT sum(a.ttl_gaji) as ttl_gaji, c.rp_oprasional, b.nm_bulan
        FROM tb_gaji_penutup as a 
        left join bulan as b on b.bulan = a.bulan_dibayar
        left join oprasional as c on c.bulan = a.bulan_dibayar and a.tahun_dibayar = c.tahun
        group by a.bulan_dibayar , a.tahun_dibayar;");


        $sheet7->getStyle("A1:B1")->applyFromArray($style_atas);
        $sheet7->setCellValue('A1', 'Bulan');
        $sheet7->setCellValue('B1', 'Rp Oprasional');

        $kolomcost = 2;
        foreach ($oprasional as $o) {
            $sheet7->setCellValue('A' . $kolomcost, $o->nm_bulan);
            $sheet7->setCellValue('B' . $kolomcost, $o->rp_oprasional + $o->ttl_gaji);
            $kolomcost++;
        }
        $sheet7->getStyle('A2:B' . $kolomcost - 1)->applyFromArray($style);






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

    public function getSummaryIbudsa(IbuSUmmary $model)
    {
        $cbtapcs = $model::bkstockawal_sum()->pcs + $this->getSuntikan(11)->pcs;
        $cbtagr = $model::bkstockawal_sum()->gr + $this->getSuntikan(11)->gr;
        $cbtarp = $model::bkstockawal_sum()->ttl_rp + $this->getSuntikan(11)->ttl_rp;

        $cbt = [
            [
                'awal' => [
                    'label' =>  'box stock awal bk',
                    'apcs' => $cbtapcs,
                    'agr' => $cbtagr,
                    'arp' => $cbtarp,

                ],
                'opname1' => [
                    'label' =>  'box stock cabut sedang proses',

                    'cpcs' => $model::bksedang_proses_sum()->pcs,
                    'cgr' => $model::bksedang_proses_sum()->gr,
                    'crp' => $model::bksedang_proses_sum()->ttl_rp,
                ],
                'opname2' => [
                    'label' =>  'box selesai cabut siap cetak belum serah',

                    'b2pcs' => $model::bkselesai_siap_ctk_sum()->pcs,
                    'b2gr' => $model::bkselesai_siap_ctk_sum()->gr,
                    'b2rp' => $model::bkselesai_siap_ctk_sum()->ttl_rp,

                    'cost_kerja' => $model::bkselesai_siap_ctk_sum()->cost_kerja,
                    'cost_op' => 3,
                    'cost_dll' => 3,
                ],
                'proses1' => [
                    'label' =>  'box selesai cabut siap cetak diserahkan',
                    'b2pcs' => $model::bkselesai_siap_ctk_diserahkan_sum()->pcs + $this->getSuntikan(14)->pcs,
                    'b2gr' => $model::bkselesai_siap_ctk_diserahkan_sum()->gr + $this->getSuntikan(14)->gr,
                    'b2rp' => $model::bkselesai_siap_ctk_diserahkan_sum()->ttl_rp + $this->getSuntikan(14)->ttl_rp,

                    'cost_kerja' => $model::bkselesai_siap_ctk_diserahkan_sum()->cost_kerja,
                    'cost_op' => 3,
                    'cost_dll' => 3,
                ],
                'opname3' => [
                    'label' =>  'box selesai cbt siap sortir belum serah',
                    'b2pcs' => 0,
                    'b2gr' => $model::bkselesai_siap_str_sum()->gr,
                    'b2rp' => $model::bkselesai_siap_str_sum()->ttl_rp,

                    'cost_kerja' => $model::bkselesai_siap_str_sum()->cost_kerja,
                    'cost_op' => 3,
                    'cost_dll' => 3,
                ],
                'proses2' => [
                    'label' =>  'box selesai cbt siap sortir diserahkan',
                    'b2pcs' => 0,
                    'b2gr' => $model::bkselesai_siap_str_diserahkan_sum()->gr + $this->getSuntikan(16)->gr,
                    'b2rp' => $model::bkselesai_siap_str_diserahkan_sum()->ttl_rp + $this->getSuntikan(16)->ttl_rp,

                    'cost_kerja' => $model::bkselesai_siap_str_diserahkan_sum()->cost_kerja,
                    'cost_op' => 3,
                    'cost_dll' => 3,
                ],
                'opname4' => [
                    'label' =>  'box cbt sisa pgws',
                    'cpcs' => $model::bkstock_sum()->pcs,
                    'cgr' => $model::bkstock_sum()->gr,
                    'crp' => $model::bkstock_sum()->ttl_rp,
                ],
            ],
        ];

        $ctk = [
            [
                'awal' => [
                    'label' =>  'cetak opname',
                    'apcs' => $this->getSummary(21)->pcs,
                    'agr' => $this->getSummary(21)->gr,
                    'arp' => $this->getSummary(21)->ttl_rp,
                ],
                'awal2' => [
                    'label' =>  'cetak stock awal',
                    'apcs' => $model::cetak_stok_awal()->pcs + $this->getSuntikan(22)->pcs,
                    'agr' => $model::cetak_stok_awal()->gr + $this->getSuntikan(22)->gr,
                    'arp' => $model::cetak_stok_awal()->ttl_rp + $this->getSuntikan(22)->ttl_rp,
                ],
                // 'opname' => [
                //     'label' =>  'cetak sedang proses',
                //     'apcs' => $cbtapcs,
                //     'agr' => 2,
                //     'arp' => 3,

                // ],

                // 'opname3' => [
                //     'label' =>  'cetak selesai siap sortir belum serah',
                //     'apcs' => $cbtapcs,
                //     'agr' => 2,
                //     'arp' => 3,

                // ],
                // 'proses' => [
                //     'label' =>  'tidak cetak diserahkan',
                //     'apcs' => $cbtapcs,
                //     'agr' => 2,
                //     'arp' => 3,

                // ],
                // 'proses1' => [
                //     'label' =>  'cetak selesai siap sortir diserahkan',
                //     'apcs' => $cbtapcs,
                //     'agr' => 2,
                //     'arp' => 3,

                // ],
                // 'opname4' => [
                //     'label' =>  'cetak sisa pgws ',
                //     'apcs' => $cbtapcs,
                //     'agr' => 2,
                //     'arp' => 3,

                // ],

            ],
        ];

        $sortir = [
            ['awal' => 'sortir opname'],
            ['awal' => 'sortir stock awal'],
            ['opname' => 'sortir sedang proses'],
            ['opname' => 'sortir selesai siap grading belum serah'],
            ['proses' => 'sortir selesai siap grading diserahkan'],
            ['opname' => 'sortir sisa pgws'],
        ];

        $pengiriman = [
            ['awal' => 'siap kirim opname'],
            ['awal' => 'grading stock'],
            ['opname' => 'box belum kirim gudang wip'],
            ['opname' => 'box selesai kirim pengiriman'],
        ];

        $datas = [
            'cabut' => $cbt,
            // 'cetak' => $ctk,
            // 'sortir' => $sortir,
            // 'pengiriman' => $pengiriman
        ];

        $data = [
            'title' => 'Data Totalan',
            'cbt' => $cbt,
            'datas' => $datas
        ];
        return view('home.gudang.get_summary_ibu', $data);
    }

    public function getSummaryIbu(IbuSUmmary $model)
    {

        // cabut
        $a11 = $model::bkstockawal_sum();
        $a11suntik = $this->getSuntikan(11);
        $a14suntik = $this->getSuntikan(14);
        $a16suntik = $this->getSuntikan(16);

        $a11pcs = $a11->pcs + $a11suntik->pcs;
        $a11gr = $a11->gr + $a11suntik->gr;
        $a11ttlrp = $a11->ttl_rp + $a11suntik->ttl_rp;

        $a12 = $model::bksedang_proses_sum();
        $a12pcs = $a12->pcs;
        $a12gr = $a12->gr;
        $a12ttlrp = $a12->ttl_rp;

        $a12 = $model::bksedang_proses_sum();
        $a12pcs = $a12->pcs;
        $a12gr = $a12->gr;
        $a12ttlrp = $a12->ttl_rp;

        $a13 = $model::bkselesai_siap_ctk_sum();
        $a13pcs = $a13->pcs;
        $a13gr = $a13->gr;
        $a13ttlrp = $a13->ttl_rp;
        $a13costkerja = $a13->cost_kerja;

        $a14 = $model::bkselesai_siap_ctk_diserahkan_sum();
        $a14pcs = $a14->pcs + $a14suntik->pcs;
        $a14gr = $a14->gr + $a14suntik->gr;
        $a14ttlrp = $a14->ttl_rp + $a14suntik->ttl_rp;
        $a14costkerja = $a14->cost_kerja;


        $a15 = $model::bkselesai_siap_str_sum();
        $a15pcs = $a15->pcs ?? 0;
        $a15gr = $a15->gr;
        $a15ttlrp = $a15->ttl_rp;
        $a15costkerja = $a15->cost_kerja;


        $a16 = $model::bkselesai_siap_str_diserahkan_sum();
        $a16pcs = $a16->pcs ?? 0;
        $a16gr = $a16->gr + $a16suntik->gr;
        $a16ttlrp = $a16->ttl_rp + $a16suntik->ttl_rp;
        $a16costkerja = $a16->cost_kerja;


        $a17 = $model::bkstock_sum();
        $a17pcs = $a17->pcs;
        $a17gr = $a17->gr;
        $a17ttlrp = $a17->ttl_rp;
        // ---- end cabut

        // cetak
            $ca11 = $this->getSuntikan(21);
            $ca11pcs = $ca11->pcs;
            $ca11gr = $ca11->gr;
            $ca11ttlrp = $ca11->ttl_rp;

            $ca12 = $model::cetak_stok_awal();
            $ca12suntik = $this->getSuntikan(14);
            $ca12pcs = $ca12->pcs + $ca12suntik->pcs;
            $ca12gr = $ca12->gr + $ca12suntik->gr;
            $ca12ttlrp = $ca12->ttl_rp + $ca12suntik->ttl_rp;

            $ca13 = $model::cetak_proses();
            $ca13pcs = $ca13->pcs;
            $ca13gr = $ca13->gr;
            $ca13ttlrp = $ca13->ttl_rp;
            $ca13costkerja = $ca13->cost_kerja;

            $ca14 = $model::cetak_selesai();
            $ca14pcs = $ca14->pcs;
            $ca14gr = $ca14->gr;
            $ca14ttlrp = $ca14->ttl_rp;
            $ca14costkerja = $ca14->cost_kerja;

            $ca15 = $model::tdk_cetak_selesai_diserahkan();
            $ca15pcs = $ca15->pcs;
            $ca15gr = $ca15->gr;
            $ca15ttlrp = $ca15->ttl_rp;
            $ca15costkerja = $ca15->cost_kerja;

            $ca16 = $model::cetak_selesai_diserahkan();
            $ca16suntik = $this->getSuntikan(26);
            $ca16pcs = $ca16->pcs + $ca16suntik->pcs;
            $ca16gr = $ca16->gr + $ca16suntik->gr;
            $ca16ttlrp = $ca16->ttl_rp + $ca16suntik->ttl_rp;
            $ca16costkerja = $ca16->cost_kerja;

            $ca17 = $model::cetak_stok();
            $ca17suntik = $this->getSuntikan(27);

            $ca17pcs = $ca17->pcs + $ca17suntik->pcs;
            $ca17gr = $ca17->gr + $ca17suntik->gr;
            $ca17ttlrp = $ca17->ttl_rp + $ca17suntik->ttl_rp;
        // ---- end cetak

        // sortir
            $s1 = $this->getSuntikan(31);
            $s1pcs = $s1->pcs;
            $s1gr = $s1->gr;
            $s1ttlrp = $s1->ttl_rp;

            $s2 = $model::stock_sortir_awal();
            $s2suntik = $this->getSuntikan(32);
            $s2pcs = $s2->pcs + $s2suntik->pcs;
            $s2gr = $s2->gr + $s2suntik->gr;
            $s2ttlrp = $s2->ttl_rp + $s2suntik->ttl_rp;

            $s3 = $model::sortir_proses();
            $s3pcs = $s3->pcs;
            $s3gr = $s3->gr;
            $s3ttlrp = $s3->ttl_rp;

            $s4 = $model::sortir_selesai();
            $s4pcs = $s4->pcs;
            $s4gr = $s4->gr;
            $s4ttlrp = $s4->ttl_rp;
            $s4cost_kerja = $s4->cost_kerja;

            $s5 = $model::sortir_selesai_diserahkan();
            $s5suntik = $this->getSuntikan(35);
            $s5pcs = $s5->pcs + $s5suntik->pcs;
            $s5gr = $s5->gr + $s5suntik->gr;
            $s5ttlrp = $s5->ttl_rp + $s5suntik->ttl_rp;
            $s5cost_kerja = $s5->cost_kerja;

            $s6 = $model::stock_sortir();
            $s6pcs = $s6->pcs;
            $s6gr = $s6->gr;
            $s6ttlrp = $s6->ttl_rp;
        // ---- end sortir

        // pengiriman
            $p1 = $this->getSuntikan(41);
            $p1pcs = $p1->pcs;
            $p1gr = $p1->gr;
            $p1ttlrp = $p1->ttl_rp;

            $p2 = $model::grading_stock();
            $p2suntik = $this->getSuntikan(42);
            $p2pcs = $p2->pcs + $p2suntik->pcs;
            $p2gr = $p2->gr + $p2suntik->gr;
            $p2ttlrp = $p2->ttl_rp + $p2suntik->ttl_rp;

            $p3 = $this->getSuntikan(41);
            $p3pcs = 8089;
            $p3gr = 46030;
            $p3ttlrp = 524883058;

            $p4 = $this->getSuntikan(41);
            $p4pcs = 62769;
            $p4gr = 370722;
            $p4ttlrp = 4227366871;

        // ---- end pengiriman

        // summary total 
        $cpcs = $a12pcs + $a17pcs;
        $cgr = $a12gr + $a17gr;
        $cttlrp = $a12ttlrp + $a17ttlrp;

        $b2pcs = $a13pcs + $a14pcs;
        $b2gr = $a13gr + $a14gr + $a15gr + $a16gr;
        $b2ttlrp = $a13ttlrp + $a14ttlrp + $a15ttlrp + $a16ttlrp;

        $cost_kerja = $a13costkerja + $a14costkerja + $a15costkerja + $a16costkerja;

        $sumTtlCbt = [
            'apcs' => $a11pcs,
            'agr' => $a11gr,
            'attlrp' => $a11ttlrp,

            'bpcs' => $a11pcs - $cpcs,
            'bgr' => $a11gr - $cgr,
            'battlrp' => $a11ttlrp - $cttlrp,

            'b2pcs' => $b2pcs,
            'b2gr' => $b2gr,
            'b2ttlrp' => $b2ttlrp,

            'cost_kerja' => $cost_kerja,
            'cost_op' => 0,
            'cost_dl' => 0,

            'cpcs' => $cpcs,
            'cgr' => $cgr,
            'cttlrp' => $cttlrp,
        ];

        $cpcs = $ca13pcs + $ca17pcs;
        $cgr = $ca13gr + $ca17gr;
        $cttlrp = $ca13ttlrp + $ca17ttlrp;

        $b2pcs = $ca14pcs + $ca15pcs + $ca16pcs;
        $b2gr =  $ca14gr + $ca15gr + $ca16gr;
        $b2ttlrp =  $ca14ttlrp + $ca16ttlrp;

        $cost_kerja = $ca14costkerja + $ca16costkerja;

        $sumTtlCtk = [
            'apcs' => $ca11pcs + $ca12pcs,
            'agr' => $ca11gr + $ca12gr,
            'attlrp' => $ca11ttlrp + $ca12ttlrp,

            'bpcs' => ($ca11pcs + $ca12pcs) - $cpcs,
            'bgr' => ($ca11gr + $ca12gr) - $cgr,
            'battlrp' => ($ca11ttlrp + $ca12ttlrp) - $cttlrp,

            'b2pcs' => $b2pcs,
            'b2gr' => $b2gr,
            'b2ttlrp' => $b2ttlrp,

            'cost_kerja' => $cost_kerja,
            'cost_op' => 0,
            'cost_dl' => 0,

            'cpcs' => $cpcs,
            'cgr' => $cgr,
            'cttlrp' => $cttlrp,
        ];

        $cpcs = $ca13pcs + $ca17pcs;
        $cgr = $ca13gr + $ca17gr;
        $cttlrp = $ca13ttlrp + $ca17ttlrp;

        $b2pcs = $ca14pcs + $ca15pcs + $ca16pcs;
        $b2gr =  $ca14gr + $ca15gr + $ca16gr;
        $b2ttlrp =  $ca14ttlrp + $ca16ttlrp;

        $cost_kerja = $ca14costkerja + $ca16costkerja;

        $sumTtlCtk = [
            'apcs' => $ca11pcs + $ca12pcs,
            'agr' => $ca11gr + $ca12gr,
            'attlrp' => $ca11ttlrp + $ca12ttlrp,

            'bpcs' => ($ca11pcs + $ca12pcs) - $cpcs,
            'bgr' => ($ca11gr + $ca12gr) - $cgr,
            'battlrp' => ($ca11ttlrp + $ca12ttlrp) - $cttlrp,

            'b2pcs' => $b2pcs,
            'b2gr' => $b2gr,
            'b2ttlrp' => $b2ttlrp,

            'cost_kerja' => $cost_kerja,
            'cost_op' => 0,
            'cost_dl' => 0,

            'cpcs' => $cpcs,
            'cgr' => $cgr,
            'cttlrp' => $cttlrp,
        ];

        $cpcs = $s3pcs + $s6pcs;
        $cgr = $s3gr + $s6gr;
        $cttlrp = $s3ttlrp + $s6ttlrp;

        $b2pcs = $s4pcs + $s5pcs;
        $b2gr =  $s4gr + $s5gr;
        $b2ttlrp =  $s4ttlrp + $s5ttlrp;

        $cost_kerja = $s5cost_kerja;

        $sumTtlSortir = [
            'apcs' => $s1pcs + $s2pcs,
            'agr' => $s1gr + $s2gr,
            'attlrp' => $s1ttlrp + $s2ttlrp,

            'bpcs' => ($s1pcs + $s2pcs) - $cpcs,
            'bgr' => ($s1gr + $s2gr) - $cgr,
            'battlrp' => ($s1ttlrp + $s2ttlrp) - $cttlrp,

            'b2pcs' => $b2pcs,
            'b2gr' => $b2gr,
            'b2ttlrp' => $b2ttlrp,

            'cost_kerja' => $cost_kerja,
            'cost_op' => 0,
            'cost_dl' => 0,

            'cpcs' => $cpcs,
            'cgr' => $cgr,
            'cttlrp' => $cttlrp,
        ];

        $cpcs = $p3pcs;
        $cgr = $p3gr;
        $cttlrp = $p3ttlrp;

        $b2pcs = $p4pcs;
        $b2gr =  $p4gr;
        $b2ttlrp =  $p4ttlrp;

        $cost_kerja = 0;

        $sumTtlpengiriman = [
            'apcs' => $p1pcs + $p2pcs,
            'agr' => $p1gr + $p2gr,
            'attlrp' => $p1ttlrp + $p2ttlrp,

            'bpcs' => ($p1pcs + $p2pcs) - $cpcs,
            'bgr' => ($p1gr + $p2gr) - $cgr,
            'battlrp' => ($p1ttlrp + $p2ttlrp) - $cttlrp,

            'b2pcs' => $b2pcs,
            'b2gr' => $b2gr,
            'b2ttlrp' => $b2ttlrp,

            'cost_kerja' => $cost_kerja,
            'cost_op' => 0,
            'cost_dl' => 0,

            'cpcs' => $cpcs,
            'cgr' => $cgr,
            'cttlrp' => $cttlrp,
        ];
        // ---- end total

        $cost_op_pcs = $sumTtlCbt['b2pcs'] + $sumTtlSortir['b2pcs'] + $sumTtlCtk['b2pcs'];
        $cost_op_gr = $sumTtlCbt['b2gr'] + $sumTtlSortir['b2gr'] + $sumTtlCtk['b2gr'];
        $akhir_kerja = $sumTtlCbt['cost_kerja'] + $sumTtlSortir['cost_kerja'] + $sumTtlCtk['cost_kerja'];

        $cost_dll = DB::table('tb_gaji_penutup')->sum('dll');
        $cost_cu =  DB::selectOne("SELECT sum(a.ttl_rp) as cost_cu
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    where b.kategori ='CU' and a.bulan_dibayar BETWEEN '6' and '8';")->cost_cu;
        $denda = DB::table('tb_denda')->whereIn('bulan_dibayar', [6, 7, 8])->sum('nominal');
        $uangCost = 1815907127.33;

        $awal_pcs = $a11pcs + $ca11pcs + $s1pcs + $p1pcs + 2;
        $awal_gr = $a11gr + $ca11gr + $s1gr + $p1gr;
        $awal_rp_bk = $a11ttlrp + $ca11ttlrp + $s1ttlrp + $p1ttlrp;

        $akhir_pcs = $a12pcs + $a13pcs + $a15pcs + $a17pcs + $ca17pcs + $s3pcs  + $s4pcs + $s6pcs + $p3pcs + $p4pcs;
        $akhir_gr = $a12gr + $a13gr + $a15gr + $a17gr + $ca17gr + $s3gr  + $s4gr + $s6gr + $p3gr + $p4gr;
        $akhir_rp_bk = $a12ttlrp + $a13ttlrp + $a15ttlrp + $a17ttlrp + $ca17ttlrp + $s3ttlrp  + $s4ttlrp + $s6ttlrp + $p3ttlrp + $p4ttlrp;

        $cost_op = $uangCost - $akhir_kerja - $cost_dll - $cost_cu + $denda;
        $rp_gr_op = $cost_op / $cost_op_gr;
        $rp_gr_dll = ($cost_dll + $cost_cu - $denda) / $cost_op_gr;

        $a13op = $a13gr * $rp_gr_op;
        $a14op = $a14gr * $rp_gr_op;
        $a15op = $a15gr * $rp_gr_op;
        $a16op = $a16gr * $rp_gr_op;

        $ca14op = $ca14gr * $rp_gr_op;
        $ca15op = $ca15gr * $rp_gr_op;
        $ca16op = $ca16gr * $rp_gr_op;

        $s4op = $s4gr * $rp_gr_op;
        $s5op = $s5gr * $rp_gr_op;

        $a13dll = $a13gr * $rp_gr_dll;
        $a14dll = $a14gr * $rp_gr_dll;
        $a15dll = $a15gr * $rp_gr_dll;
        $a16dll = $a16gr * $rp_gr_dll;

        $ca14dll = $ca14gr * $rp_gr_dll;
        $ca15dll = $ca15gr * $rp_gr_dll;
        $ca16dll = $ca16gr * $rp_gr_dll;

        $s4dll = $s4gr * $rp_gr_dll;
        $s5dll = $s5gr * $rp_gr_dll;

        $ttlOp = $a13op + $a14op + $a15op + $a16op + $ca14op + $ca15op + $ca16op + $s4op + $s5op;
        $ttlDll = $a13dll + $a14dll + $a15dll + $a16dll + $ca14dll + $ca15dll + $ca16dll + $s4dll + $s5dll;

        $data = [
            'title' => 'Data Totalan',
            'cost_dll' => $cost_dll,
            'cost_cu' => $cost_cu,
            'denda' => $denda,
            'uangCost' => $uangCost,

            'awal_pcs' => $awal_pcs,
            'awal_gr' => $awal_gr,
            'awal_rp_bk' => $awal_rp_bk,
            'akhir_pcs' => $akhir_pcs,
            'akhir_gr' => $akhir_gr,
            'akhir_rp_bk' => $akhir_rp_bk,
            'akhir_kerja' => $akhir_kerja,

            'cost_op_pcs' => $cost_op_pcs,
            'cost_op_gr' => $cost_op_gr,

            'cost_op' => $cost_op,
            'rp_gr_op' => $rp_gr_op,
            'rp_gr_dll' => $rp_gr_dll,

            'a13op' => $a13op,
            'a14op' => $a14op,
            'a15op' => $a15op,
            'a16op' => $a16op,

            'a13dll' => $a13dll,
            'a14dll' => $a14dll,
            'a15dll' => $a15dll,
            'a16dll' => $a16dll,

            'ca14op' => $ca14op,
            'ca15op' => $ca15op,
            'ca16op' => $ca16op,

            'ca14dll' => $ca14dll,
            'ca15dll' => $ca15dll,
            'ca16dll' => $ca16dll,

            's4op' => $s4op,
            's5op' => $s5op,

            's4dll' => $s4dll,
            's5dll' => $s5dll,

            'ttlOp' => $ttlOp,
            'ttlDll' => $ttlDll,

            // cabut
            'a11pcs' => $a11pcs,
            'a11gr' => $a11gr,
            'a11ttlrp' => $a11ttlrp,

            'a12pcs' => $a12pcs,
            'a12gr' => $a12gr,
            'a12ttlrp' => $a12ttlrp,

            'a13pcs' => $a13pcs,
            'a13gr' => $a13gr,
            'a13ttlrp' => $a13ttlrp,
            'a13costkerja' => $a13costkerja,

            'a14pcs' => $a14pcs,
            'a14gr' => $a14gr,
            'a14ttlrp' => $a14ttlrp,
            'a14costkerja' => $a14costkerja,

            'a15pcs' => $a15pcs,
            'a15gr' => $a15gr,
            'a15ttlrp' => $a15ttlrp,
            'a15costkerja' => $a15costkerja,

            'a16pcs' => $a16pcs,
            'a16gr' => $a16gr,
            'a16ttlrp' => $a16ttlrp,
            'a16costkerja' => $a16costkerja,

            'a17pcs' => $a17pcs,
            'a17gr' => $a17gr,
            'a17ttlrp' => $a17ttlrp,
            // end cabut

            // cetak
            'ca11pcs' => $ca11pcs,
            'ca11gr' => $ca11gr,
            'ca11ttlrp' => $ca11ttlrp,

            'ca12pcs' => $ca12pcs,
            'ca12gr' => $ca12gr,
            'ca12ttlrp' => $ca12ttlrp,

            'ca13pcs' => $ca13pcs,
            'ca13gr' => $ca13gr,
            'ca13ttlrp' => $ca13ttlrp,
            'ca13costkerja' => $ca13costkerja,

            'ca14pcs' => $ca14pcs,
            'ca14gr' => $ca14gr,
            'ca14ttlrp' => $ca14ttlrp,
            'ca14costkerja' => $ca14costkerja,

            'ca15pcs' => $ca15pcs,
            'ca15gr' => $ca15gr,
            'ca15ttlrp' => $ca15ttlrp,
            'ca15costkerja' => $ca15costkerja,

            'ca16pcs' => $ca16pcs,
            'ca16gr' => $ca16gr,
            'ca16ttlrp' => $ca16ttlrp,
            'ca16costkerja' => $ca16costkerja,

            'ca17pcs' => $ca17pcs,
            'ca17gr' => $ca17gr,
            'ca17ttlrp' => $ca17ttlrp,
            // end cetak

            // sortir
            's1pcs' => $s1pcs,
            's1gr' => $s1gr,
            's1ttlrp' => $s1ttlrp,

            's2pcs' => $s2pcs,
            's2gr' => $s2gr,
            's2ttlrp' => $s2ttlrp,

            's3pcs' => $s3pcs,
            's3gr' => $s3gr,
            's3ttlrp' => $s3ttlrp,

            's4pcs' => $s4pcs,
            's4gr' => $s4gr,
            's4ttlrp' => $s4ttlrp,
            's4cost_kerja' => $s4cost_kerja,

            's5pcs' => $s5pcs,
            's5gr' => $s5gr,
            's5ttlrp' => $s5ttlrp,
            's5cost_kerja' => $s5cost_kerja,

            's6pcs' => $s6pcs,
            's6gr' => $s6gr,
            's6ttlrp' => $s6ttlrp,
            // end sortir

            // pengiriman
            'p1pcs' => $p1pcs,
            'p1gr' => $p1gr,
            'p1ttlrp' => $p1ttlrp,

            'p2pcs' => $p2pcs,
            'p2gr' => $p2gr,
            'p2ttlrp' => $p2ttlrp,

            'p3pcs' => $p3pcs,
            'p3gr' => $p3gr,
            'p3ttlrp' => $p3ttlrp,

            'p4pcs' => $p4pcs,
            'p4gr' => $p4gr,
            'p4ttlrp' => $p4ttlrp,
            // end pengiriman

        ];
        return view('home.gudang.get_summary_ibu2', $data);
    }

    public function getOpname($index)
    {
        $datas = [
            11 => gudangcekModel::bkstockawal(),
            12 => gudangcekModel::bksedang_proses(),
            13 => gudangcekModel::bkselesai_siap_ctk(),
            14 => gudangcekModel::bkselesai_siap_ctk_diserahkan(),
            15 => gudangcekModel::bkselesai_siap_str(),
            16 => gudangcekModel::bkselesai_siap_str_diserahkan(),
            17 => gudangcekModel::bkstock(),

            21 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_awal_stock'"),
            22 => gudangcekModel::cetak_stok_awal(),
            23 => gudangcekModel::cetak_proses(),
            24 => gudangcekModel::cetak_selesai(),
            25 => gudangcekModel::tdk_cetak_selesai_diserahkan(),
            26 => gudangcekModel::cetak_selesai_diserahkan(),
            27 => gudangcekModel::cetak_stok(),
        ];
        if (array_key_exists($index, $datas)) {
            return $datas[$index];
        } else {
            return false;
        }
    }

    public function getSummary($index)
    {
        $datas = [
            11 => IbuSummary::bkstockawal_sum(),
            12 => IbuSummary::bksedang_proses_sum(),
            13 => IbuSummary::bkselesai_siap_ctk_sum(),
            14 => IbuSummary::bkselesai_siap_ctk_diserahkan_sum(),
            15 => IbuSummary::bkselesai_siap_str_sum(),
            16 => IbuSummary::bkselesai_siap_str_diserahkan_sum(),
            17 => IbuSummary::bkstock_sum(),

            21 => DB::selectOne("SELECT pcs,gr,ttl_rp FROM opname_suntik WHERE id_opname_suntik = 15"),
            22 => IbuSummary::cetak_stok_awal(),
            // 23 => gudangcekModel::cetak_proses(),
            // 24 => gudangcekModel::cetak_selesai(),
            // 25 => gudangcekModel::tdk_cetak_selesai_diserahkan(),
            // 26 => gudangcekModel::cetak_selesai_diserahkan(),
            // 27 => gudangcekModel::cetak_stok(),
        ];
        return $datas[$index];
    }
    public function getSuntikan($index)
    {
        $datas = [
            11 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_cbt_awal'"),
            14  => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_siap_cetak_diserahkan'"),
            16  => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_eo_diserahkan'"),
            26 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_serah'"),
            21 => DB::selectOne("SELECT pcs,gr,ttl_rp FROM opname_suntik WHERE id_opname_suntik = 15"),
            22 => DB::selectOne("SELECT pcs,gr,ttl_rp FROM opname_suntik WHERE id_opname_suntik = 14"),
            27 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_sisa'"),
            31 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_stok_awal' and opname = 'Y'"),
            32 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_stok_awal' and opname = 'T'"),
            35 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_selesai_diserahkan'"),
            41 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'grading' and opname = 'Y'"),
            42 => DB::selectOne("SELECT sum(pcs) as pcs, sum(gr) as gr, sum(ttl_rp) as ttl_rp FROM `opname_suntik` WHERE ket ='grading' and opname = 'T';"),
        ];
        if (array_key_exists($index, $datas)) {
            return $datas[$index];
        } else {
            return false;
        }
    }

    public function detailSummaryIbu(Request $r)
    {
        $index = $r->index;
        $data = [
            'title' => 'Data Totalan',
            'index' => $index,
            'datas' => $this->getOpname($index),
            'suntikan' => $this->getSuntikan($index)

        ];

        return view('home.gudang.detail_summary_ibu', $data);
    }
}
