<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grading;
use App\Models\SummaryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasapController extends Controller
{
    public function index(Request $r)
    {
        if (empty($r->id_pengawas)) {
            $where = '';
            $where2 = '';
        } else {
            $where = "AND b.id = $r->id_pengawas";
            $where2 = "AND e.id = $r->id_pengawas";
        }
        $data = DB::select("SELECT a.tgl_terima as tgl, c.nm_partai, b.id,  b.name, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr_awal
        FROM cabut as a 
        left join users as b on b.id = a.id_pengawas
        left join bk as c on c.no_box = a.no_box and c.kategori = 'cabut'
        where c.baru = 'baru' and a.no_box != '9999' $where 
        group by a.tgl_terima , b.name
        UNION ALL
        SELECT d.tgl_ambil as tgl, f.nm_partai, e.id, e.name, 0 as pcs, sum(d.gr_eo_awal) as gr_awal
        FROM eo as d
        left join users as e on e.id = d.id_pengawas
        left join bk as f on f.no_box = d.no_box and f.kategori = 'cabut'
        where f.baru = 'baru' and d.no_box != '9999' $where2
        group by d.tgl_ambil, e.name

        
        ORDER BY tgl DESC;
        ");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function detail($id_pengawas, $tgl)
    {
        $data = DB::select("SELECT a.tgl_terima as tgl, a.tgl_serah as tgl_selesai, a.no_box, c.nm_partai, d.nama,  sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr_awal
        FROM cabut as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join bk as c on c.no_box = a.no_box and c.kategori = 'cabut'
        left join hasil_wawancara as d on d.id_anak = b.id_anak
        where c.baru = 'baru' and a.id_pengawas = '$id_pengawas' and a.tgl_terima = '$tgl'
        group by a.no_box
UNION ALL
SELECT d.tgl_ambil as tgl, d.tgl_serah as tgl_selesai, d.no_box, f.nm_partai, g.nama, 0 as pcs, sum(d.gr_eo_awal) as gr_awal
        FROM eo as d
        left join tb_anak as e on e.id_anak = d.id_anak
        left join bk as f on f.no_box = d.no_box and f.kategori = 'cabut'
        left join hasil_wawancara as g on g.id_anak = e.id_anak
        where f.baru = 'baru' and d.id_pengawas ='$id_pengawas' and d.tgl_ambil = '$tgl'
        group by d.no_box
        
        ORDER BY no_box ASC;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }


    public function bk(Request $r)
    {
        if (empty($r->bulan)) {
            $bulan = date('m');
            $tahun = date('Y');
        } else {
            $bulan = $r->bulan;
            $tahun = $r->tahun;
        }
        $data = DB::select("SELECT a.*, b.name
        FROM bk as a
        left join users as b on b.id = a.penerima
        where a.baru = 'baru' and a.kategori = 'cabut' and month(a.tgl) = '$bulan' and year(a.tgl) = '$tahun'
        ");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function cabut(Request $r)
    {

        if (empty($r->id_pengawas)) {
            $where = '';
        } else {
            $where = "AND a.id_pengawas = $r->id_pengawas";
        }
        $data = DB::select("SELECT  a.id_anak, a.no_box, c.tipe, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, d.batas_susut,
        c.nm_partai,e.nama, f.name, a.tgl_terima as tgl, a.id_pengawas, a.tgl_serah as tgl_akhir
        FROM cabut as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join hasil_wawancara as e on e.id_anak = b.id_anak
         left join users as f on f.id = a.id_pengawas
        join (
        SELECT e.no_box, e.tipe, e.baru, e.nm_partai
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        left join tb_kelas as d on d.id_kelas = a.id_kelas
        where c.baru = 'baru'  and a.selesai = 'Y' $where 
        group by a.id_pengawas, a.tgl_terima

        UNION ALL 


        SELECT  b.id_anak, a.no_box, c.tipe, 0 as pcs , sum(a.gr_eo_awal) as gr_awal, 0 as pcs_akhir, sum(a.gr_eo_akhir) as gr_akhir, 100 as batas_susut, c.nm_partai, e.nama, f.name, a.tgl_ambil as tgl,a.id_pengawas, a.tgl_serah as tgl_akhir
        FROM eo as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join hasil_wawancara as e on e.id_anak = b.id_anak
        left join users as f on f.id = a.id_pengawas
        join (
        SELECT e.no_box, e.tipe, e.baru, e.nm_partai
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        where c.baru = 'baru' and  a.selesai = 'Y' $where
        group by a.id_pengawas, a.tgl_ambil

       

        order by tgl DESC
        
        ;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function cabut_pengeringan(Request $r)
    {

        if (empty($r->id_pengawas)) {
            $where = '';
        } else {
            $where = "AND a.id_pengawas = $r->id_pengawas";
        }
        $data = DB::select("SELECT  a.id_anak, a.no_box, c.tipe, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, d.batas_susut,
        c.nm_partai,e.nama, f.name, a.tgl_terima as tgl, a.id_pengawas, a.tgl_serah as tgl_akhir
        FROM cabut as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join hasil_wawancara as e on e.id_anak = b.id_anak
         left join users as f on f.id = a.id_pengawas
        join (
        SELECT e.no_box, e.tipe, e.baru, e.nm_partai
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        left join tb_kelas as d on d.id_kelas = a.id_kelas
        where c.baru = 'baru'  and a.selesai = 'Y' $where 
        group by a.id_pengawas, a.tgl_serah

        UNION ALL 


        SELECT  b.id_anak, a.no_box, c.tipe, 0 as pcs , sum(a.gr_eo_awal) as gr_awal, 0 as pcs_akhir, sum(a.gr_eo_akhir) as gr_akhir, 100 as batas_susut, c.nm_partai, e.nama, f.name, a.tgl_ambil as tgl,a.id_pengawas, a.tgl_serah as tgl_akhir
        FROM eo as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join hasil_wawancara as e on e.id_anak = b.id_anak
        left join users as f on f.id = a.id_pengawas
        join (
        SELECT e.no_box, e.tipe, e.baru, e.nm_partai
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        where c.baru = 'baru' and  a.selesai = 'Y' $where
        group by a.id_pengawas, a.tgl_serah
       

        order by tgl_akhir DESC
        
        ;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function cabut_detail(Request $r)
    {

        $data = DB::select("SELECT  a.id_anak, a.no_box, c.tipe, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, d.batas_susut,
        c.nm_partai,e.nama, f.name, a.tgl_terima as tgl, a.tgl_serah as tgl_akhir
        FROM cabut as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join hasil_wawancara as e on e.id_anak = b.id_anak
         left join users as f on f.id = a.id_pengawas
        join (
        SELECT e.no_box, e.tipe, e.baru, e.nm_partai
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        left join tb_kelas as d on d.id_kelas = a.id_kelas
        where c.baru = 'baru'  and a.selesai = 'Y' and a.tgl_terima = '$r->tgl' and a.id_pengawas = '$r->id_pengawas'
        

        UNION ALL 


        SELECT  b.id_anak, a.no_box, c.tipe, 0 as pcs , a.gr_eo_awal, 0 as pcs_akhir, a.gr_eo_akhir, 100 as batas_susut, c.nm_partai, e.nama, f.name, a.tgl_ambil as tgl, a.tgl_serah as tgl_akhir
        FROM eo as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join hasil_wawancara as e on e.id_anak = b.id_anak
        left join users as f on f.id = a.id_pengawas
        join (
        SELECT e.no_box, e.tipe, e.baru, e.nm_partai
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        where c.baru = 'baru' and  a.selesai = 'Y' and a.tgl_ambil = '$r->tgl' and a.id_pengawas = '$r->id_pengawas'
        

        order by tgl DESC
        
        ;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function cabut_detail_pengeringan(Request $r)
    {

        $data = DB::select("SELECT  a.id_anak, a.no_box, c.tipe, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, d.batas_susut,
        c.nm_partai,e.nama, f.name, a.tgl_terima as tgl, a.tgl_serah as tgl_akhir
        FROM cabut as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join hasil_wawancara as e on e.id_anak = b.id_anak
         left join users as f on f.id = a.id_pengawas
        join (
        SELECT e.no_box, e.tipe, e.baru, e.nm_partai
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        left join tb_kelas as d on d.id_kelas = a.id_kelas
        where c.baru = 'baru'  and a.selesai = 'Y' and a.tgl_serah = '$r->tgl' and a.id_pengawas = '$r->id_pengawas'
        

        UNION ALL 


        SELECT  b.id_anak, a.no_box, c.tipe, 0 as pcs , a.gr_eo_awal, 0 as pcs_akhir, a.gr_eo_akhir, 100 as batas_susut, c.nm_partai, e.nama, f.name, a.tgl_ambil as tgl, a.tgl_serah as tgl_akhir
        FROM eo as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join hasil_wawancara as e on e.id_anak = b.id_anak
        left join users as f on f.id = a.id_pengawas
        join (
        SELECT e.no_box, e.tipe, e.baru, e.nm_partai
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        where c.baru = 'baru' and  a.selesai = 'Y' and a.tgl_serah = '$r->tgl' and a.id_pengawas = '$r->id_pengawas'
        

        order by tgl DESC
        
        ;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function cetak(Request $r)
    {

        if (empty($r->id_pengawas)) {
            $where = '';
        } else {
            $where = "AND a.id_pengawas = $r->id_pengawas";
        }

        $data = DB::select("SELECT e.name, a.id_pengawas, a.tgl, d.nm_partai, c.nama, a.no_box, d.tipe, sum(a.pcs_awal_ctk) as pcs_awal_ctk, sum(a.gr_awal_ctk) as gr_awal_ctk, sum((COALESCE(a.pcs_tdk_cetak,0) + COALESCE(a.pcs_akhir))) as pcs_akhir, sum((COALESCE(a.gr_tdk_cetak,0) + COALESCE(a.gr_akhir,0))) as gr_akhir
        FROM cetak_new as a
        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as e on e.id = a.id_pengawas
        
        LEFT join (
        SELECT d.no_box , d.tipe, d.nm_partai
            FROM bk as d 
            where d.kategori ='Cabut'
        ) as d on d.no_box = a.no_box
        where b.kategori = 'CTK' and a.selesai ='Y' $where
        group by a.tgl, a.id_pengawas
        order by a.tgl DESC
        ;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function cetak_detail(Request $r)
    {

        $data = DB::select("SELECT e.name, f.nama, a.id_pengawas, a.tgl, d.nm_partai,  a.no_box, d.tipe, sum(a.pcs_awal_ctk) as pcs_awal_ctk, sum(a.gr_awal_ctk) as gr_awal_ctk, sum((COALESCE(a.pcs_tdk_cetak,0) + COALESCE(a.pcs_akhir))) as pcs_akhir, sum((COALESCE(a.gr_tdk_cetak,0) + COALESCE(a.gr_akhir,0))) as gr_akhir
        FROM cetak_new as a
        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as e on e.id = a.id_pengawas
        left join hasil_wawancara as f on f.id_anak = c.id_anak
        LEFT join (
        SELECT d.no_box , d.tipe, d.nm_partai
            FROM bk as d 
            where d.kategori ='Cabut'
        ) as d on d.no_box = a.no_box
        where b.kategori = 'CTK' and a.selesai ='Y' and a.id_pengawas = '$r->id_pengawas' and a.tgl = '$r->tgl'
        group by a.no_box
    
        ;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }


    public function grading(Request $r)
    {

        $data = DB::select("SELECT a.tgl, a.no_invoice, a.nm_partai, sum(a.pcs) as pcs, sum(a.gr) as gr
        FROM grading_partai as a 
        group by a.no_invoice
        order by a.tgl DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function grading_detail(Request $r)
    {

        $data = DB::select("SELECT a.tgl, a.grade, a.nm_partai, sum(a.pcs) as pcs, sum(a.gr) as gr, count(a.box_pengiriman) as box , a.not_oke
        FROM grading_partai as a 
        where a.no_invoice = '$r->no_invoice'
        group by a.grade
        order by a.not_oke DESC, a.grade ASC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function pengiriman_akhir(Request $r)
    {

        $data = DB::select("SELECT 
        b.no_barcode, 
        b.tgl_input,
        a.grade, 
        SUM(a.pcs) as pcs, 
        SUM(a.gr) as gr, 
        GROUP_CONCAT(DISTINCT CONCAT(\"'\", a.nm_partai, \"'\") SEPARATOR ', ') AS nm_partai 
        FROM grading_partai as a
        JOIN pengiriman as b ON b.no_box = a.box_pengiriman
        GROUP BY b.tgl_input
        Order by b.tgl_input DESC
        ");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function pengiriman_akhir_detail(Request $r)
    {
        if (empty($r->tgl)) {
            $tgl = date('Y-m-d');
        } else {
            $tgl = $r->tgl;
        }

        $data = DB::select("SELECT 
        b.no_barcode, 
        a.grade, 
        SUM(a.pcs) as pcs, 
        SUM(a.gr) as gr, 
        GROUP_CONCAT(DISTINCT CONCAT(\"'\", a.nm_partai, \"'\") SEPARATOR ', ') AS nm_partai,
        b.no_barcode
        FROM grading_partai as a
        JOIN pengiriman as b ON b.no_box = a.box_pengiriman
        WHERE b.tgl_input = '$tgl'
        GROUP BY b.no_barcode, a.grade");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function pengiriman_akhir_detail_group_grade(Request $r)
    {
        $tgl = $r->tgl;
        $data = DB::select("SELECT a.no_barcode, a.grade, sum(a.pcs) as pcs, sum(a.gr) as gr, count(a.no_barcode) as jlh_box, a.nm_partai
        FROM (
        SELECT 
                b.no_barcode, 
                a.grade, 
                SUM(a.pcs) as pcs, 
                SUM(a.gr) as gr,
                GROUP_CONCAT(DISTINCT CONCAT(\"'\", a.nm_partai, \"'\") SEPARATOR ', ') AS nm_partai
                FROM grading_partai as a
                JOIN pengiriman as b ON b.no_box = a.box_pengiriman
                WHERE b.tgl_input = '$tgl'
                GROUP BY b.no_barcode, a.grade
        ) as a
        group by a.grade;");


        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function pengiriman_akhir_detail_group_grade2(Request $r)
    {
        $tgl = $r->tgl;
        $data = DB::select("SELECT 
                b.no_barcode, 
                a.grade, 
                SUM(a.pcs) as pcs, 
                SUM(a.gr) as gr,
                a.nm_partai, a.box_pengiriman
                FROM grading_partai as a
                JOIN pengiriman as b ON b.no_box = a.box_pengiriman
                WHERE b.tgl_input = '$tgl'
                GROUP BY b.no_barcode, a.grade, a.nm_partai;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function bk_awal(Request $r)
    {
        $data = SummaryModel::summarybk();
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function cabutbulan(Request $r)
    {
        $bulan = empty($r->bulan) ? date('m') : $r->bulan;
        $tahun = empty($r->tahun) ? date('Y') : $r->tahun;
        $data = DB::select("SELECT a.tgl_terima, b.nama, a.id_anak, a.no_box, c.tipe, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, d.batas_susut
        FROM cabut as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        join (
        SELECT e.no_box, e.tipe, e.baru
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        left join tb_kelas as d on d.id_kelas = a.id_kelas
        where c.baru = 'baru' and MONTH(a.tgl_terima) = '$bulan' and YEAR(a.tgl_terima) = '$tahun' and a.selesai = 'Y'

        UNION ALL 


        SELECT a.tgl_ambil as tgl_terima, b.nama, b.id_anak, a.no_box, c.tipe, 0 as pcs , a.gr_eo_awal as gr_awal, 0 as pcs_akhir, a.gr_eo_akhir as gr_akhir, 100 as batas_susut
        FROM eo as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        join (
        SELECT e.no_box, e.tipe, e.baru
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        where c.baru = 'baru' and MONTH(a.tgl_ambil) = '$bulan' and YEAR(a.tgl_ambil) = '$tahun' and a.selesai = 'Y';");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function produkrelease(Request $r)
    {
        $bulan = empty($r->bulan) ? date('m') : $r->bulan;
        $tahun = empty($r->tahun) ? date('Y') : $r->tahun;
        $data = DB::select("SELECT a.grade, GROUP_CONCAT(a.tgl_input) as tgl, GROUP_CONCAT(a.no_barcode SEPARATOR '\n') as barcode, GROUP_CONCAT(a.cek SEPARATOR '\n') as cek
        FROM(
            SELECT a.grade, a.tgl_input, a.no_barcode, if(a.selesai = 'Y','Release','Hold') as cek
        FROM pengiriman as a 
        where MONTH(a.tgl_input) = '$bulan' and YEAR(a.tgl_input) = '$tahun'
        group by a.grade , a.no_barcode
        ) as a 
        group by a.grade
        order by a.grade asc;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function monitoringProdukJadi()
    {
        $data = DB::select("SELECT a.tgl_input, a.grade , a.no_box, sum(a.gr) as gr
        FROM pengiriman as a
        group by a.tgl_input, a.no_box;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function kontrolPengemasan(Request $r)
    {
        $data = DB::select("SELECT a.no_barcode, a.tgl_input, a.grade, sum(a.pcs) as pcs, sum(a.gr_awal) as gr_awal, sum(a.gr) as gr
        FROM (
        SELECT a.no_barcode, a.no_box, a.tgl_input, a.grade, sum(a.pcs) as pcs, sum(a.gr) as gr_awal, sum(a.gr + (a.gr * (b.kadar/100))) as gr
        FROM pengiriman as a 
        left join pengiriman_packing_list as b on b.id_pengiriman = a.no_box
        group by a.no_box
        ) as a 
        where a.tgl_input = '$r->tgl'

        group by a.no_barcode;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function buktiPermintaan(Request $r)
    {
        $data = DB::select("SELECT a.id_penerima, c.name, a.tanggal, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
        FROM formulir_sarang as a
        left join bk as b on b.no_box = a.no_box and b.kategori ='cabut'
        left join users as c on c.id = a.id_penerima
        where a.kategori = 'cabut'
        group by a.id_penerima, a.tanggal
        ORDER by a.tanggal DESC;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function detailBuktiPermintaan(Request $r)
    {
        $data = DB::select("SELECT b.nm_partai, c.name, a.tanggal, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
        FROM formulir_sarang as a
        left join bk as b on b.no_box = a.no_box and b.kategori ='cabut'
        left join users as c on c.id = a.id_penerima
        where a.kategori = 'cabut' and a.id_penerima = '$r->id_penerima' and a.tanggal = '$r->tanggal'
        group by b.nm_partai
        ORDER by a.tanggal DESC;");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function ttlgrading(Request $r)
    {

        $data = DB::select("SELECT a.tgl, sum(a.pcs) as pcs, sum(a.gr) as gr
        FROM grading_partai as a 
        group by a.tgl
        order by a.tgl DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function ttlgrading_detail(Request $r)
    {

        $data = DB::select("SELECT a.tgl, a.grade, 
        GROUP_CONCAT(DISTINCT CONCAT(\"'\", a.nm_partai, \"'\") SEPARATOR ', ') AS nm_partai ,
        
         sum(a.pcs) as pcs, sum(a.gr) as gr, count(a.box_pengiriman) as box FROM grading_partai as a 
        where a.tgl = '$r->tgl'
        group by a.grade
        order by a.grade ASC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function ttlgrading_detail2(Request $r)
    {

        $data = DB::select("SELECT a.tgl, a.grade, a.nm_partai,
        sum(a.pcs) as pcs, sum(a.gr) as gr, count(a.box_pengiriman) as box FROM grading_partai as a 
        where a.tgl = '$r->tgl'
        group by a.grade , a.nm_partai
        order by a.nm_partai ASC, a.grade ASC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function stok_grade(Request $r)
    {

        $data = DB::select("SELECT b.grade_id, sum(COALESCE(a.pcs_awal,0) - COALESCE(c.pcs_akhir)) as pcs, sum(COALESCE(a.gr_awal,0) - COALESCE(c.gr_akhir,0)) as gr
        FROM bk as a
        join sbw_kotor as b on b.nm_partai = a.nm_partai
        left join (
            SELECT c.no_box, c.pcs_awal as pcs_akhir, c.gr_awal as gr_akhir
            FROM bk as c 
            where c.kategori = 'cabut' and c.formulir = 'Y'
        ) as c on c.no_box = a.no_box
        where a.kategori = 'cabut'
        group by b.grade_id;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function stok_grade_detail(Request $r)
    {

        $data = DB::select("SELECT b.tgl, b.grade_id, b.no_invoice, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, 'masuk' as ket
        FROM bk as a
        left join sbw_kotor as b on b.nm_partai = a.nm_partai
        where b.grade_id = '$r->id'
        group by b.tgl, b.no_invoice

        UNION all

        SELECT a.tgl, b.grade_id, b.no_invoice, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, 'keluar' as ket
        FROM bk as a
        left join sbw_kotor as b on b.nm_partai = a.nm_partai
        where b.grade_id = '$r->id' and a.formulir = 'Y'
        group by a.tgl, b.no_invoice

        order by tgl ASC, ket DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function first_tracebelity(Request $r)
    {

        $data = DB::select("SELECT b.tgl, a.nm_partai, b.grade_id, b.rwb_id, b.no_invoice, sum(a.pcs_awal) as pcs , sum(a.gr_awal) as gr_awal
        FROM bk as a
        join sbw_kotor as b on b.nm_partai = a.nm_partai
        WHERE a.kategori = 'cabut'
        group by a.nm_partai
        order by b.tgl asc;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function first_tracebelity2(Request $r)
    {

        $data = DB::selectOne("SELECT b.tgl, a.nm_partai, b.grade_id, b.rwb_id, b.no_invoice, sum(a.pcs_awal) as pcs , sum(a.gr_awal) as gr_awal, b.kg as berat_kotor, c.gr as gr_kotor
        FROM bk as a
        left join sbw_kotor as b on b.nm_partai = a.nm_partai
        left join bk_awal as c on c.nm_partai = a.nm_partai
        WHERE a.kategori = 'cabut' and a.nm_partai = '$r->nm_partai'
        group by a.nm_partai
        order by b.tgl asc;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function tracebelity1(Request $r)
    {

        $data = DB::select("SELECT e.tgl as tgl_panen, e.no_invoice, e.kg as berat_bersih, (f.gr ) as gr_kotor,  sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, a.tgl_terima, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, max(a.tgl_serah) as tgl_serah, 'cabut' as ket, e.rwb_id,
max(i.tgl) as tgl_selesai_ctk, sum(i.pcs_awal_ctk) as pcs_awal_ctk, sum(i.gr_awal_ctk) as gr_awal_ctk, sum(i.pcs_tdk_cetak + i.pcs_akhir) as pcs_akhir_ctk, sum(i.gr_tdk_cetak + i.gr_akhir) as gr_akhir_ctk, sum(k.pcs) as pcs_grading, sum(k.gr) as gr_grading, max(k.tgl) as tgl_grading, m.pcs_kirim, m.gr_kirim, m.tgl_kirim

        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join sbw_kotor as e on e.nm_partai = b.nm_partai
        left join bk_awal as f on f.nm_partai = b.nm_partai
        left join cetak_new as i on i.no_box = a.no_box and i.id_kelas_cetak !='12'
        left join grading as k on k.no_box_sortir = a.no_box and k.no_invoice is not null
        left join (
        	SELECT m.nm_partai, sum(m.pcs) as pcs_kirim , sum(m.gr) as gr_kirim , max(n.tgl_input) as tgl_kirim
            FROM grading_partai as m 
            left join pengiriman as n on n.no_box = m.box_pengiriman
            WHERE m.sudah_kirim ='Y'
            group by m.nm_partai
        ) as m on m.nm_partai = b.nm_partai
        where b.nm_partai = '$r->nm_partai'
        group by a.tgl_terima

        UNION ALL

        SELECT g.tgl as tgl_panen, g.no_invoice, g.kg as berat_bersih, (h.gr/1000) as gr_kotor, 0 as pcs_awal, sum(c.gr_eo_awal) as gr_awal, c.tgl_ambil as tgl_terima, 0 as pcs_akhir, sum(c.gr_eo_akhir) as gr_akhir, max(c.tgl_serah) as tgl_serah, 'eo' as ket, g.rwb_id,
        max(j.tgl) as tgl_selesai_ctk, sum(j.pcs_awal_ctk) as pcs_awal_ctk, sum(j.gr_awal_ctk) as gr_awal_ctk,sum(j.pcs_tdk_cetak + j.pcs_akhir) as pcs_akhir_ctk, sum(j.gr_tdk_cetak + j.gr_akhir) as gr_akhir_ctk, sum(l.pcs) as pcs_grading, sum(l.gr) as gr_grading,max(l.tgl) as tgl_grading, m.pcs_kirim,m.gr_kirim, m.tgl_kirim
        
        FROM eo as c 
        left join bk as d on d.no_box = c.no_box and d.kategori = 'cabut'
        left join sbw_kotor as g on g.nm_partai = d.nm_partai
        left join bk_awal as h on h.nm_partai = d.nm_partai
        left join cetak_new as j on j.no_box = c.no_box and j.id_kelas_cetak !='12'
        left join grading as l on l.no_box_sortir = c.no_box and l.no_invoice is not null
       left join (
        	SELECT m.nm_partai, sum(m.pcs) as pcs_kirim , sum(m.gr) as gr_kirim , max(n.tgl_input) as tgl_kirim
            FROM grading_partai as m 
            left join pengiriman as n on n.no_box = m.box_pengiriman
            WHERE m.sudah_kirim ='Y'
            group by m.nm_partai
        ) as m on m.nm_partai = d.nm_partai
        where d.nm_partai = '$r->nm_partai'
        group by c.tgl_ambil;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function delivery(Request $r)
    {

        $data = DB::select("SELECT 
            a.no_nota,
            b.nm_packing,
            b.tujuan,
            b.tgl,
            c.box as ttl_box,
            sum(a.pcs) as pcs,
            sum(a.gr + (a.gr / b.kadar)) as gr_naik,
            sum(a.gr) as gr,
            sum(d.cost_bk) as cost_bk,
            sum(d.cost_kerja) as cost_kerja,
           sum(d.cost_cu) as cost_cu,
            sum(d.cost_op) as cost_op,
            max(d.bulan) bulan, max(d.tahun) as tahun
        from pengiriman as a 
        join (
            select no_nota,kadar,nm_packing,tujuan,tgl from pengiriman_packing_list GROUP BY no_nota 
        ) as b on a.no_nota = b.no_nota
        join (
            SELECT no_nota, COUNT(DISTINCT no_barcode) AS box, SUM(pcs) AS sum_pcs, SUM(gr) AS sum_gr
            FROM `pengiriman`
            GROUP BY no_nota
        ) as c on a.no_nota = c.no_nota
        left join (
            SELECT b.box_pengiriman , sum(b.cost_bk) as cost_bk, sum(b.cost_op) as cost_op, sum(b.cost_kerja) as cost_kerja, sum(b.cost_cu) as cost_cu, max(b.bulan) as bulan , max(b.tahun) as tahun
            FROM grading_partai as b 
            where b.sudah_kirim = 'Y'
            group by b.box_pengiriman
        ) as d on d.box_pengiriman = a.no_box
        
        GROUP by a.no_nota 
        order by b.no_nota DESC");


        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function delivery_detail(Request $r)
    {

        $data = DB::select("SELECT 
        b.no_barcode, 
        b.tgl_input,
        b.grade, 
        SUM(a.pcs) as pcs, 
        SUM(a.gr) as gr, 
        GROUP_CONCAT(DISTINCT CONCAT(\"'\", a.nm_partai, \"'\") SEPARATOR ', ') AS nm_partai 
        FROM grading_partai as a
        JOIN pengiriman as b ON b.no_box = a.box_pengiriman
        where b.no_nota = '$r->no_nota'
        GROUP BY b.grade
        Order by b.grade DESC");


        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function tb_anak(Request $r)
    {
        $data = DB::select("SELECT b.id, b.nama FROM tb_anak as a 
        left join hasil_wawancara as b on b.id_anak = a.id_anak
        where a.id_pengawas = '$r->id_pengawas';");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function no_box(Request $r)
    {
        $data = DB::select("SELECT a.no_box, a.pcs_awal
        FROM cabut as a 
        where a.id_pengawas = $r->id_pengawas;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function detail_box(Request $r)
    {
        $data = DB::selectOne("SELECT a.nm_partai
        FROM bk as a 
        where a.no_box = '$r->no_box' and a.kategori = 'cabut';");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function stok_produk_jadi(Request $r)
    {
        $data = DB::select("SELECT 
    all_data.grade,
    all_data.pcs,
    all_data.gr,
    COALESCE(done_data.pcs_akhir, 0) AS pcs_akhir,
    COALESCE(done_data.gr_akhir, 0) AS gr_akhir
FROM (
    SELECT grade, SUM(pcs) AS pcs, SUM(gr) AS gr
    FROM pengiriman
    GROUP BY grade
) AS all_data
LEFT JOIN (
    SELECT grade, SUM(pcs) AS pcs_akhir, SUM(gr) AS gr_akhir
    FROM pengiriman
    WHERE selesai = 'Y'
    GROUP BY grade
) AS done_data
ON all_data.grade = done_data.grade;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function stok_produk_jadi_detail(Request $r)
    {
        $data = DB::select("SELECT b.tgl as tgl, sum(d.pcs) as pcs , sum(d.gr) as gr, 'masuk' as ket,  GROUP_CONCAT(DISTINCT CONCAT(\"'\", d.nm_partai, \"'\") SEPARATOR ', ') AS nm_partai 
        FROM grading_partai as d
        left join pengiriman as a on a.no_box = d.box_pengiriman
        left join pengiriman_packing_list as b on b.no_nota = a.no_nota
        where a.grade = 'dg' 
        group by b.tgl

        UNION ALL

        SELECT c.tgl as tgl, sum(e.pcs) as pcs , sum(e.gr) as gr, 'keluar' as ket, GROUP_CONCAT(DISTINCT CONCAT(\"'\", e.nm_partai, \"'\") SEPARATOR ', ') AS nm_partai 
        FROM grading_partai as e
        left join pengiriman as b on b.no_box = e.box_pengiriman
        left join pengiriman_packing_list as c on c.no_nota = b.no_nota
        where b.grade = 'dg' and b.selesai ='Y'
        GROUP by c.tgl

        ORDER by tgl ASC, ket DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }


    public function detail_bjm_sinta(Request $r)
    {
        $data = DB::selectOne("SELECT sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal FROM bk as a where a.nm_partai like '%$r->nm_partai%' and a.kategori = 'cabut' group by a.nm_partai;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function cabut_pengeringan_new(Request $r)
    {
        $data = DB::select("SELECT id_pengawas, nm_pengawas, sum(pcs) as pcs, sum(gr)as gr, sum(gr_akhir) as gr_akhir FROM (
        SELECT 
            DATE_ADD(c.tgl_terima, INTERVAL a.n DAY) AS tgl,
            c.no_box,
            c.tgl_terima, c.tgl_serah, c.id_pengawas, f.name as nm_pengawas, c.id_anak, e.nama as nm_anak, d.nm_partai,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
            THEN c.pcs_awal - FLOOR(c.pcs_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
            ELSE FLOOR(c.pcs_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
            END AS pcs,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
            THEN c.gr_awal - FLOOR(c.gr_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
            ELSE FLOOR(c.gr_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
            END AS gr,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
            THEN c.gr_akhir - FLOOR(c.gr_akhir / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
            ELSE FLOOR(c.gr_akhir / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
            END AS gr_akhir
        FROM cabut c
        JOIN angka a ON a.n <= DATEDIFF(c.tgl_serah, c.tgl_terima)
        left join bk as d on d.no_box = c.no_box and d.kategori = 'cabut'
        left join tb_anak as e on e.id_anak = c.id_anak
        left join users as f on f.id = c.id_pengawas
        where c.no_box != '9999'
    
    	UNION ALL
    	
    SELECT 
            DATE_ADD(c.tgl_ambil, INTERVAL a.n DAY) AS tgl,
            c.no_box,
            c.tgl_ambil as tgl_terima, c.tgl_serah, c.id_pengawas, f.name as nm_pengawas, c.id_anak, e.nama as nm_anak, d.nm_partai,
            0 AS pcs,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_ambil) 
            THEN c.gr_eo_awal - FLOOR(c.gr_eo_awal / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_ambil))
            ELSE FLOOR(c.gr_eo_awal / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1))
            END AS gr,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_ambil) 
            THEN c.gr_eo_akhir - FLOOR(c.gr_eo_akhir / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_ambil))
            ELSE FLOOR(c.gr_eo_akhir / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1))
            END AS gr_akhir
        FROM eo c
        JOIN angka a ON a.n <= DATEDIFF(c.tgl_serah, c.tgl_ambil)
        left join bk as d on d.no_box = c.no_box and d.kategori = 'cabut'
        left join tb_anak as e on e.id_anak = c.id_anak
        left join users as f on f.id = c.id_pengawas
        where c.no_box != '9999'
    
    
        ) AS hasil
        where tgl_terima BETWEEN '2025-07-28' and NOW()
        Group by id_pengawas
        ORDER BY tgl DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function cabut_pengeringan_new_detail(Request $r)
    {
        $data = DB::select("SELECT * FROM (
  SELECT 
    DATE_ADD(c.tgl_terima, INTERVAL a.n DAY) AS tgl,
    c.no_box,
    c.tgl_terima, c.tgl_serah, c.id_pengawas, c.id_anak, f.nama as nm_anak, d.nm_partai, 'cabut' as kategori,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
      THEN c.pcs_awal - FLOOR(c.pcs_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
      ELSE FLOOR(c.pcs_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
    END AS pcs,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
      THEN c.gr_awal - FLOOR(c.gr_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
      ELSE FLOOR(c.gr_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
    END AS gr,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
      THEN c.gr_akhir - FLOOR(c.gr_akhir / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
      ELSE FLOOR(c.gr_akhir / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
    END AS gr_akhir,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
      THEN g.pcs - FLOOR(g.pcs / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
      ELSE FLOOR(g.pcs / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
    END AS pcs_not_ok
  FROM cabut c
  JOIN angka a ON a.n <= DATEDIFF(c.tgl_serah, c.tgl_terima)
  left join bk as d on d.no_box = c.no_box and d.kategori = 'cabut'
  left join tb_anak as e on e.id_anak = c.id_anak
  left join hasil_wawancara as f on f.id_anak = e.id_anak
  left join tb_hancuran as g on g.no_box = c.no_box and g.kategori = 'cetak'
  where c.no_box != '9999'
    
    UNION ALL 
    
    SELECT 
    DATE_ADD(c.tgl_ambil, INTERVAL a.n DAY) AS tgl,
    c.no_box,
    c.tgl_ambil as tgl_terima, c.tgl_serah, c.id_pengawas, c.id_anak, f.nama as nm_anak, d.nm_partai, 'eo' as kategori,
    0 AS pcs,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_ambil) 
      THEN c.gr_eo_awal - FLOOR(c.gr_eo_awal / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_ambil))
      ELSE FLOOR(c.gr_eo_awal / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1))
    END AS gr,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_ambil) 
      THEN c.gr_eo_akhir - FLOOR(c.gr_eo_akhir / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_ambil))
      ELSE FLOOR(c.gr_eo_akhir / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1))
    END AS gr_akhir,
    0 AS pcs_not_ok
  FROM eo c
  JOIN angka a ON a.n <= DATEDIFF(c.tgl_serah, c.tgl_ambil)
  left join bk as d on d.no_box = c.no_box and d.kategori = 'cabut'
  left join tb_anak as e on e.id_anak = c.id_anak
  left join hasil_wawancara as f on f.id_anak = e.id_anak
  left join tb_hancuran as g on g.no_box = c.no_box and g.kategori = 'cetak'
  where c.no_box != '9999'

) AS hasil
WHERE tgl_terima BETWEEN '2025-07-28' and now() and id_pengawas = $r->id_pengawas
group by tgl, no_box
ORDER BY  tgl ASC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }


    public function cuci_nitrit(Request $r)
    {
        $data = DB::select("SELECT tgl, id_pengawas, nm_pengawas, sum(pcs) as pcs, sum(gr)as gr, sum(gr_akhir) as gr_akhir FROM (
        SELECT 
            DATE_ADD(c.tgl_terima, INTERVAL a.n DAY) AS tgl,
            c.no_box,
            c.tgl_terima, c.tgl_serah, c.id_pengawas, f.name as nm_pengawas, c.id_anak, e.nama as nm_anak, d.nm_partai,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
            THEN c.pcs_awal - FLOOR(c.pcs_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
            ELSE FLOOR(c.pcs_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
            END AS pcs,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
            THEN c.gr_awal - FLOOR(c.gr_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
            ELSE FLOOR(c.gr_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
            END AS gr,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
            THEN c.gr_akhir - FLOOR(c.gr_akhir / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
            ELSE FLOOR(c.gr_akhir / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
            END AS gr_akhir
        FROM cabut c
        JOIN angka a ON a.n <= DATEDIFF(c.tgl_serah, c.tgl_terima)
        left join bk as d on d.no_box = c.no_box and d.kategori = 'cabut'
        left join tb_anak as e on e.id_anak = c.id_anak
        left join users as f on f.id = c.id_pengawas

        where c.no_box != '9999'
    
    	UNION ALL
    	
    SELECT 
            DATE_ADD(c.tgl_ambil, INTERVAL a.n DAY) AS tgl,
            c.no_box,
            c.tgl_ambil as tgl_terima, c.tgl_serah, c.id_pengawas, f.name as nm_pengawas, c.id_anak, e.nama as nm_anak, d.nm_partai,
            0 AS pcs,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_ambil) 
            THEN c.gr_eo_awal - FLOOR(c.gr_eo_awal / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_ambil))
            ELSE FLOOR(c.gr_eo_awal / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1))
            END AS gr,
            CASE 
            WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_ambil) 
            THEN c.gr_eo_akhir - FLOOR(c.gr_eo_akhir / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_ambil))
            ELSE FLOOR(c.gr_eo_akhir / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1))
            END AS gr_akhir
        FROM eo c
        JOIN angka a ON a.n <= DATEDIFF(c.tgl_serah, c.tgl_ambil)
        left join bk as d on d.no_box = c.no_box and d.kategori = 'cabut'
        left join tb_anak as e on e.id_anak = c.id_anak
        left join users as f on f.id = c.id_pengawas
        where c.no_box != '9999'
    
    
        ) AS hasil
        where tgl_terima BETWEEN '2025-07-28' and NOW()
        Group by tgl, id_pengawas
        ORDER BY tgl DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function nitrit_detail(Request $r)
    {
        $data = DB::select("SELECT * FROM (
  SELECT 
    DATE_ADD(c.tgl_terima, INTERVAL a.n DAY) AS tgl,
    c.no_box,
    c.tgl_terima, c.tgl_serah, c.id_pengawas, c.id_anak, f.nama as nm_anak, d.nm_partai, 'cabut' as kategori,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
      THEN c.pcs_awal - FLOOR(c.pcs_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
      ELSE FLOOR(c.pcs_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
    END AS pcs,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
      THEN c.gr_awal - FLOOR(c.gr_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
      ELSE FLOOR(c.gr_awal / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
    END AS gr,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
      THEN c.gr_akhir - FLOOR(c.gr_akhir / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
      ELSE FLOOR(c.gr_akhir / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
    END AS gr_akhir,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_terima) 
      THEN g.pcs - FLOOR(g.pcs / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_terima))
      ELSE FLOOR(g.pcs / (DATEDIFF(c.tgl_serah, c.tgl_terima) + 1))
    END AS pcs_not_ok
  FROM cabut c
  JOIN angka a ON a.n <= DATEDIFF(c.tgl_serah, c.tgl_terima)
  left join bk as d on d.no_box = c.no_box and d.kategori = 'cabut'
  left join tb_anak as e on e.id_anak = c.id_anak
  left join hasil_wawancara as f on f.id_anak = e.id_anak
  left join tb_hancuran as g on g.no_box = c.no_box and g.kategori = 'cetak'
  where c.no_box != '9999'
    
    UNION ALL 
    
    SELECT 
    DATE_ADD(c.tgl_ambil, INTERVAL a.n DAY) AS tgl,
    c.no_box,
    c.tgl_ambil as tgl_terima, c.tgl_serah, c.id_pengawas, c.id_anak, f.nama as nm_anak, d.nm_partai, 'eo' as kategori,
    0 AS pcs,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_ambil) 
      THEN c.gr_eo_awal - FLOOR(c.gr_eo_awal / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_ambil))
      ELSE FLOOR(c.gr_eo_awal / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1))
    END AS gr,
    CASE 
      WHEN a.n = DATEDIFF(c.tgl_serah, c.tgl_ambil) 
      THEN c.gr_eo_akhir - FLOOR(c.gr_eo_akhir / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1)) * (DATEDIFF(c.tgl_serah, c.tgl_ambil))
      ELSE FLOOR(c.gr_eo_akhir / (DATEDIFF(c.tgl_serah, c.tgl_ambil) + 1))
    END AS gr_akhir,
    0 AS pcs_not_ok
  FROM eo c
  JOIN angka a ON a.n <= DATEDIFF(c.tgl_serah, c.tgl_ambil)
  left join bk as d on d.no_box = c.no_box and d.kategori = 'cabut'
  left join tb_anak as e on e.id_anak = c.id_anak
  left join hasil_wawancara as f on f.id_anak = e.id_anak
  left join tb_hancuran as g on g.no_box = c.no_box and g.kategori = 'cetak'
  where c.no_box != '9999'
) AS hasil
WHERE tgl = '$r->tgl' and id_pengawas = $r->id_pengawas
group by tgl, no_box
ORDER BY  no_box ASC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function steaming_new_detail(Request $r)
    {
        $data = DB::select("SELECT 
    group_id,
    GROUP_CONCAT(no_barcode) AS barcodes,
    GROUP_CONCAT(DISTINCT grade SEPARATOR ', ') AS grades,
    SUM(pcs) AS total_pcs,
    SUM(gr) AS total_gr,
    GROUP_CONCAT(DISTINCT nm_partai SEPARATOR ', ') AS nm_partai
FROM (
    SELECT 
        *,
        FLOOR(running_gr / 1000) AS group_id
    FROM (
        SELECT 
            x.*,
            @running_gr := @running_gr + x.gr AS running_gr
        FROM (
            SELECT 
                b.no_barcode, 
                a.grade, 
                SUM(a.pcs) AS pcs, 
                SUM(a.gr) AS gr, 
                GROUP_CONCAT(DISTINCT a.nm_partai SEPARATOR ', ') AS nm_partai
            FROM grading_partai AS a
            JOIN pengiriman AS b ON b.no_box = a.box_pengiriman
            WHERE b.tgl_input = '$r->tgl'
            GROUP BY b.no_barcode, a.grade
            ORDER BY b.no_barcode
        ) AS x,
        (SELECT @running_gr := 0) AS vars
    ) AS step1
) AS grouped
GROUP BY group_id
ORDER BY group_id;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }



    public function steaming_baru(Request $r)
    {
        $data = DB::select("SELECT a.tgl , sum(a.pcs) as pcs, sum(a.gr) as gr FROM grading_partai as a group by a.tgl order by a.tgl DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function hasil_penimbangan_new(Request $r)
    {
        $data = DB::select("SELECT 
            GROUP_CONCAT(DISTINCT a.nm_partai SEPARATOR ', ') AS nm_partai, 
            COUNT(DISTINCT a.box_pengiriman) AS box, 
            a.grade, 
            SUM(a.pcs) AS pcs, 
            SUM(a.gr) AS gr
        FROM grading_partai AS a 
        WHERE a.tgl = '$r->tgl'
        GROUP BY a.grade
        order by a.grade ASC
        ;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }


    public function steaming_baru_detail(Request $r)
    {
        $data = DB::select("WITH RECURSIVE data_pecah AS (
  SELECT 
    id_grading,
    nm_partai,
    grade,
    tgl,
    box_pengiriman,
    pcs,
    gr,
    1 AS bagian_ke
  FROM grading_partai
  WHERE gr <= 1000

  UNION ALL

  SELECT 
    id_grading,
    nm_partai,
    grade,
    tgl,
    box_pengiriman,
    pcs * LEAST(1000, gr - bagian_ke * 1000 + 1000) / gr AS pcs,
    LEAST(1000, gr - bagian_ke * 1000 + 1000) AS gr,
    bagian_ke + 1
  FROM data_pecah
  WHERE gr > bagian_ke * 1000
),

data_dengan_urut AS (
  SELECT 
    *,
    ROW_NUMBER() OVER (
      PARTITION BY nm_partai, grade
      ORDER BY id_grading, bagian_ke
    ) AS urut
  FROM data_pecah
),

gruping AS (
  SELECT 
    *,
    SUM(gr) OVER (
      PARTITION BY nm_partai, grade
      ORDER BY urut
    ) AS gr_akumulasi
  FROM data_dengan_urut
),

final_grup AS (
  SELECT 
    *,
    FLOOR((gr_akumulasi - 1) / 1000) + 1 AS grup_ke
  FROM gruping
),

-- Ambil grup lengkap dengan info box_pengiriman
grup_lengkap AS (
  SELECT 
    nm_partai,
    grade,
    grup_ke,
    tgl,
    box_pengiriman,
    pcs,
    gr
  FROM final_grup
),

-- Cek total gr dan box_pengiriman unik per grup_ke
grup_akhir AS (
  SELECT 
    nm_partai,
    grade,
    grup_ke,
    MAX(DATE(tgl)) AS tgl_terakhir,
    ROUND(SUM(pcs)) AS total_pcs,
    SUM(gr) AS total_gr
  FROM grup_lengkap
  GROUP BY nm_partai, grade, grup_ke
),

-- Ambil hanya yang 1000 gr dan tgl cocok
grup_valid AS (
  SELECT *
  FROM grup_akhir
  WHERE total_gr = 1000 AND tgl_terakhir = '$r->tgl'
)

-- JOIN kembali untuk hitung box_pengiriman unik
SELECT 
  g.*,
  COUNT(DISTINCT gl.box_pengiriman) AS jumlah_box_didalamnya
FROM grup_valid g
JOIN grup_lengkap gl
  ON g.nm_partai = gl.nm_partai 
 AND g.grade = gl.grade 
 AND g.grup_ke = gl.grup_ke
GROUP BY 
  g.nm_partai,
  g.grade,
  g.grup_ke,
  g.tgl_terakhir,
  g.total_pcs,
  g.total_gr
ORDER BY g.grade DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function coba_steaming(Request $request)
    {
        $query = DB::table('grading_partai')
            ->select('nm_partai', 'tgl', 'gr', 'pcs', 'id_grading')
            ->orderBy('tgl')
            ->orderBy('id_grading');

        if ($request->has('tgl')) {
            $query->whereDate('tgl', $request->tgl);
        }

        $results = $query->get();

        $batches = collect();
        $currentTanggal = null;
        $currentBatchGr = 0;
        $currentBatchPcs = 0;
        $currentBatchPartai = [];

        foreach ($results as $row) {
            if ($currentTanggal !== $row->tgl) {
                if ($currentBatchGr > 0) {
                    $batches->push([
                        'nm_partai' => implode(', ', $currentBatchPartai),
                        'tgl'       => $currentTanggal,
                        'gr'        => $currentBatchGr,
                        'pcs'       => $currentBatchPcs
                    ]);
                }
                $currentTanggal = $row->tgl;
                $currentBatchGr = 0;
                $currentBatchPcs = 0;
                $currentBatchPartai = [];
            }

            $remainingGr = $row->gr;
            $remainingPcs = $row->pcs;

            while ($remainingGr > 0) {
                $space = 1000 - $currentBatchGr;

                if (!in_array($row->nm_partai, $currentBatchPartai)) {
                    $currentBatchPartai[] = $row->nm_partai;
                }

                if ($remainingGr >= $space) {
                    // Hitung proporsi pcs yang masuk batch
                    $pcsToAdd = round($remainingPcs * ($space / $remainingGr), 2);

                    $currentBatchGr += $space;
                    $currentBatchPcs += $pcsToAdd;

                    $batches->push([
                        'nm_partai' => implode(', ', $currentBatchPartai),
                        'tgl'       => $currentTanggal,
                        'gr'        => $currentBatchGr,
                        'pcs'       => $currentBatchPcs
                    ]);

                    // Reset batch
                    $currentBatchGr = 0;
                    $currentBatchPcs = 0;
                    $currentBatchPartai = [];

                    // Kurangi sisa
                    $remainingGr -= $space;
                    $remainingPcs -= $pcsToAdd;
                } else {
                    // Semua sisa masuk batch
                    $currentBatchGr += $remainingGr;
                    $currentBatchPcs += $remainingPcs;
                    $remainingGr = 0;
                    $remainingPcs = 0;
                }
            }
        }

        if ($currentBatchGr > 0) {
            $batches->push([
                'nm_partai' => implode(', ', $currentBatchPartai),
                'tgl'       => $currentTanggal,
                'gr'        => $currentBatchGr,
                'pcs'       => $currentBatchPcs
            ]);
        }



        return response()->json(
            [
                'status' => 'success',
                'message' => 'success',
                'data' => $batches
            ]
        );
    }


    public function pengiriman_bulan(Request $r)
    {
        $data = DB::select("SELECT MONTH(a.tgl) as bulan , YEAR(a.tgl) as tahun
        FROM pengiriman_packing_list as a 
        group by MONTH(a.tgl) , YEAR(a.tgl)
        ORDER by a.tgl DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function pengiriman_bulan_detaiil(Request $r)
    {
        $data = DB::select("SELECT a.tgl,`tujuan`
        FROM pengiriman_packing_list as a 
        where MONTH(a.tgl) = '$r->bulan' and YEAR(a.tgl) = '$r->tahun'
        group by a.tgl
        ORDER by a.tgl ASC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
}
