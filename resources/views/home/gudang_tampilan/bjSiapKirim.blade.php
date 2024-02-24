@php
    $pengiriman = DB::select("SELECT a.grade, a.no_box, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.gr * a.rp_gram) as ttl_rp, if(c.pcs_ambil is null,0,c.pcs_ambil) as pcs_ambil, if(c.gr_ambil is null,0,c.gr_ambil) as gr_ambil, if(c.ttl_rp_ambil is null,0,c.ttl_rp_ambil) as ttl_rp_ambil
        FROM siapkirim_list_grading as a
        left join (
        SELECT c.grade, sum(c.pcs) as pcs_ambil, sum(c.gr) as gr_ambil, sum(c.gr * c.rp_gram) as ttl_rp_ambil
            FROM pengiriman as c 
            GROUP by c.grade
        ) as c on c.grade = a.grade
        -- where a.no_box is not null 
        GROUP by a.grade
        HAVING pcs - pcs_ambil <> 0 OR gr - gr_ambil <> 0
        ");

    $ttl_pcs = 0;
    $ttl_gr = 0;
    $ttl_rp = 0;
    foreach ($pengiriman as $p) {
        $ttl_pcs += $p->pcs - $p->pcs_ambil;
        $ttl_gr += $p->gr - $p->gr_ambil;
        $ttl_rp += $p->ttl_rp_ambil;
    }
@endphp
<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#gudang_bj_awal">
    <x-theme.card-stok title="13. BJ Siap Kirim" pcs="{{ $ttl_pcs }}" gr="{{ $ttl_gr }}"
        ttl_rp="{{ $ttl_rp }}"></x-theme.card-stok>
</a>
