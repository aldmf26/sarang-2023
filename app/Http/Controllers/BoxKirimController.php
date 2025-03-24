<?php

namespace App\Http\Controllers;

use App\Exports\TblPoExport;
use App\Models\Grading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BoxKirimController extends Controller
{

    public function index()
    {
        $deleteToken = bin2hex(random_bytes(32));
        Session::put('delete_token', $deleteToken);

        $boxKirim = DB::table('pengiriman')->select('no_box', 'grade', 'pcs', 'gr', 'no_barcode', 'id_pengiriman')->get();
        $data = [
            'title' => 'Box Kirim',
            'boxKirim' => $boxKirim,
            'deleteToken' => $deleteToken,
        ];
        return view('home.pengiriman.index', $data);
    }

    public function add(Request $r)
    {
        $data = [
            'title' => 'Buat Box Pengiriman',
        ];
        return view('home.pengiriman.add', $data);
    }

    public function create(Request $r)
    {
        if (!$r->gr) {
            return redirect()->back()->with('error', 'Data Belum Lengkap');
        }
        DB::beginTransaction();
        try {
            $admin = auth()->user()->name;
            $tgl_input = date('Y-m-d');
            $no_nota = DB::table('pengiriman')->orderBy('id_pengiriman', 'DESC')->first();
            $no_nota = empty($no_nota) ? 1001 : $no_nota->no_nota + 1;

            $dataToInsert = [];
            for ($i = 0; $i < count($r->gr); $i++) {
                if ($r->pcs[$i] != 0) {

                    $grade = DB::table('grading as a')
                        ->join('tb_grade as b', 'a.id_grade', '=', 'b.id_grade')
                        ->where('a.no_box_grading', $r->no_grading[$i])
                        ->first()
                        ->nm_grade;

                    $dataToInsert[] = [
                        'no_box' => $r->no_grading[$i],
                        'pcs' => $r->pcs[$i],
                        'gr' => $r->gr[$i],
                        'cek_qc' => $r->cek_qc[$i],
                        'no_barcode' => $r->no_barcode[$i],
                        'admin' => $admin,
                        'grade' => $grade,
                        'tgl_input' => $tgl_input,
                        'no_nota' => $no_nota,
                    ];
                }
            }

            DB::table('pengiriman')->insert($dataToInsert);

            DB::commit();
            return redirect()->route('pengiriman.index')->with('sukses', 'Data Berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengiriman.index')->with('error', $e->getMessage());
        }
    }



    public function edit(Request $r)
    {
        $token = $r->token;
        if (!$token || $token !== Session::get('delete_token')) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid token.');
        }
        // Dapatkan dan validasi id_pengiriman
        $id_pengiriman = explode(',', $r->id_pengiriman);
        if (empty($id_pengiriman)) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid id_pengiriman.');
        }
        $id_pengiriman = array_map('intval', $id_pengiriman);

        $boxKirim = DB::table('pengiriman')
            ->select('tgl_input', 'cek_qc', 'no_box', 'grade', 'pcs', 'gr', 'no_barcode', 'id_pengiriman')
            ->whereIn('id_pengiriman', $id_pengiriman)
            ->get();
        $data = [
            'title' => 'Edit Box Kirim',
            'boxKirim' => $boxKirim
        ];
        return view('home.pengiriman.edit', $data);
    }

    public function update(Request $r)
    {
        $token = $r->token;
        if (!$token || $token !== Session::get('delete_token')) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid token.');
        }
        // Dapatkan dan validasi id_pengiriman
        $id_pengiriman = explode(',', $r->id_pengiriman);
        if (empty($id_pengiriman)) {
            return redirect()->route('pengiriman.index')->with('error', 'Invalid id_pengiriman.');
        }

        DB::beginTransaction();
        try {
            $admin = auth()->user()->name;

            for ($i = 0; $i < count($r->gr); $i++) {
                if ($r->pcs[$i] != 0) {

                    $grade = DB::table('grading as a')
                        ->join('tb_grade as b', 'a.id_grade', '=', 'b.id_grade')
                        ->where('a.no_box_grading', $r->no_grading[$i])
                        ->first()
                        ->nm_grade;

                    $dataToInsert = [
                        'no_box' => $r->no_grading[$i],
                        'pcs' => $r->pcs[$i],
                        'gr' => $r->gr[$i],
                        'cek_qc' => $r->cek_qc[$i],
                        'no_barcode' => $r->no_barcode[$i],
                        'admin' => $admin,
                        'grade' => $grade,
                        'tgl_input' => $r->tgl_input[$i],
                    ];
                    DB::table('pengiriman')->where('id_pengiriman', $r->id_pengiriman[$i])->update($dataToInsert);
                }
            }


            DB::commit();
            return redirect()->route('pengiriman.index')->with('sukses', 'Data Berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengiriman.index')->with('error', $e->getMessage());
        }
    }

    public function delete(Request $r)
    {
        // Hapus data
        $find = DB::table('pengiriman')->where('id_pengiriman', $r->id);
        // foreach($find->get() as $d){
        //     DB::table('grading_partai')->where('box_pengiriman', $d->no_box)->update(['sudah_kirim' => 'T']);
        // }
        $find->delete();
        return redirect()->back()->with('success', 'Data dihapus');
    }


    public function ubah(Request $r)
    {
        // Hapus data
        $data2 = [
            'pcs' => $r->pcs,
            'gr' => $r->gr,
            'no_barcode' => $r->barcode,
            'grade' => $r->grade,
        ];
        DB::table('pengiriman')->where('id_pengiriman', $r->id_pengiriman)->update($data2);
    }

    public function kirim(Request $r)
    {
        if ($r->submit == 'print') {
            return redirect()->route('gradingbj.print', ['no_box' => $r->no_box]);
        } else {
            $admin = auth()->user()->name;
            $tgl_input = date('Y-m-d');
            $no_nota = DB::table('pengiriman')->orderBy('no_nota', 'DESC')->value('no_nota');
            $no_nota = empty($no_nota) ? 1001 : $no_nota + 1;
            $no_nota = $r->no_nota ?? $no_nota;
            foreach (explode(',', $r->no_box) as $d) {
                $ambilBox = DB::selectOne("SELECT grade,sum(pcs) as pcs, sum(gr) as gr, sum(a.ttl_rp) as ttl_rp , 
            sum(a.cost_bk) as cost_bk, sum(a.cost_kerja) as cost_kerja, sum(a.cost_cu) as cost_cu
            FROM `grading_partai` as a
                    where a.box_pengiriman = $d
                    group by a.box_pengiriman");
                $dataToInsert[] = [
                    'no_box' => $d,
                    'pcs' => $ambilBox->pcs,
                    'gr' => $ambilBox->gr,
                    'admin' => $admin,
                    'grade' => $ambilBox->grade,
                    'tgl_input' => $tgl_input,
                    'no_nota' => $no_nota,
                    'rp_gram' => 1,
                    'ttl_rp' => $ambilBox->ttl_rp,
                    'cost_bk' => $ambilBox->cost_bk,
                    'cost_kerja' => $ambilBox->cost_kerja,
                    'cost_cu' => $ambilBox->cost_cu,
                ];
            }
            DB::table('pengiriman')->insert($dataToInsert);
            return redirect()->route('pengiriman.po', $no_nota)->with('sukses', 'data sudah masuk po');
        }
    }
    public function qc(Request $r)
    {
        if ($r->submit == 'print') {
            return redirect()->route('gradingbj.print', ['no_box' => $r->no_box]);
        } else {
            $admin = auth()->user()->name;
            $tgl_input = date('Y-m-d');
            $no_invoice = DB::table('formulir_sarang')->where('kategori', 'qc')->orderBy('no_invoice', 'DESC')->value('no_invoice');
            $no_invoice = empty($no_invoice) ? 1001 : $no_invoice + 1;


            foreach (explode(',', $r->no_box) as $d) {
                $ambilBox = DB::selectOne("SELECT grade,sum(pcs) as pcs, sum(gr) as gr
                FROM grading_partai as a
                where a.box_pengiriman = $d
                group by a.box_pengiriman");

                $data1 = [
                    'cek_qc' => 'Y',
                ];
                DB::table('grading_partai')->where('box_pengiriman', $d)->update($data1);

                $dataToInsert[] = [
                    'no_invoice' => $no_invoice,
                    'no_box' => $d,
                    'id_pemberi' => 459,
                    'id_penerima' => 459,
                    'pcs_awal' => $ambilBox->pcs,
                    'gr_awal' => $ambilBox->gr,
                    'tanggal' => $tgl_input,
                    'kategori' => 'qc',
                    'selesai' => 'T',
                ];
            }
            DB::table('formulir_sarang')->insert($dataToInsert);

            return redirect()->back()->with('sukses', 'data sudah masuk qc');
        }
    }



    public function kirim_grade2(Request $r)
    {
        $admin = auth()->user()->name;
        $tgl_input = date('Y-m-d');
        dd($r->all());
    }
    public function list_po(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $packing = DB::select("SELECT 
            a.no_nota,
            count(*) as ttl_box,
            a.tgl_input,
            sum(a.pcs) as pcs,
            sum(a.gr) as gr  
            FROM `pengiriman` as a
            WHERE selesai = 'T' GROUP BY a.no_nota order by a.no_nota DESC");


        $tgl1 = tglFormat($tgl1);
        $tgl2 = tglFormat($tgl2);
        $data = [
            'title' => 'List Po ',
            'packing' => $packing,
        ];

        return view('home.packinglist.list_po_pengiriman', $data);
    }


    public function po($no_nota)
    {
        $po = DB::selectOne("SELECT a.no_barcode,a.tgl_input as tanggal,a.no_nota,sum(pcs) as pcs, sum(gr) as gr, count(*) as ttl FROM `pengiriman` as a
                WHERE a.no_nota = $no_nota GROUP by a.no_nota order by a.id_pengiriman ASC");
        if (empty($po)) {
            return redirect()->route('gradingbj.gudang_siap_kirim')->with('error', 'data tidak ditemukan');
        }
        $data = [
            'title' => 'Wip siap kirim',
            'po' => $po,
            'no_nota' => $no_nota,
        ];
        return view('home.pengiriman.po', $data);
    }

    public function po_export($no_nota)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $style_atas = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
        );


        $kolom = [
            'A' => 'no box',
            'B' => 'grade 1',
            'C' => 'pcs 1',
            'D' => 'gr 1',
            'E' => 'grade 2',
            'F' => 'pcs 2',
            'G' => 'gr 2',
            'H' => 'no barcode',
        ];
        foreach ($kolom as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }
        $pengiriman = Grading::tbl_po($no_nota);
        $no = 2;

        foreach ($pengiriman as $item) {
            $sheet->setCellValue('A' . $no, $item->no_box);
            $sheet->setCellValue('B' . $no, $item->grade1);
            $sheet->setCellValue('C' . $no, $item->pcs1);
            $sheet->setCellValue('D' . $no, $item->gr1);
            $sheet->setCellValue('E' . $no, $item->grade2);
            $sheet->setCellValue('F' . $no, $item->pcs2);
            $sheet->setCellValue('G' . $no, $item->gr2);
            $sheet->setCellValue('H' . $no, $item->no_barcode);

            $no++;
        }

        $sheet->getStyle('A1:H1')->applyFromArray($style_atas);
        $sheet->getStyle('A2:H' . $no - 1)->applyFromArray($styleBaris);


        $writer = new Xlsx($spreadsheet);

        // Menggunakan response untuk mengirimkan file ke browser
        $fileName = "Export List Po $no_nota";
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

    public function load_tbl_po(Request $r)
    {
        $no_nota = $r->no_nota;

        $pengiriman = Grading::tbl_po($no_nota);

        $data = [
            'title' => 'Wip siap kirim',
            'no_nota' => $no_nota,
            'pengiriman' => $pengiriman
        ];
        return view('home.packinglist.tbl_po', $data);
    }

    public function loadTblTmbhBox(Request $r)
    {
        $gudang = Grading::stock_wip();
        $data = [
            'title' => 'Wip siap kirim',
            'gudang' => $gudang,
        ];
        return view('home.pengiriman.tbl_tbh_po', $data);
    }

    public function loadTblSumList(Request $r)
    {
        $pengiriman = Grading::tbl_po($r->no_nota);
        $data = [
            'title' => 'Wip siap kirim',
            'pengiriman' => $pengiriman,
        ];
        return view('home.pengiriman.tbl_sum_listpo', $data);
    }
    public function save_po(Request $r)
    {
        try {
            DB::beginTransaction();
            $no_invoice = $r->no_nota;
            $tgl = $r->tgl;
            $getFormulir = DB::table('pengiriman')->where('no_nota', $no_invoice)->get();
            if ($r->submit == 'draft') {
                for ($i = 0; $i < count($r->id_pengiriman); $i++) {
                    $data2 = [
                        'tgl_input' => $tgl,
                        'pcs' => $r->pcs2[$i],
                        'gr' => $r->gr2[$i],
                        'no_barcode' => $r->barcode[$i],
                        'grade' => $r->grade2[$i],
                        'no_nota' => $no_invoice,
                    ];
                    DB::table('pengiriman')->where('id_pengiriman', $r->id_pengiriman[$i])->update($data2);
                }
                $redir = "pengiriman.list_po";
            } else {
                foreach ($getFormulir as $d) {
                    $data[] = [
                        'id_pengiriman' => $d->no_box,
                        'tgl' => $tgl,
                        'no_nota' => $no_invoice,
                        'nm_packing' => $r->nm_packing,
                        'tujuan' => $r->tujuan,
                        'kadar' => $r->kadar,
                    ];
                }
                DB::table('pengiriman_packing_list')->insert($data);

                for ($i = 0; $i < count($r->id_pengiriman); $i++) {
                    $data2 = [
                        'tgl_input' => $tgl,
                        'pcs' => $r->pcs2[$i],
                        'gr' => $r->gr2[$i],
                        'no_barcode' => $r->barcode[$i],
                        'grade' => $r->grade2[$i],
                        'no_nota' => $no_invoice,
                    ];

                    DB::table('pengiriman')->where('id_pengiriman', $r->id_pengiriman[$i])->update($data2);
                    DB::table('pengiriman')->where('no_nota', $no_invoice)->update(['selesai' => 'Y']);
                    DB::table('grading_partai')->where('box_pengiriman', $r->box_grading[$i])->update(['sudah_kirim' => 'Y']);
                }
                $redir = "packinglist.pengiriman";
            }
            DB::commit();
            return redirect()->route($redir)->with('sukses', 'Data Berhasil di selesaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function selesai_grade(Request $r)
    {
        try {
            DB::beginTransaction();
            $no_invoice = $r->no_invoice;
            $tgl = date('Y-m-d');
            $getFormulir = DB::table('pengiriman')->where('no_nota', $no_invoice)->get();
            foreach ($getFormulir as $d) {
                $data[] = [
                    'id_pengiriman' => $d->id_pengiriman,
                    'tgl' => $tgl,
                    'no_nota' => $d->no_nota,
                ];
            }
            DB::table('pengiriman_packing_list')->insert($data);
            DB::commit();
            return redirect()->back()->with('sukses', 'Data Berhasil di selesaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }



    public function print_formulir_grade(Request $r)
    {
        $formulir = DB::table('pengiriman_packing_list as a')
            ->join('pengiriman as b', 'a.id_pengiriman', '=', 'b.no_box')
            ->where('a.no_nota', $r->no_invoice)
            ->select('b.no_box', 'b.pcs', 'b.gr', 'b.grade', 'a.tgl')
            ->get();
        $data = [
            'title' => 'Gudang Sarang',
            'formulir' => $formulir,
        ];
        return view('home.pengiriman.print_po', $data);
    }

    public function batal($no_nota)
    {
        $find = DB::table('pengiriman')->where('no_nota', $no_nota);
        // foreach($find->get() as $d){
        //     DB::table('grading_partai')->where('box_pengiriman', $d->no_box)->update(['sudah_kirim' => 'T']);
        // }
        $find->delete();
        return redirect()->route('gradingbj.gudang_siap_kirim')->with('sukses', 'Data Berhasil di selesaikan');
    }

    public function gudang(Request $r)
    {
        $selesai = DB::table('pengiriman_packing_list as a')
            ->join('pengiriman as b', 'a.id_pengiriman', '=', 'b.no_box')
            ->select('b.no_box', 'b.pcs', 'b.gr', 'b.grade', 'b.rp_gram')
            ->get();

        $gudang = Grading::gudangPengirimanGr();
        // $gudang = Grading::siapKirim();

        $data = [
            'title' => 'Gudang Pengiriman',
            'gudang' => $gudang,
            'selesai' => $selesai
        ];
        return view('home.pengiriman.gudang', $data);
    }
}
