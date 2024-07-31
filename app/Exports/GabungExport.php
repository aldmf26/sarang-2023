<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GabungExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'user' => new LaporanDetailPartai(),
            'Posisi' => new LaporanDetailPartai2(),
        ];
    }
}
