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
        $data = DB::select("SELECT c.no_invoice, c.tanggal, b.name, sum(c.pcs_awal) as pcs, sum(c.gr_awal) as gr_awal
        FROM cabut as a 
        left join users as b on b.id = a.id_pengawas
        join (
            SELECT c.no_invoice, c.no_box, c.pcs_awal, c.gr_awal, c.tanggal
            FROM formulir_sarang as c 
            where c.kategori ='cabut'
        ) as c on c.no_box = a.no_box
        group by c.no_invoice
        order by c.no_invoice DESC");
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ], 200);
    }

    public function detail($no_invoice)
    {
        $data = DB::select("SELECT d.nama, c.no_invoice, a.no_box, e.tipe, a.pcs_awal, a.gr_awal
        FROM cabut as a 
        left join users as b on b.id = a.id_pengawas
        left join tb_anak as d on d.id_anak = a.id_anak
        join (
            SELECT c.no_invoice, c.no_box, c.pcs_awal, c.gr_awal, c.tanggal
            FROM formulir_sarang as c 
            where c.kategori ='cabut'
            group by c.no_box
        ) as c on c.no_box = a.no_box
        left join (
            SELECT e.no_box, e.tipe
            FROM bk as e
            where e.kategori = 'cabut'
            group by e.no_box
        ) as e on e.no_box = a.no_box
        where c.no_invoice='$no_invoice';");

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
        if (empty($r->tgl)) {
            $tgl = date('Y-m-d');
        } else {
            $tgl = $r->tgl;
        }
        $data = DB::select("SELECT b.nama, a.id_anak, a.no_box, c.tipe, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, d.batas_susut
        FROM cabut as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        join (
        SELECT e.no_box, e.tipe, e.baru
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        left join tb_kelas as d on d.id_kelas = a.id_kelas
        where c.baru = 'baru' and a.tgl_terima = '$tgl' and a.selesai = 'Y'

        UNION ALL 


        SELECT b.nama, b.id_anak, a.no_box, c.tipe, 0 as pcs , a.gr_eo_awal as gr_awal, 0 as pcs_akhir, a.gr_eo_akhir as gr_akhir, 100 as batas_susut
        FROM eo as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        join (
        SELECT e.no_box, e.tipe, e.baru
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        where c.baru = 'baru' and a.tgl_ambil = '$tgl' and a.selesai = 'Y';");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }

    public function cetak(Request $r)
    {
        if (empty($r->tgl)) {
            $tgl = date('Y-m-d');
        } else {
            $tgl = $r->tgl;
        }
        $data = DB::select("SELECT c.nama, a.no_box, d.tipe, a.pcs_awal_ctk, a.gr_awal_ctk, (COALESCE(a.pcs_tdk_cetak,0) + COALESCE(a.pcs_akhir)) as pcs_akhir, (COALESCE(a.gr_tdk_cetak,0) + COALESCE(a.gr_akhir,0)) as gr_akhir
        FROM cetak_new as a
        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
        left join tb_anak as c on c.id_anak = a.id_anak
        LEFT join (
        SELECT d.no_box , d.tipe
            FROM bk as d 
            where d.kategori ='Cabut'
        ) as d on d.no_box = a.no_box
        where a.tgl = '$tgl' and b.kelas = 'CTK' and a.selesai ='Y';");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }


    public function grading(Request $r)
    {
        if (empty($r->tgl)) {
            $tgl = date('Y-m-d');
        } else {
            $tgl = $r->tgl;
        }

        $data = DB::select("SELECT a.grade, sum(a.pcs) as pcs, sum(a.gr) as gr , count(a.box_pengiriman) as box
        FROM (
            SELECT a.grade, sum(a.pcs) as pcs, sum(a.gr) as gr, a.box_pengiriman
            FROM grading_partai as a 
            where a.tgl = '$tgl'
            group by a.grade, a.box_pengiriman
        ) as a 
        group by a.grade;");
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
        $data = DB::select("SELECT b.nama, a.id_anak, a.no_box, c.tipe, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, d.batas_susut
        FROM cabut as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        join (
        SELECT e.no_box, e.tipe, e.baru
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        left join tb_kelas as d on d.id_kelas = a.id_kelas
        where c.baru = 'baru' and MONTH(a.tgl_terima) = '$bulan' and a.selesai = 'Y'

        UNION ALL 


        SELECT b.nama, b.id_anak, a.no_box, c.tipe, 0 as pcs , a.gr_eo_awal as gr_awal, 0 as pcs_akhir, a.gr_eo_akhir as gr_akhir, 100 as batas_susut
        FROM eo as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        join (
        SELECT e.no_box, e.tipe, e.baru
        FROM bk as e
        where e.kategori = 'cabut'
        group by e.no_box
        ) as c on c.no_box = a.no_box
        where c.baru = 'baru' and MONTH(a.tgl_ambil) = '$bulan' and a.selesai = 'Y';");

        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'data' => $data
        ]);
    }
}
