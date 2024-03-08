<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OpnameController extends Controller
{
    public function index(Request $r)
    {
        $cabut = [
            [
                'title' => 'BK'
            ],
            [
                'title' => 'BK CBT AWAL'
            ],
            [
                'title' => 'BK SISA SINTA'
            ],
            [
                'title' => 'BK CBT PGWS',
                'query' => $this->bkCbtPgws()
            ],
            [
                'title' => 'BK CBT SISA PGWS',
                'query' => $this->bkCbtPgws()
            ],
            [
                'title' => 'GDG CBT SELESAI',
                'query' => $this->bkCbtPgwsSelesai()
            ],
        ];
        $data = [
            'title' => 'Opname Semua Barang',
            'cabut' => $cabut
        ];
        return view('home.opname.index', $data);
    }
    
    public function detail(Request $r)
    {
        $bkCbt = $this->bkCbt();
        $bkCtk = $this->bkCtkDetail();
        $bjGradeAwal = $this->bjGradeAwalDetail();
        $boxSp = $this->boxSpDetail();
        $bjSpProses = $this->bjSpProsesDetail();
        $gdgSpSelesai = $this->gdgSpSelesaiDetail();
        $pengiriman = $this->pengirimanDetail();
        $box_kirim = $this->boxKirimDetail();
        $packingList = $this->packingListDetail();
        $boxBarcode = $this->boxBarcodeDetail();


        $arr = [
            [
                'title' => 'bk',
                'query' => $bkCbt
            ],
            [
                'title' => 'bk cbt awal',
                'query' => $bkCbt
            ],
            [
                'title' => 'bk sisa Sinta',
                'query' => $bkCbt
            ],
            [
                'title' => 'bk cbt pgws',
                'query' => $bkCbt
            ],
            [
                'title' => 'bk cbt sisa pgws',
                'query' => $bkCbt
            ],
            [
                'title' => 'gdg cbt selesai',
                'query' => $this->bkCbtSelesai()
            ]

        ];

        $no = $r->no;
        $title = $arr[$no - 1]['title'];
        $query = $arr[$no - 1]['query'];

        $data = [
            'no' => $no,
            'title' => $title,
            'query' => $query
        ];
        return view('home.opname.detail', $data);
    }

    public function bkCbtPgws()
    {
        return DB::select("SELECT a.tipe,
        a.no_box,
        sum(a.pcs_awal) as pcs_bk,
        sum(a.gr_awal) as gr_bk,
        sum(b.pcs) as pcs_awal,
        sum(b.gr) as gr_awal,
        sum(c.gr_eo) as gr_eoeo,
        sum(c.gr_eo_akhir) as gr_eoeo_akhir,
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
            SELECT a.no_box,sum(a.gr_eo_awal) as gr_eo, sum(a.gr_eo_akhir) as gr_eo_akhir 
            FROM eo  as a
            JOIN bk on bk.no_box = a.no_box
            GROUP BY a.no_box
        ) as c on a.no_box = c.no_box
        where a.kategori in ('cabut','eo')
        group by a.tipe");
    }
    public function bkCbtPgwsSelesai()
    {
        return DB::select("SELECT a.tipe,
        a.no_box,
        sum(a.pcs_awal) as pcs_bk,
        sum(a.gr_awal) as gr_bk,
        sum(b.pcs) as pcs_awal,
        sum(b.gr) as gr_awal,
        sum(c.gr_eo) as gr_eoeo,
        sum(c.gr_eo_akhir) as gr_eoeo_akhir,
        sum(b.ttl_rp) as ttl_rp,
        sum(c.eo_ttl_rp) as eo_ttl_rp,
         sum(b.pcs_akhir) as pcs_akhir,
         sum(b.gr_akhir) as gr_akhir
        FROM bk as a
        LEFT JOIN (
            SELECT a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr,sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir,sum(if(a.selesai = 'T', a.rupiah, a.ttl_rp)) as ttl_rp
            FROM cabut  as a
            left join bk as b on b.no_box = a.no_box and b.kategori in('cabut','eo')
            WHERE a.selesai = 'Y' GROUP BY a.no_box
        ) as b on a.no_box = b.no_box
        left JOIN (
            SELECT a.no_box,sum(a.gr_eo_awal) as gr_eo, sum(a.gr_eo_akhir) as gr_eo_akhir, sum(a.ttl_rp)  as eo_ttl_rp
            FROM eo  as a
            JOIN bk on bk.no_box = a.no_box
            WHERE a.selesai = 'Y' GROUP BY a.no_box
        ) as c on a.no_box = c.no_box
        where a.kategori in ('cabut','eo')
        group by a.tipe");
    }

    public function bkCtk()
    {
        return DB::selectOne("SELECT a.nm_partai,
        a.no_box,
        sum(a.pcs_awal) as pcs_bk,
        sum(a.gr_awal) as gr_bk,
        sum(a.ttl_rp) as ttl_rp,
        sum(b.pcs_awal) as pcs_awal,
        sum(b.gr_awal) as gr_awal,
        sum(b.pcs_akhir) as pcs_akhir,
        sum(b.gr_akhir) as gr_akhir,
        sum(c.pcs_akhir) as pcs_akhir_selesai,
        sum(c.gr_akhir) as gr_akhir_selesai,
        sum(c.ttl_rp_cost) as ttl_rp_cost
        FROM bk as a 
        LEFT JOIN (
           SELECT a.no_box, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir
           FROM cetak  as a
           left join bk as b on b.no_box = a.no_box and b.kategori = 'cetak'
           GROUP BY a.no_box
        ) as b on a.no_box = b.no_box
        LEFT JOIN (
           SELECT a.no_box, sum(a.rp_pcs * a.pcs_akhir) as ttl_rp_cost, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir
           FROM cetak  as a
           left join bk as b on b.no_box = a.no_box and b.kategori = 'cetak'
           WHERE a.selesai = 'Y'
           GROUP BY a.no_box
        ) as c on a.no_box = c.no_box
        where a.kategori = 'cetak';");
    }

    public function bjGradeAwal()
    {
        return DB::selectOne("SELECT grade,
          sum(pcs - pcs_kredit) as pcs,
          sum(gr - gr_kredit) as gr,
          (SUM(gr * rp_gram) - SUM(gr_kredit * rp_gram_kredit)) as ttl_rp
         FROM `pengiriman_list_gradingbj` WHERE no_box is null");
    }

    public function boxSp()
    {
        return DB::selectOne("SELECT sum(a.pcs_kredit) as pcs,
         sum(a.gr_kredit) as gr,
         SUM(a.gr_kredit * a.rp_gram_kredit) as ttl_rp
         FROM `pengiriman_list_gradingbj` as a
         WHERE pengawas != 0;");
    }

    public function bjSpProses()
    {
        return DB::selectOne("SELECT sum(a.pcs_awal - pcs_akhir) as pcs,sum(a.gr_awal - gr_akhir) as gr
         FROM `sortir` as a 
         JOIN bk as b on a.no_box = b.no_box AND b.kategori = 'sortir'
         WHERE a.selesai = 'T' AND a.no_box != 9999");
    }
    public function gdgSpSelesai()
    {
        return DB::selectOne("SELECT sum(pcs_akhir) as pcs,sum(gr_akhir) as gr,sum(a.ttl_rp) as ttl_rp
         FROM `sortir` as a 
         JOIN bk as b on a.no_box = b.no_box AND b.kategori = 'sortir'
         WHERE a.selesai = 'Y';");
    }

    public function pengiriman()
    {
        $pengiriman =  DB::select("SELECT a.grade, a.no_box, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.gr * a.rp_gram) as ttl_rp, if(c.pcs_ambil is null,0,c.pcs_ambil) as pcs_ambil, if(c.gr_ambil is null,0,c.gr_ambil) as gr_ambil, if(c.ttl_rp_ambil is null,0,c.ttl_rp_ambil) as ttl_rp_ambil
        FROM siapkirim_list_grading as a
        left join (
        SELECT c.grade, sum(c.pcs) as pcs_ambil, sum(c.gr) as gr_ambil, sum(c.gr * c.rp_gram) as ttl_rp_ambil
            FROM pengiriman as c 
            GROUP by c.grade
        ) as c on c.grade = a.grade
        GROUP by a.grade
        HAVING pcs - pcs_ambil <> 0 OR gr - gr_ambil <> 0
        ");

        $ttl_pcs = 0;
        $ttl_gr = 0;
        $ttl_rp = 0;

        foreach ($pengiriman as $p) {
            $ttl_pcs += $p->pcs - $p->pcs_ambil;
            $ttl_gr += $p->gr - $p->gr_ambil;
            $ttl_rp += $p->ttl_rp_ambil;
        }

        return [
            'ttl_pcs' => $ttl_pcs,
            'ttl_gr' => $ttl_gr,
            'ttl_rp' => $ttl_rp,
        ];
    }

    public function boxKirim($tgl1, $tgl2)
    {
        $box_kirim =  DB::select("SELECT a.* FROM `pengiriman`as a
            LEFT JOIN tb_grade as b on a.grade = b.nm_grade
            LEFT JOIN pengiriman_packing_list as c on a.no_nota_packing_list = c.no_nota
            WHERE a.tgl_pengiriman BETWEEN '$tgl1' and '$tgl2' AND a.no_nota_packing_list = ''
            ORDER BY b.urutan asc;");

        $ttl_pcs_siap = 0;
        $ttl_gr_siap = 0;
        $ttl_rp_siap = 0;
        foreach ($box_kirim as $b) {
            $ttl_pcs_siap += $b->pcs;
            $ttl_gr_siap += $b->gr;
            $ttl_rp_siap += $b->rp_gram * $b->gr_akhir;
        }

        return [
            'ttl_pcs_siap' => $ttl_pcs_siap,
            'ttl_gr_siap' => $ttl_gr_siap,
            'ttl_rp_siap' => $ttl_rp_siap,
        ];
    }

    public function packingList()
    {
        return DB::selectOne("SELECT sum(a.pcs_akhir) as pcs, sum(a.gr_akhir + a.gr_naik) as gr, sum(a.rp_gram * (a.gr_akhir + a.gr_naik)) as ttl_rp FROM `pengiriman` as a
        WHERE a.no_nota_packing_list is not null;");
    }

    public function boxBarcode()
    {
        return DB::selectOne("SELECT sum(a.pcs_akhir) as pcs, sum(a.gr_akhir + a.gr_naik) as gr, sum(a.rp_gram * (a.gr_akhir + a.gr_naik)) as ttl_rp FROM `pengiriman` as a
        WHERE a.no_box is not null;");
    }

    public function bkHerry()
    {
        $get = Http::get("https://gudangsarang.ptagafood.com/api/apibk/sumWip");
        return json_decode($get);
    }

    public function bjCtk()
    {
        return DB::selectOne("SELECT b.nm_partai, a.no_box, b.tipe, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, sum(a.ttl_rp) as ttl_rp
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        where a.selesai = 'Y'");
    }

    public function sumCtk()
    {
        $get = Http::get("https://gudangsarang.ptagafood.com/api/apibk/sumCtk");
        return json_decode($get);
    }


    public function index2(Request $r)
    {

        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $bkHerry = $this->bkHerry();
        $bkCbtPgws = $this->bkCbtPgws();
        $bkCtk = $this->bkCtk();
        $bjCtk = $this->bjCtk();
        $sumCtk = $this->sumCtk();
        $bjGradeAwal = $this->bjGradeAwal();
        $boxSp = $this->boxSp();
        $bjSpProses = $this->bjSpproses();
        $gdgSpSelesai = $this->gdgSpSelesai();
        $ttl_pcs = $this->pengiriman()['ttl_pcs'];
        $ttl_gr = $this->pengiriman()['ttl_gr'];
        $ttl_rp = $this->pengiriman()['ttl_rp'];

        $ttl_pcs_siap = $this->boxKirim($tgl1, $tgl2)['ttl_pcs_siap'];
        $ttl_gr_siap = $this->boxKirim($tgl1, $tgl2)['ttl_gr_siap'];
        $ttl_rp_siap = $this->boxKirim($tgl1, $tgl2)['ttl_rp_siap'];

        $boxBarcode = $this->boxBarcode();
        $packingList = $this->packingList();
        $hrgaModalSatuan = $bkHerry->harga_modal_satuan;
        $cards = [
            [
                'no' => 1,
                'title' => 'bk',
                'body' => [
                    'pcs' => $bkHerry->pcs,
                    'gr' => $bkHerry->gr,
                    'ttl_rp' => $bkHerry->total_rp,
                ],
            ],
            [
                'no' => 1,
                'title' => 'bk cbt awal',
                'body' => [
                    'pcs' => $bkCbtPgws->pcs_bk + $bkHerry->pcs_susut,
                    'gr' => $bkCbtPgws->gr_bk + $bkHerry->gr_susut,
                    'ttl_rp' => $bkCbtPgws->gr_bk * $hrgaModalSatuan,
                ],
            ],
            [
                'no' => 1,
                'title' => 'bk sisa sinta',
                'body' => [
                    'pcs' => $bkHerry->pcs - ($bkCbtPgws->pcs_bk + $bkHerry->pcs_susut),
                    'gr' => $bkHerry->gr - ($bkCbtPgws->gr_bk + $bkHerry->gr_susut),
                ],
            ],
            [
                'no' => 2,
                'title' => 'bk cbt pgws',
                'body' => [
                    'pcs' => $bkCbtPgws->pcs_awal,
                    'gr' => $bkCbtPgws->gr_awal + $bkCbtPgws->gr_eoeo,
                ],
            ],
            [
                'no' => 3,
                'title' => 'bk cbt sisa pgws',
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
                    'ttl_rp' => $bkCbtPgws->ttl_rp,
                ],
            ],
            [
                'no' => 5,
                'title' => 'bj ctk',
                'body' => [
                    // 'pcs' => $bjCtk->pcs_akhir + $sumCtk->pcs,
                    // 'gr' => $bjCtk->gr_akhir + $sumCtk->gr,
                    // 'ttl_rp' => $bjCtk->ttl_rp + $sumCtk->ttl_rp,
                    'pcs' => 9999,
                    'gr' => 9999,
                    'ttl_rp' => 9999,

                ],

            ],
            [
                'no' => 5,
                'title' => 'bj ctk awal',
                'body' => [
                    'pcs' => $bkCtk->pcs_bk,
                    'gr' => $bkCtk->gr_bk,
                    // 'ttl_rp' => $bkCtk->ttl_rp,

                ],

            ],
            [
                'no' => 5,
                'title' => 'bj sisa ctk',
                'body' => [
                    'pcs' => 9999,
                    'gr' => 9999,
                    // 'ttl_rp' => $bkCtk->ttl_rp,

                ],

            ],
            [
                'no' => 5,
                'title' => 'bj ctk pgws',
                'body' => [
                    'pcs' => $bkCtk->pcs_awal,
                    'gr' => $bkCtk->gr_awal,
                    'ttl_rp' => $bkCtk->ttl_rp,

                ],

            ],
            [
                'no' => 5,
                'title' => 'bj ctk sisa pgws',
                'body' => [
                    'pcs' => $bkCtk->pcs_awal - $bkCtk->pcs_akhir,
                    'gr' => $bkCtk->gr_awal - $bkCtk->gr_akhir,

                ],

            ],
            [
                'no' => 5,
                'title' => 'bj ctk selesai',
                'body' => [
                    'pcs' => $bkCtk->pcs_akhir_selesai,
                    'gr' => $bkCtk->gr_akhir_selesai,
                    'ttl_rp' => $bkCtk->ttl_rp_cost

                ],

            ],
            // [
            //     'no' => 6,
            //     'title' => 'bj ctk pgws',
            //     'body' => [
            //         'pcs' => $bkCtk->pcs_awal,
            //         'gr' => $bkCtk->gr_awal,
            //     ],
            // ],
            // [
            //     'no' => 7,
            //     'title' => 'bj ctk proses',
            //     'body' => [
            //         'pcs' => $bkCtk->pcs_awal - $bkCtk->pcs_akhir,
            //         'gr' => $bkCtk->gr_awal - $bkCtk->gr_akhir,
            //     ],
            // ],
            // [
            //     'no' => 8,
            //     'title' => 'gdg ctk selesai',
            //     'body' => [
            //         'pcs' => $bkCtk->pcs_akhir_selesai,
            //         'gr' => $bkCtk->gr_akhir_selesai,
            //     ],
            // ],
            // [
            //     'no' => 9,
            //     'title' => 'bj grading awal',
            //     'body' => [
            //         'pcs' => $bjGradeAwal->pcs,
            //         'gr' => $bjGradeAwal->gr,
            //         'ttl_rp' => $bjGradeAwal->ttl_rp,
            //     ],
            // ],
            // [
            //     'no' => 10,
            //     'title' => 'box sp',
            //     'body' => [
            //         'pcs' => $boxSp->pcs,
            //         'gr' => $boxSp->gr,
            //         'ttl_rp' => $boxSp->ttl_rp,
            //     ],
            // ],
            // [
            //     'no' => 11,
            //     'title' => 'bj sp proses',
            //     'body' => [
            //         'pcs' => $bjSpProses->pcs,
            //         'gr' => $bjSpProses->gr,
            //         'ttl_rp' => '00',
            //     ],
            // ],

            // [
            //     'no' => 12,
            //     'title' => 'gdg sp selesai',
            //     'body' => [
            //         'pcs' => $gdgSpSelesai->pcs,
            //         'gr' => $gdgSpSelesai->gr,
            //         'ttl_rp' => $gdgSpSelesai->ttl_rp,
            //     ],
            // ],
            // [
            //     'no' => 13,
            //     'title' => 'bj siap kirim',
            //     'body' => [
            //         'pcs' => $ttl_pcs,
            //         'gr' => $ttl_gr,
            //         'ttl_rp' => $ttl_rp,
            //     ],
            // ],
            // [
            //     'no' => 14,
            //     'title' => 'gdg siap kirim',
            //     'body' => [
            //         'pcs' => $ttl_pcs_siap,
            //         'gr' => $ttl_gr_siap,
            //         'ttl_rp' => $ttl_rp_siap,
            //     ],
            // ],

            // [
            //     'no' => 15,
            //     'title' => 'packing list',
            //     'body' => [
            //         'pcs' => $packingList->pcs,
            //         'gr' => $packingList->gr,
            //         'ttl_rp' => $packingList->ttl_rp,
            //     ],
            // ],
            // [
            //     'no' => 16,
            //     'title' => 'box barcode',
            //     'body' => [
            //         'pcs' => $boxBarcode->pcs,
            //         'gr' => $boxBarcode->gr,
            //         'ttl_rp' => $boxBarcode->ttl_rp,
            //     ],
            // ],
        ];
        $data = [
            'title' => 'Opname Semua Barang',
            'cards' => $cards
        ];
        return view('home.opname.index', $data);
    }

    public function bkCbt()
    {
        return DB::select("SELECT a.nm_partai,
        a.tipe,
        a.no_box,
        a.pcs_awal as pcs_bk,
        a.gr_awal as gr_bk,
        b.pcs as pcs_awal,
        COALESCE(b.gr, 0) + COALESCE(c.gr_eo, 0) as gr_awal,
        COALESCE(b.gr_akhir, 0) + COALESCE(c.gr_eo_akhir, 0) as gr_akhir,
        b.ttl_rp as ttl_rp,
         b.pcs_akhir as pcs_akhir
        FROM bk as a
        LEFT JOIN (
            SELECT a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr,sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir,sum(if(a.selesai = 'T', a.rupiah, a.ttl_rp)) as ttl_rp
            FROM cabut  as a
            left join bk as b on b.no_box = a.no_box and b.kategori in('cabut','eo')
            GROUP BY a.no_box
        ) as b on a.no_box = b.no_box
        left JOIN (
            SELECT a.no_box,sum(a.gr_eo_awal) as gr_eo, sum(a.gr_eo_akhir) as gr_eo_akhir 
            FROM eo  as a
            JOIN bk on bk.no_box = a.no_box
            GROUP BY a.no_box
        ) as c on a.no_box = c.no_box
        where a.kategori in ('cabut','eo');");
    }
    public function bkCbtSelesai()
    {
        return DB::select("SELECT a.nm_partai,
        a.tipe,
        a.no_box,
        a.pcs_awal as pcs_bk,
        a.gr_awal as gr_bk,
        b.pcs as pcs_awal,
        COALESCE(b.gr, 0) + COALESCE(c.gr_eo, 0) as gr_awal,
        COALESCE(b.gr_akhir, 0) + COALESCE(c.gr_eo_akhir, 0) as gr_akhir,
        COALESCE(b.ttl_rp, 0) + COALESCE(c.eo_ttl_rp, 0) as ttl_rp,
         b.pcs_akhir as pcs_akhir
        FROM bk as a
        LEFT JOIN (
            SELECT a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr,sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir,sum(if(a.selesai = 'T', a.rupiah, a.ttl_rp)) as ttl_rp
            FROM cabut  as a
            left join bk as b on b.no_box = a.no_box and b.kategori in('cabut','eo')
            WHERE a.selesai = 'Y' GROUP BY a.no_box
        ) as b on a.no_box = b.no_box
        left JOIN (
            SELECT a.no_box,sum(a.gr_eo_awal) as gr_eo, sum(a.gr_eo_akhir) as gr_eo_akhir,
            sum(a.ttl_rp) as eo_ttl_rp
            FROM eo  as a
            JOIN bk on bk.no_box = a.no_box
            WHERE a.selesai = 'Y' GROUP BY a.no_box
        ) as c on a.no_box = c.no_box
        where a.kategori in ('cabut','eo');");
    }

    public function bkCtkDetail()
    {
        return DB::select("SELECT a.nm_partai,
         a.tipe,
         a.nm_partai,
         a.no_box,
         a.pcs_awal as pcs_bk,
         a.gr_awal as gr_bk,
         b.pcs_awal as pcs_awal,
         b.gr_awal as gr_awal,
         b.pcs_akhir as pcs_akhir,
         b.gr_akhir as gr_akhir,
         c.pcs_akhir as pcs_akhir_selesai,
         c.gr_akhir as gr_akhir_selesai
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
    }

    public function bjGradeAwalDetail()
    {
        return DB::select("SELECT grade,
          pcs - pcs_kredit as pcs_awal,
          grade,
          gr - gr_kredit as gr_awal,
          (gr * rp_gram - gr_kredit * rp_gram_kredit) as ttl_rp
         FROM `pengiriman_list_gradingbj` WHERE no_box is null");
    }

    public function boxSpDetail()
    {
        return DB::select("SELECT b.no_box,b.nm_partai,b.tipe,a.pcs_kredit as pcs_awal,
        a.gr_kredit as gr_awal,
        (a.gr_kredit * a.rp_gram_kredit) as ttl_rp
        FROM `pengiriman_list_gradingbj` as a
        JOIN bk as b on a.no_box = b.no_box
        WHERE a.pengawas != 0;");
    }

    public function bjSpProsesDetail()
    {
        return DB::select("SELECT b.no_box,b.nm_partai,b.tipe,sum(a.pcs_awal - a.pcs_akhir) as pcs_awal,sum(a.gr_awal - a.gr_akhir) as gr_awal
         FROM `sortir` as a 
         JOIN bk as b on a.no_box = b.no_box AND b.kategori = 'sortir'
         WHERE a.selesai = 'T' AND a.no_box != 9999 group by b.no_box");
    }
    public function gdgSpSelesaiDetail()
    {
        return DB::select("SELECT b.nm_partai,b.no_box,b.tipe,sum(pcs_akhir) as pcs_awal,sum(gr_akhir) as gr_awal,sum(a.ttl_rp) as ttl_rp
         FROM `sortir` as a 
         JOIN bk as b on a.no_box = b.no_box AND b.kategori = 'sortir'
         WHERE a.selesai = 'Y' group by a.no_box");
    }

    public function pengirimanDetail()
    {
        return DB::select("SELECT a.grade, a.no_box, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.gr * a.rp_gram) as ttl_rp, if(c.pcs_ambil is null,0,c.pcs_ambil) as pcs_ambil, if(c.gr_ambil is null,0,c.gr_ambil) as gr_ambil, if(c.ttl_rp_ambil is null,0,c.ttl_rp_ambil) as ttl_rp_ambil
        FROM siapkirim_list_grading as a
        left join (
        SELECT c.grade, sum(c.pcs) as pcs_ambil, sum(c.gr) as gr_ambil, sum(c.gr * c.rp_gram) as ttl_rp_ambil
            FROM pengiriman as c 
            GROUP by c.grade
        ) as c on c.grade = a.grade
        GROUP by a.grade
        HAVING pcs - pcs_ambil <> 0 OR gr - gr_ambil <> 0
        ");
    }
    public function boxKirimDetail()
    {
        return DB::select("SELECT a.partai as nm_partai, a.tipe,a.no_box, a.pcs_akhir as pcs_awal, a.gr_akhir as gr_awal, (a.gr_akhir * a.rp_gram) as ttl_rp FROM `pengiriman`as a
        LEFT JOIN tb_grade as b on a.grade = b.nm_grade
        LEFT JOIN pengiriman_packing_list as c on a.no_nota_packing_list = c.no_nota
        WHERE a.no_nota_packing_list = ''
        ORDER BY b.urutan asc;");
    }

    public function packingListDetail()
    {
        return DB::select("SELECT a.partai as nm_partai,a.no_box,a.grade,a.pcs_akhir as pcs_awal, a.gr_akhir + a.gr_naik as gr_awal, (a.rp_gram * (a.gr_akhir + a.gr_naik)) as ttl_rp FROM `pengiriman` as a
        WHERE a.no_nota_packing_list is not null;");
    }
    public function boxBarcodeDetail()
    {
        return DB::select("SELECT a.partai as nm_partai,a.no_box,a.grade,a.pcs_akhir as pcs_awal, a.gr_akhir + a.gr_naik as gr_awal, (a.rp_gram * (a.gr_akhir + a.gr_naik)) as ttl_rp FROM `pengiriman` as a
        WHERE a.no_box is not null;");
    }



    public function detail2(Request $r)
    {
        $bkCbt = $this->bkCbt();
        $bkCtk = $this->bkCtkDetail();
        $bjGradeAwal = $this->bjGradeAwalDetail();
        $boxSp = $this->boxSpDetail();
        $bjSpProses = $this->bjSpProsesDetail();
        $gdgSpSelesai = $this->gdgSpSelesaiDetail();
        $pengiriman = $this->pengirimanDetail();
        $box_kirim = $this->boxKirimDetail();
        $packingList = $this->packingListDetail();
        $boxBarcode = $this->boxBarcodeDetail();


        $arr = [
            [
                'title' => 'bk',
                'query' => $bkCbt
            ],
            [
                'title' => 'bk cbt awal',
                'query' => $bkCbt
            ],
            [
                'title' => 'bk cbt pgws',
                'query' => $bkCbt
            ],
            [
                'title' => 'bk cbt sisa pgws',
                'query' => $bkCbt
            ],
            [
                'title' => 'gdg cbt selesai',
                'query' => $bkCbt
            ],
            [
                'title' => 'bj ctk awal',
                'query' => $bkCtk
            ],
            [
                'title' => 'bj ctk pgws',
                'query' => $bkCtk
            ],
            [
                'title' => 'bj ctk proses',
                'query' => $bkCtk
            ],
            [
                'title' => 'gdg ctk selesai',
                'query' => $bkCtk
            ],
            [
                'title' => 'bj grading awal',
                'query' => $bjGradeAwal
            ],
            [
                'title' => 'box sp',
                'query' => $boxSp
            ],
            [
                'title' => 'bj sp proses',
                'query' => $bjSpProses
            ],
            [
                'title' => 'gdg sp selesai',
                'query' => $gdgSpSelesai
            ],
            [
                'title' => 'bj siap kirim',
                'query' => $pengiriman
            ],
            [
                'title' => 'gdg siap kirim',
                'query' => $box_kirim
            ],
            [
                'title' => 'packing list',
                'query' => $packingList
            ],
            [
                'title' => 'box barcode',
                'query' => $boxBarcode
            ],
        ];

        $no = $r->no - 1;
        $title = $arr[$no]['title'];
        $query = $arr[$no]['query'];

        $data = [
            'no' => $no,
            'title' => $title,
            'query' => $query
        ];
        return view('home.opname.detail', $data);
    }

    public function blog()
    {
        $blog = DB::table('blog')->get();
        return response()->json($blog);
    }
    public function blog_detail($slug)
    {
        $blog = DB::table('blog')->where('judul', $slug)->first();
        return response()->json($blog);
    }
    public function blog_lainnya($slug)
    {
        // Ambil ID artikel saat ini
        $currentBlogId = DB::table('blog')->where('judul', $slug)->value('id');

        // Ambil ID semua artikel selain artikel saat ini
        $otherBlogIds = DB::table('blog')->where('id', '!=', $currentBlogId)->pluck('id')->toArray();

        // Acak urutan ID artikel selain artikel saat ini
        shuffle($otherBlogIds);

        // Ambil 3 ID pertama
        $randomBlogIds = array_slice($otherBlogIds, 0, 3);

        // Ambil data blog berdasarkan ID yang sudah diacak
        $blogs = DB::table('blog')->whereIn('id', $randomBlogIds)->get();

        return response()->json($blogs);
    }
}
