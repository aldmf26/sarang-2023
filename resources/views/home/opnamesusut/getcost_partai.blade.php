<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">Partai</th>
            <th class="dhead">Grade</th>
            <th class="text-end dhead">Pcs</th>
            <th class="text-end dhead">Gr</th>
            <th class="text-end dhead">Rp</th>
            <th class="text-end dhead">Pcs tidak cetak</th>
            <th class="text-end dhead">Gr tidak cetak</th>
            <th class="text-end dhead">Pcs akhir</th>
            <th class="text-end dhead">Gr akhir</th>
            <th class="text-end dhead">Susut%</th>
            <th class="text-end dhead">Cost Rp</th>
            <th class="text-end dhead">Rp/gr</th>
            {{-- <th class="text-end dhead">Saldo</th> --}}
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $bk->nm_partai }}</td>
            <td>{{ $bk->tipe }}</td>
            <td class="text-end">{{ number_format($bk->pcs_awal, 0) }}</td>
            <td class="text-end">{{ number_format($bk->gr_awal, 0) }}</td>
            <td class="text-end">{{ number_format($bk->ttl_rp, 0) }}</td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">{{ number_format($bk->ttl_rp, 0) }}</td>
            <td class="text-end">{{ empty($bk->ttl_rp) ? 0 : number_format($bk->ttl_rp / $bk->gr_awal, 0) }}</td>
            {{-- <td class="text-end"></td> --}}
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
            @php
                $gr_awal_cabut = $cabut->gr_awal ?? 0;
                $gr_akhir_cabut = $cabut->gr ?? 0;
                $gr_eo = $eo->gr ?? 0;
                $gr_eo_awal = $eo->gr_eo_awal ?? 0;
                $sstCabut = 
            @endphp
            <td class="text-end">{{ number_format($gr_akhir_cabut + $gr_eo, 0) }}</td>
            <td class="text-end">
                {{ $gr_eo + $gr_akhir_cabut == 0 ? 0 : number_format((1 - ($gr_akhir_cabut + $gr_eo) / ($gr_awal_cabut + $gr_eo_awal)) * 100, 0) }}
                %
            </td>
            <td class="text-end">{{ number_format(($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0), 0) }}</td>
            <td class="text-end">
                {{ $gr_akhir_cabut + $gr_eo == 0 ? 0 : number_format((($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0)) / ($gr_akhir_cabut + $gr_eo), 0) }}
            </td>
            {{-- <td class="text-end">{{ number_format($bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0), 0) }}</td> --}}
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
            <td class="text-end">
                {{ empty($cetak->gr_awal) ? 0 : number_format((1 - $cetak->gr / $cetak->gr_awal) * 100, 0) }} %</td>
            <td class="text-end">{{ number_format($cetak->ttl_rp, 0) }}</td>
            <td class="text-end">{{ empty($cetak->gr) ? 0 : number_format($cetak->ttl_rp / $cetak->gr, 0) }}</td>
            {{-- <td class="text-end">
                {{ number_format($cetak->ttl_rp + $bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0), 0) }}
            </td> --}}
        </tr>
        <tr>
            <td>Sortir</td>
            <td></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">{{ number_format($sortir->pcs ?? 0, 0) }}</td>
            <td class="text-end">{{ number_format($sortir->gr ?? 0, 0) }}</td>
            @php
                $gr_awal_sortir = $sortir->gr_awal ?? 0;
                $gr_akhir_sortir = $sortir->gr ?? 0;
            @endphp
            <td class="text-end">
                {{ empty($gr_awal_sortir) ? 0 : number_format((1 - $gr_akhir_sortir / $gr_awal_sortir) * 100, 0) }} %
            </td>
            <td class="text-end">{{ number_format($sortir->ttl_rp ?? 0, 0) }}</td>
            <td class="text-end">{{ empty($sortir->gr) ? 0 : number_format($sortir->ttl_rp / $sortir->gr, 0) }}</td>
            {{-- <td class="text-end">
                {{ number_format($sortir->ttl_rp + $bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) + $cetak->ttl_rp, 0) }}
            </td> --}}
        </tr>
        <tr>
            <td>Grading</td>
            <td></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">{{ number_format($grading->pcs ?? 0, 0) }}</td>
            <td class="text-end"><a href="javascript:void(0)" class="detail_grade fw-bold text-decoration-underline"
                    nm_partai="{{ $bk->nm_partai }}" data-bs-toggle="modal"
                    data-bs-target="#detail_data">{{ number_format($grading->gr ?? 0, 0) }}</a></td>
            @php
                $gr_susut = $gradingsusut->gr ?? 0;
                $gr_grading = $grading->gr ?? 0;
            @endphp
            <td class="text-end">
                {{ empty($gr_grading) ? 0 : number_format((1 - $grading->gr / ($gr_susut + $gr_grading)) * 100, 0) }}%
            </td>
            <td class="text-end">{{ number_format($grading->ttl_rp ?? 0, 0) }}</td>
            <td class="text-end">{{ empty($grading->ttl_rp) ? 0 : number_format($grading->ttl_rp / $grading->gr, 0) }}
            </td>
            {{-- <td class="text-end">
                {{ number_format($grading->ttl_rp + $sortir->ttl_rp + $bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) + $cetak->ttl_rp, 0) }}
            </td> --}}
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th></th>
            <th class="text-end">{{ number_format($bk->pcs_awal, 0) }}</th>
            <th class="text-end">{{ number_format($bk->gr_awal, 0) }}</th>
            <th class="text-end">{{ number_format($bk->ttl_rp, 0) }}</th>

            <th class="text-end">{{ number_format($cetak->pcs_tdk, 0) }}</th>
            <th class="text-end">{{ number_format($cetak->gr_tdk, 0) }}</th>

            <th class="text-end">{{ number_format($grading->pcs ?? 0, 0) }}</th>
            <th class="text-end">{{ number_format($grading->gr ?? 0, 0) }}</th>
            <th class="text-end">
                @php
                    $sstCabut = $gr_eo + $gr_akhir_cabut == 0 ? 0 : number_format((1 - ($gr_akhir_cabut + $gr_eo) / ($gr_awal_cabut + $gr_eo_awal)) * 100, 0);
                    $sstCtk = empty($cetak->gr_awal) ? 0 : number_format((1 - $cetak->gr / $cetak->gr_awal) * 100, 0);
                    $sstSortir = empty($gr_awal_sortir) ? 0 : number_format((1 - $gr_akhir_sortir / $gr_awal_sortir) * 100, 0);
                    $sstGrading = empty($gr_grading) ? 0 : number_format((1 - $grading->gr / ($gr_susut + $gr_grading)) * 100, 0);
                @endphp
                {{ $sstCabut + $sstCtk + $sstSortir + $sstGrading }}%
                {{-- {{ empty($gr_grading) ? 0 : number_format((1 - $grading->gr / ($gr_susut + $gr_grading)) * 100, 0) }}% --}}
            </th>
            <th class="text-end">
                @php
                    $ttl2 =
                        ($sortir->ttl_rp ?? 0) +
                        ($grading->ttl_rp ?? 0) +
                        ($cabut->ttl_rp ?? 0) +
                        ($eo->ttl_rp ?? 0) +
                        $cetak->ttl_rp +
                        $bk->ttl_rp;
                @endphp
                {{ number_format($ttl2, 0) }}
            </th>
            <th class="text-end">
                {{ empty($grading->gr) ? 0 : number_format($ttl2 / $grading->gr, 0) }}
            </th>
            {{-- <th class="text-end">
                {{ number_format($grading->ttl_rp + $sortir->ttl_rp + $bk->ttl_rp + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) + $cetak->ttl_rp, 0) }}
            </th> --}}
        </tr>
    </tfoot>
</table>
