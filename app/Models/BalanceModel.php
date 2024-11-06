<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BalanceModel extends Model
{
    use HasFactory;

    public static function cabut($bulan,$tahun)
    {
        return DB::selectOne("SELECT 
        bulan_dibayar,
        SUM(cost) AS cost,
        SUM(pcs) AS pcs,
        SUM(gr) AS gr,
        SUM(ttl_rp) AS ttl_rp
        FROM (
        SELECT 
            a.ttl_rp AS cost, 
            a.pcs_akhir AS pcs, 
            a.gr_akhir AS gr, 
            (b.hrga_satuan * b.gr_awal) AS ttl_rp, 
            a.bulan_dibayar 
        FROM 
            cabut AS a 
            LEFT JOIN bk AS b ON b.no_box = a.no_box 
            AND b.kategori = 'cabut' 
        WHERE 
            a.selesai = 'Y' 
            AND b.baru = 'baru' 
            AND a.pcs_awal != 0 
            AND a.bulan_dibayar = $bulan

        UNION ALL 

        SELECT 
            a.ttl_rp AS cost, 
            0 AS pcs, 
            a.gr_eo_akhir AS gr, 
            (b.hrga_satuan * b.gr_awal) AS ttl_rp, 
            a.bulan_dibayar 
        FROM 
            eo AS a 
            LEFT JOIN bk AS b ON b.no_box = a.no_box 
            AND b.kategori = 'cabut' 
            AND a.bulan_dibayar = $bulan
        WHERE 
            a.selesai = 'Y' 
            AND b.baru = 'baru'

        UNION ALL 

        SELECT 
            a.ttl_rp AS cost, 
            a.pcs_akhir AS pcs, 
            a.gr_akhir AS gr, 
            (b.hrga_satuan * b.gr_awal) AS ttl_rp, 
            a.bulan_dibayar 
        FROM 
            cabut AS a 
            LEFT JOIN bk AS b ON b.no_box = a.no_box 
            AND b.kategori = 'cabut' 
        WHERE 
            a.selesai = 'Y' 
            AND b.baru = 'baru' 
            AND a.pcs_awal = 0
            AND a.bulan_dibayar = $bulan
        ) AS combined_data
        GROUP BY bulan_dibayar;");
    }

    public static function cetak($bulan,$tahun)
    {
        
        return DB::selectOne("SELECT 
        sum(COALESCE(a.pcs_akhir,0) + COALESCE(a.pcs_tdk_cetak,0)) as pcs, 
        sum(COALESCE(a.gr_akhir,0) + COALESCE(a.gr_tdk_cetak,0)) as gr, 
        sum(COALESCE(e.gr_awal * e.hrga_satuan,0) + COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0)) as ttl_rp, 
        sum(a.ttl_rp) as cost_kerja
        FROM cetak_new as a 
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        join formulir_sarang as b on b.no_box = a.no_box and b.kategori = 'cetak'
        left join cabut as c on c.no_box = a.no_box
        left join eo as d on d.no_box = a.no_box
        where a.selesai = 'Y' and g.kategori = 'CTK' and e.baru = 'baru' and a.bulan_dibayar = $bulan;");
    }
    public static function sortir($bulan,$tahun)
    {
        
        return DB::selectOne("SELECT SUM(a.pcs_akhir) as pcs, SUM(a.gr_akhir) as gr, 
        SUM(COALESCE(b.hrga_satuan * b.gr_awal,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0)) as ttl_rp,
        sum(a.ttl_rp) as cost_kerja
                    FROM sortir as a 
                    LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                    JOIN formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'

                    left join cabut as d on d.no_box = a.no_box
                    left join eo as e on e.no_box = a.no_box
        
                    left join(
                    SELECT a.no_box, sum(a.ttl_rp) as ttl_rp
                                FROM cetak_new as a 
                                left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                                where b.kategori = 'CTK'
                                group by a.no_box
                    ) as f on f.no_box = a.no_box
                    WHERE  a.bulan = $bulan and a.selesai = 'Y' AND b.baru = 'baru' and a.no_box in (SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'grade');
        ");
    }

    public static function grading($bulan,$tahun = 2024)
    {
        return DB::select("SELECT a.bulan,a.tahun,a.box_pengiriman as box_grading, sum(pcs) as pcs,sum(gr) as gr,sum(a.cost_bk) as cost_bk, sum(a.cost_op) as cost_op,a.grade FROM `grading_partai` as a
            WHERE a.bulan = $bulan and a.tahun = $tahun
            GROUP BY a.bulan,a.grade;");
    }

}
