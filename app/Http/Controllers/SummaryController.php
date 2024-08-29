<?php

namespace App\Http\Controllers;

use App\Models\gudangcekModel;
use App\Models\SummaryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SummaryController extends Controller
{
    public function index(Request $r)
    {
        $bk = Http::get("https://gudangsarang.ptagafood.com/api/apibk/sum_partai");
        $bk = json_decode($bk, TRUE);
        DB::table('bk_awal')->truncate();
        foreach ($bk as $v) {
            $data = [
                'nm_partai' => $v['ket2'],
                'nm_partai_dulu' => $v['ket'],
                'pcs' => $v['pcs'] ?? 0,
                'gr' => $v['gr'],
                'grade' => $v['nm_grade'],
                'ttl_rp' => $v['total_rp'],
            ];
            DB::table('bk_awal')->insert($data);
        }
        $uang_cost = DB::select("SELECT a.* FROM oprasional as a");
        $data = [
            'title' => 'Data Gudang Awal',
            'bk' => SummaryModel::summarybk(),
            'bk_suntik' => DB::select("SELECT * FROM opname_suntik WHERE opname = 'Y'"),
            'uang_cost' => $uang_cost,
            'box_cabut_sedang_proses' => SummaryModel::bksedang_proses(),
            'box_cabut_belum_serah' => SummaryModel::bkselesai_siap_ctk(),
            'bkselesai_siap_str' => SummaryModel::bkselesai_siap_str(),
            'bk_sisa_pgws' => SummaryModel::bk_sisa_pgws(),
            'cetak_proses' => SummaryModel::cetak_proses(),
            'cetak_selesai_belum_serah' => SummaryModel::cetak_selesai_belum_serah(),
            'cetak_sisa_pgws' => SummaryModel::cetak_sisa_pgws(),

            'sortir_proses' => SummaryModel::sortir_proses(),
            'sortir_selesai' => SummaryModel::sortir_selesai(),
            'stock_sortir' => SummaryModel::stock_sortir(),
            'grading_stock' => SummaryModel::grading_stock(),

            'bkselesai_siap_ctk_diserahkan' => SummaryModel::bkselesai_siap_ctk_diserahkan(),
            'bkselesai_siap_str_diserahkan' => SummaryModel::bkselesai_siap_str_diserahkan(),
            'cetak_selesai_diserahkan' => SummaryModel::cetak_selesai_diserahkan(),
            'sortir_selesai_diserahkan' => SummaryModel::sortir_selesai_diserahkan(),
            'pengiriman' => DB::selectOne("SELECT sum(a.pcs) as pcs , sum(a.gr) as gr , sum(a.gr * a.rp_gram) as total_rp
                FROM pengiriman as a;"),

            // suntik
            'suntik_ctk_sisa' => SummaryModel::bk_suntik('cetak_sisa'),
            'suntik_grading' => SummaryModel::bk_suntik('grading'),
            'suntik_cetak_diserahkan' => SummaryModel::bk_suntik('cetak_serah'),
            'suntik_stock_siap_cetak_diserahkan' => SummaryModel::bk_suntik('stock_siap_cetak_diserahkan'),
            'suntik_stock_eo_diserahkan' => SummaryModel::bk_suntik('stock_eo_diserahkan'),
            'suntik_sortir_selesai_diserahkan' => SummaryModel::bk_suntik('sortir_selesai_diserahkan'),

            'cost_dll' => DB::selectOne("SELECT sum(`dll`) as dll FROM `tb_gaji_penutup`"),
            'cost_cu' => DB::selectOne("SELECT sum(a.ttl_rp) as cost_cu
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori ='CU' and a.bulan_dibayar BETWEEN '6' and '8';"),
            'denda' => DB::selectOne("SELECT sum(`nominal`) as ttl_denda FROM `tb_denda` WHERE `bulan_dibayar` BETWEEN '6' and '8';")
        ];

        return view('home.summary.index', $data);
    }

    public function detail_partai(Request $r)
    {
        $data = [
            'bk' => SummaryModel::summarybk(),
            'suntik_ctk_sisa' => DB::selectOne("SELECT sum(pcs) as pcs, sum(gr) as gr , sum(ttl_rp) as ttl_rp FROM opname_suntik WHERE opname = 'Y'")
        ];
        return view('home.summary.detail_partai', $data);
    }
    public function bk_sisa(Request $r)
    {
        $data = [
            'title' => 'Data Gudang Awal',
            'box_cabut_sedang_proses' => SummaryModel::bksedang_proses(),
            'box_cabut_belum_serah' => SummaryModel::bkselesai_siap_ctk(),
            'bkselesai_siap_str' => SummaryModel::bkselesai_siap_str(),
        ];

        return view('home.summary.bk_sisa', $data);
    }

    public function export_summary(Request $r)
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
        $style_opname = [
            'borders' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'f6a0e0',  // Warna background dalam format ARGB (contoh: kuning)
                ],
            ],

        ];
        $style_rp = [
            'borders' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFC107',  // Warna background dalam format ARGB (contoh: kuning)
                ],
            ],

        ];
        $style_rp_kirim = [
            'borders' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'CE37A8',  // Warna background dalam format ARGB (contoh: kuning)
                ],
            ],

        ];
        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary');

        $sheet->getStyle("A2:E6")->applyFromArray($style);
        $sheet->getStyle("A1:E1")->applyFromArray($style_atas);
        $sheet->setCellValue('A1', 'keterangan');
        $sheet->setCellValue('B1', 'bk herry');
        $sheet->setCellValue('C1', 'bk sinta');
        $sheet->setCellValue('D1', 'susut');
        $sheet->setCellValue('E1', 'cost kerja');
        $bk = SummaryModel::summarybk();
        $bk_suntik = DB::select("SELECT * FROM opname_suntik WHERE opname = 'Y'");

        $sheet->setCellValue('A2', 'pcs');
        $sheet->setCellValue('B2', sumBk($bk, 'pcs') + sumBk($bk_suntik, 'pcs'));
        $sheet->setCellValue('C2', sumBk($bk, 'pcs_bk') + sumBk($bk_suntik, 'pcs'));
        $sheet->setCellValue('D2', '');
        $sheet->setCellValue('E2', '');

        $bk_awal = sumBk($bk, 'gr') + sumBk($bk_suntik, 'gr');
        $bk_akhir = sumBk($bk, 'gr_bk') + sumBk($bk_suntik, 'gr');
        $ttl_rp = sumBk($bk, 'ttl_rp') + sumBk($bk_suntik, 'ttl_rp');

        $sheet->setCellValue('A3', 'gr');
        $sheet->setCellValue('B3', sumBk($bk, 'gr') + sumBk($bk_suntik, 'gr'));
        $sheet->setCellValue('C3', sumBk($bk, 'gr_bk') + sumBk($bk_suntik, 'gr'));
        $sheet->setCellValue('D3', round((1 - ($bk_akhir / $bk_awal)) * 100, 1) . '%');
        $sheet->setCellValue('E3', '');

        $sheet->setCellValue('A4', 'rp/gr');
        $sheet->setCellValue('B4', round($ttl_rp / $bk_awal, 1));
        $sheet->setCellValue('C4', round($ttl_rp / $bk_akhir, 1));
        $sheet->setCellValue('D4', '');
        $sheet->setCellValue('E4', '');

        $sheet->setCellValue('A5', 'total rp');
        $sheet->setCellValue('B5', $ttl_rp);
        $sheet->setCellValue('C5', $ttl_rp);
        $sheet->setCellValue('D5', '');
        $sheet->setCellValue('E5', 1815907127.33);

        $sheet->setCellValue('A6', 'total rp + cost');
        $sheet->setCellValue('B6', 0);
        $sheet->setCellValue('C6', 0);
        $sheet->setCellValue('D6', '');
        $sheet->setCellValue('E6', $ttl_rp + 1815907127.33);


        $sheet->getStyle("H1:I1")->applyFromArray($style_atas);
        $sheet->setCellValue('G1', 'cost kerja');
        $sheet->setCellValue('H1', 'bulan & tahun');
        $sheet->setCellValue('I1', 'total rp');

        $uang_cost = [
            'uang_cost' => ['juni 2024', 858415522.9],
            ['juli 2024', 957491604, 74]
        ];
        $kolom = 2;
        foreach ($uang_cost as $u) {
            $sheet->setCellValue('H' . $kolom, $u[0]);
            $sheet->setCellValue('I' . $kolom, $u[1]);
            $kolom++;
        }
        $sheet->getStyle("H1:I" . $kolom - 1)->applyFromArray($style);



        // batas
        $box_cabut_sedang_proses = SummaryModel::bksedang_proses();
        $box_cabut_belum_serah = SummaryModel::bkselesai_siap_ctk();
        $bkselesai_siap_str = SummaryModel::bkselesai_siap_str();
        $bk_sisa_pgws = SummaryModel::bk_sisa_pgws();
        $cetak_proses = SummaryModel::cetak_proses();
        $cetak_selesai_belum_serah = SummaryModel::cetak_selesai_belum_serah();
        $cetak_sisa_pgws = SummaryModel::cetak_sisa_pgws();
        $sortir_proses = SummaryModel::sortir_proses();
        $sortir_selesai = SummaryModel::sortir_selesai();
        $stock_sortir = SummaryModel::stock_sortir();
        $grading_stock = SummaryModel::grading_stock();
        $pengiriman = DB::selectOne("SELECT sum(a.pcs) as pcs , sum(a.gr) as gr , sum(a.gr * a.rp_gram) as total_rp
                FROM pengiriman as a;");

        $bkselesai_siap_ctk_diserahkan = SummaryModel::bkselesai_siap_ctk_diserahkan();
        $bkselesai_siap_str_diserahkan = SummaryModel::bkselesai_siap_str_diserahkan();
        $cetak_selesai_diserahkan = SummaryModel::cetak_selesai_diserahkan();
        $sortir_selesai_diserahkan = SummaryModel::sortir_selesai_diserahkan();

        // suntik
        $suntik_ctk_sisa = SummaryModel::bk_suntik('cetak_sisa');
        $suntik_grading = SummaryModel::bk_suntik('grading');
        $suntik_cetak_diserahkan = SummaryModel::bk_suntik('cetak_serah');
        $suntik_stock_siap_cetak_diserahkan = SummaryModel::bk_suntik('stock_siap_cetak_diserahkan');
        $suntik_stock_eo_diserahkan = SummaryModel::bk_suntik('stock_eo_diserahkan');
        $suntik_sortir_selesai_diserahkan = SummaryModel::bk_suntik('sortir_selesai_diserahkan');

        $cost_dll = DB::selectOne("SELECT sum(`dll`) as dll FROM `tb_gaji_penutup`");
        $cost_cu = DB::selectOne("SELECT sum(a.ttl_rp) as cost_cu
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori ='CU' and a.bulan_dibayar BETWEEN '6' and '8';");
        $denda = DB::selectOne("SELECT sum(`nominal`) as ttl_denda FROM `tb_denda` WHERE `bulan_dibayar` BETWEEN '6' and '8';");

        $gr_box_s_cetak_belum_serah = array_sum(array_column($box_cabut_belum_serah, 'gr'));
        $gr_box_s_cetak_diserahkan =
            array_sum(array_column($bkselesai_siap_ctk_diserahkan, 'gr')) +
            $suntik_stock_siap_cetak_diserahkan->gr;
        $gr_box_s_sortir_belum_serah = array_sum(array_column($bkselesai_siap_str, 'gr'));

        $gr_box_s_sortir_diserahkan =
            array_sum(array_column($bkselesai_siap_str_diserahkan, 'gr')) + $suntik_stock_eo_diserahkan->gr;

        $gr_cetak_selesai_b_serah = array_sum(array_column($cetak_selesai_belum_serah, 'gr'));
        $gr_cetak_selesai_diserahkan =
            array_sum(array_column($cetak_selesai_diserahkan, 'gr')) + $suntik_cetak_diserahkan->gr;
        $gr_sortir_s_g_belum_serah = array_sum(array_column($sortir_selesai, 'gr'));
        $gr_sortir_s_g_belum_diserahkan =
            array_sum(array_column($sortir_selesai_diserahkan, 'gr')) +
            $suntik_sortir_selesai_diserahkan->gr;
        $gr_tdk_cetak = array_sum(array_column($cetak_selesai_diserahkan, 'gr_tdk_ctk'));

        $operasional = 1815907127.33;
        $ttl_gr_operasional =
            $gr_box_s_cetak_belum_serah +
            $gr_box_s_cetak_diserahkan +
            $gr_box_s_sortir_belum_serah +
            $gr_box_s_sortir_diserahkan +
            $gr_cetak_selesai_b_serah +
            $gr_cetak_selesai_diserahkan +
            $gr_sortir_s_g_belum_serah +
            $gr_sortir_s_g_belum_diserahkan +
            $gr_tdk_cetak;

        $cs_box_s_cetak_belum_serah = array_sum(array_column($box_cabut_belum_serah, 'cost_kerja'));
        $cs_box_s_cetak_diserahkan = array_sum(array_column($bkselesai_siap_ctk_diserahkan, 'cost_kerja'));
        $cs_box_s_sortir_belum_serah = array_sum(array_column($bkselesai_siap_str, 'cost_kerja'));
        $cs_box_s_sortir_diserahkan = array_sum(array_column($bkselesai_siap_str_diserahkan, 'cost_kerja'));
        $cs_cetak_selesai_b_serah = array_sum(array_column($cetak_selesai_belum_serah, 'cost_kerja'));
        $cs_cetak_selesai_diserahkan = array_sum(array_column($cetak_selesai_diserahkan, 'cost_kerja'));
        $cs_sortir_s_g_belum_serah = array_sum(array_column($sortir_selesai, 'cost_kerja'));
        $cs_sortir_s_g_belum_diserahkan = array_sum(array_column($sortir_selesai_diserahkan, 'cost_kerja'));

        $ttl_cost_kerja =
            $cs_box_s_cetak_belum_serah +
            $cs_box_s_cetak_diserahkan +
            $cs_box_s_sortir_belum_serah +
            $cs_box_s_sortir_diserahkan +
            $cs_cetak_selesai_b_serah +
            $cs_cetak_selesai_diserahkan +
            $cs_sortir_s_g_belum_serah +
            $cs_sortir_s_g_belum_diserahkan;

        $cost_cu_dll = $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda;
        $cost_oprasional = $operasional - $ttl_cost_kerja - $cost_cu_dll;

        $rp_gr_cost_op = $cost_oprasional / $ttl_gr_operasional;
        $rp_gr_cu_dll = $cost_cu_dll / $ttl_gr_operasional;

        $ttlrp1 = sumBk($box_cabut_sedang_proses, 'ttl_rp');
        $ttlrp2 =
            sumBk($box_cabut_belum_serah, 'ttl_rp') +
            $rp_gr_cost_op * $gr_box_s_cetak_belum_serah +
            $rp_gr_cu_dll * $gr_box_s_cetak_belum_serah;
        $ttlrp3 =
            sumBk($bkselesai_siap_ctk_diserahkan, 'cost_kerja') +
            $rp_gr_cost_op * $gr_box_s_cetak_diserahkan +
            $rp_gr_cu_dll * $gr_box_s_cetak_diserahkan;
        $ttlrp4 =
            sumBk($bkselesai_siap_str, 'ttl_rp') +
            $rp_gr_cost_op * $gr_box_s_sortir_belum_serah +
            $rp_gr_cu_dll * $gr_box_s_sortir_belum_serah;
        $ttlrp5 =
            sumBk($bkselesai_siap_str_diserahkan, 'cost_kerja') +
            $rp_gr_cost_op * $gr_box_s_sortir_diserahkan +
            $rp_gr_cu_dll * $gr_box_s_sortir_diserahkan;
        $ttlrp6 = sumBk($bk_sisa_pgws, 'ttl_rp');
        $ttlrp7 = sumBk($cetak_proses, 'ttl_rp');
        $ttlrp8 =
            sumBk($cetak_selesai_belum_serah, 'ttl_rp') +
            $rp_gr_cost_op * $gr_cetak_selesai_b_serah +
            $rp_gr_cu_dll * $gr_cetak_selesai_b_serah;
        $ttlrp9 = $rp_gr_cost_op * $gr_tdk_cetak + $rp_gr_cu_dll * $gr_tdk_cetak;
        $ttlrp10 =
            sumBk($cetak_selesai_diserahkan, 'cost_kerja') +
            $rp_gr_cost_op * $gr_cetak_selesai_diserahkan +
            $rp_gr_cu_dll * $gr_cetak_selesai_diserahkan;
        $ttlrp11 = sumBk($cetak_sisa_pgws, 'ttl_rp') + $suntik_ctk_sisa->ttl_rp;
        $ttlrp12 = sumBk($sortir_proses, 'ttl_rp');
        $ttlrp13 =
            sumBk($sortir_selesai, 'ttl_rp') +
            $rp_gr_cost_op * $gr_sortir_s_g_belum_serah +
            $rp_gr_cu_dll * $gr_sortir_s_g_belum_serah;
        $ttlrp14 =
            sumBk($sortir_selesai_diserahkan, 'cost_kerja') +
            $rp_gr_cost_op * $gr_sortir_s_g_belum_diserahkan +
            $rp_gr_cu_dll * $gr_sortir_s_g_belum_diserahkan;
        $ttlrp15 = sumBk($stock_sortir, 'ttl_rp');
        $ttlrp16 = sumBk($grading_stock, 'ttl_rp') + $suntik_grading->ttl_rp;

        $ttl_rp = $ttlrp1 + $ttlrp2 + $ttlrp3 + $ttlrp4 + $ttlrp5 + $ttlrp6 + $ttlrp7 + $ttlrp8 + $ttlrp9 + $ttlrp10 + $ttlrp11 +
            $ttlrp12 + $ttlrp13 + $ttlrp14 + $ttlrp15 + $ttlrp16;


        $sheet->getStyle("K1:P1")->applyFromArray($style_atas);
        $sheet->getStyle("K2:P19")->applyFromArray($style);

        $sheet->getStyle("K2:K3")->applyFromArray($style_opname);
        $sheet->getStyle("K5")->applyFromArray($style_opname);
        $sheet->getStyle("K7:K9")->applyFromArray($style_opname);
        $sheet->getStyle("K12:K14")->applyFromArray($style_opname);
        $sheet->getStyle("K16:K17")->applyFromArray($style_opname);
        $sheet->getStyle("M19:N19")->applyFromArray($style_opname);
        $sheet->getStyle("P2:P19")->applyFromArray($style_rp);
        $sheet->getStyle("K18")->applyFromArray($style_rp_kirim);

        $sheet->setCellValue('K1', 'kategori');
        $sheet->setCellValue('L1', 'keterangan');
        $sheet->setCellValue('M1', 'pcs');
        $sheet->setCellValue('N1', 'gr');
        $sheet->setCellValue('O1', 'rp/gr');
        $sheet->setCellValue('P1', 'total rp');

        $sheet->setCellValue('K2', 'opname');
        $sheet->setCellValue('L2', 'box stock cabut sedang proses');
        $sheet->setCellValue('M2', sumBk($box_cabut_sedang_proses, 'pcs'));
        $sheet->setCellValue('N2', sumBk($box_cabut_sedang_proses, 'gr'));
        $sheet->setCellValue('O2', round(sumBk($box_cabut_sedang_proses, 'ttl_rp') / sumBk($box_cabut_sedang_proses, 'gr'), 0));
        $sheet->setCellValue('P2', sumBk($box_cabut_sedang_proses, 'ttl_rp'));

        $sheet->setCellValue('K3', 'opname');
        $sheet->setCellValue('L3', 'box selesai cabut siap cetak belum serah');
        $sheet->setCellValue('M3', sumBk($box_cabut_belum_serah, 'pcs'));
        $sheet->setCellValue('N3', sumBk($box_cabut_belum_serah, 'gr'));
        $sheet->setCellValue('O3', round(sumBk($box_cabut_belum_serah, 'ttl_rp') / sumBk($box_cabut_belum_serah, 'gr'), 0));
        $sheet->setCellValue('P3', sumBk($box_cabut_belum_serah, 'ttl_rp') + $rp_gr_cost_op * $gr_box_s_cetak_belum_serah +
            $rp_gr_cu_dll * $gr_box_s_cetak_belum_serah);

        $sheet->setCellValue('K4', 'proses');
        $sheet->setCellValue('L4', 'box selesai cabut siap cetak diserahkan');
        $sheet->setCellValue('M4', 0);
        $sheet->setCellValue('N4', sumBk($bkselesai_siap_ctk_diserahkan, 'gr') + $suntik_stock_siap_cetak_diserahkan->gr);
        $sheet->setCellValue('O4', 0);
        $sheet->setCellValue('P4', sumBk($bkselesai_siap_ctk_diserahkan, 'cost_kerja') +
            $rp_gr_cost_op * $gr_box_s_cetak_diserahkan +
            $rp_gr_cu_dll * $gr_box_s_cetak_diserahkan);

        $sheet->setCellValue('K5', 'opname');
        $sheet->setCellValue('L5', 'box selesai cbt siap sortir belum serah');
        $sheet->setCellValue('M5', 0);
        $sheet->setCellValue('N5', sumBk($bkselesai_siap_str, 'gr'));
        $sheet->setCellValue('O5', round(sumBk($bkselesai_siap_str, 'ttl_rp') / sumBk($bkselesai_siap_str, 'gr'), 0));
        $sheet->setCellValue('P5', sumBk($bkselesai_siap_str, 'ttl_rp') + $rp_gr_cost_op * $gr_box_s_sortir_belum_serah +
            $rp_gr_cu_dll * $gr_box_s_sortir_belum_serah);

        $sheet->setCellValue('K6', 'proses');
        $sheet->setCellValue('L6', 'box selesai cbt siap sortir diserahkan');
        $sheet->setCellValue('M6', 0);
        $sheet->setCellValue('N6', sumBk($bkselesai_siap_str_diserahkan, 'gr') + $suntik_stock_eo_diserahkan->gr);
        $sheet->setCellValue('O6', 0);
        $sheet->setCellValue('P6', sumBk($bkselesai_siap_str_diserahkan, 'cost_kerja') + $rp_gr_cost_op * $gr_box_s_sortir_diserahkan +
            $rp_gr_cu_dll * $gr_box_s_sortir_diserahkan);

        $sheet->setCellValue('K7', 'opname');
        $sheet->setCellValue('L7', 'box cbt sisa pgws');
        $sheet->setCellValue('M7', sumBk($bk_sisa_pgws, 'pcs'));
        $sheet->setCellValue('N7', sumBk($bk_sisa_pgws, 'gr'));
        $sheet->setCellValue('O7', round(sumBk($bk_sisa_pgws, 'ttl_rp') / sumBk($bk_sisa_pgws, 'gr'), 0));
        $sheet->setCellValue('P7', sumBk($bk_sisa_pgws, 'ttl_rp'));

        // cetak
        $sheet->setCellValue('K8', 'opname');
        $sheet->setCellValue('L8', 'cetak sedang proses');
        $sheet->setCellValue('M8', sumBk($cetak_proses, 'pcs'));
        $sheet->setCellValue('N8', sumBk($cetak_proses, 'gr'));
        $sheet->setCellValue('O8', empty(sumBk($cetak_proses, 'ttl_rp')) ? 0 : round(sumBk($cetak_proses, 'ttl_rp') / sumBk($cetak_proses, 'gr'), 0));
        $sheet->setCellValue('P8', sumBk($cetak_proses, 'ttl_rp'));

        $sheet->setCellValue('K9', 'opname');
        $sheet->setCellValue('L9', 'cetak selesai siap sortir belum serah');
        $sheet->setCellValue('M9', sumBk($cetak_selesai_belum_serah, 'pcs'));
        $sheet->setCellValue('N9', sumBk($cetak_selesai_belum_serah, 'gr'));
        $sheet->setCellValue('O9', empty(sumBk($cetak_proses, 'ttl_rp')) ? 0 : round(sumBk($cetak_selesai_belum_serah, 'ttl_rp') / sumBk($cetak_selesai_belum_serah, 'gr'), 0));
        $sheet->setCellValue('P9', sumBk($cetak_selesai_belum_serah, 'ttl_rp') + $rp_gr_cost_op * $gr_cetak_selesai_b_serah +
            $rp_gr_cu_dll * $gr_cetak_selesai_b_serah);

        $sheet->setCellValue('K10', 'proses');
        $sheet->setCellValue('L10', 'tidak cetak diserahkan');
        $sheet->setCellValue('M10', 0);
        $sheet->setCellValue('N10', sumBk($cetak_selesai_diserahkan, 'gr_tdk_ctk'));
        $sheet->setCellValue('O10', 0);
        $sheet->setCellValue('P10', $rp_gr_cost_op * $gr_tdk_cetak + $rp_gr_cu_dll * $gr_tdk_cetak);

        $sheet->setCellValue('K11', 'proses');
        $sheet->setCellValue('L11', 'cetak selesai siap sortir diserahkan');
        $sheet->setCellValue('M11', 0);
        $sheet->setCellValue('N11', sumBk($cetak_selesai_diserahkan, 'gr') + $suntik_cetak_diserahkan->gr);
        $sheet->setCellValue('O11', 0);
        $sheet->setCellValue('P11', sumBk($cetak_selesai_diserahkan, 'cost_kerja') + $rp_gr_cost_op * $gr_cetak_selesai_diserahkan +
            $rp_gr_cu_dll * $gr_cetak_selesai_diserahkan);

        $sheet->setCellValue('K12', 'opname');
        $sheet->setCellValue('L12', 'cetak sisa pgws');
        $sheet->setCellValue('M12', sumBk($cetak_sisa_pgws, 'pcs') + $suntik_ctk_sisa->pcs);
        $sheet->setCellValue('N12', sumBk($cetak_sisa_pgws, 'gr') + $suntik_ctk_sisa->gr);
        $sheet->setCellValue('O12', empty(sumBk($cetak_sisa_pgws, 'gr')) ? 0 : sumBk($cetak_sisa_pgws, 'ttl_rp') / sumBk($cetak_sisa_pgws, 'gr'));
        $sheet->setCellValue('P12', sumBk($cetak_sisa_pgws, 'ttl_rp') + $suntik_ctk_sisa->ttl_rp);

        $sheet->setCellValue('K13', 'opname');
        $sheet->setCellValue('L13', 'sortir sedang proses');
        $sheet->setCellValue('M13', sumBk($sortir_proses, 'pcs'));
        $sheet->setCellValue('N13', sumBk($sortir_proses, 'gr'));
        $sheet->setCellValue('O13', empty(sumBk($sortir_proses, 'gr')) ? 0 : sumBk($sortir_proses, 'ttl_rp') / sumBk($sortir_proses, 'gr'));
        $sheet->setCellValue('P13', sumBk($sortir_proses, 'ttl_rp'));

        $sheet->setCellValue('K14', 'opname');
        $sheet->setCellValue('L14', 'sortir selesai siap grading belum serah');
        $sheet->setCellValue('M14', sumBk($sortir_selesai, 'pcs'));
        $sheet->setCellValue('N14', sumBk($sortir_selesai, 'gr'));
        $sheet->setCellValue('O14', empty(sumBk($sortir_selesai, 'gr')) ? 0 : sumBk($sortir_selesai, 'ttl_rp') / sumBk($sortir_selesai, 'gr'));
        $sheet->setCellValue('P14', sumBk($sortir_selesai, 'ttl_rp') + $rp_gr_cost_op * $gr_sortir_s_g_belum_serah +
            $rp_gr_cu_dll * $gr_sortir_s_g_belum_serah);

        $sheet->setCellValue('K15', 'proses');
        $sheet->setCellValue('L15', 'sortir selesai siap grading diserahkan');
        $sheet->setCellValue('M15', 0);
        $sheet->setCellValue('N15', sumBk($sortir_selesai_diserahkan, 'gr') + $suntik_sortir_selesai_diserahkan->gr);
        $sheet->setCellValue('O15', 0);
        $sheet->setCellValue('P15', sumBk($sortir_selesai_diserahkan, 'cost_kerja') + $rp_gr_cost_op * $gr_sortir_s_g_belum_diserahkan +
            $rp_gr_cu_dll * $gr_sortir_s_g_belum_diserahkan);

        $sheet->setCellValue('K16', 'opname');
        $sheet->setCellValue('L16', 'sortir sisa pgws');
        $sheet->setCellValue('M16', sumBk($stock_sortir, 'pcs'));
        $sheet->setCellValue('N16', sumBk($stock_sortir, 'gr'));
        $sheet->setCellValue('O16', empty(sumBk($stock_sortir, 'gr')) ? 0 : sumBk($stock_sortir, 'ttl_rp') / sumBk($stock_sortir, 'gr'));
        $sheet->setCellValue('P16', sumBk($stock_sortir, 'ttl_rp'));

        $sheet->setCellValue('K17', 'opname');
        $sheet->setCellValue('L17', 'box belum kirim gudang wip');
        $sheet->setCellValue('M17', sumBk($grading_stock, 'pcs') + $suntik_grading->pcs - $pengiriman->pcs);
        $sheet->setCellValue('N17', sumBk($grading_stock, 'gr') + $suntik_grading->gr - $pengiriman->gr);
        $sheet->setCellValue('O17', empty(sumBk($grading_stock, 'gr')) ? 0 : (sumBk($grading_stock, 'ttl_rp') + $suntik_grading->ttl_rp - $pengiriman->total_rp) / (sumBk($grading_stock, 'gr') + $suntik_grading->gr - $pengiriman->gr));
        $sheet->setCellValue('P17', sumBk($grading_stock, 'ttl_rp') + $suntik_grading->ttl_rp - $pengiriman->total_rp);

        $sheet->setCellValue('K18', 'sudah kirim');
        $sheet->setCellValue('L18', 'box selesai kirim pengiriman');
        $sheet->setCellValue('M18', $pengiriman->pcs);
        $sheet->setCellValue('N18', $pengiriman->gr);
        $sheet->setCellValue('O18', $pengiriman->total_rp / $pengiriman->gr);
        $sheet->setCellValue('P18', $pengiriman->total_rp);

        $sheet->setCellValue('K19', 'Total');
        $sheet->setCellValue('L19', '');
        $sheet->setCellValue('M19', array_sum(array_column($box_cabut_sedang_proses, 'pcs')) + array_sum(array_column($box_cabut_belum_serah, 'pcs')) + array_sum(array_column($bk_sisa_pgws, 'pcs')) + array_sum(array_column($cetak_proses, 'pcs')) + array_sum(array_column($cetak_selesai_belum_serah, 'pcs')) + array_sum(array_column($cetak_sisa_pgws, 'pcs')) + array_sum(array_column($sortir_proses, 'pcs')) + array_sum(array_column($sortir_selesai, 'pcs')) + array_sum(array_column($stock_sortir, 'pcs')) + array_sum(array_column($grading_stock, 'pcs')) + $suntik_grading->pcs + $suntik_ctk_sisa->pcs);

        $sheet->setCellValue('N19', array_sum(array_column($box_cabut_sedang_proses, 'gr')) + array_sum(array_column($box_cabut_belum_serah, 'gr')) + array_sum(array_column($bk_sisa_pgws, 'gr')) + array_sum(array_column($cetak_proses, 'gr')) + array_sum(array_column($cetak_selesai_belum_serah, 'gr')) + array_sum(array_column($cetak_sisa_pgws, 'gr')) + array_sum(array_column($sortir_proses, 'gr')) + array_sum(array_column($sortir_selesai, 'gr')) + array_sum(array_column($stock_sortir, 'gr')) + array_sum(array_column($grading_stock, 'gr')) + array_sum(array_column($bkselesai_siap_str, 'gr')) + $suntik_grading->gr + $suntik_ctk_sisa->gr);
        $sheet->setCellValue('O19', 0);
        $sheet->setCellValue('P19', $ttl_rp);







        $namafile = "Summary Gudang.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }



    public function detail_box(Request $r)
    {
        $data = [
            'nm_partai' => $r->nm_partai,
            'bk' => DB::select("SELECT *
            FROM bk as a 
            left join users as b on b.id = penerima
            where a.nm_partai = '$r->nm_partai' and a.kategori ='cabut'
            "),
        ];
        return view('home.summary.detail_box', $data);
    }

    public function history_box(Request $r)
    {
        $data = [
            'bk' => DB::selectOne("SELECT a.nm_partai, a.pengawas, b.name,  sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal , sum(a.gr_awal * a.hrga_satuan) as ttl_rp
            FROM bk as a 
            left join users as b on b.id = penerima
            where a.no_box = '$r->no_box' and a.kategori ='cabut' and a.no_box in( SELECT b.no_box FROM cabut as b 
                UNION ALL 
                SELECT b.no_box FROM eo as b  )
             "),
            'no_box' => $r->no_box,
            'cabut' => DB::selectOne("SELECT a.*, b.name, c.nama, (d.hrga_satuan * d.gr_awal) as cost_bk
            FROM cabut as a 
            left join users as b on b.id = a.id_pengawas
            left join tb_anak as c on c.id_anak = a.id_anak
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            where a.no_box = '$r->no_box';"),

            'cetak' => DB::selectOne("SELECT a.no_box, a.pcs_awal_ctk, a.gr_awal_ctk, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_akhir, a.gr_akhir, 
            a.ttl_rp as cost_ctk, (b.hrga_satuan * b.gr_awal) as cost_bk, d.ttl_rp as cost_cbt, f.name as nm_serah, g.name as nm_terima, h.nama as nm_anak
            FROM cetak_new as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            left join kelas_cetak as  c on c.id_kelas_cetak = a.id_kelas_cetak
            left join cabut as d on d.no_box = a.no_box
            left join formulir_sarang as e on e.no_box = a.no_box and e.kategori = 'cetak'
            left join users as f on f.id = e.id_pemberi
            left join users as g on g.id = e.id_penerima
            left join tb_anak as h on h.id_anak = a.id_anak
            where c.kategori = 'CTK' and a.no_box = '$r->no_box';"),
            'sortir' => DB::selectOne("SELECT a.*, c.name as nm_serah, d.name as nm_terima, e.nama as nm_karyawan
            FROM sortir as a 
            left join formulir_sarang as b on b.no_box = a.no_box and b.kategori = 'sortir'
            left join users as c on c.id = b.id_pemberi
            left join users as d on d.id = b.id_penerima
            left join tb_anak as e on e.id_anak = a.id_anak
            where a.no_box = '$r->no_box';")

        ];
        return view('home.summary.history_box', $data);
    }

    public function history_partai(Request $r)
    {
        $data = [
            'nm_partai' => $r->nm_partai,
            'bk' => DB::selectOne("SELECT sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr , sum(a.gr_awal * a.hrga_satuan) as ttl_rp
            FROM bk as a 
            where a.nm_partai = '$r->nm_partai' and a.kategori ='cabut' and a.no_box in( SELECT b.no_box FROM cabut as b 
                UNION ALL 
                SELECT b.no_box FROM eo as b  )
             "),
            'bk_sisa' => DB::selectOne("SELECT sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr , sum(a.gr_awal * a.hrga_satuan) as ttl_rp
            FROM bk as a 
            where a.nm_partai = '$r->nm_partai' and a.kategori ='cabut' and a.no_box not in(SELECT b.no_box FROM cabut as b 
                UNION ALL 
                SELECT b.no_box FROM eo as b  )
             "),

            'cabut' => SummaryModel::cabut_history($r->nm_partai),

            'cabut_sisa' => SummaryModel::cabut_sisa_history($r->nm_partai),

            'cetak' => SummaryModel::cetak($r->nm_partai),
            'cbt_tanpa_pcs' => SummaryModel::cabut_history_lewat($r->nm_partai),

            'cetak_sisa' => DB::selectOne("SELECT b.nm_partai, 
            sum(a.pcs_awal_ctk) as pcs, 
            sum(a.gr_awal_ctk) as gr, 
            sum(a.pcs_akhir) as pcs_akhir, 
            sum(a.gr_akhir) as gr_akhir, 
            sum(a.ttl_rp) as cost_ctk,
            sum(b.gr_awal * b.hrga_satuan) as cost_bk,
            sum(d.gr_akhir) as gr_akhir_cbt,
            sum(d.ttl_rp) as cost_cbt
            FROM cetak_new as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas_cetak
            left join cabut as d on d.no_box = a.no_box
            where a.id_kelas_cetak = 0 and b.nm_partai = '$r->nm_partai' and a.no_box not in(SELECT b.no_box from formulir_sarang as b where b.kategori = 'sortir')
            GROUP by b.nm_partai;"),

            'sortir' => DB::selectOne("SELECT b.nm_partai, sum(a.pcs_awal) as pcs , sum(a.gr_awal) as gr, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(e.gr_akhir) as gr_akhir_ctk, sum(COALESCE(d.gr_akhir,0) + COALESCE(f.gr_eo_akhir,0)) as gr_akhir_cbt,
            sum(a.ttl_rp) as cost_sortir, 
            sum(b.gr_awal * b.hrga_satuan) as cost_bk, 
            sum(COALESCE(d.ttl_rp,0) + COALESCE(f.ttl_rp,0) ) as cost_cabut, sum(e.ttl_rp) as cost_ctk
        
            FROM sortir as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            left join cabut as d on d.no_box = a.no_box
            left join eo as f on f.no_box = a.no_box
            left join (
            	SELECT e.no_box, e.ttl_rp, (e.gr_akhir + e.gr_tdk_cetak) as gr_akhir
                FROM cetak_new as e 
                left join kelas_cetak as f on f.id_kelas_cetak = e.id_kelas_cetak
                where f.kategori = 'CTK'
            ) as e on e.no_box = a.no_box
            where  b.nm_partai = '$r->nm_partai' and a.selesai = 'Y' 
            GROUP by b.nm_partai;"),

            'sortir_sisa' => DB::selectOne("SELECT b.nm_partai, sum(a.pcs_awal) as pcs , sum(a.gr_awal) as gr, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(e.gr_akhir) as gr_akhir_ctk, sum(COALESCE(d.gr_akhir,0) + COALESCE(f.gr_eo_akhir,0)) as gr_akhir_cbt,
            sum(a.ttl_rp) as cost_sortir, 
            sum(b.gr_awal * b.hrga_satuan) as cost_bk, 
            sum(COALESCE(d.ttl_rp,0) + COALESCE(f.ttl_rp,0) ) as cost_cabut, sum(e.ttl_rp) as cost_ctk
            FROM sortir as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            left join cabut as d on d.no_box = a.no_box
            left join eo as f on f.no_box = a.no_box
            left join (
            	SELECT e.no_box, e.ttl_rp, (e.gr_akhir + e.gr_tdk_cetak) as gr_akhir
                FROM cetak_new as e 
                left join kelas_cetak as f on f.id_kelas_cetak = e.id_kelas_cetak
                where f.kategori = 'CTK'
            ) as e on e.no_box = a.no_box
            where  b.nm_partai = '$r->nm_partai' and a.selesai = 'T'  
            GROUP by b.nm_partai;"),

            'grading' => DB::selectOne("SELECT a.nm_partai, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(c.pcs) as pcs_akhir, sum(c.gr) as gr_akhir, a.cost_bk, c.tgl
                FROM (
                SELECT b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(b.hrga_satuan * b.gr_awal) as cost_bk
                FROM formulir_sarang as a
                    left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                    where b.baru = 'baru' and a.kategori = 'grade'
                    group by b.nm_partai
                ) as a
                left join (
                SELECT c.nm_partai, sum(c.pcs) as pcs, sum(c.gr) as gr, max(c.tgl) as tgl
                    FROM grading_partai as c 
                    group by c.nm_partai
                ) as c on c.nm_partai = a.nm_partai
                where a.nm_partai = '$r->nm_partai'
                group by a.nm_partai;")



        ];
        return view('home.summary.history_partai', $data);
    }
}
