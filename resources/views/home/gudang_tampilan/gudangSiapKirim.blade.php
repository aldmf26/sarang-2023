@php
    $box_kirim = DB::select("SELECT a.* FROM `pengiriman`as a
            LEFT JOIN tb_grade as b on a.grade = b.nm_grade
            LEFT JOIN pengiriman_packing_list as c on a.no_nota_packing_list = c.no_nota
            WHERE a.tgl_pengiriman BETWEEN '$tgl1' and '$tgl2' AND a.no_nota_packing_list = ''
            ORDER BY b.urutan asc;");

    $ttl_pcs = 0;
    $ttl_gr = 0;
    $ttl_rp = 0;
    foreach ($box_kirim as $b) {
        $ttl_pcs += $b->pcs;
        $ttl_gr += $b->gr;
        $ttl_rp += $b->rp_gram * $b->gr_akhir;
    }
@endphp
<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#gudang_bj_awal">
    <x-theme.card-stok title="14. Gudang Siap Kirim" pcs="{{ $ttl_pcs }}" gr="{{ $ttl_gr }}"
        ttl_rp="{{ $ttl_rp }}"></x-theme.card-stok>
</a>
