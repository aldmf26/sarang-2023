<table class="table" id="table1">
    <thead>
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Id Anak</th>
            <th>Nama Anak</th>
            <th>Keterangan</th>
            @if ($kategori == 'cetak')
                <th>Pcs</th>
                <th>Gr</th>
            @else
            @endif
            <th>Lokasi</th>
            <th class="text-end">Rupiah </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $d)
            <tr>
                <td>{{ $d->id_hariandll }}</td>
                <td>{{ tanggal($d->tgl) }}</td>
                <td>{{ $d->id_anak }}</td>
                <td>{{ $d->nama }}</td>
                <td>{{ $d->ket }}</td>
                @if ($kategori == 'cetak')
                    <td class="text-end">{{ $d->pcs }}</td>
                    <td class="text-end">{{ $d->gr }}</td>
                @else
                @endif
                <td>{{ $d->lokasi }}</td>
                <td class="text-end">{{ $d->rupiah }}</td>

            </tr>
        @endforeach
    </tbody>
</table>
