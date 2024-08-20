<tbody>
    <tr>
        <th class="bg-primary text-white" colspan="17">Cabut</th>
    </tr>

    <tr class="pointer detail" index="11">
        <td>Awal</td>
        <td>box stock awal bk</td>

        <td>{{ number_format($a11pcs, 0) }}</td>
        <td>{{ number_format($a11gr, 0) }}</td>
        <td>{{ number_format($a11ttlrp, 0) }}</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor

    </tr>
    <tr class="pointer detail" index="12">
        <td>Opname</td>
        <td>box stock cabut sedang proses</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($a12pcs, 0) }}</td>
        <td>{{ number_format($a12gr, 0) }}</td>
        <td>{{ number_format($a12ttlrp, 0) }}</td>
    </tr>

    <tr class="pointer detail" index="13">
        <td>Opname</td>
        <td>box selesai cabut siap cetak belum serah</td>
        @for ($i = 0; $i < 6; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($a13pcs, 0) }}</td>
        <td>{{ number_format($a13gr, 0) }}</td>
        <td>{{ number_format($a13ttlrp, 0) }}</td>
        <td>{{ number_format($a13costkerja, 0) }}</td>
        <td>{{ number_format($a13op, 0) }}</td>
        <td>{{ number_format($a13dll, 0) }}</td>
        @for ($i = 0; $i < 3; $i++)
            <td></td>
        @endfor
    </tr>
    <tr class="pointer detail" index="14">
        <td>Proses</td>
        <td>box selesai cabut siap cetak diserahkan</td>
        @for ($i = 0; $i < 6; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($a14pcs, 0) }}</td>
        <td>{{ number_format($a14gr, 0) }}</td>
        <td>{{ number_format($a14ttlrp, 0) }}</td>
        <td>{{ number_format($a14costkerja, 0) }}</td>
        <td>{{ number_format($a14op, 0) }}</td>
        <td>{{ number_format($a14dll, 0) }}</td>
        @for ($i = 0; $i < 3; $i++)
            <td></td>
        @endfor

    </tr>
    <tr class="pointer detail" index="15">
        <td>Opname</td>
        <td>box selesai cbt siap sortir belum serah</td>
        @for ($i = 0; $i < 7; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($a15gr, 0) }}</td>
        <td>{{ number_format($a15ttlrp, 0) }}</td>
        <td>{{ number_format($a15costkerja, 0) }}</td>
        <td>{{ number_format($a15op, 0) }}</td>
        <td>{{ number_format($a15dll, 0) }}</td>
        @for ($i = 0; $i < 3; $i++)
            <td></td>
        @endfor
    </tr>
    <tr class="pointer detail" index="16">
        <td>Proses</td>
        <td>box selesai cbt siap sortir diserahkan</td>
        @for ($i = 0; $i < 7; $i++)
            <td></td>
        @endfor
        <td>{{ number_format($a16gr, 0) }}</td>
        <td>{{ number_format($a16ttlrp, 0) }}</td>
        <td>{{ number_format($a16costkerja, 0) }}</td>
        <td>{{ number_format($a16op, 0) }}</td>
        <td>{{ number_format($a16dll, 0) }}</td>
        @for ($i = 0; $i < 3; $i++)
            <td></td>
        @endfor


    </tr>
    <tr class="pointer detail" index="17">
        <td>Opname</td>
        <td>box cbt sisa pgws</td>
        @for ($i = 0; $i < 12; $i++)
            <td></td>
        @endfor

        <td>{{ number_format($a17pcs, 0) }}</td>
        <td>{{ number_format($a17gr, 0) }}</td>
        <td>{{ number_format($a17ttlrp, 0) }}</td>
    </tr>
    <tr>
        <td></td>
        <th>Total</th>
        @php
            $cpcs = $a12pcs + $a17pcs;
            $cgr = $a12gr + $a17gr;
            $cttlrp = $a12ttlrp + $a17ttlrp;

            $b2pcs = $a13pcs + $a14pcs;
            $b2gr = $a13gr + $a14gr + $a15gr + $a16gr;
            $b2ttlrp = $a13ttlrp + $a14ttlrp + $a15ttlrp + $a16ttlrp;

            $cost_kerja = $a13costkerja + $a14costkerja + $a15costkerja + $a16costkerja;
            $ttlOp = $a13op + $a14op + $a15op + $a16op;
            $ttlDll = $a13dll + $a14dll + $a15dll + $a16dll;

            $sumTtl = [
                'apcs' => $a11pcs,
                'agr' => $a11gr,
                'attlrp' => $a11ttlrp,

                'bpcs' => $a11pcs - $cpcs,
                'bgr' => $a11gr - $cgr,
                'battlrp' => $a11ttlrp - $cttlrp,

                'b2pcs' => $b2pcs,
                'b2gr' => $b2gr,
                'b2ttlrp' => $b2ttlrp,

                'cost_kerja' => $cost_kerja,
                'cost_op' => $ttlOp,
                'cost_dl' => $ttlDll,

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
            $susut = (1 - $b2gr / ($a11gr - $cgr)) * 100;
        @endphp
        <td></td>
        <th>Susut</th>
        @for ($i = 0; $i <= 14; $i++)
            <th>{{ $i == 7 ? number_format($susut, 0) . '%' : '' }}</th>
        @endfor
    </tr>
</tbody>