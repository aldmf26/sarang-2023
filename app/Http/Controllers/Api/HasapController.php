<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SummaryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasapController extends Controller
{
    public function index()
    {
        $data = DB::select("SELECT a.tgl_terima as tgl, c.nm_partai, b.id,  b.name, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr_awal
        FROM cabut as a 
        left join users as b on b.id = a.id_pengawas
        left join bk as c on c.no_box = a.no_box and c.kategori = 'cabut'
        where c.baru = 'baru'
        group by a.tgl_terima , b.name
        UNION ALL
        SELECT d.tgl_ambil as tgl, f.nm_partai, e.id, e.name, 0 as pcs, sum(d.gr_eo_awal) as gr_awal
        FROM eo as d
        left join users as e on e.id = d.id_pengawas
        left join bk as f on f.no_box = d.no_box and f.kategori = 'cabut'
        where f.baru = 'baru'
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
        $data = DB::select("SELECT a.tgl_terima as tgl, a.no_box, c.nm_partai, d.nama,  sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr_awal
        FROM cabut as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join bk as c on c.no_box = a.no_box and c.kategori = 'cabut'
        left join hasil_wawancara as d on d.id_anak = b.id_anak
        where c.baru = 'baru' and a.id_pengawas = '$id_pengawas' and a.tgl_terima = '$tgl'
        group by a.no_box
UNION ALL
SELECT d.tgl_ambil as tgl, d.no_box, f.nm_partai, g.nama, 0 as pcs, sum(d.gr_eo_awal) as gr_awal
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

        $data = DB::select("SELECT  a.id_anak, a.no_box, c.tipe, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir, d.batas_susut,
        c.nm_partai,e.nama, f.name, a.tgl_serah as tgl, a.id_pengawas
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
        where c.baru = 'baru'  and a.selesai = 'Y'
        group by a.id_pengawas, a.tgl_serah

        UNION ALL 


        SELECT  b.id_anak, a.no_box, c.tipe, 0 as pcs , sum(a.gr_eo_awal) as gr_awal, 0 as pcs_akhir, sum(a.gr_eo_akhir) as gr_akhir, 100 as batas_susut, c.nm_partai, e.nama, f.name, a.tgl_serah as tgl,a.id_pengawas
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
        where c.baru = 'baru' and  a.selesai = 'Y'
        group by a.id_pengawas, a.tgl_serah

        order by tgl DESC
        
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
        c.nm_partai,e.nama, f.name, a.tgl_serah as tgl
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


        SELECT  b.id_anak, a.no_box, c.tipe, 0 as pcs , a.gr_eo_awal, 0 as pcs_akhir, a.gr_eo_akhir, 100 as batas_susut, c.nm_partai, e.nama, f.name, a.tgl_serah as tgl
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
        where b.kategori = 'CTK' and a.selesai ='Y'
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

        $data = DB::select("SELECT a.tgl, a.nm_partai, sum(a.pcs) as pcs, sum(a.gr) as gr
        FROM grading_partai as a 
        group by a.tgl, a.nm_partai
        order by a.tgl DESC;");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
    public function pengiriman_akhir(Request $r)
    {
        if (empty($r->tgl)) {
            $tgl = date('Y-m-d');
        } else {
            $tgl = $r->tgl;
        }

        $data = DB::select("SELECT a.no_box, a.grade, a.pcs, a.gr, b.tgl, a.no_nota, a.no_barcode, a.tgl_input
        FROM pengiriman as a 
        join (
        select no_nota,kadar,nm_packing,tujuan,tgl from pengiriman_packing_list GROUP BY no_nota 
        ) as b on a.no_nota = b.no_nota
        left join (
                    SELECT b.box_pengiriman , sum(b.cost_bk) as cost_bk, sum(b.cost_op) as cost_op, sum(b.cost_kerja) as cost_kerja, sum(b.cost_cu) as cost_cu, max(b.bulan) as bulan , max(b.tahun) as tahun
                    FROM grading_partai as b 
                    where b.sudah_kirim = 'Y'
                    group by b.box_pengiriman
        ) as d on d.box_pengiriman = a.no_box
        where b.tgl = '$tgl'
        GROUP by a.no_box;");
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
}
