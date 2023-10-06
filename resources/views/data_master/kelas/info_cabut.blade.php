<h6>{{ ucwords($jenis) }} Paket : <span>{{ $detail->kelas }} {{ $detail->tipe }}</span></h6>
<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <tr>
                <th width="100" style="vertical-align: middle">Total Rp</th>
                <td width="10">=</td>
                <td> rp target - (pcs hcr * rp denda hcr ({{ number_format($detail->denda_hcr,0) }})) + eot bonus - denda susut + bonus susut</td>
            </tr>
            <tr>
                <th width="100" style="vertical-align: middle">Denda Hancur</th>
                <td width="10">=</td>
                <td>pcs hancur * rp denda ({{ number_format($detail->denda_hcr,0) }})</td>
            </tr>
            <tr>
                <th width="100" style="vertical-align: middle">Eot Bonus</th>
                <td width="10">=</td>
                <td>(eot cabut - gr awal cabut * 0.2 ) * rp eot ({{ $detail->eot }})</td>
            </tr>
            <tr>
                <th width="100" style="vertical-align: middle">Denda Susut</th>
                <td width="10">=</td>
                <td>susut saat cabut (lebih dari >) batas susut {{ number_format($detail->batas_susut,1) }}% <br>
                     maka (susut cabut - batas susut {{ number_format($detail->batas_susut,1) }}%) * 100 * 3% * rp targetnya  </td>
            </tr>
            <tr>
                <th width="100" style="vertical-align: middle">Bonus Susut</th>
                <td width="10">=</td>
                <td>susut saat cabut (kurang dari <) bonus susut {{ number_format($detail->bonus_susut,1) }}% <br>
                     maka (rp bonus ({{ number_format($detail->rp_bonus,0) }}) * gr awal cabut) / gr kelas {{ number_format($detail->gr) }} </td>
            </tr>
        </table>
    </div>
</div>