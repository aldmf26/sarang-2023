<table class="table table-bordered table-hover" id="tableHalamanCabut">
    <thead>
        <tr>
            <th class="dhead">nama anak</th>
            <th class="dhead">nama pengawas</th>
            <th class="dhead">no box</th>
            <th class="text-end dhead">pcs awal</th>
            <th class="text-end dhead">gr awal</th>
            <th class="text-end dhead">pcs akhir</th>
            <th class="text-end dhead">gr akhir</th>
            <th class="text-end dhead">susut</th>
            <th class="text-end dhead">Modal bk</th>
            <th class="text-end dhead">Cost Kerja</th>
        </tr>
        <tr>
            <th colspan="3" class="dhead">Total</th>
            <th class="text-end dhead">{{ number_format(sumBk($box_stock, 'pcs_awal'), 0) }}</th>
            <th class="text-end dhead">{{ number_format(sumBk($box_stock, 'gr_awal', 0)) }}</th>
            <th class="text-end dhead">{{ number_format(sumBk($box_stock, 'pcs_akhir'), 0) }}</th>
            <th class="text-end dhead">{{ number_format(sumBk($box_stock, 'gr_akhir'), 0) }}</th>
            <th class="text-end dhead">
                {{ number_format((1 - sumBk($box_stock, 'gr_akhir') / sumBk($box_stock, 'gr_awal')) * 100, 0) }}%</th>
            <th class="text-end dhead">{{ number_format(sumBk($box_stock, 'modal_rp'), 0) }}</th>
            <th class="text-end dhead">{{ number_format(sumBk($box_stock, 'cost_kerja'), 0) }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($box_stock as $b)
            <tr>
                <td>{{ $b->name }}</td>
                <td>{{ $b->nama }}</td>
                <td>{{ $b->no_box }}</td>
                <td class="text-end">{{ $b->pcs_awal }}</td>
                <td class="text-end">{{ $b->gr_awal }}</td>
                <td class="text-end">{{ $b->pcs_akhir }}</td>
                <td class="text-end">{{ $b->gr_akhir }}</td>
                @php
                    $susut = (1 - $b->gr_akhir / $b->gr_awal) * 100;
                @endphp
                <td class="text-end ">
                    {{ number_format((1 - $b->gr_akhir / $b->gr_awal) * 100, 0) }}%</td>
                <td class="text-end">{{ number_format($b->modal_rp, 0) }}</td>
                <td class="text-end">{{ number_format($b->cost_kerja, 0) }}</td>
            </tr>
        @endforeach
    </tbody>

</table>
