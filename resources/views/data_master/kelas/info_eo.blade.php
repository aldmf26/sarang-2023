<h6>{{ ucwords($jenis) }} Paket : <span>{{ $detail->kelas }} {{ $detail->tipe }}</span></h6>
<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <tr>
                <th width="90" style="vertical-align: middle">Total Rp</th>
                <td width="10">=</td>
                <td>gr eo akhir * rp eo ({{ number_format($detail->rupiah,0) }}) </td>
            </tr>
        </table>
    </div>
</div>