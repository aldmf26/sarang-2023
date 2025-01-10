<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CostGlobalModel extends Model
{
    use HasFactory;

    public static function costGlobal()
    {
        return DB::select("SELECT a.*, b.cost_op
FROM (
SELECT a.nm_partai, a.tgl, b.nm_partai_dulu,  b.grade, sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk,  sum(a.hrga_satuan * a.gr_awal) as cost_bk, b.bulan, b.tahun, sum(COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0)) as cabut, sum(e.ttl_rp) as cetak, sum(f.ttl_rp) as sortir, sum(g.ttl_rp) as cu
        FROM bk as a 
        left join bk_awal as b on b.nm_partai = a.nm_partai
        left join cabut as c on c.no_box = a.no_box
        left join eo as d on d.no_box = a.no_box
        left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp 
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori = 'CTK'
            group by a.no_box
        ) as e on e.no_box = a.no_box
        left join sortir as f on f.no_box = a.no_box
        left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp 
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori = 'CU'
            group by a.no_box
        ) as g on g.no_box = a.no_box
        where a.baru = 'baru' and a.kategori ='cabut' and a.no_box != 9999 
        group by a.nm_partai
        order by b.tahun ASC, b.bulan ASC, a.nm_partai ASC
) as a

left join (
	SELECT h.nm_partai, sum(h.cost_op) as cost_op
    FROM grading_partai as h 
    group by h.nm_partai
) as b on b.nm_partai = a.nm_partai;");
    }
}
