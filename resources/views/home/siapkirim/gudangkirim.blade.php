<x-theme.app sizeCard="6" :title="$title">
    <x-slot name="cardHeader">
        {{-- @include('home.gradingbj.button_nav') --}}

        <h6>{{ $title }}</h6>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12 mb-3">
                @include('home.packing.nav', ['name' => 'index'])
            </div>
            <div class="col-lg-12">
                @php
                    $ttl_pcs = 0;
                    $ttl_gr = 0;
                    $ttl_rp = 0;
                @endphp
                @foreach ($gudangkirim as $no => $g)
                    @php
                        $ttl_pcs += $g->pcs - $g->pcs_ambil;
                        $ttl_gr += $g->gr - $g->gr_ambil;
                        $ttl_rp += $g->ttl_rp - $g->ttl_rp_ambil;
                    @endphp
                @endforeach
                <table class="table table-bordered" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Grade</th>
                            <th class="text-end">Pcs <br> {{ number_format($ttl_pcs, 0) }}</th>
                            <th class="text-end">Gram <br> {{ number_format($ttl_gr, 0) }}</th>
                            <th class="text-end">Rp Gram <br>
                                {{ empty($ttl_rp) ? 0 : number_format($ttl_rp / $ttl_gr, 0) }}</th>
                            <th class="text-end">Ttl Rp <br> {{ number_format($ttl_rp, 0) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gudangkirim as $no => $g)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $g->grade }}</td>
                                <td class="text-end">{{ $g->pcs - $g->pcs_ambil }}</td>
                                <td class="text-end">{{ $g->gr - $g->gr_ambil }}</td>
                                <td class="text-end">
                                    @if ($g->ttl_rp - $g->ttl_rp_ambil == 0)
                                        0
                                    @else
                                        {{ number_format(($g->ttl_rp - $g->ttl_rp_ambil) / ($g->gr - $g->gr_ambil), 0) }}
                                    @endif

                                    {{-- {{ $g->ttl_rp - $g->ttl_rp_ambil }}
                                    {{ $g->gr - $g->gr_ambil }} --}}
                                </td>
                                <td class="text-end">{{ number_format($g->ttl_rp - $g->ttl_rp_ambil, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>

    </x-slot>
</x-theme.app>
