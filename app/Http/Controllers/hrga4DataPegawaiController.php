<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga4DataPegawaiController extends Controller
{
    public function index()
    {
        $karyawans = DB::table('hasil_wawancara')->where('keputusan_lulus', 'lulus')->get();
        $data = [
            'title' => 'Data Pegawai',
            'karyawans' => $karyawans

        ];
        return view('hccp.hrga4.index', $data);
    }

    public function export()
    {
        $style_atas = array(
            'font' => [
                'bold' => true,
                'name' => 'Arial', // Font Cambria
                'size' => 12,  // Mengatur teks menjadi tebal
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'C0C0C0', // Contoh warna kuning
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'wrapText' => true,
            ],
        );

        $style_dok = [
            'font' => [
                'name' => 'Cambria',
                'size' => 12,
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,      // Menengahkan secara vertikal
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, // Menengahkan secara horizontal
            ],
        ];

        $style_kop = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,      // Menengahkan secara vertikal
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Menengahkan secara horizontal
            ],
        ];


        $style = [
            'font' => [
                'name' => 'Arial', // Font Cambria
                'size' => 12,        // Ukuran font
            ]

        ];
        $style1 = [
            'font' => [
                'name' => 'Arial', // Font Cambria
                'size' => 12,        // Ukuran font
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,      // Menengahkan secara vertikal
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Menengahkan secara horizontal
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],

        ];
        $style2 = [
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DASHED, // Gaya titik-titik
                    'color' => ['argb' => 'FF000000'], // Warna hitam
                ],
            ],

        ];
        $style_kiri_kanan = [
            'font' => [
                'name' => 'Cambria', // Font Cambria
                'size' => 10,        // Ukuran font
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,  // Border tipis di kiri
                    'color' => ['argb' => 'FF000000'], // Warna hitam (opsional)
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,  // Border tipis di kanan
                    'color' => ['argb' => 'FF000000'], // Warna hitam (opsional)
                ],
            ],
        ];
        $style_kiri_kanan_bawah = [
            'font' => [
                'name' => 'Cambria', // Font Cambria
                'size' => 10,        // Ukuran font
            ],
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,  // Border tipis di kiri
                    'color' => ['argb' => 'FF000000'], // Warna hitam (opsional)
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,  // Border tipis di kanan
                    'color' => ['argb' => 'FF000000'], // Warna hitam (opsional)
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,  // Border tipis di bawah
                    'color' => ['argb' => 'FF000000'], // Warna hitam (opsional)
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                'wrapText' => true,   // Menempatkan teks di atas secara vertikal
            ],

        ];
        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Data Pegawai');


        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo perusahaan');
        $drawing->setPath($_SERVER['DOCUMENT_ROOT'] . '/img/logo.jpeg'); // Path absolut
        $drawing->setHeight(80); // Tinggi dalam satuan piksel (2,1 cm = 210 piksel)
        $drawing->setWidth(140);
        $drawing->setOffsetX(20);
        // Lebar dalam satuan piksel (3,81 cm = 381 piksel)
        $drawing->setCoordinates('A2'); // Lokasi gambar
        $drawing->setWorksheet($sheet1);

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo perusahaan');
        $drawing->setPath($_SERVER['DOCUMENT_ROOT'] . '/img/data_pegawai.jpeg'); // Path absolut
        $drawing->setHeight(50); // Tinggi gambar dalam piksel
        $drawing->setCoordinates('C2'); // Koordinat awal gambar

        // Atur manual offset agar gambar berada di tengah
        $drawing->setOffsetX(170); // Offset horizontal (coba 30 sebagai nilai awal)
        // Offset vertikal (coba 10 sebagai nilai awal)

        $drawing->setWorksheet($sheet1);
        $sheet1->mergeCells('C2:E3'); // Merge cell untuk area target


        $sheet1->setCellValue('F5', 'FRM.HRGA.01.04');
        $sheet1->mergeCells('F5:G5');

        $sheet1->getStyle('F5:G5')->applyFromArray($style_dok);

        $sheet1->setCellValue('A8', "Update :" . date('d M Y'));
        $sheet1->getStyle('A8')->applyFromArray($style);

        $sheet1->setCellValue('A9', 'NO');
        $sheet1->setCellValue('B9', 'DIVISI / DEPT');
        $sheet1->setCellValue('C9', 'NAMA');
        $sheet1->setCellValue('D9', 'JENIS KELAMIN/ TANGGGAL LAHIR');
        $sheet1->setCellValue('E9', 'STATUS');
        $sheet1->setCellValue('F9', 'TANGGAL MASUK');
        $sheet1->setCellValue('G9', 'POSISI');

        $sheet1->getStyle('A9:G9')->applyFromArray($style_atas);

        $sheet1->getColumnDimension('A')->setWidth(8.55);
        $sheet1->getColumnDimension('B')->setWidth(27.09);
        $sheet1->getColumnDimension('C')->setWidth(27.55);
        $sheet1->getColumnDimension('D')->setWidth(29.36);
        $sheet1->getColumnDimension('E')->setWidth(28.55);
        $sheet1->getColumnDimension('F')->setWidth(27.36);
        $sheet1->getColumnDimension('G')->setWidth(17.64);

        $karyawans = DB::table('hasil_wawancara')->where('keputusan_lulus', 'lulus')->get();

        $kolom = 10;
        $no = 1;
        foreach ($karyawans as $d) {
            $sheet1->setCellValue('A' . $kolom, $no++);
            $sheet1->setCellValue('B' . $kolom, $d->posisi);
            $sheet1->setCellValue('C' . $kolom, $d->nama);
            $sheet1->setCellValue('D' . $kolom, $d->jenis_kelamin . "/" . tanggal($d->tgl_lahir));
            $sheet1->setCellValue('E' . $kolom, $d->status);
            $sheet1->setCellValue('F' . $kolom, '01 Februari 2023');
            $sheet1->setCellValue('G' . $kolom, 'Pengawas');
            $kolom++;
        }

        $sheet1->getStyle('A10:G' . ($kolom - 1))->applyFromArray($style1);













        $namafile = "FRM.HRGA.01.04 - Data Pegawai.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
