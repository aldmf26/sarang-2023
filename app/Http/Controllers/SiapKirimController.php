<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiapKirimController extends Controller
{
    protected $nmTbl = 'siapkirim_grading';
    public function getDataMaster($jenis)
    {

        $arr = [
            'gradingbj' => DB::select("SELECT grade, sum(pcs) as pcs, sum(gr) as gr, sum(gr * rp_gram) as ttl_rp, sum(pcs_kredit) as pcs_kredit, sum(gr_kredit) as gr_kredit, sum(gr_kredit * rp_gram_kredit) as ttl_rp_kredit
                        FROM `siapkirim_list_grading` 
                        GROUP BY grade 
                        HAVING pcs - pcs_kredit <> 0 OR gr - gr_kredit <> 0"),
            'pengawas' => DB::table('users')->where('posisi_id', 13)->get()
        ];
        return $arr[$jenis];
    }
    public function index()
    {
        $data = [
            'title' => 'Siap Kirim',
            'datas' => DB::select("SELECT a.no_grading,sum(pcs_awal) as pcs_awal, sum(gr_awal) as gr_awal,a.tgl,a.partai,a.ket,count(a.no_box) as ttl_box , sum(a.ttl_rp + a.cost_sortir) as ttl_rp
            FROM `siapkirim_grading` as a
            GROUP BY no_grading ORDER BY a.no_grading DESC;"),
            'gudangbj' => $this->getDataMaster('gradingbj'),
        ];
        return view('home.siapkirim.index', $data);
    }

    public function add()
    {
        $sortir = DB::select("SELECT b.tipe,a.id_sortir,a.no_box,sum(a.pcs_akhir) as pcs_akhir,sum(a.gr_akhir) as gr_akhir, a.ttl_rp as cost_sortir, sum(c.rp_gram_kredit * c.gr_kredit) as ttl_rp
        FROM sortir as a
        join bk as b on a.no_box = b.no_box and b.kategori = 'sortir'
        LEFT JOIN pengiriman_list_gradingbj as c on a.no_box = c.no_box
        LEFT JOIN `siapkirim_grading` AS p ON a.no_box = p.no_box
        WHERE a.selesai = 'Y'  AND p.no_box IS NULL GROUP BY a.no_box ORDER BY b.tipe ASC;");

        if (!$sortir) {
            return redirect()->route('siapkirim.index')->with('error', 'Data Sortir Masih tidak ada !');
        }

        $data = [
            'title' => 'Tambah Siap Kirim',
            'sortir' => $sortir
        ];
        return view('home.siapkirim.add', $data);
    }
    public function create(Request $r)
    {
        try {
            DB::beginTransaction();
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
                    'cost_sortir' => $r->cost_sortir[$i],
                    'tipe' => $r->tipe[$i]
                ];
            }

            $db->insert($datas);
            DB::commit();

            return redirect()->route('siapkirim.index')->with('sukses', 'Data Berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('siapkirim.add')->with('error', $e->getMessage());
        }
    }

    public function getDetail($no_grading)
    {
        return $data = [
            'no_grading' => $no_grading,
            'tbGradeBentuk' => DB::table('tb_grade')->where('status', 'bentuk')->get(),
            'tbGradeTurun' => DB::table('tb_grade')->where('status', 'turun')->get(),
            'listGrading' => DB::table('siapkirim_list_grading')->where('no_grading', $no_grading)->get(),
            'box' => DB::table($this->nmTbl)->where('no_grading', $no_grading)->get(),
            'boxJudul' => DB::table($this->nmTbl)->where('no_grading', $no_grading)->first()
        ];
    }

    public function load_grading(Request $r)
    {
        return view('home.siapkirim.grading', $this->getDetail($r->no_grading));
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
            DB::table('siapkirim_list_grading')->insert($datas);
            DB::commit();
            return redirect()->route('siapkirim.index')->with('sukses', 'Berhasil tambah grading');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('siapkirim.index')->with('error', $e->getMessage());
        }
    }

    public function load_detail(Request $r)
    {
        return view('home.siapkirim.detail', $this->getDetail($r->no_grading));
    }
}
