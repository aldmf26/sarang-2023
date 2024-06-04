<table>
    <tr>
        <th>Id Grading</th>
        <th>No Grading</th>
        <th>Tgl Input</th>
        <th>Tipe</th>
        <th>Partai</th>
        <th>No Box</th>
        <th>Pcs Awal</th>
        <th>Gr Awal</th>
        <th>Pcs Akhir</th>
        <th>Gr Akhir</th>
    </tr>
    @foreach ($tbl as $d)
        <tr>
            <td style="background-color: red">{{ $d->id_grading }}</td>
            <td>GRDBJ-{{ $d->no_grading }}</td>
            <td>{{ $d->tgl }}</td>
            <td>{{ $d->ket }}</td>
            <td>{{ $d->partai }}</td>
            <td>{{ $d->no_box }}</td>
            <td>{{ $d->pcs_awal }}</td>
            <td>{{ $d->gr_awal }}</td>
            <td>{{ $d->pcs_akhir == 0 ? $d->pcs_awal : $d->pcs_akhir }}</td>
            <td>{{ $d->gr_akhir == 0 ? $d->gr_awal : $d->gr_akhir }}</td>
        </tr>
    @endforeach
</table>
