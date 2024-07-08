<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="row">
            @include('home.gudang.nav')
            <div class="col-lg-6">
                <h6 class="float-start mt-1">Gudang Grading</h6>
            </div>
            <div class="col-lg-6">
                <a class="btn btn-sm btn-primary float-end" href="{{ route('gudang.export') }}"><i
                        class="fas fa-print"></i>Export All</a>
            </div>
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-5">
                <input type="text" id="tbl8input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl8" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="7">Grading stock</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pemilik</th>
                                <th class="dhead text-center">Penerima</th>
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
                                            'pcs' => array_sum(array_column($tl, 'pcs')),
                                            'gr' => array_sum(array_column($tl, 'gr')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-center"></th>
                                <th class="dheadstock text-end">{{ count($grading) }}</th>
                                <th class="dheadstock text-end">{{ number_format(ttl3($grading)['pcs'], 0) }}
                                </th>
                                <th class="dheadstock text-end">{{ number_format(ttl3($grading)['gr'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grading as $d)
                                <tr>
                                    <td align="center">{{ $d->pemilik }}</td>
                                    <td align="center">{{ $d->penerima }}</td>
                                    <td align="center">{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box_sortir }}</td>
                                    <td align="right">{{ $d->pcs }}</td>
                                    <td align="right">{{ $d->gr }}</td>
                                    <td align="right">
                                        {{ number_format(($d->cost_bk + $d->cost_cbt + $d->cost_ctk + $d->cost_eo + $d->cost_str) / $d->gr_awal, 0) }}
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
