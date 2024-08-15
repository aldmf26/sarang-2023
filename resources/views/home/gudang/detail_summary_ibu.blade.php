<table class="table table-border table-hover" id="tblSummary">
    @php
        $dhead = 'dhead';
    @endphp
    <thead>
        <tr>
            <th class="{{ $dhead }}">Pemilik</th>
            <th class="{{ $dhead }}">Partai</th>
            <th class="{{ $dhead }}">No Box</th>
            <th class="{{ $dhead }} text-end">Pcs</th>
            <th class="{{ $dhead }} text-end">Gr</th>
            <th class="{{ $dhead }} text-end">Rp/gr</th>
            <th class="{{ $dhead }} text-end">Cost Bk</th>
            {{-- <th class="{{$dhead}}">cost kerja</th>
            <th class="{{$dhead}}">cost op</th>
            <th class="{{$dhead}}">cost cu dll denda</th> --}}
        </tr>
    </thead>
    <thead>
        <tr>
            <th colspan="3"></th>
            <th class="text-end">{{ number_format(sumCol($datas, 'pcs'), 0) }}</th>
            <th class="text-end">{{ number_format(sumCol($datas, 'gr'), 0) }}</th>
            <th></th>
            <th class="text-end">{{ number_format(sumCol($datas, 'ttl_rp'), 0) }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $d)
            <tr>
                <td>{{ $d->name }}</td>
                <td>{{ $d->nm_partai }}</td>
                <td>{{ $d->no_box }}</td>
                <td align="right">{{ $d->pcs }}</td>
                <td align="right">{{ $d->gr }}</td>
                <td align="right">
                    {{ number_format(round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $d->gr, 0), 0) }}</td>
                <td align="right">{{ number_format(round($d->ttl_rp, 0), 0) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
