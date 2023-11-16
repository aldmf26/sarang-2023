<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BkImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            // Jika semua elemen kosong, lewati ke iterasi berikutnya
            return null;
        }
        DB::table('bk')->insert([
            'no_lot' => $row['nolot'],
            'no_box' => $row['nobox'],
            'tipe' => $row['tipe'],
            'ket' => $row['ket'],
            'warna' => $row['warna'],
            'tgl' => $row['tgl'],
            'pengawas' => auth()->user()->name,
            'penerima' => $row['id_penerima'],
            'pcs_awal' => $row['pcs_awal'],
            'gr_awal' => $row['gr_awal'],
            'kategori' => $row['kategori'],
        ]);
    }
}
