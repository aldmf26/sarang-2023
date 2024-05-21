<table class="table table-hover table-bordered">
    <thead>
        <tr>
            <th class="dhead">No</th>
            <th class="dhead">No Box</th>
            <th class="dhead">Tanggal</th>
            <th class="dhead">Pengawas</th>
            <th class="dhead">Anak</th>
            <th class="dhead text-end">Pcs Awal</th>
            <th class="dhead text-end">Gr Awal</th>
            <th class="dhead text-end">Pcs Akhir</th>
            <th class="dhead text-end">Gr Akhir</th>
            <th class="dhead text-end">Ttl Rp</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $d->no_box }}</td>
                <td>{{ date('d M Y', strtotime($d->tgl)) }}</td>
                <td>{{ $d->pengawas }}</td>
                <td>{{ $d->nama }}</td>
                <td class="text-end">{{ number_format($d->pcs_awal, 0) }}</td>
                <td class="text-end">{{ number_format($d->gr_awal, 0) }}</td>
                <td class="text-end">{{ number_format($d->pcs_akhir, 0) }}</td>
                <td class="text-end">{{ number_format($d->gr_akhir, 0) }}</td>
                <td class="text-end">{{ number_format($d->ttl_rp, 0) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
