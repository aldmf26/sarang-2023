<x-theme.app title="{{ $title }}" table="Y" sizeCard="9">
    <x-slot name="cardHeader">
        <h6 class="mt-1">{{ $title }}</h6>

        <h6>Filter Bulan :</h6>
        <div class="d-flex gap-2">
            @foreach ($dataBulan as $d)
                <div>
                    <a href="{{ route('cocokan.balance.cost', ['bulan' => $d->bulan, 'tahun' => $d->tahun]) }}"
                        class="btn btn-sm {{ $d->bulan == $bulan ? 'btn-info' : '' }}">{{ formatTglGaji($d->bulan, $d->tahun) }}</a>
                </div>
            @endforeach
        </div>

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-12">
                <table class="table table-bordered" id="table1">
                    <thead>
                        <tr>
                            <th class="dhead">Bulan</th>
                            <th class="dhead">Box Grading</th>
                            <th class="dhead">Grade</th>
                            <th class="dhead text-end">Pcs</th>
                            <th class="dhead text-end">Gr</th>
                            <th class="dhead text-end">Cost Bk</th>
                            <th class="dhead text-end">Cost Op</th>
                            <th class="dhead text-end">Ttl</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th class="bg-info text-white">Total</th>
                            <th class=" bg-info text-white">{{ count($grading) }} Box</th>
                            <th class=" bg-info text-white"></th>
                            <th class="text-end bg-info text-white">{{ number_format(sumCol($grading, 'pcs'), 0) }}</th>
                            <th class="text-end bg-info text-white">{{ number_format(sumCol($grading, 'gr'), 0) }}</th>
                            <th class="text-end bg-info text-white">{{ number_format(sumCol($grading, 'cost_bk'), 0) }}
                            </th>
                            <th class="text-end bg-info text-white">{{ number_format(sumCol($grading, 'cost_op'), 0) }}
                            </th>
                            <th class="text-end bg-info text-white">
                                {{ number_format(sumCol($grading, 'cost_bk') + sumCol($grading, 'cost_op'), 0) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grading as $d)
                            <tr>
                                <td>{{ formatTglGaji($d->bulan, $d->tahun) }}</td>
                                <td>P{{ $d->box_grading }}</td>
                                <td>{{ strtoupper($d->grade) }}</td>
                                <td class="text-end">{{ number_format($d->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($d->gr, 0) }}</td>
                                <td class="text-end">{{ number_format($d->cost_bk, 0) }}</td>
                                <td class="text-end">{{ number_format($d->cost_op, 0) }}</td>
                                <td class="text-end">{{ number_format($d->cost_bk + $d->cost_op, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

    </x-slot>
</x-theme.app>
