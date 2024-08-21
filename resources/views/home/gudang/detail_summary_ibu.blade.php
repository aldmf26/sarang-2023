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
        @php
            $dataBnyk = is_array($datas);
            if ($dataBnyk) {
                if ($suntikan) {
                    $ttlPcs = sumCol($datas, 'pcs') + $suntikan->pcs;
                    $ttlGr = sumCol($datas, 'gr') + $suntikan->gr;
                    $ttlTtl_rp = sumCol($datas, 'ttl_rp') + $suntikan->ttl_rp;
                } else {
                    $ttlPcs = sumCol($datas, 'pcs');
                    $ttlGr = sumCol($datas, 'gr');
                    $ttlTtl_rp = sumCol($datas, 'ttl_rp');
                }
            }

        @endphp
        @if ($dataBnyk)
            <tr>
                <th colspan="3"></th>
                <th class="text-end">{{ number_format($ttlPcs, 0) }}</th>
                <th class="text-end">{{ number_format($ttlGr, 0) }}</th>
                <th></th>
                <th class="text-end">{{ number_format($ttlTtl_rp, 0) }}</th>
            </tr>
        @endif
    </thead>
    <tbody>
        @if ($suntikan)
            <tr>
                <td>-</td>
                <td>suntikan</td>
                <td></td>
                <td align="right">{{ number_format($suntikan->pcs ?? 0, 0) }}</td>
                <td align="right">{{ number_format($suntikan->gr ?? 0, 0) }}</td>
                <td align="right">0</td>
                <td align="right">{{ number_format($suntikan->ttl_rp ?? 0, 0) }}</td>
            </tr>
        @endif
        @if ($index != 21)
            @if (!$dataBnyk)
                <tr>
                    <td>-</td>
                    <td>suntikan</td>
                    <td></td>
                    <td align="right">{{ $datas->pcs ?? '' }}</td>
                    <td align="right">{{ $datas->gr ?? 0 }}</td>
                    <td align="right">0</td>
                    <td align="right">{{ number_format($datas->ttl_rp ?? 0, 0) }}</td>
                </tr>
            @else
                @foreach ($datas as $d)
                    @php
                        $gr = $d->gr ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $d->name }}</td>
                        <td>{{ $d->nm_partai }}</td>
                        <td>{{ $d->no_box }}</td>
                        <td align="right">{{ $d->pcs ?? '' }}</td>
                        <td align="right">{{ $gr }}</td>
                        <td align="right">
                            {{ number_format($gr == 0 ? 0 : round($d->ttl_rp == 0 ? 0 : ($d->ttl_rp + $d->cost_cu) / $gr, 0), 0) }}
                        </td>
                        <td align="right">{{ number_format(round($d->ttl_rp, 0), 0) }}</td>
                    </tr>
                @endforeach
            @endif
        @endif

    </tbody>
</table>
