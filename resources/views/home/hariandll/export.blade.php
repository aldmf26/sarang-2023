<table class="table" id="table1">
    <thead>
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Nama Anak</th>
            <th>Keterangan</th>
            <th>Lokasi</th>
            <th class="text-end">Rupiah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ tanggal($d->tgl) }}</td>
                <td>{{ $d->nama }}</td>
                <td>{{ $d->ket }}</td>
                <td>{{ $d->lokasi }}</td>
                <td class="text-end">{{ $d->rupiah}}</td>

            </tr>
        @endforeach
    </tbody>
</table>