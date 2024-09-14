<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OpnameNewModel extends Model
{
    use HasFactory;
    public static function bksisapgws()
    {
        $result = DB::select("SELECT a.no_box, b.name, a.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, 
        sum(a.gr_awal * a.hrga_satuan) as ttl_rp
            FROM bk as a 
            left join users as b on b.id = a.penerima
            where a.kategori ='cabut' and a.baru ='baru' 
            AND NOT EXISTS (SELECT 1 FROM cabut AS b WHERE b.no_box = a.no_box) 
            AND NOT EXISTS (SELECT 1 FROM eo AS c WHERE c.no_box = a.no_box)
            AND a.baru = 'baru'
            group by a.no_box
            order by b.name ASC
            ");
        return $result;
    }

    public static function bksedang_proses_sum()
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr ,sum(b.gr_awal * b.hrga_satuan) as ttl_rp, sum(if(a.ttl_rp < 0 , 0 , a.ttl_rp)) as cost_kerja, c.name
    FROM cabut as a
    LEFT JOIN bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
    left join users as c on c.id = a.id_pengawas
    WHERE a.selesai = 'T' AND a.no_box != 9999 and b.baru = 'baru'
    group by a.no_box
    
    UNION ALL
    
    SELECT d.no_box, e.nm_partai, 0 as pcs, sum(d.gr_eo_awal) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp, sum(if(d.ttl_rp < 0 , 0 , d.ttl_rp)) as cost_kerja,c.name
    FROM eo as d
    LEFT JOIN bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
    left join users as c on c.id = d.id_pengawas
    WHERE d.selesai = 'T' AND d.no_box != 9999 and e.baru = 'baru'
    group by d.no_box

   

    ");

        return $result;
    }
    public static function bksedang_selesai_sum()
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr ,sum(b.gr_awal * b.hrga_satuan) as ttl_rp, sum(a.ttl_rp) as cost_kerja, c.name
    FROM cabut as a
    LEFT JOIN bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
    left join users as c on c.id = a.id_pengawas
    WHERE a.selesai = 'Y' and a.no_box not in(SELECT a.no_box FROM formulir_sarang as a group by a.no_box) AND a.no_box != 9999 and b.baru = 'baru'
    group by a.no_box
    
    UNION ALL
    
    SELECT d.no_box, e.nm_partai, 0 as pcs, sum(d.gr_eo_awal) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp, sum(d.ttl_rp) as cost_kerja,c.name
    FROM eo as d
    LEFT JOIN bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
    left join users as c on c.id = d.id_pengawas
    WHERE d.selesai = 'Y' and d.no_box not in(SELECT a.no_box FROM formulir_sarang as a group by a.no_box) AND d.no_box != 9999 and e.baru = 'baru'
    group by d.no_box

   

    ");

        return $result;
    }


    public static function cetak_stok()
    {
        $result = DB::select("SELECT e.name, a.no_box, c.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(c.hrga_satuan  * c.gr_awal) as ttl_rp, sum(d.ttl_rp) as cost_kerja
        FROM formulir_sarang as a 
        left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
        left join cabut as d on d.no_box = a.no_box
        left join users as e on e.id = a.id_penerima
        WHERE a.kategori = 'cetak'   
        and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 0) and a.no_box != 0
        group by a.no_box
        order by e.name ASC
        ");

        return $result;
    }
    public static function cetak_proses()
    {
        $result = DB::select("SELECT a.no_box, d.nm_partai, sum(a.pcs_awal_ctk) as pcs, sum(a.gr_awal_ctk) as gr, sum(d.gr_awal * d.hrga_satuan) as ttl_rp, sum(c.ttl_rp) as cost_kerja , e.name
            FROM cetak_new as a 
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            left join cabut as c on c.no_box = a.no_box
            left join users as e on e.id = a.id_pengawas
            where a.selesai = 'T' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            group by a.no_box
            order by e.name ASC;
        ");

        return $result;
    }
    public static function cetak_selesai()
    {
        $result = DB::select("SELECT a.no_box, d.nm_partai, sum(a.pcs_awal_ctk) as pcs, sum(a.gr_awal_ctk) as gr, sum(d.gr_awal * d.hrga_satuan) as ttl_rp, sum(COALESCE(c.ttl_rp,0) + COALESCE(a.ttl_rp,0)) as cost_kerja , e.name
            FROM cetak_new as a 
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            left join cabut as c on c.no_box = a.no_box
            left join users as e on e.id = a.id_pengawas
            where a.selesai = 'Y' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            and a.no_box not in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'sortir')
            group by a.no_box
            order by e.name ASC;
        ");

        return $result;
    }
}
