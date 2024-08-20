<tbody>
    <tr>
        <th class="bg-primary text-white" colspan="17">Pengiriman</th>
    </tr>

    <tr class="pointer detail">
        <td>Awal</td>
        <td>sortir opname</td>

        <td>{{ number_format($p1pcs, 0) }}</td>
        <td>{{ number_format($p1gr, 0) }}</td>
        <td>{{ number_format($p1ttlrp, 0) }}</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor
    </tr>
    <tr class="pointer detail">
        <td>Awal</td>
        <td>sortir stock awal</td>
        <td>{{ number_format($p2pcs, 0) }}</td>
        <td>{{ number_format($p2gr, 0) }}</td>
        <td>{{ number_format($p2ttlrp, 0) }}</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor
    </tr>

    <tr class="pointer detail">
        <td class="pink-magenta  text-white">Opname</td>
        <td>sortir sedang proses</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($p3pcs, 0) }}</td>
        <td>{{ number_format($p3gr, 0) }}</td>
        <td>{{ number_format($p3ttlrp, 0) }}</td>
    </tr>
    
    <tr class="pointer detail">
        <td class="pink-magenta  text-white">Opname</td>
        <td>sortir selesai siap grading diserahkan</td>
        @for ($i = 0; $i < 6; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($p4pcs, 0) }}</td>
        <td>{{ number_format($p4gr, 0) }}</td>
        <td>{{ number_format($p4ttlrp, 0) }}</td>
        <td>{{ number_format(0, 0) }}</td>
        @for ($i = 0; $i < 5; $i++)
            <td></td>
        @endfor
    </tr>
    
    <tr>
        <td></td>
        <th>Total</th>
        @php
            $cpcs = $p3pcs;
            $cgr = $p3gr;
            $cttlrp = $p3ttlrp;

            $b2pcs = $p4pcs;
            $b2gr =  $p4gr;
            $b2ttlrp =  $p4ttlrp;

            $cost_kerja = 0;

            $sumTtl = [
                'apcs' => $p1pcs + $p2pcs,
                'agr' => $p1gr+ $p2gr,
                'attlrp' => $p1ttlrp + $p2ttlrp,

                'bpcs' => ($p1pcs + $p2pcs) - $cpcs,
                'bgr' => ($p1gr + $p2gr) - $cgr,
                'battlrp' => ($p1ttlrp + $p2ttlrp) - $cttlrp,

                'b2pcs' => $b2pcs,
                'b2gr' => $b2gr,
                'b2ttlrp' => $b2ttlrp,

                'cost_kerja' => $cost_kerja,
                'cost_op' => 0,
                'cost_dl' => 0,

                'cpcs' => $cpcs,
                'cgr' => $cgr,
                'cttlrp' => $cttlrp,
            ];
        @endphp
        @foreach ($sumTtl as $i => $d)
            <th>
                {{ number_format($d, 0) }}
            </th>
        @endforeach
    </tr>
    <tr>
        @php
            $susut = (1 - $b2gr / (($p1gr + $p2gr) - $cgr)) * 100;
        @endphp
        <td></td>
        <th>Susut</th>
        @for ($i = 0; $i <= 14; $i++)
            <th>{{ $i == 7 ? number_format($susut, 1) . '%' : '' }}</th>
        @endfor
    </tr>
</tbody>