<x-theme.app title="{{ $title }}" table="Y" sizeCard="11">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.opnamenew.nav')
            @include('home.opnamenew.cabut.nav')

            <div class="col-lg-12">
                <h5 for="" class="fw-bold text-decoration-underline">{{ $title }}</h5>
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

                        @foreach ($cabut_awal as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $b->nm_partai }}</td>
                                <td>{{ $b->name }}</td>
                                <td>{{ $b->no_box }}</td>
                                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr_awal, 0) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                <td class="text-end">0</td>
                                {{-- <td class="text-end">0</td>
                                <td class="text-end">0</td> --}}
                                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                                <td class="text-end">{{ number_format($b->ttl_rp / $b->gr_awal, 0) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td>partai suntik</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="text-end">{{ number_format($a11suntik->pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($a11suntik->gr, 0) }}</td>
                            <td class="text-end">{{ number_format($a11suntik->ttl_rp, 0) }}</td>
                            <td class="text-end">0</td>
                            {{-- <td class="text-end">0</td>
                            <td class="text-end">0</td> --}}
                            <td class="text-end">{{ number_format($a11suntik->ttl_rp, 0) }}</td>
                            <td class="text-end">{{ number_format($a11suntik->ttl_rp / $a11suntik->gr, 0) }}</td>
                        </tr>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="dheadstock ">Box : {{ count($cabut_awal) }}</th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock "></th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($cabut_awal, 'pcs') + $a11suntik->pcs, 0) }}</th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($cabut_awal, 'gr_awal') + $a11suntik->gr, 0) }}</th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($cabut_awal, 'ttl_rp') + $a11suntik->ttl_rp, 0) }}</th>
                            <th class="dheadstock  text-end">0</th>
                            {{-- <th class="dheadstock  text-end">0</th>
                            <th class="dheadstock  text-end">0</th> --}}
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($cabut_awal, 'ttl_rp') + $a11suntik->ttl_rp, 0) }}</th>
                            <th class="dheadstock  text-end">
                                {{ number_format(sumBk($cabut_awal, 'ttl_rp') / sumBk($cabut_awal, 'gr_awal'), 0) }}
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
