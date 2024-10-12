<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PaketSortir implements ToModel, WithHeadingRow
{
    public function model(array  $row)
    {
        if (!empty($row['kelas'])) {
            DB::table('tb_kelas_sortir')->insert([
                'kelas' => $row['kelas'],
                'gr' => $row['gr'],
                'rupiah' => $row['rupiah'],
                'denda_susut' => $row['denda_sst'],
                'denda' => $row['denda_rp'],
                'tgl_input' => date('Y-m-d')
            ]);
        }
    }
}
