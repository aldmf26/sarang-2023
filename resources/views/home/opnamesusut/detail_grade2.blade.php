<h6>Partai : {{ $partai }}</h6>
<br>
<table class="table table-bordered" id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead text-center">Grade</th>
            <th class="dhead text-end">Pcs</th>
            <th class="dhead text-end">Gr</th>
            <th class="dhead text-end">Modal</th>
            <th class="dhead text-end">Cost Operasional</th>
        </tr>
        <tr>
            <th class="dhead text-center">Total</th>
            <th class="dhead text-end">{{ number_format(sumBk($grade, 'pcs')) }}</th>
            <th class="dhead text-end">{{ number_format(sumBk($grade, 'gr')) }}</th>
            <th class="dhead text-end">
                {{ number_format(sumBk($grade, 'cost_bk') + sumBk($grade, 'cost_kerja') + sumBk($grade, 'cost_op'), 0) }}
            </th>
            <th class="dhead text-end">0</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($grade as $g)
            <tr>
                <td class="text-center">{{ $g->grade }}</td>
                <td class="text-end">{{ number_format($g->pcs, 0) }}</td>
                <td class="text-end">{{ number_format($g->gr, 0) }}</td>
                <td class="text-end">{{ number_format($g->cost_bk + $g->cost_kerja + $g->cost_op, 0) }} </td>
                <td class="text-end">0</td>
            </tr>
        @endforeach
    </tbody>
</table>
