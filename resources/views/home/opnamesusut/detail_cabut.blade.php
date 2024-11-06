<h5>Pengawas : {{ $nm_pengawas }} | Tipe : {{ $tipe }}</h5>
<table class="table table-bordered table-hover">
    <thead>

        <tr>
            <th>nama anak</th>
            <th>no box</th>
            <th class="text-end">pcs awal</th>
            <th class="text-end">gr awal</th>
            <th class="text-end">pcs akhir</th>
            <th class="text-end">gr akhir</th>
            <th class="text-end">susut</th>
        </tr>
        <tr>
            <th colspan="2">Total</th>
            <th class="text-end">{{ number_format(sumBk($box_stock, 'pcs'), 0) }}</th>
            <th class="text-end">{{ number_format(sumBk($box_stock, 'gr_awal', 0)) }}</th>
            <th class="text-end">{{ number_format(sumBk($box_stock, 'pcs'), 0) }}</th>
            <th class="text-end">{{ number_format(sumBk($box_stock, 'gr_akhir'), 0) }}</th>
            <th class="text-end">
                {{ number_format((1 - sumBk($box_stock, 'gr_akhir') / sumBk($box_stock, 'gr_awal')) * 100, 0) }}%</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($box_stock as $b)
            <tr>
                <td>{{ $b->nm_anak }}</td>
                <td>{{ $b->no_box }}</td>
                <td class="text-end">{{ $b->pcs }}</td>
                <td class="text-end">{{ $b->gr_awal }}</td>
                <td class="text-end">{{ $b->pcs }}</td>
                <td class="text-end">{{ $b->gr_akhir }}</td>
                @php
                    $susut = (1 - $b->gr_akhir / $b->gr_awal) * 100;
                @endphp
                <td class="text-end {{ $susut > $b->batas_susut ? 'text-danger' : '' }}">
                    {{ number_format((1 - $b->gr_akhir / $b->gr_awal) * 100, 0) }}%</td>
            </tr>
        @endforeach
    </tbody>

</table>
