<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')
            <div class="col-lg-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead text-center">Nama Partai</th>
                            <th class="dhead text-center">Tipe</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Modal</th>
                            <th class="dhead text-end">Cabut</th>
                            <th class="dhead text-end">Cetak</th>
                            <th class="dhead text-end">Sp</th>
                            <th class="dhead text-end">Cu</th>
                            <th class="dhead text-end">Cost Operasioanal</th>
                            <th class="dhead text-end">Total Rp</th>
                            <th class="dhead text-end">Rp/gr</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($global as $g)
                            <tr>
                                <td>{{ $g->nm_partai }}</td>
                                <td>{{ $g->grade }}</td>
                                <td class="text-end">{{ number_format($g->pcs_bk, 0) }}</td>
                                <td class="text-end">{{ number_format($g->gr_bk, 0) }}</td>
                                <td class="text-end">{{ number_format($g->cost_bk, 0) }}</td>
                                <td class="text-end">{{ number_format($g->cabut, 0) }}</td>
                                <td class="text-end">{{ number_format($g->cetak, 0) }}</td>
                                <td class="text-end">{{ number_format($g->sortir, 0) }}</td>
                                <td class="text-end">{{ number_format($g->cu, 0) }}</td>
                                <td class="text-end">{{ number_format($g->cost_op, 0) }}</td>
                                <td class="text-end">
                                    {{ number_format($g->cost_bk + $g->cabut + $g->cetak + $g->sortir + $g->cu + $g->cost_op, 0) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format(($g->cost_bk + $g->cabut + $g->cetak + $g->sortir + $g->cu + $g->cost_op) / $g->gr_bk, 1) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>
        @section('scripts')
        @endsection
    </x-slot>
</x-theme.app>
