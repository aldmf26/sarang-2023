<h6>Operasional bulan : {{ date('F', strtotime('01-' . $bulan . '-' . $tahun)) }} {{ $tahun }}</h6>
<br>
<div class="row">
    <div class="col-lg-8"></div>
    <div class="col-lg-4">
        <label for="">Pilih Bulan</label>
        <select name="" id="" class="form-control bulan_op">
            <option value="">Pilih bulan</option>
            @foreach ($dataBulan as $b)
                <option value="{{ $b->id_oprasional }}" @selected($id_oprasional == $b->id_oprasional)>{{ $b->bulan }} ~
                    {{ $b->tahun }}</option>
            @endforeach

        </select>
    </div>
</div>
<br>
<table class="table table-bordered" width="100%">
    <thead>
        <tr>
            <th class="text-end">Gr Cabut</th>
            <th class="text-end">Gr Eo</th>
            <th class="text-end">Gr Cetak</th>
            <th class="text-end">Gr Sortir</th>
            <th class="text-end">Total Gaji</th>
            <th class="text-end">Cost Operasional</th>
            <th class="text-end" width="25%">Total Cost Operasional</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-end">{{ number_format($total->gr_cabut, 0) }}</td>
            <td class="text-end">{{ number_format($total->gr_eo, 0) }}</td>
            <td class="text-end">{{ number_format($total->gr_ctk, 0) }}</td>
            <td class="text-end">{{ number_format($total->gr_sortir, 0) }}</td>
            <td class="text-end">{{ number_format($cost_cbt->cost + $cost_ctk->cost + $cost_str->cost, 0) }}</td>
            <td class="text-end">
                {{ empty($cost_oprasional->rp_oprasional) ? 0 : number_format($cost_oprasional->rp_oprasional, 0) }}
                <input type="hidden" name="gaji" value="{{ $cost_cbt->cost + $cost_ctk->cost + $cost_str->cost }}">
            </td>
            <td x-data="numberFormat({{ empty($cost_oprasional->ttl_rp) ? 0 : $cost_oprasional->ttl_rp }})">
                <input type="text" class="form-control" autofocus name="biaya_oprasional" id="number"
                    x-model="formattedNumber" @keyup="formatNumber"
                    value="{{ empty($cost_oprasional->ttl_rp) ? 0 : $cost_oprasional->ttl_rp }}">

                <input type="hidden" class="form-control" name="bulan" value="{{ $bulan }}">
                <input type="hidden" class="form-control" name="tahun" value="{{ $tahun }}">

            </td>
        </tr>
    </tbody>
</table>
<hr style="border: 1px solid black">
{{-- <h5>Total: {{ number_format($total->gr_cabut + $total->gr_eo + $total->gr_ctk + $total->gr_sortir, 0) }} | Rp/gr :
    {{ empty($cost_oprasional->ttl_rp) ? 0 : number_format($cost_oprasional->ttl_rp / ($total->gr_cabut + $total->gr_eo + $total->gr_ctk + $total->gr_sortir), 0) }}
    @php
        $ttl_gr = $total->gr_cabut + $total->gr_eo + $total->gr_ctk + $total->gr_sortir;
    @endphp

    <input type="hidden" name="gr_akhir" value="{{ $ttl_gr }}">
</h5> --}}
