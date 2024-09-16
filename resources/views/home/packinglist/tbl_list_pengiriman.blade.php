<table id="tbl-list" class="table table-bordered table-sm" style="border: 1px solid #6779bc;">
    @php
        $color = "style='background-color: #C8C6C4; color:black'";
    @endphp
    <thead>
        <tr>
            <th class="text-center" {!! $color !!}>Partai</th>
            <th class="text-center" {!! $color !!}>Tipe</th>
            <th class="text-center" {!! $color !!}>Grade</th>
            <th class="text-center" {!! $color !!}>Pcs</th>
            <th class="text-center" {!! $color !!}>Pcs</th>
            <th class="text-center" {!! $color !!}>Grade 2</th>
            <th class="text-center" {!! $color !!}>Pcs 2</th>
            <th class="text-center" {!! $color !!}>Pcs 2</th>
            <th class="text-center" {!! $color !!}>No Box</th>
            <th class="text-center" {!! $color !!}>Cek QC</th>
            <th class="text-center" {!! $color !!}>Admin</th>
        </tr>

    </thead>
    <tbody>
        @php
            $ttlPcs = 0;
            $ttlGr = 0;
        @endphp
        @foreach ($pengirimanBox as $d)
            @php
                $ttlPcs += $d->pcs;
                $ttlGr += $d->gr;

                $cekGrade = $d->grade != $d->grade2;
                $cekPcs = $d->pcs != $d->pcs2;
                $cekGr = $d->gr != $d->gr2;

                $bg = 'bg-danger text-black';
            @endphp
            <tr class="text-center">
                <td>{{ $d->nm_partai }}</td>
                <td>{{ $d->tipe }}</td>
                <td>{{ $d->grade }}</td>
                <td>{{ number_format($d->pcs, 0) }}</td>
                <td>{{ number_format($d->gr, 0) }}</td>
                <td class="{{ $cekGrade ? $bg : '' }}">{{ $d->grade2 }}</td>
                <td class="{{ $cekPcs ? $bg : '' }}">{{ number_format($d->pcs2, 0) }}</td>
                <td class="{{ $cekGr ? $bg : '' }}">{{ number_format($d->gr2, 0) }}</td>
                <td>{{ $d->no_box }}</td>
                <td>{{ $d->cek_akhir }}</td>
                <td>{{ strtoupper($d->admin) }}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="3" class="text-center" {!! $color !!}>TOTAL</th>
            <th class="text-center" {!! $color !!}>{{ number_format($ttlPcs, 0) }}</th>
            <th class="text-center" {!! $color !!}>{{ number_format($ttlGr, 0) }}</th>
            <th class="text-center" {!! $color !!}></th>

            <th class="text-center" {!! $color !!}>{{ number_format($ttlPcs, 0) }}</th>
            <th class="text-center" {!! $color !!}>{{ number_format($ttlGr, 0) }}</th>
            <th colspan="3" class="text-center" {!! $color !!}></th>
        </tr>
    </tbody>

</table>
