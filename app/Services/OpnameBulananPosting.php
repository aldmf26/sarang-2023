<?php

namespace App\Services;

use App\Models\OpnameBulananInvoice;
use Illuminate\Support\Facades\DB;

class OpnameBulananPosting
{
    private int $boxCounter = 0;

    private array $invoices = [];

    public function __construct(private OpnameBulananSource $source) {}

    public function lock(int $id, object $user): OpnameBulananInvoice
    {
        $mysql = DB::connection()->getDriverName() === 'mysql';
        if ($mysql && (int) DB::selectOne("SELECT GET_LOCK('opname_cabut_import', 10) AS acquired")->acquired !== 1) {
            $this->source->reject('Transaksi box lain sedang berjalan. Coba kembali.');
        }
        try {
            return DB::transaction(function () use ($id, $user) {
                // Mutex juga berlaku saat menggunakan SQLite pada pemeriksaan terisolasi.
                DB::table('opname_bulanan_sequences')->where('name', 'posting')->lockForUpdate()->first();
                DB::table('opname_bulanan_sequences')->where('name', 'posting')->increment('value');
                $invoice = OpnameBulananInvoice::lockForUpdate()->findOrFail($id);
                abort_unless((int) $user->posisi_id === 1 || (int) $user->id === (int) $invoice->pengawas_id, 403);
                if ($invoice->status === 'locked') {
                    return $invoice;
                }
                if ($invoice->status !== 'submitted') {
                    $this->source->reject('Ajukan invoice kepada pengawas terlebih dahulu.');
                }
                $lines = $invoice->lines()->lockForUpdate()->get();
                if ($lines->isEmpty()) {
                    $this->source->reject('Invoice belum memiliki baris box.');
                }
                $sources = [];
                foreach ($lines as $line) {
                    $sources[$line->id] = $this->source->read($line->no_box, $invoice->posisi, $id, true);
                    if ($sources[$line->id]['fingerprint'] !== $line->snapshot['fingerprint']) {
                        $this->source->reject("Stok / biaya box {$line->no_box} berubah. Kembalikan invoice dan perbarui barisnya sebelum diajukan ulang.");
                    }
                }
                $this->boxCounter = 0;
                $this->invoices = [];
                $shipment = (string) max(1001, $this->maxNumber('pengiriman', 'no_nota') + 1);
                if (strlen($shipment) > 10) {
                    $this->source->reject('Nomor pengiriman melebihi kapasitas tabel.');
                }
                $shippingBoxes = [];
                $shippingTotals = [];
                foreach ($lines as $line) {
                    $source = $sources[$line->id];
                    $newBox = $this->nextBox();
                    $modal = round($line->cost_bk + $line->cost_kerja + $line->cost_op + $line->cost_cu, 2);
                    $price = $modal / (float) $line->gr_real;
                    $this->reopen($invoice, $line, $source, $newBox, $modal, $price, $user);

                    if ($source['rows']['grading_partai']) {
                        $shippingBox = $line->no_box;
                        $this->finishResults($source, $line);
                    } else {
                        // Box sebelum WIP digabung ke box kirim berdasarkan grade aktual.
                        $gradeKey = mb_strtolower(trim($line->grade_real));
                        $shippingBox = $shippingBoxes[$gradeKey] ??= $this->nextBox();
                        $this->completeEarly($invoice, $line->no_box, $line, $source, 'lama', $user);
                        $gradingInvoice = $this->formNumber('lama', 'grading');
                        DB::table('grading')->insert([
                            'no_box_sortir' => $line->no_box, 'no_box_grading' => $shippingBox,
                            'pcs' => $line->pcs_real, 'gr' => $line->gr_real, 'id_grade' => null,
                            'no_invoice' => $gradingInvoice, 'tgl' => $invoice->tanggal,
                            'admin' => $user->name, 'selesai' => 'Y',
                        ]);
                        $this->result($invoice, $line, $shippingBox, $gradingInvoice, $source['tipe'], $user, [
                            'cost_bk' => $line->cost_bk, 'cost_kerja' => $line->cost_kerja,
                            'cost_op' => $line->cost_op, 'cost_cu' => $line->cost_cu,
                        ], true, true);
                    }
                    foreach (['wip', 'qc', 'wip2'] as $stage) {
                        $this->form($invoice, 'lama', $stage, $shippingBox, $line->pcs_real, $line->gr_real, $user, true);
                    }
                    $line->update([
                        'no_box_baru' => $newBox, 'no_box_kirim' => $shippingBox,
                        'modal_baru' => $modal, 'harga_baru' => $price,
                    ]);
                    $shippingTotals[$shippingBox] ??= ['grade' => $line->grade_real, 'pcs' => 0, 'gr' => 0,
                        'cost_bk' => 0, 'cost_kerja' => 0, 'cost_op' => 0, 'cost_cu' => 0];
                    foreach (['pcs' => 'pcs_real', 'gr' => 'gr_real', 'cost_bk' => 'cost_bk',
                        'cost_kerja' => 'cost_kerja', 'cost_op' => 'cost_op', 'cost_cu' => 'cost_cu'] as $key => $field) {
                        $shippingTotals[$shippingBox][$key] += (float) $line->{$field};
                    }
                }
                foreach ($shippingTotals as $box => $total) {
                    $value = $total['cost_bk'] + $total['cost_kerja'] + $total['cost_op'] + $total['cost_cu'];
                    // Formulir baru memakai total box gabungan, bukan jumlah baris pertama saja.
                    foreach (['wip', 'qc', 'wip2'] as $stage) {
                        if (! isset($this->invoices['lama:'.$stage])) {
                            continue;
                        }
                        DB::table('formulir_sarang')->where('no_box', (string) $box)->where('kategori', $stage)
                            ->where('no_invoice', $this->invoices['lama:'.$stage])
                            ->update(['pcs_awal' => $total['pcs'], 'gr_awal' => $total['gr']]);
                    }
                    DB::table('pengiriman')->insert($total + [
                        'no_box' => (string) $box, 'no_nota' => $shipment, 'tgl_input' => $invoice->tanggal,
                        'admin' => $user->name, 'no_barcode' => '', 'cek_qc' => 'Y', 'selesai' => 'Y',
                        'baru' => 'Y', 'rp_gram' => $value / $total['gr'], 'ttl_rp' => $value - $total['cost_op'],
                        'opname_cost_locked' => true, 'cost_op_cek' => $invoice->no_invoice,
                    ]);
                    DB::table('pengiriman_packing_list')->insert([
                        'id_pengiriman' => $box, 'no_nota' => $shipment, 'tgl' => $invoice->tanggal,
                        'nm_packing' => $invoice->no_invoice, 'pgws_cek' => mb_substr($user->name, 0, 50),
                        'tujuan' => $invoice->tujuan, 'kadar' => 0, 'selesai' => 'Y',
                        'no_invoice_manual' => $invoice->no_invoice,
                    ]);
                }
                $invoice->update(['status' => 'locked', 'locked_at' => now(), 'locked_by' => $user->id,
                    'no_pengiriman' => $shipment, 'transaksi' => $this->invoices, 'alasan_kembali' => null]);

                return $invoice;
            }, 3);
        } finally {
            if ($mysql) {
                DB::select("SELECT RELEASE_LOCK('opname_cabut_import')");
            }
        }
    }

