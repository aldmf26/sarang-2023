<?php

namespace App\Http\Controllers;

use App\Exports\GradingbjTemplateExport;
use App\Models\ApiGudangGradingModel;
use App\Models\Grading;
use App\Models\PengirimanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GradingBjController extends Controller
{
    protected $nmTbl = 'pengiriman_gradingbj';
    public function getDataMaster($jenis, $noBox = null)
    {
        $whereBox = $noBox ? "AND b.no_box in ($noBox) " : '';
        $formulir = DB::select("SELECT 
        b.no_box, b.tanggal, e.tipe, c.name as pemberi, b.no_invoice, sum(b.pcs_awal - d.pcs) as pcs_awal, sum(b.gr_awal - d.gr) as gr_awal
        FROM grading as a 
        JOIN formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade'
        JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'sortir'
        $whereBox
        LEFT JOIN(
            select no_box_sortir as no_box,sum(pcs) as pcs,sum(gr) as gr
            from grading 
            group by no_box_sortir
        ) as d on d.no_box = b.no_box
        JOIN users as c on c.id = b.id_pemberi
        GROUP BY b.no_box
        HAVING sum(b.pcs_awal - d.pcs) > 0 OR sum(b.gr_awal - d.gr) > 0
        ORDER BY b.tanggal DESC");

        $susut = DB::select("SELECT a.no_box,a.gr_awal as gr_awal,b.gr FROM `formulir_sarang` as a
        join (
        SELECT a.no_box_sortir as no_box,sum(a.pcs) as pcs,sum(a.gr) as gr FROM `grading` as a
        GROUP BY a.no_box_sortir
        HAVING sum(a.gr) > 0
        ) as b on a.no_box = b.no_box
        WHERE a.kategori = 'grade'
        GROUP by a.no_box;");
        $arr = [
            'formulir' => $formulir,
            'pengawas' => DB::table('users')->where('posisi_id', 13)->get(),
            'selisih' => DB::table('grading_selisih')->get(),
            'susut' => $susut
        ];
        return $arr[$jenis];
    }
    public function index(Request $r)
    {
        $data = [
            'title' => 'Grading',
            'formulir' => Grading::dapatkanStokBoxGradingbj('formulir')
        ];
        return view('home.gradingbj.index', $data);
    }

    public function po(Request $r) {}

    public function load_selisih()
    {
        $selisih = $this->getDataMaster('selisih');
        $susut = $this->getDataMaster('susut');

        $data = [
            'title' => 'Load Selisih',
            'selisih' => $selisih,
            'susut' => $susut
        ];
        return view('home.gradingbj.selisih', $data);
    }

    public function export_selisih()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('grading selisih');
        $koloms = [
            'A' => 'no box',
            'B' => 'tgl',
            'C' => 'pcs',
            'D' => 'gr',
            'E' => 'admin',

        ];

        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }
        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('F1:J1')->applyFromArray($styleBold);
        $sheet->getStyle('A1:C1')->applyFromArray($styleBold);

        $selisih = $this->getDataMaster('selisih');

        $no = 2;
        foreach ($selisih as $item) {
            $sheet->setCellValue('A' . $no, $item->no_box);
            $sheet->setCellValue('B' . $no, $item->tgl);
            $sheet->setCellValue('C' . $no, $item->pcs);
            $sheet->setCellValue('D' . $no, $item->gr);
            $sheet->setCellValue('E' . $no, $item->admin);

            $no++;
        }

        $sheet->getStyle('A1:E' . $no - 1)->applyFromArray($styleBaris);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Grading Selisih";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );
    }

    public function export_susut()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('grading susut');
        $koloms = [
            'A' => 'no box',
            'B' => 'susut',

        ];

        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }
        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:B1')->applyFromArray($styleBold);

        $selisih = $this->getDataMaster('susut');

        $no = 2;
        foreach ($selisih as $item) {
            $sheet->setCellValue('A' . $no, $item->no_box);
            $sheet->setCellValue('B' . $no, number_format((1 - ($item->gr / $item->gr_awal)) * 100, 0));

            $no++;
        }

        $sheet->getStyle('A1:B' . $no - 1)->applyFromArray($styleBaris);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Grading Susut";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );
    }

    public function grading(Request $r)
    {
        if ($r->submit == 'export') {
            return $this->exportGrading($r->no_box);
        }
        if ($r->submit == 'selisih') {
            return $this->selisih($r->no_box);
        }
        $getFormulir = Grading::dapatkanStokBox('formulir', $r->no_box);
        $no_invoice = 1001;
        $gradeStatuses = ['bentuk', 'turun'];
        $tb_grade = DB::table('tb_grade')->whereIn('status', $gradeStatuses)->orderBy('status', 'ASC')->get();
        $gradeBentuk = $tb_grade->where('status', 'bentuk');
        $gradeTurun = $tb_grade->where('status', 'turun');
        $data = [
            'title' => 'Grading Proses',
            'no_invoice' => $no_invoice,
            'user' => auth()->user()->name,
            'getFormulir' => $getFormulir,
            'gradeBentuk' => $tb_grade,
            'gradeTurun' => $gradeTurun,
        ];

        return view('home.gradingbj.grading', $data);
    }

    public function grading_partai(Request $r)
    {
        $no_box = $r->no_box;
        //split string to array
        $no_boxPecah = explode(',', $no_box); //split string to array
        $partaiData = DB::table('bk')
            ->whereIn('no_box', $no_boxPecah)
            ->where('kategori', 'cabut')
            ->select('nm_partai', 'tipe', 'ket') // Pastikan 'tipe' adalah kolom yang valid di tabel 'bk'
            ->get();

        // Dapatkan jumlah unik untuk partai, tipe, dan ket
        $uniqueCounts = [
            'partai' => $partaiData->pluck('nm_partai')->map(function ($item) {
                return strtolower($item);
            })->unique()->count(),
            'tipe' => $partaiData->pluck('tipe')->map(function ($item) {
                return strtolower($item);
            })->unique()->count(),
            'ket' => $partaiData->pluck('ket')->map(function ($item) {
                return strtolower($item);
            })->unique()->count(),
        ];


        // Validasi setiap kriteria
        foreach ($uniqueCounts as $key => $count) {
            if ($count > 1) {
                return redirect()->back()->with('error', ucfirst($key) . ' harus sama.');
            }
        }

        if ($r->submit == 'serah') {
            $getFormulir = DB::table('formulir_sarang')->where('kategori', 'grade')->whereIn('no_box', $no_boxPecah)->get();
            $urutanInvoice = DB::table('formulir_sarang')
                ->where('kategori', 'grading')
                ->max('no_invoice');

            $no_invoice = $urutanInvoice ? $urutanInvoice + 1 : 11055;
            foreach ($getFormulir as $d) {
                $data[] = [
                    'no_box' => $d->no_box,
                    'pcs_awal' => $d->pcs_awal,
                    'gr_awal' => $d->gr_awal,
                    'tanggal' => date('Y-m-d'),
                    'kategori' => 'grading',
                    'id_pemberi' => auth()->user()->id,
                    'id_penerima' => auth()->user()->id,
                    'no_invoice' => $no_invoice
                ];
            }
            DB::table('formulir_sarang')->insert($data);
            return redirect()->back()->with('sukses', 'berhasil di po');
        }

        // Redirect ke rute yang akan ditampilkan sebagai GET
        return redirect()->route('gradingbj.grading_partai_result', [
            'no_box' => $no_box, // Kirim data yang diperlukan ke halaman GET,
        ]);
    }

    public function gradingPartaiResult(Request $r)
    {
        $no_box = $r->no_box;
        // Ambil data yang sama seperti pada POST
        $partaiData = DB::table('bk')
            ->whereIn('no_box', explode(',', $no_box))
            ->where('kategori', 'cabut')
            ->select('nm_partai', 'tipe', 'ket')
            ->get();
        $getFormulir = Grading::dapatkanStokBoxtesting('formulir', $r->no_box);
        $tb_grade = DB::table('tb_grade')->whereIn('status', ['bentuk', 'turun'])->orderBy('status', 'ASC')->get();

        $data = [
            'title' => 'Grading Partai',
            'user' => auth()->user()->name,
            'nm_partai' => $partaiData->first()->nm_partai,
            'getFormulir' => $getFormulir,
            'gradeBentuk' => $tb_grade,
            'no_box' => $no_box,
            'no_invoice' => $r->no_invoice
        ];

        return view('home.gradingbj.grading_partai', $data);
    }

    public function cek_box_kirim(Request $r)
    {
        $boxkirim = $r->boxkirim;
        $grade = $r->grade;
        if (!empty($boxkirim)) {
            $databox =  DB::selectOne("SELECT a.grade, a.box_pengiriman, sum(a.pcs) as pcs, sum(a.gr) as gr
                    FROM grading_partai as a 
                    where a.box_pengiriman = '$boxkirim'
                    group by a.box_pengiriman;");
        }
        if (!empty($grade)) {
            $cek250 =  DB::selectOne("SELECT 
                a.box_pengiriman,
                sum(a.pcs) as pcs,
                sum(a.gr) as gr,
                a.grade, 
                a.sudah_print, 
                a.urutan
                FROM grading_partai as a
                WHERE a.formulir = 'T' and a.grade = '$grade'
                GROUP BY a.box_pengiriman 
                having sum(a.gr) < 250
                ORDER BY a.grade ASC;");
        }

        if (!empty($cek250)) {
            return json_encode([
                'box_pengiriman' => $cek250->box_pengiriman,
                'html' => '<span style="color: blue;">Pcs : ' . round($cek250->pcs, 0) . ' <br/> ' . 'Gr : ' . round($cek250->gr, 0) . '</span>'
            ]);
        }

        $cekSudahKirim = DB::table('pengiriman')->where('no_box', $boxkirim)->exists();

        if ($cekSudahKirim) {
            return json_encode([
                'box_pengiriman' => '',
                'html' => "<span class='text-danger fw-bold'> BOX SUDAH DIKIRIM </span>"
            ]);
        }
        if (empty($databox)) {
            return json_encode([
                'box_pengiriman' => '',
                'html' => "<span class='fw-bold'> data tidak ditemukan </span>"
            ]);
        } else {
            if ($databox->grade == $grade) {
                return json_encode([
                    'box_pengiriman' => '',
                    'html' => '<span style="color: blue;">Pcs : ' . round($databox->pcs, 0) . ' <br/> ' . 'Gr : ' . round($databox->gr, 0) . '</span>'
                ]);
            } else {
                return json_encode([
                    'box_pengiriman' => '',
                    'html' => "<span class='text-danger fw-bold'> grade: $databox->grade </span>"
                ]);
            }
        }
    }

    public function create_partai(Request $r)
    {
        try {
            DB::beginTransaction();
            $nm_partai = $r->nm_partai;
            $bulan = $r->bulan;
            $tahun = 2025;
            $tgl = date('Y-m-d');
            $lastItem = DB::table('grading_partai')->where('nm_partai', $nm_partai)->orderBy('urutan', 'desc')->first();
            $urutan = !$lastItem ? 1 : $lastItem->urutan + 1;
            $no_invoice = $r->no_nota;

            for ($i = 0; $i < count($r->no_box); $i++) {
                $getFormulir = DB::table('formulir_sarang')->where([['kategori', 'grade'], ['no_box', $r->no_box[$i]]])->first();
                $dataGrading[] = [
                    'no_box_sortir' => $r->no_box[$i],
                    'pcs' => $getFormulir->pcs_awal,
                    'gr' => $getFormulir->gr_awal,
                    'no_invoice' => $no_invoice,
                    'admin' => auth()->user()->name,
                    'tgl' => $tgl,
                ];
            }

            for ($i = 0; $i < count($r->grade); $i++) {
                if (!$r->box_sp[$i]) {
                    continue;
                }
                $data = [
                    'bulan' => '',
                    'tahun' => '',
                    'no_invoice' => $no_invoice,
                    'nm_partai' => $nm_partai,
                    'urutan' => $no_invoice,
                    'grade' => $r->grade[$i],
                    'tipe' => $r->tipe,
                    'pcs' => $r->pcs[$i],
                    'gr' => $r->gr[$i],
                    'tgl' => $tgl,
                    'admin' => auth()->user()->name,
                    'box_pengiriman' => $r->box_sp[$i],
                    'ttl_rp' => $r->rpGr * $r->gr[$i],
                    'cost_bk' => $r->rpGrBk * $r->gr[$i],
                    'cost_kerja' => $r->rpGrKerja * $r->gr[$i],
                    'cost_kerja' => $r->rpGrKerja * $r->gr[$i],
                    'cost_cu' => $r->rpGrCu * $r->gr[$i],
                ];

                $data2[] = $data;

                $boxsp = $r->box_sp[$i];
                $getBoxkirim = DB::selectOne("SELECT a.box_pengiriman, a.grade
                FROM grading_partai as a 
                where `box_pengiriman` = '$boxsp';");

                $cekBoxSudahKirim = DB::table('pengiriman')->where('no_box', $boxsp)->exists();
                if ($cekBoxSudahKirim) {
                    return redirect()->back()->withInput()->with('error', 'BOX SUDAH DIKIRIM : ' . $boxsp);
                }

                if (!empty($getBoxkirim)) {
                    if ($getBoxkirim->grade != $r->grade[$i]) {
                        return redirect()->back()->withInput()->with('error', 'Box grading tidak boleh lebih dari satu grade ' . $getBoxkirim->box_pengiriman);
                    }
                }

                DB::table('grading_partai')->insert($data);
            }

            $ttlPcsSortir = $r->ttlPcs;
            $ttlGrSortir = $r->ttlGr;

            $ttlPcsGrading = array_sum(array_column($data2, 'pcs'));
            $ttlGrGrading = array_sum(array_column($data2, 'gr'));

            $selisihPcs =  $ttlPcsSortir - $ttlPcsGrading;
            $selisihGr =  $ttlGrSortir - $ttlGrGrading;

            if ($ttlPcsGrading != $ttlPcsSortir || $ttlGrGrading != $ttlGrSortir) {
                return redirect()->back()->withInput()->with('error', 'Total pcs dan gr grading tidak boleh lebih dari ttl pcs atau gr sortir');
            }

            // if ($selisihGr > 0) {
            //     DB::table('grading_selisih')->insert([
            //         'no_box' => $no_invoice,
            //         'pcs' => $selisihPcs,
            //         'gr' => $selisihGr,
            //         'admin' => auth()->user()->name,
            //         'tgl' => $tgl,
            //     ]);
            // }

            DB::table('grading')->insert($dataGrading);

            DB::commit();
            return redirect()->route('gradingbj.index')->with('sukses', 'Berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function exportGrading($no_box)
    {
        $getFormulir = Grading::dapatkanStokBox('formulir', $no_box);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Grading');
        $koloms = [
            'A' => 'No Box Dipilih',
            'B' => 'pcs awal',
            'C' => 'gr awal',
            'D' => 'pcs akhir',
            'E' => 'gr akhir',
            'F' => 'susut',

            'H' => 'No Box',
            'I' => 'grade',
            'J' => 'pcs',
            'K' => 'gr',
            'L' => 'box pengiriman',

            'N' => 'grade',

        ];
        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }
        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:F1')->applyFromArray($styleBold);
        $styleBackground = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF00',
                ],
            ],
        ];
        $sheet->getStyle('H1:L1')->applyFromArray($styleBackground);
        $sheet->getStyle('H1:L1')->applyFromArray($styleBold);
        $sheet->getStyle('N1')->applyFromArray($styleBold);

        $sheet->setCellValue('D2', "SUMIFS(J:J,H:H,A2)");
        $sheet->setCellValue('E2', "SUMIFS(K:K,H:H,A2)");
        $sheet->setCellValue('F2', "1-(E2/C2)");
        $no = 2;
        foreach ($getFormulir as $item) {
            $sheet->setCellValue('A' . $no, $item->no_box);
            $sheet->setCellValue('B' . $no, $item->pcs_awal);
            $sheet->setCellValue('C' . $no, $item->gr_awal);
            $h_no_box = $item->no_box;
            // Mengisi kolom H dengan nilai no_box selama 15 baris
            for ($i = 0; $i < 15; $i++) {
                // $sheet->setCellValue('H' . ($no + $i), $h_no_box);
            }
            $no += 15;
        }

        $grade = DB::table('tb_grade')->get();
        $no = 2;
        foreach ($grade as $item) {
            $sheet->setCellValue('N' . $no, $item->nm_grade);

            $no++;
        }
        $sheet->getStyle('N1:N' . $no - 1)->applyFromArray($styleBaris);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Template Grading";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );
    }

    public function getNoInvoiceTambah()
    {
        $cekInvoice = DB::selectOne("SELECT no_invoice FROM `grading` ORDER by no_invoice DESC limit 1;");
        $noinvoice = isset($cekInvoice->no_invoice) ? $cekInvoice->no_invoice + 1 : 1001;
        return $noinvoice;
    }


    public function template_import()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Grading');
        $koloms = [
            'A' => 'tgl',
            'B' => 'partai',
            'C' => 'urutan',
            'D' => 'no box',
            'E' => 'pcs',
            'F' => 'gr',
            'G' => 'grade',
            'H' => 'pcs',
            'I' => 'gr',
            'J' => 'no pengiriman',
            'K' => 'cost bk',
            'L' => 'cost kerja',
            'M' => 'cost cu',

            'O' => 'grade',
            'P' => 'tipe',

        ];

        $tbGrade = DB::table('tb_grade')->get();
        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }

        foreach ($tbGrade as $i => $item) {
            $sheet->setCellValue('O' . ($i + 2), $item->nm_grade);
            $sheet->setCellValue('P' . ($i + 2), $item->tipe);
        }
        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($styleBold);
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($styleBaris);
        $sheet->getStyle('O1:P1')->applyFromArray($styleBaris);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Template Grading";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );
    }

    public function import(Request $r)
    {
        $file = $r->file('file');
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $admin = auth()->user()->name . "aa";
        $tglD = date('Y-m-d');
        DB::beginTransaction();
        try {
            foreach (array_slice($sheetData, 1) as $row) {
                $tgl = $row[0];
                $partai = $row[1];
                $urutan = $row[2];
                $nobox = $row[3];
                $pcsSortir = $row[4];
                $grSortir = $row[5];
                $grade = $row[6];
                $pcs = $row[7];
                $gr = $row[8];
                $noPengiriman = $row[9];
                $costbk = $row[10] ?? 0;
                $costkerja = $row[11] ?? 0;
                $costcu = $row[12] ?? 0;

                if (empty($tgl) && empty($partai)) {
                    continue;
                }

                if (
                    empty($tgl) ||
                    empty($partai)
                ) {
                    $pesan = [
                        empty($grade) => "GRADE",
                        empty($gr) => "GR",
                        empty($noPengiriman) => "BOX PENGIRIMAN",
                    ];
                    DB::rollBack();
                    return redirect()
                        ->route('gradingbj.index')
                        ->with('error', "ERROR! " . $pesan[true] . " : $grade, $gr, $noPengiriman" . 'TIDAK BOLEH KOSONG');
                } else {
                    // pengecekan grade jika tidak ada di list tb_grade
                    $cekGrade = DB::table('tb_grade')->where('nm_grade', $grade)->first();
                    if (!$cekGrade) {
                        DB::rollBack();
                        return redirect()
                            ->route('gradingbj.index')
                            ->with('error', "GRADE " . $grade . ' TIDAK TERDAFTAR');
                    }

                    $tipe = $cekGrade->tipe;
                    $no_inv = "$partai-$urutan";

                    // pengecekan nobox sortir tidak ada
                    if (!empty($nobox)) {
                        // $cekBox = DB::table('formulir_sarang')->where([['no_box', $nobox], ['kategori', 'grade']])->first();
                        // if (!$cekBox) {
                        //     DB::rollBack();
                        //     return redirect()
                        //         ->route('gradingbj.index')
                        //         ->with('error', "Box :  " . $nobox . ' BELUM SERAH KE GRADING');
                        // } else {
                        // }
                        DB::table('grading')->insert([
                            'no_box_sortir' => $nobox,
                            'pcs' => $pcsSortir,
                            'gr' => $grSortir,
                            'no_invoice' => $no_inv,
                            'tgl' => $tgl,
                            'admin' => "import-$tglD"
                        ]);
                    }
                    if (!empty($grade)) {
                        DB::table('grading_partai')->insert([
                            'nm_partai' => $partai,
                            'urutan' => $urutan,
                            'no_invoice' => $no_inv,
                            'box_pengiriman' => $noPengiriman,
                            'grade' => $grade,
                            'tipe' => $tipe,
                            'pcs' => $pcs,
                            'gr' => $gr,
                            'tgl' => $tgl,
                            'cost_bk' => $costbk,
                            'cost_kerja' => $costkerja,
                            'cost_cu' => $costcu,
                            'admin' => "import-$tglD"
                        ]);
                    }
                }
            }
            DB::commit();
            return redirect()->route('gradingbj.index')->with('sukses', 'Data berhasil import');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function selisih($no_box)
    {
        try {
            DB::beginTransaction();
            foreach (explode(',', $no_box) as $d) {
                $grading = Grading::dapatkanStokBox('formulir', $d)[0];
                $data[] = [
                    'no_box' => $d,
                    'pcs' => $grading->pcs_awal,
                    'gr' => $grading->gr_awal,
                    'admin' => auth()->user()->name,
                    'tgl' => date('Y-m-d'),
                ];
                DB::table('grading')->where('no_box_sortir', $d)->update(['selesai' => 'Y']);
            }
            DB::table('grading_selisih')->insert($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('sukses', 'Data Berhasil');
    }

    public function create(Request $r)
    {
        $noinvoice = $this->getNoInvoiceTambah();
        for ($i = 0; $i < count($r->grade); $i++) {
            $id_grade = $r->grade[$i];
            $no_box_sortir = $r->no_box_sortir[$i];
            $no_box_grading = $r->box_sp[$i];
            $pcs = $r->pcs[$i];
            $gr = $r->gr[$i];
            $admin = auth()->user()->name;
            $tgl = date('Y-m-d');

            $data[] = [
                'id_grade' => $id_grade,
                'no_box_grading' => $no_box_grading,
                'no_box_sortir' => $no_box_sortir,
                'pcs' => $pcs,
                'gr' => $gr,
                'admin' => $admin,
                'tgl' => $tgl,
                'no_invoice' => $noinvoice,
            ];
        }
        DB::table('grading')->insert($data);
        return redirect()->route('gradingbj.index')->with('sukses', 'Berhasil');
    }

    public function gudang_siap_kirim(Request $r)
    {
        $gudang = Grading::stock_wip();

        $data = [
            'title' => 'Stock Siap Kirim',
            'gudang' => $gudang
        ];
        return view('home.gradingbj.gudang_siap_kirim_partai', $data);
    }


    public function detail(Request $r)
    {
        $no_box = $r->no_box;
        $detail = DB::table('grading_partai as a')
            ->select(
                'a.box_pengiriman',
                'a.grade',
                'a.pcs',
                'a.gr',
                'a.nm_partai',
                'a.no_invoice',
                'a.sudah_kirim',
            )
            ->where('a.box_pengiriman', $no_box)->get();
        $data = [
            'no_box' => $no_box,
            'detail' => $detail
        ];
        return view('home.gradingbj.detail_gudang_siap_kirim', $data);
    }

    public function detail_perpartai(Request $r)
    {
        $no_box = $r->no_box;
        $detail = DB::table('grading_partai as a')
            ->selectRaw('a.id_grading,a.box_pengiriman,a.grade,a.nm_partai,a.no_invoice,a.sudah_kirim, a.bulan, a.pcs, a.gr')
            ->where('a.box_pengiriman', $no_box)
            ->groupBy('a.id_grading')
            ->get();

        $data = [
            'no_box' => $no_box,
            'detail' => $detail
        ];
        return view('home.gradingbj.detail_gudang_siap_kirim_perpartai', $data);
    }

    public function cancel_perpartai(Request $r)
    {
        $id_grading = $r->id_grading;

        $getFormulir = DB::select("SELECT 
            nm_partai,
            no_invoice,
            box_pengiriman,
            grade,
            pcs,
            bulan,
            gr,
            tgl,
            ttl_rp,
            cost_bk,
            cost_kerja,
            admin
            FROM `grading_partai`
            WHERE id_grading = '$id_grading'");
        $gradeStatuses = ['bentuk', 'turun'];
        $tb_grade = DB::table('tb_grade')->whereIn('status', $gradeStatuses)->orderBy('status', 'ASC')->get();
        $gradeTurun = $tb_grade->where('status', 'turun');
        $nm_partai = $getFormulir[0]->nm_partai;
        $admin = $getFormulir[0]->admin;
        $tgl = $getFormulir[0]->tgl;

        $no_invoice = $getFormulir[0]->no_invoice;
        $bulan = $getFormulir[0]->bulan;


        $data = [
            'title' => 'Cancel Grading',
            'bulan' => $bulan,
            'user' => auth()->user()->name,
            'gradeBentuk' => $tb_grade,
            'gradeTurun' => $gradeTurun,
            'getFormulir' => $getFormulir,
            'nm_partai' => $nm_partai,
            'id_grading' => $id_grading,
            'admin' => $admin,
            'no_invoice' => $no_invoice,
            'tgl' => $tgl,
            'formulir' => 'Y',
        ];
        return view('home.gradingbj.cancel_grading_partai', $data);
    }
    public function cancelBoxPengiriman(Request $r)
    {
        $no_invoice = $r->no_invoice;

        $getFormulir = DB::select("SELECT 
            nm_partai,
            no_invoice,
            box_pengiriman,
            grade,
            pcs,
            bulan,
            tahun,
            gr,
            tgl,
            sudah_kirim,
            formulir,
            ttl_rp,
            cost_bk,
            cost_kerja,
            cost_cu,
            cost_op,
            bulan,
            tahun,
            admin
            FROM `grading_partai`
            WHERE no_invoice = '$no_invoice'");


        $getBox = DB::select("SELECT a.no_box_sortir as no_box, a.pcs,a.gr, b.tipe FROM `grading` as a 
        join bk as b on a.no_box_sortir = b.no_box and b.kategori = 'cabut'
        WHERE a.no_invoice = '$no_invoice'");
        $gradeStatuses = ['bentuk', 'turun'];
        $tb_grade = DB::table('tb_grade')->whereIn('status', $gradeStatuses)->orderBy('status', 'ASC')->get();
        $gradeTurun = $tb_grade->where('status', 'turun');
        $nm_partai = $getFormulir[0]->nm_partai;
        $admin = $getFormulir[0]->admin;
        $tgl = $getFormulir[0]->tgl;

        $data = [
            'title' => 'Cancel Grading',
            'no_invoice' => $no_invoice,
            'nm_partai' => "nm_partai",
            'user' => auth()->user()->name,
            'gradeBentuk' => $tb_grade,
            'gradeTurun' => $gradeTurun,
            'getFormulir' => $getFormulir,
            'getBox' => $getBox,
            'nm_partai' => $nm_partai,
            'admin' => $admin,
            'tgl' => $tgl,
        ];
        return view('home.gradingbj.cancel_grading', $data);
    }

    public function createUlangPartai(Request $r)
    {
        try {
            DB::beginTransaction();
            $id_grading = $r->id_grading;
            $tgl = date('Y-m-d');



            $get = DB::table('grading_partai')->where('id_grading', $id_grading)->first();
            $bulan = $get->bulan;
            $tahun = $get->tahun;
            $no_invoice = $get->no_invoice;
            $nm_partai = $get->nm_partai;

            $urutan = $get->urutan;
            $tipe = $get->tipe;

            $gr = $get->gr;

            $rpGr = $get->ttl_rp / $gr;
            $rpGrBk = $get->cost_bk / $gr;
            $rpGrKerja = $get->cost_kerja / $gr;
            $rpGrOp = $get->cost_op / $gr;

            for ($i = 0; $i < count($r->grade); $i++) {
                $cekBox = DB::table('grading_partai')->where('box_pengiriman', $r->box_sp[$i])->where('bulan', '!=', $bulan)->first();
                if ($cekBox) {
                    DB::rollBack();
                    return redirect()->route('gradingbj.index')->with('error', "ERROR! Box Pengiriman " . $r->box_sp[$i] . " sudah terdaftar di bulan " . $cekBox->bulan);
                }

                $data[] = [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'no_invoice' => $no_invoice,
                    'nm_partai' => $nm_partai,
                    'urutan' => $urutan,
                    'grade' => $r->grade[$i],
                    'tipe' => $tipe,
                    'pcs' => $r->pcs[$i],
                    'gr' => $r->gr[$i],
                    'tgl' => $tgl,
                    'admin' => auth()->user()->name,
                    'box_pengiriman' => $r->box_sp[$i],
                    'ttl_rp' => $rpGr * $r->gr[$i],
                    'cost_bk' => $rpGrBk * $r->gr[$i],
                    'cost_kerja' => $rpGrKerja * $r->gr[$i],
                    'cost_cu' => 0,
                    'cost_op' => $rpGrOp * $r->gr[$i],
                    'formulir' => 'Y',
                ];
            }
            DB::table('grading_partai')->where('id_grading', $id_grading)->delete();
            DB::table('grading_partai')->insert($data);

            DB::commit();
            return redirect()->route('gradingbj.gudang_siap_kirim')->with('sukses', 'Berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gradingbj.gudang_siap_kirim')->with('error', $e->getMessage());
        }
    }

    public function createUlang(Request $r)
    {
        try {
            DB::beginTransaction();
            $no_invoice = $r->no_nota;
            $nm_partai = $r->nm_partai;
            $tgl = date('Y-m-d');

            $get = DB::table('grading_partai')->where('no_invoice', $no_invoice);
            $urutan = $get->first()->urutan;
            $boxPengiriman = $get->pluck('box_pengiriman');

            DB::table('grading_partai')->where('no_invoice', $no_invoice)->delete();

            $ttlGr = 0;
            $ttlRp = 0;
            $cost_bk = 0;
            $cost_kerja = 0;

            for ($i = 0; $i < count($r->no_box); $i++) {

                $getFormulirRp = Grading::dapatkanStokBoxtesting('formulir', $r->no_box[$i]);

                $tipe = $getFormulirRp[0]->tipe;

                $getFormulir = DB::table('formulir_sarang')->where([['kategori', 'grade'], ['no_box', $r->no_box[$i]]])->first();
                $dataGrading[] = [
                    'no_box_sortir' => $r->no_box[$i],
                    'pcs' => $getFormulir->pcs_awal,
                    'gr' => $getFormulir->gr_awal,
                    'no_invoice' => $no_invoice,
                    'admin' => auth()->user()->name,
                    'tgl' => $tgl,
                ];
                $ttlGr += $getFormulir->gr_awal;

                $ttlRp +=
                    sumBk($getFormulirRp, 'cost_bk') +
                    sumBk($getFormulirRp, 'cost_cbt') +
                    sumBk($getFormulirRp, 'cost_str') +
                    sumBk($getFormulirRp, 'cost_eo') +
                    sumBk($getFormulirRp, 'cost_ctk') +
                    sumBk($getFormulirRp, 'cost_cu');


                $cost_bk += sumBk($getFormulirRp, 'cost_bk');

                $cost_kerja +=
                    sumBk($getFormulirRp, 'cost_cbt') +
                    sumBk($getFormulirRp, 'cost_str') +
                    sumBk($getFormulirRp, 'cost_eo') +
                    sumBk($getFormulirRp, 'cost_ctk');
            }

            $rpGr = $ttlRp / $ttlGr;
            $rpGrBk = $cost_bk / $ttlGr;
            $rpGrKerja = $cost_kerja / $ttlGr;


            for ($i = 0; $i < count($r->grade); $i++) {
                $sudahKrim = $r->sudah_kirim[$i] == 'Y';

                $data[] = [
                    'bulan' => $r->bulan[$i],
                    'tahun' => $r->tahun[$i],
                    'no_invoice' => $no_invoice,
                    'nm_partai' => $nm_partai,
                    'urutan' => $urutan,
                    'grade' => $r->grade[$i],
                    'tipe' => $tipe,
                    'pcs' => $r->pcs[$i],
                    'gr' => $r->gr[$i],
                    'sudah_kirim' => $r->sudah_kirim[$i] ?? 'T',
                    'formulir' => $r->formulir[$i] ?? 'T',
                    'tgl' => $tgl,
                    'admin' => auth()->user()->name,
                    'box_pengiriman' => $r->box_sp[$i],
                    'ttl_rp' =>  $sudahKrim ? $r->ttl_rp[$i] : $rpGr * $r->gr[$i],
                    'cost_bk' => $sudahKrim ? $r->cost_bk[$i] : $rpGrBk * $r->gr[$i],
                    'cost_kerja' => $sudahKrim ? $r->cost_kerja[$i] : $rpGrKerja * $r->gr[$i],
                    'cost_cu' => 0,
                    'cost_op' => $sudahKrim ? $r->cost_op[$i] : 0,
                ];
            }

            DB::table('grading_partai')->insert($data);


            DB::commit();
            return redirect()->route('gradingbj.index')->with('sukses', 'Berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $r)
    {
        return redirect()->route('gradingbj.index')->with('sukses', 'Berhasil');
    }

    public function selesai(Request $r)
    {
        DB::table('grading')->where('no_box_grading', $r->no_box)->update(['selesai' => $r->selesai == 'T' ? 'Y' : 'T']);
        return redirect()->back()->with('sukses', 'diselesaikan');
    }

    public function opname(Request $r)
    {
        $data = [
            'title' => 'Grading Opname'
        ];
        return view('home.gradingbj.opname', $data);
    }

    public function detail_pengiriman(Request $r)
    {
        $getFormulir = DB::select("SELECT nm_partai,no_invoice,box_pengiriman,grade,pcs,gr,tgl,
        admin
        FROM `grading_partai`
        WHERE no_invoice = '$r->no_invoice' and grade != 'susut'");

        $getFormulirSusut = DB::select("SELECT nm_partai,no_invoice,box_pengiriman,grade,pcs,gr,tgl,
        admin
        FROM `grading_partai`
        WHERE no_invoice = '$r->no_invoice' and grade = 'susut'");

        $box_grading = Grading::detailPengiriman($r->no_invoice);


        $data = [
            'title' => 'Detail Pengiriman',
            'nm_partai' => $getFormulir[0]->nm_partai,
            'admin' => $getFormulir[0]->admin,
            'tgl' => $getFormulir[0]->tgl,
            'no_invoice' => $getFormulir[0]->no_invoice,
            'box_grading' => $box_grading,
            'grading' => $getFormulir,
            'grading_susut' => $getFormulirSusut,
            'rp_susut' => DB::selectOne("SELECT  * FROM rp_susut as a ")
        ];
        return view('home.gradingbj.detail_pengiriman', $data);
    }

    public function print_grading($no_nota)
    {
        $box_grading = Grading::detailPengiriman($no_nota);
        $getFormulir = DB::select("SELECT nm_partai,no_invoice,box_pengiriman,grade,pcs,gr,tgl,
        admin
        FROM `grading_partai`
        WHERE no_invoice = '$no_nota' and grade != 'susut'");

        $data = [
            'title' => 'Print Grading',
            'no_nota' => $no_nota,
            'box_grading' => $box_grading,
            'no_invoice' => $no_nota,
            'grading' => $getFormulir,


        ];
        return view('home.gradingbj.print_grading', $data);
    }

    public function template_import_gudang_siap_kirim()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Gudang Siap Kirim');
        $koloms = [
            'A' => 'tgl',
            'B' => 'pcs',
            'C' => 'gr',
            'D' => 'no box grading',
            'E' => 'no barcode pengiriman',
            'F' => 'no invoice',
        ];

        $tbGrade = DB::table('tb_grade')->get();
        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }

        // foreach($tbGrade as $i => $item){
        //     $sheet->setCellValue('L' . ($i+2), $item->nm_grade);
        //     $sheet->setCellValue('M' . ($i+2), $item->tipe);
        // }
        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];


        $sheet->getStyle('A1:F1')->applyFromArray($styleBold);
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($styleBaris);
        // $sheet->getStyle('L1:M1')->applyFromArray($styleBaris);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Template Gudang Siap Kirim";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );
    }

    public function import_gudang_siap_kirim(Request $r)
    {
        $file = $r->file('file');
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        $admin = auth()->user()->name;
        $tglHari = date('Y-m-d');
        DB::beginTransaction();
        try {
            foreach (array_slice($sheetData, 1) as $row) {

                $tgl = $row[0];
                $grade = $row[1];
                $pcs = $row[2];
                $gr = $row[3];
                $noboxGrading = $row[4];
                $noGradingPengiriman = $row[5];
                $no_invoice = $row[6];
                $ttl_rp = $row[7];



                if (empty($no_invoice) && empty($pcs) && empty($gr) && empty($noboxGrading) && empty($noGradingPengiriman)) {
                    continue;
                }

                if (
                    empty($gr) ||
                    empty($noGradingPengiriman)
                ) {
                    $pesan = [
                        empty($gr) => "GR",
                        empty($noGradingPengiriman) => "BOX GRADING PENGIRIMAN",
                    ];
                    DB::rollBack();
                    return redirect()
                        ->route('gradingbj.index')
                        ->with('error', "ERROR! " . $pesan[true] . 'TIDAK BOLEH KOSONG');
                } else {
                    // $grade = DB::table('grading_partai')->where('box_pengiriman', $noboxGrading)->first();
                    DB::table('pengiriman')->insert([
                        'tgl_input' => $tgl,
                        'pcs' => $pcs,
                        'gr' => $gr,
                        'no_box' => $noboxGrading,
                        'no_barcode' => $noGradingPengiriman,
                        'no_nota' => $no_invoice,
                        'admin' => "import-$tglHari",
                        'grade' => $grade,
                        'ttl_rp' => $ttl_rp,
                    ]);
                }
            }
            DB::commit();
            return redirect()->back()->with('sukses', 'Data berhasil import');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function gudang()
    {
        $posisi = auth()->user()->posisi_id;
        $data = [
            'title' => 'Gudang',
            'posisi' => $posisi,
            'gradingStok' => Grading::dapatkanStokBox('formulir'),
            'gradingSelesai' => Grading::selesai(),
            'gradingSisa' => Grading::sisa(),
            'users' => DB::table('users')->where('posisi_id', 16)->get(),
        ];
        return view('home.gradingbj.gudang', $data);
    }

    public function print(Request $r)
    {
        $box_grading = explode(',', $r->no_box);
        $get = DB::table('grading_partai')
            ->select('box_pengiriman', 'grade', 'nm_partai', DB::raw('SUM(pcs) as pcs'), DB::raw('SUM(gr) as gr'))
            ->whereIn('box_pengiriman', $box_grading)
            ->groupBy('box_pengiriman');

        $get->update(['sudah_print' => 'Y']);

        $getBox = $get->get();

        $data = [
            'title' => 'Print Grading',
            'getBox' => $getBox
        ];
        return view('home.gradingbj.print', $data);
    }

    public function save_formulir(Request $r)
    {
        try {
            DB::beginTransaction();
            $cekBox = DB::selectOne("SELECT no_invoice FROM `formulir_sarang` WHERE kategori = 'wip' ORDER by no_invoice DESC limit 1;");
            $no_invoice = isset($cekBox->no_invoice) ? $cekBox->no_invoice + 1 : 1001;
            $no_box = explode(',', $r->no_box[0]);

            foreach ($no_box as $d) {
                $cekBox = Grading::selesai($d);
                $data[] = [
                    'no_invoice' => $no_invoice,
                    'no_box' => $d,
                    'id_pemberi' => auth()->user()->id,
                    'id_penerima' => $r->id_penerima,
                    'pcs_awal' => $cekBox->pcs,
                    'gr_awal' => $cekBox->gr,
                    'tanggal' => date('Y-m-d'),
                    'kategori' => 'wip',
                ];

                $data2 = [
                    'bulan' => $r->bulan,
                    'tahun' => date('Y'),
                ];
                DB::table('grading_partai')->where('box_pengiriman', $d)->update($data2);
            }
            DB::table('formulir_sarang')->insert($data);

            DB::commit();
            return redirect()->back()->with('sukses', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('sukses', $e->getMessage());
        }
    }
}
