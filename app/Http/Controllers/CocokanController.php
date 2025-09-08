<?php

namespace App\Http\Controllers;

use App\Models\BalanceModel;
use Illuminate\Http\Request;
use App\Models\CocokanModel;
use App\Models\Grading;
use App\Models\OpnameNewModel;
use App\Models\SummaryModel;
use Illuminate\Support\Facades\DB;
use stdClass;
use Illuminate\Support\Facades\Http;

class CocokanController extends Controller
{
    public function index(CocokanModel $model)
    {
        $bk = Http::get("https://gudangsarang.ptagafood.com/api/apibk/sum_partai");
        $bk = json_decode($bk, TRUE);
        DB::table('bk_awal')->truncate();
        foreach ($bk as $v) {
            $data = [
                'nm_partai' => $v['ket2'],
                'nm_partai_dulu' => $v['ket'],
                'pcs' => $v['pcs'] ?? 0,
                'gr' => $v['gr'],
                'grade' => $v['nm_grade'],
                'ttl_rp' => $v['total_rp'],
                'bulan' => date('m', strtotime($v['tgl'])),
                'tahun' => date('Y', strtotime($v['tgl'])),
                'pcs_susut' => $v['pcs_susut'],
                'gr_susut' => $v['gr_susut'],
            ];
            DB::table('bk_awal')->insert($data);
        }
        $a11 = $model::bkstockawal_sum();
        $a11suntik = $this->getSuntikan(11);
        $bk_awal = new stdClass();
        $bk_awal->pcs = $a11->pcs + $a11suntik->pcs;
        $bk_awal->gr = $a11->gr + $a11suntik->gr;
        $bk_awal->ttl_rp = $a11->ttl_rp + $a11suntik->ttl_rp;
        // akhir
        $a14suntik = $this->getSuntikan(14);
        $a16suntik = $this->getSuntikan(16);
        $a12 = $model::bkselesai_siap_ctk_diserahkan_sum();

        $bk_akhir = new stdClass();
        $bk_akhir->pcs = $a12->pcs + $a14suntik->pcs + $a16suntik->pcs;
        $bk_akhir->gr = $a12->gr + $a14suntik->gr + $a16suntik->gr;
        $bk_akhir->ttl_rp = $a12->ttl_rp + $a14suntik->ttl_rp + $a16suntik->ttl_rp;
        $bk_akhir->cost_kerja = $a12->cost_kerja;

        $ttl_gr = $this->getCost($model, 'ttl_gr');
        $cost_op = $this->getCost($model, 'cost_op');
        $cost_dll = $this->getCost($model, 'dll');

        $data = [
            'title' => 'Cabut',
            'bk_awal' => $bk_awal,
            'cbt_proses' => $model::bksedang_proses_sum(),
            'cbt_sisa_pgws' => $model::bksisapgws(),
            'bk_akhir' => $bk_akhir,
            'ttl_gr' => $ttl_gr,
            'cost_op' => $cost_op,
            'cost_dll' => $cost_dll

        ];
        return view('home.cocokan.index', $data);
    }
    public function cetak(CocokanModel $model)
    {
        $ca11 = $this->getSuntikan(21);
        $ctk_opname = new stdClass();
        $ctk_opname->pcs = $ca11->pcs;
        $ctk_opname->gr = $ca11->gr;
        $ctk_opname->ttl_rp = $ca11->ttl_rp;

        $ca2 = $model::cetak_stok_awal();

        $ca12suntik = $this->getSuntikan(23);
        $akhir_cbt = new stdClass();
        $akhir_cbt->pcs = $ca2->pcs + $ca12suntik->pcs;
        $akhir_cbt->gr = $ca2->gr + $ca12suntik->gr;
        $akhir_cbt->ttl_rp = $ca2->ttl_rp + $ca12suntik->ttl_rp + $ca2->cost_kerja;



        $ca17 = $model::cetak_stok();
        $ca17suntik = $this->getSuntikan(27);


        $cetak_sisa = new stdClass();
        $cetak_sisa->pcs = $ca17->pcs + $ca17suntik->pcs;
        $cetak_sisa->gr = $ca17->gr + $ca17suntik->gr;
        $cetak_sisa->ttl_rp = $ca17->ttl_rp + $ca17suntik->ttl_rp;

        $ca16suntik = $this->getSuntikan(26);
        $ca16 = $model::cetak_selesai();
        $cetak_akhir = new stdClass();
        $cetak_akhir->pcs = $ca16->pcs + $ca16suntik->pcs;
        $cetak_akhir->gr = $ca16->gr + $ca16suntik->gr;
        $cetak_akhir->ttl_rp = $ca16->ttl_rp + $ca16suntik->ttl_rp;
        $cetak_akhir->cost_kerja = $ca16->cost_kerja;

        $ttl_gr = $this->getCost($model, 'ttl_gr');
        $cost_op = $this->getCost($model, 'cost_op');
        $cost_dll = $this->getCost($model, 'dll');
        $proses = $model::cetak_proses();
        $data = [

            'title' => 'Cetak',
            'ctk_opname' => $ctk_opname,
            'akhir_cbt' => $akhir_cbt,
            'cetak_proses' => $proses,
            'cetak_sisa' => $cetak_sisa,
            'cetak_akhir' => $cetak_akhir,
            'ttl_gr' => $ttl_gr,
            'cost_op' => $cost_op,
            'cost_dll' => $cost_dll

        ];
        return view('home.cocokan.cetak', $data);
    }
    public function sortir(CocokanModel $model)
    {
        $s1 = $model::stock_sortir_awal();
        $s2suntik = $this->getSuntikan(32);
        $akhir_cetak = new stdClass();
        $akhir_cetak->pcs = $s1->pcs + $s2suntik->pcs;
        $akhir_cetak->gr = $s1->gr + $s2suntik->gr;
        $akhir_cetak->ttl_rp = $s1->ttl_rp + $s2suntik->ttl_rp;

        $s3 = $model::sortir_akhir();

        $s5suntik = $this->getSuntikan(35);

        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
        $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp;
        $sortir_akhir->cost_kerja = $s3->cost_kerja;

        $ttl_gr = $this->getCost($model, 'ttl_gr');
        $cost_op = $this->getCost($model, 'cost_op');
        $cost_dll = $this->getCost($model, 'dll');
        $opname = $this->getSuntikan(31);
        $sedang_proses = $model::sortir_proses();
        $sortir_sisa = $model::stock_sortir();
        $data = [
            'title' => 'Sortir ',
            'opname' => $opname,
            'akhir_cetak' => $akhir_cetak,
            'sedang_proses' => $sedang_proses,
            'sortir_sisa' => $sortir_sisa,
            'sortir_akhir' => $sortir_akhir,
            'ttl_gr' => $ttl_gr,
            'cost_op' => $cost_op,
            'cost_dll' => $cost_dll

        ];
        return view('home.cocokan.sortir', $data);
    }
    public function grading(CocokanModel $model)
    {
        $s3 = $model::sortir_akhir();

        $s5suntik = $this->getSuntikan(35);

        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
        $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp + $s3->cost_kerja;
        $sortir_akhir->cost_bk = $s3->cost_bk;


        // $pengiriman = DB::selectOne("SELECT sum(b.pcs) as pcs, sum(b.gr) as gr FROM pengiriman as a
        //     JOIN grading_partai as b on a.no_box = b.box_pengiriman");

        $grading = $model->gradingOne();
        $grading_proses = $model->gradingProsesOne();

        $grading_sisa = $model->gradingSisaOne();

        $sumTtlRpPengiriman = DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp FROM pengiriman as a ");

        $data = [
            'title' => 'Grading ',
            'opname' =>  $this->getSuntikan(41),
            'sortir_akhir' => $sortir_akhir,
            // 'pengiriman' => $pengiriman,
            'sumTtlRpPengiriman' => $sumTtlRpPengiriman,
            'grading' => $grading,
            'grading_proses' => $grading_proses,
            'grading_sisa' => $grading_sisa,
        ];
        return view('home.cocokan.grading', $data);
    }

