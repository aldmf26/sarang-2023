<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use App\Models\CetakModel;
use App\Models\Grading;
use App\Models\Sortir;
use App\Models\TotalanModel;
use App\Models\TotalannewModel;
use App\Models\gudangcekModel;
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


    function export2(Request $r)
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

        $gudangbk = gudangcekModel::bkstockawal();


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

        $stock_cbt_awal = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_cbt_awal'");
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
        $gudangbkproses = gudangcekModel::bksedang_proses();
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
        $sheet1->setCellValue('AB1', 'Box Selesai siap ctk diserahkan');
        $sheet1->setCellValue('AC1', 'Pemilik');
        $sheet1->setCellValue('AD1', 'Partai');
        $sheet1->setCellValue('AE1', 'No Box');
        $sheet1->setCellValue('AF1', 'Pcs');
        $sheet1->setCellValue('AG1', 'Gr');
        $sheet1->setCellValue('AH1', 'Rp/gr');
        $sheet1->setCellValue('AI1', 'Total Rp');

        $bkselesai_siap_ctk_diserahkan = gudangcekModel::bkselesai_siap_ctk_diserahkan();

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
        $stock_siap_cetak_diserahkan = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_siap_cetak_diserahkan'");
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


        $bkselesai_siap_str = gudangcekModel::bkselesai_siap_str();

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

        $bkselesai_siap_str_diserahkan = gudangcekModel::bkselesai_siap_str_diserahkan();

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
        $stock_siap_sortir_diserahkan = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_eo_diserahkan'");
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

        $gudangbksisa = gudangcekModel::bkstock();


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
        $cetak_stok_awal = gudangcekModel::cetak_stok_awal();
        $kolom2 = 2;
        foreach ($cetak_stok_awal as $d) {
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
        $stock_cetak_awal = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_awal_stock'");
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
        $sheet2->setCellValue('S1', 'Cetak selesai siap sortir belum serah');
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


        $sheet2->getStyle("AD1:AJ1")->applyFromArray($style_atas);
        $sheet2->setCellValue('AC1', 'Cetak tidak cetak diserahkan');
        $sheet2->setCellValue('AD1', 'Pemilik');
        $sheet2->setCellValue('AE1', 'Partai');
        $sheet2->setCellValue('AF1', 'No Box');
        $sheet2->setCellValue('AG1', 'Pcs');
        $sheet2->setCellValue('AH1', 'Gr');
        $sheet2->setCellValue('AI1', 'Rp/gr');
        $sheet2->setCellValue('AJ1', 'Total Rp');

        $tdk_cetak_selesai_diserahkan = gudangcekModel::tdk_cetak_selesai_diserahkan();
        $kolom4 = 2;
        foreach ($tdk_cetak_selesai_diserahkan as $d) {
            $sheet2->setCellValue('AD' . $kolom4, $d->name);
            $sheet2->setCellValue('AE' . $kolom4, $d->nm_partai);
            $sheet2->setCellValue('AF' . $kolom4, $d->no_box);
            $sheet2->setCellValue('AG' . $kolom4, $d->pcs_tdk_ctk);
            $sheet2->setCellValue('AH' . $kolom4, $d->gr_tdk_ctk);
            $ttl_rpctk_selesai = $d->ttl_rp + $d->cost_op + $d->cost_cu;
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

        $cetak_selesai_diserahkan = gudangcekModel::cetak_selesai_diserahkan();
        $kolom4 = 2;
        foreach ($cetak_selesai_diserahkan as $d) {
            $sheet2->setCellValue('AM' . $kolom4, $d->name);
            $sheet2->setCellValue('AN' . $kolom4, $d->nm_partai);
            $sheet2->setCellValue('AO' . $kolom4, $d->no_box);
            $sheet2->setCellValue('AP' . $kolom4, $d->pcs);
            $sheet2->setCellValue('AQ' . $kolom4, $d->gr);
            $ttl_rpctk_selesai = $d->ttl_rp + $d->cost_op + $d->cost_cu;
            $sheet2->setCellValue('AR' . $kolom4, round($ttl_rpctk_selesai / $d->gr, 0));
            $sheet2->setCellValue('AS' . $kolom4, round($ttl_rpctk_selesai, 0));
            $kolom4++;
        }
        $suntik_ctk_diserahkan = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_serah'");
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
        $cetak_stock = gudangcekModel::cetak_stok();
        $kolom2 = 2;
        foreach ($cetak_stock as $d) {
            $sheet2->setCellValue('AV' . $kolom2, $d->name);
            $sheet2->setCellValue('AW' . $kolom2, $d->nm_partai);
            $sheet2->setCellValue('AX' . $kolom2, $d->no_box);
            $sheet2->setCellValue('AY' . $kolom2, $d->pcs_awal);
            $sheet2->setCellValue('AZ' . $kolom2, $d->gr_awal);
            $ttl_rp_ctstok = $d->ttl_rp + $d->cost_cbt + $d->cost_op + $d->cost_cu;
            $sheet2->setCellValue('BA' . $kolom2, round($ttl_rp_ctstok / $d->gr_awal, 0));
            $sheet2->setCellValue('BB' . $kolom2, round($ttl_rp_ctstok, 0));
            $kolom2++;
        }
        $suntik_ctk_sisa = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_sisa'");
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

        $sortir_stock = gudangcekModel::stock_sortir_awal();
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

        $sheet3->getStyle("T1:Z1")->applyFromArray($style_atas);
        $sheet3->setCellValue('S1', 'Sortir selesai siap grading belum serah');
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


        $sheet3->getStyle("AC1:AI1")->applyFromArray($style_atas);
        $sheet3->setCellValue('AB1', 'Sortir selesai siap grading diserahkan');
        $sheet3->setCellValue('AC1', 'Pemilik');
        $sheet3->setCellValue('AD1', 'Partai');
        $sheet3->setCellValue('AE1', 'No Box');
        $sheet3->setCellValue('AF1', 'Pcs');
        $sheet3->setCellValue('AG1', 'Gr');
        $sheet3->setCellValue('AH1', 'Rp/gr');
        $sheet3->setCellValue('AI1', 'Total Rp');

        $sortir_selesai_diserahkan = gudangcekModel::sortir_selesai_diserahkan();
        $kolom4 = 2;
        foreach ($sortir_selesai_diserahkan as $d) {
            $sheet3->setCellValue('AC' . $kolom4, $d->name);
            $sheet3->setCellValue('AD' . $kolom4, $d->nm_partai);
            $sheet3->setCellValue('AE' . $kolom4, $d->no_box);
            $sheet3->setCellValue('AF' . $kolom4, $d->pcs);
            $sheet3->setCellValue('AG' . $kolom4, $d->gr);
            $ttl_rp_str_selesai = $d->ttl_rp + $d->cost_op + $d->cost_cu;
            $sheet3->setCellValue('AH' . $kolom4, round($ttl_rp_str_selesai / $d->gr, 0));
            $sheet3->setCellValue('AI' . $kolom4, round($ttl_rp_str_selesai, 0));
            $kolom4++;
        }
        $suntik_str_stock_diserhkan = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_selesai_diserahkan'");
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

        $stock_sortir_sisa = gudangcekModel::stock_sortir();
        $kolom4 = 2;
        foreach ($stock_sortir_sisa as $d) {
            $sheet3->setCellValue('AL' . $kolom4, $d->name);
            $sheet3->setCellValue('AM' . $kolom4, $d->nm_partai);
            $sheet3->setCellValue('AN' . $kolom4, $d->no_box);
            $sheet3->setCellValue('AO' . $kolom4, $d->pcs);
            $sheet3->setCellValue('AP' . $kolom4, $d->gr);
            $ttl_rp_str_selesai = $d->ttl_rp + $d->cost_op + $d->cost_cu;
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
            $sheet5->setCellValue('O' . $kolom4, round($d->ttl_rp / $d->gr_grading, 0));
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
}
