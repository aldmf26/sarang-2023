@php
    $sortirp = DB::selectOne("SELECT sum(a.pcs_awal) as pcs_awal, sum(if(a.selesai = 'Y',0, a.gr_awal - a.gr_akhir )) as ttl_gr, sum(a.pcs_akhir) as pcs_akhir, sum(a.ttl_rp) as ttl_rp
FROM sortir as a;");
@endphp
<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#gudang_bj_awal">
    <x-theme.card-stok title="11. BJ SP Proses" pcs="{{ $sortirp->pcs_awal - $sortirp->pcs_akhir }}"
        gr="{{ $sortirp->ttl_gr }}" ttl_rp="{{ $sortirp->ttl_rp }}"></x-theme.card-stok>
</a>
