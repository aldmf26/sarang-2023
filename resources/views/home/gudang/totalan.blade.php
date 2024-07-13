<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">
            @include('home.gudang.nav')
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>
            <div class="col-lg-6">
                <a class="btn btn-sm btn-primary float-end" href="{{ route('gudang.export') }}"><i
                        class="fas fa-print"></i>Export All</a>
            </div>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @php
                $cost_bk = 0;
                foreach ($bk as $d) {
                    $cost_bk += $d->hrga_satuan * $d->gr;
                }

                $cost_bk_proses = 0;
                foreach ($cabut as $d) {
                    $cost_bk_proses += $d->hrga_satuan * $d->gr;
                }

                $cost_cabutSelesai = 0;
                foreach ($cabutSelesai as $d) {
                    $cost_cabutSelesai += $d->hrga_satuan * $d->gr;
                }

                $cost_eoSelesai = 0;
                foreach ($eoSelesai as $d) {
                    $cost_eoSelesai += $d->hrga_satuan * $d->gr;
                }

                $cost_cabut_selesai = 0;
                foreach ($cabut_selesai as $d) {
                    $cost_cabut_selesai += $d->ttl_rp + $d->cost_cbt;
                }

                $cost_cetak_proses = 0;
                foreach ($cetak_proses as $d) {
                    $cost_cetak_proses += $d->ttl_rp + $d->cost_cbt;
                }

                $cost_cetak_selesai = 0;
                foreach ($cetak_selesai as $d) {
                    $cost_cetak_selesai += $d->ttl_rp + $d->cost_cbt + $d->cost_ctk;
                }

                $cost_cetak_selesai = 0;
                foreach ($cetak_selesai as $d) {
                    $cost_cetak_selesai += $d->ttl_rp + $d->cost_cbt + $d->cost_ctk;
                }
                $cost_siap_sortir = 0;
                foreach ($siap_sortir as $d) {
                    $cost_siap_sortir += $d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo;
                }
                $cost_sortir_proses = 0;
                foreach ($sortir_proses as $d) {
                    $cost_sortir_proses += $d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo;
                }
                $cost_sortir_selesai = 0;
                foreach ($sortir_selesai as $d) {
                    $cost_sortir_selesai += $d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo + $d->cost_str;
                }
                $cost_grading = 0;
                foreach ($grading as $d) {
                    $cost_grading += $d->cost_bk + $d->cost_cbt + $d->cost_ctk + $d->cost_eo + $d->cost_str;
                }
                $cost_gradingbox = 0;
                foreach ($gradingbox as $d) {
                    $cost_gradingbox += $d->ttl_rp;
                }
                $cost_gradingboxkirim = 0;
                foreach ($gradingboxkirim as $d) {
                    $cost_gradingboxkirim += $d->ttl_rp;
                }

            @endphp

            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Box Stock</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($bk) }}</td>
                            <td class="text-end">{{ number_format(array_sum(array_column($bk, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">{{ number_format(array_sum(array_column($bk, 'gr')), 0) }}</td>
                            <td class="text-end">
                                @if (array_sum(array_column($bk, 'gr')) != 0)
                                    {{ number_format($cost_bk / array_sum(array_column($bk, 'gr')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Box Sedang Proses</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($cabut) }}</td>
                            <td class="text-end">{{ number_format(array_sum(array_column($cabut, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">{{ number_format(array_sum(array_column($cabut, 'gr')), 0) }}</td>
                            <td class="text-end">
                                @if (array_sum(array_column($cabut, 'gr')) != 0)
                                    {{ number_format($cost_bk_proses / array_sum(array_column($cabut, 'gr')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Box Selesai Siap Cetak</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($cabutSelesai) }}</td>
                            <td class="text-end">{{ number_format(array_sum(array_column($cabutSelesai, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">{{ number_format(array_sum(array_column($cabutSelesai, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($cabutSelesai, 'gr')) != 0)
                                    {{ number_format($cost_cabutSelesai / array_sum(array_column($cabutSelesai, 'gr')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Box Selesai Siap Sortir</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($eoSelesai) }}</td>
                            <td class="text-end">{{ number_format(array_sum(array_column($eoSelesai, 'pcs')), 0) }}
                            </td>
                            <td class="text-end">{{ number_format(array_sum(array_column($eoSelesai, 'gr')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($eoSelesai, 'gr')) != 0)
                                    {{ number_format($cost_eoSelesai / array_sum(array_column($eoSelesai, 'gr')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Cetak Stock</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($cabut_selesai) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cabut_selesai, 'pcs_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cabut_selesai, 'gr_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($cabut_selesai, 'gr_awal')) != 0)
                                    {{ number_format($cost_cabut_selesai / array_sum(array_column($cabut_selesai, 'gr_awal')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Cetak sedang proses</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($cetak_selesai) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_selesai, 'pcs_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_selesai, 'gr_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($cetak_selesai, 'gr_awal')) != 0)
                                    {{ number_format($cost_cetak_selesai / array_sum(array_column($cetak_selesai, 'gr_awal')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Cetak selesai siap sortir</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($cetak_proses) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_proses, 'pcs_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($cetak_proses, 'gr_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($cetak_proses, 'gr_awal')) != 0)
                                    {{ number_format($cost_cetak_proses / array_sum(array_column($cetak_proses, 'gr_awal')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">

            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Sortir Stock</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($siap_sortir) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($siap_sortir, 'pcs_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($siap_sortir, 'gr_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($siap_sortir, 'gr_awal')) != 0)
                                    {{ number_format($cost_siap_sortir / array_sum(array_column($siap_sortir, 'gr_awal')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Sortir Sedang Proses</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($sortir_proses) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_proses, 'pcs_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_proses, 'gr_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($sortir_proses, 'gr_awal')) != 0)
                                    {{ number_format($cost_sortir_proses / array_sum(array_column($sortir_proses, 'gr_awal')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Sortir Sedang Proses</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($sortir_selesai) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_selesai, 'pcs_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($sortir_selesai, 'gr_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($sortir_selesai, 'gr_awal')) != 0)
                                    {{ number_format($cost_sortir_selesai / array_sum(array_column($sortir_selesai, 'gr_awal')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Grading Stock</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($grading) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($grading, 'pcs_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($grading, 'gr_awal')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($grading, 'gr_awal')) != 0)
                                    {{ number_format($cost_grading / array_sum(array_column($grading, 'gr_awal')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Box belum kirim</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($gradingbox) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($gradingbox, 'pcs_grading')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($gradingbox, 'gr_grading')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($gradingbox, 'gr_grading')) != 0)
                                    {{ number_format($cost_gradingbox / array_sum(array_column($gradingbox, 'gr_grading')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-3">
                <table id="tbl8" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class="dhead text-center">Box selesai kirim</th>
                        <tr>
                        <tr>
                            <th class="dhead text-end nowrap">Ttl Box</th>
                            <th class="dhead text-end nowrap">Pcs</th>
                            <th class="dhead text-end nowrap">Gr</th>
                            <th class="dhead text-end nowrap">Rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-end">{{ count($gradingboxkirim) }}</td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($gradingboxkirim, 'pcs_grading')), 0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format(array_sum(array_column($gradingboxkirim, 'gr_grading')), 0) }}
                            </td>
                            <td class="text-end">
                                @if (array_sum(array_column($gradingboxkirim, 'gr_grading')) != 0)
                                    {{ number_format($cost_gradingboxkirim / array_sum(array_column($gradingboxkirim, 'gr_grading')), 0) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </section>
    </x-slot>
    @section('scripts')
        <script>
            ["tbl1", "tbl2", "tbl3", "tbl4", "tbl5", "tbl6", "tgbl7", "tbl8", "tbl9", "tbl10"].forEach((tbl, i) => pencarian(
                `tbl${i+1}input`, tbl));
        </script>
    @endsection
</x-theme.app>
