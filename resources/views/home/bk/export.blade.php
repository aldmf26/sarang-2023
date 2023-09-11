<table>
    <thead>
        <tr>
            <th>#</th>
            <th>No Lot</th>
            <th>No Box</th>
            <th>Tipe</th>
            <th>Ket</th>
            <th>Warna</th>
            <th>Tgl terima</th>
            <th>Pengawas</th>
            <th>Penerima</th>
            <th>Pcs Awal</th>
            <th>Gr Awal</th>
        </tr>
    </thead>
    <tbody>
    
        @foreach ($datas as $no => $b)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $b->no_lot }}</td>
                <td>{{ $b->no_box }}</td>
                <td>{{ $b->tipe }}</td>
                <td>{{ $b->ket_bk }}</td>
                <td>{{ $b->nm_warna }}</td>
                <td>{{ tanggal($b->tgl) }}</td>
                <td>{{ $b->pengawas }}</td>
                <td>{{ $b->penerima == '1' ? 'Jenah' : ($b->penerima == '2' ? 'Nurul' : 'Erna') }}
                </td>
                <td>{{ $b->pcs_awal }}</td>
                <td>{{ $b->gr_awal }}</td>
               
            </tr>
        @endforeach
    </tbody>

</table>
