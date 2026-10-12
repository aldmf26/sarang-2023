<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OpnameGradingImport extends OpnameSortirImport
{
    public const STATUSES = ['grade' => 'Sisa belum grading'];

    public const HEADERS = [...parent::HEADERS, 'id_pengawas_grading'];

    public function validateRows(array $rows, string $status): array
    {
        if (isset(static::STATUSES[$status]) && $rows && count($rows) <= 500) {
            foreach ($rows as &$row) {
                // Mengikuti alur Sortir lewat pada penyerahan semua box ke grading.
                $row['id_anak_sortir'] = 2;
                $row['id_kelas_sortir'] = 11;
            }
            unset($row);
        }

        return parent::validateRows($rows, $status);
    }

    protected function rules(): array
    {
        return array_replace(parent::rules(), [
            'id_anak_sortir' => 'required|integer|in:2|exists:tb_anak,id_anak',
            'id_kelas_sortir' => 'required|integer|in:11|exists:tb_kelas_sortir,id_kelas',
            'id_pengawas_grading' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('posisi_id', '!=', 1))],
        ]);
    }

    protected function finishRow(array $row, array &$invoices): void
    {
        parent::finishRow($row, $invoices);
        DB::table('sortir')->insert([
            'no_box' => $row['no_box'], 'id_kelas' => $row['id_kelas_sortir'],
            'tgl' => $row['tgl'], 'tgl_input' => $row['tgl'], 'bulan' => 0,
            'tahun_dibayar' => (int) substr($row['tgl'], 0, 4),
            'id_anak' => $row['id_anak_sortir'], 'id_pengawas' => $row['id_pengawas_sortir'],
            'pcs_awal' => $row['pcs_awal'], 'gr_awal' => $row['gr_awal'],
            'pcs_tdk_sortir' => 0, 'gr_tdk_sortir' => 0,
            'pcs_akhir' => $row['pcs_awal'], 'gr_akhir' => $row['gr_awal'],
            'rp_target' => 0, 'ttl_rp' => 0, 'denda_sp' => 0, 'pcuc' => 0, 'pcus' => 0,
            'selesai' => 'Y', 'penutup' => 'T',
        ]);
        $group = 'grade|'.$row['id_pengawas_sortir'].'|'.$row['id_pengawas_grading'].'|'.$row['tgl'];
        if (! isset($invoices[$group])) {
            $last = DB::table('formulir_sarang')->where('kategori', 'grade')->pluck('no_invoice')
                ->filter(fn ($value) => ctype_digit((string) $value))->map(fn ($value) => (int) $value)->max();
            $invoices[$group] = (string) (max(1000, (int) $last) + 1);
        }
        DB::table('formulir_sarang')->insert([
            'no_invoice' => $invoices[$group], 'no_box' => $row['no_box'],
            'id_pemberi' => $row['id_pengawas_sortir'], 'id_penerima' => $row['id_pengawas_grading'],
            'pcs_awal' => $row['pcs_awal'], 'gr_awal' => $row['gr_awal'],
            'tanggal' => $row['tgl'], 'kategori' => 'grade', 'selesai' => 'T', 'print' => 'T', 'sst_aktual' => 0,
        ]);
        $this->recordHancuran($row['no_box'], $invoices[$group], 'grade');
    }
}
