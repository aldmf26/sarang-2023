<table class="table  table-bordered" id="table">
    <thead>
        <tr>
            <th class="dhead">Bulan</th>
            <th class="dhead">Pengawas</th>
            <th class="dhead ">No Box</th>
            <th class="dhead text-end">Pcs Awal Bk</th>
            <th class="dhead text-end">Gr Awal Bk</th>
            <th class="dhead text-end">Pcs Awal Kerja</th>
            <th class="dhead text-end">Gr Awal Kerja</th>
            <th class="dhead text-end">Total Rupiah</th>
            <th class="dhead text-end">Pcs Sisa Bk</th>
            <th class="dhead text-end">Gr Sisa Bk</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $c)
        <tr>
            <td>{{date("M y",strtotime($c->tgl)) }}</td>
            <td>{{$c->pengawas}}</td>
            <td>{{$c->no_box}}</td>
            <td align="right">{{$c->pcs_bk}}</td>
            <td align="right">{{$c->gr_bk}}</td>
            <td align="right">{{$c->pcs_awal}}</td>
            <td align="right">{{$c->gr_awal}}</td>
           
            <td align="right">{{ $c->rupiah }}
            </td>
            <td align="right">{{$c->pcs_bk - $c->pcs_awal}}</td>
            <td align="right">{{$c->gr_bk - $c->gr_awal}}</td>
        </tr>
        @endforeach
    </tbody>
</table>