<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">bulan kerja</th>
            <th class="dhead">nama partai</th>
            <th class="dhead">nama pa herry</th>
            <th class="dhead">grade</th>
            <th class="dhead">pcs</th>
            <th class="dhead">gr</th>
            <th class="dhead">total rp</th>
            <th class="dhead">pcs ulang</th>
            <th class="dhead">gr ulang</th>
            <th class="dhead">susut</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($bk as $b)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date('m Y', strtotime($b->tgl)) }}</td>
                <td><a href="#" class="opennNobox" nm_partai="{{ $b->nm_partai }}">{{ $b->nm_partai }}</a></td>
                <td>{{ $b->nm_partai_dulu }}</td>
                <td>{{ $b->grade }}</td>
                <td>{{ number_format($b->pcs, 0) }}</td>
                <td>{{ number_format($b->gr, 0) }}</td>
                <td>{{ number_format($b->ttl_rp, 0) }}</td>
                <td>{{ number_format($b->pcs_bk, 0) }}</td>
                <td>{{ number_format($b->gr_bk, 0) }}</td>
                <td>{{ number_format((1 - $b->gr_bk / $b->gr) * 100, 1) }} %</td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td>Partai suntik</td>
            <td>Partai suntik</td>
            <td>-</td>
            <td>{{ number_format($suntik_ctk_sisa->pcs, 0) }}</td>
            <td>{{ number_format($suntik_ctk_sisa->gr, 0) }}</td>
            <td>{{ number_format($suntik_ctk_sisa->ttl_rp, 0) }}</td>
            <td>{{ number_format($suntik_ctk_sisa->pcs, 0) }}</td>
            <td>{{ number_format($suntik_ctk_sisa->gr, 0) }}</td>
            <td>0%</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>{{ number_format(sumBk($bk, 'pcs') + $suntik_ctk_sisa->pcs, 0) }}</th>
            <th>{{ number_format(sumBk($bk, 'gr') + $suntik_ctk_sisa->gr, 0) }}</th>
            <th>{{ number_format(sumBk($bk, 'ttl_rp') + $suntik_ctk_sisa->ttl_rp, 0) }}</th>
            <th>{{ number_format(sumBk($bk, 'pcs_bk') + $suntik_ctk_sisa->pcs, 0) }}</th>
            <th>{{ number_format(sumBk($bk, 'gr_bk') + $suntik_ctk_sisa->gr, 0) }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
