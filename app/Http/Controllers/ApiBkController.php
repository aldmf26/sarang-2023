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

        $pcs_awal_cbt = 0;
        $pcs_akhir_cbt = 0;
        $gr_awal_cbt = 0;
        $gr_akhir_cbt = 0;
        $gr_flx_cbt = 0;
        $ttl_rp_cbt = 0;
        $ttl_rp_cbt_hilang = 0;
        $gr_awal_cbt_hilang = 0;
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

            $ttl_rp_cbt +=  $rupiah - $denda_hcr + $eot_bonus + $bonus_susut;
            $ttl_rp_cbt_hilang += $c->selesai == 'Y' ?  '0' : ($c->eot == 0 ? $c->rupiah : $rupiah - $denda_hcr + $eot_bonus + $bonus_susut);

            $pcs_awal_cbt += $c->pcs_awal;
            $pcs_akhir_cbt += $c->selesai == 'Y' ? $c->pcs_akhir : '0';

            $gr_awal_cbt +=  $c->gr_awal;
            $gr_awal_cbt_hilang += $c->selesai == 'Y' ? '0' :  $c->gr_awal;
            $gr_akhir_cbt += $c->selesai == 'Y' ?  $c->gr_akhir : '0';

            $gr_flx_cbt += $c->selesai == 'Y' ? $c->gr_flx : '0';
        }


        $cetak = ApiBkModel::datacetak($r->no_lot, $r->nm_partai);

        $pcs_awal_ctk = 0;
        $pcs_awal_ctk_dibawa = 0;
        $pcs_akhir_ctk = 0;
        $gr_awal_ctk = 0;
        $gr_akhir_ctk = 0;
        $gr_awal_ctk_dibawa = 0;
        $ttl_rp_all_ctk = 0;
        $ttl_rp_all_ctk_dibawa = 0;
        foreach ($cetak as $c) {
            $susut = empty($c->gr_akhir) ? '0' : (1 - ($c->gr_akhir + $c->gr_cu) / ($c->gr_awal_ctk)) * 100;
            $denda = round($susut, 0) >= $c->batas_susut ? round($susut) * $c->denda_susut : 0;
            $denda_hcr = $c->pcs_hcr * $c->denda_hcr;
            $ttl_rp = $c->pcs_akhir == '0' ? $c->pcs_awal_ctk * $c->rp_pcs : $c->pcs_akhir * $c->rp_pcs;

            $ttl_rp_all_ctk +=  $c->selesai == 'Y' ? '0' :  $ttl_rp - $denda - $denda_hcr;
            $ttl_rp_all_ctk_dibawa += $ttl_rp - $denda - $denda_hcr;
            $pcs_awal_ctk += $c->pcs_awal_ctk;
            $pcs_awal_ctk_dibawa +=  $c->selesai == 'Y' ? '0' : $c->pcs_awal_ctk - $c->pcs_akhir;
            $gr_awal_ctk_dibawa +=  $c->selesai == 'Y' ? '0' : $c->gr_awal_ctk - $c->gr_akhir + $c->gr_cu;
            $pcs_akhir_ctk +=  $c->selesai == 'T' ? '0' : $c->pcs_akhir + $c->pcs_cu;
            $gr_awal_ctk +=   $c->gr_awal_ctk;
            $gr_akhir_ctk += $c->gr_akhir + $c->gr_cu;
        }


        // $sortir = ApiBkModel::datasortir($r->no_lot, $r->nm_partai);

        // $pcs_awal_str = 0;
        // $pcs_akhir_str = 0;
        // $gr_akhir_str = 0;
        // $ttl_str = 0;
        // $ttl_str_dibawa = 0;
        // $gr_awal_str = 0;
        // foreach ($sortir as $s) {

        //     $pcs_awal_str += $s->pcs_awal;
        //     $pcs_akhir_str += $s->selesai == 'T' ? 0 : $s->pcs_akhir;
        //     $gr_awal_str +=  $s->gr_awal;
        //     $gr_akhir_str += $s->selesai == 'T' ? 0 : $s->gr_akhir;
        //     $ttl_str += $s->selesai == 'Y' ? '0' : $s->ttl_rp;
        //     $ttl_str_dibawa +=  $s->ttl_rp;
        // }


        $bk_cabut = ApiBkModel::bk_cabut_tes($r->no_lot, $r->nm_partai);


        $response = [
            'status' => 'success',
            'message' => 'Data Sarang berhasil diambil',
            'data' => [
                'bk_cabut' => $bk_cabut,
                'cabut' => [
                    'pcs_awal' => $pcs_awal_cbt,
                    'pcs_akhir' => $pcs_akhir_cbt,
                    'gr_awal' => $gr_awal_cbt,
                    'gr_awal_cbt_hilang' => $gr_awal_cbt_hilang,
                    'gr_akhir' => $gr_akhir_cbt + $gr_flx_cbt,
                    'susut' => $ttl_rp_cbt_hilang == '0' ? '0' : (1 - (($gr_akhir_cbt + $gr_flx_cbt) / $gr_awal_cbt)) * 100,
                    'rp_gram' => empty($gr_awal_cbt) ? '0' : $ttl_rp_cbt_hilang / $gr_awal_cbt,
                    'ttl_rp' =>  $ttl_rp_cbt_hilang,
                    'ttl_rp_dibawa' =>  $ttl_rp_cbt,
                ],
                'cetak' => [
                    'pcs_awal' => $pcs_awal_ctk,
                    'pcs_awal_ctk_dibawa' => $pcs_awal_ctk_dibawa,
                    'pcs_akhir' => $pcs_akhir_ctk,
                    'gr_awal' => $gr_awal_ctk,
                    'gr_akhir' => $gr_akhir_ctk,
                    'gr_awal_ctk_dibawa' => $gr_awal_ctk_dibawa,
                    'rp_c' => $ttl_rp_all_ctk,
                    'rp_c_dibawa' => $ttl_rp_all_ctk_dibawa,
                    'rp_gram' => empty($gr_akhir_ctk) ? '0' : ($ttl_rp_all_ctk) / $gr_akhir_ctk,
                    'susut' => $ttl_rp_all_ctk == 0 ? '0' : (empty($gr_akhir_ctk) ? '0' : (1 - ($gr_akhir_ctk / $gr_awal_ctk)) * 100),
                ],
            ],
        ];
        return response()->json($response);
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
}
