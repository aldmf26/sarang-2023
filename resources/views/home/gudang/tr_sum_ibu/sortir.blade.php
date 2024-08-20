<tbody>
    <tr>
        <th class="bg-primary text-white" colspan="17">Sortir</th>
    </tr>

    <tr class="pointer detail">
        <td>Awal</td>
        <td>sortir opname</td>

        <td>{{ number_format($s1pcs, 0) }}</td>
        <td>{{ number_format($s1gr, 0) }}</td>
        <td>{{ number_format($s1ttlrp, 0) }}</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor
    </tr>
    <tr class="pointer detail">
        <td>Awal</td>
        <td>sortir stock awal</td>
        <td>{{ number_format($s2pcs, 0) }}</td>
        <td>{{ number_format($s2gr, 0) }}</td>
        <td>{{ number_format($s2ttlrp, 0) }}</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor
    </tr>

    <tr class="pointer detail">
        <td>Opname</td>
        <td>sortir sedang proses</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($s3pcs, 0) }}</td>
        <td>{{ number_format($s3gr, 0) }}</td>
        <td>{{ number_format($s3ttlrp, 0) }}</td>
    </tr>
    <tr class="pointer detail">
        <td>Opname</td>
        <td>sortir selesai siap grading belum serah</td>
        @for ($i = 0; $i < 6; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($s4pcs, 0) }}</td>
        <td>{{ number_format($s4gr, 0) }}</td>
        <td>{{ number_format($s4ttlrp, 0) }}</td>
        <td>{{ number_format($s4cost_kerja, 0) }}</td>
        <td>{{ number_format($s4op, 0) }}</td>
        <td>{{ number_format($s4dll, 0) }}</td>
        @for ($i = 0; $i < 3; $i++)
            <td></td>
        @endfor

    </tr>
    <tr class="pointer detail">
        <td>Proses</td>
        <td>sortir selesai siap grading diserahkan</td>
        @for ($i = 0; $i < 6; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($s5pcs, 0) }}</td>
        <td>{{ number_format($s5gr, 0) }}</td>
        <td>{{ number_format($s5ttlrp, 0) }}</td>
        <td>{{ number_format($s5cost_kerja, 0) }}</td>
        <td>{{ number_format($s5op, 0) }}</td>
        <td>{{ number_format($s5dll, 0) }}</td>
        @for ($i = 0; $i < 3; $i++)
            <td></td>
        @endfor


    </tr>
    <tr class="pointer detail">
        <td>Opname</td>
        <td>sortir sisa pgws</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor

        <td>{{ number_format($s6pcs, 0) }}</td>
        <td>{{ number_format($s6gr, 0) }}</td>
        <td>{{ number_format($s6ttlrp, 0) }}</td>
    </tr>
    <tr>
        <td></td>
        <th>Total</th>
        @php
            $cpcs = $s3pcs + $s6pcs;
            $cgr = $s3gr + $s6gr;
            $cttlrp = $s3ttlrp + $s6ttlrp;

            $b2pcs = $s4pcs + $s5pcs;
            $b2gr =  $s4gr + $s5gr;
            $b2ttlrp =  $s4ttlrp + $s5ttlrp;

            $cost_kerja = $s5cost_kerja;
            $op = $s5op;
            $dll = $s5dll;

            $sumTtl = [
                'apcs' => $s1pcs + $s2pcs,
                'agr' => $s1gr + $s2gr,
                'attlrp' => $s1ttlrp + $s2ttlrp,

                'bpcs' => ($s1pcs + $s2pcs) - $cpcs,
                'bgr' => ($s1gr + $s2gr) - $cgr,
                'battlrp' => ($s1ttlrp + $s2ttlrp) - $cttlrp,

                'b2pcs' => $b2pcs,
                'b2gr' => $b2gr,
                'b2ttlrp' => $b2ttlrp,

                'cost_kerja' => $cost_kerja,
                'cost_op' => $op,
                'cost_dl' => $dll,

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
            $susut = (1 - $b2gr / (($s1gr + $s2gr) - $cgr)) * 100;
        @endphp
        <td></td>
        <th>Susut</th>
        @for ($i = 0; $i <= 14; $i++)
            <th>{{ $i == 7 ? number_format($susut, 1) . '%' : '' }}</th>
        @endfor
    </tr>
</tbody>