    public function wip1(CocokanModel $model)
    {
        $data = [
            'title' => 'WIP 1',
            'grading'  =>  $model->gradingOne(),
            'sisa_belum_wip1' => $model->sisa_belum_wip1(),
            'wip1akhir' => $model->wip1_akhir(),
        ];
        return view('home.cocokan.wip1', $data);
    }
    public function qc(CocokanModel $model)
    {
        $data = [
            'title' => 'Qc',
            'wip1akhir' => $model->wip1_akhir(),
            'sisa_belum_qc' => $model->sisa_belum_qc(),
            'qc_akhir' => $model->qc_akhir(),
        ];
        return view('home.cocokan.qc', $data);
    }
    public function wip2(CocokanModel $model)
    {
        $data = [
            'title' => 'WIP2',
            'qc_akhir' => $model->qc_akhir(),
            'wip2proses' => $model->wip2proses(),
            'wip2akhir' => $model->wip2akhir()

        ];
        return view('home.cocokan.wip2', $data);
    }


    public function pengiriman(CocokanModel $model)
    {
        $sa = $model::sortir_akhir();
        $p2suntik = $this->getSuntikan(42);
        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $sa->pcs + $p2suntik->pcs;
        $sortir_akhir->gr = $sa->gr + $p2suntik->gr;
        $sortir_akhir->ttl_rp = $sa->ttl_rp + $p2suntik->ttl_rp;

        $pengiriman = Grading::pengirimanSum();

        $grading = $model->gradingOne();
        $grading_sisa = DB::selectOne("SELECT a.no_box_sortir, sum(b.pcs_awal - d.pcs) as pcs , sum(b.gr_awal - d.gr) as gr FROM grading as a left join formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade' JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'cabut' LEFT JOIN( select no_box_sortir as no_box,sum(pcs) as pcs,sum(gr) as gr from grading group by no_box_sortir ) as d on d.no_box = a.no_box_sortir WHERE a.selesai = 'T';");

        $sumTtlRpPengiriman = DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp FROM pengiriman as a ");

        $belum_kirim = Grading::belumKirimSum();

        $list_pengiriman = Grading::list_pengiriman_sum2();
        $list_pengiriman_belum = Grading::list_pengiriman_sum_belum();

        $data = [
            'title' => 'Pengiriman ',
            'opname' =>  $this->getSuntikan(41),
            'sortir_akhir' => $sortir_akhir,
            'pengiriman' => $pengiriman,
            'sumTtlRpPengiriman' => $sumTtlRpPengiriman,
            'grading' => $grading,
            'grading_sisa' => $grading_sisa,
            'belum_kirim' => $belum_kirim,
            'list_pengiriman' => $list_pengiriman,
            'list_pengiriman_belum' => $list_pengiriman_belum,
            'wip2akhir' => $model->wip2akhir(),
            'pengiriman_proses' => $model->pengiriman_proses(),
        ];
        return view('home.cocokan.pengiriman', $data);
    }

