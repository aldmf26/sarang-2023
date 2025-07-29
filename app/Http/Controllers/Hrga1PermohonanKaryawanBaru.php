<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Hrga1PermohonanKaryawanBaru extends Controller
{
    public function index(Request $r)
    {
        setSessionDivisi($r);

        $hrga1 = DB::table('hrga1_permohonan_karyawan_baru')
            ->where('id_divisi', session('id_divisi'))
            ->orderBy('id', 'desc')
            ->get();
        $data = [
            'title' => 'Hrga 1 Permohonan Karyawan Baru',
            'hrga1' => $hrga1
        ];
        return view('hccp.hrga1_penerimaan.hrga1.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Permohonan Karyawan Baru',
        ];
        return view('hccp.hrga1_penerimaan.hrga1.create', $data);
    }

    public function store(Request $r)
    {
        $data = $r->except('_token');
        $data['tgl_input'] = now();
        $data['admin'] = auth()->user()->name;
        $data['id_divisi'] = session('id_divisi');

        DB::table('hrga1_permohonan_karyawan_baru')->insert($data);
        return redirect()->route('hrga1.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function edit($id)
    {
        $hrga1 = DB::table('hrga1_permohonan_karyawan_baru')->where('id', $id)->first();
        $data = [
            'title' => 'Edit Permohonan Karyawan Baru',
            'get' => $hrga1
        ];
        return view('hccp.hrga1_penerimaan.hrga1.edit', $data);
    }

    public function update(Request $r, $id)
    {
        $data = $r->except('_token');
        $data['tgl_input'] = now();
        $data['admin'] = auth()->user()->name;

        DB::table('hrga1_permohonan_karyawan_baru')->where('id', $id)->update($data);
        return redirect()->route('hrga1.index')->with('sukses', 'Data Berhasil diubah');
    }

    public function delete($id)
    {
        DB::table('hrga1_permohonan_karyawan_baru')->where('id', $id)->delete();
        return redirect()->route('hrga1.index')->with('sukses', 'Data Berhasil dihapus');
    }

    public function export($id)
    {
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Tambahkan kop surat
        $this->addLogo($sheet);
        $this->addLogo2($sheet);
        // Tambahkan header tabel
        $this->addTableData($sheet, $id);

        // Auto width kolom
        $this->autoSizeColumns($sheet);

        // Simpan file
        $writer = new Xlsx($spreadsheet);


        // Set font Cambria untuk seluruh sheet
        $spreadsheet->getDefaultStyle()->getFont()->setName('Cambria');
        $spreadsheet->getDefaultStyle()->getFont()->setSize('12');
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="FRM.HRGA.01.01 - Permohonan Karyawan Baru.xlsx"');
        header('Cache-Control: max-age=0');

        // Langsung kirim ke output
        $writer->save('php://output');
        exit;
    }
    protected function addLogo(Worksheet $sheet)
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Perusahaan');
        $drawing->setPath(public_path('uploads/logo.jpeg'));
        $drawing->setHeight(90);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
    }
    protected function addLogo2(Worksheet $sheet)
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Perusahaan');
        $drawing->setPath(public_path('uploads/logo2.jpeg'));
        $drawing->setHeight(40);
        $drawing->setCoordinates('C2');
        $drawing->setWorksheet($sheet);

        $sheet->setCellValue('G4', 'Dok.No.: FRM.HRGA.01.01, Rev.00');
        $sheet->getStyle('G4')->applyFromArray([
            'font' => [
                'size' => 8,
                'name' => 'Cambria'
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
    }

    protected function addTableData(Worksheet $sheet, $id)
    {
        $getData = DB::table('hrga1_permohonan_karyawan_baru')->where('id', $id)->first();

        $sheet->mergeCells('B9:I10');
        $sheet->setCellValue('B9', 'Bersama ini kami mohon bantuannya untuk menyediakan tenaga kerja dengan kualifikasi sebagai berikut:');
        $sheet->getStyle('B9')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('B12', 'Status Posisi');
        $sheet->setCellValue('B13', 'Jabatan');
        $sheet->setCellValue('B14', 'Jumlah');
        $sheet->setCellValue('B15', 'Alasan Penambahan');

        $status1 = $getData->status_posisi == 'Kontrak' ? '⬛' : '⬜';
        $status2 = $getData->status_posisi == 'Tetap' ? '⬛' : '⬜';

        $sheet->setCellValue('C12', ': ' . $status2);
        $sheet->setCellValue('C13', ':');
        $sheet->setCellValue('C14', ':');
        $sheet->setCellValue('C15', ':');

        $sheet->setCellValue('D12', 'Karyawan Tetap');
        $sheet->setCellValue('D13', $getData->jabatan);
        $sheet->setCellValue('D14', $getData->jumlah . ' orang');
        $sheet->setCellValue('D15', $getData->alasan_penambahan);

        $sheet->setCellValue('E12', $status1);
        $sheet->setCellValue('F12', 'Karyawan Kontrak');

        // kualifikasi
        $sheet->getStyle('B18')->getFont()->setBold(true);
        $sheet->getStyle('B18')->getFont()->setUnderline(true);
        $sheet->setCellValue('B18', 'Kualifikasi');
        $sheet->setCellValue('B19', '1. Umur');
        $sheet->setCellValue('B20', '2. Jenis Kelamin');
        $sheet->setCellValue('B21', '3. Pendidikan');
        $sheet->setCellValue('B22', '4. Pengalaman');
        $sheet->setCellValue('B23', '5. Pelatihan');
        $sheet->setCellValue('B24', '6. Mental / Sikap');
        $sheet->setCellValue('B25', '7. Uraian Kerja');

        $sheet->setCellValue('C19', ':');
        $sheet->setCellValue('C20', ':');
        $sheet->setCellValue('C21', ':');
        $sheet->setCellValue('C22', ':');
        $sheet->setCellValue('C23', ':');
        $sheet->setCellValue('C24', ':');
        $sheet->setCellValue('C25', ':');


        $sheet->setCellValue('D19', $getData->umur);
        $sheet->setCellValue('D20', $getData->j_kelamin);
        $sheet->setCellValue('D21', $getData->pendidikan);
        $sheet->setCellValue('D22', $getData->pengalaman);
        $sheet->setCellValue('D23', $getData->pelatihan);
        $sheet->setCellValue('D24', $getData->mental);
        $sheet->setCellValue('D25', $getData->uraian_kerja);

        $sheet->setCellValue('C27', ':');
        $sheet->setCellValue('B27', 'Tanggal Dibutuhkan');
        $sheet->setCellValue('D27', \Carbon\Carbon::parse($getData->tgl_dibutuhkan)->format('d F Y'));

        $sheet->setCellValue('F27', 'Diajukan Oleh: ' . $getData->diajukan_oleh);

        $sheet->setCellValue('F30', 'Tanggal: ' . \Carbon\Carbon::parse($getData->tgl_input)->format('d F Y'));


        $sheet->getStyle('B32')->getFont()->setBold(true);
        $sheet->getStyle('B32')->getFont()->setItalic(true);
        $sheet->getStyle('B37')->getFont()->setItalic(true);
        $sheet->setCellValue('B32', 'Diisi oleh HRD');
        $sheet->getStyle('B32')->getFont()->setSize(9);

        $sheet->mergeCells('B33:D33');
        $sheet->setCellValue('B33', 'Disetujui / Ditangguhkan / Ditolak*');
        $sheet->setCellValue('B37', '* Coret salah satu');
        $sheet->getStyle('B37')->getFont()->setSize(9);
        $sheet->setCellValue('F33', 'Diterima Oleh:');
        $sheet->setCellValue('F37', 'Tanggal');

        $sheet->getStyle('B33:F33')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C0C0C0');
        $sheet->getStyle('B33:F33')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('B37:F37')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('B34:F37')->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('B34:F37')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('D33:D37')->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
    }
    protected function autoSizeColumns(Worksheet $sheet)
    {
        foreach (range('B', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
