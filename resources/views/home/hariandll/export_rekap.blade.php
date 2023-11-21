<table class="table" id="table1">
    <thead>
        <tr>
            <th class="dhead">Bulan</th>
            <th class="dhead ">Pgws</th>
            <th class="dhead ">Nama Anak</th>
            <th class="dhead">Ket</th>
            <th class="dhead">Lokasi</th>
            <th class="dhead text-end">Rupiah </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $c)
            @php
                $ttl = 0;
                foreach ($datas as $d) {
                    $ttl += $d->total_rupiah;
                }
            @endphp
            <tr>
                <td>{{ date('M y', strtotime($c->tgl)) }}</td>
                <td>{{ $c->name }}</td>
                <td>{{ $c->nama }}</td>
                <td>
                    {{ ucwords($c->ket) }}
                </td>
                <td>{{ ucwords($c->lokasi) }}</td>
                <td align="right">{{ $c->total_rupiah }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="5"></td>
            <td style="font-weight: bold">{{ $ttl }}</td>
        </tr>
    </tbody>
</table>
