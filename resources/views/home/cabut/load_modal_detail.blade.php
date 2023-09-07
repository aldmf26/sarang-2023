<table width="50%">
    <tr>
        <td width="20%" style="padding: 10px;" class="fw-bold">No Box</td>
        <td width="2%" style="padding: 10px;">:</td>
        <td style="padding: 10px;">{{$detail->no_box}}</td>
    </tr>
    <tr>
        <td width="20%" style="padding: 10px;" class="fw-bold">Tanggal Terima</td>
        <td width="2%" style="padding: 10px;">:</td>
        <td style="padding: 10px;">{{tanggal($detail->tgl_terima)}}</td>
    </tr>
    <tr>
        <td width="20%" style="padding: 10px;" class="fw-bold">Nama Anak</td>
        <td width="2%" style="padding: 10px;">:</td>
        <td style="padding: 10px;">{{$detail->nama}}</td>
    </tr>

</table>

<hr style="border: 1px solid #435EBE">
<table class="table table-bordered" width="100%">
    <thead>
        <tr>
            <th class="dhead text-end">Pcs Awal</th>
            <th class="dhead text-end">Gr Awal</th>
            <th class="dhead text-end">Pcs Akhir</th>
            <th class="dhead text-end">Gr Akhir</th>
            <th class="dhead text-end">Pcs Hcr</th>
            <th class="dhead text-end">EOT</th>
            <th class="dhead text-end">Susut</th>
            <th class="dhead text-end">Denda sst</th>
            <th class="dhead text-end">Denda Hcr</th>
            <th class="dhead text-end">EOT Rp</th>
            <th class="dhead text-end">Rp Target</th>
            <th class="dhead text-end">Total Rp</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align="right">{{number_format($detail->pcs_awal,0)}}</td>
            <td align="right">{{number_format($detail->gr_awal,0)}}</td>
            <td align="right">{{number_format($detail->pcs_akhir,0)}}</td>
            <td align="right">{{number_format($detail->gr_akhir,0)}}</td>
            <td align="right">{{number_format($detail->pcs_hcr,0)}}</td>
            <td align="right">{{number_format($detail->eot,0)}}</td>
            @php
            $susut = empty($detail->gr_akhir) ? 0 : ((1 - ($detail->gr_flx + $detail->gr_akhir) / $detail->gr_awal) *
            100);

            $denda = empty($detail->gr_akhir) ? 0 : ( $susut > 23.4 ? ($susut - 23.4) * 0.03 * $detail->rupiah : 0);
            $denda_hcr = $detail->pcs_hcr * 5000;

            $eot_bonus = empty($detail->eot) ? 0 : ($detail->eot - ($detail->gr_awal * 0.02)) * 750;
            @endphp
            <td align="right">{{number_format($susut,0)}} %</td>
            <td align="right">{{number_format($denda,0)}} </td>
            <td align="right">{{number_format($denda_hcr,0)}} </td>
            <td align="right">{{number_format($eot_bonus,0)}} </td>
            <td align="right">{{number_format($detail->rupiah,0)}} </td>
            <td align="right">{{number_format($detail->rupiah - $denda -$denda_hcr + $eot_bonus ,0)}} </td>
        </tr>
    </tbody>
</table>