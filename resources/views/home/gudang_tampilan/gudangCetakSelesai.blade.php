@php
    $cetak = DB::select("SELECT b.tipe,a.id_cetak,a.no_box,sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, b.ttl_rp, c.cost_cabut, ((a.pcs_akhir * a.rp_pcs) + a.rp_harian - (a.pcs_hcr * d.denda_hcr )) as cost_cetak
        FROM `cetak` as a
        join bk as b on a.no_box = b.no_box and b.kategori = 'cetak'
        left join (
        	SELECT c.no_box , sum(c.ttl_rp) as cost_cabut
            FROM cabut as c
            GROUP by c.no_box
        ) as c on c.no_box = a.no_box
        left join kelas_cetak as d on d.id_kelas_cetak = a.id_kelas
        LEFT JOIN `pengiriman_gradingbj` AS p ON a.no_box = p.no_box
        WHERE a.selesai = 'Y'  AND p.no_box IS NULL GROUP BY no_box ORDER BY b.tipe ASC;");
@endphp

@php
    $ttl_pcs = 0;
    $ttl_gr = 0;
    $ttl_rp = 0;
@endphp
@foreach ($cetak as $c)
    @php
        $ttl_pcs += $c->pcs_akhir;
        $ttl_gr += $c->gr_akhir;
        $ttl_rp += $c->ttl_rp + $c->cost_cabut + $c->cost_cetak;
    @endphp
@endforeach

<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#gudang_cetak">
    <x-theme.card-stok title="8. Gudang Cetak Selesai" pcs="{{ $ttl_pcs }}" gr="{{ $ttl_gr }}"
        ttl_rp="{{ $ttl_rp }}"></x-theme.card-stok>
</a>

<x-theme.modal idModal="gudang_cetak" size="modal-lg" btnSave="T" title="Detail">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th class="dhead">Tipe</th>
                <th class="dhead">No Box</th>
                <th class="dhead text-end">Pcs</th>
                <th class="dhead text-end">Gr</th>
                <th class="dhead text-end">Ttl Rp</th>
                <th class="dhead text-end">Cost Cabut</th>
                <th class="dhead text-end">Cost Cetak</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cetak as $c)
                <tr>
                    <td>{{ $c->tipe }}</td>
                    <td>{{ $c->no_box }}</td>
                    <td align="right">{{ $c->pcs_akhir }}</td>
                    <td align="right">{{ $c->gr_akhir }}</td>
                    <td align="right">{{ number_format($c->ttl_rp, 0) }}</td>
                    <td align="right">{{ number_format($c->cost_cabut, 0) }}</td>
                    <td align="right">{{ number_format($c->cost_cetak, 0) }}</td>
                </tr>
            @endforeach


        </tbody>
    </table>
</x-theme.modal>
