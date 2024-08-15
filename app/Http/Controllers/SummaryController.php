<?php

namespace App\Http\Controllers;

use App\Models\gudangcekModel;
use App\Models\SummaryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
        $uang_cost = [
            'uang_cost' => ['juni 2024', 858415522.9],
            ['juli 2024', 957491604, 74]
        ];

        $data_set = [
            'judul' => ['$box_cabut_sedang_proses'],
            ['$box_cabut_belum_serah'],
            ['$bkselesai_siap_str'],
            ['$bk_sisa_pgws'],
            ['$cetak_proses'],
            ['$cetak_selesai_belum_serah'],
            ['$cetak_sisa_pgws'],
            ['$cetak_sisa_pgws'],
            ['$sortir_proses'],
            ['$sortir_selesai'],
            ['$stock_sortir'],
            ['$grading_stock'],
        ];


        $data = [
            'title' => 'Data Gudang Awal',
            'data_set' => $data_set,
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
            'suntik_ctk_sisa' => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_sisa'"),
            'sortir_proses' => SummaryModel::sortir_proses(),
            'sortir_selesai' => SummaryModel::sortir_selesai(),
            'stock_sortir' => SummaryModel::stock_sortir(),
            'grading_stock' => SummaryModel::grading_stock(),
            'suntik_grading' => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'grading'"),


        ];

        return view('home.summary.index', $data);
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
}
