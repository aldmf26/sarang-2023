<table class="table table-striped">
<tr>
    <th class="h6">Nama : {{ ucwords($absen[0]->nama) }} ({{ ucwords($absen[0]->kelas) }})</th>
</tr>
</table>
<table class="table table-striped" id="tableDetail">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">Tanggal</th>
            <th class="dhead">Keterangan</th>
        </tr>
    </thead>
@foreach ($absen as $i => $d)
    <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ tanggal($d->tgl) }}</td>
        <td>{{ ucwords($d->ket) }}</td>
    </tr>
@endforeach
</table>