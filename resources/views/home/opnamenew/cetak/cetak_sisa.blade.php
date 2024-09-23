<x-theme.app title="{{ $title }}" table="Y" sizeCard="11">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.opnamenew.nav')
            @include('home.opnamenew.cetak.nav')

            <div class="col-lg-12">

                <h5 for="" class="fw-bold text-decoration-underline">{{ $title }}</h5>
                <table class="table table-bordered " id="tableScroll" width="100%">
                    <thead>
                        <tr>
                            <th class="dhead">no</th>
                            <th class="dhead">partai</th>
                            <th class="dhead">pengawas</th>
                            <th class="dhead">no box</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">ttl rp bk</th>
                            <th class="dhead text-end">cost kerja</th>
                            <th class="dhead text-end">ttl rp</th>
                            <th class="dhead text-end">rp/gr</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($query as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>{{ $b->name }}</td>
                                <td>{{ $b->no_box }}</td>
                                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                <td class="text-end">0</td>

                                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp / $b->gr, 0) }}</td>
                            </tr>
                        @endforeach
                        {{-- <tr>
                            <td></td>
                            <td>partai suntik</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="text-end">{{ number_format($suntik->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($suntik->gr, 0) }}</td>
                            <td class="text-end">{{ number_format($suntik->ttl_rp, 0) }}</td>
                            <td class="text-end">0</td>

                            <td class="text-end">{{ number_format($suntik->ttl_rp, 0) }}</td>
                            <td class="text-end">{{ number_format($suntik->ttl_rp / $suntik->gr, 0) }}</td>
                        </tr> --}}

                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dheadstock ">Box : {{ count($query) }}</th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($query, 'pcs'), 0) }}</th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($query, 'gr'), 0) }}</th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($query, 'ttl_rp'), 0) }}
                            </th>
                            <th class="dheadstock  text-end">0</th>

                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($query, 'ttl_rp'), 0) }}
                            </th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($query, 'ttl_rp') / sumBk($query, 'gr'), 0) }}
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </section>
        @section('scripts')
            <script>
                $('#bk_stock').DataTable({
                    "searching": true,
                    scrollY: '200px',
                    scrollX: false,
                    scrollCollapse: true,
                    "autoWidth": true,
                    "paging": false,
                    "info": false
                });
                $('#bk_proses').DataTable({
                    "searching": true,
                    scrollY: '200px',
                    scrollX: false,
                    scrollCollapse: true,
                    "autoWidth": true,
                    "paging": false,
                    "info": false
                });
                $('#bk_selesai').DataTable({
                    "searching": true,
                    scrollY: '200px',
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
