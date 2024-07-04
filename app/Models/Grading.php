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
        $formulir = DB::select("SELECT 
        b.no_box, b.tanggal, e.tipe, c.name as pemberi, b.no_invoice, (b.pcs_awal - d.pcs) as pcs_awal, (b.gr_awal - d.gr) as gr_awal
        FROM grading as a 
        JOIN formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade'
        JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'sortir'
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

        $arr = [
            'formulir' => $formulir,
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
                    LEFT JOIN sortir_data sd ON FIND_IN_SET(sd.no_box, a.no_box_sortir) 
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
}
