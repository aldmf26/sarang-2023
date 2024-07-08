<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">
            @include('home.gudang.nav')
            <div class="col-lg-6">
                <h6 class="float-start mt-1">Gudang Sortir</h6>
            </div>
            <div class="col-lg-6">
                <a class="btn btn-sm btn-primary float-end" href="{{ route('gudang.export') }}"><i
                        class="fas fa-print"></i>
                    Export All</a>
            </div>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-4">
                <input type="text" id="tbl8input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl8" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Sortir stock</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                            </tr>
                            @php
                                if (!function_exists('ttl3')) {
                                    function ttl3($tl)
                                    {
                                        return [
                                            'pcs_awal' => array_sum(array_column($tl, 'pcs_awal')),
                                            'gr_awal' => array_sum(array_column($tl, 'gr_awal')),
                                            'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                            'cost_cbt' => array_sum(array_column($tl, 'cost_cbt')),
                                            'cost_ctk' => array_sum(array_column($tl, 'cost_ctk')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">{{ count($siap_sortir) }}</th>
                                <th class="dheadstock text-end">{{ number_format(ttl3($siap_sortir)['pcs_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end">{{ number_format(ttl3($siap_sortir)['gr_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siap_sortir as $d)
                                <tr>
                                    <td align="center">{{ $d->name }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">
                                        {{ number_format(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo) / $d->gr_awal, 0) }}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl9input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl9" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Sortir sedang proses</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>

                            </tr>
                            @php
                                if (!function_exists('ttl3')) {
                                    function ttl3($tl)
                                    {
                                        return [
                                            'pcs_awal' => array_sum(array_column($tl, 'pcs_awal')),
                                            'gr_awal' => array_sum(array_column($tl, 'gr_awal')),
                                            'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                            'cost_cbt' => array_sum(array_column($tl, 'cost_cbt')),
                                            'cost_ctk' => array_sum(array_column($tl, 'cost_ctk')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">{{ count($sortir_proses) }}</th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl3($sortir_proses)['pcs_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl3($sortir_proses)['gr_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortir_proses as $d)
                                <tr>
                                    <td align="center">{{ $d->name }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">
                                        {{ number_format(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_eo) / $d->gr_awal, 0) }}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4">
                <input type="text" id="tbl10input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl10" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Sortir selesai siap grading</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>

                            </tr>
                            @php
                                if (!function_exists('ttl4')) {
                                    function ttl4($tl)
                                    {
                                        return [
                                            'pcs_awal' => array_sum(array_column($tl, 'pcs_awal')),
                                            'gr_awal' => array_sum(array_column($tl, 'gr_awal')),
                                            'ttl_rp' => array_sum(array_column($tl, 'ttl_rp')),
                                            'cost_cbt' => array_sum(array_column($tl, 'cost_cbt')),
                                            'cost_ctk' => array_sum(array_column($tl, 'cost_ctk')),
                                            'cost_str' => array_sum(array_column($tl, 'cost_str')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">{{ count($sortir_selesai) }}</th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($sortir_selesai)['pcs_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($sortir_selesai)['gr_awal'], 0) }}
                                </th>
                                <th class="dheadstock text-center"></th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortir_selesai as $d)
                                <tr>
                                    <td align="center">{{ $d->name }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">
                                        {{ number_format(($d->ttl_rp + $d->cost_cbt + $d->cost_ctk + $d->cost_str + $d->cost_eo) / $d->gr_awal, 0) }}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
