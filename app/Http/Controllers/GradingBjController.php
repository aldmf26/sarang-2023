<?php

namespace App\Http\Controllers;

use App\Exports\GradingbjTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GradingBjController extends Controller
{
    protected $nmTbl = 'pengiriman_gradingbj';
    public function index()
    {
        $data = [
            'title' => 'Grading BJ',
            'datas' => DB::select("SELECT a.no_grading,sum(pcs_awal) as pcs_awal, sum(gr_awal) as gr_awal,a.tgl,a.partai,a.ket,count(a.no_box) as ttl_box , sum(a.ttl_rp + a.cost_cabut + a.cost_cetak) as ttl_rp
            FROM `pengiriman_gradingbj` as a
            GROUP BY no_grading ORDER BY a.no_grading DESC;"),
            'gudangbj' => DB::select("SELECT grade,sum(pcs) as pcs, sum(gr) as gr FROM `pengiriman_list_gradingbj` GROUP BY grade")
        ];
        return view('home.gradingbj.index', $data);
    }

    public function add()
    {
        $cetak = DB::select("SELECT b.tipe,a.id_cetak,a.no_box,sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, b.ttl_rp, c.cost_cabut, ((a.pcs_akhir * a.rp_pcs) + a.rp_harian - (a.pcs_hcr * d.denda_hcr )) as cost_cetak
        FROM `cetak` as a
        join bk as b on a.no_box = b.no_box and b.kategori = 'cetak'
        left join (
        	SELECT c.no_box , sum(c.ttl_rp) as cost_cabut
            FROM cabut as c
            GROUP by c.no_box
        ) as c on c.no_box = a.no_box
        left join kelas_cetak as d on d.id_kelas_cetak = a.id_kelas
        LEFT JOIN `pengiriman_gradingbj` AS p ON a.no_box = p.no_box
        WHERE a.selesai = 'Y'  AND p.no_box IS NULL GROUP BY no_box ORDER BY b.tipe ASC;");

        if (!$cetak) {
            return redirect()->route('gradingbj.index')->with('error', 'Data Cetak Masih tidak ada !');
        }

        $data = [
            'title' => 'Tambah Grading BJ',
            'cetak' => $cetak
        ];
        return view('home.gradingbj.add', $data);
    }

    public function create(Request $r)
    {
        $db = DB::table($this->nmTbl);
        $no_nota = $db->orderBy('id_grading', 'DESC')->first();
        $no_nota = empty($no_nota) ? 1 : $no_nota->no_grading + 1;
        $tgl = $r->tgl;
        $admin = auth()->user()->name;
        $datas = [];
        for ($i = 0; $i < count($r->no_box); $i++) {

            $datas[] = [
                'ket' => $r->ket,
                'partai' => $r->partai,
                'no_box' => $r->no_box[$i],
                'pcs_awal' => $r->pcs_akhir[$i],
                'gr_awal' => $r->gr_akhir[$i],
                'admin' => $admin,
                'tgl' => $tgl,
                'no_grading' => $no_nota,
                'ttl_rp' => $r->ttl_rp[$i],
                'cost_cabut' => $r->cost_cabut[$i],
                'cost_cetak' => $r->cost_cetak[$i],
                'tipe' => $r->tipe[$i]
            ];
        }

        $db->insert($datas);
        return redirect()->route('gradingbj.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function create_grading(Request $r)
    {
        try {
            DB::beginTransaction();
            $datas = [];
            for ($i = 0; $i < count($r->gr); $i++) {
                if ($r->gr[$i] != 0 || !empty($r->gr[$i])) {
                    $datas[] = [
                        'grade' => $r->grade[$i],
                        'pcs' => $r->pcs[$i],
                        'gr' => $r->gr[$i],
                        'no_grading' => $r->no_grading,
                        'admin' => auth()->user()->name,
                        'tgl_grading' => date('Y-m-d'),
                    ];
                }
            }
            DB::table('pengiriman_list_gradingbj')->insert($datas);
            DB::commit();
            return redirect()->route('gradingbj.index')->with('sukses', 'Berhasil tambah grading');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gradingbj.index')->with('error', $e->getMessage());
        }
    }

    public function getDetail($no_grading)
    {
        return $data = [
            'no_grading' => $no_grading,
            'tbGrade' => DB::table('tb_grade')->get(),
            'listGrading' => DB::table('pengiriman_list_gradingbj')->where('no_grading', $no_grading)->get(),
            'box' => DB::table($this->nmTbl)->where('no_grading', $no_grading)->get()
        ];
    }

    public function load_grading(Request $r)
    {
        return view('home.gradingbj.grading', $this->getDetail($r->no_grading));
    }

    public function load_detail(Request $r)
    {
        return view('home.gradingbj.detail', $this->getDetail($r->no_grading));
    }

    public function template()
    {
        $tbl = DB::table("pengiriman_gradingbj as a")
            ->get();
        $totalrow = count($tbl) + 1;
        return Excel::download(new GradingbjTemplateExport($tbl, $totalrow), 'Template Grading BJ.xlsx');
    }

    public function import(Request $r)
    {
        $file = $r->file('file');
        $spreadsheet = IOFactory::load($file);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        DB::beginTransaction();
        try {
            foreach (array_slice($sheetData, 1) as $row) {
                if (empty(array_filter($row))) {
                    continue;
                }
                if (empty($row[0]) && !empty($row[7])) {
                    $no_nota = DB::table($this->nmTbl)->orderBy('id_grading', 'DESC')->first();
                    $no_nota = empty($no_nota) ? 1 : $no_nota->no_grading + 1;

                    DB::table('pengiriman_gradingbj')->insert([
                        'no_grading' => $no_nota,
                        'tgl' => $row[2],
                        'ket' => $row[3],
                        'partai' => $row[4],
                        'no_box' => $row[5],
                        'pcs_awal' => $row[6],
                        'gr_awal' => $row[7],
                        'pcs_akhir' => $row[8],
                        'gr_akhir' => $row[9],
                        'admin' => auth()->user()->name,
                    ]);
                } else {
                    DB::table('pengiriman_gradingbj')->where('id_grading', $row[0])->update([
                        'tgl' => $row[2],
                        'ket' => $row[3],
                        'partai' => $row[4],
                        'no_box' => $row[5],
                        'pcs_awal' => $row[6],
                        'gr_awal' => $row[7],
                        'pcs_akhir' => $row[8],
                        'gr_akhir' => $row[9],
                        'admin' => auth()->user()->name,
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

    public function load_ambil_box_kecil()
    {
        $data = [
            'title' => 'Tambah Box Kecil'
        ];
        return view('home.gradingbj.load_ambil_box_kecil', $data);
    }
}
