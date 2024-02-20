<x-theme.app sizeCard="10" title="{{ $title }}">
    <x-slot name="cardHeader">

        @include('home.gradingbj.button_nav')

    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8 mb-3">
                @include('home.gradingbj.nav')
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No Box</th>
                            <th>Grade</th>
                            <th class="text-end">Pcs</th>
                            <th class="text-end">Gr</th>
                            <th class="text-end">Rp Gr</th>
                            <th class="text-end">Ttl Rp</th>
                            <th class="text-end">Pcs Akhir</th>
                            <th class="text-end">Gr Akhir</th>
                            <th class="text-end">Cost Sortir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($box_kecil as $no => $g)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $g->no_box }}</td>
                                <td>{{ $g->grade }}</td>
                                <td class="text-end">{{ $g->pcs }}</td>
                                <td class="text-end">{{ $g->gr }}</td>
                                @php
                                    $ttlRp= $g->rp_gram * $g->gr;
                                @endphp
                                <td class="text-end">{{ number_format($g->rp_gram, 0) }}</td>
                                <td class="text-end">{{ number_format($ttlRp, 0) }}</td>
                                <td class="text-end">{{ number_format($g->pcs_sortir, 0) }}</td>
                                <td class="text-end">{{ number_format($g->gr_sortir, 0) }}</td>
                                <td class="text-end">{{ number_format($g->ttlrp_sortir, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>
    </x-slot>
</x-theme.app>
