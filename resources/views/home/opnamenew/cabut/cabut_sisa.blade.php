<x-theme.app title="{{ $title }}" table="Y" sizeCard="11">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.opnamenew.nav')
            @include('home.opnamenew.cabut.nav')
            <div class="col-lg-12">
                <h5 for="" class="fw-bold text-decoration-underline">Cabut sisa pengawas</h5>
                <table class="table table-bordered " id="bk_stock">
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
                            {{-- <th class="dhead text-end">cost cu dll</th>
                            <th class="dhead text-end">cost operasional</th> --}}
                            <th class="dhead text-end">ttl rp</th>
                            <th class="dhead text-end">rp/gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($box_stock as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>{{ $b->name }}</td>
                                <td>{{ $b->no_box }}</td>
                                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                <td class="text-end">0</td>
                                {{-- <td class="text-end">0</td>
                                <td class="text-end">0</td> --}}
                                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp / $b->gr, 0) }}</td>
                            </tr>
                        @endforeach


                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dheadstock ">Box : {{ count($box_stock) }}</th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_stock, 'pcs'), 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_stock, 'gr'), 0) }}</th>
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_stock, 'ttl_rp'), 0) }}</th>
                            <th class="dheadstock  text-end">0</th>
                            {{-- <th class="dheadstock  text-end">0</th>
                            <th class="dheadstock  text-end">0</th> --}}
                            <th class="dheadstock  text-end">{{ number_format(sumBk($box_stock, 'ttl_rp'), 0) }}</th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($box_stock, 'ttl_rp') / sumBk($box_stock, 'gr'), 0) }}</th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </section>
        @section('scripts')
            <script>
                $('#bk_stock').DataTable({
                    "searching": true,
                    scrollY: '450px',
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