    private function reopen($invoice, $line, array $source, string $box, float $modal, float $price, object $user): void
    {
        $origin = collect($source['rows']['bk'])->firstWhere('kategori', 'cabut') ?? [];
        DB::table('bk')->insert([
            'no_box' => $box, 'nm_partai' => $line->nm_partai, 'no_lot' => $origin['no_lot'] ?? '0',
            'tipe' => $source['tipe'], 'ket' => $line->grade_real, 'warna' => $origin['warna'] ?? null,
            'tgl' => $invoice->tanggal, 'tgl_input' => $invoice->tanggal,
            'pengawas' => mb_substr($user->name, 0, 20),
            'penerima' => $invoice->posisi === 'bk' ? 0 : $invoice->pengawas_id,
            'pcs_awal' => $line->pcs_real, 'gr_awal' => $line->gr_real,
            'hrga_satuan' => $price, 'ttl_rp' => $modal, 'kategori' => 'cabut',
            'baru' => 'baru', 'selesai' => 'T', 'susut' => 0, 'pgws_grade' => '',
            'formulir' => $invoice->posisi === 'bk' ? 'T' : 'Y',
            'invoice_formulir' => $invoice->posisi === 'bk' ? '' : $this->formNumber('baru', 'cabut'),
        ]);
        $rank = array_search($invoice->posisi, array_keys(OpnameBulananSource::POSITIONS), true);
        if ($rank >= 1) {
            $this->form($invoice, 'baru', 'cabut', $box, $line->pcs_real, $line->gr_real, $user, $rank > 1);
        }
        if ($rank >= 2) {
            $this->work('cabut', $box, $line, $invoice, $user);
            $this->form($invoice, 'baru', 'cetak', $box, $line->pcs_real, $line->gr_real, $user, $rank > 2);
        }
        if ($rank >= 3) {
            $this->work('cetak_new', $box, $line, $invoice, $user);
            $this->form($invoice, 'baru', 'sortir', $box, $line->pcs_real, $line->gr_real, $user, $rank > 3);
        }
        if ($rank >= 4) {
            $this->work('sortir', $box, $line, $invoice, $user);
            $this->form($invoice, 'baru', 'grade', $box, $line->pcs_real, $line->gr_real, $user, $rank > 4);
        }
        if ($rank >= 5) {
            $gradingInvoice = $this->formNumber('baru', 'grading');
            $this->form($invoice, 'baru', 'grading', $box, $line->pcs_real, $line->gr_real, $user, true);
            DB::table('grading')->insert(['no_box_sortir' => $box, 'no_box_grading' => $box,
                'pcs' => $line->pcs_real, 'gr' => $line->gr_real, 'no_invoice' => $gradingInvoice,
                'tgl' => $invoice->tanggal, 'admin' => $user->name, 'selesai' => 'Y', 'id_grade' => null]);
            $this->result($invoice, $line, $box, $gradingInvoice, $source['tipe'], $user,
                ['cost_bk' => $modal, 'cost_kerja' => 0, 'cost_op' => 0, 'cost_cu' => 0], false, $rank >= 6);
            if ($rank >= 6) {
                $this->form($invoice, 'baru', 'wip', $box, $line->pcs_real, $line->gr_real, $user, true);
            }
            if ($rank >= 7) {
                DB::table('grading_partai')->where('box_pengiriman', $box)->update(['cek_qc' => 'Y']);
                $this->form($invoice, 'baru', 'wip2', $box, $line->pcs_real, $line->gr_real, $user, true);
            }
        }
    }

