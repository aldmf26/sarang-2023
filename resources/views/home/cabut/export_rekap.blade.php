<table class="table  table-bordered" id="table">
    <thead>
        <tr>
            <th class="dhead ">No Box</th>
            <th class="dhead text-end">Pcs Awal Bk</th>
            <th class="dhead text-end">Gr Awal Bk</th>
            <th class="dhead">Bulan</th>
            <th class="dhead">Pengawas</th>
            <th class="dhead text-end">Pcs Awal Kerja</th>
            <th class="dhead text-end">Gr Awal Kerja</th>
            <th class="dhead text-end">Pcs Akhir Kerja</th>
            <th class="dhead text-end">Gr Akhir Kerja</th>
            <th class="dhead text-end">Eot</th>
            <th class="dhead text-end">Flx</th>
            <th class="dhead text-end">Susut</th>
            <th class="dhead text-end">Total Rupiah</th>
            <th class="dhead text-end">Pcs Sisa Bk</th>
            <th class="dhead text-end">Gr Sisa Bk</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $c)
            <tr>
                <td>{{ $c->no_box }}</td>
                <td align="right">{{ $c->pcs_bk }}</td>
                <td align="right">{{ $c->gr_bk }}</td>
                <td>{{ date('M y', strtotime($c->tgl)) }}</td>
                <td>{{ $c->pengawas }}</td>
                <td align="right">{{ $c->pcs_awal }}</td>
                <td align="right">{{ $c->gr_awal }}</td>
                <td align="right">{{ $c->pcs_akhir }}</td>
                <td align="right">{{ $c->gr_akhir }}</td>

                <td align="right">{{ $c->gr_flx }}</td>
                <td align="right">{{ $c->eot }}</td>
                @php
                    $susut = empty($c->gr_awal) ? 0 : (1 - ($c->gr_flx + $c->gr_akhir) / $c->gr_awal) * 100;

                @endphp
                <td align="right">{{ number_format($susut, 0) }} %</td>

                <td align="right">{{ $c->rupiah }}
                </td>
                <td align="right">{{ $c->pcs_bk - $c->pcs_awal }}</td>
                <td align="right">{{ $c->gr_bk - $c->gr_awal }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
