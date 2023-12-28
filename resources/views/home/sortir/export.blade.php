<table class="table table-bordered" id="table1">
    <thead>
        <tr>
            <th>#</th>
            <th>No Box</th>
            <th>Tipe</th>
            <th>Pengawas</th>
            <th>Anak</th>
            <th>Tgl Terima</th>
            <th>Pcs Awal</th>
            <th>Gr Awal</th>
            <th>Pcs Akhir</th>
            <th>Gr Akhir</th>
            <th>Susut</th>
            <th>Rp Target</th>
            <th>Denda SP</th>
            <th>Ttl Gaji</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $d->no_box }}</td>
                <td>{{ $d->kelas }}</td>
                <td>{{ ucwords(auth()->user()->name) }}</td>
                <td>{{ $d->nama }}</td>
                <td>{{ $d->tgl }}</td>
                <td>{{ $d->pcs_awal ?? 0 }}</td>
                <td>{{ $d->gr_awal ?? 0 }}</td>
                <td>{{ $d->pcs_akhir ?? 0 }}</td>
                <td>{{ $d->gr_akhir ?? 0 }}</td>
                @php
                    $susut = empty($d->gr_akhir) ? 0 : (1 - ($d->gr_akhir / $d->gr_awal)) * 100;
                @endphp
                <td>{{ $susut }}%</td>
                <td>{{ $d->rp_target ?? 0 }}</td>
                <td>{{ $d->denda_sp ?? 0 }}</td>
                <td>{{ $d->ttl_rp ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>