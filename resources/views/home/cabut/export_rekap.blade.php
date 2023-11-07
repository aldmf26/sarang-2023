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
        @php
            $ttlPcsBk = 0;
            $ttlGrBk = 0;

            $ttlPcsAwal = 0;
            $ttlGrAwal = 0;
            $ttlPcsAkhir = 0;
            $ttlGrAkhir = 0;

            $ttlFlx = 0;
            $ttlEot = 0;
            $ttlSusut = 0;

            $ttlRp = 0;

            $ttlPcsSisa = 0;
            $ttlGrSisa = 0;
        @endphp
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

            @php
                $ttlPcsBk += $c->pcs_bk;
                $ttlGrBk += $c->gr_bk;

                $ttlPcsAwal += $c->pcs_awal;
                $ttlGrAwal += $c->gr_awal;
                $ttlPcsAkhir += $c->pcs_akhir;
                $ttlGrAkhir += $c->gr_akhir;

                $ttlFlx += $c->gr_flx;
                $ttlEot += $c->eot;
                $ttlSusut += $susut;

                $ttlRp += $c->rupiah;

                $ttlPcsSisa += $c->pcs_bk - $c->pcs_awal;
                $ttlGrSisa += $c->gr_bk - $c->gr_awal;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
  
        <tr>
            <th>Total</th>
            <th>{{ $ttlPcsBk }}</th>
            <th>{{ $ttlGrBk }}</th>
            <th></th>
            <th></th>
            <th>{{ $ttlPcsAwal }}</th>
            <th>{{ $ttlGrAwal }}</th>
            <th>{{ $ttlPcsAkhir }}</th>
            <th>{{ $ttlGrAkhir }}</th>

            <th>{{ $ttlFlx }}</th>
            <th>{{ $ttlEot }}</th>
            <th>{{ $ttlSusut }}</th>

            <th>{{ $ttlRp }}</th>

            <th>{{ $ttlPcsSisa }}</th>
            <th>{{ $ttlGrSisa }}</th>
        </tr>
    </tfoot>
</table>
