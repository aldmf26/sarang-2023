<table class="table table-bordered table-hover" id="tableHalamanbk">
    <thead>
        <tr>
            <th class="dhead">nama pengawas</th>
            <th class="dhead">no box</th>
            <th class="text-end dhead">pcs awal</th>
            <th class="text-end dhead">gr awal</th>
            <th class="text-end dhead">Modal bk</th>
        </tr>
        <tr>
            <th colspan="2" class="dhead">Total</th>
            <th class="text-end dhead">{{ number_format(sumBk($box_stock, 'pcs_awal'), 0) }}</th>
            <th class="text-end dhead">{{ number_format(sumBk($box_stock, 'gr_awal', 0)) }}</th>
            <th class="text-end dhead">{{ number_format(sumBk($box_stock, 'ttl_rp'), 0) }}</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($box_stock as $b)
            <tr>
                <td>{{ $b->name }}</td>
                <td>{{ $b->no_box }}</td>
                <td class="text-end">{{ $b->pcs_awal }}</td>
                <td class="text-end">{{ $b->gr_awal }}</td>
                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
            </tr>
        @endforeach
    </tbody>

</table>
