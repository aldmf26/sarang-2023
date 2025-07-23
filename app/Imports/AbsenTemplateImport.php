<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AbsenTemplateImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        DB::beginTransaction();
        try {
            if (empty($row['id']) && empty($row['tgl']) && empty($row['keterangan'])) {
                return null; // Lewati baris kosong
            }

            $tanggalFormatted = is_numeric($row['tgl'])
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tgl'])->format('Y-m-d')
                : (strtotime($row['tgl']) ? date('Y-m-d', strtotime($row['tgl'])) : $row['tgl']);

            DB::table('absen')->insert([
                'id_anak' => $row['id'],
                'tgl' => $tanggalFormatted,
                'ket' => $row['keterangan'],
                'bulan_dibayar' => 7,
                'tahun_dibayar' => date('Y'),
                // Hapus id_anak dan id_kerja jika tidak ada di tabel
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal menyimpan data: " . $e->getMessage());
            return null; // Abaikan baris ini
        }
    }
}
