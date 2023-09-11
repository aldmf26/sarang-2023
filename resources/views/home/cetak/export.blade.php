<table class="table table-bordered" id="table1">
    <thead>
        <tr>
            <th>#</th>
            <th>No Box</th>
            <th>Grade</th>
            <th>Nama</th>
            <th>Tanggal</th>
            <th></th>
            <th class="text-end">Awal</th>
            <th class="text-end">Tidak Cetak</th>
            <th class="text-end">Akhir</th>
            <th class="text-end">Susut</th>
            <th class="text-end">Denda</th>
            <th class="text-end">Ttl Rp</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cetak as $no => $c)
        <tr>
            <td>{{$no+1}}</td>
            <td>{{$c->no_box}}</td>
            <td>VL</td>
            <td>{{$c->nama}}</td>
            <td>{{date('d M y',strtotime($c->tgl))}}</td>
            <td>
                pcs: <br>
                gr :
            </td>
            <td align="right">
                {{$c->pcs_awal}} <br> {{$c->gr_awal}}
            </td>
            <td align="right">
                {{$c->pcs_tidak_ctk}} <br> {{$c->gr_tidak_ctk}}
            </td>
            <td align="right">
                {{$c->pcs_akhir}} <br> {{$c->gr_akhir}}
            </td>
            @php
            $susut = empty($c->gr_akhir) ? '0' :(1-($c->gr_akhir / ($c->gr_awal -
            $c->gr_tidak_ctk))) * 100;
            $denda = round($susut,0) * 50000;

            @endphp
            <td align="right">{{number_format($susut,1)}} %</td>
            <td align="right">Rp.{{ number_format($denda,0)}}</td>
            <td align="right">Rp. {{number_format( ($c->rp_pcs * $c->pcs_awal) - $denda,0)}}</td>
        
        </tr>
        @endforeach


    </tbody>

</table>