<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center dhead" rowspan="3">GRADE</th>
                    <th class="text-center dhead">FROM</th>
                    <th class="text-center dhead"></th>
                    <th class="text-center dhead">{{ strtoupper($detailPacking->nm_packing) }}</th>
                </tr>
                <tr>
                    <th class="text-center dhead">DATE</th>
                    <th class="text-center dhead"></th>
                    <th class="text-center dhead">{{ tanggal($detailPacking->tgl) }}</th>
                </tr>
                <tr>
                    <th class="text-center dhead">BOX</th>
                    <th class="text-center dhead">PCS</th>
                    <th class="text-center dhead">GRAM</th>
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
                    $gr = $d->gr + $d->gr_naik;
                        $ttlBox += $d->box;
                        $ttlPcs += $d->pcs;
                        $ttlGr += $gr;
                    @endphp
                    <tr class="text-center">
                        <td>{{ $d->grade }}</td>
                        <td>{{ number_format($d->box,0) }}</td>
                        <td>{{ number_format($d->pcs,0) }}</td>
                        <td>{{ number_format($gr,0) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-center dhead">TOTAL</th>
                    <th class="text-center dhead">{{ number_format($ttlBox,0) }}</th>
                    <th class="text-center dhead">{{ number_format($ttlPcs,0) }}</th>
                    <th class="text-center dhead">{{ number_format($ttlGr,0) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
