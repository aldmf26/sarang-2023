<input type="text" class="form-control form-control-sm mb-2" id="pencarianGudang">
<table class="table table-hover table-bordered" id="gudang">
    <thead class="">
        <tr>
            <th class="dhead ">Grade</th>
            <th class="dhead  text-end">Pcs </th>
            <th class="dhead  text-end">Gr </th>
        </tr>
    </thead>
    <tbody>
        @php
            $pcs = 0;
            $gr = 0;
        @endphp
        @foreach ($gudangbj as $no => $d)
            @php
                $pcs += $d->pcs;
                $gr += $d->gr;
            @endphp
            <tr>
                <td>{{ $d->grade }}</td>
                <td align="right">{{ number_format($d->pcs, 0) }}</td>
                <td align="right">{{ number_format($d->gr, 0) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text-end">Grand Total</th>
            <th class="text-end">{{ number_format($pcs, 0) }}</th>
            <th class="text-end">{{ number_format($gr, 0) }}</th>
        </tr>
    </tfoot>
</table>