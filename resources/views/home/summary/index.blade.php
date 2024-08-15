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
            <div class="col-lg-6">
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
            <div class="col-lg-6">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">ket</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">rp/gr</th>
                            <th class="dhead text-end">total rp</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
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
                                {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'ttl_rp')), 0) }}
                            </td>
                        </tr>

                        <tr>
                            <td>box selesai cabut siap cetak belum serah</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($box_cabut_belum_serah, 'pcs')), 0) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($box_cabut_belum_serah, 'gr')), 0) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($box_cabut_belum_serah, 'ttl_rp')) / array_sum(array_column($box_cabut_belum_serah, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($box_cabut_belum_serah, 'ttl_rp')), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>box selesai cbt siap sortir belum serah</td>
                            <td class="text-end">
                                0</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bkselesai_siap_str, 'gr')), 0) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bkselesai_siap_str, 'ttl_rp')) / array_sum(array_column($bkselesai_siap_str, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bkselesai_siap_str, 'ttl_rp')), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>box cbt sisa pgws </td>
                            <td class="text-end">{{ number_format(array_sum(array_column($bk_sisa_pgws, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk_sisa_pgws, 'gr')), 0) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk_sisa_pgws, 'ttl_rp')) / array_sum(array_column($bk_sisa_pgws, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($bk_sisa_pgws, 'ttl_rp')), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>cetak sedang proses </td>
                            <td class="text-end">{{ number_format(array_sum(array_column($cetak_proses, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_proses, 'gr')), 0) }}</td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($cetak_proses, 'ttl_rp'))) ? 0 : number_format(array_sum(array_column($cetak_proses, 'ttl_rp')) / array_sum(array_column($cetak_proses, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_proses, 'ttl_rp')), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>cetak selesai siap sortir belum serah</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_selesai_belum_serah, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_selesai_belum_serah, 'gr')), 0) }}</td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($cetak_selesai_belum_serah, 'ttl_rp'))) ? 0 : number_format(array_sum(array_column($cetak_selesai_belum_serah, 'ttl_rp')) / array_sum(array_column($cetak_selesai_belum_serah, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_selesai_belum_serah, 'ttl_rp')), 0) }}
                            </td>
                        </tr>
                        <tr>
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
                                {{ number_format(array_sum(array_column($cetak_sisa_pgws, 'ttl_rp')) + $suntik_ctk_sisa->ttl_rp, 0) }}
                            </td>
                        </tr>
                        <tr>
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
                                {{ number_format(array_sum(array_column($sortir_proses, 'ttl_rp')), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td>sortir selesai siap grading belum serah</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_selesai, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_selesai, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ empty(array_sum(array_column($sortir_selesai, 'gr'))) ? 0 : number_format(array_sum(array_column($sortir_selesai, 'ttl_rp')) / array_sum(array_column($sortir_selesai, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_selesai, 'ttl_rp')), 0) }}
                            </td>
                        </tr>
                        <tr>
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
                                {{ number_format(array_sum(array_column($stock_sortir, 'ttl_rp')), 0) }}
                            </td>
                        </tr>
                        <tr>
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
                                {{ number_format(array_sum(array_column($grading_stock, 'ttl_rp')) + $suntik_grading->ttl_rp, 0) }}
                            </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td class="text-end fw-bold">
                                {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'pcs')) + array_sum(array_column($box_cabut_belum_serah, 'pcs')) + array_sum(array_column($bk_sisa_pgws, 'pcs')) + array_sum(array_column($cetak_proses, 'pcs')) + array_sum(array_column($cetak_selesai_belum_serah, 'pcs')) + array_sum(array_column($cetak_sisa_pgws, 'pcs')) + array_sum(array_column($sortir_proses, 'pcs')) + array_sum(array_column($sortir_selesai, 'pcs')) + array_sum(array_column($stock_sortir, 'pcs')) + array_sum(array_column($grading_stock, 'pcs')) + $suntik_grading->pcs + $suntik_ctk_sisa->pcs, 0) }}
                            </td>
                            <td class="text-end fw-bold">
                                {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'gr')) + array_sum(array_column($box_cabut_belum_serah, 'gr')) + array_sum(array_column($bk_sisa_pgws, 'gr')) + array_sum(array_column($cetak_proses, 'gr')) + array_sum(array_column($cetak_selesai_belum_serah, 'gr')) + array_sum(array_column($cetak_sisa_pgws, 'gr')) + array_sum(array_column($sortir_proses, 'gr')) + array_sum(array_column($sortir_selesai, 'gr')) + array_sum(array_column($stock_sortir, 'gr')) + array_sum(array_column($grading_stock, 'gr')) + array_sum(array_column($bkselesai_siap_str, 'gr')) + $suntik_grading->gr + $suntik_ctk_sisa->gr, 0) }}
                            </td>
                            <td></td>
                            <td class="text-end fw-bold">
                                {{ number_format(array_sum(array_column($box_cabut_sedang_proses, 'ttl_rp')) + array_sum(array_column($box_cabut_belum_serah, 'ttl_rp')) + array_sum(array_column($bk_sisa_pgws, 'ttl_rp')) + array_sum(array_column($cetak_proses, 'ttl_rp')) + array_sum(array_column($cetak_selesai_belum_serah, 'ttl_rp')) + array_sum(array_column($cetak_sisa_pgws, 'ttl_rp')) + array_sum(array_column($sortir_proses, 'ttl_rp')) + array_sum(array_column($sortir_selesai, 'ttl_rp')) + array_sum(array_column($stock_sortir, 'ttl_rp')) + array_sum(array_column($grading_stock, 'ttl_rp')) + array_sum(array_column($bkselesai_siap_str, 'ttl_rp')) + $suntik_grading->ttl_rp + $suntik_ctk_sisa->ttl_rp, 0) }}
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
