<?php

namespace App\Http\Controllers;

use App\Exports\HasilWawancaraExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class hasilWawancaraController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Hasil Wawancara',
            'hasilWawancara' => DB::table('hasil_wawancara')->orderBy('id', 'DESC')->get(),
        ];
        return view('hccp.hasilwawancara.index', $data);
    }
    public function create()
    {
        $data = [
            'title' => 'Tambah Hasil Wawancara',
        ];
        return view('hccp.hasilwawancara.tambah', $data);
    }

    public function store(Request $r)
    {
        $data = [
            'nama' => $r->nama,
            'tgl_lahir' => $r->tgl_lahir,
            'jenis_kelamin' => $r->jenis_kelamin,
            'posisi' => $r->posisi,
            'kesimpulan' => $r->kesimpulan,
            'keputusan' => $r->keputusan,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        DB::table('hasil_wawancara')->insert($data);

        return redirect()->route('hasilwawancara.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Hasil Wawancara',
            'hasil' => DB::table('hasil_wawancara')->where('id', $id)->first()
        ];
        return view('hccp.hasilwawancara.edit', $data);
    }

    public function update(Request $r)
    {
        $data = [
            'nama' => $r->nama,
            'tgl_lahir' => $r->tgl_lahir,
            'jenis_kelamin' => $r->jenis_kelamin,
            'posisi' => $r->posisi,
            'kesimpulan' => $r->kesimpulan,
            'keputusan' => $r->keputusan,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        DB::table('hasil_wawancara')->where('id', $r->id)->update($data);
        return redirect()->route('hasilwawancara.index')->with('sukses', 'Data Berhasil diupdate');
    }

    public function delete($id)
    {
        DB::table('hasil_wawancara')->where('id', $id)->delete();
        return redirect()->route('hasilwawancara.index')->with('sukses', 'Data Berhasil dihapus');
    }

    public function export($id)
    {
        $style_atas = array(
            'font' => [
                'bold' => true,
                'name' => 'Cambria', // Font Cambria
                'size' => 10,  // Mengatur teks menjadi tebal
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
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,      // Menengahkan secara vertikal
            ],
        );

        $style_dok = [
            'font' => [
                'name' => 'Cambria',
                'size' => 8,
            ],
        ];

        $style_kop = [
            'font' => [
                'name' => 'Cambria', // Font Cambria
                'size' => 10,        // Ukuran font
            ],
        ];


        $style = [
            'font' => [
                'name' => 'Cambria', // Font Cambria
                'size' => 10,        // Ukuran font
            ]

        ];
        $style1 = [
            'font' => [
                'name' => 'Cambria', // Font Cambria
                'size' => 10,        // Ukuran font
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,      // Menengahkan secara vertikal
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Menengahkan secara horizontal
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
        $sheet1->setTitle('Hasil Wawancara');


        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo perusahaan');
        $drawing->setPath($_SERVER['DOCUMENT_ROOT'] . '/img/logo.jpeg'); // Path absolut
        $drawing->setHeight(70); // Tinggi dalam satuan piksel (2,1 cm = 210 piksel)
        $drawing->setWidth(120);
        // Lebar dalam satuan piksel (3,81 cm = 381 piksel)
        $drawing->setCoordinates('A2'); // Lokasi gambar
        $drawing->setWorksheet($sheet1);

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo perusahaan');
        $drawing->setPath($_SERVER['DOCUMENT_ROOT'] . '/img/hasil_wawancara.jpeg'); // Path absolut
        $drawing->setHeight(40); // Tinggi dalam satuan piksel (2,1 cm = 210 piksel)

        // Lebar dalam satuan piksel (3,81 cm = 381 piksel)
        $drawing->setCoordinates('C2'); // Lokasi gambar
        $drawing->setWorksheet($sheet1);

        $sheet1->setCellValue('H5', 'Dok.No.: FRM.HRGA.01.02, Rev.00');
        $sheet1->mergeCells('H5:J5');
        $sheet1->getStyle('H5:J5')->applyFromArray($style_dok);



        $hasil = DB::table('hasil_wawancara')->where('id', $id)->first();
        $sheet1->setCellValue('B8', 'Nama Calon  Karyawan');
        $sheet1->setCellValue('D8', ': ' . $hasil->nama);
        $sheet1->setCellValue('B9', 'Usia');
        $sheet1->setCellValue('D9', ': ' . Umur($hasil->tgl_lahir, $hasil->created_at));
        $sheet1->setCellValue('B10', 'Jenis Kelamin');
        $sheet1->setCellValue('D10', ': ' . $hasil->jenis_kelamin);
        $sheet1->setCellValue('B11', 'Posisi');
        $sheet1->setCellValue('D11', ': ' . $hasil->posisi);

        $sheet1->getStyle('B8:D11')->applyFromArray($style_kop);
        $sheet1->getColumnDimension('C')->setWidth(10.91);

        $sheet1->setCellValue('B13', 'Tanggal Wawancara Tahap I:');
        $sheet1->mergeCells('B13:I13');
        $sheet1->getStyle('B13:I13')->applyFromArray($style_atas);
        $sheet1->getRowDimension(13)->setRowHeight(18);

        $sheet1->setCellValue('B14', 'Kesimpulan:');
        $sheet1->mergeCells('B14:I14');
        $sheet1->setCellValue('B15',  $hasil->kesimpulan);
        $sheet1->mergeCells('B15:I21');
        $sheet1->getStyle('B14:I14')->applyFromArray($style_kiri_kanan);
        $sheet1->getStyle('B15:I21')->applyFromArray($style_kiri_kanan_bawah);


        $sheet1->setCellValue('B22', 'Keputusan:');

        $sheet1->setCellValue('D22', $hasil->keputusan == 'dilanjutkan' ? "⬛ Dilanjutkan" : "⬜ Dilanjutkan");
        $sheet1->mergeCells('D22:E22');
        $sheet1->setCellValue('H22', $hasil->keputusan == 'ditolak' ? "⬛ Ditolak" : "⬜ Ditolak");
        $sheet1->mergeCells('H22:I22');
        $sheet1->getStyle('B22:I22')->applyFromArray($style);

        $sheet1->setCellValue('B24', 'Pewawancara:');
        $sheet1->getStyle('B24')->applyFromArray($style);
        $sheet1->setCellValue('D24', 'Nama');
        $sheet1->mergeCells('D24:E24');
        $sheet1->setCellValue('H24', 'Paraf');
        $sheet1->mergeCells('H24:I24');
        $sheet1->getStyle('D24:I24')->applyFromArray($style1);

        $sheet1->setCellValue('D25', '');
        $sheet1->setCellValue('H25', '');

        $sheet1->mergeCells('D25:E25');
        $sheet1->mergeCells('H25:I25');

        $sheet1->getStyle('D25:E25')->applyFromArray($style2);
        $sheet1->getStyle('H25:I25')->applyFromArray($style2);

        $sheet1->setCellValue('D26', '');
        $sheet1->setCellValue('H26', '');

        $sheet1->mergeCells('D26:E26');
        $sheet1->mergeCells('H26:I26');

        $sheet1->getStyle('D26:E26')->applyFromArray($style2);
        $sheet1->getStyle('H26:I26')->applyFromArray($style2);

        $sheet1->setCellValue('D27', '');
        $sheet1->setCellValue('H27', '');

        $sheet1->mergeCells('D27:E27');
        $sheet1->mergeCells('H27:I27');

        $sheet1->getStyle('D27:E27')->applyFromArray($style2);
        $sheet1->getStyle('H27:I27')->applyFromArray($style2);







        $namafile = "FRM.HRGA.01.02 - Hasil Wawancara.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}