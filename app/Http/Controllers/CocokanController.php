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

    public function zeroSuntik()
    {
        $obj = new stdClass();
        $obj->pcs = 0;
        $obj->gr = 0;
        $obj->ttl_rp = 0;
        return $obj;
    }

    public function getBalanceCost(CocokanModel $model, $index)
    {
        $a12 = $model::bkselesai_siap_ctk_diserahkan_sum();

        $ca16 = $model::cetak_selesai();
        $s3 = $model::sortir_akhir();

        $gr_akhir_all = $a12->gr + $ca16->gr + $s3->gr;
        $ttl_cost_kerja = $a12->cost_kerja + $ca16->cost_kerja + $s3->cost_kerja;

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

    private function calculateBalanceCost(array $data): array
    {
        $objectValue = static fn ($object, string $field): float =>
            (float) ($object->{$field} ?? 0);
        $arrayValue = static fn ($rows, string $field): float =>
            (float) sumBk($rows ?? [], $field);

        $totalBkRp =
            $objectValue($data['bk_sisa'], 'ttl_rp') +
            $objectValue($data['cbt_proses'], 'ttl_rp') +
            $objectValue($data['cbt_proses'], 'cost_kerja') +
            $objectValue($data['cbt_proses'], 'cost_op') +
            $objectValue($data['cbt_sisa_pgws'], 'ttl_rp') +
            $arrayValue($data['cabut_selesai_siap_cetak'], 'ttl_rp') +
            $arrayValue($data['cabut_selesai_siap_cetak'], 'cost_kerja') +
            $arrayValue($data['cabut_selesai_siap_cetak'], 'cost_op') +
            $objectValue($data['cetak_proses'], 'ttl_rp') +
            $objectValue($data['cetak_proses'], 'cost_kerja') +
            $objectValue($data['cetak_proses'], 'cost_op') +
            $objectValue($data['cetak_sisa'], 'ttl_rp') +
            $arrayValue($data['cetak_selesai'], 'ttl_rp') +
            $arrayValue($data['cetak_selesai'], 'cost_kerja') +
            $arrayValue($data['cetak_selesai'], 'cost_op') +
            $objectValue($data['sedang_proses'], 'ttl_rp') +
            $objectValue($data['sedang_proses'], 'cost_kerja') +
            $objectValue($data['sedang_proses'], 'cost_op') +
            $objectValue($data['sortir_sisa'], 'ttl_rp') +
            $objectValue($data['sortir_sisa'], 'cost_kerja') +
            $objectValue($data['sortir_sisa'], 'cost_op') +
            $arrayValue($data['sortir_selesai'], 'ttl_rp') +
            $arrayValue($data['sortir_selesai'], 'cost_kerja') +
            $arrayValue($data['sortir_selesai'], 'cost_op') +
            $objectValue($data['grading_sisa'], 'cost_bk') +
            $objectValue($data['grading_proses'], 'cost_bk') +
            $objectValue($data['grading_proses'], 'cost_kerja') +
            $objectValue($data['grading_proses'], 'cost_op') +
            $objectValue($data['grading_susut'], 'cost_bk') +
            $objectValue($data['grading_susut'], 'cost_kerja') +
            $objectValue($data['grading_susut'], 'cost_cu') +
            $objectValue($data['grading_susut'], 'cost_op') +
            $objectValue($data['sisa_belum_wip1'], 'ttl_rp') +
            $objectValue($data['sisa_belum_qc'], 'ttl_rp') +
            $objectValue($data['wip2proses'], 'ttl_rp') +
            $objectValue($data['pengiriman_proses'], 'ttl_rp') +
            $objectValue($data['pengiriman'], 'cost_bk') +
            $objectValue($data['pengiriman'], 'cost_kerja') +
            $objectValue($data['pengiriman'], 'cost_cu') +
            $objectValue($data['pengiriman'], 'cost_op');

        $modalBk = $arrayValue($data['bk'], 'cost_bk');
        $totalOperasional = $arrayValue($data['uang_cost'], 'total_operasional');
        $cumulativeCost = round($totalBkRp - $modalBk - $totalOperasional, 2);

        $baseline = (float) (
            DB::table('balance_sheet_closings')
                ->orderByDesc('tahun')
                ->orderByDesc('bulan')
                ->value('cumulative_cost_baseline') ?? 0
        );

        return [
            'total_bk_rp' => $totalBkRp,
            'modal_bk' => $modalBk,
            'total_operasional' => $totalOperasional,
            'cumulative' => $cumulativeCost,
            'baseline' => $baseline,
            'current' => round($cumulativeCost - $baseline, 2),
        ];
    }

    private function buildBalanceRows(array $data): array
    {
        $objectValue = static fn ($object, string $field): float =>
            (float) ($object->{$field} ?? 0);
        $arrayValue = static fn ($rows, string $field): float =>
            (float) sumBk($rows ?? [], $field);

        $rows = [
            ['BK Sisa', $data['bk_sisa'], 0, 'stock',
                $objectValue($data['bk_sisa'], 'ttl_rp')],
            ['Cabut sedang proses', $data['cbt_proses'], 1, 'process',
                $objectValue($data['cbt_proses'], 'ttl_rp')
                + $objectValue($data['cbt_proses'], 'cost_kerja')
                + $objectValue($data['cbt_proses'], 'cost_op')],
            ['Cabut sisa pengawas', $data['cbt_sisa_pgws'], 2, 'process',
                $objectValue($data['cbt_sisa_pgws'], 'ttl_rp')],
            ['Cabut selesai siap cetak belum kirim', null, 3, 'stock',
                $arrayValue($data['cabut_selesai_siap_cetak'], 'ttl_rp')
                + $arrayValue($data['cabut_selesai_siap_cetak'], 'cost_kerja')
                + $arrayValue($data['cabut_selesai_siap_cetak'], 'cost_op')],
            ['Cetak sedang proses', $data['cetak_proses'], 4, 'process',
                $objectValue($data['cetak_proses'], 'ttl_rp')
                + $objectValue($data['cetak_proses'], 'cost_kerja')
                + $objectValue($data['cetak_proses'], 'cost_op')],
            ['Cetak sisa pengawas', $data['cetak_sisa'], 5, 'process',
                $objectValue($data['cetak_sisa'], 'ttl_rp')],
            ['Cetak selesai siap sortir belum kirim', null, 6, 'stock',
                $arrayValue($data['cetak_selesai'], 'ttl_rp')
                + $arrayValue($data['cetak_selesai'], 'cost_kerja')
                + $arrayValue($data['cetak_selesai'], 'cost_op')],
            ['Sortir sedang proses', $data['sedang_proses'], 7, 'process',
                $objectValue($data['sedang_proses'], 'ttl_rp')
                + $objectValue($data['sedang_proses'], 'cost_kerja')
                + $objectValue($data['sedang_proses'], 'cost_op')],
            ['Sortir sisa pengawas', $data['sortir_sisa'], 8, 'process',
                $objectValue($data['sortir_sisa'], 'ttl_rp')
                + $objectValue($data['sortir_sisa'], 'cost_kerja')
                + $objectValue($data['sortir_sisa'], 'cost_op')],
            ['Sortir selesai siap grading belum kirim', null, 9, 'stock',
                $arrayValue($data['sortir_selesai'], 'ttl_rp')
                + $arrayValue($data['sortir_selesai'], 'cost_kerja')
                + $arrayValue($data['sortir_selesai'], 'cost_op')],
            ['Sisa belum grading', $data['grading_sisa'], 10, 'process',
                $objectValue($data['grading_sisa'], 'cost_bk')],
            ['Grading sedang proses', $data['grading_proses'], 11, 'process',
                $objectValue($data['grading_proses'], 'cost_bk')
                + $objectValue($data['grading_proses'], 'cost_kerja')
                + $objectValue($data['grading_proses'], 'cost_op')
                + $objectValue($data['grading_susut'], 'cost_bk')
                + $objectValue($data['grading_susut'], 'cost_kerja')
                + $objectValue($data['grading_susut'], 'cost_cu')
                + $objectValue($data['grading_susut'], 'cost_op')],
            ['WIP1 sedang proses', $data['sisa_belum_wip1'], 12, 'process',
                $objectValue($data['sisa_belum_wip1'], 'ttl_rp')],
            ['QC sedang proses', $data['sisa_belum_qc'], 13, 'process',
                $objectValue($data['sisa_belum_qc'], 'ttl_rp')],
            ['WIP2 sedang proses', $data['wip2proses'], 14, 'process',
                $objectValue($data['wip2proses'], 'ttl_rp')],
            ['Pengiriman sedang proses', $data['pengiriman_proses'], 15, 'process',
                $objectValue($data['pengiriman_proses'], 'ttl_rp')],
            ['Pengiriman', $data['pengiriman'], 16, 'process',
                $objectValue($data['pengiriman'], 'cost_bk')
                + $objectValue($data['pengiriman'], 'cost_kerja')
                + $objectValue($data['pengiriman'], 'cost_cu')
                + $objectValue($data['pengiriman'], 'cost_op')],
        ];

        return collect($rows)->map(function (array $row) use ($data, $objectValue, $arrayValue) {
            [$label, $source, $detailId, $type, $total] = $row;

            $arraySources = [
                3 => $data['cabut_selesai_siap_cetak'],
                6 => $data['cetak_selesai'],
                9 => $data['sortir_selesai'],
            ];
            $pcs = isset($arraySources[$detailId])
                ? $arrayValue($arraySources[$detailId], 'pcs')
                : $objectValue($source, 'pcs');
            $gr = isset($arraySources[$detailId])
                ? $arrayValue($arraySources[$detailId], 'gr')
                : $objectValue($source, 'gr');

            return [
                'label' => $label,
                'pcs' => $pcs,
                'gr' => $gr,
                'total' => $total,
                'average' => $gr > 0 ? $total / $gr : 0,
                'detail_id' => $detailId,
                'type' => $type,
            ];
        })->all();
    }

    /**
     * Menempelkan cost operasional Cabut/EO tepat satu kali per no_box.
     * Beberapa query detail dapat menghasilkan lebih dari satu baris untuk box
     * yang sama, sehingga baris berikutnya sengaja diberi nol agar tidak dobel.
     */
    private function attachOperationalCostByBox(array $rows): array
    {
        if (!$rows) {
            return $rows;
        }

        $boxNumbers = collect($rows)
            ->pluck('no_box')
            ->filter(fn ($box) => $box !== null && $box !== '')
            ->map(fn ($box) => (string) $box)
            ->unique()
            ->values();

        $costs = DB::query()
            ->fromSub(function ($query) use ($boxNumbers) {
                $query->from('cabut')
                    ->selectRaw('CAST(no_box AS CHAR) AS no_box, COALESCE(cost_op, 0) AS cost_op')
                    ->whereIn(DB::raw('CAST(no_box AS CHAR)'), $boxNumbers->all())
                    ->unionAll(
                        DB::table('eo')
                            ->selectRaw('CAST(no_box AS CHAR) AS no_box, COALESCE(cost_op, 0) AS cost_op')
                            ->whereIn(DB::raw('CAST(no_box AS CHAR)'), $boxNumbers->all())
                    );
            }, 'operational_cost')
            ->selectRaw('no_box, SUM(cost_op) AS cost_op')
            ->groupBy('no_box')
            ->pluck('cost_op', 'no_box');

        $attached = [];
        foreach ($rows as $row) {
            $box = (string) ($row->no_box ?? '');
            $row->cost_op = isset($attached[$box]) ? 0 : (float) ($costs[$box] ?? 0);
            $attached[$box] = true;
        }

        return $rows;
    }

    public function balancesheet()
    {
        $cetakStok = CocokanModel::cetak_stok_balance();

        $cetak_sisa = new stdClass();
        $cetak_sisa->pcs = $cetakStok->pcs;
        $cetak_sisa->gr = $cetakStok->gr;
        $cetak_sisa->cost_op = $cetakStok->cost_op ?? 0;
        $cetak_sisa->ttl_rp = $cetakStok->ttl_rp + $cetakStok->cost_kerja + $cetak_sisa->cost_op;

        $pengiriman = Grading::pengirimanSum();
        $grading_susut = Grading::belumKirimSumsusut();

        $model = new CocokanModel();

        $data = [
            'title' => 'Balance Sheet ',
            'bk' => SummaryModel::summarybk(),
            'bk_sisa' => CocokanModel::summarybk_sisa(),
            'bk_suntik' => [],
            'uang_cost' => BalanceModel::uangCost(),
            'cbt_proses' => CocokanModel::bksedang_proses_sum(),
            'cbt_sisa_pgws' => CocokanModel::bksisapgws(),
            'cetak_proses' => CocokanModel::cetak_proses_balance(),
            'cetak_sisa' => $cetak_sisa,
            'sedang_proses' => CocokanModel::sortir_proses_balance(),
            'sortir_sisa' => CocokanModel::sortir_stock_balance(),
            'pengiriman' => $pengiriman,
            'grading_sisa' => CocokanModel::gradingSisaOne(),
            'cabut_selesai_siap_cetak' => $this->attachOperationalCostByBox(OpnameNewModel::bksedang_selesai_sum()),
            'cetak_selesai' => $this->attachOperationalCostByBox(OpnameNewModel::cetak_selesai()),
            'sortir_selesai' => $this->attachOperationalCostByBox(OpnameNewModel::sortir_selesai()),
            'grading_susut' => $grading_susut,
            'sisa_belum_wip1' => $model->sisa_belum_wip1(),
            'sisa_belum_qc' => $model->sisa_belum_qc(),
            'wip2proses' => $model->wip2proses(),
            'pengiriman_proses' => $model->pengiriman_proses(),
            'grading_proses' => $model->gradingProsesOne(),
        ];
        $costSummary = $this->calculateBalanceCost($data);
        $balanceRows = $this->buildBalanceRows($data);
        $bkTotalGr = sumBk($data['bk'], 'gr_bk') + sumBk($data['bk_suntik'], 'gr');
        $bkTotalRp = sumBk($data['bk'], 'cost_bk') + sumBk($data['bk_suntik'], 'ttl_rp');

        $data['bk_totals'] = [
            'pcs' => sumBk($data['bk'], 'pcs_bk') + sumBk($data['bk_suntik'], 'pcs'),
            'gr' => $bkTotalGr,
            'total' => $bkTotalRp,
            'average' => $bkTotalGr > 0 ? $bkTotalRp / $bkTotalGr : 0,
        ];
        $data['balance_rows'] = $balanceRows;
        $data['detail_routes'] = [
            1 => route('cocokan.detailCabutProses'),
            2 => route('cocokan.detailCabutSisa'),
            3 => route('cocokan.detailCabutBelumKirim'),
            4 => route('cocokan.detailCetakSedangProses'),
            5 => route('cocokan.detailCetakSisa'),
            6 => route('cocokan.detailCetakBelumKirim'),
            7 => route('cocokan.detailSortirProses'),
            8 => route('cocokan.detailSortirSisa'),
            9 => route('cocokan.detailSortirBelumKirim'),
            10 => route('cocokan.detailSisaBelumGrading'),
            11 => route('cocokan.detailGradingSedangProses'),
            12 => route('cocokan.detailWip1SedangProses'),
            13 => route('cocokan.detailQcSedangProses'),
            14 => route('cocokan.detailWip2SedangProses'),
            15 => route('cocokan.detailPengirimanSedangProses'),
            16 => route('cocokan.detailPengiriman'),
        ];
        $data['balance_totals'] = [
            'pcs' => collect($balanceRows)->sum('pcs'),
            'gr' => collect($balanceRows)->sum('gr'),
            'total' => $costSummary['total_bk_rp'],
        ];
        $data['cost_berjalan'] = $costSummary['current'];
        $closingAdjustments = DB::table('balance_sheet_closings')
            ->get()
            ->mapWithKeys(fn ($closing) => [
                (int) $closing->tahun . '-' . (int) $closing->bulan =>
                    (float) $closing->cost_berjalan_sebelum_tutup,
            ])
            ->all();
        $data['cost_rows'] = collect($data['uang_cost'])->map(function ($cost) use ($closingAdjustments) {
            $adjustment = $closingAdjustments[(int) $cost->tahun . '-' . (int) $cost->bulan] ?? 0;

            return [
                'bulan' => (int) $cost->bulan,
                'tahun' => (int) $cost->tahun,
                'periode' => date('F Y', strtotime($cost->tahun . '-' . $cost->bulan . '-01')),
                'gaji' => (float) $cost->gaji + $adjustment,
                'operasional' => (float) $cost->total_operasional - (float) $cost->gaji,
                'total' => (float) $cost->total_operasional + $adjustment,
            ];
        })->all();
        $data['cost_totals'] = [
            'gaji' => collect($data['cost_rows'])->sum('gaji'),
            'operasional' => collect($data['cost_rows'])->sum('operasional'),
            'total' => collect($data['cost_rows'])->sum('total'),
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
                DB::rollBack();
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
            $grading_proses = $model->gradingProsesOne();
            $sisa_belum_wip1 = $model->sisa_belum_wip1();
            $sisa_belum_qc = $model->sisa_belum_qc();
            $wip2proses = $model->wip2proses();
            $pengiriman_proses = $model->pengiriman_proses();
            $bk_sisa = CocokanModel::summarybk_sisa();

            $bk = SummaryModel::summarybk();
            $bk_suntik = DB::select("SELECT * FROM opname_suntik WHERE opname = 'Y'");
            $uangCost = BalanceModel::uangCost();

            $ttl_sisa_belum_kirim =
                $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op;

            $ttl_pengiriman =
                $pengiriman->cost_bk +
                $pengiriman->cost_kerja +
                $pengiriman->cost_cu +
                $pengiriman->cost_op;

            $ttl_sisa_blum_grading = $grading_sisa->cost_bk;

            $costSummary = $this->calculateBalanceCost([
                'bk' => $bk,
                'bk_sisa' => $bk_sisa,
                'uang_cost' => $uangCost,
                'cbt_proses' => $cbt_proses,
                'cbt_sisa_pgws' => $cbt_sisa_pgws,
                'cabut_selesai_siap_cetak' => $cabut_selesai_siap_cetak,
                'cetak_proses' => $cetak_proses,
                'cetak_sisa' => $cetak_sisa,
                'cetak_selesai' => $cetak_selesai,
                'sedang_proses' => $sedang_proses,
                'sortir_sisa' => $sortir_sisa,
                'sortir_selesai' => $sortir_selesai,
                'grading_sisa' => $grading_sisa,
                'grading_proses' => $grading_proses,
                'grading_susut' => $grading_susut,
                'sisa_belum_wip1' => $sisa_belum_wip1,
                'sisa_belum_qc' => $sisa_belum_qc,
                'wip2proses' => $wip2proses,
                'pengiriman_proses' => $pengiriman_proses,
                'pengiriman' => $pengiriman,
            ]);


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
                foreach ($uangCost as $u) {
                    $closingAdjustment = DB::table('balance_sheet_closings')
                        ->where('tahun', (int) $u->tahun)
                        ->where('bulan', (int) $u->bulan)
                        ->value('cost_berjalan_sebelum_tutup') ?? 0;

                    if (
                        (int) $u->tahun === (int) $tahun_ditutup
                        && (int) $u->bulan === (int) $bulan_ditutup
                    ) {
                        $closingAdjustment = $costSummary['current'];
                    }

                    $data2[] = [
                        'bulan_tahun' => date('F Y', strtotime($u->tahun . '-' . $u->bulan . '-' . '01')),
                        'gaji' => $u->gaji + $closingAdjustment,
                        'cost_op' => $u->total_operasional - $u->gaji,
                        'ttl_rp' => $u->total_operasional + $closingAdjustment,
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
                    'ttl_rp' => 0,
                    'tgl_ditutup' => $tgl_ditutup,
                    'bulan_ditutup' => $bulan_ditutup,
                    'tahun_ditutup' => $tahun_ditutup,
                    'admin' => $admin,
                ];
                DB::table('history_cost_perbulan')->insert($data2);
            }

            DB::table('balance_sheet_closings')->updateOrInsert(
                [
                    'bulan' => (int) $bulan_ditutup,
                    'tahun' => (int) $tahun_ditutup,
                ],
                [
                    'cost_berjalan_sebelum_tutup' => $costSummary['current'],
                    'cumulative_cost_baseline' => $costSummary['cumulative'],
                    'closed_at' => $tgl_ditutup,
                    'admin' => $admin,
                    'updated_at' => $tgl_ditutup,
                    'created_at' => $tgl_ditutup,
                ]
            );
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
                        'ttl_rp' => sumBk($cabut_selesai_siap_cetak, 'ttl_rp') + sumBk($cabut_selesai_siap_cetak, 'cost_kerja') + sumBk($cabut_selesai_siap_cetak, 'cost_op'),
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
                        'ttl_rp' => sumBk($cetak_selesai, 'ttl_rp') + sumBk($cetak_selesai, 'cost_kerja') + sumBk($cetak_selesai, 'cost_op'),
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
                        'ttl_rp' => sumBk($sortir_selesai, 'ttl_rp') + sumBk($sortir_selesai, 'cost_kerja') + sumBk($sortir_selesai, 'cost_op'),
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
            'box' => $this->attachOperationalCostByBox($model::bksedang_proses_sum()),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCabutSisa(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $this->attachOperationalCostByBox($model::bksisapgws()),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCabutBelumKirim(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $this->attachOperationalCostByBox($model::bksedang_selesai_sum()),

        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCetakSedangProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $this->attachOperationalCostByBox($model::cetak_proses()),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCetakSisa(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $this->attachOperationalCostByBox($model::cetak_stok()),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailCetakBelumKirim(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $this->attachOperationalCostByBox($model::cetak_selesai()),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailSortirProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $this->attachOperationalCostByBox($model::sortir_proses()),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailSortirSisa(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $this->attachOperationalCostByBox($model::sortir_stock()),
        ];
        return view('home.cocokan.balance.detailcabutproses', $data);
    }
    public function detailSortirBelumKirim(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box' => $this->attachOperationalCostByBox($model::sortir_selesai()),
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
