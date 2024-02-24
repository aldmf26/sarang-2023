<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpnameController extends Controller
{
    public function index()
    {

        $bkCbtPgws = DB::selectOne("SELECT a.nm_partai,
        a.no_box,
        sum(a.pcs_awal) as pcs_bk,
        sum(a.gr_awal) as gr_bk,
        sum(b.pcs) as pcs_awal,
        sum(b.gr) as gr_awal,
        c.gr_eo,
        sum(b.ttl_rp) as ttl_rp,
         sum(b.pcs_akhir) as pcs_akhir,
         sum(b.gr_akhir) as gr_akhir
        FROM bk as a
        LEFT JOIN (
            SELECT a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr,sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir,sum(if(a.selesai = 'T', a.rupiah, a.ttl_rp)) as ttl_rp
            FROM cabut  as a
            left join bk as b on b.no_box = a.no_box and b.kategori in('cabut','eo')
            GROUP BY a.no_box
        ) as b on a.no_box = b.no_box
        left JOIN (
            SELECT a.no_box,sum(a.gr_eo_awal) as gr_eo 
            FROM eo  as a
            JOIN bk on bk.no_box = a.no_box
            GROUP BY a.no_box
        ) as c on a.no_box = c.no_box
        where a.kategori in ('cabut','eo');");

        $bkCtk = DB::selectOne("SELECT a.nm_partai,
         a.no_box,
         sum(a.pcs_awal) as pcs_bk,
         sum(a.gr_awal) as gr_bk,
         sum(b.pcs_awal) as pcs_awal,
         sum(b.gr_awal) as gr_awal,
         sum(b.pcs_akhir) as pcs_akhir,
         sum(b.gr_akhir) as gr_akhir,
         sum(c.pcs_akhir) as pcs_akhir_selesai,
         sum(c.gr_akhir) as gr_akhir_selesai
         FROM bk as a 
         LEFT JOIN (
            SELECT a.no_box, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir
            FROM cetak  as a
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cetak'
            GROUP BY a.no_box
         ) as b on a.no_box = b.no_box
         LEFT JOIN (
            SELECT a.no_box, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir
            FROM cetak  as a
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cetak'
            WHERE a.selesai = 'Y'
            GROUP BY a.no_box
         ) as c on a.no_box = c.no_box
         where a.kategori = 'cetak';");

        $bjGradeAwal = DB::selectOne("SELECT grade,
          sum(pcs - pcs_kredit) as pcs,
          sum(gr - gr_kredit) as gr,
          (SUM(gr * rp_gram) - SUM(gr_kredit * rp_gram_kredit)) as ttl_rp
         FROM `pengiriman_list_gradingbj`");

        $boxSp = DB::selectOne("SELECT sum(a.pcs_kredit) as pcs,
         sum(a.gr_kredit) as gr,
         SUM(a.gr_kredit * a.rp_gram_kredit) as ttl_rp
         FROM `pengiriman_list_gradingbj` as a
         WHERE pengawas != 0;");

         $bjSpProses = DB::selectOne("SELECT sum(a.pcs_awal - pcs_akhir) as pcs,sum(a.gr_awal - gr_akhir) as gr
         FROM `sortir` as a 
         JOIN bk as b on a.no_box = b.no_box AND b.kategori = 'sortir';");

         $gdgSpSelesai = DB::selectOne("SELECT sum(pcs_akhir) as pcs,sum(gr_akhir) as gr,sum(a.ttl_rp) as ttl_rp
         FROM `sortir` as a 
         JOIN bk as b on a.no_box = b.no_box AND b.kategori = 'sortir'
         WHERE a.selesai = 'Y';");

        $bjSiapKirim = DB::selectOne("SELECT grade,
        sum(pcs - pcs_kredit) as pcs,
        sum(gr - gr_kredit) as gr,
        (SUM(gr * rp_gram) - SUM(gr_kredit * rp_gram_kredit)) as ttl_rp
        FROM `siapkirim_list_grading`");

        $cards = [
            [
                'no' => 1,
                'title' => 'bk cbt awal',
                'body' => [
                    'pcs' => $bkCbtPgws->pcs_bk,
                    'gr' => $bkCbtPgws->gr_bk,
                ],
            ],
            [
                'no' => 2,
                'title' => 'bk cbt pgws',
                'body' => [
                    'pcs' => $bkCbtPgws->pcs_awal,
                    'gr' => $bkCbtPgws->gr_awal,
                ],
            ],
            [
                'no' => 3,
                'title' => 'bk cbt proses',
                'body' => [
                    'pcs' => $bkCbtPgws->pcs_awal - $bkCbtPgws->pcs_akhir,
                    'gr' => $bkCbtPgws->gr_awal - $bkCbtPgws->gr_akhir,
                ],
            ],
            [
                'no' => 4,
                'title' => 'gdg cbt selesai',
                'body' => [
                    'pcs' => $bkCbtPgws->pcs_akhir,
                    'gr' => $bkCbtPgws->gr_akhir,
                ],
            ],
            [
                'no' => 5,
                'title' => 'bj ctk awal',
                'body' => [
                    'pcs' => $bkCtk->pcs_bk,
                    'gr' => $bkCtk->gr_bk,
                ],
            ],
            [
                'no' => 6,
                'title' => 'bj ctk pgws',
                'body' => [
                    'pcs' => $bkCtk->pcs_awal,
                    'gr' => $bkCtk->gr_awal,
                ],
            ],
            [
                'no' => 7,
                'title' => 'bj ctk proses',
                'body' => [
                    'pcs' => $bkCtk->pcs_awal - $bkCtk->pcs_akhir,
                    'gr' => $bkCtk->gr_awal - $bkCtk->gr_akhir,
                ],
            ],
            [
                'no' => 8,
                'title' => 'gdg ctk selesai',
                'body' => [
                    'pcs' => $bkCtk->pcs_akhir_selesai,
                    'gr' => $bkCtk->gr_akhir_selesai,
                ],
            ],
            [
                'no' => 9,
                'title' => 'bj grading awal',
                'body' => [
                    'pcs' => $bjGradeAwal->pcs,
                    'gr' => $bjGradeAwal->gr,
                    'ttl_rp' => $bjGradeAwal->ttl_rp,
                ],
            ],
            [
                'no' => 10,
                'title' => 'box sp',
                'body' => [
                    'pcs' => $boxSp->pcs,
                    'gr' => $boxSp->gr,
                    'ttl_rp' => $boxSp->ttl_rp,
                ],
            ],
            [
                'no' => 11,
                'title' => 'bj sp proses',
                'body' => [
                    'pcs' => $bjSpProses->pcs,
                    'gr' => $bjSpProses->gr,
                    'ttl_rp' => '00',
                ],
            ],
            
            [
                'no' => 12,
                'title' => 'gdg sp selesai',
                'body' => [
                    'pcs' => $gdgSpSelesai->pcs,
                    'gr' => $gdgSpSelesai->gr,
                    'ttl_rp' => $gdgSpSelesai->ttl_rp,
                ],
            ],
            [
                'no' => 13,
                'title' => 'bj siap kirim',
                'body' => [
                    'pcs' => $bjSiapKirim->pcs,
                    'gr' => $bjSiapKirim->gr,
                    'ttl_rp' => $bjSiapKirim->ttl_rp,
                ],
            ],
            [
                'no' => 14,
                'title' => 'gdg siap kirim',
                'body' => 'listnya',
            ],
            [
                'no' => 15,
                'title' => 'packing list',
                'body' => 'listnya',
            ],
            [
                'no' => 16,
                'title' => 'box barcode',
                'body' => 'listnya',
            ],
        ];
        $data = [
            'title' => 'Opname Semua Barang',
            'cards' => $cards
        ];
        return view('home.opname.index', $data);
    }
}
