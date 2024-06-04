<x-theme.app sizeCard="6">
    <x-slot name="cardHeader">
        @include('home.gradingbj.button_nav')


    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12 mb-3">
                @include('home.gradingbj.nav')
            </div>
            <div class="col-lg-12">
                @php
                    $ttl_pcs = 0;
                    $ttl_gr = 0;
                    $ttl_rp = 0;
                @endphp
                @foreach ($gudangbj as $no => $g)
                    @php
                        $ttl_pcs += $g->pcs - $g->pcs_kredit;
                        $ttl_gr += $g->gr - $g->gr_kredit;
                        $ttl_rp += $g->ttl_rp - $g->ttl_rp_kredit;
                    @endphp
                @endforeach
                <table class="table table-bordered" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Grade</th>
                            <th class="text-end">Pcs <br> {{ number_format($ttl_pcs, 0) }}</th>
                            <th class="text-end">Gram <br> {{ number_format($ttl_gr, 0) }}</th>
                            <th class="text-end">Rp Gram <br> </th>
                            <th class="text-end">Ttl Rp <br> {{ number_format($ttl_rp, 0) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gudangbj as $no => $g)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $g->grade }}</td>
                                <td class="text-end">{{ $g->pcs - $g->pcs_kredit }}</td>
                                <td class="text-end">{{ $g->gr - $g->gr_kredit }}</td>
                                <td class="text-end">
                                    {{ number_format(($g->ttl_rp - $g->ttl_rp_kredit) / ($g->gr - $g->gr_kredit), 0) }}
                                    {{-- {{ $g->ttl_rp - $g->ttl_rp_kredit }}
                                    {{ $g->gr - $g->gr_kredit }} --}}
                                </td>
                                <td class="text-end">{{ number_format($g->ttl_rp - $g->ttl_rp_kredit, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>
    </x-slot>
</x-theme.app>
