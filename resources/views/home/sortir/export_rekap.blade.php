<table class="table table-bordered table-hover table-striped" id="table">
    <thead>
        <tr>
            <th class="dhead">Bulan</th>
            <th class="dhead">Pengawas</th>
            <th class="dhead">No Box</th>
            <th class="dhead text-end">Pcs Akhir Cetak</th>
            <th class="dhead text-end">Gr Akhir Cetak</th>
            <th class="dhead text-end">Pcs Awal Sortir</th>
            <th class="dhead text-end">Gr Awal Sortir</th>
            @php
                $ttl =0;
                 foreach($datas as $d) {
                     $ttl +=$d->ttl_rp;
                 }
            @endphp
            <th class="dhead text-end">Total Rupiah ({{ number_format($ttl, 0) }})</th>
            <th class="dhead text-end">Pcs Sisa Bk</th>
            <th class="dhead text-end">Gr Sisa Bk</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $n => $d)
            <tr>
                <td>{{ date('M Y', strtotime($d->tgl)) }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->no_box }}</td>
                <td class="text-end">{{ $d->cabut_pcs_akhir }}</td>
                <td class="text-end">{{ $d->cabut_gr_akhir }}</td>
                <td class="text-end">{{ $d->pcs_awal }}</td>
                <td class="text-end">{{ $d->gr_awal }}</td>
                <td align="right">{{$d->ttl_rp }}</td>
                <td class="text-end">{{ $d->pcs_awal - $d->cabut_pcs_akhir }}</td>
                <td class="text-end">{{ $d->gr_awal - $d->cabut_gr_akhir }}</td>
            </tr>
        @endforeach
    </tbody>
</table>