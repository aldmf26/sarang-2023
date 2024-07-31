<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanDetailPartai implements FromView
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }
    public function view(): View
    {
        return view('home.laporan.export_detail', [
            'partai' => $this->query
        ]);
    }
}