    private function completeEarly($invoice, string $box, $line, array $source, string $branch, object $user): void
    {
        DB::table('bk')->where('no_box', $box)->where('kategori', 'cabut')->update(['formulir' => 'Y', 'selesai' => 'Y']);
        foreach (['cabut', 'eo', 'cetak_new', 'sortir'] as $table) {
            $rows = collect($source['rows'][$table]);
            if ($rows->isEmpty()) {
                if ($table !== 'eo' && ! ($table === 'cabut' && $source['rows']['eo'])) {
                    $this->work($table, $box, $line, $invoice, $user);
                }

                continue;
            }
            $active = $rows->where('selesai', 'T')->values();
            $initialGr = $active->sum(fn ($r) => $r['gr_eo_awal'] ?? $r['gr_awal_ctk'] ?? $r['gr_awal'] ?? 0);
            $pcsLeft = (int) $line->pcs_real;
            $grLeft = (float) $line->gr_real;
            foreach ($active as $index => $row) {
                $ratio = $initialGr > 0 ? (float) ($row['gr_eo_awal'] ?? $row['gr_awal_ctk'] ?? $row['gr_awal'] ?? 0) / $initialGr : 1 / $active->count();
                $pcs = $index === $active->count() - 1 ? $pcsLeft : (int) floor($line->pcs_real * $ratio);
                $gr = $index === $active->count() - 1 ? $grLeft : round($line->gr_real * $ratio, 2);
                $pcsLeft -= $pcs;
                $grLeft -= $gr;
                $fields = ['selesai' => 'Y', $table === 'eo' ? 'gr_eo_akhir' : 'gr_akhir' => $gr];
                if ($table !== 'eo') {
                    $fields['pcs_akhir'] = $pcs;
                }
                if (in_array($table, ['cabut', 'eo'])) {
                    $fields['tgl_serah'] = $invoice->tanggal;
                }
                $key = ['cabut' => 'id_cabut', 'eo' => 'id_eo', 'cetak_new' => 'id_cetak', 'sortir' => 'id_sortir'][$table];
                DB::table($table)->where($key, $row[$key])->update($fields);
            }
            if (in_array($table, ['cabut', 'cetak_new'])) {
                DB::table($table)->where('no_box', $box)->update(['formulir' => 'Y']);
            }
        }
        DB::table('cabut')->where('no_box', $box)->update(['opname_cost_locked' => true]);
        DB::table('eo')->where('no_box', $box)->update(['opname_cost_locked' => true]);
        foreach (['cabut', 'cetak', 'sortir', 'grade', 'grading'] as $stage) {
            $this->form($invoice, $branch, $stage, $box, $line->pcs_real, $line->gr_real, $user, true);
        }
    }

