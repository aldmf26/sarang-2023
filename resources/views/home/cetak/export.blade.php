<table class="table table-bordered" id="table1">
    <thead>
        <tr>
            <th>#</th>
            <th>No Box</th>
            <th>Grade</th>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Pcs Awal</th>
            <th>Gr Awal</th>
            <th>Pcs Tidak Cetak</th>
            <th>Gr Tidak Cetak</th>
            <th>Pcs Akhir</th>
            <th>Gr Akhir</th>
            <th>Susut</th>
            <th>Denda</th>
            <th>Ttl Rp</th>
            <th>Selesai</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $c)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $c->no_box }}</td>
                <td>VL</td>
                <td>{{ $c->nama }}</td>
                <td>{{ $c->tgl }}</td>
                <td>
                    {{ $c->pcs_awal }}
                </td>
                <td>
                    {{ $c->gr_awal }}
                </td>
                <td>
                    {{ $c->pcs_tidak_ctk }}
                </td>
                <td>
                    {{ $c->gr_tidak_ctk }}
                </td>
                <td>
                    {{ $c->pcs_akhir }}
                </td>
                <td>
                    {{ $c->gr_akhir }}
                </td>
                @php
                    $susut = empty($c->gr_akhir) ? '0' : (1 - $c->gr_akhir / ($c->gr_awal - $c->gr_tidak_ctk)) * 100;
                    $denda = round($susut, 0) * 50000;
                    
                @endphp
                <td>{{ $susut }} %</td>
                <td>{{ $denda }}</td>
                <td>{{ $c->rp_pcs * $c->pcs_awal - $denda }}</td>
                <td>{{ $c->selesai }}</td>

            </tr>
        @endforeach


    </tbody>

</table>
