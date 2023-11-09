<div class="row">
    <div class="col-lg-5">
        <table>
            <tr>
                <td width="25%" style="padding: 10px;" class="fw-bold">No Box</td>
                <td width="2%" style="padding: 10px;">:</td>
                <td style="padding: 10px;">{{ $detail->no_box }}</td>
            </tr>
            <tr>
                <td width="25%" style="padding: 10px;" class="fw-bold">Tanggal Terima</td>
                <td width="2%" style="padding: 10px;">:</td>
                <td style="padding: 10px;">{{ tanggal($detail->tgl) }}</td>
            </tr>
        </table>
    </div>
    <div class="col-lg-6">
        <table >
            <tr>
                <td width="20%" style="padding: 10px;" class="fw-bold">Nama Anak</td>
                <td width="2%" style="padding: 10px;">:</td>
                <td style="padding: 10px;">{{ strtoupper($detail->nama) . ' / ' . $detail->id_kelas }}</td>
            </tr>
            <tr>
                <td width="20%" style="padding: 10px;" class="fw-bold">Kelas/Paket</td>
                <td width="2%" style="padding: 10px;">:</td>
                
                <td style="padding: 10px;">Kelas {{ $detail->nm_kelas }} {{ number_format($detail->rp_kelas,0) }}</td>
            </tr>
        
        </table>
    </div>
</div>


{{-- <hr style="border: 1px solid #435EBE">
<table class="table table-bordered" width="100%">
    <thead>
        <tr>
            <th class="dhead text-end">Pcs Awal</th>
            <th class="dhead text-end">Gr Awal</th>
            <th class="dhead text-end">Pcuc</th>
            <th class="dhead text-end">Pcus</th>
            <th class="dhead text-end">Pcs Akhir</th>
            <th class="dhead text-end">Gr Akhir</th>
            <th class="dhead text-end">Pcs Hcr</th>
            <th class="dhead text-end">Susut</th>
            <th class="dhead text-end">Rp Target</th>
            <th class="dhead text-end">Total Rp</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align="right">{{ number_format($detail->pcs_awal, 0) }}</td>
            <td align="right">{{ number_format($detail->gr_awal, 0) }}</td>
            <td align="right">{{ number_format($detail->pcs_akhir, 0) }}</td>
            <td align="right">{{ number_format($detail->gr_akhir, 0) }}</td>
            <td align="right">{{ number_format($detail->pcs_hcr, 0) }}</td>
            <td align="right">{{ number_format($detail->eot, 0) }}</td>
            @php
                $hasil = rumusTotalRp($detail);
            @endphp
            <td align="right">{{ number_format($hasil->susut, 0) }} %</td>
            <td align="right">{{ number_format($hasil->bonus_susut, 0) }}</td>
            <td align="right">{{ number_format($hasil->denda, 0) }} </td>
            <td align="right">{{ number_format($hasil->denda_hcr, 0) }} </td>
            <td align="right">{{ number_format($hasil->eot_bonus, 0) }} </td>
            <td align="right">{{ number_format($detail->rupiah, 0) }} </td>
            <td align="right">{{ number_format($hasil->ttl_rp, 0) }} </td>
        </tr>
    </tbody>
</table> --}}
