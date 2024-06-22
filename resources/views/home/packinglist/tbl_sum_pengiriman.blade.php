<table id="tbl-sum"  class="table text-black table-bordered table-sm" style="border: 1px solid #6779bc;">
    @php
        $color = "style='background-color: #C8C6C4'";
    @endphp
    <thead>
        <tr>
            <th class="text-center align-middle" {!!$color!!} rowspan="3">GRADE</th>
            <th class="text-center" {!!$color!!}>FROM</th>
            <th class="text-center" {!!$color!!}></th>
            <th class="text-center" {!!$color!!}>{{ strtoupper($detailPacking->nm_packing) }}</th>
        </tr>
        <tr>
            <th class="text-center" {!!$color!!}>DATE</th>
            <th class="text-center" {!!$color!!}></th>
            <th class="text-center" {!!$color!!}>{{ tanggal($detailPacking->tgl) }}</th>
        </tr>
        <tr>
            <th class="text-center" {!!$color!!}>BOX</th>
            <th class="text-center" {!!$color!!}>PCS</th>
            <th class="text-center" {!!$color!!}>GRAM</th>
        </tr>
    </thead>
    <tbody>
        @php
            $ttlBox = 0;
            $ttlPcs = 0;
            $ttlGr = 0;
        @endphp
        @foreach ($detail as $d)
            @php
                $gr = $d->gr;
                $ttlBox += $d->box;
                $ttlPcs += $d->pcs;
                $ttlGr += $gr;
            @endphp
            <tr class="text-center">
                <td>{{ $d->grade }}</td>
                <td>{{ number_format($d->box, 0) }}</td>
                <td>{{ number_format($d->pcs, 0) }}</td>
                <td>{{ number_format($gr, 0) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" {!!$color!!}>TOTAL</th>
            <th class="text-center" {!!$color!!}>{{ number_format($ttlBox, 0) }}</th>
            <th class="text-center" {!!$color!!}>{{ number_format($ttlPcs, 0) }}</th>
            <th class="text-center" {!!$color!!}>{{ number_format($ttlGr, 0) }}</th>
        </tr>
    </tfoot>
</table>