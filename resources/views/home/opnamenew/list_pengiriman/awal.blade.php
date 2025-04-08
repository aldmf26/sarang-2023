<x-theme.app title="{{ $title }}" table="Y" sizeCard="11">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.opnamenew.nav')
            {{-- @include('home.opnamenew.pengiriman.nav') --}}

            <div class="col-lg-12">

                <h5 for="" class="fw-bold text-decoration-underline">{{ $title }}</h5>
                <table class="table table-bordered " id="bk_stock">
                    <thead>
                        <tr>
                            <th class="dhead">no</th>
                            <th class="dhead">No Nota</th>
                            <th class="dhead">partai</th>
                            <th class="dhead">box grading</th>
                            <th class="dhead">grade</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">ttl rp</th>
                            <th class="dhead text-end">rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sumTtlRp = 0;
                        @endphp
                        @foreach ($query as $d)
                        @php
                            $ttlRp = $d->ttl_rp;
                            $sumTtlRp += $ttlRp;
                            $total = $d->cost_bk + $d->cost_kerja + $d->cost_cu + $d->cost_op;
                        @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $no_nota }}</td>
                                <td>{{ $d->nm_partai }}</td>
                                <td>{{ $d->no_box }}</td>
                                <td>{{ $d->grade }}</td>
                                <td class="text-end">{{ number_format($d->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($d->gr) }}</td>
                                <td class="text-end">{{ number_format($total) }}</td>
                                <td class="text-end">{{ number_format($total / $d->gr) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                   
                    <tfoot>
                        <tr>
                            <th class="dheadstock " colspan="5">Box : {{ count($query) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($query, 'pcs'), 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($query, 'gr'), 0) }}</th>

                            @php
                                $sumTtlRp = sumBk($query, 'cost_bk') + sumBk($query, 'cost_kerja') + sumBk($query, 'cost_cu') + sumBk($query, 'cost_op');
                            @endphp

                            <th class="dheadstock  text-end">{{ number_format($sumTtlRp, 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format($sumTtlRp / sumBk($query, 'gr'), 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </section>
        @section('scripts')
            <script>
                $('#bk_stock').DataTable({
                    "searching": true,
                    scrollY: '500px',
                    scrollX: false,
                    scrollCollapse: true,
                    "autoWidth": true,
                    "paging": false,
                    "info": false
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
