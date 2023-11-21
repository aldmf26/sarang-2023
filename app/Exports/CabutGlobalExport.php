<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class CabutGlobalExport implements FromView, WithEvents
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

                $style = [
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
                ];
                
                $cellRange = 'B1:M1';
                $cellRangeLoop = 'B1:M' . $this->totalrow;
                $sheet->getStyle($cellRangeLoop)->applyFromArray($style);
                $sheet->getStyle($cellRange)->getFont()->setBold(true);

                $cellRange = 'O1:R1';
                $cellRangeLoop = 'O1:R' . $this->totalrow;
                $sheet->getStyle($cellRangeLoop)->applyFromArray($style);
                $sheet->getStyle($cellRange)->getFont()->setBold(true);

                $cellRange = 'T1:Z1';
                $cellRangeLoop = 'T1:Z' . $this->totalrow;
                $sheet->getStyle($cellRangeLoop)->applyFromArray($style);
                $sheet->getStyle($cellRange)->getFont()->setBold(true);

                $cellRange = 'AB1:AD1';
                $cellRangeLoop = 'AB1:AD' . $this->totalrow;
                $sheet->getStyle($cellRangeLoop)->applyFromArray($style);
                $sheet->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }
}
