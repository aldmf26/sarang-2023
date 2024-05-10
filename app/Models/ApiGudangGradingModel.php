<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ApiGudangGradingModel extends Model
{
    use HasFactory;
    public static function dataCetak()
    {
        $cetak = DB::table('cetak as a')
            ->selectRaw('b.tipe, a.id_cetak, a.no_box, SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, b.ttl_rp as total_rp, c.cost_cabut, ((a.pcs_akhir * a.rp_pcs) + a.rp_harian - (a.pcs_hcr * d.denda_hcr )) as cost_cetak')
            ->join('bk as b', function ($join) {
                $join->on('a.no_box', '=', 'b.no_box')
                    ->where('b.kategori', '=', 'cetak');
            })
            ->leftJoin(DB::raw('(SELECT c.no_box, SUM(c.ttl_rp) as cost_cabut FROM cabut as c GROUP BY c.no_box) as c'), 'c.no_box', '=', 'a.no_box')
            ->leftJoin('kelas_cetak as d', 'd.id_kelas_cetak', '=', 'a.id_kelas')
            ->leftJoin('pengiriman_gradingbj as p', 'a.no_box', '=', 'p.no_box')
            ->where('a.selesai', '=', 'Y')
            ->whereNull('p.no_box')
            ->groupBy('a.no_box')
            ->orderBy('b.tipe', 'ASC')
            ->get();
        return $cetak;
    }

    public static function cabutSelesai()
    {
        $tblBk = DB::table('pengiriman_gradingbj')->pluck('no_box')->toArray();
        $response = Http::get("https://gudangsarang.ptagafood.com/api/apibk/bkSortirApi");
        $data = json_decode($response->getBody());

        $data = array_filter($data, function ($item) use ($tblBk) {
            // Mengembalikan false jika no_box ada di dalam $tblBk 
            return !in_array($item->no_box, $tblBk);
        });

        return  array_values($data);
    }

    public static function suntikan()
    {
        $suntikan = DB::select("SELECT a.id_suntikan,a.tipe,a.no_box,a.pcs,a.gr,a.ttl_rp,a.cost_cabut,a.cost_cetak 
                    FROM grading_suntikan as a
                    WHERE NOT EXISTS (
                        SELECT 1 
                        FROM pengiriman_gradingbj AS b 
                        WHERE b.no_box = a.no_box 
                            AND b.pcs_awal = a.pcs 
                            AND b.gr_awal = a.gr
                                );");
        return $suntikan;
    }

    public static function grade_selesai()
    {
        $selesai = DB::select("SELECT a.tipe,a.no_box,a.partai,a.pcs_awal,a.gr_awal,a.ttl_rp,a.cost_cabut,a.cost_cetak FROM pengiriman_gradingbj as a");
        return $selesai;
    }

    public static function gudangBj()
    {
        return DB::select("SELECT grade, sum(pcs) as pcs, sum(gr) as gr, sum(gr * rp_gram) as ttl_rp, sum(pcs_kredit) as pcs_kredit, sum(gr_kredit) as gr_kredit, sum(gr_kredit * rp_gram_kredit) as ttl_rp_kredit
                FROM `pengiriman_list_gradingbj` 
                GROUP BY grade 
                HAVING pcs - pcs_kredit <> 0 OR gr - gr_kredit <> 0");
    }

    public static function historyBoxKecil()
    {
        $boxKecil = DB::select("SELECT 
                    a.no_box,
                    a.grade,
                    a.pcs_kredit as pcs,
                     a.gr_kredit as gr,
                    a.rp_gram_kredit as rp_gram,
                    a.pengawas,
                    a.no_grading,
                    b.pcs as pcs_sortir,
                    b.gr as gr_sortir,
                    b.ttl_rp as ttlrp_sortir, b.name
                    FROM `pengiriman_list_gradingbj` as a 
                    LEFT JOIN (
                        SELECT no_box,sum(pcs_akhir) as pcs, sum(gr_akhir) as gr, sum(ttl_rp) as ttl_rp, b.name
                        FROM `sortir` 
                        left join users as b on b.id = sortir.id_pengawas 
                        WHERE selesai = 'Y'
                        GROUP BY no_box
                    ) as b on a.no_box = b.no_box
                    WHERE a.no_box is not null ");
        return $boxKecil;
    }
}