    private function finishResults(array $source, $line): void
    {
        $rows = collect($source['rows']['grading_partai']);
        $totalGr = (float) $rows->sum('gr');
        $remainingPcs = (int) $line->pcs_real;
        $remainingGr = (float) $line->gr_real;
        foreach ($rows as $index => $row) {
            $ratio = $totalGr > 0 ? (float) $row['gr'] / $totalGr : 1 / $rows->count();
            $pcs = $index === $rows->count() - 1 ? $remainingPcs : (int) floor($line->pcs_real * $ratio);
            $gr = $index === $rows->count() - 1 ? $remainingGr : round($line->gr_real * $ratio, 2);
            $remainingPcs -= $pcs;
            $remainingGr -= $gr;
            DB::table('grading_partai')->where('id_grading', $row['id_grading'])->update([
                'pcs' => $pcs, 'gr' => $gr, 'grade' => $line->grade_real, 'nm_partai' => $line->nm_partai,
                'formulir' => 'Y', 'cek_qc' => 'Y', 'sudah_kirim' => 'Y', 'opname_cost_locked' => true,
            ]);
        }
        // Bekukan pula box asal hasil gabungan agar alokasi OP berikutnya tidak mengubah biaya historisnya.
        $origins = DB::table('grading')->whereIn('no_invoice', $rows->pluck('no_invoice'))->pluck('no_box_sortir');
        foreach (['cabut', 'eo'] as $table) {
            DB::table($table)->whereIn('no_box', $origins)->update(['opname_cost_locked' => true]);
        }
        DB::table('qc')->where('box_pengiriman', $line->no_box)->update(['wip2' => 'Y', 'selesai' => 'Y']);
    }

    private function result($invoice, $line, string $box, string $gradingInvoice, string $type, object $user, array $costs, bool $sent, bool $wip): void
    {
        // Konvensi tabel hasil/pengiriman: ttl_rp belum memasukkan cost_op.
        $value = array_sum($costs) - $costs['cost_op'];
        DB::table('grading_partai')->insert($costs + [
            'nm_partai' => $line->nm_partai, 'urutan' => (int) $gradingInvoice,
            'no_invoice' => $gradingInvoice, 'box_pengiriman' => $box, 'grade' => $line->grade_real,
            'tipe' => $type, 'pcs' => $line->pcs_real, 'gr' => $line->gr_real,
            'tgl' => $invoice->tanggal, 'admin' => $user->name, 'cost' => $value / $line->gr_real,
            'ttl_rp' => $value, 'formulir' => $wip ? 'Y' : 'T', 'cek_qc' => $sent ? 'Y' : 'T',
            'sudah_kirim' => $sent ? 'Y' : 'T', 'sudah_print' => 'T', 'not_oke' => 'T',
            'bulan' => (int) substr($invoice->tanggal, 5, 2), 'tahun' => (int) substr($invoice->tanggal, 0, 4),
            'opname_cost_locked' => $sent,
        ]);
    }

