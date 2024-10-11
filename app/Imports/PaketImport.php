<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets ;

class PaketImport implements WithMultipleSheets 
{
    public function sheets(): array
    {
        return [
            new PaketCabut(),
            new PaketEo(),
            new PaketCetak(),
            new PaketSortir()
        ];
    }
}
