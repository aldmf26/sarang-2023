<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">Partai</th>
            <th class="dhead">Grade</th>
            <th class="text-end dhead">Pcs tidak cetak</th>
            <th class="text-end dhead">Gr tidak cetak</th>
            <th class="text-end dhead">Pcs</th>
            <th class="text-end dhead">Gr</th>
            <th class="text-end dhead">Rp</th>
            <th class="text-end dhead">Pcs akhir</th>
            <th class="text-end dhead">Gr akhir</th>
            <th class="text-end dhead">Cost Rp</th>
            <th class="text-end dhead">Rp/gr</th>
            <th class="text-end dhead">Saldo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $bk->nm_partai }}</td>
            <td>{{ $bk->tipe }}</td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">{{ number_format($bk->pcs_awal, 0) }}</td>
            <td class="text-end">{{ number_format($bk->gr_awal, 0) }}</td>
            <td class="text-end">{{ number_format($bk->ttl_rp, 0) }}</td>


            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">{{ number_format($bk->ttl_rp, 0) }}</td>
            <td class="text-end"></td>
            <td class="text-end"></td>
        </tr>
        <tr>
            <td>Cabut</td>
            <td></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">{{ number_format($cabut->pcs ?? 0, 0) }}</td>
            <td class="text-end">{{ number_format($cabut->gr ?? (0 + $eo->gr ?? 0), 0) }}</td>
            <td class="text-end">{{ number_format(($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0), 0) }}</td>
            <td class="text-end">
                {{ number_format((($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0)) / ($cabut->gr ?? (0 + $eo->gr ?? 0)), 0) }}
            </td>
            <td class="text-end">{{ number_format($bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0), 0) }}</td>
        </tr>
        <tr>
            <td>Cetak</td>
            <td></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">{{ number_format($cetak->pcs_tdk, 0) }}</td>
            <td class="text-end">{{ number_format($cetak->gr_tdk, 0) }}</td>
            <td class="text-end">{{ number_format($cetak->pcs, 0) }}</td>
            <td class="text-end">{{ number_format($cetak->gr, 0) }}</td>
            <td class="text-end">{{ number_format($cetak->ttl_rp, 0) }}</td>
            <td class="text-end">{{ number_format($cetak->ttl_rp / $cetak->gr, 0) }}</td>
            <td class="text-end">
                {{ number_format($cetak->ttl_rp + $bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0), 0) }}
            </td>
        </tr>
        <tr>
            <td>Sortir</td>
            <td></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">{{ number_format($sortir->pcs, 0) }}</td>
            <td class="text-end">{{ number_format($sortir->gr, 0) }}</td>
            <td class="text-end">{{ number_format($sortir->ttl_rp, 0) }}</td>
            <td class="text-end">{{ number_format($sortir->ttl_rp / $sortir->gr, 0) }}</td>
            <td class="text-end">
                {{ number_format($sortir->ttl_rp + $bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) + $cetak->ttl_rp, 0) }}
            </td>
        </tr>
        <tr>
            <td>Grading</td>
            <td></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">{{ number_format($grading->pcs, 0) }}</td>
            <td class="text-end">{{ number_format($grading->gr, 0) }}</td>
            <td class="text-end">{{ number_format($grading->ttl_rp, 0) }}</td>
            <td class="text-end">{{ number_format($grading->ttl_rp / $grading->gr, 0) }}</td>
            <td class="text-end">
                {{ number_format($grading->ttl_rp + $sortir->ttl_rp + $bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) + $cetak->ttl_rp, 0) }}
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th></th>
            <th class="text-end">{{ number_format($bk->pcs_awal, 0) }}</th>
            <th class="text-end">{{ number_format($bk->gr_awal, 0) }}</th>
            <th class="text-end">{{ number_format($bk->ttl_rp, 0) }}</th>

            <th class="text-end"></th>
            <th class="text-end"></th>

            <th class="text-end">{{ number_format($grading->pcs, 0) }}</th>
            <th class="text-end">{{ number_format($grading->gr, 0) }}</th>
            <th class="text-end">
                @php
                    $ttl2 =
                        $sortir->ttl_rp +
                        $grading->ttl_rp +
                        ($cabut->ttl_rp ?? 0) +
                        ($eo->ttl_rp ?? 0) +
                        $cetak->ttl_rp;
                @endphp
                {{ number_format($ttl2, 0) }}
            </th>
            <th class="text-end">
                {{ number_format($ttl2 / $grading->gr, 0) }}
            </th>
            <th class="text-end">
                {{ number_format($grading->ttl_rp + $sortir->ttl_rp + $bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) + $cetak->ttl_rp, 0) }}
            </th>
        </tr>
    </tfoot>
</table>
