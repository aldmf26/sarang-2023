<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class BkImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows[2] as $row) {
            dd($row);
            if (!empty($row)) {
                DB::table('bk')->insert([
                    'no_lot' => $row,
                    'no_box' => $row,
                    'tipe' => $row,
                    'ket' => $row,
                    'warna' => $row,
                    'pengawas' => auth()->user()->n,
                    'penerima' => $row,
                    'pcs_awal' => $row,
                    'gr_awal' => $row,
                    'kategori' => $row,
                ]);
            }
            
        }
    }
}
