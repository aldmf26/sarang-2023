<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PaketCabut implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array  $row)
    {
        if (!empty($row['paket']) || !empty($row['lokasi'])) {
            DB::table('tb_kelas')->insert([
                'tipe' => $row['paket'],
                'lokasi' => $row['lokasi'],
                'id_tipe_brg' => 33,
                'id_paket' => 2,
                'id_kategori' => 2,
                'jenis' => 2,
                'pcs' => 0,
                'gr' => $row['gr'],
                'rupiah' => $row['rp'],
                'rp_bonus' => $row['rp_bonus'],
                'batas_susut' => $row['batas_susut'],
                'denda_susut_persen' => $row['denda_susut'],
                'bonus_susut' => $row['bonus_susut'],
                'batas_eot' => $row['batas_eot'],
                'eot' => $row['eot'],
                'denda_hcr' => $row['denda_hcr'],
                'tgl_input' => date('Y-m-d')
            ]);
        }

    }
}
