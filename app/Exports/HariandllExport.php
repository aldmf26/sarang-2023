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

    public function __construct($tbl, $view)
    {
        $this->tbl = $tbl;
        $this->view = $view;
        $this->totalrow = count($tbl) + 1;
    }

    public function view(): View
    {
        return view($this->view, [
            'datas' => $this->tbl,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $cellRange = 'A1:F1';
                $cellRangeLoop = 'A1:F' . $this->totalrow;
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
