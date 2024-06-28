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

        $arr = [
            'formulir' => $formulir,
            'pengawas' => DB::table('users')->where('posisi_id', 13)->get()
        ];
        return $arr[$jenis];
    }
    public function index(Request $r)
    {
        $data = [
            'title' => 'Grading',
            'formulir' => Grading::dapatkanStokBox('formulir')
        ];

        return view('home.gradingbj.index', $data);
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

    public function exportGrading($no_box)
    {
        $getFormulir = Grading::dapatkanStokBox('formulir', $no_box);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Grading');
        $koloms = [
            'A' => 'No Box Dipilih',
            'B' => 'pcs',
            'C' => 'gr',

            'F' => 'No Box',
            'G' => 'grade',
            'H' => 'pcs',
            'I' => 'gr',
            'J' => 'box pengiriman',

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

        $no = 2;
        foreach ($getFormulir as $item) {
            $sheet->setCellValue('A' . $no, $item->no_box);
            $sheet->setCellValue('B' . $no, $item->pcs_awal);
            $sheet->setCellValue('C' . $no, $item->gr_awal);

            $no++;
        }

        $sheet->getStyle('A1:C' . $no - 1)->applyFromArray($styleBaris);

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

    public function import(Request $r)
    {
        $file = $r->file('file');
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        $noinvoice = $this->getNoInvoiceTambah();

        $admin = auth()->user()->name;
        $tgl = date('Y-m-d');

        DB::beginTransaction();
        try {
            foreach (array_slice($sheetData, 1) as $row) {
                $nobox = $row[5];
                $grade = $row[6];
                $pcs = $row[7];
                $gr = $row[8];
                $boxPengiriman = $row[9];

                if (empty(array_filter($row))) {
                    continue;
                }

                if (
                    empty($nobox) ||
                    empty($grade) ||
                    // empty($pcs) ||
                    empty($gr) ||
                    empty($boxPengiriman)
                ) {
                    $pesan = [
                        empty($nobox) => "NO BOX",
                        empty($grade) => "GRADE",
                        // empty($pcs) => "PCS",
                        empty($gr) => "GR",
                        empty($boxPengiriman) => "BOX PENGIRIMAN",
                    ];
                    DB::rollBack();
                    return redirect()
                            ->route('gradingbj.index')
                            ->with('error', "ERROR! " . $pesan[true] . 'TIDAK BOLEH KOSONG');
                } else {
                    $cekGrade = DB::table('tb_grade')->where('nm_grade', $grade)->first();
                    if(!$cekGrade){
                        DB::rollBack();
                        return redirect()
                                ->route('gradingbj.index')
                                ->with('error', "GRADE " . $grade . ' TIDAK TERDAFTAR');
                    }

                    DB::table('grading')->insert([
                        'id_grade' => $cekGrade->id_grade,
                        'no_box_grading' => $boxPengiriman,
                        'no_box_sortir' => $nobox,
                        'pcs' => $pcs,
                        'gr' => $gr,
                        'admin' => $admin,
                        'tgl' => $tgl,
                        'no_invoice' => $noinvoice,
                    ]);
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
        $gudang = Grading::siapKirim();
        $data = [
            'title' => 'Stock Siap Kirim',
            'gudang' => $gudang
        ];
        return view('home.gradingbj.gudang_siap_kirim', $data);
    }
    public function detail(Request $r)
    {
        $no_box = $r->no_box;
        $detail = DB::table('grading as a')
            ->select('c.tipe','c.ket', 'b.nm_grade as grade', 'a.no_box_grading as no_box', 'a.no_box_sortir', 'a.pcs', 'a.gr')
            ->join('tb_grade as b', 'a.id_grade', 'b.id_grade')
            ->join('bk as c', 'c.no_box', 'a.no_box_sortir')
            ->where([['a.no_box_grading', $no_box], ['c.kategori', 'sortir']])->get();
        $data = [
            'no_box' => $no_box,
            'detail' => $detail
        ];
        return view('home.gradingbj.detail_gudang_siap_kirim', $data);
    }

    public function cancel(Request $r)
    {
        DB::table('grading')->where('no_box_grading', $r->no_box)->delete();
        return redirect()->back()->with('sukses', 'diselesaikan');
    }

    public function selesai(Request $r)
    {
        DB::table('grading')->where('no_box_grading', $r->no_box)->update(['selesai' => $r->selesai == 'T' ? 'Y' : 'T']);
        return redirect()->back()->with('sukses', 'diselesaikan');
    }
}
