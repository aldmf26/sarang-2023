<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OpnameBulananUncounted
{
    public function query(string $month, array $filters = [])
    {
        $stock = DB::query()->fromSub($this->early()->unionAll($this->late()), 'stock');
        foreach (['no_box', 'no_box_baru'] as $column) {
            $stock->whereNotExists(function ($q) use ($column, $month) {
                $q->selectRaw('1')->from('opname_bulanan_lines as l')
                    ->join('opname_bulanan_invoices as i', 'i.id', '=', 'l.invoice_id')
                    ->whereColumn('l.'.$column, 'stock.no_box')->where('i.bulan', $month.'-01')
                    ->whereIn('i.status', $column === 'no_box_baru' ? ['locked'] : ['draft', 'submitted', 'locked']);
            });
        }

        return $stock->leftJoin('users as owner', 'owner.id', '=', 'stock.pengawas_id')
            ->select('stock.*')->selectRaw("COALESCE(owner.name, 'Tanpa pengawas') AS pengawas")
            ->where(fn ($q) => $q->where('stock.pcs', '>', 0)->orWhere('stock.gr', '>', 0))
            ->when($filters['posisi'] ?? null, fn ($q, $v) => $q->where('stock.posisi', $v))
            ->when(isset($filters['pengawas_id']), fn ($q) => $q->where('stock.pengawas_id', (int) $filters['pengawas_id']))
            ->when($filters['q'] ?? null, fn ($q, $v) => $q->where(fn ($q) => $q
                ->where('stock.no_box', 'like', '%'.$v.'%')->orWhere('stock.nm_partai', 'like', '%'.$v.'%')
                ->orWhere('stock.grade', 'like', '%'.$v.'%')));
    }

    private function forms($query, string $category, string $alias, string $box): void
    {
        $latest = DB::table('formulir_sarang')->where('kategori', $category)
            ->select('no_box')->selectRaw('MAX(id_formulir) AS latest_id')->groupBy('no_box');
        $query->leftJoinSub($latest, $alias.'_latest', $alias.'_latest.no_box', '=', $box)
            ->leftJoin('formulir_sarang as '.$alias, $alias.'.id_formulir', '=', $alias.'_latest.latest_id');
    }

    private function work(string $table, string $pcs, string $grams, ?string $category = null)
    {
        $q = DB::table($table.' as w');
        if ($category) {
            $q->join('kelas_cetak as kelas', 'kelas.id_kelas_cetak', '=', 'w.id_kelas_cetak')->where('kelas.kategori', $category);
        }

        return $q->select('w.no_box')->selectRaw("COUNT(*) AS count_rows, SUM({$pcs}) AS pcs, SUM({$grams}) AS gr,
            MAX(w.id_pengawas) AS owner_id, MIN(w.id_pengawas) AS min_owner")->groupBy('w.no_box');
    }

    private function early()
    {
        $bk = DB::table('bk')->where('kategori', 'cabut')->where('baru', 'baru')->select('no_box')
            ->selectRaw('MAX(nm_partai) AS nm_partai, MAX(ket) AS grade, SUM(pcs_awal) AS pcs,
                SUM(gr_awal) AS gr, MAX(penerima) AS owner_id, COUNT(*) AS count_rows')->groupBy('no_box');
        $q = DB::query()->fromSub($bk, 'b');
        $q->leftJoinSub($this->work('cabut', "CASE WHEN w.selesai = 'Y' THEN COALESCE(w.pcs_akhir, w.pcs_awal, 0) ELSE COALESCE(w.pcs_awal, 0) END",
            "CASE WHEN w.selesai = 'Y' THEN COALESCE(w.gr_akhir, 0) ELSE COALESCE(w.gr_awal, 0) END"), 'cb', 'cb.no_box', '=', 'b.no_box');
        $q->leftJoinSub($this->work('eo', '0', "CASE WHEN w.selesai = 'Y' THEN COALESCE(w.gr_eo_akhir, 0) ELSE COALESCE(w.gr_eo_awal, 0) END"), 'eo', 'eo.no_box', '=', 'b.no_box');
        $q->leftJoinSub($this->work('cetak_new', "CASE WHEN w.selesai = 'Y' THEN COALESCE(w.pcs_akhir, w.pcs_awal, 0) ELSE COALESCE(w.pcs_awal_ctk, 0) END",
            "CASE WHEN w.selesai = 'Y' THEN COALESCE(w.gr_akhir, 0) ELSE COALESCE(w.gr_awal_ctk, 0) END", 'CTK'), 'ct', 'ct.no_box', '=', 'b.no_box');
        $q->leftJoinSub($this->work('sortir', "CASE WHEN w.selesai = 'Y' THEN COALESCE(w.pcs_akhir, w.pcs_awal, 0) ELSE COALESCE(w.pcs_awal, 0) END",
            "CASE WHEN w.selesai = 'Y' THEN COALESCE(w.gr_akhir, 0) ELSE COALESCE(w.gr_awal, 0) END"), 'sr', 'sr.no_box', '=', 'b.no_box');
        foreach (['cabut' => 'fc', 'cetak' => 'ft', 'sortir' => 'fs', 'grade' => 'fg'] as $stage => $alias) {
            $this->forms($q, $stage, $alias, 'b.no_box');
        }
        $allocated = DB::table('grading')->select('no_box_sortir')
            ->selectRaw('SUM(pcs) AS pcs, SUM(gr) AS gr, COUNT(*) AS count_rows')->groupBy('no_box_sortir');
        $q->leftJoinSub($allocated, 'ga', 'ga.no_box_sortir', '=', 'b.no_box');
        $position = "CASE WHEN fg.id_formulir IS NOT NULL OR ga.count_rows > 0 THEN 'grading'
            WHEN sr.count_rows > 0 OR fs.id_formulir IS NOT NULL THEN 'sortir'
            WHEN ct.count_rows > 0 OR ft.id_formulir IS NOT NULL THEN 'cetak'
            WHEN cb.count_rows > 0 OR eo.count_rows > 0 OR fc.id_formulir IS NOT NULL OR b.owner_id > 0 THEN 'cabut' ELSE 'bk' END";
        $owner = 'CASE WHEN fg.id_formulir IS NOT NULL THEN fg.id_penerima
            WHEN sr.count_rows > 0 THEN sr.owner_id WHEN fs.id_formulir IS NOT NULL THEN fs.id_penerima
            WHEN ct.count_rows > 0 THEN ct.owner_id WHEN ft.id_formulir IS NOT NULL THEN ft.id_penerima
            WHEN cb.count_rows > 0 THEN cb.owner_id WHEN eo.count_rows > 0 THEN eo.owner_id
            WHEN fc.id_formulir IS NOT NULL THEN fc.id_penerima ELSE b.owner_id END';
        $quantity = function ($field) {
            return "CASE WHEN fg.id_formulir IS NOT NULL THEN fg.{$field}_awal - COALESCE(ga.{$field}, 0)
                WHEN sr.count_rows > 0 THEN sr.{$field} WHEN fs.id_formulir IS NOT NULL THEN fs.{$field}_awal
                WHEN ct.count_rows > 0 THEN ct.{$field} WHEN ft.id_formulir IS NOT NULL THEN ft.{$field}_awal
                WHEN cb.count_rows > 0 OR eo.count_rows > 0 THEN COALESCE(cb.{$field}, 0) + COALESCE(eo.{$field}, 0)
                WHEN fc.id_formulir IS NOT NULL THEN fc.{$field}_awal ELSE b.{$field} END";
        };

        return $q->select('b.no_box', 'b.nm_partai', 'b.grade')->selectRaw($quantity('pcs').' AS pcs, '.$quantity('gr').' AS gr, '.$owner.' AS pengawas_id, '.$position." AS posisi,
            CASE WHEN b.count_rows > 1 THEN 'BK sumber ganda'
                WHEN ga.count_rows > 0 THEN 'Grading sebagian: periksa sisa box'
                WHEN ($position) = 'cabut' AND (cb.owner_id != cb.min_owner OR eo.owner_id != eo.min_owner OR cb.owner_id != eo.owner_id)
                    OR ($position) = 'cetak' AND ct.owner_id != ct.min_owner
                    OR ($position) = 'sortir' AND sr.owner_id != sr.min_owner THEN 'Box dipegang beberapa pengawas'
                ELSE '' END AS issue")
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('grading_partai as gp')->whereColumn('gp.box_pengiriman', 'b.no_box'))
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('pengiriman as p')->whereColumn('p.no_box', 'b.no_box'))
            ->where(function ($q) {
                $q->whereNull('ga.count_rows')->orWhereNotNull('fg.id_formulir');
            });
    }

    private function late()
    {
        $q = DB::table('grading_partai as g')->where('g.sudah_kirim', 'T')
            ->whereRaw("LOWER(TRIM(g.grade)) != 'susut'")
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('pengiriman as p')->whereColumn('p.no_box', 'g.box_pengiriman'));
        foreach (['wip' => 'fw', 'qc' => 'fq', 'wip2' => 'f2'] as $stage => $alias) {
            $this->forms($q, $stage, $alias, 'g.box_pengiriman');
        }

        return $q->selectRaw("g.box_pengiriman AS no_box, GROUP_CONCAT(DISTINCT g.nm_partai) AS nm_partai,
            GROUP_CONCAT(DISTINCT g.grade) AS grade, SUM(g.pcs) AS pcs, SUM(g.gr) AS gr,
            CASE WHEN MAX(g.formulir) = 'Y' AND MAX(g.cek_qc) = 'Y' THEN COALESCE(MAX(f2.id_penerima), MAX(fq.id_penerima), 0)
                ELSE COALESCE(MAX(fw.id_penerima), MAX(fq.id_penerima), 0) END AS pengawas_id,
            CASE WHEN MAX(g.formulir) = 'T' THEN 'hasil_grading' WHEN MAX(g.cek_qc) = 'T' THEN 'wip1' ELSE 'wip2' END AS posisi,
            CASE WHEN COUNT(DISTINCT LOWER(TRIM(g.grade))) > 1 OR COUNT(DISTINCT g.formulir) > 1 OR COUNT(DISTINCT g.cek_qc) > 1
                THEN 'Periksa grade / posisi box gabungan' ELSE '' END AS issue")->groupBy('g.box_pengiriman');
    }
}
