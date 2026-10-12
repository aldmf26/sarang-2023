<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OpnameSortirImport extends OpnameCetakImport
{
    public const STATUSES = ['sortir' => 'Sortir sisa pengawas'];

    public const HEADERS = [...parent::HEADERS, 'id_kelas_cetak', 'id_pengawas_sortir'];

    public function validateRows(array $rows, string $status): array
    {
        if (isset(static::STATUSES[$status]) && $rows && count($rows) <= 500) {
            foreach ($rows as &$row) {
                if (! isset($row['id_anak_cetak'])) {
                    $children = DB::table('tb_anak')->where('id_pengawas', $row['id_pengawas_cetak'] ?? null)
                        ->where('berhenti', 'T')->pluck('id_anak');
                    $row['id_anak_cetak'] = $children->isEmpty() ? null : $children[random_int(0, $children->count() - 1)];
                }
            }
            unset($row);
        }

        return parent::validateRows($rows, $status);
    }

    protected function rules(): array
    {
        return array_replace(parent::rules(), [
            'id_kelas_cetak' => 'required|integer|exists:kelas_cetak,id_kelas_cetak,kategori,CTK',
            'id_pengawas_sortir' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('posisi_id', '!=', 1))],
            'id_anak_cetak' => 'required|integer|exists:tb_anak,id_anak',
        ]);
    }

    protected function rowErrors(array $row, int $line): array
    {
        $errors = parent::rowErrors($row, $line);
        $child = DB::table('tb_anak')->where('id_anak', $row['id_anak_cetak'])->first();
        if ((int) $child->id_pengawas !== (int) $row['id_pengawas_cetak'] || $child->berhenti !== 'T') {
            $errors[] = "Baris {$line}: anak Cetak tidak lagi sesuai dengan anak aktif pengawas Cetak. Unggah ulang file.";
        }

        return $errors;
    }

    protected function finishRow(array $row, array &$invoices): void
    {
        parent::finishRow($row, $invoices);
        DB::table('cetak_new')->insert([
            'id_pemberi' => $row['id_pengawas'], 'id_pengawas' => $row['id_pengawas_cetak'],
            'no_box' => $row['no_box'], 'tgl' => $row['tgl'], 'id_anak' => $row['id_anak_cetak'],
            'pcs_awal' => $row['pcs_awal'], 'gr_awal' => $row['gr_awal'],
            'pcs_tdk_cetak' => 0, 'gr_tdk_cetak' => 0,
            'pcs_awal_ctk' => $row['pcs_awal'], 'gr_awal_ctk' => $row['gr_awal'],
            'id_kelas_cetak' => $row['id_kelas_cetak'], 'rp_satuan' => 0,
            'pcs_akhir' => $row['pcs_awal'], 'pcs_hcr' => 0, 'gr_akhir' => $row['gr_awal'],
            'ttl_rp' => 0, 'rp_tambahan' => 0, 'bulan_dibayar' => 0,
            'tahun_dibayar' => (int) substr($row['tgl'], 0, 4),
            'selesai' => 'Y', 'capai' => 'Y', 'tipe_bayar' => 2, 'formulir' => 'Y', 'penutup' => 'T',
        ]);
        $group = 'sortir|'.$row['id_pengawas_cetak'].'|'.$row['id_pengawas_sortir'].'|'.$row['tgl'];
        if (! isset($invoices[$group])) {
            $last = DB::table('formulir_sarang')->where('kategori', 'sortir')->pluck('no_invoice')
                ->filter(fn ($value) => ctype_digit((string) $value))->map(fn ($value) => (int) $value)->max();
            $invoices[$group] = (string) (max(1000, (int) $last) + 1);
        }
        DB::table('formulir_sarang')->insert([
            'no_invoice' => $invoices[$group], 'no_box' => $row['no_box'],
            'id_pemberi' => $row['id_pengawas_cetak'], 'id_penerima' => $row['id_pengawas_sortir'],
            'pcs_awal' => $row['pcs_awal'], 'gr_awal' => $row['gr_awal'],
            'tanggal' => $row['tgl'], 'kategori' => 'sortir', 'selesai' => 'T', 'print' => 'T', 'sst_aktual' => 0,
        ]);
        $this->recordHancuran($row['no_box'], $invoices[$group], 'sortir');
    }
}
