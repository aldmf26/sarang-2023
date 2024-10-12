<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PaketEo implements ToModel, WithHeadingRow
{
    public function model(array  $row)
    {
        if (!empty($row['kelas']) || !empty($row['rp'])) {
            DB::table('tb_kelas')->insert([
                'id_paket' => 1,
                'kelas' => $row['kelas'],
                'id_tipe_brg' => 37,
                'rupiah' => $row['rp'],
                'jenis' => 2,
                'id_kategori' => 3,
                'tgl_input' => date('Y-m-d')
            ]);
        }
    }
}