<tbody>
    <tr>
        <th class="bg-primary text-white" colspan="17">Cetak</th>
    </tr>

    <tr class="pointer detail">
        <td>Awal</td>
        <td>cetak opname</td>

        <td>{{ number_format($ca11pcs, 0) }}</td>
        <td>{{ number_format($ca11gr, 0) }}</td>
        <td>{{ number_format($ca11ttlrp, 0) }}</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor

    </tr>
    <tr class="pointer detail">
        <td>Awal</td>
        <td>cetak stock awal</td>
        <td>{{ number_format($ca12pcs, 0) }}</td>
        <td>{{ number_format($ca12gr, 0) }}</td>
        <td>{{ number_format($ca12ttlrp, 0) }}</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor
    </tr>

    <tr class="pointer detail">
        <td>Opname</td>
        <td>cetak sedang proses</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($ca13pcs, 0) }}</td>
        <td>{{ number_format($ca13gr, 0) }}</td>
        <td>{{ number_format($ca13ttlrp, 0) }}</td>
    </tr>
    <tr class="pointer detail">
        <td>Opname</td>
        <td>cetak selesai siap sortir belum serah</td>
        @for ($i = 0; $i < 6; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($ca14pcs, 0) }}</td>
        <td>{{ number_format($ca14gr, 0) }}</td>
        <td>{{ number_format($ca14ttlrp, 0) }}</td>
        <td>{{ number_format($ca14costkerja, 0) }}</td>
        <td>{{ number_format($ca14op, 0) }}</td>
        <td>{{ number_format($ca14dll, 0) }}</td>

        @for ($i = 0; $i < 3; $i++)
            <td></td>
        @endfor

    </tr>
    <tr class="pointer detail">
        <td>Proses</td>
        <td>tidak cetak  diserahkan</td>
        @for ($i = 0; $i < 6; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($ca15pcs, 0) }}</td>
        <td>{{ number_format($ca15gr, 0) }}</td>
        <td>{{ number_format(0, 0) }}</td>
        <td>{{ number_format(0, 0) }}</td>
        <td>{{ number_format($ca15op, 0) }}</td>
        <td>{{ number_format($ca15dll, 0) }}</td>
        @for ($i = 0; $i < 3; $i++)
            <td></td>
        @endfor
    </tr>
    <tr class="pointer detail">
        <td>Proses</td>
        <td>cetak selesai siap sortir diserahkan</td>
        @for ($i = 0; $i < 6; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($ca16pcs, 0) }}</td>
        <td>{{ number_format($ca16gr, 0) }}</td>
        <td>{{ number_format($ca16ttlrp, 0) }}</td>
        <td>{{ number_format($ca16costkerja, 0) }}</td>
        <td>{{ number_format($ca16op, 0) }}</td>
        <td>{{ number_format($ca16dll, 0) }}</td>
        @for ($i = 0; $i < 3; $i++)
            <td></td>
        @endfor


    </tr>
    <tr class="pointer detail">
        <td>Opname</td>
        <td>cetak sisa pgws</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor

        <td>{{ number_format($ca17pcs, 0) }}</td>
        <td>{{ number_format($ca17gr, 0) }}</td>
        <td>{{ number_format($ca17ttlrp, 0) }}</td>
    </tr>
    <tr>
        <td></td>
        <th>Total</th>
        @php
            $cpcs = $ca13pcs + $ca17pcs;
            $cgr = $ca13gr + $ca17gr;
            $cttlrp = $ca13ttlrp + $ca17ttlrp;

            $b2pcs = $ca14pcs + $ca15pcs + $ca16pcs;
            $b2gr =  $ca14gr + $ca15gr + $ca16gr;
            $b2ttlrp =  $ca14ttlrp + $ca16ttlrp;

            $cost_kerja = $ca14costkerja + $ca16costkerja;
            $costOp = $ca14op + $ca16op;
            $costDll = $ca14dll + $ca16dll;

            $sumTtl = [
                'apcs' => $ca11pcs + $ca12pcs,
                'agr' => $ca11gr + $ca12gr,
                'attlrp' => $ca11ttlrp + $ca12ttlrp,

                'bpcs' => ($ca11pcs + $ca12pcs) - $cpcs,
                'bgr' => ($ca11gr + $ca12gr) - $cgr,
                'battlrp' => ($ca11ttlrp + $ca12ttlrp) - $cttlrp,

                'b2pcs' => $b2pcs,
                'b2gr' => $b2gr,
                'b2ttlrp' => $b2ttlrp,

                'cost_kerja' => $cost_kerja,
                'cost_op' => $costOp,
                'cost_dl' => $costDll,

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
            $susut = (1 - $b2gr / (($ca11gr + $ca12gr) - $cgr)) * 100;
        @endphp
        <td></td>
        <th>Susut</th>
        @for ($i = 0; $i <= 14; $i++)
            <th>{{ $i == 7 ? number_format($susut, 1) . '%' : '' }}</th>
        @endfor
    </tr>
</tbody>