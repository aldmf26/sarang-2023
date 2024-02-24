@php
    $gudangbj = DB::select("SELECT grade, sum(pcs) as pcs, sum(gr) as gr, sum(gr * rp_gram) as ttl_rp, sum(pcs_kredit) as pcs_kredit, sum(gr_kredit) as gr_kredit, sum(gr_kredit * rp_gram_kredit) as ttl_rp_kredit
     FROM `pengiriman_list_gradingbj` 
    GROUP BY grade 
    HAVING pcs - pcs_kredit <> 0 OR gr - gr_kredit <> 0");
@endphp
@php
    $ttl_pcs = 0;
    $ttl_gr = 0;
    $ttl_rp = 0;
@endphp
@foreach ($gudangbj as $no => $g)
    @php
        $ttl_pcs += $g->pcs - $g->pcs_kredit;
        $ttl_gr += $g->gr - $g->gr_kredit;
        $ttl_rp += $g->ttl_rp - $g->ttl_rp_kredit;
    @endphp
@endforeach
<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#gudang_bj_awal">
    <x-theme.card-stok title="9. BJ Grading Awal" pcs="{{ $ttl_pcs }}" gr="{{ $ttl_gr }}"
        ttl_rp="{{ $ttl_rp }}"></x-theme.card-stok>
</a>
<x-theme.modal idModal="gudang_bj_awal" size="modal-lg" btnSave="T" title="Detail">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="dhead">#</th>
                <th class="dhead">Grade</th>
                <th class="dhead text-end">Pcs <br> {{ number_format($ttl_pcs, 0) }}</th>
                <th class="dhead text-end">Gram <br> {{ number_format($ttl_gr, 0) }}</th>
                <th class="dhead text-end">Rp Gram <br> {{ empty($ttl_rp) ? 0 : number_format($ttl_rp / $ttl_gr, 0) }}
                </th>
                <th class="dhead text-end">Ttl Rp <br> {{ number_format($ttl_rp, 0) }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gudangbj as $no => $g)
                <tr>
                    <td>{{ $no + 1 }}</td>
                    <td>{{ $g->grade }}</td>
                    <td class="text-end">{{ $g->pcs - $g->pcs_kredit }}</td>
                    <td class="text-end">{{ $g->gr - $g->gr_kredit }}</td>
                    <td class="text-end">
                        {{ number_format(($g->ttl_rp - $g->ttl_rp_kredit) / ($g->gr - $g->gr_kredit), 0) }}
                    </td>
                    <td class="text-end">{{ number_format($g->ttl_rp - $g->ttl_rp_kredit, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-theme.modal>
