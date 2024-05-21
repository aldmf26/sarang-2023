<?php

namespace App\Http\Controllers;

use App\Exports\GradingbjTemplateExport;
use App\Models\ApiGudangGradingModel;
use App\Models\PengirimanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GradingBjController extends Controller
{
    protected $nmTbl = 'pengiriman_gradingbj';
    public function getDataMaster($jenis)
    {

        $arr = [
            'gradingbj' => DB::select("SELECT grade, sum(pcs) as pcs, sum(gr) as gr, sum(gr * rp_gram) as ttl_rp, sum(pcs_kredit) as pcs_kredit, sum(gr_kredit) as gr_kredit, sum(gr_kredit * rp_gram_kredit) as ttl_rp_kredit
                        FROM `pengiriman_list_gradingbj` 
                        GROUP BY grade 
                        HAVING pcs - pcs_kredit <> 0 OR gr - gr_kredit <> 0"),
            'pengawas' => DB::table('users')->where('posisi_id', 13)->get()
        ];
        return $arr[$jenis];
    }
    public function index(Request $r)
    {
        $data = [
            'title' => 'Grading BJ',
            'datas' => DB::select("SELECT a.no_grading,sum(pcs_awal) as pcs_awal, sum(gr_awal) as gr_awal,a.tgl,a.partai,a.ket,count(a.no_box) as ttl_box , sum(a.ttl_rp + a.cost_cabut + a.cost_cetak) as ttl_rp
            FROM `pengiriman_gradingbj` as a
            GROUP BY no_grading ORDER BY a.no_grading DESC;"),
            'gudangbj' => $this->getDataMaster('gradingbj'),
            'kategori' => 'grading'
        ];

        return view('home.gradingbj.index', $data);
    }

    public function gudang_bj()
    {
        $data = [
            'title'  => 'Grading Bj',
            'gudangbj' => $this->getDataMaster('gradingbj'),
        ];
        return view('home.gradingbj.gudang_bj', $data);
    }

    public function add()
    {
        $cetak = ApiGudangGradingModel::dataCetak();
        $cabut_selesai = ApiGudangGradingModel::cabutSelesai();
        $suntikan = ApiGudangGradingModel::suntikan();

        $data = [
            'title' => 'Tambah Grading BJ',
            'cetak' => $cetak,
            'cabut_selesai' => $cabut_selesai,
            'suntikan' => $suntikan

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
        return redirect()->route('gradingbj.history_ambil')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function create_grading(Request $r)
    {
        try {
            DB::beginTransaction();
            $datas = [];
            for ($i = 0; $i < count($r->gr); $i++) {
                $rp_gram = $r->ttl_rp / $r->ttl_gr;
                if ($r->gr[$i] != 0 || !empty($r->gr[$i])) {
                    $datas[] = [
                        'grade' => $r->grade[$i],
                        'pcs' => $r->pcs[$i],
                        'gr' => $r->gr[$i],
                        'no_grading' => $r->no_grading,
                        'admin' => auth()->user()->name,
                        'tgl_grading' => date('Y-m-d'),
                        'rp_gram' => $rp_gram
                    ];
                }
            }
            DB::table('pengiriman_list_gradingbj')->insert($datas);
            DB::commit();
            return redirect()->route('gradingbj.history_ambil')->with('sukses', 'Berhasil tambah grading');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gradingbj.history_ambil')->with('error', $e->getMessage());
        }
    }

    public function getDetail($no_grading)
    {
        return $data = [
            'no_grading' => $no_grading,
            'tbGradeBentuk' => DB::table('tb_grade')->where('status', 'bentuk')->get(),
            'tbGradeTurun' => DB::table('tb_grade')->where('status', 'turun')->get(),
            'listGrading' => DB::table('pengiriman_list_gradingbj')->where('no_grading', $no_grading)->where('no_box', NULL)->get(),
            'box' => DB::table($this->nmTbl)->where('no_grading', $no_grading)->get(),
            'boxJudul' => DB::table($this->nmTbl)->where('no_grading', $no_grading)->first()
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
            'title' => 'Tambah Box Kecil',
            'gudangbj' => $this->getDataMaster('gradingbj'),
            'pengawas' => $this->getDataMaster('pengawas')

        ];
        return view('home.gradingbj.load_ambil_box_kecil', $data);
    }

    public function get_select_grade(Request $r)
    {
        $grade = $r->grade;
        $cek = DB::selectOne("SELECT grade,sum(pcs - pcs_kredit) as pcs, sum(gr - gr_kredit) as gr, sum((gr * rp_gram) - (gr_kredit * rp_gram_kredit)) as ttl_rp FROM `pengiriman_list_gradingbj` 
        WHERE grade = '$grade'
        GROUP BY grade HAVING pcs <> 0 OR gr <> 0");

        return json_encode([
            'pcs' => $cek->pcs,
            'gr' => $cek->gr,
            'ttl_rp' => $cek->ttl_rp
        ]);
    }

    public function create_ambil_box_kecil(Request $r)
    {
        try {
            DB::beginTransaction();
            $tgl = $r->tgl;
            $grade = $r->grade;
            $pcsTtlAmbil = $r->pcsTtlAmbil;
            $grTtlAmbil = $r->grTtlAmbil;
            $ttlrpTtlAmbil = floatval(str_replace(['.', ','], ['', '.'], $r->ttlrpTtlAmbil));


            $rpGram = $ttlrpTtlAmbil / $grTtlAmbil;
            $datas = [];
            $datasBk = [];
            $noGrading = DB::table('pengiriman_list_gradingbj')->orderBy('id_list_grading', 'DESC')->first();
            $noGrading = empty($noGrading) ? 1 : $noGrading->no_grading + 1;
            for ($i = 0; $i < count($r->gr); $i++) {
                $datas[] = [
                    'no_grading' => $noGrading,
                    'tgl_grading' => $tgl,
                    'grade' => $grade,
                    'pcs_kredit' => $r->pcs[$i],
                    'no_box' => $r->no_box[$i],
                    'gr_kredit' => $r->gr[$i],
                    'admin' => auth()->user()->name,
                    'rp_gram_kredit' => $rpGram,
                    'pengawas' => $r->pengawas[$i] ?? 0
                ];
                // $id_pengws = DB::table('users')->where('name', $r->pengawas[$i])->first()->id;

                $datasBk[] = [
                    'nm_partai' => '1',
                    'no_box' => $r->no_box[$i],
                    'tipe' =>  $grade,
                    'ket' => '1',
                    'warna' => '1',
                    'pengawas' => auth()->user()->name,
                    'penerima' => $id_pengws ?? 0,
                    'pcs_awal' => $r->pcs[$i],
                    'gr_awal' => $r->gr[$i],
                    'tgl' => $tgl,
                    'kategori' => 'sortir'
                ];
            }
            DB::table('pengiriman_list_gradingbj')->insert($datas);
            DB::table('bk')->insert($datasBk);

            DB::commit();
            return redirect()->route('gradingbj.history_box_kecil')->with('sukses', 'Berhasil di tambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gradingbj.index')->with('error', $e->getMessage());
        }
    }

    public function history_box_kecil()
    {
        $boxKecil = ApiGudangGradingModel::historyBoxKecil();
        $data = [
            'title' => 'History Box Kecil',
            'box_kecil' => $boxKecil,
            'pengawas' => $this->getDataMaster('pengawas')
        ];
        return view('home.gradingbj.history_box_kecil', $data);
    }

    public function gudang_bahan_jadi(Request $r)
    {
        $data = [
            'title'  => 'Grading Bj',
            'gudangbj' => PengirimanModel::Pengiriman(),
        ];
        return view('home.gradingbj.gudang_bhn_jadi', $data);
    }

    public function halAwal()
    {
        $data = [
            'title'  => 'Grading Bj',
        ];
        return view('home.gradingbj.halawal', $data);
    }

    public function create_suntikan(Request $r)
    {
        for ($i = 0; $i < count($r->gr); $i++) {
            $datas[] = [
                'nm_partai' => $r->nm_partai[$i],
                'tipe' => $r->tipe[$i],
                'no_box' => $r->no_box[$i],
                'pcs' => $r->pcs[$i],
                'gr' => $r->gr[$i],
                'ttl_rp' => $r->ttl_rp[$i],
                'cost_cabut' => $r->cost_cabut[$i],
                'cost_cetak' => $r->cost_cetak[$i],
                'tgl' => date('Y-m-d'),
                'admin' => auth()->user()->name
            ];
        }

        DB::table('grading_suntikan')->insert($datas);
        return redirect()->route('gradingbj.add')->with('sukses', 'Data Berhasil ditambahkan');
    }
    public function create_suntikan_boxsp(Request $r)
    {
        try {
            DB::beginTransaction();
            $noGrading = DB::table('pengiriman_list_gradingbj')->orderBy('no_grading', 'DESC')->first();
            for ($i = 0; $i < count($r->gr_kredit); $i++) {
                $cekGrade = DB::table('pengiriman_list_gradingbj')->where('grade', $r->grade[$i])->first();
                if (!$cekGrade) {
                    return redirect()->route('gradingbj.history_box_kecil')->with('error', 'Grade tidak ada di gudang grading');
                }
                $datas[] = [
                    'no_grading' => $noGrading->no_grading + 1,
                    'grade' => $r->grade[$i],
                    'no_box' => $r->no_box[$i],
                    'pcs_kredit' => $r->pcs_kredit[$i],
                    'gr_kredit' => $r->gr_kredit[$i],
                    'rp_gram_kredit' => $r->rp_gram_kredit[$i],
                    'pengawas' => $r->pengawas[$i],
                    'tgl_grading' => date('Y-m-d'),
                    'admin' => auth()->user()->name
                ];
            }

            DB::table('pengiriman_list_gradingbj')->insert($datas);
            DB::commit();
            return redirect()->route('gradingbj.history_box_kecil')->with('sukses', 'Data Berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gradingbj.history_box_kecil')->with('error', $e->getMessage());
        }
    }
}
