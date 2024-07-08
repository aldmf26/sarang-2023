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

            <div class="col-lg-4">
                <input type="text" id="tbl9input" class="form-control form-control-sm mb-2" placeholder="cari">
                <div style="overflow-y: scroll; height: 400px">
                    <table id="tbl9" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="dhead text-center" colspan="6">Box Belum Kirim</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pengawas</th>
                                <th class="dhead text-center">No Box Kirim</th>
                                <th class="dhead text-center">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                            </tr>
                            @php
                                if (!function_exists('ttl4')) {
                                    function ttl4($tl)
                                    {
                                        return [
                                            'pcs_grading' => array_sum(array_column($tl, 'pcs_grading')),
                                            'gr_grading' => array_sum(array_column($tl, 'gr_grading')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-end">{{ count($gradingbox) }}</th>
                                <th class="dheadstock text-end"></th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($gradingbox)['pcs_grading'], 0) }}
                                </th>
                                <th class="dheadstock text-end">{{ number_format(ttl4($gradingbox)['gr_grading'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gradingbox as $d)
                                <tr>
                                    <td align="center">{{ $d->penerima }}</td>
                                    <td align="center">P{{ $d->no_box_grading }}</td>
                                    <td align="center">{{ $d->nm_grade }}</td>
                                    <td align="right">{{ $d->pcs_grading }}</td>
                                    <td align="right">{{ $d->gr_grading }}</td>
                                    <td align="right">
                                        {{ number_format($d->ttl_rp / $d->gr_grading, 0) }}
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
                                <th class="dhead text-center" colspan="6">Box Selesai Kirim</th>
                            </tr>
                            <tr>
                                <th class="dhead text-center">Pengawas</th>
                                <th class="dhead text-center">No Box Kirim</th>
                                <th class="dhead text-center">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Rp/gr</th>
                            </tr>
                            @php
                                if (!function_exists('ttl4')) {
                                    function ttl4($tl)
                                    {
                                        return [
                                            'pcs_grading' => array_sum(array_column($tl, 'pcs_grading')),
                                            'gr_grading' => array_sum(array_column($tl, 'gr_grading')),
                                        ];
                                    }
                                }
                            @endphp
                            <tr>
                                <th class="dheadstock text-center">Total</th>
                                <th class="dheadstock text-end">{{ count($gradingboxkirim) }}</th>
                                <th class="dheadstock text-end"></th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($gradingboxkirim)['pcs_grading'], 0) }}
                                </th>
                                <th class="dheadstock text-end">
                                    {{ number_format(ttl4($gradingboxkirim)['gr_grading'], 0) }}
                                </th>
                                <th class="dheadstock text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gradingboxkirim as $d)
                                <tr>
                                    <td align="center">{{ $d->penerima }}</td>
                                    <td align="center">P{{ $d->no_box_grading }}</td>
                                    <td align="center">{{ $d->nm_grade }}</td>
                                    <td align="right">{{ $d->pcs_grading }}</td>
                                    <td align="right">{{ $d->gr_grading }}</td>
                                    <td align="right">
                                        {{ number_format($d->ttl_rp / $d->gr_grading, 0) }}
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
