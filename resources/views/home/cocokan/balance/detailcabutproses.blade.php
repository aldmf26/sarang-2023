<table class="table table-bordered " id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead">no</th>
            <th class="dhead">partai</th>
            <th class="dhead">pengawas</th>
            <th class="dhead">no box</th>
            <th class="dhead text-end">pcs</th>
            <th class="dhead text-end">gr</th>
            <th class="dhead text-end">modal bk</th>
            <th class="dhead text-end">cost kerja</th>
            <th class="dhead text-end">ttl rp</th>
            <th class="dhead text-end">rata-rata</th>
        </tr>
        <tr>
            <th class="dheadstock ">Box : {{ count($box) }}</th>
            <th class="dheadstock "></th>
            <th class="dheadstock "></th>
            <th class="dheadstock "></th>
            <th class="dheadstock  text-end">{{ number_format(sumBk($box, 'pcs'), 0) }}</th>
            <th class="dheadstock  text-end">{{ number_format(sumBk($box, 'gr'), 0) }}</th>
            <th class="dheadstock  text-end">{{ number_format(sumBk($box, 'ttl_rp'), 0) }}</th>
            <th class="dheadstock  text-end">{{ number_format(sumBk($box, 'cost_kerja'), 0) }}</th>
            <th class="dheadstock  text-end">{{ number_format(sumBk($box, 'ttl_rp') + sumBk($box, 'cost_kerja'), 0) }}
            </th>
            <th class="dheadstock  text-end">
                {{ empty(sumBk($box, 'gr')) ? 0 : number_format((sumBk($box, 'ttl_rp') + sumBk($box, 'cost_kerja')) / sumBk($box, 'gr'), 0) }}
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($box as $b)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $b->nm_partai }}</td>
                <td>{{ $b->name }}</td>
                <td>{{ $b->no_box }}</td>
                <td class="text-end">{{ number_format($b->pcs, 0) }}</td>
                <td class="text-end">{{ number_format($b->gr, 0) }}</td>
                <td class="text-end">{{ number_format($b->ttl_rp, 0) }}</td>
                <td class="text-end">{{ number_format($b->cost_kerja ?? 0, 0) }}</td>
                <td class="text-end">{{ number_format($b->ttl_rp + ($b->cost_kerja ?? 0), 0) }}</td>
                <td class="text-end">
                    {{ empty($b->gr) ? 0 : number_format(($b->ttl_rp + ($b->cost_kerja ?? 0)) / $b->gr, 0) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
