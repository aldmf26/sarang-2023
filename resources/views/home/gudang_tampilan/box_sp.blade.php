@php
    $boxKecil = DB::select("SELECT a.no_box,a.grade,a.pcs_kredit as pcs,a.gr_kredit as gr,a.rp_gram_kredit as rp_gram,a.pengawas,a.no_grading, 
    if(b.pcs_awal is null ,0 ,b.pcs_awal) as pcs_awal , if(b.gr_awal is null ,0,b.gr_awal) as gr_awal
    FROM `pengiriman_list_gradingbj` as a 
        LEFT JOIN (
            SELECT b.no_box, b.pcs_awal, b.gr_awal
                FROM bk as b
                WHERE b.penerima != 0
                GROUP BY b.no_box
            ) as b on a.no_box = b.no_box
    WHERE a.no_box is not null;");
@endphp

@php
    $ttl_pcs = 0;
    $ttl_gr = 0;
    $ttl_rp = 0;
@endphp
@foreach ($boxKecil as $b)
    @php
        $ttl_pcs += $b->pcs - $b->pcs_awal;
        $ttl_gr += $b->gr - $b->gr_awal;
        $ttl_rp += 0;
    @endphp
@endforeach

<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#box_sp">
    <x-theme.card-stok title="10. Box SP" pcs="{{ $ttl_pcs }}" gr="{{ $ttl_gr }}"
        ttl_rp="{{ $ttl_rp }}"></x-theme.card-stok>
</a>
