<table class="table table-bordered" id="ctk_proses">
    <thead>
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>No Box</th>
            <th>Nama anak</th>
            <th class="text-end">Pcs Awal</th>
            <th class="text-end">Gr Awal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cabut as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $d->tgl }}</td>
                <td>{{ $d->no_box }}</td>
                <td>{{ $d->nama }}</td>
                <td class="text-end">{{ $d->pcs_awal }}</td>
                <td class="text-end">{{ $d->gr_awal }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
