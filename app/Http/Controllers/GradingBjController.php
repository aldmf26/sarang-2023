<?php

namespace App\Http\Controllers;

use App\Exports\GradingbjTemplateExport;
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
            // 'kategori' => $kategori
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
        $cetak = DB::table('cetak as a')
            ->selectRaw('b.tipe, a.id_cetak, a.no_box, SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, b.ttl_rp as total_rp, c.cost_cabut, ((a.pcs_akhir * a.rp_pcs) + a.rp_harian - (a.pcs_hcr * d.denda_hcr )) as cost_cetak')
            ->join('bk as b', function ($join) {
                $join->on('a.no_box', '=', 'b.no_box')
                    ->where('b.kategori', '=', 'cetak');
            })
            ->leftJoin(DB::raw('(SELECT c.no_box, SUM(c.ttl_rp) as cost_cabut FROM cabut as c GROUP BY c.no_box) as c'), 'c.no_box', '=', 'a.no_box')
            ->leftJoin('kelas_cetak as d', 'd.id_kelas_cetak', '=', 'a.id_kelas')
            ->leftJoin('pengiriman_gradingbj as p', 'a.no_box', '=', 'p.no_box')
            ->where('a.selesai', '=', 'Y')
            ->whereNull('p.no_box')
            ->groupBy('a.no_box')
            ->orderBy('b.tipe', 'ASC')
            ->get();





        $tblBk = DB::table('pengiriman_gradingbj')->pluck('no_box')->toArray();
        $response = Http::get("https://gudangsarang.ptagafood.com/api/apibk/bkSortirApi");
        $data = json_decode($response->getBody());

        $data = array_filter($data, function ($item) use ($tblBk) {
            // Mengembalikan false jika no_box ada di dalam $tblBk
            return !in_array($item->no_box, $tblBk);
        });

        $cabut_selesai = array_values($data);

        // if (!$cetak && !$cabut_selesai) {
        //     return redirect()->route('gradingbj.history_ambil')->with('error', 'Data Cetak Masih tidak ada !');
        // }

        $data = [
            'title' => 'Tambah Grading BJ',
            'cetak' => $cetak,
            'cabut_selesai' => $cabut_selesai

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
                    'tipe' => '1',
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
        $boxKecil = DB::select("SELECT 
                    a.no_box,
                    a.grade,
                    a.pcs_kredit as pcs,
                     a.gr_kredit as gr,
                    a.rp_gram_kredit as rp_gram,
                    a.pengawas,
                    a.no_grading,
                    b.pcs as pcs_sortir,
                    b.gr as gr_sortir,
                    b.ttl_rp as ttlrp_sortir, b.name
                    FROM `pengiriman_list_gradingbj` as a 
                    LEFT JOIN (
                        SELECT no_box,sum(pcs_akhir) as pcs, sum(gr_akhir) as gr, sum(ttl_rp) as ttl_rp, b.name
                        FROM `sortir` 
                        left join users as b on b.id = sortir.id_pengawas 
                        WHERE selesai = 'Y'
                        GROUP BY no_box
                    ) as b on a.no_box = b.no_box
                    WHERE a.no_box is not null ");
        $data = [
            'title' => 'History Box Kecil',
            'box_kecil' => $boxKecil
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
}
