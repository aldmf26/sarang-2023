<table class="table table-bordered table-hover table-striped" id="table">
    <thead>
        <tr>
            <th class="dhead ">No Box</th>
            <th class="dhead text-end">Pcs Awal Bk</th>
            <th class="dhead text-end">Gr Awal Bk</th>
            <th class="dhead">Bulan</th>
            <th class="dhead">Pengawas</th>
            <th class="dhead text-end">Pcs Awal </th>
            <th class="dhead text-end">Gr Awal </th>
            <th class="dhead text-end">Pcs Akhir </th>
            <th class="dhead text-end">Gr Akhir </th>
            <th class="dhead text-end">Pcs Cu </th>
            <th class="dhead text-end">Gr Cu </th>
            <th class="dhead text-end">Susut</th>
            <th class="dhead text-end">Total Rupiah</th>
            <th class="dhead text-end">Pcs Sisa Bk</th>
            <th class="dhead text-end">Gr Sisa Bk</th>
        </tr>
    </thead>
    <tbody>
        @php
            $pcs_bk = 0;
            $gr_bk = 0;
            $pcs_awal = 0;
            $gr_awal = 0;
            $pcs_akhir = 0;
            $gr_akhir = 0;
            $pcs_cu = 0;
            $gr_cu = 0;
            $ttl_rp = 0;
            $pcs_sisa = 0;
            $gr_sisa = 0;
        @endphp
        @foreach ($datas as $n => $d)
            <tr>
                <td>{{ $d->no_box }}</td>
                <td>{{ $d->pcs_awal }}</td>
                <td>{{ $d->gr_awal }}</td>
                <td>{{ date('M Y', strtotime($d->tgl)) }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->pcs_awal }}</td>
                <td>{{ $d->gr_awal }}</td>
                <td>{{ $d->pcs_akhir }}</td>
                <td>{{ $d->gr_akhir }}</td>
                <td>{{ $d->pcs_cu }}</td>
                <td>{{ $d->gr_cu }}</td>
                <td>{{ round((1 - ($d->gr_akhir + $d->gr_cu) / $d->gr_awal) * 100, 0) }} %</td>
                <td>{{ round($d->ttl_rp + $d->rp_harian - $d->denda_susut - $d->denda_hcr, 0) }}</td>
                <td>{{ $d->pcs_awal - $d->pcs_awal - $d->pcs_tdk_ctk }}</td>
                <td>{{ $d->gr_awal - $d->gr_awal - $d->gr_tidak_ctk }}</td>
            </tr>
            @php
                $pcs_bk += $d->pcs_awal;
                $gr_bk += $d->gr_awal;
                $pcs_awal += $d->pcs_awal;
                $gr_awal += $d->gr_awal;
                $pcs_akhir += $d->pcs_akhir;
                $gr_akhir += $d->gr_akhir;
                $pcs_cu += $d->pcs_cu;
                $gr_cu += $d->gr_cu;
                $ttl_rp += $d->ttl_rp + $d->rp_harian - $d->denda_susut - $d->denda_hcr;
                $pcs_sisa += $d->pcs_awal - $d->pcs_awal - $d->pcs_tdk_ctk;
                $gr_sisa += $d->gr_awal - $d->gr_awal - $d->gr_tidak_ctk;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th>{{ $pcs_bk }}</th>
            <th>{{ $gr_bk }}</th>
            <th></th>
            <th></th>
            <th>{{ $pcs_awal }}</th>
            <th>{{ $gr_awal }}</th>
            <th>{{ $pcs_akhir }}</th>
            <th>{{ $gr_akhir }}</th>
            <th>{{ $pcs_cu }}</th>
            <th>{{ $gr_cu }}</th>
            <th>{{ round((1 - ($gr_akhir + $gr_cu) / $gr_awal) * 100, 0) }}%</th>
            <th>{{ $ttl_rp }}</th>
            <th>{{ $pcs_sisa }}</th>
            <th>{{ $gr_sisa }}</th>
        </tr>
    </tfoot>
</table>
