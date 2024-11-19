<h6>Partai : {{ $partai }}</h6>
<br>
<table class="table table-bordered" id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead text-center">Grade</th>
            <th class="dhead text-end">Pcs</th>
            <th class="dhead text-end">Gr</th>
        </tr>
        <tr>
            <th class="dhead text-center">Total</th>
            <th class="dhead text-end">{{ number_format(sumBk($grade, 'pcs')) }}</th>
            <th class="dhead text-end">{{ number_format(sumBk($grade, 'gr')) }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($grade as $g)
            <tr>
                <td class="text-center">{{ $g->grade }}</td>
                <td class="text-end">{{ number_format($g->pcs, 0) }}</td>
                <td class="text-end">{{ number_format($g->gr, 0) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
