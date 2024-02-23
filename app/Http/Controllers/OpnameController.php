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
                'body' => 'listnya',
            ],
            [
                'no' => 6,
                'title' => 'bj ctk pgws',
                'body' => 'listnya',
            ],
            [
                'no' => 7,
                'title' => 'bj ctk proses',
                'body' => 'listnya',
            ],
            [
                'no' => 8,
                'title' => 'gdg ctk selesai',
                'body' => 'listnya',
            ],
            [
                'no' => 9,
                'title' => 'bj grading awal',
                'body' => 'listnya',
            ],
            [
                'no' => 10,
                'title' => 'box sp',
                'body' => 'listnya',
            ],
            [
                'no' => 11,
                'title' => 'bj sp proses',
                'body' => 'listnya',
            ],
            [
                'no' => 12,
                'title' => 'gdg sp selesai',
                'body' => 'listnya',
            ],
            [
                'no' => 13,
                'title' => 'bj siap kirim',
                'body' => 'listnya',
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
