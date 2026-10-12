<?php

namespace App\Services;

use App\Models\OpnameImportHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class OpnameImportCancellation
{
    public function cancel(int $historyId): int
    {
        $mysql = DB::connection()->getDriverName() === 'mysql';
        if ($mysql && (int) DB::selectOne("SELECT GET_LOCK('opname_cabut_import', 10) AS acquired")->acquired !== 1) {
            throw ValidationException::withMessages(['history' => 'Import lain sedang berjalan. Coba lagi.']);
        }
        try {
            return DB::transaction(function () use ($historyId) {
                $history = OpnameImportHistory::query()->lockForUpdate()->findOrFail($historyId);
                $categories = match ($history->tujuan) {
                    'bk' => [],
                    'stok' => ['cabut'], 'cetak' => ['cabut', 'cetak'],
                    'sortir' => ['cabut', 'cetak', 'sortir'], 'grade' => ['cabut', 'cetak', 'sortir', 'grade'],
                    default => null,
                };
                $boxes = array_column($history->rows ?? [], 'no_box');
                if ($categories === null || ! $boxes || count($boxes) !== (int) $history->jumlah_box
                    || count(array_unique($boxes)) !== count($boxes)) {
                    $this->reject('Data riwayat import tidak lengkap.');
                }
                $data = [];
                foreach (['bk', 'cabut', 'cetak_new', 'sortir', 'formulir_sarang', 'tb_hancuran', 'eo', 'pengiriman', 'grading'] as $table) {
                    $column = $table === 'grading' ? 'no_box_sortir' : 'no_box';
                    $data[$table] = DB::table($table)->whereIn($column, $boxes)->lockForUpdate()->get()->groupBy($column);
                }
                foreach (['cabut_spesial', 'cetak', 'grade', 'grading_selisih', 'grading_serah', 'grading_suntikan', 'siapkirim_grading', 'siapkirim_list_grading', 'tb_hariandll'] as $table) {
                    if (Schema::hasTable($table) && DB::table($table)->whereIn('no_box', $boxes)->lockForUpdate()->first()) {
                        $this->reject("Box import sudah memiliki transaksi terkait di {$table}.");
                    }
                }
                foreach ($history->rows as $row) {
                    $box = $row['no_box'];
                    foreach ($row['_transaction_ids'] ?? [] as $table => $ids) {
                        $idColumn = ['bk' => 'id_bk', 'cabut' => 'id_cabut', 'cetak_new' => 'id_cetak', 'sortir' => 'id_sortir', 'formulir_sarang' => 'id_formulir', 'tb_hancuran' => 'id'][$table] ?? null;
                        if (! $idColumn || ! isset($data[$table])) {
                            $this->reject('Referensi transaksi import tidak valid.');
                        }
                        $actualIds = $data[$table]->get($box, collect())->pluck($idColumn)->all();
                        sort($ids);
                        sort($actualIds);
                        if (array_map('strval', $actualIds) !== array_map('strval', $ids)) {
                            $this->reject("Referensi transaksi box {$box} di {$table} sudah berubah.");
                        }
                    }
                    foreach (['eo', 'grading', 'pengiriman'] as $table) {
                        if ($data[$table]->get($box, collect())->isNotEmpty()) {
                            $this->reject("Box {$box} sudah memiliki transaksi lanjutan di {$table}.");
                        }
                    }
                    $forms = $data['formulir_sarang']->get($box, collect());
                    if ($forms->count() !== count($categories)) {
                        $this->reject("Formulir box {$box} sudah berubah atau dilanjutkan ke tahap lain.");
                    }
                    $receivers = ['cabut' => $row['id_pengawas'], 'cetak' => $row['id_pengawas_cetak'] ?? null,
                        'sortir' => $row['id_pengawas_sortir'] ?? null, 'grade' => $row['id_pengawas_grading'] ?? null];
                    $givers = ['cabut' => $history->user_id, 'cetak' => $row['id_pengawas'],
                        'sortir' => $row['id_pengawas_cetak'] ?? null, 'grade' => $row['id_pengawas_sortir'] ?? null];
                    $hancuran = $data['tb_hancuran']->get($box, collect());
                    // Riwayat sebelum pencatatan hancuran boleh belum memiliki baris hancuran.
                    foreach ($categories as $category) {
                        $form = $this->one($forms->where('kategori', $category), $box, 'formulir '.$category);
                        $this->fields($form, ['id_pemberi' => $givers[$category], 'id_penerima' => $receivers[$category],
                            'pcs_awal' => $row['pcs_awal'], 'gr_awal' => $row['gr_awal'], 'tanggal' => $row['tgl'],
                            'selesai' => 'T', 'sst_aktual' => 0], $box);
                        if (! in_array((string) $form->no_invoice, array_map('strval', $history->invoices[$category] ?? []), true)) {
                            $this->reject("Invoice box {$box} sudah berubah.");
                        }
                        foreach ($hancuran->where('kategori', $category) as $item) {
                            $this->fields($item, ['no_invoice' => $form->no_invoice, 'pcs' => 0], $box);
                        }
                        if ($hancuran->where('kategori', $category)->count() > 1) {
                            $this->reject("Data hancuran box {$box} sudah berubah.");
                        }
                    }
                    if ($hancuran->whereNotIn('kategori', $categories)->isNotEmpty()) {
                        $this->reject("Box {$box} sudah memiliki hancuran dari tahap lanjutan.");
                    }
                    $bk = $this->one($data['bk']->get($box, collect()), $box, 'BK');
                    $this->fields($bk, ['nm_partai' => $row['nm_partai'], 'tipe' => $row['tipe'], 'ket' => $row['ket'], 'warna' => $row['warna'],
                        'tgl' => $row['tgl'], 'tgl_input' => $row['tgl'], 'penerima' => $row['id_pengawas'],
                        'pcs_awal' => $row['pcs_awal'], 'gr_awal' => $row['gr_awal'], 'hrga_satuan' => $row['hrga_satuan'],
                        'ttl_rp' => $row['gr_awal'] * $row['hrga_satuan'], 'kategori' => 'cabut', 'baru' => 'baru',
                        'selesai' => 'T', 'formulir' => $history->tujuan === 'bk' ? 'T' : 'Y', 'no_lot' => '0', 'pgws_grade' => '', 'susut' => 0,
                        'invoice_formulir' => $history->tujuan === 'bk' ? '' : $forms->firstWhere('kategori', 'cabut')->no_invoice], $box);
                    $stages = ['cabut' => count($categories) >= 2, 'cetak_new' => count($categories) >= 3, 'sortir' => count($categories) >= 4];
                    foreach ($stages as $table => $expected) {
                        $items = $data[$table]->get($box, collect());
                        if (! $expected) {
                            if ($items->isNotEmpty()) {
                                $this->reject("Box {$box} sudah diproses di {$table} setelah import.");
                            }
                            continue;
                        }
                        $item = $this->one($items, $box, $table);
                        $fields = ['pcs_awal' => $row['pcs_awal'], 'pcs_akhir' => $row['pcs_awal'],
                            'gr_awal' => $row['gr_awal'], 'gr_akhir' => $row['gr_awal'], 'ttl_rp' => 0, 'selesai' => 'Y', 'penutup' => 'T'];
                        $fields += match ($table) {
                            'cabut' => ['id_pengawas' => $row['id_pengawas'], 'id_anak' => $row['id_anak_cabut'], 'id_kelas' => $row['id_kelas_cabut'],
                                'tgl_terima' => $row['tgl'], 'tgl_serah' => $row['tgl'], 'cost_op' => 0, 'rupiah' => 0, 'formulir' => 'Y', 'bulan_dibayar' => 0,
                                'pcs_hcr' => 0, 'gr_hcr' => 0, 'pcs_flx' => 0, 'gr_flx' => 0, 'eot' => 0, 'invoice' => null],
                            'cetak_new' => ['id_pengawas' => $row['id_pengawas_cetak'], 'id_anak' => $row['id_anak_cetak'], 'id_kelas_cetak' => $row['id_kelas_cetak'],
                                'tgl' => $row['tgl'], 'pcs_awal_ctk' => $row['pcs_awal'], 'gr_awal_ctk' => $row['gr_awal'],
                                'pcs_tdk_cetak' => 0, 'gr_tdk_cetak' => 0, 'pcs_hcr' => 0, 'rp_satuan' => 0, 'rp_tambahan' => 0,
                                'capai' => 'Y', 'tipe_bayar' => 2, 'id_pemberi' => $row['id_pengawas'], 'formulir' => 'Y', 'bulan_dibayar' => 0],
                            'sortir' => ['id_pengawas' => $row['id_pengawas_sortir'], 'id_anak' => $row['id_anak_sortir'], 'id_kelas' => $row['id_kelas_sortir'],
                                'tgl' => $row['tgl'], 'tgl_input' => $row['tgl'], 'pcs_tdk_sortir' => 0, 'gr_tdk_sortir' => 0,
                                'rp_target' => 0, 'denda_sp' => 0, 'pcuc' => 0, 'pcus' => 0, 'bulan' => 0],
                        };
                        $this->fields($item, $fields + ['tahun_dibayar' => (int) substr($row['tgl'], 0, 4)], $box);
                    }
                }
                // Validasi seluruh batch selesai sebelum menghapus satu transaksi pun.
                foreach (['tb_hancuran', 'formulir_sarang', 'sortir', 'cetak_new', 'cabut', 'bk'] as $table) {
                    DB::table($table)->whereIn('no_box', $boxes)->delete();
                }
                $count = $history->jumlah_box;
                $history->delete();

                return $count;
            });
        } finally {
            if ($mysql) {
                DB::select("SELECT RELEASE_LOCK('opname_cabut_import')");
            }
        }
    }

    private function one($rows, string $box, string $table): object
    {
        if ($rows->count() !== 1) {
            $this->reject("Transaksi {$table} box {$box} tidak lagi sesuai riwayat import.");
        }

        return $rows->first();
    }

    private function fields(object $actual, array $expected, string $box): void
    {
        foreach ($expected as $field => $value) {
            if (! property_exists($actual, $field) || ($value === null ? $actual->$field !== null : (string) $actual->$field !== (string) $value)) {
                $this->reject("Transaksi box {$box} sudah berubah ({$field}).");
            }
        }
    }

    private function reject(string $reason): never
    {
        throw ValidationException::withMessages(['history' => $reason.' Pembatalan tidak dilakukan; seluruh data tetap tersimpan.']);
    }
}
