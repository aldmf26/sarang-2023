<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Grading extends Model
{
    public static function dapatkanStokBox($jenis, $noBox = null)
    {
        $whereBox = $noBox ? "AND b.no_box in ($noBox) " : '';
        $whereBoxPartai = $noBox ? "AND e.nm_partai = '$noBox'  " : '';
        $groupBoxPartai = $noBox ? ",b.no_box" : '';

        $formulir = DB::select("SELECT 
        b.no_box, b.tanggal, e.tipe,e.nm_partai, c.name as pemberi, b.no_invoice, (b.pcs_awal - d.pcs) as pcs_awal, (b.gr_awal - d.gr) as gr_awal
        FROM grading as a 
        JOIN formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade'
        JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'cabut'
        $whereBox
        LEFT JOIN(
            select no_box_sortir as no_box,sum(pcs) as pcs,sum(gr) as gr
            from grading 
            group by no_box_sortir
        ) as d on d.no_box = b.no_box
        JOIN users as c on c.id = b.id_pemberi
        WHERE a.selesai  = 'T'
        GROUP BY b.no_box
        HAVING sum(b.pcs_awal - d.pcs) > 0 OR sum(b.gr_awal - d.gr) > 0
        ORDER BY b.tanggal DESC");

        $formulirGroupBy = DB::select("SELECT 
        e.nm_partai,
        count(b.no_box) as no_box,
        b.no_box as no_box_sortir,
        b.tanggal,
        e.tipe,
        c.name as pemberi,
        b.no_invoice,
        SUM(b.pcs_awal) - SUM(d.pcs) as pcs_awal,
        SUM(b.gr_awal) - SUM(d.gr) as gr_awal
        FROM grading as a 
        JOIN formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade'
        JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'cabut'
        LEFT JOIN(
            select no_box_sortir as no_box, SUM(pcs) as pcs, SUM(gr) as gr
            from grading 
            group by no_box_sortir
        ) as d on d.no_box = b.no_box
        JOIN users as c on c.id = b.id_pemberi
        WHERE a.selesai  = 'T' $whereBoxPartai
        GROUP BY e.nm_partai $groupBoxPartai
        HAVING SUM(b.pcs_awal - d.pcs) > 0 OR SUM(b.gr_awal - d.gr) > 0
        ORDER BY b.tanggal DESC");

        $arr = [
            'formulir' => $formulir,
            'formulirGroupBy' => $formulirGroupBy,
            'pengawas' => DB::table('users')->where('posisi_id', 13)->get()
        ];
        return $arr[$jenis];
    }

    public static function siapKirim()
    {
        $gudang = DB::select(
            "SELECT b.nm_grade as grade,b.id_grade,a.selesai, a.no_invoice, a.no_box_grading as no_box, sum(a.pcs) as pcs, sum(a.gr) as gr, c.pcs as pcs_pengiriman, c.gr as gr_pengiriman
            FROM `grading` as a
            left JOIN tb_grade as b on a.id_grade = b.id_grade
            LEFT JOIN (
                select no_box, sum(pcs) as pcs,sum(gr) as gr from pengiriman group by no_box
            ) as c on c.no_box = a.no_box_grading
            WHERE a.id_grade is not null
            GROUP BY a.no_box_grading 
            ORDER BY a.no_box_grading ASC"
        );
        return $gudang;
    }

    public static function gudangPengirimanGr($no_box = null)
    {
        $whereBox = $no_box ? "AND g.no_box = $no_box " : '';
        $select = $no_box ? 'selectOne' : 'select';
        return DB::$select("WITH
                sortir_data AS (
                SELECT 
                    a.no_box, 
                    (
                    (
                        (a.hrga_satuan * a.gr_awal) + COALESCE(b.ttl_rp, 0) + COALESCE(c.ttl_rp, 0) + COALESCE(d.ttl_rp, 0)
                    ) / COALESCE(d.gr_akhir, 1)
                    ) as rp_gram_str 
                FROM 
                    bk as a 
                    LEFT JOIN cabut as b ON b.no_box = a.no_box 
                    LEFT JOIN (
                    SELECT 
                        c.no_box, 
                        c.gr_akhir, 
                        c.ttl_rp 
                    FROM 
                        cetak_new as c 
                        LEFT JOIN kelas_cetak as d ON d.id_kelas_cetak = c.id_kelas_cetak 
                        LEFT JOIN oprasional as e ON e.bulan = c.bulan_dibayar 
                    WHERE 
                        d.kategori = 'CTK'
                    ) as c ON c.no_box = a.no_box 
                    LEFT JOIN sortir as d ON d.no_box = a.no_box 
                WHERE 
                    a.kategori = 'cabut' 
                    AND a.baru = 'baru' 
                GROUP BY 
                    a.no_box
                ), 
            
                grading_total AS (
                SELECT 
                    GROUP_CONCAT(a.no_box_sortir) as no_box_sortir, 
                    b.nm_grade as grade, 
                    b.id_grade, 
                    a.selesai, 
                    a.no_invoice, 
                    a.no_box_grading as no_box, 
                    SUM(a.pcs) as pcs, 
                    SUM(a.gr) as gr, 
                    c.pcs as pcs_pengiriman, 
                    c.gr as gr_pengiriman, 
                    SUM(sd.rp_gram_str) AS total_rp_gram_str 
                FROM 
                    grading as a 
                    LEFT JOIN tb_grade as b ON a.id_grade = b.id_grade 
                    LEFT JOIN (
                    SELECT 
                        no_box, 
                        SUM(pcs) as pcs, 
                        SUM(gr) as gr 
                    FROM 
                        pengiriman 
                    GROUP BY 
                        no_box
                    ) as c ON c.no_box = a.no_box_grading 
                    sd.no_box, a.no_box_sortir) 
                WHERE 
                    a.id_grade IS NOT NULL 
                GROUP BY 
                    a.no_box_grading 
                ORDER BY 
                    a.no_box_grading ASC
                ) -- Mengambil hasil akhir
                SELECT 
                g.no_box_sortir, 
                g.grade, 
                g.id_grade, 
                g.selesai, 
                g.no_invoice, 
                g.no_box, 
                g.pcs, 
                g.gr, 
                g.pcs_pengiriman, 
                g.gr_pengiriman, 
                g.total_rp_gram_str 
                FROM 
                grading_total g
                WHERE 
                NOT EXISTS (
                    SELECT 1
                    FROM pengiriman p
                    WHERE p.no_box = g.no_box
                ) $whereBox
                ");
    }

    public static function grading_stock()
    {
        $result = DB::select("SELECT h.name as pemilik, i.name as penerima, c.nm_partai, a.no_box_sortir, b.pcs_awal, b.gr_awal, (b.pcs_awal - j.pcs_ambil) as pcs, (COALESCE(b.gr_awal,0) - COALESCE(j.gr_ambil,0) - COALESCE(k.gr,0)) as gr,
        (c.gr_awal * c.hrga_satuan) as cost_bk,
                d.ttl_rp as cost_cbt, e.ttl_rp as cost_eo, f.ttl_rp as cost_ctk, g.ttl_rp as cost_str
                FROM grading as a 
                left join formulir_sarang as b on b.no_box = a.no_box_sortir and b.kategori ='grade'
                left join bk as c on c.no_box = a.no_box_sortir and c.kategori = 'cabut'
                left join cabut as d on d.no_box =  a.no_box_sortir
                left join eo as e on e.no_box = a.no_box_sortir
                left join cetak_new as f on f.no_box = a.no_box_sortir
                left join sortir as g on g.no_box = a.no_box_sortir
                left join users as h on h.id = c.penerima
                left join users as i on i.id = b.id_penerima
                
                left join (
                    SELECT j.no_box_sortir, sum(j.pcs) as pcs_ambil, sum(j.gr) as gr_ambil
                    FROM grading as j 
                    GROUP by j.no_box_sortir 
                ) as j on j.no_box_sortir = a.no_box_sortir
                left join grading_selisih as k on k.no_box = a.no_box_sortir
                where a.gr = 0 and (COALESCE(b.gr_awal,0) - COALESCE(j.gr_ambil,0) - COALESCE(k.gr,0)) != 0
                group by a.no_box_sortir;");

        return $result;
    }

    public static function gradingbox()
    {
        $result = DB::select("SELECT h.name as pemilik, i.name as penerima, a.no_box_sortir, a.no_box_grading, b.pcs_awal, b.gr_awal, sum(a.pcs) as pcs_grading, sum(a.gr) as gr_grading,
        sum(round((((c.gr_awal * c.hrga_satuan) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) + COALESCE(g.ttl_rp,0)) / b.gr_awal),0) * a.gr) as ttl_rp,
        j.nm_grade, sum(c.gr_awal * c.hrga_satuan) as cost_bk
        FROM grading as a 
        left join formulir_sarang as b on b.no_box = a.no_box_sortir and b.kategori ='grade'
        left join bk as c on c.no_box = a.no_box_sortir and c.kategori = 'cabut'
        left join cabut as d on d.no_box =  a.no_box_sortir
        left join eo as e on e.no_box = a.no_box_sortir
        left join (
        	SELECT d.no_box, d.ttl_rp 
            FROM cetak_new as d 
            left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
            where h.kategori = 'CTK'
        ) as f on f.no_box = a.no_box_sortir
        left join sortir as g on g.no_box = a.no_box_sortir
        left join users as h on h.id = c.penerima
        left join users as i on i.id = b.id_penerima
        left join tb_grade as j on j.id_grade = a.id_grade
        where a.gr != 0 and a.no_box_grading not in(SELECT k.no_box FROM pengiriman as k)
        group by a.no_box_grading;");

        return $result;
    }
    public static function gradingboxkirim()
    {
        $result = DB::select("SELECT h.name as pemilik, i.name as penerima, a.no_box_sortir, a.no_box_grading, b.pcs_awal, b.gr_awal, sum(a.pcs) as pcs_grading, sum(a.gr) as gr_grading,
        sum(round((((c.gr_awal * c.hrga_satuan) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) + COALESCE(g.ttl_rp,0)) / b.gr_awal),0) * a.gr) as ttl_rp,
        j.nm_grade
        FROM grading as a 
        left join formulir_sarang as b on b.no_box = a.no_box_sortir and b.kategori ='grade'
        left join bk as c on c.no_box = a.no_box_sortir and c.kategori = 'cabut'
        left join cabut as d on d.no_box =  a.no_box_sortir
        left join eo as e on e.no_box = a.no_box_sortir
        left join (
        	SELECT d.no_box, d.ttl_rp 
            FROM cetak_new as d 
            left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
            where h.kategori = 'CTK'
        ) as f on f.no_box = a.no_box_sortir
        left join sortir as g on g.no_box = a.no_box_sortir
        left join users as h on h.id = c.penerima
        left join users as i on i.id = b.id_penerima
        left join tb_grade as j on j.id_grade = a.id_grade
        where a.gr != 0 and a.no_box_grading in(SELECT k.no_box FROM pengiriman as k)
        group by a.no_box_grading;");

        return $result;
    }
}
