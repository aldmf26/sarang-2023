<table id="tbl-list" class="table table-bordered table-sm" style="border: 1px solid #6779bc;">
    @php
        $color = "style='background-color: #C8C6C4'";
    @endphp
    <thead>
        <tr>
            <th class="text-center" {!!$color!!}>Partai</th>
            <th class="text-center" {!!$color!!}>Tipe</th>
            <th class="text-center" {!!$color!!}>Grade</th>
            <th class="text-center" {!!$color!!}>Pcs Akhir</th>
            <th class="text-center" {!!$color!!}>Pcs Akhir</th>
            <th class="text-center" {!!$color!!}>No Box</th>
            <th class="text-center" {!!$color!!}>Cek QC</th>
            <th class="text-center" {!!$color!!}>Admin</th>
        </tr>

    </thead>
    <tbody>
        @php
            $ttlPcs = 0;
            $ttlGr = 0;
        @endphp
        @foreach ($pengirimanBox as $d)
            @php
                $ttlPcs += $d->pcs_akhir;
                $ttlGr += $d->gr_akhir;
            @endphp
            <tr class="text-center">
                <td>{{ $d->partai }}</td>
                <td>{{ $d->tipe }}</td>
                <td>{{ $d->grade }}</td>
                <td>{{ number_format($d->pcs_akhir, 0) }}</td>
                <td>{{ number_format($d->gr_akhir, 0) }}</td>
                <td>{{ $d->no_box }}</td>
                <td>{{ $d->cek_akhir }}</td>
                <td>{{ strtoupper($d->admin) }}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="3" class="text-center" {!!$color!!}>TOTAL</th>
            <th class="text-center" {!!$color!!}>{{ number_format($ttlPcs, 0) }}</th>
            <th class="text-center" {!!$color!!}>{{ number_format($ttlGr, 0) }}</th>
            <th colspan="3" class="text-center" {!!$color!!}></th>
        </tr>
    </tbody>

</table>