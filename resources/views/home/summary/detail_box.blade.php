<h5>Partai : {{ $nm_partai }}</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">no box</th>
            <th class="dhead">pengawas</th>
            <th class="dhead">grade</th>
            <th class="dhead text-end">pcs</th>
            <th class="dhead text-end">gr</th>
            <th class="dhead text-end">total rp</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($bk as $b)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><a target="_blank"
                        href="{{ route('summary.history_box', ['no_box' => $b->no_box]) }}">{{ $b->no_box }}</a></td>
                <td>{{ $b->name }}</td>
                <td>{{ $b->tipe }}</td>
                <td class="text-end">{{ number_format($b->pcs_awal, 0) }}</td>
                <td class="text-end">{{ number_format($b->gr_awal, 0) }}</td>
                <td class="text-end">{{ number_format($b->gr_awal * $b->hrga_satuan, 0) }}</td>
            </tr>
        @endforeach
    </tbody>
    {{-- <tfoot>
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
    </tfoot> --}}
</table>
