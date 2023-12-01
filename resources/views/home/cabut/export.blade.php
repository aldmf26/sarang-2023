<table class="table" id="table1">
    <thead>
        <tr>
            <th>Pengawas</th>
            <th>Tgl Terima</th>
            <th>No Box</th>
            <th>Anak</th>
            <th>Pcs Awal</th>
            <th>Gr Awal</th>
            <th>Gr Flx</th>
            <th>Pcs Akhir</th>
            <th>Gr Akhir</th>
            <th>EOT</th>
            <th>Pcs Hcr</th>
            <th>Susut</th>
            <th>Rp Target</th>
            <th>Ttl Gaji</th>
            <th>Selesai</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $d)
            <tr>
                <td>{{ auth()->user()->name }}</td>
                <td>{{ $d->tgl_terima }}</td>
                <td>{{ $d->no_box }}</td>
                <td>{{ $d->nama }}</td>
                <td>{{ $d->pcs_awal }}</td>
                <td>{{ $d->gr_awal }}</td>
                <td>{{ $d->gr_flx }}</td>
                <td>{{ $d->pcs_akhir ?? 0 }}</td>
                <td>{{ $d->gr_akhir ?? 0 }}</td>
                <td>{{ $d->eot ?? 0 }}</td>
                <td>{{ $d->pcs_hcr ?? 0 }}</td>
                @php
                    $hasil = rumusTotalRp($d);
                @endphp
                <td>{{  number_format($hasil->susut,0) }}%</td>
                <td>{{ $d->rupiah }}</td>
                <td>{{ $hasil->ttl_rp }}</td>
                <td>{{ $d->selesai }}</td>
            </tr>
        @endforeach
    </tbody>

</table>