    private function work(string $table, string $box, $line, $invoice, object $user): void
    {
        $common = ['no_box' => $box, 'id_pengawas' => $invoice->pengawas_id,
            'id_anak' => 2, 'pcs_awal' => $line->pcs_real, 'gr_awal' => $line->gr_real,
            'pcs_akhir' => $line->pcs_real, 'gr_akhir' => $line->gr_real,
            'ttl_rp' => 0, 'selesai' => 'Y', 'penutup' => 'T', 'tahun_dibayar' => (int) substr($invoice->tanggal, 0, 4)];
        $data = match ($table) {
            'cabut' => ['id_kelas' => 126, 'tgl_terima' => $invoice->tanggal, 'tgl_serah' => $invoice->tanggal,
                'bulan_dibayar' => 0, 'cost_op' => 0, 'rupiah' => 0, 'formulir' => 'Y', 'invoice' => null,
                'pcs_hcr' => 0, 'gr_hcr' => 0, 'pcs_flx' => 0, 'gr_flx' => 0, 'eot' => 0],
            'cetak_new' => ['id_pemberi' => $user->id, 'tgl' => $invoice->tanggal,
                'id_kelas_cetak' => $this->cetakClass(), 'pcs_tdk_cetak' => 0, 'gr_tdk_cetak' => 0,
                'pcs_awal_ctk' => $line->pcs_real, 'gr_awal_ctk' => $line->gr_real, 'rp_satuan' => 0,
                'pcs_hcr' => 0, 'rp_tambahan' => 0, 'bulan_dibayar' => 0,
                'capai' => 'Y', 'tipe_bayar' => 2, 'formulir' => 'Y'],
            'sortir' => ['id_kelas' => 11, 'tgl' => $invoice->tanggal, 'tgl_input' => $invoice->tanggal,
                'bulan' => 0, 'pcs_tdk_sortir' => 0, 'gr_tdk_sortir' => 0,
                'rp_target' => 0, 'denda_sp' => 0, 'pcuc' => 0, 'pcus' => 0],
        };
        DB::table($table)->insert($common + $data);
    }

    private function cetakClass(): int
    {
        $id = DB::table('kelas_cetak')->where('kategori', 'CTK')->orderBy('id_kelas_cetak')->value('id_kelas_cetak');
        if (! $id) {
            $this->source->reject('Kelas Cetak CTK belum tersedia.');
        }

        return (int) $id;
    }

    private function form($invoice, string $branch, string $stage, string $box, $pcs, $gr, object $user, bool $done): void
    {
        $existing = DB::table('formulir_sarang')->where('no_box', $box)->where('kategori', $stage);
        if ($existing->exists()) {
            if ($done) {
                $existing->update(['selesai' => 'Y']);
            }

            return;
        }
        DB::table('formulir_sarang')->insert([
            'no_box' => $box, 'no_invoice' => $this->formNumber($branch, $stage),
            'id_pemberi' => $user->id, 'id_penerima' => $invoice->pengawas_id,
            'pcs_awal' => $pcs, 'gr_awal' => $gr, 'tanggal' => $invoice->tanggal,
            'kategori' => $stage, 'selesai' => $done ? 'Y' : 'T', 'print' => 'T', 'sst_aktual' => 0,
        ]);
    }

    private function formNumber(string $branch, string $stage): string
    {
        return $this->invoices[$branch.':'.$stage] ??= (string) max(1001,
            $this->maxNumber('formulir_sarang', 'no_invoice', $stage) + 1,
            $stage === 'grading' ? $this->maxNumber('grading_partai', 'no_invoice') + 1 : 0);
    }

    private function maxNumber(string $table, string $column, ?string $stage = null): int
    {
        $type = DB::connection()->getDriverName() === 'mysql' ? 'UNSIGNED' : 'INTEGER';

        return (int) DB::table($table)->when($stage, fn ($q) => $q->where('kategori', $stage))
            ->selectRaw("MAX(CAST({$column} AS {$type})) AS number")->value('number');
    }

    private function nextBox(): string
    {
        if (! $this->boxCounter) {
            $this->boxCounter = $this->maxNumber('bk', 'no_box', 'cabut');
        }
        $this->boxCounter++;
        if ($this->boxCounter === 9999) {
            $this->boxCounter++;
        }
        if ($this->boxCounter > 999999999) {
            $this->source->reject('Nomor box baru melebihi 9 digit.');
        }

        return (string) $this->boxCounter;
    }
}
