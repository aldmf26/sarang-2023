<table class="table table-bordered" id="tableAnak">
    <thead>
        <tr>
            <th width="5">#</th>
            <th>Nama Anak</th>
            <th>Tgl</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $d)
        <tr>
            <td>{{ $no+1 }}</td>
            <td>{{ $d->nama }}</td>
            <td>{{ tanggal($d->tgl) }}</td>
            <td align="center">
                <button type="button" class="btn rounded-pill hapusAnakSisa" id_absen="{{ $d->id_absen }}"><i
                        class="fas fa-trash text-danger"></i>
                </button>
            </td>
        </tr>
        @endforeach
            
    </tbody>
</table>