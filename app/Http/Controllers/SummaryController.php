<?php

namespace App\Http\Controllers;

use App\Models\BalanceModel;
use App\Models\gudangcekModel;
use App\Models\SummaryModel;
use App\Models\TotalanModel;
use App\Models\TotalannewModel;
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
            where b.kategori ='CU' and a.bulan_dibayar BETWEEN '6' and '9';"),
            'denda' => DB::selectOne("SELECT sum(`nominal`) as ttl_denda FROM `tb_denda` WHERE `bulan_dibayar` BETWEEN '6' and '8';")
        ];

        return view('home.summary.index', $data);
    }


    public function get_operasional(Request $r)
    {
        if (empty($r->id_oprasional)) {
            $bulan = DB::selectOne("SELECT max(a.bulan_dibayar) as bulan , max(a.tahun_dibayar) as tahun
        FROM tb_gaji_penutup as a;");
        } else {
            $bulan = DB::table('oprasional')->where('id_oprasional', $r->id_oprasional)->first();
        }

        $bulan_array = DB::table('oprasional')->get();
        $data = [
            'total' => DB::selectOne("SELECT sum(a.cbt_gr_akhir) as gr_cabut, sum(a.eo_gr_akhir) as gr_eo, sum(a.ctk_gr_akhir) as gr_ctk, sum(a.srt_gr_akhir) as gr_sortir, sum(COALESCE(a.cbt_ttlrp,0) + COALESCE(a.eo_ttlrp,0) + COALESCE(a.ctk_ttl_rp,0) + COALESCE(a.srt_ttlrp,0)) as ttl_gaji
            FROM tb_gaji_penutup as a 
            where a.bulan_dibayar = '$bulan->bulan' and a.tahun_dibayar  = '$bulan->tahun';"),
            'cost_cbt' => BalanceModel::cost_cbt_eo($bulan->bulan, $bulan->tahun),
            'cost_ctk' => BalanceModel::cost_ctk($bulan->bulan, $bulan->tahun),
            'cost_str' => BalanceModel::cost_sortir($bulan->bulan, $bulan->tahun),
            'bulan' => $bulan->bulan,
            'tahun' => $bulan->tahun,
            'cost_oprasional' => DB::selectOne("SELECT sum(a.rp_oprasional) as rp_oprasional, sum(a.total_operasional) as ttl_rp FROM oprasional as a where a.bulan = '$bulan->bulan' and a.tahun = '$bulan->tahun';"),
            'dataBulan' => $bulan_array,
            'id_oprasional' => $r->id_oprasional
        ];

        return view('home.summary.operasional', $data);
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
        $uang_cost = DB::select("SELECT a.* FROM oprasional as a");


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
        $sheet->setCellValue('E5', sumBk($uang_cost, 'total_oprasional'));

        $sheet->setCellValue('A6', 'total rp + cost');
        $sheet->setCellValue('B6', 0);
        $sheet->setCellValue('C6', 0);
        $sheet->setCellValue('D6', '');
        $sheet->setCellValue('E6', $ttl_rp + sumBk($uang_cost, 'total_oprasional'));


        $sheet->getStyle("H1:I1")->applyFromArray($style_atas);
        $sheet->setCellValue('G1', 'cost kerja');
        $sheet->setCellValue('H1', 'bulan & tahun');
        $sheet->setCellValue('I1', 'total rp');


        $kolom = 2;
        foreach ($uang_cost as $u) {
            $sheet->setCellValue('H' . $kolom, date('F Y', strtotime($u->tahun . '-' . $u->bulan . '-01')));
            $sheet->setCellValue('I' . $kolom, $u->total_operasional);
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

        $operasional = sumBk($uang_cost, 'total_operasional');
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
        $sheet->setCellValue('O3', empty(sumBk($box_cabut_belum_serah, 'ttl_rp')) ? 0 : round(sumBk($box_cabut_belum_serah, 'ttl_rp') / sumBk($box_cabut_belum_serah, 'gr'), 0));
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
        $sheet->setCellValue('O9', empty(sumBk($cetak_selesai_belum_serah, 'ttl_rp')) ? 0 : round(sumBk($cetak_selesai_belum_serah, 'ttl_rp') / sumBk($cetak_selesai_belum_serah, 'gr'), 0));
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
            where  b.nm_partai = '$r->nm_partai' and a.selesai = 'Y' and a.no_box  in(SELECT b.no_box from formulir_sarang as b where b.kategori = 'grade')
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
            where  b.nm_partai = '$r->nm_partai' and a.no_box not in(SELECT b.no_box from formulir_sarang as b where b.kategori = 'grade')    
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

    function export2(gudangcekModel $model)
    {
        $style_atas = array(
            'font' => [
                'bold' => true,
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
        $sheet6 = $spreadsheet->getActiveSheet();

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
        $spreadsheet->setActiveSheetIndex(1);
        $sheet7 = $spreadsheet->getActiveSheet(1);
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


    public function saveoprasional(Request $r)
    {

        $bulan = $r->bulan;
        $grading_partai = DB::select("SELECT * FROM grading_partai as a where   a.bulan ='$bulan' ");


        $ttl_gr = sumBk($grading_partai, 'gr');


        $formattedNumber = $r->biaya_oprasional;
        // Hapus pemisah ribuan untuk mendapatkan angka mentah
        $rawNumber = str_replace(',', '', $formattedNumber);

        // Validasi angka mentah
        if (!is_numeric($rawNumber)) {
            return redirect()->back()->withErrors(['biaya_oprasional' => 'The number is not valid.']);
        }
        DB::table('oprasional')->where('bulan', $r->bulan)->where('tahun', $r->tahun)->delete();

        $rp_gr = ($rawNumber - $r->gaji) / $ttl_gr;

        $rp_oprasional = $rawNumber - $r->gaji;
        $data = [
            'rp_oprasional' => $rp_oprasional,
            'bulan' => $r->bulan,
            'tahun' => $r->tahun,
            'rp_gr' => $rawNumber / $r->gr_akhir,
            'gr' => $r->gr_akhir,
            'total_operasional' => $rawNumber
        ];
        DB::table('oprasional')->insert($data);

        foreach ($grading_partai as $p) {
            $data = [
                'cost_op' => $p->gr * $rp_gr
            ];
            DB::table('grading_partai')->where('id_grading', $p->id_grading)->update($data);
        }
        return redirect()->back()->with('sukses', 'Data Berhasil ditambahkan');
    }
}
