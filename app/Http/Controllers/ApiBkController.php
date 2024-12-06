<?php

namespace App\Http\Controllers;

use App\Models\ApiBkModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiBkController extends Controller
{
    public function sarang(Request $r)
    {
        $cabut = ApiBkModel::datacabut($r->no_lot, $r->nm_partai);
        // Tambahkan pengecekan apakah $cabut tidak kosong
        if (empty($cabut)) {
            $ttl_rp_cbt = 0;
            $pcs_awal_cbt = 0;
            $pcs_akhir_cbt = 0;
            $gr_awal_cbt =  0;
            $gr_akhir_cbt = 0;
            $gr_flx_cbt = 0;
        } else {
            $pcs_awal_cbt = 0;
            $gr_awal_cbt = 0;
            $pcs_akhir_cbt = 0;
            $gr_akhir_cbt = 0;
            $gr_flx_cbt = 0;
            $ttl_rp_cbt = 0;

            foreach ($cabut as $c) {
                $ttl_rp_cbt += $c->selesai == 'Y' ? $c->ttl_rp : $c->rupiah;
                $pcs_awal_cbt += $c->pcs_awal;
                $pcs_akhir_cbt += $c->selesai == 'Y' ? $c->pcs_akhir : '0';
                $gr_awal_cbt +=  $c->gr_awal;
                $gr_akhir_cbt += $c->selesai == 'Y' ?  $c->gr_akhir : '0';
                $gr_flx_cbt += $c->selesai == 'Y' ? $c->gr_flx : '0';
            }
        }



        $bk_cabut = ApiBkModel::bk_cabut_tes($r->no_lot, $r->nm_partai);

        // Perbarui respons untuk mencakup data BK cabut
        $response = [
            'bk_cabut' => $bk_cabut,
            'cabut' => [
                'pcs_awal' => $pcs_awal_cbt,
                'pcs_akhir' => $pcs_akhir_cbt,
                'gr_awal' => $gr_awal_cbt,
                'gr_akhir' => $gr_akhir_cbt + $gr_flx_cbt,
                'susut' => $gr_akhir_cbt == '0' ? '0' : (1 - (($gr_akhir_cbt + $gr_flx_cbt) / $gr_awal_cbt)) * 100,
                'ttl_rp' =>  $ttl_rp_cbt,
            ],
        ];

        return response()->json($response);
    }

    public function sarang_sum(Request $r)
    {
        $cabut = ApiBkModel::datacabutsum($r->nm_partai);

        $pcs_awal_cbt = 0;
        $pcs_akhir_cbt = 0;
        $gr_awal_cbt = 0;
        $gr_akhir_cbt = 0;
        $gr_flx_cbt = 0;
        $ttl_rp_cbt = 0;
        $ttl_rp_cbt_hilang = 0;
        $gr_awal_cbt_hilang = 0;
        $eot = 0;
        foreach ($cabut as $c) {
            $susut = empty($c->gr_awal) ? 0 : (1 - ($c->gr_flx + $c->gr_akhir) / $c->gr_awal) * 100;
            $batas_eot = empty($c->gr_awal) ? 0 : $c->gr_awal * $c->batas_eot;
            $bonus_susut = 0;
            $rupiah = $c->rupiah;
            if ($susut > $c->batas_susut) {
                $denda = ($susut - $c->batas_susut) * 0.03 * $c->rupiah;
                $rupiah = $rupiah - $denda;
            }
            if ($susut < $c->bonus_susut) {
                $bonus_susut = $c->rp_bonus != 0  ? ($c->rp_bonus * $c->gr_awal) / $c->gr_kelas : 0;
            }

            $denda_hcr = $c->pcs_hcr * $c->denda_hcr;
            $eot_bonus = ($c->eot - $c->gr_awal * $c->batas_eot) * $c->eot_rp;

            $ttl_rp_cbt +=  $c->rupiah - $denda_hcr + $eot_bonus + $bonus_susut;
            $ttl_rp_cbt_hilang +=  $c->selesai == 'T' ? $c->rupiah : $c->rupiah - $denda_hcr + $eot_bonus + $bonus_susut;

            $pcs_awal_cbt += $c->pcs_awal;
            $pcs_akhir_cbt += $c->selesai == 'Y' ? $c->pcs_akhir : '0';

            $gr_awal_cbt +=  $c->gr_awal;
            $gr_awal_cbt_hilang += $c->selesai == 'Y' ? '0' :  $c->gr_awal;
            $gr_akhir_cbt += $c->selesai == 'Y' ?  $c->gr_akhir : '0';

            $gr_flx_cbt += $c->selesai == 'Y' ? $c->gr_flx : '0';
            $eot += $c->selesai == 'Y' ? $c->eot : '0';
        }
        $response = [
            'pcs_awal' => $pcs_awal_cbt,
            'pcs_akhir' => $pcs_akhir_cbt,
            'gr_awal' => $gr_awal_cbt,
            'gr_awal_cbt_hilang' => $gr_awal_cbt_hilang,
            'gr_akhir' => $gr_akhir_cbt + $gr_flx_cbt,
            'gr_flx' => $gr_flx_cbt,
            'eot' => $eot,
            'susut' => $ttl_rp_cbt_hilang == '0' ? '0' : (1 - (($gr_akhir_cbt + $gr_flx_cbt) / $gr_awal_cbt)) * 100,
            'rp_gram' => empty($gr_awal_cbt) ? '0' : $ttl_rp_cbt_hilang / $gr_awal_cbt,
            'ttl_rp' =>  $ttl_rp_cbt_hilang,
            'ttl_rp_dibawa' =>  $ttl_rp_cbt,
        ];
        return response()->json($response);
    }
    public function datacabutsum2(Request $r)
    {
        $cabut = ApiBkModel::datacabutsum2($r->nm_partai);

        return response()->json($cabut);
    }
    public function datacabutsum3(Request $r)
    {
        $cabut = ApiBkModel::datacabutsum3($r->nm_partai);

        return response()->json($cabut);
    }
    public function datacabutsum2backup(Request $r)
    {
        $cabut = ApiBkModel::datacabutsum2backup($r->nm_partai, $r->tgl1, $r->tgl2);

        return response()->json($cabut);
    }
    public function datasortirsum(Request $r)
    {
        $sortir = ApiBkModel::data_sortir_sum($r->nm_partai);

        return response()->json($sortir);
    }
    public function datacetak(Request $r)
    {
        $cetak = ApiBkModel::cetak_sum_selesai($r->nm_partai);

        return response()->json($cetak);
    }

    function bk_sum(Request $r)
    {
        $cabut = ApiBkModel::bk_cabut_sum($r->nm_partai);

        return response()->json($cabut);
    }
    function bk_sum_sortir(Request $r)
    {
        $cabut = ApiBkModel::bk_sortir_sum($r->nm_partai, 'sortir');

        return response()->json($cabut);
    }
    function bk_sum_cetak(Request $r)
    {
        $cabut = ApiBkModel::bk_sortir_sum($r->nm_partai, 'cetak');

        return response()->json($cabut);
    }

    function export_sarang(Request $r)
    {

        $bk_cabut = ApiBkModel::export($r->no_lot, $r->nm_partai);
        $response = [
            'status' => 'success',
            'message' => 'Data Sarang berhasil diambil',
            'data' => [
                'bk_cabut' => $bk_cabut,
            ],
        ];
        return response()->json($response);
    }

    function cabut_export(Request $r)
    {
        $cabut = ApiBkModel::datacabut_export($r->no_box);

        $susut = empty($cabut->gr_awal) ? 0 : (1 - ($cabut->gr_flx + $cabut->gr_akhir) / $cabut->gr_awal) * 100;
        $batas_eot = empty($cabut->gr_awal) ? 0 : $cabut->gr_awal * $cabut->batas_eot;
        $bonus_susut = 0;
        $rupiah = $cabut->rupiah;
        if ($susut > $cabut->batas_susut) {
            $denda = ($susut - $cabut->batas_susut) * 0.03 * $cabut->rupiah;
            $rupiah = $rupiah - $denda;
        }
        if ($susut < $cabut->bonus_susut) {
            $bonus_susut = $cabut->rp_bonus != 0  ? ($cabut->rp_bonus * $cabut->gr_awal) / $cabut->gr_kelas : 0;
        }

        $denda_hcr = $cabut->pcs_hcr * $cabut->denda_hcr;
        $eot_bonus = ($cabut->eot - $cabut->gr_awal * $cabut->batas_eot) * $cabut->eot_rp;

        $ttl_rp_cbt =  $rupiah - $denda_hcr + $eot_bonus + $bonus_susut;
        $ttl_rp_cbt_hilang = $cabut->selesai == 'Y' ?  '0' : ($cabut->eot == 0 ? $cabut->rupiah : $rupiah - $denda_hcr + $eot_bonus + $bonus_susut);

        $pcs_awal_cbt = $cabut->pcs_awal;
        $pcs_akhir_cbt = $cabut->selesai == 'Y' ? $cabut->pcs_akhir : '0';

        $gr_awal_cbt =  $cabut->gr_awal;
        $gr_awal_cbt_hilang = $cabut->selesai == 'Y' ? '0' :  $cabut->gr_awal;
        $gr_akhir_cbt = $cabut->selesai == 'Y' ?  $cabut->gr_akhir : '0';

        $gr_flx_cbt = $cabut->selesai == 'Y' ? $cabut->gr_flx : '0';


        $cetak = ApiBkModel::datacetak_export($r->no_box);
        $susut = empty($cetak->gr_akhir) ? '0' : (1 - ($cetak->gr_akhir + $cetak->gr_cu) / ($cetak->gr_awal_ctk)) * 100;
        $denda = round($susut, 0) >= $cetak->batas_susut ? round($susut) * $cetak->denda_susut : 0;
        $denda_hcr = $cetak->pcs_hcr * $cetak->denda_hcr;
        $ttl_rp = $cetak->pcs_akhir == '0' ? $cetak->pcs_awal_ctk * $cetak->rp_pcs : $cetak->pcs_akhir * $cetak->rp_pcs;

        $ttl_rp_all_ctk =  $cetak->selesai == 'Y' ? '0' :  $ttl_rp - $denda - $denda_hcr;
        $ttl_rp_all_ctk_dibawa = $ttl_rp - $denda - $denda_hcr;
        $pcs_awal_ctk = $cetak->pcs_awal_ctk;
        $pcs_awal_ctk_dibawa =  $cetak->selesai == 'Y' ? '0' : $cetak->pcs_awal_ctk - $cetak->pcs_akhir;
        $gr_awal_ctk_dibawa =  $cetak->selesai == 'Y' ? '0' : $cetak->gr_awal_ctk - $cetak->gr_akhir + $cetak->gr_cu;
        $pcs_akhir_ctk =  $cetak->selesai == 'T' ? '0' : $cetak->pcs_akhir + $cetak->pcs_cu;
        $gr_awal_ctk =   $cetak->gr_awal_ctk;
        $gr_akhir_ctk = $cetak->gr_akhir + $cetak->gr_cu;

        $response = [
            'status' => 'success',
            'message' => 'Data Sarang berhasil diambil',
            'data' => [
                'cabut' => [
                    'pcs_awal' => $cabut->pcs_awal,
                    'gr_awal' => $cabut->gr_awal,
                    'pcs_akhir' => $cabut->pcs_akhir,
                    'gr_akhir' => $cabut->gr_akhir,
                    'rp_c' =>  $ttl_rp_cbt_hilang,
                    'rp_gram' => $ttl_rp_cbt_hilang == 0 ? 0 :  $ttl_rp_cbt / $cabut->gr_awal,
                    'susut' => $susut
                ],
                'cetak' => [
                    'pcs_awal' => $cetak->pcs_awal,
                    'gr_awal' => $cetak->gr_awal,
                    'pcs_akhir' => $cetak->pcs_akhir,
                    'gr_akhir' => $cetak->gr_akhir,
                    'rp_c' => $ttl_rp_all_ctk,
                    'rp_c_dibawa' => $ttl_rp_all_ctk_dibawa,
                    'rp_gram' => empty($gr_akhir_ctk) ? '0' : ($ttl_rp_all_ctk) / $gr_akhir_ctk,
                    'susut' => $ttl_rp_all_ctk == 0 ? '0' : (empty($gr_akhir_ctk) ? '0' : (1 - ($gr_akhir_ctk / $gr_awal_ctk)) * 100),
                ],
            ],
        ];
        return response()->json($response);
    }

    function show_box(Request $r)
    {
        $bk_cabut = ApiBkModel::bk_cabut_cabut($r->nm_partai, $r->limit);


        return response()->json($bk_cabut);
    }
    function show_box_sortir(Request $r)
    {
        $bk_cabut = ApiBkModel::bk_sortir_box($r->nm_partai, $r->limit);


        return response()->json($bk_cabut);
    }
    function cabut_perbox(Request $r)
    {
        $cabut = ApiBkModel::datacabutperbox($r->no_box);

        return response()->json($cabut);
    }

    function bk_sum_all(Request $r)
    {
        $cabut = ApiBkModel::bk_sortir_sum($r->nm_partai, $r->kategori);

        return response()->json($cabut);
    }
    function cabut_selesai(Request $r)
    {
        $cabut = ApiBkModel::cabut_selesai();

        return response()->json($cabut);
    }

    public function wipSortir()
    {
        $gradingbj = DB::select("SELECT 
        a.grade,
         sum(a.pcs) as pcs,
         sum(a.gr) as gr,
         sum(a.gr * a.rp_gram) as ttl_rp,
         sum(a.pcs_kredit) as pcs_kredit,
         sum(a.gr_kredit) as gr_kredit,
         sum(a.gr_kredit * a.rp_gram_kredit) as ttl_rp_kredit,

         b.pcs_bk,
         b.gr_bk,
         b.ttl_rp_bk,
         c.pcs_awal as pcs_awal,
         c.pcs_akhir as pcs_akhir,
         c.gr_awal as gr_awal,
         c.gr_akhir as gr_akhir,
         c.cost_sortir,
         d.pcs_sisa,
         d.gr_sisa,
         d.ttl_rp_sisa
        FROM `pengiriman_list_gradingbj` as a
        JOIN (
            SELECT grade, 
                sum(pcs_kredit) as pcs_bk, 
                sum(gr_kredit) as gr_bk,
                sum(gr_kredit * rp_gram_kredit) as ttl_rp_bk
            FROM pengiriman_list_gradingbj GROUP BY grade
        ) as b on a.grade = b.grade
        LEFT JOIN (
            SELECT 
            b.tipe,
            b.no_box,
            sum(a.pcs_awal) as pcs_awal,
            sum(a.pcs_akhir) as pcs_akhir,
            sum(a.gr_awal) as gr_awal,
            sum(a.gr_akhir) as gr_akhir ,
            sum(a.ttl_rp) as cost_sortir
            FROM `sortir` as a 
            JOIN bk as b on a.no_box = b.no_box
            WHERE a.selesai = 'Y' 
            GROUP BY b.tipe
        ) as c on c.tipe = a.grade
        left JOIN (
            SELECT grade, 
                sum(pcs_kredit) as pcs_sisa, 
                sum(gr_kredit) as gr_sisa,
                sum(gr_kredit * rp_gram_kredit) as ttl_rp_sisa
            FROM pengiriman_list_gradingbj WHERE pengawas = 0 GROUP BY grade
        ) as d on a.grade = d.grade
        GROUP BY grade;
        ");

        return response()->json($gradingbj);
    }

    public function cetak_detail(Request $r)
    {
        $cetak = ApiBkModel::cetak_detail($r->no_box);


        return response()->json($cetak);
    }
    public function cetak_detail_export(Request $r)
    {
        $cetak = DB::table('cetak')->get();
        return response()->json($cetak);
    }
    public function bikin_box(Request $r)
    {
        $cabut = DB::select("SELECT a.nm_partai, a.no_box, a.tipe, a.ket, a.warna, a.tgl, a.pengawas, d.name,
        a.pcs_awal, a.gr_awal, (a.pcs_awal - if(b.pcs_cabut is null ,0,b.pcs_cabut)) as pcs_sisa , (a.gr_awal - if(c.gr_eo is null ,0,c.gr_eo) - if(b.gr_cabut is null , 0, b.gr_cabut)) as gr_sisa
        FROM bk as a 
        left join (
         SELECT b.no_box, sum(b.pcs_awal) as pcs_cabut, sum(b.gr_awal) as gr_cabut
            FROM cabut as b 
            GROUP by b.no_box
        ) as b on b.no_box = a.no_box
        
        left join (
         SELECT c.no_box, sum(c.gr_eo_awal) as gr_eo
            FROM eo as c 
            GROUP by c.no_box
        ) as c on c.no_box = a.no_box
        
        left join users as d on d.id = a.penerima
        where a.kategori = 'cabut' and (a.gr_awal - if(c.gr_eo is null ,0,c.gr_eo) - if(b.gr_cabut is null , 0, b.gr_cabut)) != 0;
        ");
        return response()->json($cabut);
    }

    function cabut_selesai_new(Request $r)
    {
        $cabut = ApiBkModel::cabut_selesai_new();

        return response()->json($cabut);
    }
    function cabut_laporan(Request $r)
    {
        $cabut = ApiBkModel::cabut_laporan();

        return response()->json($cabut);
    }
    function cetak_laporan_all(Request $r)
    {
        $cabut = ApiBkModel::cetak_partai($r->nm_partai);

        return response()->json($cabut);
    }
    function cabut_detail(Request $r)
    {
        $cabut = ApiBkModel::cabut_detail($r->nm_partai, $r->limit);

        return response()->json($cabut);
    }
    function cabut_selesai_g_cetak(Request $r)
    {
        $cabut = ApiBkModel::cabut_selesai_g_cetak();

        return response()->json($cabut);
    }
    function cabut_selesai_g_cetak_nota(Request $r)
    {
        $cabut = ApiBkModel::cabut_selesai_g_cetak_nota($r->no_box);

        return response()->json($cabut);
    }
    function cetak_pgws(Request $r)
    {
        $cetak = ApiBkModel::cetak_pgws();

        return response()->json($cetak);
    }
    function cetak_belum_selesai(Request $r)
    {
        $cetak = ApiBkModel::cetak_belum_selesai();

        return response()->json($cetak);
    }
    function cetak_laporan(Request $r)
    {
        $cetak = ApiBkModel::cetak_laporan();

        return response()->json($cetak);
    }

    public function grading_bj()
    {
        $data = [
            'grading' => ApiBkModel::grading_bj(),
            'bk_sortir' => ApiBkModel::bk_sortir(),
        ];
        return response()->json($data);
    }
    public function sum_partai(Request $r)
    {
        $r = DB::select("SELECT a.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
        FROM bk as a 
        where a.kategori = 'cabut'
        GROUP by a.nm_partai;");

        return response()->json($r);
    }
}
