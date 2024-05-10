<table class="table table-bordered" id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">No Box</th>
            <th class="dhead">Grade</th>
            <th class="dhead">Tanggal</th>
            <th class="dhead">Nama</th>

            <th class="dhead text-end">pcs awal</th>
            <th class="dhead text-end">gr awal</th>
            <th class="dhead text-end">rp target</th>
            <th width="130px" class="dhead text-end">pcs akhir</th>
            <th width="130px" class="dhead text-end">gr akhir</th>
            <th class="dhead text-end">sst%</th>
            <th class="dhead text-end">Total Rp</th>
            <th class="dhead">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cetak as $no => $c)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $c->no_box }}</td>
                <td>{{ $c->grade }}</td>
                <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
                <td>{{ $c->nm_anak }}</td>

                <td class="text-end">{{ $c->pcs_awal_ctk }}</td>
                <td class="text-end">{{ $c->gr_awal_ctk }}</td>
                <td class="text-end">{{ number_format($c->rp_satuan, 0) }}</td>
                <td class="text-end">
                    <input type="text" class="form-control text-end" name="pcs_akhir[]" value="{{ $c->pcs_akhir }}">
                </td>
                <td class="text-end">
                    <input type="text" class="form-control text-end" name="gr_akhir[]" value="{{ $c->gr_akhir }}">
                </td>
                <td class="text-end">
                    {{ empty($c->gr_akhir) ? 0 : number_format((1 - $c->gr_akhir / $c->gr_awal_ctk) * 100, 1) }}%</td>
                <td class="text-end">{{ number_format($c->pcs_akhir * $c->rp_satuan) }}</td>
                <td><button type="button" class="btn btn-sm btn-warning">Akhir</button></td>
            </tr>
        @endforeach

    </tbody>
</table>
