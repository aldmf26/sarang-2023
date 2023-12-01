<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class HariandllExport  implements FromView, WithEvents
{
    protected $tbl;
    protected $view;
    protected $totalrow;
    protected $kategori;

    public function __construct($tbl, $view, $kategori)
    {
        $this->tbl = $tbl;
        $this->view = $view;
        $this->totalrow = count($tbl) + 1;
        $this->kategori = $kategori;
    }

    public function view(): View
    {
        return view($this->view, [
            'datas' => $this->tbl,
            'kategori' =>  $this->kategori
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $sheet = $event->sheet;
                if ($this->kategori == 'cetak') {
                    $cellRange = 'A1:I1';
                    $cellRangeLoop = 'A1:I' . $this->totalrow;
                } else {
                    $cellRange = 'A1:G1';
                    $cellRangeLoop = 'A1:G' . $this->totalrow;
                }

                // $sheet->setAutoFilter($cellRange);

                $sheet->getStyle($cellRangeLoop)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => false
                    ]
                ]);
                $sheet->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }
}
