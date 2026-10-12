<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OpnameCetakImport extends OpnameCabutImport
{
    public const STATUSES = ['cetak' => 'Cetak sisa pengawas'];

    public const HEADERS = [
        'no_box', 'nm_partai', 'tipe', 'ket', 'warna', 'tgl',
        'pcs_awal', 'gr_awal', 'hrga_satuan',
        'id_pengawas_cetak',
    ];

    public function validateRows(array $rows, string $status): array
    {
        if (! isset(static::STATUSES[$status]) || ! $rows || count($rows) > 500) {
            return parent::validateRows($rows, $status);
        }
        $children = DB::table('tb_anak as a')
            ->where('a.id_pengawas', 285)->where('a.berhenti', 'T')
            ->get(['a.id_anak']);
        if ($children->isEmpty()) {
            throw ValidationException::withMessages(['file' => 'Arbayah (ID 285) belum memiliki anak aktif.']);
        }
        foreach ($rows as &$row) {
            $row['id_pengawas'] = 285;
            $row['id_kelas_cabut'] = 126;
            // Pilih saat pratinjau; simpan pilihan yang sama saat validasi ulang.
            if (! isset($row['id_anak_cabut'])) {
                $child = $children[random_int(0, $children->count() - 1)];
                $row['id_anak_cabut'] = $child->id_anak;
            }
        }
        unset($row);

        return parent::validateRows($rows, $status);
    }

    protected function rules(): array
    {
        return array_replace(parent::rules(), [
            'id_pengawas' => 'required|integer|in:285|exists:users,id,posisi_id,13',
            'id_anak_cabut' => 'required|integer|exists:tb_anak,id_anak',
            'id_kelas_cabut' => 'required|integer|in:126|exists:tb_kelas,id_kelas',
            'tgl_selesai_cabut' => 'prohibited',
            'gr_akhir_cabut' => 'prohibited',
            'biaya_cabut' => 'prohibited',
            'cost_op_cabut' => 'prohibited',
            'id_pengawas_cetak' => 'required|integer|exists:users,id,posisi_id,14',
            'tgl_terima_cetak' => 'prohibited',
        ]);
    }

    protected function rowErrors(array $row, int $line): array
    {
        $child = DB::table('tb_anak')->where('id_anak', $row['id_anak_cabut'])->first();

        return (int) $child->id_pengawas === 285 && $child->berhenti === 'T' ? [] : [
            "Baris {$line}: anak Cabut tidak lagi sesuai dengan anak aktif Arbayah. Unggah ulang file.",
        ];
    }

    protected function finishRow(array $row, array &$invoices): void
    {
        DB::table('cabut')->insert([
            'no_box' => $row['no_box'], 'id_pengawas' => $row['id_pengawas'],
            'id_anak' => $row['id_anak_cabut'], 'id_kelas' => $row['id_kelas_cabut'],
            'tgl_terima' => $row['tgl'], 'tgl_serah' => $row['tgl'],
            'pcs_awal' => $row['pcs_awal'], 'pcs_akhir' => $row['pcs_awal'],
            'gr_awal' => $row['gr_awal'], 'gr_akhir' => $row['gr_awal'],
            'ttl_rp' => 0, 'cost_op' => 0,
            'rupiah' => 0,
            'selesai' => 'Y', 'formulir' => 'Y', 'penutup' => 'T',
            'bulan_dibayar' => 0, 'tahun_dibayar' => (int) substr($row['tgl'], 0, 4),
            'invoice' => null, 'pcs_hcr' => 0, 'gr_hcr' => 0, 'pcs_flx' => 0, 'gr_flx' => 0, 'eot' => 0,
        ]);
        $group = 'cetak|'.$row['id_pengawas'].'|'.$row['id_pengawas_cetak'].'|'.$row['tgl'];
        if (! isset($invoices[$group])) {
            $last = DB::table('formulir_sarang')->where('kategori', 'cetak')->distinct()->pluck('no_invoice')
                ->filter(fn ($value) => ctype_digit((string) $value))->map(fn ($value) => (int) $value)->max();
            $invoices[$group] = (string) (max(1000, (int) $last) + 1);
        }
        DB::table('formulir_sarang')->insert([
            'no_invoice' => $invoices[$group], 'no_box' => $row['no_box'],
            'id_pemberi' => $row['id_pengawas'], 'id_penerima' => $row['id_pengawas_cetak'],
            'pcs_awal' => $row['pcs_awal'], 'gr_awal' => $row['gr_awal'],
            'tanggal' => $row['tgl'], 'kategori' => 'cetak',
            'selesai' => 'T', 'print' => 'T', 'sst_aktual' => 0,
        ]);
        $this->recordHancuran($row['no_box'], $invoices[$group], 'cetak');
    }
}
