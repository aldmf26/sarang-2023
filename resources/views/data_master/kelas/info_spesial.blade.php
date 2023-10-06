<h6>{{ ucwords($jenis) }} Paket : <span>{{ $detail->kelas }} {{ $detail->tipe }}</span></h6>
<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <tr>
                <th width="100" style="vertical-align: middle">Rp Target</th>
                <td width="10">=</td>   
                <td>pcs awal spesial / pcs kelas ({{ number_format($detail->pcs,0) }}) * rp paket  ({{ number_format($detail->rupiah,0) }}) </td>
            </tr>
            <tr>
                <th width="100" style="vertical-align: middle">Ttl Rp Spc</th>
                <td width="10">=</td>   
                <td>jika pcs xbayar (lebih dari >) 0 <br> maka hasilnya 0 jika tidak mengambil rp target</td>
            </tr>
        </table>
    </div>
</div>