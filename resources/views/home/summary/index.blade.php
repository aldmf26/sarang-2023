<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">
            {{-- @include('home.summary.nav') --}}
            <div class="col-lg-6">
                <h6 class="float-start mt-1">Summary</h6>
            </div>

            <style>
                .clickable-row:hover {
                    cursor: pointer;
                    background-color: #f5f5f5;
                }
            </style>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-10"></div>
            <div class="col-lg-2">
                <a href="{{ route('summary.export_summary') }}" class="btn btn-primary float-end"><i
                        class="fas fa-file-excel"></i> export</a>
            </div>
            <div class="col-lg-6 mt-2">
                <table width="100%" class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">Ket</th>
                            <th class="dhead text-end">Bk herry</th>
                            <th class="dhead text-end">Bk sinta</th>
                            <th class="dhead text-end">Sst</th>
                            <th class="dhead text-end">Cost kerja</th>
                        </tr>
                    </thead>
                    <tbody class="clickable-row open-modal" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <tr>
                            <td>pcs</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk, 'pcs')) + array_sum(array_column($bk_suntik, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk, 'pcs_bk')) + array_sum(array_column($bk_suntik, 'pcs')), 0) }}
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>gr</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk, 'gr')) + array_sum(array_column($bk_suntik, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk, 'gr_bk')) + array_sum(array_column($bk_suntik, 'gr')), 0) }}
                            </td>
                            @php
                                $bk_awal =
                                    array_sum(array_column($bk, 'gr')) + array_sum(array_column($bk_suntik, 'gr'));
                                $bk_akhir =
                                    array_sum(array_column($bk, 'gr_bk')) + array_sum(array_column($bk_suntik, 'gr'));
                                $ttl_rp =
                                    array_sum(array_column($bk, 'ttl_rp')) +
                                    array_sum(array_column($bk_suntik, 'ttl_rp'));
                            @endphp
                            <td class="text-end">{{ number_format((1 - $bk_akhir / $bk_awal) * 100, 1) }} %</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>rp/gr</td>
                            <td class="text-end">{{ number_format($ttl_rp / $bk_awal, 0) }}</td>
                            <td class="text-end">{{ number_format($ttl_rp / $bk_akhir, 0) }}</td>
                            <td class="text-end">

                            </td>

                            <td></td>
                        </tr>
                        <tr>
                            <td>Total Rp</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk, 'ttl_rp')) + array_sum(array_column($bk_suntik, 'ttl_rp')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk, 'ttl_rp')) + array_sum(array_column($bk_suntik, 'ttl_rp')), 0) }}
                            </td>
                            <td></td>
                            <td class="text-end">{{ number_format(1815907127.33, 0) }}</td>
                        </tr>
                        <tr>
                            <td>Total Rp + cost</td>
                            <td class="text-end">
                                0
                            </td>
                            <td class="text-end">
                                0
                            </td>
                            <td></td>
                            <td class="text-end bg-warning text-white">
                                {{ number_format(array_sum(array_column($bk, 'ttl_rp')) + array_sum(array_column($bk_suntik, 'ttl_rp')) + 1815907127.33, 0) }}
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
            <div class="col-lg-6 mt-2">
                @php
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

                    $ttl_rp =
                        $ttlrp1 +
                        $ttlrp2 +
                        $ttlrp3 +
                        $ttlrp4 +
                        $ttlrp5 +
                        $ttlrp6 +
                        $ttlrp7 +
                        $ttlrp8 +
                        $ttlrp9 +
                        $ttlrp10 +
                        $ttlrp11 +
                        $ttlrp12 +
                        $ttlrp13 +
                        $ttlrp14 +
                        $ttlrp15 +
                        $ttlrp16;

                @endphp

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">kategori</th>
                            <th class="dhead">ket</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">rp/gr</th>
                            <th class="dhead text-end">total rp</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>box stock cabut sedang proses</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'ttl_rp')) / array_sum(array_column($box_cabut_sedang_proses, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(sumBk($box_cabut_sedang_proses, 'ttl_rp'), 0) }}
                            </td>
                        </tr>

                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>box selesai cabut siap cetak belum serah</td>
                            <td class="text-end">
                                {{ number_format(sumBk($box_cabut_belum_serah, 'pcs'), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(sumBk($box_cabut_belum_serah, 'gr'), 0) }}
                                {{-- gr pakai --}}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($box_cabut_belum_serah, 'ttl_rp')) / array_sum(array_column($box_cabut_belum_serah, 'gr')), 0) }}
                            </td>

                            <td class="text-end">
                                {{ number_format(
                                    sumBk($box_cabut_belum_serah, 'ttl_rp') +
                                        $rp_gr_cost_op * $gr_box_s_cetak_belum_serah +
                                        $rp_gr_cu_dll * $gr_box_s_cetak_belum_serah,
                                    0,
                                ) }}
                            </td>
                        </tr>
                        <tr>
                            <td>proses</td>
                            <td>box selesai cabut siap cetak diserahkan</td>
                            <td class="text-end">0</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bkselesai_siap_ctk_diserahkan, 'gr')) + $suntik_stock_siap_cetak_diserahkan->gr, 0) }}
                                {{-- gr pakai --}}
                            </td>
                            <td class="text-end">0</td>
                            <td class="text-end">
                                {{ number_format(
                                    sumBk($bkselesai_siap_ctk_diserahkan, 'cost_kerja') +
                                        $rp_gr_cost_op * $gr_box_s_cetak_diserahkan +
                                        $rp_gr_cu_dll * $gr_box_s_cetak_diserahkan,
                                    0,
                                ) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>box selesai cbt siap sortir belum serah</td>
                            <td class="text-end">
                                0</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bkselesai_siap_str, 'gr')), 0) }}
                                {{-- gr pakai --}}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bkselesai_siap_str, 'ttl_rp')) / array_sum(array_column($bkselesai_siap_str, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(
                                    sumBk($bkselesai_siap_str, 'ttl_rp') +
                                        $rp_gr_cost_op * $gr_box_s_sortir_belum_serah +
                                        $rp_gr_cu_dll * $gr_box_s_sortir_belum_serah,
                                    0,
                                ) }}
                            </td>
                        </tr>
                        <tr>
                            <td>proses</td>
                            <td>box selesai cbt siap sortir diserahkan</td>
                            <td class="text-end">
                                0</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bkselesai_siap_str_diserahkan, 'gr')) + $suntik_stock_eo_diserahkan->gr, 0) }}
                                {{-- gr pakai --}}
                            </td>
                            <td class="text-end"> 0</td>
                            <td class="text-end">
                                {{ number_format(
                                    sumBk($bkselesai_siap_str_diserahkan, 'cost_kerja') +
                                        $rp_gr_cost_op * $gr_box_s_sortir_diserahkan +
                                        $rp_gr_cu_dll * $gr_box_s_sortir_diserahkan,
                                    0,
                                ) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>box cbt sisa pgws </td>
                            <td class="text-end">{{ number_format(array_sum(array_column($bk_sisa_pgws, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk_sisa_pgws, 'gr')), 0) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk_sisa_pgws, 'ttl_rp')) / array_sum(array_column($bk_sisa_pgws, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(sumBk($bk_sisa_pgws, 'ttl_rp'), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>cetak sedang proses </td>
                            <td class="text-end">{{ number_format(array_sum(array_column($cetak_proses, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_proses, 'gr')), 0) }}</td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($cetak_proses, 'ttl_rp'))) ? 0 : number_format(array_sum(array_column($cetak_proses, 'ttl_rp')) / array_sum(array_column($cetak_proses, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(sumBk($cetak_proses, 'ttl_rp'), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>cetak selesai siap sortir belum serah</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_selesai_belum_serah, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_selesai_belum_serah, 'gr')), 0) }}
                                {{-- gr pakai --}}
                            </td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($cetak_selesai_belum_serah, 'ttl_rp'))) ? 0 : number_format(array_sum(array_column($cetak_selesai_belum_serah, 'ttl_rp')) / array_sum(array_column($cetak_selesai_belum_serah, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(
                                    sumBk($cetak_selesai_belum_serah, 'ttl_rp') +
                                        $rp_gr_cost_op * $gr_cetak_selesai_b_serah +
                                        $rp_gr_cu_dll * $gr_cetak_selesai_b_serah,
                                    0,
                                ) }}
                            </td>
                        </tr>
                        <tr>
                            <td>proses</td>
                            <td>tidak cetak diserahkan</td>
                            <td class="text-end">
                                0
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_selesai_diserahkan, 'gr_tdk_ctk')), 0) }}
                                {{-- gr pakai --}}
                            </td>
                            <td class="text-end">0</td>
                            <td class="text-end">
                                {{ number_format($rp_gr_cost_op * $gr_tdk_cetak + $rp_gr_cu_dll * $gr_tdk_cetak, 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>proses</td>
                            <td>cetak selesai siap sortir diserahkan</td>
                            <td class="text-end">
                                0
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_selesai_diserahkan, 'gr')) + $suntik_cetak_diserahkan->gr, 0) }}
                                {{-- gr pakai --}}
                            </td>
                            <td class="text-end">0</td>
                            <td class="text-end">
                                {{ number_format(
                                    sumBk($cetak_selesai_diserahkan, 'cost_kerja') +
                                        $rp_gr_cost_op * $gr_cetak_selesai_diserahkan +
                                        $rp_gr_cu_dll * $gr_cetak_selesai_diserahkan,
                                    0,
                                ) }}

                            </td>
                        </tr>
                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>cetak sisa pgws </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_sisa_pgws, 'pcs')) + $suntik_ctk_sisa->pcs, 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_sisa_pgws, 'gr')) + $suntik_ctk_sisa->gr, 0) }}
                            </td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($cetak_sisa_pgws, 'gr'))) ? 0 : number_format(array_sum(array_column($cetak_sisa_pgws, 'ttl_rp')) / array_sum(array_column($cetak_sisa_pgws, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(sumBk($cetak_sisa_pgws, 'ttl_rp') + $suntik_ctk_sisa->ttl_rp, 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>sortir sedang proses </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_proses, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_proses, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($sortir_proses, 'gr'))) ? 0 : number_format(array_sum(array_column($sortir_proses, 'ttl_rp')) / array_sum(array_column($sortir_proses, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(sumBk($sortir_proses, 'ttl_rp'), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>sortir selesai siap grading belum serah</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_selesai, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_selesai, 'gr')), 0) }}
                                {{-- gr pakai --}}
                            </td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($sortir_selesai, 'gr'))) ? 0 : number_format(array_sum(array_column($sortir_selesai, 'ttl_rp')) / array_sum(array_column($sortir_selesai, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(
                                    sumBk($sortir_selesai, 'ttl_rp') +
                                        $rp_gr_cost_op * $gr_sortir_s_g_belum_serah +
                                        $rp_gr_cu_dll * $gr_sortir_s_g_belum_serah,
                                    0,
                                ) }}

                            </td>
                        </tr>
                        <tr>
                            <td>proses</td>
                            <td>sortir selesai siap grading diserahkan</td>
                            <td class="text-end">0</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_selesai_diserahkan, 'gr')) + $suntik_sortir_selesai_diserahkan->gr, 0) }}
                                {{-- gr pakai --}}
                            </td>
                            <td class="text-end">0</td>
                            <td class="text-end">
                                {{ number_format(
                                    sumBk($sortir_selesai_diserahkan, 'cost_kerja') +
                                        $rp_gr_cost_op * $gr_sortir_s_g_belum_diserahkan +
                                        $rp_gr_cu_dll * $gr_sortir_s_g_belum_diserahkan,
                                    0,
                                ) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>sortir sisa pgws</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($stock_sortir, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($stock_sortir, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($stock_sortir, 'gr'))) ? 0 : number_format(array_sum(array_column($stock_sortir, 'ttl_rp')) / array_sum(array_column($stock_sortir, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(sumBk($stock_sortir, 'ttl_rp'), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-success text-white">opname</td>
                            <td>grading stock</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($grading_stock, 'pcs')) + $suntik_grading->pcs, 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($grading_stock, 'gr')) + $suntik_grading->gr, 0) }}
                            </td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($grading_stock, 'gr'))) ? 0 : number_format(array_sum(array_column($grading_stock, 'ttl_rp')) / array_sum(array_column($grading_stock, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(sumBk($grading_stock, 'ttl_rp') + $suntik_grading->ttl_rp, 0) }}
                            </td>
                        </tr>
                    </tbody>



                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td></td>
                            <td class="text-end fw-bold">
                                {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'pcs')) + array_sum(array_column($box_cabut_belum_serah, 'pcs')) + array_sum(array_column($bk_sisa_pgws, 'pcs')) + array_sum(array_column($cetak_proses, 'pcs')) + array_sum(array_column($cetak_selesai_belum_serah, 'pcs')) + array_sum(array_column($cetak_sisa_pgws, 'pcs')) + array_sum(array_column($sortir_proses, 'pcs')) + array_sum(array_column($sortir_selesai, 'pcs')) + array_sum(array_column($stock_sortir, 'pcs')) + array_sum(array_column($grading_stock, 'pcs')) + $suntik_grading->pcs + $suntik_ctk_sisa->pcs, 0) }}
                            </td>
                            <td class="text-end fw-bold">
                                {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'gr')) + array_sum(array_column($box_cabut_belum_serah, 'gr')) + array_sum(array_column($bk_sisa_pgws, 'gr')) + array_sum(array_column($cetak_proses, 'gr')) + array_sum(array_column($cetak_selesai_belum_serah, 'gr')) + array_sum(array_column($cetak_sisa_pgws, 'gr')) + array_sum(array_column($sortir_proses, 'gr')) + array_sum(array_column($sortir_selesai, 'gr')) + array_sum(array_column($stock_sortir, 'gr')) + array_sum(array_column($grading_stock, 'gr')) + array_sum(array_column($bkselesai_siap_str, 'gr')) + $suntik_grading->gr + $suntik_ctk_sisa->gr, 0) }}
                            </td>
                            <td></td>
                            <td class="text-end fw-bold bg-warning text-white">
                                {{ number_format($ttl_rp, 0) }}
                            </td>
                        </tr>
                    </tfoot>

                </table>
            </div>

            <style>
                .modal-lg-max {
                    max-width: 1200px;
                }
            </style>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog  modal-lg-max">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">BK Awal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                        </div>

                    </div>
                </div>
            </div>


        </section>
        @section('scripts')
            <script>
                $(document).ready(function() {
                    $('.open-modal').click(function() {
                        $('#exampleModal').modal('show');
                    });
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
