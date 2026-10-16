<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpnameBulananSource
{
    public const POSITIONS = [
        'bk' => 'BK Sisa', 'cabut' => 'Cabut / EO', 'cetak' => 'Cetak',
        'sortir' => 'Sortir', 'grading' => 'Belum grading',
        'hasil_grading' => 'Hasil grading', 'wip1' => 'WIP 1', 'wip2' => 'WIP 2',
    ];

    public function read(string $box, string $position, ?int $invoiceId = null, bool $lock = false): array
    {
        if (DB::table('opname_bulanan_lines as l')->join('opname_bulanan_invoices as i', 'i.id', '=', 'l.invoice_id')
            ->where('l.no_box', $box)->whereIn('i.status', ['submitted', 'locked'])
            ->when($invoiceId, fn ($q) => $q->where('i.id', '!=', $invoiceId))->exists()) {
            $this->reject("Box {$box} sudah diajukan / dikunci dalam invoice opname lain.");
        }
        $rows = [];
        foreach (['bk' => ['no_box', 'id_bk'], 'cabut' => ['no_box', 'id_cabut'],
            'eo' => ['no_box', 'id_eo'], 'cetak_new' => ['no_box', 'id_cetak'],
            'sortir' => ['no_box', 'id_sortir'], 'formulir_sarang' => ['no_box', 'id_formulir'],
            'grading' => ['no_box_sortir', 'id_grading'], 'grading_partai' => ['box_pengiriman', 'id_grading'],
            'pengiriman' => ['no_box', 'id_pengiriman'], 'qc' => ['box_pengiriman', 'id']] as $table => [$column, $key]) {
            $query = DB::table($table)->where($column, $box)->orderBy($key);
            if ($lock) {
                $query->lockForUpdate();
            }
            $rows[$table] = $query->get()->map(fn ($r) => (array) $r)->all();
        }
        if ($rows['pengiriman']) {
            $this->reject("Box {$box} sudah masuk pengiriman.");
        }
        $forms = collect($rows['formulir_sarang']);
        $form = fn ($stage) => $forms->where('kategori', $stage)->last();
        $results = collect($rows['grading_partai']);
        if ($results->isNotEmpty()) {
            if ($results->contains(fn ($r) => $r['sudah_kirim'] === 'Y')
                || $results->contains(fn ($r) => strtolower(trim($r['grade'])) === 'susut')) {
                $this->reject("Box {$box} sudah dikirim atau merupakan susut.");
            }
            if ($results->pluck('grade')->map(fn ($v) => strtolower(trim($v)))->unique()->count() !== 1
                || $results->pluck('formulir')->unique()->count() !== 1
                || $results->pluck('cek_qc')->unique()->count() !== 1) {
                $this->reject("Box {$box} berisi beberapa grade / posisi. Pisahkan box terlebih dahulu.");
            }
            $first = $results->first();
            $actualPosition = $first['formulir'] === 'T' ? 'hasil_grading' : ($first['cek_qc'] === 'T' ? 'wip1' : 'wip2');
            $owner = $form($actualPosition === 'wip2' ? 'wip2' : 'wip') ?? $form('qc');
            $data = [
                'nm_partai' => mb_substr($results->pluck('nm_partai')->unique()->implode(', '), 0, 100),
                'grade' => $first['grade'], 'pcs' => $results->sum('pcs'), 'gr' => $results->sum('gr'),
                'cost_bk' => $results->sum('cost_bk'), 'cost_kerja' => $results->sum('cost_kerja'),
                'cost_op' => $results->sum('cost_op'), 'cost_cu' => $results->sum('cost_cu'),
                'pengawas_id' => (int) ($owner['id_penerima'] ?? 0), 'tipe' => $first['tipe'],
            ];
        } else {
            $bkRows = collect($rows['bk'])->where('kategori', 'cabut');
            if ($bkRows->count() !== 1) {
                $this->reject("Box {$box} tidak ditemukan atau memiliki lebih dari satu BK sumber.");
            }
            $bk = $bkRows->first();
            if ($rows['grading']) {
                $this->reject("Box {$box} sudah dipecah / digabung di grading. Gunakan nomor box hasil grading.");
            }
            $actualPosition = 'bk';
            $ownerId = (int) $bk['penerima'];
            $pcs = (float) $bk['pcs_awal'];
            $gr = (float) $bk['gr_awal'];
            $cetakClasses = DB::table('kelas_cetak')->pluck('kategori', 'id_kelas_cetak');
            $cetak = collect($rows['cetak_new'])->filter(fn ($r) => ($cetakClasses[$r['id_kelas_cetak']] ?? '') === 'CTK');
            $cu = collect($rows['cetak_new'])->filter(fn ($r) => ($cetakClasses[$r['id_kelas_cetak']] ?? '') === 'CU');
            foreach (['cabut' => collect([...$rows['cabut'], ...$rows['eo']]),
                'cetak' => $cetak, 'sortir' => collect($rows['sortir']), 'grading' => collect()] as $stage => $work) {
                $received = $form($stage === 'grading' ? 'grade' : $stage);
                if ($work->isEmpty() && ! $received && ! ($stage === 'cabut' && $ownerId > 0)) {
                    continue;
                }
                $actualPosition = $stage;
                if ($work->isNotEmpty()) {
                    $owners = $work->pluck('id_pengawas')->unique();
                    if ($owners->count() !== 1 && $stage === $position) {
                        $this->reject("Box {$box} sedang dipegang beberapa pengawas. Selesaikan pembagian terlebih dahulu.");
                    }
                    $ownerId = (int) $owners->first();
                    $pcs = $work->sum(fn ($r) => isset($r['gr_eo_awal']) ? 0 :
                        ($r['selesai'] === 'Y' ? ($r['pcs_akhir'] ?? $r['pcs_awal'] ?? 0) : ($r['pcs_awal_ctk'] ?? $r['pcs_awal'] ?? 0)));
                    $gr = $work->sum(fn ($r) => $r['selesai'] === 'Y' ?
                        ($r['gr_eo_akhir'] ?? $r['gr_akhir'] ?? 0) : ($r['gr_eo_awal'] ?? $r['gr_awal_ctk'] ?? $r['gr_awal'] ?? 0));
                } elseif ($received) {
                    $ownerId = (int) $received['id_penerima'];
                    $pcs = (float) $received['pcs_awal'];
                    $gr = (float) $received['gr_awal'];
                }
            }
            $data = [
                'nm_partai' => $bk['nm_partai'] ?? '-', 'grade' => $bk['ket'] ?? '',
                'pcs' => $pcs, 'gr' => $gr, 'pengawas_id' => $ownerId, 'tipe' => $bk['tipe'] ?? '',
                'cost_bk' => (float) $bk['gr_awal'] * (float) $bk['hrga_satuan'],
                'cost_kerja' => collect($rows['cabut'])->sum('ttl_rp') + collect($rows['eo'])->sum('ttl_rp')
                    + $cetak->sum('ttl_rp') + collect($rows['sortir'])->sum('ttl_rp'),
                'cost_op' => collect($rows['cabut'])->sum('cost_op') + collect($rows['eo'])->sum('cost_op'),
                'cost_cu' => $cu->sum('ttl_rp'),
            ];
        }
        if ($position !== $actualPosition) {
            $this->reject("Box {$box} berada di ".(self::POSITIONS[$actualPosition] ?? $actualPosition).', bukan posisi invoice ini.');
        }
        $data['no_box'] = $box;
        $data['posisi'] = $actualPosition;
        $data['rows'] = $rows;
        $data['fingerprint'] = hash('sha256', json_encode($rows));

        return $data;
    }

    public function reject(string $message): never
    {
        throw ValidationException::withMessages(['opname' => $message]);
    }
}