    public function getSuntikan($index)
    {
        $datas = [
            11 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_cbt_awal'"),
            14  => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_siap_cetak_diserahkan'"),
            16  => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_eo_diserahkan'"),
            26 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_serah'"),
            21 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_awal_stock' and opname = 'Y'"),
            22 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_awal_stock' "),
            23 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_awal_stock' and opname = 'T'"),
            24 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_selesai_siap_sortir_diserahkan' and opname = 'T'"),
            27 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_sisa'"),
            31 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_stok_awal' and opname = 'Y'"),
            32 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_stok_awal' and opname = 'T'"),
            35 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_selesai_diserahkan'"),
            41 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'grading' and opname = 'Y'"),
            42 => DB::selectOne("SELECT sum(pcs) as pcs, sum(gr) as gr, sum(ttl_rp) as ttl_rp FROM `opname_suntik` WHERE ket ='grading' and opname = 'T';"),
            // 43 => DB::selectOne("SELECT sum(pcs) as pcs, sum(gr) as gr, sum(ttl_rp) as ttl_rp FROM `opname_suntik` WHERE ket ='cetak_selesai' and opname = 'T';"),
        ];
        if (array_key_exists($index, $datas)) {
            return $datas[$index];
        } else {
            return false;
        }
    }

    public function getCost(CocokanModel $model, $index)
    {
        $a14suntik = $this->getSuntikan(14);
        $a16suntik = $this->getSuntikan(16);
        $a12 = $model::bkselesai_siap_ctk_diserahkan_sum();

        $bk_akhir = new stdClass();
        $bk_akhir->pcs = $a12->pcs + $a14suntik->pcs + $a16suntik->pcs;
        $bk_akhir->gr = $a12->gr + $a14suntik->gr + $a16suntik->gr;
        $bk_akhir->ttl_rp = $a12->ttl_rp + $a14suntik->ttl_rp + $a16suntik->ttl_rp;
        $bk_akhir->cost_kerja = $a12->cost_kerja;

        $ca16suntik = $this->getSuntikan(26);
        $ca16 = $model::cetak_selesai();
        $cetak_akhir = new stdClass();
        $cetak_akhir->pcs = $ca16->pcs + $ca16suntik->pcs;
        $cetak_akhir->gr = $ca16->gr + $ca16suntik->gr;
        $cetak_akhir->ttl_rp = $ca16->ttl_rp + $ca16suntik->ttl_rp;
        $cetak_akhir->cost_kerja = $ca16->cost_kerja;


        $s3 = $model::sortir_akhir();
        $s5suntik = $this->getSuntikan(35);

        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
        $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp;

        $gr_akhir_all = $a12->gr + $a14suntik->gr + $a16suntik->gr + $ca16->gr + $ca16suntik->gr + $s3->gr + $s5suntik->gr;
        $ttl_cost_kerja = $a12->cost_kerja  +  $ca16->cost_kerja +  $s3->cost_kerja;



        $uang_cost = BalanceModel::uangCost();
        $ttl_cost_op = sumBk($uang_cost, 'total_operasional');


        $cost_dll = DB::selectOne("SELECT sum(`dll`) as dll, max(bulan_dibayar) as bulan FROM `tb_gaji_penutup`");
        $bulan = $cost_dll->bulan;
        $cost_cu = DB::selectOne("SELECT sum(a.ttl_rp) as cost_cu
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori ='CU' and a.bulan_dibayar BETWEEN '6' and '$bulan';");
        $denda = DB::selectOne("SELECT sum(`nominal`) as ttl_denda FROM `tb_denda` WHERE `bulan_dibayar` BETWEEN '6' and '$bulan';");

        $ttl_semua = $ttl_cost_kerja + $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda;
        $dll = $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda;
        $cost_op = $ttl_cost_op - $ttl_semua;


        $datas = [
            1 => $ttl_cost_kerja,
            'ttl_gr' => $gr_akhir_all,
            'dll' => $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda,
            'cost_op' => $ttl_cost_op - $ttl_semua
        ];
        if (array_key_exists($index, $datas)) {
            return $datas[$index];
        } else {
            return false;
        }
    }

    public function balancesheet()
    {
        $bk = Http::get("https://gudangsarang.ptagafood.com/api/apibk/sum_partai");
        $bk = json_decode($bk, TRUE);
        DB::table('bk_awal')->truncate();
        foreach ($bk as $v) {
            $data = [
                'nm_partai' => $v['ket2'],
                'nm_partai_dulu' => $v['ket'],
                'pcs' => $v['pcs'] ?? 0,
                'gr' => $v['gr'],
                'grade' => $v['nm_grade'],
                'ttl_rp' => $v['total_rp'],
                'bulan' => date('m', strtotime($v['tgl'])),
                'tahun' => date('Y', strtotime($v['tgl'])),
                'pcs_susut' => $v['pcs_susut'],
                'gr_susut' => $v['gr_susut'],
            ];
            DB::table('bk_awal')->insert($data);
        }

        $ca17 = CocokanModel::cetak_stok_balance();
        $ca17suntik = $this->getSuntikan(27);

        $cetak_sisa = new stdClass();
        $cetak_sisa->pcs = $ca17->pcs + $ca17suntik->pcs;
        $cetak_sisa->gr = $ca17->gr + $ca17suntik->gr;
        $cetak_sisa->modal = $ca17->ttl_rp;
        $cetak_sisa->ttl_rp = $ca17->ttl_rp + $ca17suntik->ttl_rp + $ca17->cost_kerja;


        // $sa = CocokanModel::akhir_sortir();
        // $p2suntik = $this->getSuntikan(42);
        // $sortir_akhir = new stdClass();
        // $sortir_akhir->pcs = $sa->pcs + $p2suntik->pcs;
        // $sortir_akhir->gr = $sa->gr + $p2suntik->gr;
        // $sortir_akhir->ttl_rp = $sa->ttl_rp + $p2suntik->ttl_rp;


        $pengiriman = Grading::pengirimanSum();
        $grading = Grading::belumKirimSum();
        $grading_susut = Grading::belumKirimSumsusut();

        $a14suntik = $this->getSuntikan(14);
        $a16suntik = $this->getSuntikan(16);
        $a12 = CocokanModel::bkselesai_siap_ctk_diserahkan_sum();

        $bk_akhir = new stdClass();
        $bk_akhir->pcs = $a12->pcs + $a14suntik->pcs + $a16suntik->pcs;
        $bk_akhir->gr = $a12->gr + $a14suntik->gr + $a16suntik->gr;
        $bk_akhir->ttl_rp = $a12->ttl_rp + $a14suntik->ttl_rp + $a16suntik->ttl_rp;
        $bk_akhir->cost_kerja = $a12->cost_kerja;

        $model = new CocokanModel();
        $ttl_gr = $this->getCost($model, 'ttl_gr');
        $cost_op = $this->getCost($model, 'cost_op');
        $cost_dll = $this->getCost($model, 'dll');

        $ca16suntik = $this->getSuntikan(26);
        $ca16 = $model::cetak_selesai();
        $cetak_akhir = new stdClass();
        $cetak_akhir->pcs = $ca16->pcs + $ca16suntik->pcs;
        $cetak_akhir->gr = $ca16->gr + $ca16suntik->gr;
        $cetak_akhir->ttl_rp = $ca16->ttl_rp + $ca16suntik->ttl_rp;
        $cetak_akhir->cost_kerja = $ca16->cost_kerja;

        $s3 = $model::sortir_akhir();
        $s5suntik = $this->getSuntikan(35);

        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
        $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp;
        $sortir_akhir->cost_kerja = $s3->cost_kerja;

        $grading_akhir =  DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp,sum(a.pcs) as pcs, sum(a.gr) as gr ,
        sum(a.cost_bk) as cost_bk, sum(a.cost_kerja) as cost_kerja, sum(a.cost_cu) as cost_cu, sum(a.cost_op) as cost_op
        FROM grading_partai as a ");

        $grading_proses = $model->gradingProsesOne();

        $data = [
            'title' => 'Balance Sheet ',
            'bk' => SummaryModel::summarybk(),
            'bk_suntik' => DB::select("SELECT * FROM opname_suntik WHERE opname = 'Y'"),
            'uang_cost' => BalanceModel::uangCost(),
            'bk_akhir' => $bk_akhir,
            'cbt_proses' => CocokanModel::bksedang_proses_sum(),
            'cbt_sisa_pgws' => CocokanModel::bksisapgws(),
            'cetak_proses' => CocokanModel::cetak_proses_balance(),
            'cbt_blm_kirim' => CocokanModel::bksedang_selesai_sum(),
            'cetak_sisa' => $cetak_sisa,
            'sedang_proses' => CocokanModel::sortir_proses_balance(),
            'sortir_sisa' => CocokanModel::sortir_stock_balance(),
            'opname' =>  $this->getSuntikan(41),
            'sortir_akhir' => $sortir_akhir,
            'pengiriman' => $pengiriman,
            'grading' => $grading,
            'ttl_gr' => $ttl_gr,
            'cost_op' => $cost_op,
            'cost_dll' => $cost_dll,
            'cetak_akhir'  => $cetak_akhir,
            'grading_sisa' => CocokanModel::gradingSisaOne(),
            'grading_sisa2' => OpnameNewModel::grading_sisa(),
            'grading_akhir' => $grading_akhir,
            'cabut_selesai_siap_cetak' => OpnameNewModel::bksedang_selesai_sum(),
            'cetak_selesai' => OpnameNewModel::cetak_selesai(),
            'sortir_selesai' => OpnameNewModel::sortir_selesai(),
            'grading_susut' => $grading_susut,
            'sisa_belum_wip1' => $model->sisa_belum_wip1(),
            'sisa_belum_qc' => $model->sisa_belum_qc(),
            'wip2proses' => $model->wip2proses(),
            'pengiriman_proses' => $model->pengiriman_proses(),
            'grading_proses' => $grading_proses,


        ];
        return view('home.cocokan.balance', $data);
    }

    public function cek_cocokan()
    {
        $data = [
            'cbt_proses' => CocokanModel::bksedang_proses_sum(),
            'cbt_sisa_pgws' => CocokanModel::bksisapgws(),
            'cabut_selesai_siap_cetak' => OpnameNewModel::bksedang_selesai_sum(),
        ];
        return view('home.cocokan.ceck_cocokan', $data);
    }

    public function tutup()
    {

        try {
            DB::beginTransaction();
            $tgl_ditutup = now();
            $bulan_ditutup = date('m');
            $tahun_ditutup = date('Y');
            $admin = auth()->user()->name;

            $cekBkKerjaTutup = DB::table('history_bk_kerja')->where([['bulan_ditutup', $bulan_ditutup], ['tahun_ditutup', $tahun_ditutup]])->exists();
            $cekCostTutup = DB::table('history_cost_perbulan')->where([['bulan_ditutup', $bulan_ditutup], ['tahun_ditutup', $tahun_ditutup]])->exists();
            $cekBkRpTutup = DB::table('history_bk_rp')->where([['bulan_ditutup', $bulan_ditutup], ['tahun_ditutup', $tahun_ditutup]])->exists();

            if ($cekBkKerjaTutup && $cekCostTutup && $cekBkRpTutup) {
                return redirect()->back()->with('error', 'Data sudah ditutup');
            }


            $model = new CocokanModel();
            $s3 = $model::sortir_akhir();
            $s5suntik = $this->getSuntikan(35);

            $sortir_akhir = new stdClass();
            $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
            $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
            $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp;
            $sortir_akhir->cost_kerja = $s3->cost_kerja;
            $opname =  $this->getSuntikan(41);
            $sortir_akhir = $sortir_akhir;

            $grading = Grading::belumKirimSum();
            $pengiriman = Grading::pengirimanSum();
            $grading_sisa = CocokanModel::gradingSisaOne();
            $cbt_proses = CocokanModel::bksedang_proses_sum();
            $cbt_sisa_pgws = CocokanModel::bksisapgws();
            $cetak_proses = CocokanModel::cetak_proses_balance();
            $cbt_blm_kirim = CocokanModel::bksedang_selesai_sum();
            $ca17 = CocokanModel::cetak_stok_balance();
            $ca17suntik = $this->getSuntikan(27);
            $cetak_sisa = new stdClass();
            $cetak_sisa->ttl_rp = $ca17->ttl_rp + $ca17suntik->ttl_rp + $ca17->cost_kerja;

            $cetak_sisa = $cetak_sisa;
            $sedang_proses = CocokanModel::sortir_proses_balance();
            $sortir_sisa = CocokanModel::sortir_stock_balance();

            $cabut_selesai_siap_cetak = OpnameNewModel::bksedang_selesai_sum();
            $cetak_selesai = OpnameNewModel::cetak_selesai();
            $sortir_selesai = OpnameNewModel::sortir_selesai();
            $grading_susut = Grading::belumKirimSumsusut();

            $bk = SummaryModel::summarybk();
            $bk_suntik = DB::select("SELECT * FROM opname_suntik WHERE opname = 'Y'");

            $ttl_sisa_belum_kirim =
                $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op;

            $ttl_pengiriman =
                $pengiriman->cost_bk +
                $pengiriman->cost_kerja +
                $pengiriman->cost_cu +
                $pengiriman->cost_op;

            $ttl_sisa_blum_grading = $grading_sisa->cost_bk;

            $ttl_cost_berjalan =
                $cbt_proses->ttl_rp +
                $cbt_sisa_pgws->ttl_rp +
                $cetak_proses->ttl_rp +
                $cetak_proses->cost_kerja +
                $cbt_blm_kirim->cost_kerja +
                $cetak_sisa->ttl_rp +
                $sedang_proses->ttl_rp +
                $sedang_proses->cost_kerja +
                $sortir_sisa->ttl_rp +
                $sortir_sisa->cost_kerja +
                $ttl_sisa_blum_grading +
                $ttl_pengiriman +
                $ttl_sisa_belum_kirim +
                sumBk($cabut_selesai_siap_cetak, 'ttl_rp') +
                sumBk($sortir_selesai, 'ttl_rp') +
                sumBk($cetak_selesai, 'ttl_rp') +
                sumBk($cabut_selesai_siap_cetak, 'cost_kerja') +
                sumBk($sortir_selesai, 'cost_kerja') +
                sumBk($cetak_selesai, 'cost_kerja') +
                $grading_susut->cost_bk +
                $grading_susut->cost_kerja +
                $grading_susut->cost_cu +
                $grading_susut->cost_op;


            if (!$cekBkKerjaTutup) {
                $bk_sinta = SummaryModel::summarybk();
                foreach ($bk_sinta as $b) {

                    $pcs_susut = is_null($b->pcs_susut) ? 'belum selesai' : $b->pcs_susut;
                    $gr_susut = is_null($b->gr_susut) ? 'belum selesai' : $b->gr_susut;
                    $susut_persen = is_null($b->pcs_susut) ? 'belum selesai' : (1 - ($b->gr / $b->gr_bk)) * 100;

                    $pcs_sinta = $pcs_susut == 'belum selesai' ? $b->pcs -  $b->pcs_bk : 0;
                    $gr_sinta = $gr_susut == 'belum selesai' ? $b->gr -  $b->gr_bk : 0;
                    $ttl_rp_sinta = $susut_persen == 'belum selesai' ? $b->ttl_rp - $b->cost_bk : 0;

                    $data[] = [
                        'bulan_kerja' => date('F Y', strtotime('01-' . $b->bulan . '-' . $b->tahun)),
                        'nm_partai' => $b->nm_partai,
                        'grade' => $b->grade,
                        'pcs_bk' => $b->pcs,
                        'gr_bk' => $b->gr,
                        'ttl_rp_bk' => $b->ttl_rp,
                        'rata_rata_bk' => empty($b->gr) ? 0 : $b->ttl_rp / $b->gr,
                        'pcs_diambil' => $b->pcs_bk,
                        'gr_diambil' => $b->gr_bk,
                        'ttl_rp_diambil' => $b->cost_bk,
                        'rata_rata_diambil' => $b->cost_bk / $b->gr_bk,
                        'pcs_susut' => $pcs_susut,
                        'gr_susut' => $gr_susut,
                        'susut_persen' => $susut_persen,
                        'pcs_sinta' => $pcs_sinta,
                        'gr_sinta' => $gr_sinta,
                        'ttl_rp_sinta' => $susut_persen == 'belum selesai' ? $b->ttl_rp - $b->cost_bk : 0,
                        'rata_rata_sinta' => $pcs_susut == 'belum selesai' ? $ttl_rp_sinta / $gr_sinta : 0,
                        'tgl_ditutup' => $tgl_ditutup,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'admin' => $admin,
                    ];
                }
                DB::table('history_bk_kerja')->insert($data);
            }

            if (!$cekCostTutup) {
                $uangCost = BalanceModel::uangCost();
                foreach ($uangCost as $u) {
                    $data2[] = [
                        'bulan_tahun' => date('F Y', strtotime($u->tahun . '-' . $u->bulan . '-' . '01')),
                        'gaji' => $u->gaji,
                        'cost_op' => $u->total_operasional - $u->gaji,
                        'ttl_rp' => $u->total_operasional,
                        'tgl_ditutup' => $tgl_ditutup,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'admin' => $admin,
                    ];
                }

                $data2[] = [
                    'bulan_tahun' => 'cost berjalan',
                    'gaji' => 0,
                    'cost_op' => 0,
                    'ttl_rp' => $ttl_cost_berjalan -
                        sumBk($uangCost, 'total_operasional') -
                        sumBk($bk, 'cost_bk') -
                        sumBk($bk_suntik, 'ttl_rp'),
                    'tgl_ditutup' => $tgl_ditutup,
                    'bulan_ditutup' => $bulan_ditutup,
                    'tahun_ditutup' => $tahun_ditutup,
                    'admin' => $admin,
                ];
                DB::table('history_cost_perbulan')->insert($data2);
            }
            if (!$cekBkRpTutup) {
                $data3 = [
                    [
                        'ket' => 'cabut sedang proses',
                        'pcs' => $cbt_proses->pcs ?? 0,
                        'gr' => $cbt_proses->gr ?? 0,
                        'ttl_rp' => $cbt_proses->ttl_rp,
                        'rata_rata' => $cbt_proses->ttl_rp / $cbt_proses->pcs,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Cabut sisa pengawas',
                        'pcs' => $cbt_sisa_pgws->pcs ?? 0,
                        'gr' => $cbt_sisa_pgws->gr ?? 0,
                        'ttl_rp' => $cbt_sisa_pgws->ttl_rp,
                        'rata_rata' => $cbt_sisa_pgws->ttl_rp / $cbt_sisa_pgws->gr,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Cabut selesai siap cetak belum kirim',
                        'pcs' => sumBk($cabut_selesai_siap_cetak, 'pcs'),
                        'gr' => sumBk($cabut_selesai_siap_cetak, 'gr'),
                        'ttl_rp' => sumBk($cabut_selesai_siap_cetak, 'ttl_rp') + sumBk($cabut_selesai_siap_cetak, 'cost_kerja'),
                        'rata_rata' => 0,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Cetak sedang Proses',
                        'pcs' => $cetak_proses->pcs ?? 0,
                        'gr' => $cetak_proses->gr ?? 0,
                        'ttl_rp' => $cetak_proses->ttl_rp ?? (0 + $cetak_proses->cost_kerja ?? 0),
                        'rata_rata' => empty($cetak_proses->gr) ? 0 : ($cetak_proses->ttl_rp + $cetak_proses->cost_kerja) / $cetak_proses->gr,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Cetak sisa Pengawas',
                        'pcs' => $cetak_sisa->pcs ?? 0,
                        'gr' => $cetak_sisa->gr ?? 0,
                        'ttl_rp' => $cetak_sisa->ttl_rp ?? 0,
                        'rata_rata' => empty($cetak_sisa->gr) ? 0 : $cetak_sisa->ttl_rp / $cetak_sisa->gr,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Cetak selesai siap sortir belum kirim',
                        'pcs' => sumBk($cetak_selesai, 'pcs'),
                        'gr' => sumBk($cetak_selesai, 'gr'),
                        'ttl_rp' => sumBk($cetak_selesai, 'ttl_rp') + sumBk($cetak_selesai, 'cost_kerja'),
                        'rata_rata' => 0,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Sortir sedang Proses',
                        'pcs' => $sedang_proses->pcs ?? 0,
                        'gr' => $sedang_proses->gr ?? 0,
                        'ttl_rp' => $sedang_proses->ttl_rp + $sedang_proses->cost_kerja,
                        'rata_rata' => 0,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Sortir sisa Pengawas',
                        'pcs' => $sortir_sisa->pcs ?? 0,
                        'gr' => $sortir_sisa->gr ?? 0,
                        'ttl_rp' => $sortir_sisa->ttl_rp + $sortir_sisa->cost_kerja,
                        'rata_rata' => ($sortir_sisa->ttl_rp + $sortir_sisa->cost_kerja) / $sortir_sisa->gr,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Sortir selesai siap grading belum kirim',
                        'pcs' => sumBk($sortir_selesai, 'pcs'),
                        'gr' => sumBk($sortir_selesai, 'gr'),
                        'ttl_rp' => sumBk($sortir_selesai, 'ttl_rp') + sumBk($sortir_selesai, 'cost_kerja'),
                        'rata_rata' => 0,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Sisa belum grading',
                        'pcs' => $grading_sisa->pcs ?? 0,
                        'gr' => $grading_sisa->gr ?? 0,
                        'ttl_rp' => $grading_sisa->cost_bk,
                        'rata_rata' => empty($grading_sisa->gr) ? 0 : $grading_sisa->cost_bk / $grading_sisa->gr,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Pengiriman',
                        'pcs' => $pengiriman->pcs ?? 0,
                        'gr' => $pengiriman->gr ?? 0,
                        'ttl_rp' => $pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op,
                        'rata_rata' => ($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op) / $pengiriman->gr,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Sisa belum kirim ( sisa + qc)',
                        'pcs' => $grading->pcs ?? 0,
                        'gr' => $grading->gr ?? 0,
                        'ttl_rp' => $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op + $grading_susut->cost_bk + $grading_susut->cost_kerja + $grading_susut->cost_cu + $grading_susut->cost_op,
                        'rata_rata' => ($grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op + $grading_susut->cost_bk + $grading_susut->cost_kerja + $grading_susut->cost_cu + $grading_susut->cost_op) / $grading->gr,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                    [
                        'ket' => 'Selisih',
                        'pcs' => $sortir_akhir->pcs + $opname->pcs - $grading->pcs - $pengiriman->pcs - ($grading_sisa->pcs ?? 0),
                        'gr' => 0,
                        'ttl_rp' => 0,
                        'rata_rata' => 0,
                        'bulan_ditutup' => $bulan_ditutup,
                        'tahun_ditutup' => $tahun_ditutup,
                        'tgl_ditutup' => $tgl_ditutup,
                        'admin' => $admin,
                    ],
                ];

                DB::table('history_bk_rp')->insert($data3);
            }

            DB::commit();
            return redirect()->back()->with('sukses', 'Berhasil tutup');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function opname(Request $r)
    {
        $data = [
            'title' => 'Opname',
            'cbt_proses' => CocokanModel::bksedang_proses_sum(),
        ];
        return view('home.cocokan.opname', $data);
    }

    public function list_pengiriman(Request $r)
    {
        $query = Grading::list_pengiriman_sum();

        $data = [
            'title' => 'List Pengiriman',
            'query' => $query,
        ];
        return view('home.cocokan.list_pengiriman', $data);
    }

    public function detailCabutProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::bksedang_proses_sum(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCabutSisa(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::bksisapgws(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCabutBelumKirim(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::bkselesai_belum_kirim(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCetakSedangProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::cetak_proses(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCetakSisa(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::cetak_stok(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCetakBelumKirim(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::cetak_selesai(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailSortirProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::sortir_proses(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailSortirSisa(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::sortir_stock(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailSortirBelumKirim(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::sortir_selesai(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailSisaBelumGrading(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::gradingSisa(),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailGradingSedangProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::gradingSednagProses(),
        ];
        return view('home.cocokan.balance.detailgradingProses', $data);
    }
    public function detailWip1SedangProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::wip1SedangProses(),
        ];
        return view('home.cocokan.balance.detailgradingProses', $data);
    }
    public function detailQcSedangProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::qcSedangProses(),
        ];
        return view('home.cocokan.balance.detailgradingProses', $data);
    }
    public function detailWip2SedangProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::wip2SedangProses(),
        ];
        return view('home.cocokan.balance.detailgradingProses', $data);
    }
    public function detailPengirimanSedangProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::PengirimanSedangProses(),
        ];
        return view('home.cocokan.balance.detailgradingProses', $data);
    }
    public function detailPengiriman(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $model::Pengiriman(),
        ];
        return view('home.cocokan.balance.detailgradingProses', $data);
    }
    public function susutgrading(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'grading' => $model::Grading_susut(),
        ];
        return view('home.cocokan.susutgrading', $data);
    }

    public function Susut()
    {
        $bk = DB::select("SELECT a.nm_partai, a.tipe, sum(a.pcs_awal) as pcs , sum(a.gr_awal) as gr, sum(a.gr_awal * a.hrga_satuan) as modal_awal , b.pcs_akhir, b.gr_akhir, b.cost_op, b.cost_kerja
        FROM bk as a  
        left join (
        SELECT sum(a.pcs) as pcs_akhir, sum(a.gr) as gr_akhir, sum(a.cost_bk) as cost_bk, sum(a.cost_op) as cost_op, sum(a.cost_kerja) as cost_kerja, a.nm_partai
        FROM grading_partai as a
        where a.grade != 'susut' 
        group by a.nm_partai
        )  as b on b.nm_partai = a.nm_partai
        where a.kategori = 'cabut' and a.baru = 'baru' and a.no_box != 9999 group by a.nm_partai order by a.nm_partai ASC;");
        $data = [
            'title' => 'Laporan Partai',
            'bk' => $bk,
        ];
        return view('home.cocokan.susut', $data);
    }
}
