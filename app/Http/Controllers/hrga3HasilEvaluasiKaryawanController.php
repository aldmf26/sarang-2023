<?php

namespace App\Http\Controllers;

use App\Models\hrga3HasilEvaluasiKaryawan as hrga3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class hrga3HasilEvaluasiKaryawanController extends Controller
{
    public function index(Request $r)
    {
        $keputusan = $r->keputusan ?? 'lulus';
        $model = hrga3::all();
        $karyawans = DB::table('hasil_wawancara')->where('keputusan_lulus', $keputusan)->get();
        $data = [
            'title' => 'Harga 3 Hasil Evaluasi Karyawan',
            'keputusan' => $keputusan,
            'karyawans' => $karyawans
        ];
        return view('hccp.hrga3.index', $data);
    }

    public function penilaianShow(Request $r)
    {
        $id = $r->id;
        $karyawan = DB::table('hasil_wawancara')->where('id', $id)->first();
        $penilaians = hrga3::where('karyawan_id', $id)->get();

        return view('hccp.hrga3.penilaian_show', compact('karyawan', 'penilaians'));
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Hasil Evaluasi Karyawan',
            'karyawans' => DB::table('hasil_wawancara')->where('keputusan', 'dilanjutkan')->get(),
        ];
        return view('hccp.hrga3.create', $data);
    }

    public function getKaryawan(Request $r)
    {
        $id = $r->id;
        $karyawan = DB::table('hasil_wawancara')->where('id', $id)->first();
        $data = [
            'usia' => Umur($karyawan->tgl_lahir, $karyawan->created_at),
            'j_kelamin' => $karyawan->jenis_kelamin,
            'posisi' => $karyawan->posisi
        ];
        return response()->json($data);
    }

    public function store(Request $r)
    {
        try {
            DB::beginTransaction();
            foreach ($r->penilaian as $kriteria => $nilai) {
                Hrga3::create([
                    'karyawan_id' => $r->id_karyawan,
                    'kriteria' => ucfirst($kriteria),
                    'standar' => $nilai['standar'],
                    'hasil' => $nilai['hasil'],
                    'admin' => auth()->user()->name
                ]);

                DB::table('hasil_wawancara')->where('id', $r->id_karyawan)->update([
                    'status' => $r->status_posisi,
                    'keputusan_lulus' => $r->keputusan,
                    'periode_masa_percobaan' => $r->periode,
                ]);
            }
            DB::commit();
            return redirect()->route('hrga3.index')->with('sukses', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
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
        $spreadsheet->getDefaultStyle()->getFont()->setSize('10');
        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="FRM.HRGA.01.03 - Hasil Evaluasi Karyawan Baru.xlsx"');
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

        $sheet->setCellValue('D4', 'Dok.No.: FRM.HRGA.01.03, Rev.00');
        $sheet->getStyle('D4')->applyFromArray([
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
        $get = DB::table('hasil_wawancara')->where('id', $id)->first();

        $sheet->setCellValue('B8', 'Nama Karyawan');
        $sheet->setCellValue('B9', 'Usia');
        $sheet->setCellValue('B10', 'Jenis Kelamin');
        $sheet->setCellValue('B11', 'Posisi');
        $sheet->setCellValue('B12', 'Periode Masa Percobaan');
        $sheet->setCellValue('B13', '* Coret yang tidak sesuai');
        



        $sheet->setCellValue('C8', ': ' . $get->nama);
        $sheet->setCellValue('C9', ': ' . Umur($get->tgl_lahir, $get->created_at));
        $sheet->setCellValue('C10', ': ' . $get->jenis_kelamin);
        $sheet->setCellValue('C11', ': ' . $get->posisi);
        $periode = $get->periode_masa_percobaan; // Periode yang dipilih
        $periods = [1 => '1 bulan', 3 => ' / 3 bulan', 6 => ' / 6 bulan*']; // Daftar periode
        $row = 12;

        // Buat objek Rich Text untuk menggabungkan teks dengan format berbeda
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

        foreach ($periods as $key => $text) {
            // Tambahkan teks ke Rich Text
            $run = $richText->createTextRun($text);

            // Jika periode tidak sesuai, tambahkan format coret
            if ($periode != $key) {
                $run->getFont()->setStrikethrough(true);
            }

           
        }

        // Masukkan Rich Text ke sel
        $sheet->setCellValue('C' . $row, $richText);

        $sheet->mergeCells('B16:D16');
        $sheet->getStyle('B16:D17')->getFont()->setBold(true);
        $sheet->getStyle('B16:D17')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('B16', 'PENILAIAN KARYAWAN');
        $sheet->setCellValue('B17', 'Kriteria Penilaian');
        $sheet->setCellValue('C17', 'Standar Penilaian');
        $sheet->setCellValue('D17', 'Hasil Penilaian');

        $penilaians = hrga3::where('karyawan_id', $id)->get();

        $sheet->getColumnDimension('B')->setWidth(28.7);
        $sheet->getColumnDimension('C')->setWidth(28.7);
        $sheet->getColumnDimension('D')->setWidth(28.7);
        $row = 18;
        foreach ($penilaians as $penilaian) {
            $sheet->getCell('B' . $row)->setValue($penilaian->kriteria == 'Kompetensi_inti' ? 'Kompetensi Inti' : $penilaian->kriteria);
            $sheet->getCell('C' . $row)->setValue($penilaian->standar);
            $sheet->getCell('D' . $row)->setValue($penilaian->hasil);
            $sheet->getStyle('B' . $row . ':D' . $row)->getAlignment()->setWrapText(true);
            $row++;
        }

        $rowKeputusan = $row + 2;
        $sheet->getStyle('B' . $rowKeputusan)->getFont()->setBold(true);
        $sheet->getStyle('B' . $rowKeputusan)->getFont()->setUnderline(true);
        $sheet->setCellValue('B' . $rowKeputusan, 'Keputusan');

        $status1 = $get->keputusan_lulus == 'lulus' ? '⬛' : '⬜';
        $status2 = $get->keputusan_lulus == 'tidak lulus' ? '⬛' : '⬜';

        $sheet->setCellValue('C' . $rowKeputusan, $status1.' Lulus Masa Percobaan');
        $sheet->setCellValue('C' . $rowKeputusan + 1, $status2.' Tidak Lulus Masa Percobaan');


        $sheet->getStyle('B1:E45')->getFont()->setName('Cambria');
        $sheet->getStyle('B1:E45')->getFont()->setSize('10');
        $sheet->getStyle('B13')->getFont()->setItalic(true);
        $sheet->getStyle('B13')->getFont()->setSize(8);

    }
    protected function autoSizeColumns(Worksheet $sheet)
    {
        // foreach (range('B', 'F') as $columnID) {
        //     $sheet->getColumnDimension($columnID)->setAutoSize(true);
        // }
    }
}
