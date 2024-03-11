<div class="row">
    <div class="col-lg-12">
        <h5>{{ strtoupper($title) }}</h5>
        @if (!$query)
            <span class="text-warning">Data tidak ada data</span>
        @else
            @if ($no)
            @endif
            <table class="table table-hover table-bordered" id="tblOpname">
                <thead>
                    <tr class="sticky-header">
                        <th class="dhead" width="5">#</th>
                        <th class="dhead">Partai</th>
                        <th class="dhead">No Box</th>
                        <th class="dhead">Tipe</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-end">Ttl Rp</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ttlPcs = 0;
                        $ttlGr = 0;
                        $ttlTtlRp = 0;
                    @endphp
                    @foreach ($query as $i => $d)
                    @php
                            switch ($no) {
                                case 1:
                                    $pcs = $d->pcs ?? 0;
                                    $gr = $d->gr ?? 0;
                                    $ttl_rp = $d->total_rp ?? 0;
                                    break;
                                case 4:
                                    $pcs = $d->pcs_awal ?? 0;
                                    $gr = $d->gr_awal ?? 0;
                                    $ttl_rp = $d->ttl_rp ?? 0;
                                    break;
                                case 5:
                                    $pcs = $d->pcs_awal - $d->pcs_akhir ?? 0;
                                    $gr = $d->gr_awal - $d->gr_akhir ?? 0;
                                    $ttl_rp = $d->ttl_rp ?? 0;
                                    break;
                                case 6:
                                    $pcs = $d->pcs_akhir;
                                    $gr = $d->gr_akhir;
                                    $ttl_rp = $d->ttl_rp ?? 0;
                                    break;
                                default:
                                    break;
                            }

                            $ttlPcs += $pcs;
                            $ttlGr += $gr;
                            $ttlTtlRp += $ttl_rp;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $d->nm_partai ?? '-' }}</td>
                            <td>{{ $d->no_box ?? '-' }}</td>
                            <td>{{ $d->tipe ?? $d->grade }}</td>
                            <td align="right">{{ number_format($pcs, 0) }}</td>
                            <td align="right">{{ number_format($gr, 0) }}</td>
                            <td align="right">{{ number_format($ttl_rp, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-center dhead">Total</th>
                        <th class="text-end dhead">{{ number_format($ttlPcs, 0) }}</th>
                        <th class="text-end dhead">{{ number_format($ttlGr, 0) }}</th>
                        <th class="text-end dhead">{{ number_format($ttlTtlRp, 0) }}</th>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
</div>
