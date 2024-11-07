<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-5">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <h6>Bk Kerja</h6>
                    </div>
                    <div>
                        <input autofocus placeholder="pencarian" type="text" id="tbl1input"
                            class="form-control form-control-sm">
                    </div>
                </div>
                <div style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-bordered" id="tbl1">
                        <thead>
                            <tr>
                                <th class="dhead">No</th>
                                <th class="dhead">Bulan kerja</th>
                                <th class="dhead">Nama partai</th>
                                <th class="dhead">Grade</th>
                                <th class="text-end dhead">Pcs</th>
                                <th class="text-end dhead">Gr</th>
                                <th class="text-end dhead">Ttl Rp</th>
                            </tr>
                            <tr>
                                <td class="dhead"></td>
                                <td class="dhead">Total</td>
                                <td class="dhead"></td>
                                <td class="dhead"></td>
                                <td class="text-end dhead">
                                    {{ number_format(sumBk($bk, 'pcs_bk') + sumBk($bk_suntik, 'pcs'), 0) }}</td>
                                <td class="text-end dhead">
                                    {{ number_format(sumBk($bk, 'gr_bk') + sumBk($bk_suntik, 'gr'), 0) }}</td>
                                <td class="text-end dhead">
                                    {{ number_format(sumBk($bk, 'cost_bk') + sumBk($bk_suntik, 'ttl_rp'), 0) }}</td>

                            </tr>

                        </thead>
                        <tbody>

                            @php
                                $no = 0;
                            @endphp
                            @foreach ($bk as $b)
                                <tr>
                                    <td>{{ $no + 1 }}</td>
                                    <td>{{ empty($b->bulan) ? '-' : date('F Y', strtotime('01-' . $b->bulan . '-' . $b->tahun)) }}
                                    </td>
                                    <td>{{ $b->nm_partai }}</td>
                                    <td>{{ $b->grade }}</td>
                                    <td class="text-end">{{ number_format($b->pcs_bk, 0) }}</td>
                                    <td class="text-end">{{ number_format($b->gr_bk, 0) }}</td>
                                    <td class="text-end">{{ number_format($b->cost_bk, 0) }}</td>
                                </tr>
                                @php
                                    $no++;
                                @endphp
                            @endforeach
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>0</td>
                                <td>Partai suntik</td>
                                <td></td>
                                <td class="text-end">{{ number_format(sumBk($bk_suntik, 'pcs'), 0) }}</td>
                                <td class="text-end">{{ number_format(sumBk($bk_suntik, 'gr'), 0) }}</td>
                                <td class="text-end">{{ number_format(sumBk($bk_suntik, 'ttl_rp'), 0) }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <h6>Cost Perbulan</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">bulan & tahun</th>
                            <th class="text-end dhead">gaji</th>
                            <th class="text-end dhead">cost operasional</th>
                            <th class="text-end dhead">total rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($uang_cost as $u)
                            <tr>
                                <td>{{ date('F Y', strtotime($u->tahun . '-' . $u->bulan . '-' . '01')) }}
                                </td>
                                <td class="text-end"><a target="_blank"
                                        href="{{ route('cocokan.balance.gaji', ['bulan' => $u->bulan, 'tahun' => $u->tahun]) }}">{{ number_format($u->gaji, 0) }}</a>
                                </td>
                                <td class="text-end"><a target="_blank"
                                        href="{{ route('cocokan.balance.cost', ['bulan' => $u->bulan, 'tahun' => $u->tahun]) }}">{{ number_format($u->total_operasional - $u->gaji, 0) }}</a>
                                </td>
                                <td class="text-end">{{ number_format($u->total_operasional, 0) }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-end">{{ number_format(sumBk($uang_cost, 'gaji'), 0) }}</th>
                            <th class="text-end">
                                {{ number_format(sumBk($uang_cost, 'total_operasional') - sumBk($uang_cost, 'gaji'), 0) }}
                            </th>
                            <th>{{ number_format(sumBk($uang_cost, 'total_operasional'), 0) }}</th>
                        </tr>
                        @for ($i = 0; $i < 5; $i++)
                            <tr>
                                <th colspan="3">&nbsp;</th>
                            </tr>
                        @endfor
                        @php
                            $ttl_sisa_belum_kirim =
                                $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op;

                            $ttl_pengiriman =
                                $pengiriman->cost_bk +
                                $pengiriman->cost_kerja +
                                $pengiriman->cost_cu +
                                $pengiriman->cost_op;

                            $ttl_sisa_blum_grading =
                                sumbk($grading_sisa2, 'cost_bk') + sumbk($grading_sisa2, 'cost_kerja');

                            $ttl_cost_berjalan =
                                $cbt_proses->ttl_rp +
                                $cbt_sisa_pgws->ttl_rp +
                                $cetak_proses->ttl_rp +
                                $cetak_proses->cost_kerja +
                                $cbt_blm_kirim->cost_kerja +
                                $cetak_sisa->ttl_rp +
                                $sedang_proses->ttl_rp +
                                $sedang_proses->cost_kerja +
                                $sortir_sisa->ttl_rp +
                                $sortir_sisa->cost_kerja +
                                $ttl_sisa_blum_grading +
                                $ttl_pengiriman +
                                $ttl_sisa_belum_kirim +
                                sumBk($cabut_selesai_siap_cetak, 'ttl_rp') +
                                sumBk($sortir_selesai, 'ttl_rp') +
                                sumBk($cetak_selesai, 'ttl_rp') +
                                sumBk($cabut_selesai_siap_cetak, 'cost_kerja') +
                                sumBk($sortir_selesai, 'cost_kerja') +
                                sumBk($cetak_selesai, 'cost_kerja') +
                                $grading_susut->cost_bk +
                                $grading_susut->cost_kerja +
                                $grading_susut->cost_cu +
                                $grading_susut->cost_op;

                            $ttl_berjalan =
                                $ttl_cost_berjalan -
                                sumBk($uang_cost, 'total_operasional') -
                                sumBk($bk, 'cost_bk') -
                                sumBk($bk_suntik, 'ttl_rp');
                        @endphp
                        <tr>
                            <th>Cost Berjalan</th>
                            <th></th>
                            <th></th>
                            <th class="text-end">
                                {{ number_format($ttl_cost_berjalan - sumBk($uang_cost, 'total_operasional') - sumBk($bk, 'cost_bk') - sumBk($bk_suntik, 'ttl_rp'), 0) }}
                            </th>
                        </tr>
                        <tr>
                            <th class="dhead">Total Bk + Operasional + cost berjalan</th>
                            <th class="dhead"></th>
                            <th class="dhead"></th>
                            <th class="text-end dhead">
                                {{ number_format(sumBk($uang_cost, 'total_operasional') + sumBk($bk, 'cost_bk') + sumBk($bk_suntik, 'ttl_rp') + $ttl_berjalan, 0) }}
                            </th>
                        </tr>

                    </tfoot>
                </table>
            </div>
            <div class="col-lg-3">
                <h6>Bk Rp</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">ket</th>
                            <th class="text-end dhead">pcs</th>
                            <th class="text-end dhead">gr</th>
                            <th class="text-end dhead">Total Rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- <tr>
                            <td style="background-color: #F7F700">Cabut akhir</td>
                            <td class="text-end">{{ number_format($bk_akhir->pcs, 0) }}
                            </td>
                            <td class="text-end">{{ number_format($bk_akhir->gr, 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($bk_akhir->cost_kerja + ($cost_dll / $ttl_gr) * $bk_akhir->gr + ($cost_op / $ttl_gr) * $bk_akhir->gr, 0) }}
                            </td>
                        </tr> --}}
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Cabut sedang proses</td>
                            <td class="text-end">{{ number_format($cbt_proses->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($cbt_proses->gr, 0) }}</td>
                            <td class="text-end">{{ number_format($cbt_proses->ttl_rp, 0) }}</td>

                        </tr>
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Cabut sisa pengawas</td>
                            <td class="text-end">{{ number_format($cbt_sisa_pgws->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($cbt_sisa_pgws->gr, 0) }}</td>
                            <td class="text-end">{{ number_format($cbt_sisa_pgws->ttl_rp, 0) }}</td>

                        </tr>
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Cabut selesai siap cetak belum kirim</td>
                            <td class="text-end">{{ number_format(sumBk($cabut_selesai_siap_cetak, 'pcs'), 0) }}</td>
                            <td class="text-end">{{ number_format(sumBk($cabut_selesai_siap_cetak, 'gr'), 0) }}</td>
                            <td class="text-end">
                                {{ number_format(sumBk($cabut_selesai_siap_cetak, 'ttl_rp') + sumBk($cabut_selesai_siap_cetak, 'cost_kerja'), 0) }}
                            </td>

                        </tr>
                        {{-- <tr>
                            <td style="background-color: #F7F700">Cetak Akhir</td>
                            <td class="text-end">
                                {{ number_format($cetak_akhir->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($cetak_akhir->gr, 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($cetak_akhir->cost_kerja + ($cost_dll / $ttl_gr) * $cetak_akhir->gr + ($cost_op / $ttl_gr) * $cetak_akhir->gr, 0) }}
                            </td>

                        </tr> --}}
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Cetak sedang Proses</td>
                            <td class="text-end">{{ number_format($cetak_proses->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($cetak_proses->gr, 0) }}</td>
                            <td class="text-end">
                                {{ number_format($cetak_proses->ttl_rp + $cetak_proses->cost_kerja, 0) }}
                            </td>

                        </tr>
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Cetak sisa Pengawas</td>
                            <td class="text-end">{{ number_format($cetak_sisa->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($cetak_sisa->gr, 0) }}</td>
                            <td class="text-end">{{ number_format($cetak_sisa->ttl_rp, 0) }}</td>

                        </tr>
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Cetak selesai siap sortir belum kirim</td>
                            <td class="text-end">{{ number_format(sumBk($cetak_selesai, 'pcs'), 0) }}</td>
                            <td class="text-end">{{ number_format(sumBk($cetak_selesai, 'gr'), 0) }}</td>
                            <td class="text-end">
                                {{ number_format(sumBk($cetak_selesai, 'ttl_rp') + sumBk($cetak_selesai, 'cost_kerja'), 0) }}
                            </td>

                        </tr>
                        {{-- <tr>
                            <td style="background-color: #F7F700">Sortir Akhir</td>
                            <td class="text-end">
                                {{ number_format($sortir_akhir->pcs, 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($sortir_akhir->gr, 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($sortir_akhir->cost_kerja + ($cost_dll / $ttl_gr) * $sortir_akhir->gr + ($cost_op / $ttl_gr) * $sortir_akhir->gr, 0) }}
                            </td>
                        </tr> --}}
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Sortir sedang Proses</td>
                            <td class="text-end">{{ number_format($sedang_proses->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($sedang_proses->gr, 0) }}</td>
                            <td class="text-end">
                                {{ number_format($sedang_proses->ttl_rp + $sedang_proses->cost_kerja, 0) }}</td>

                        </tr>
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Sortir sisa Pengawas</td>
                            <td class="text-end">{{ number_format($sortir_sisa->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($sortir_sisa->gr, 0) }}</td>
                            <td class="text-end">
                                {{ number_format($sortir_sisa->ttl_rp + $sortir_sisa->cost_kerja, 0) }}</td>

                        </tr>
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Sortir selesai siap grading belum kirim
                            </td>
                            <td class="text-end">{{ number_format(sumBk($sortir_selesai, 'pcs'), 0) }}</td>
                            <td class="text-end">{{ number_format(sumBk($sortir_selesai, 'gr'), 0) }}</td>
                            <td class="text-end">
                                {{ number_format(sumBk($sortir_selesai, 'ttl_rp') + sumBk($sortir_selesai, 'cost_kerja'), 0) }}
                            </td>

                        </tr>
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Sisa belum grading</td>
                            <td class="text-end">{{ number_format($grading_sisa->pcs ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($grading_sisa->gr ?? 0, 0) }}</td>
                            <td class="text-end">
                                {{ number_format(sumbk($grading_sisa2, 'cost_bk') + sumbk($grading_sisa2, 'cost_kerja'), 0) }}
                            </td>

                        </tr>
                        @php
                            $rp_satuan =
                                ($sortir_akhir->ttl_rp + $sortir_akhir->cost_kerja + $opname->ttl_rp) /
                                ($sortir_akhir->gr + $opname->gr);
                        @endphp
                        <tr>
                            <td style="background-color: #F7BAC5; color:white">Pengiriman</td>
                            <td class="text-end">{{ number_format($pengiriman->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($pengiriman->gr, 0) }}</td>
                            <td class="text-end">
                                {{ number_format($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op, 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Sisa belum kirim ( sisa + qc)</td>
                            <td class="text-end">{{ number_format($grading->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($grading->gr, 0) }}</td>
                            <td class="text-end">
                                {{ number_format($grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op + $grading_susut->cost_bk + $grading_susut->cost_kerja + $grading_susut->cost_cu + $grading_susut->cost_op, 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #F7BAC5;color:white">Selisih</td>
                            @php
                                $pcs_sisa_grading = $grading_sisa->pcs ?? 0;
                            @endphp
                            <td class="text-end text-danger fw-bold">
                                {{ number_format($sortir_akhir->pcs + $opname->pcs - $grading->pcs - $pengiriman->pcs - $pcs_sisa_grading, 0) }}
                            </td>
                            <td class="text-end text-danger fw-bold">
                                0
                            </td>
                            <td class="text-end text-danger fw-bold">
                                0
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <td class="dhead fw-bold">Total</td>

                        <td class="dhead text-end fw-bold">
                            {{ number_format($cbt_proses->pcs + $cbt_sisa_pgws->pcs + $cetak_proses->pcs + $cetak_sisa->pcs + $sedang_proses->pcs + $sortir_sisa->pcs + $grading->pcs + ($sortir_akhir->pcs + $opname->pcs - $grading->pcs) + sumBk($cabut_selesai_siap_cetak, 'pcs') + sumBk($sortir_selesai, 'pcs') + sumBk($cetak_selesai, 'pcs'), 0) }}
                        </td>
                        @php
                            $grding_sisa_gr = $grading_sisa->gr ?? 0;
                        @endphp
                        <td class="dhead text-end fw-bold">
                            {{ number_format($cbt_proses->gr + $cbt_sisa_pgws->gr + $cetak_proses->gr + $cetak_sisa->gr + $sedang_proses->gr + $sortir_sisa->gr + $grading->gr + $grding_sisa_gr + $pengiriman->gr + sumBk($cabut_selesai_siap_cetak, 'gr') + sumBk($sortir_selesai, 'gr') + sumBk($cetak_selesai, 'gr'), 0) }}
                        </td>
                        @php

                            $ttl_sisa_belum_kirim =
                                $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op;

                            $ttl_pengiriman =
                                $pengiriman->cost_bk +
                                $pengiriman->cost_kerja +
                                $pengiriman->cost_cu +
                                $pengiriman->cost_op;

                            $ttl_sisa_blum_grading =
                                sumbk($grading_sisa2, 'cost_bk') + sumbk($grading_sisa2, 'cost_kerja');

                            // $grading_sisa = $grading_sisa->cost_kerja_dll ?? 0;

                        @endphp
                        <td class="dhead text-end fw-bold">
                            {{ number_format($cbt_proses->ttl_rp + $cbt_sisa_pgws->ttl_rp + sumBk($cabut_selesai_siap_cetak, 'ttl_rp') + sumBk($cabut_selesai_siap_cetak, 'cost_kerja') + $cetak_proses->ttl_rp + $cetak_proses->cost_kerja + $cetak_sisa->ttl_rp + sumBk($cetak_selesai, 'ttl_rp') + sumBk($cetak_selesai, 'cost_kerja') + $sedang_proses->ttl_rp + $sedang_proses->cost_kerja + $sortir_sisa->ttl_rp + $sortir_sisa->cost_kerja + sumBk($sortir_selesai, 'ttl_rp') + sumBk($sortir_selesai, 'cost_kerja') + sumbk($grading_sisa2, 'cost_bk') + sumbk($grading_sisa2, 'cost_kerja') + $pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op + $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op + $grading_susut->cost_bk + $grading_susut->cost_kerja + $grading_susut->cost_cu + $grading_susut->cost_op, 0) }}
                        </td>
                    </tfoot>

                </table>

                {{-- <br>
                <h6>Cost Kerja</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">ket</th>
                            <th class="text-end dhead">pcs</th>
                            <th class="text-end dhead">gr</th>
                            <th class="text-end dhead">Total Rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="background-color: #F7F700">Cabut akhir</td>
                            <td class="text-end">0
                            </td>
                            <td class="text-end">{{ number_format($bk_akhir->gr, 0) }}</td>
                            <td class="text-end">
                                {{ number_format($bk_akhir->cost_kerja + ($cost_dll / $ttl_gr) * $bk_akhir->gr + ($cost_op / $ttl_gr) * $bk_akhir->gr, 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color: #F7F700">Cetak Akhir</td>
                            <td class="text-end">
                                0</td>
                            <td class="text-end">{{ number_format($cetak_akhir->gr, 0) }}</td>
                            <td class="text-end">
                                {{ number_format($cetak_akhir->cost_kerja + ($cost_dll / $ttl_gr) * $cetak_akhir->gr + ($cost_op / $ttl_gr) * $cetak_akhir->gr, 0) }}
                            </td>

                        </tr>
                        <tr>
                            <td style="background-color: #F7F700">Sortir Akhir</td>
                            <td class="text-end">
                                0
                            </td>
                            <td class="text-end">
                                {{ number_format($sortir_akhir->gr, 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(($cost_dll / $ttl_gr) * $sortir_akhir->gr + ($cost_op / $ttl_gr) * $sortir_akhir->gr, 0) }}
                            </td>
                        </tr>
                    </tbody>
                    @php
                        $cbt_akhir =
                            $bk_akhir->cost_kerja +
                            ($cost_dll / $ttl_gr) * $bk_akhir->gr +
                            ($cost_op / $ttl_gr) * $bk_akhir->gr;
                        $ctk_akhir =
                            $cetak_akhir->cost_kerja +
                            ($cost_dll / $ttl_gr) * $cetak_akhir->gr +
                            ($cost_op / $ttl_gr) * $cetak_akhir->gr;
                        $str_akhir =
                            ($cost_dll / $ttl_gr) * $sortir_akhir->gr + ($cost_op / $ttl_gr) * $sortir_akhir->gr;
                    @endphp
                    <tfoot>
                        <tr>

                            <td class="dhead">Total</td>
                            <td class="dhead text-end">
                                0
                            </td>
                            <td class="dhead text-end">
                                0
                            </td>

                            <td class="dhead text-end">
                                {{ number_format($cbt_akhir + $ctk_akhir + $str_akhir, 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="dhead">Grand Total</td>
                            <td class="dhead text-end">
                                0
                            </td>
                            <td class="dhead text-end">
                                0
                            </td>

                            <td class="dhead text-end">
                                {{ number_format($cbt_akhir + $ctk_akhir + $str_akhir + $cbt_proses->ttl_rp + $cbt_sisa_pgws->ttl_rp + $cetak_proses->ttl_rp + $cetak_sisa->ttl_rp + $sedang_proses->ttl_rp + $sortir_sisa->ttl_rp + $rp_satuan * $pengiriman->gr + ($grading->gr - $pengiriman->gr) * $rp_satuan, 0) }}
                            </td>
                        </tr>
                    </tfoot>

                </table> --}}
            </div>
        </section>








        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')
            </script>
            <script>
                get_opr();

                function get_opr() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('summary.get_operasional') }}",
                        success: function(response) {
                            $('#cost_opr').html(response);
                        }
                    });
                }
            </script>
            <script>
                function numberFormat(initialValue) {
                    return {
                        formattedNumber: new Intl.NumberFormat().format(initialValue),
                        formatNumber() {
                            // Hapus karakter non-digit dan simpan nomor mentah
                            let rawNumber = this.formattedNumber.replace(/\D/g, '');

                            // Format nomor dengan pemisah ribuan
                            this.formattedNumber = new Intl.NumberFormat().format(rawNumber);
                        }
                    };
                }
            </script>
        @endsection

    </x-slot>
</x-theme.app>
