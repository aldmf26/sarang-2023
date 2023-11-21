<table>
    <tr>
        @php
            $warning = 'background-color: red';
        @endphp
        <th>cabut</th>
        <th>pgws</th>
        <th>hari masuk</th>
        <th>nama anak</th>
        <th>kelas</th>
        <th>pcs awal</th>
        <th>gr awal</th>
        <th>pcs akhir</th>
        <th>gr akhir</th>
        <th>eot gr</th>
        <th>gr flx</th>
        <th>susut</th>
        <th style="{{ $warning }}">ttl rp</th>

        <th>Cabut Eo</th>
        <th>Gr eo awal</th>
        <th>Gr eo akhir</th>
        <th>susut</th>
        <th style="{{ $warning }}">ttl rp eo</th>

        <th>Sortir</th>
        <th>Pcs Awal</th>
        <th>Gr Awal</th>
        <th>Pcs Akhir</th>
        <th>Gr Akhir</th>
        <th>Susut</th>
        <th style="{{ $warning }}">ttl rp sp</th>

        <th style="{{ $warning }}">Kerja dll</th>

        <th>Gajih</th>
        <th>Rp Denda</th>
        <th>Ttl Gaji</th>
        <th>Rata2</th>
    </tr>
    @php
        $TtlRp = 0;
        $eoTtlRp = 0;
        $sortirTtlRp = 0;
        $dllTtlRp = 0;
        $dendaTtlRp = 0;
        $ttlTtlRp = 0;
        $rataTtlRp = 0;
    @endphp
    @foreach ($datas as $d)
        <tr>
            <td></td>
            <td>{{ $d->pgws }}</td>
            <td>{{ $d->hariMasuk }}</td>
            <td>{{ $d->nm_anak }}</td>
            <td>{{ $d->kelas }}</td>
            <td>{{ $d->pcs_awal }}</td>
            <td>{{ $d->gr_awal }}</td>
            <td>{{ $d->pcs_akhir }}</td>
            <td>{{ $d->gr_akhir }}</td>
            <td>{{ $d->eot }}</td>
            <td>{{ $d->gr_flx }}</td>
            <td>{{ number_format($d->susut,0) }}</td>
            <td>{{ $d->ttl_rp }}</td>

            <td></td>
            <td>{{ $d->eo_awal }}</td>
            <td>{{ $d->eo_akhir }}</td>
            <td>{{ number_format($d->eo_susut,0) }}</td>
            <td>{{ $d->eo_ttl_rp }}</td>

            <td></td>
            <td>{{ $d->sortir_pcs_awal }}</td>
            <td>{{ $d->sortir_gr_awal }}</td>
            <td>{{ $d->sortir_pcs_akhir }}</td>
            <td>{{ $d->sortir_gr_akhir }}</td>
            <td>{{ number_format($d->sortir_susut,0) }}</td>
            <td>{{ $d->sortir_ttl_rp }}</td>

            <td>{{ $d->ttl_rp_dll }}</td>

            <td></td>
            <td>{{ $d->ttl_rp_denda }}</td>
            @php
                $ttl = $d->ttl_rp + $d->eo_ttl_rp + $d->sortir_ttl_rp + $d->ttl_rp_dll - $d->ttl_rp_denda;
                $rata = empty($d->hariMasuk) ? 0 : $ttl / $d->hariMasuk;
            @endphp
            <td>
                {{ $ttl }}
            </td>
            <td>{{ $rata }}</td>


            @php
                $TtlRp += $d->ttl_rp;
                $eoTtlRp += $d->eo_ttl_rp;
                $sortirTtlRp += $d->sortir_ttl_rp;
                $dllTtlRp += $d->ttl_rp_dll;
                $dendaTtlRp += $d->ttl_rp_denda;
                $ttlTtlRp += $ttl;
                $rataTtlRp += $rata;
            @endphp
        </tr>
    @endforeach

    @php
        $bold = 'font-weight: bold';
    @endphp
    <tr>
        <td colspan="12"></td>
        <td style="{{ $bold }}">{{ $TtlRp }}</td>

        <td colspan="4"></td>
        <td style="{{ $bold }}">{{ $eoTtlRp }}</td>

        <td colspan="6"></td>
        <td style="{{ $bold }}">{{ $sortirTtlRp }}</td>

        <td style="{{ $bold }}">{{ $dllTtlRp }}</td>

        <td colspan="1"></td>
        <td style="{{ $bold }}">{{ $dendaTtlRp }}</td>

        <td style="{{ $bold }}">{{ $ttlTtlRp }}</td>
        <td style="{{ $bold }}">{{ $rataTtlRp }}</td>

    </tr>
</table>
