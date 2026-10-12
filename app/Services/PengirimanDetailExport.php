<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PengirimanDetailExport
{
    public function packingLists()
    {
        $counts = DB::table('pengiriman')->select('no_nota')
            ->selectRaw('COUNT(DISTINCT no_box) as jumlah_box')->groupBy('no_nota');

        $lists = DB::table('pengiriman_packing_list as p')
            ->leftJoinSub($counts, 'k', 'k.no_nota', '=', 'p.no_nota')
            ->select('p.no_nota')->selectRaw('MAX(p.nm_packing) as nm_packing, MAX(p.tujuan) as tujuan, COALESCE(MAX(k.jumlah_box), 0) as jumlah_box')
            ->groupBy('p.no_nota')->orderByDesc('p.no_nota')->get();
        $boxes = DB::table('pengiriman')->select('no_nota', 'no_box')->whereIn('no_nota', $lists->pluck('no_nota'))
            ->distinct()->orderBy('no_box')->get()->groupBy('no_nota');
        return $lists->each(fn ($list) => $list->boxes = $boxes->get($list->no_nota, collect())->values());
    }

    public function details(array $notas, array $selectedBoxes = [])
    {
        $packing = DB::table('pengiriman_packing_list')->select('no_nota')
            ->selectRaw('MAX(tujuan) as tujuan')->whereIn('no_nota', $notas)->groupBy('no_nota');
        // Satu sumber per packing list/box, walaupun barcode atau metadata berulang.
        $boxes = DB::table('pengiriman')->whereIn('no_nota', $notas)
            ->when($selectedBoxes, fn ($query) => $query->whereIn('no_box', $selectedBoxes))
            ->select('no_nota', 'no_box')
            ->selectRaw('MAX(grade) as grade, MAX(tgl_input) as tgl, SUM(pcs) as pcs, SUM(gr) as gr,
                SUM(ttl_rp) as ttl_rp, SUM(cost_bk) as cost_bk, SUM(cost_kerja) as cost_kerja,
                SUM(cost_cu) as cost_cu, SUM(cost_op) as cost_op')
            ->groupBy('no_nota', 'no_box');

        return DB::query()->fromSub($boxes, 'k')
            ->leftJoinSub($packing, 'p', 'p.no_nota', '=', 'k.no_nota')
            ->leftJoin('grading_partai as g', 'g.box_pengiriman', '=', 'k.no_box')
            ->select('k.no_nota', 'p.tujuan', 'k.no_box as box_pengiriman', 'g.nm_partai', 'g.no_invoice', 'g.tipe')
            ->selectRaw('COALESCE(g.grade, k.grade) as grade, COALESCE(g.pcs, k.pcs) as pcs,
                COALESCE(g.gr, k.gr) as gr, COALESCE(g.tgl, k.tgl) as tgl,
                COALESCE(g.ttl_rp, k.ttl_rp) as ttl_rp, COALESCE(g.cost_bk, k.cost_bk) as cost_bk,
                COALESCE(g.cost_kerja, k.cost_kerja) as cost_kerja, COALESCE(g.cost_cu, k.cost_cu) as cost_cu,
                COALESCE(g.cost_op, k.cost_op) as cost_op')
            ->orderBy('k.no_nota')->orderBy('k.no_box')->orderBy('grade')->orderBy('g.id_grading')->get();
    }
}
