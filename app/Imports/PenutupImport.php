<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class PenutupImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            DB::table('tb_gaji_penutup')->insert([
                'pgws' => $row[0] ?? 0,
                'hari_masuk' => $row[1] ?? 0,
                'nama' => $row[2] ?? '',
                'kelas' => $row[3] ?? '',
                'cbt_pcs_awal' => $row[4] ?? 0,
                'cbt_gr_awal' => $row[5] ?? 0,
                'cbt_pcs_akhir' => $row[6] ?? 0,
                'cbt_gr_akhir' => $row[7] ?? 0,
                'cbt_eot' => $row[8] ?? 0,
                'cbt_flx' => $row[9] ?? 0,
                'cbt_sst' => $row[10] ?? 0,
                'cbt_ttlrp' => $row[11] ?? 0,
                'eo_gr_awal' => $row[12] ?? 0,
                'eo_gr_akhir' => $row[13] ?? 0,
                'eo_sst' => $row[14] ?? 0,
                'eo_ttlrp' => $row[15] ?? 0,
                'srt_pcs_awal' => $row[16] ?? 0,
                'srt_gr_awal' => $row[17] ?? 0,
                'srt_pcs_akhir' => $row[18] ?? 0,
                'srt_gr_akhir' => $row[19] ?? 0,
                'srt_sst' => $row[20] ?? 0,
                'srt_ttlrp' => $row[21] ?? 0,
                'dll' => $row[22] ?? 0,
                'denda' => $row[23] ?? 0,
                'ttl_gaji' => $row[24] ?? 0,
                'ratarata' => $row[25] ?? 0,
                'tgl_input' => now(),
                'paid' => 'T',
                'admin' => auth()->user()->name,
                'bulan_dibayar' => 6,
                'tahun_dibayar' => 2024,
            ]);
        }
    }
}
