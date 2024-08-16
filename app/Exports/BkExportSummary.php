<?php

namespace App\Exports;

use App\Models\SummaryModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BkExportSummary implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $uang_cost = [
            'uang_cost' => ['juni 2024', 858415522.9],
            ['juli 2024', 957491604, 74]
        ];
        return view('exports.bk_export_summary', [


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

        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Apply borders to all cells in the sheet
        $sheet->getStyle('A1:F100')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // You can add more styles as needed
        return $sheet;
    }
}
