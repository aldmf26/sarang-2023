<?php

namespace App\Http\Controllers;

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
        $akhir_cbt->ttl_rp = $ca2->ttl_rp + $ca12suntik->ttl_rp;



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

        $data = [

            'title' => 'Cetak',
            'ctk_opname' => $ctk_opname,
            'akhir_cbt' => $akhir_cbt,
            'cetak_proses' => $model::cetak_proses(),
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

        $data = [
            'title' => 'Sortir ',
            'opname' => $this->getSuntikan(31),
            'akhir_cetak' => $akhir_cetak,
            'sedang_proses' => $model::sortir_proses(),
            'sortir_sisa' => $model::stock_sortir(),
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
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp;


        // $pengiriman = DB::selectOne("SELECT sum(b.pcs) as pcs, sum(b.gr) as gr FROM pengiriman as a
        //     JOIN grading_partai as b on a.no_box = b.box_pengiriman");

        $grading = DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp,sum(a.pcs) as pcs, sum(a.gr) as gr ,
        sum(a.cost_bk) as cost_bk, sum(a.cost_kerja) as cost_kerja, sum(a.cost_cu) as cost_cu, sum(a.cost_op) as cost_op
        FROM grading_partai as a 
        where a.grade != 'susut'
        ");

        $grading_sisa = DB::selectOne("SELECT a.no_box_sortir, sum(b.pcs_awal - d.pcs) as pcs , sum(b.gr_awal - d.gr) as gr , sum(g.ttl_rp) as cost_bk, sum(COALESCE(g.cost_cbt,0) + COALESCE(g.cost_eo,0) + COALESCE(g.cost_ctk,0) + COALESCE(g.cost_str,0) ) as cost_kerja
FROM grading as a 
left join formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade' 
JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'cabut' 
LEFT JOIN( select no_box_sortir as no_box,sum(pcs) as pcs,sum(gr) as gr from grading group by no_box_sortir ) as d on d.no_box = a.no_box_sortir 
left join(
        SELECT a.no_box, (a.gr_awal * a.hrga_satuan) as ttl_rp, b.ttl_rp as cost_cbt, c.ttl_rp as cost_eo, d.cost_ctk, e.ttl_rp as cost_str, f.cost_cu
            FROM bk as a 
            left JOIN cabut as b on b.no_box = a.no_box
            left JOIN eo as c on c.no_box = a.no_box
            left join (
                SELECT a.no_box, sum(a.ttl_rp) as cost_ctk 
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CTK'
                        group by a.no_box
            ) as d on d.no_box = a.no_box
            left join sortir as e on e.no_box = a.no_box
            left join (
                SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CU'
                        group by a.no_box
            ) as f on f.no_box = a.no_box
            where a.baru = 'baru' and a.kategori ='cabut'
            group by a.no_box
        ) as g on g.no_box = a.no_box_sortir
        WHERE a.selesai = 'T' ;");





        $sumTtlRpPengiriman = DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp FROM pengiriman as a ");

        $data = [
            'title' => 'Grading ',
            'opname' =>  $this->getSuntikan(41),
            'sortir_akhir' => $sortir_akhir,
            // 'pengiriman' => $pengiriman,
            'sumTtlRpPengiriman' => $sumTtlRpPengiriman,
            'grading' => $grading,
            'grading_sisa' => $grading_sisa,
        ];
        return view('home.cocokan.grading', $data);
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

        $grading = DB::selectOne("SELECT sum(a.cost_bk) as cost_bk,sum(a.pcs) as pcs, sum(a.gr) as gr FROM grading_partai as a where a.grade != 'susut'");
        $grading_sisa = DB::selectOne("SELECT a.no_box_sortir, sum(b.pcs_awal - d.pcs) as pcs , sum(b.gr_awal - d.gr) as gr FROM grading as a left join formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade' JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'cabut' LEFT JOIN( select no_box_sortir as no_box,sum(pcs) as pcs,sum(gr) as gr from grading group by no_box_sortir ) as d on d.no_box = a.no_box_sortir WHERE a.selesai = 'T';");

        $sumTtlRpPengiriman = DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp FROM pengiriman as a ");

        $belum_kirim = Grading::belumKirimSum();

        $data = [
            'title' => 'Pengiriman ',
            'opname' =>  $this->getSuntikan(41),
            'sortir_akhir' => $sortir_akhir,
            'pengiriman' => $pengiriman,
            'sumTtlRpPengiriman' => $sumTtlRpPengiriman,
            'grading' => $grading,
            'grading_sisa' => $grading_sisa,
            'belum_kirim' => $belum_kirim
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



        $uang_cost = DB::select("SELECT a.* FROM oprasional as a");
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
        $cetak_sisa->ttl_rp = $ca17->ttl_rp + $ca17suntik->ttl_rp + $ca17->cost_kerja;


        $sa = CocokanModel::akhir_sortir();
        $p2suntik = $this->getSuntikan(42);
        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $sa->pcs + $p2suntik->pcs;
        $sortir_akhir->gr = $sa->gr + $p2suntik->gr;
        $sortir_akhir->ttl_rp = $sa->ttl_rp + $p2suntik->ttl_rp;

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

        $data = [
            'title' => 'Balance Sheet ',
            'bk' => SummaryModel::summarybk(),
            'bk_suntik' => DB::select("SELECT * FROM opname_suntik WHERE opname = 'Y'"),
            'uang_cost' => DB::select("SELECT a.* FROM oprasional as a"),
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
            'grading_sisa' => CocokanModel::grading_sisa(),
            'grading_sisa2' => OpnameNewModel::grading_sisa(),
            'grading_akhir' => $grading_akhir,
            'cabut_selesai_siap_cetak' => OpnameNewModel::bksedang_selesai_sum(),
            'cetak_selesai' => OpnameNewModel::cetak_selesai(),
            'sortir_selesai' => OpnameNewModel::sortir_selesai(),
            'grading_susut' => $grading_susut


        ];
        return view('home.cocokan.balance', $data);
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
}
