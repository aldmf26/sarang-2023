<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class RekapExport  implements FromView, WithEvents
{
    protected $tbl;
    protected $view;
    protected $totalrow;

    public function __construct($tbl, $view)
    {
        $this->tbl = $tbl;
        $this->view = $view;
        $this->totalrow = count($tbl) + 2;
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
                $cellRange = 'A1:AC2';
                $cellRangeLoop = 'A1:AC' . $this->totalrow;
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
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $cellRange = 'A2:J2'; // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FCE4D6', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);
                $cellRange = 'L2:P2'; // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'DDEBF7', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);
                $cellRange = 'R2:S2'; // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'C6E0B4', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);
                $cellRange = 'U2:X2'; // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFE699', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);
                $cellRange = 'AA2:AC2'; // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'D6DCE4', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);

                $cellRange = 'K2'; // Tentukan kisaran sel dari A1 hingga K1
                // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'C00000', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);
                $cellRange = 'Q2'; // Tentukan kisaran sel dari A1 hingga K1
                // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'C00000', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);
                $cellRange = 'T2'; // Tentukan kisaran sel dari A1 hingga K1
                // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'C00000', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);
                $cellRange = 'Y2'; // Tentukan kisaran sel dari A1 hingga K1
                // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'C00000', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);
                $cellRange = 'Z2'; // Tentukan kisaran sel dari A1 hingga K1
                // Tentukan kisaran sel dari A1 hingga K1

                $sheet->getStyle($cellRange)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'C00000', // Ganti kode warna ini sesuai dengan yang Anda inginkan
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => true
                    ],
                ]);

                $sheet->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }
}
