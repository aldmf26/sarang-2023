<h5>Partai : {{ $partai }} | No Box : {{ $no_box }}</h5>
<p>
    <dt>Cabut</dt>
</p>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">Pengawas</th>
            <th class="dhead">Nama Karyawan</th>
            <th class="dhead text-end">Pcs Awal</th>
            <th class="dhead text-end">Gr Awal</th>
            <th class="dhead text-end">Pcs Akhir</th>
            <th class="dhead text-end">Gr Akhir</th>
            <th class="dhead text-end">Susut%</th>
            <th class="dhead text-end">Rp/Gr</th>
            <th class="dhead text-end">Cost Bk</th>
            <th class="dhead text-end">Cost Cabut</th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td>{{ $cabut->pgws }}</td>
            <td>{{ $cabut->nm_anak }}</td>
            <td class="text-end">{{ $cabut->pcs_awal }}</td>
            <td class="text-end">{{ $cabut->gr_awal }}</td>
            <td class="text-end">{{ $cabut->pcs_akhir }}</td>
            <td class="text-end">{{ $cabut->gr_akhir }}</td>
            <td class="text-end">{{ number_format((1 - $cabut->gr_akhir / $cabut->gr_awal) * 100, 0) }} %</td>
            <td class="text-end">{{ number_format(($cabut->cost_bk + $cabut->ttl_rp) / $cabut->gr_akhir, 0) }}</td>
            <td class="text-end">{{ number_format($cabut->cost_bk, 0) }}</td>
            <td class="text-end">{{ number_format($cabut->ttl_rp, 0) }}</td>
        </tr>

    </tbody>

</table>

<p>
    <dt>Cetak</dt>
</p>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">Pengawas</th>
            <th class="dhead">Nama Karyawan</th>
            <th class="dhead text-end">Pcs Awal</th>
            <th class="dhead text-end">Gr Awal</th>
            <th class="dhead text-end">Pcs Akhir</th>
            <th class="dhead text-end">Gr Akhir</th>
            <th class="dhead text-end">Susut%</th>
            <th class="dhead text-end">Rp/Gr</th>
            <th class="dhead text-end">Cost Bk</th>
            <th class="dhead text-end">Cost Cabut</th>
            <th class="dhead text-end">Cost Cetak</th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td>{{ $cetak->pgws ?? '-' }}</td>
            <td>{{ $cetak->nm_anak ?? '-' }}</td>
            <td class="text-end">{{ $cetak->pcs_awal ?? 0 }}</td>
            <td class="text-end">{{ $cetak->gr_awal ?? 0 }}</td>
            <td class="text-end">{{ $cetak->pcs_akhir ?? 0 }}</td>
            <td class="text-end">{{ $cetak->gr_akhir ?? 0 }}</td>
            <td class="text-end">
                {{ empty($cetak->gr_akhir) ? '0 %' : number_format((1 - $cetak->gr_akhir / $cetak->gr_awal) * 100, 0) }}
                %
            </td>
            <td class="text-end">
                {{ empty($cetak->ttl_rp) ? 0 : number_format(($cetak->cost_bk + $cetak->ttl_rp + $cetak->cost_cbt) / $cetak->gr_akhir, 0) }}
            </td>
            <td class="text-end">{{ number_format($cetak->cost_bk ?? 0, 0) }}</td>
            <td class="text-end">{{ number_format($cetak->cost_cbt ?? 0, 0) }}</td>
            <td class="text-end">{{ number_format($cetak->ttl_rp ?? 0, 0) }}</td>
        </tr>

    </tbody>

</table>
<p>
    <dt>Sortir</dt>
</p>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">Pengawas</th>
            <th class="dhead">Nama Karyawan</th>
            <th class="dhead text-end">Pcs Awal</th>
            <th class="dhead text-end">Gr Awal</th>
            <th class="dhead text-end">Pcs Akhir</th>
            <th class="dhead text-end">Gr Akhir</th>
            <th class="dhead text-end">Susut%</th>
            <th class="dhead text-end">Rp/Gr</th>
            <th class="dhead text-end">Cost Bk</th>
            <th class="dhead text-end">Cost Cabut</th>
            <th class="dhead text-end">Cost Cetak</th>
            <th class="dhead text-end">Cost Sortir</th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td>{{ $sortir->pgws ?? '-' }}</td>
            <td>{{ $sortir->nm_anak ?? '-' }}</td>
            <td class="text-end">{{ $sortir->pcs_awal ?? 0 }}</td>
            <td class="text-end">{{ $sortir->gr_awal ?? 0 }}</td>
            <td class="text-end">{{ $sortir->pcs_akhir ?? 0 }}</td>
            <td class="text-end">{{ $sortir->gr_akhir ?? 0 }}</td>
            <td class="text-end">
                {{ empty($sortir->gr_akhir) ? '0 %' : number_format((1 - $sortir->gr_akhir / $sortir->gr_awal) * 100, 0) }}
                %</td>
            <td class="text-end">
                {{ empty($sortir->cost_bk) ? 0 : number_format(($sortir->cost_bk + $sortir->ttl_rp + $sortir->cost_cbt + $sortir->cost_ctk) / $sortir->gr_akhir, 0) }}
            </td>
            <td class="text-end">{{ number_format($sortir->cost_bk ?? 0, 0) }}</td>
            <td class="text-end">{{ number_format($sortir->cost_cbt ?? 0, 0) }}</td>
            <td class="text-end">{{ number_format($sortir->cost_ctk ?? 0, 0) }}</td>
            <td class="text-end">{{ number_format($sortir->ttl_rp ?? 0, 0) }}</td>
        </tr>

    </tbody>

</table>
