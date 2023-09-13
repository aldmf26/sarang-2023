<table class="table table-striped" id="table1">
    <thead>
        <tr>
            <th>Pengawas</th>
            <th>Tgl Ambil</th>
            <th>No Box</th>
            <th>Nama Anak</th>
            <th>Tgl Serah</th>
            <th>Gr EO Awal</th>
            <th>Gr EO Akhir</th>
            <th>Susut</th>
            <th>Ttl Rp</th>
            <th>Selesai</th>
            <th>Kelas</th>
            <th>Rp gr</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $d)
            <tr>
                <td>{{ $d->name }}</td>
                <td>{{ $d->tgl_ambil }}</td>
                <td>{{ $d->no_box }}</td>
                <td>{{ $d->nama }}</td>
                <td>{{ $d->tgl_serah }}</td>
                <td>{{ $d->gr_eo_awal }}</td>
                <td>{{ $d->gr_eo_akhir }}</td>
                @php
                    $susut = empty($d->gr_eo_akhir) ? 0 : (1 - $d->gr_eo_akhir / $d->gr_eo_awal) * 100;
                    @endphp
                <td>{{ $susut }}</td>
                <td>{{ $d->ttl_rp}} </td>
                <td>{{ $d->selesai }}</td>
                <td>{{ $d->kelas }}</td>
                <td>{{ $d->rupiah }}</td>
            </tr>
        @endforeach
    </tbody>
</table>