<table class="table table-bordered " id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead">no</th>
            <th class="dhead">box pengiriman</th>
            <th class="dhead">partai</th>
            <th class="dhead">grade</th>
            <th class="dhead text-end">pcs</th>
            <th class="dhead text-end">gr</th>
            <th class="dhead text-end">modal bk</th>
            <th class="dhead text-end">cost kerja</th>
            <th class="dhead text-end">cost operasional</th>
            <th class="dhead text-end">total rp</th>
            <th class="dhead text-end">rata-rata</th>
        </tr>
        <tr>
            <th class="dheadstock ">Box : {{ count($box) }}</th>
            <th class="dheadstock "></th>
            <th class="dheadstock "></th>
            <th class="dheadstock "></th>
            <th class="dheadstock ">{{ number_format(sumBk($box, 'pcs'), 0) }}</th>
            <th class="dheadstock ">{{ number_format(sumBk($box, 'gr'), 0) }}</th>
            <th class="dheadstock ">{{ number_format(sumBk($box, 'cost_bk'), 0) }}</th>
            <th class="dheadstock ">{{ number_format(sumBk($box, 'cost_kerja'), 0) }}</th>
            <th class="dheadstock ">{{ number_format(sumBk($box, 'cost_op'), 0) }}</th>
            <th class="dheadstock ">
                {{ number_format(sumBk($box, 'cost_bk') + sumBk($box, 'cost_kerja') + sumBk($box, 'cost_op'), 0) }}
            </th>
            <th class="dheadstock ">
                {{ empty(sumBk($box, 'gr')) ? 0 : number_format((sumBk($box, 'cost_bk') + sumBk($box, 'cost_kerja') + sumBk($box, 'cost_op')) / sumBk($box, 'gr'), 0) }}
            </th>

        </tr>
    </thead>
    <tbody>
        @foreach ($box as $b)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $b->box_pengiriman }}</td>
                <td>{{ $b->daftar_partai }}</td>
                <td>{{ $b->grade }}</td>
                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                <td class="text-end">{{ number_format($b->cost_bk, 0) }}</td>
                <td class="text-end">{{ number_format($b->cost_kerja, 0) }}</td>
                <td class="text-end">{{ number_format($b->cost_op, 0) }}</td>
                <td class="text-end">{{ number_format($b->cost_op + $b->cost_bk + $b->cost_kerja, 0) }}</td>
                <td class="text-end">
                    {{ empty($b->gr) ? 0 : number_format(($b->cost_op + $b->cost_bk + $b->cost_kerja) / $b->gr, 0) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
