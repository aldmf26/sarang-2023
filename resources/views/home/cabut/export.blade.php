<table class="table" id="table1">
    <thead>
        <tr>
            <th>Pengawas</th>
            <th>Tgl Terima</th>
            <th>No Box</th>
            <th>Anak</th>
            <th >Pcs Awal</th>
            <th >Gr Awal</th>
            <th >Gr Flx</th>
            <th >Pcs Akhir</th>
            <th >Gr Akhir</th>
            <th >EOT</th>
            <th >Pcs Hcr</th>
            <th >Susut</th>
            <th >Ttl Gaji</th>
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
                    $susut = empty($d->gr_akhir) ? 0 : (1 - ($d->gr_flx + $d->gr_akhir) / $d->gr_awal) * 100;
                    
                    $denda = empty($d->gr_akhir) ? 0 : ($susut > 23.4 ? ($susut - 23.4) * 0.03 * $d->rupiah : 0);
                    $denda_hcr = $d->pcs_hcr * 5000;
                    
                    $eot_bonus = empty($d->eot) ? 0 : ($d->eot - $d->gr_awal * 0.02) * 750;
                @endphp
                <td>{{ $susut }}%</td>
                <td>{{ $d->rupiah - $denda - $denda_hcr + $eot_bonus }}</td>
            </tr>
        @endforeach
    </tbody>

</table>