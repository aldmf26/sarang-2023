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
        $result = DB::select("SELECT  c.name, a.no_box, b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_akhir) as gr ,sum(b.gr_awal * b.hrga_satuan) as ttl_rp, 
        sum(a.ttl_rp) as cost_kerja, 
        sum(a.gr_akhir * d.rp_gr) as cost_op, 
        sum(a.gr_akhir * e.rp_gr) as cost_dll
    FROM cabut as a
    LEFT JOIN bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
    left join users as c on c.id = a.id_pengawas
    left join oprasional as d on d.bulan = a.bulan_dibayar
    left join cost_dll_cu_denda as e on e.bulan_dibayar = a.bulan_dibayar
    WHERE a.selesai = 'Y' and a.formulir = 'Y' and a.no_box not in(SELECT a.no_box FROM formulir_sarang as a group by a.no_box) AND a.no_box != 9999 and b.baru = 'baru'
    group by a.no_box
    
    UNION ALL
    
    SELECT c.name, d.no_box, e.nm_partai, 0 as pcs, sum(d.gr_eo_akhir) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp,

    sum(d.ttl_rp) as cost_kerja,
    sum(d.gr_eo_akhir * f.rp_gr) as cost_op, 
    sum(d.gr_eo_akhir * g.rp_gr) as cost_dll

    FROM eo as d
    LEFT JOIN bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
    left join users as c on c.id = d.id_pengawas
    left join oprasional as f on f.bulan = d.bulan_dibayar
    left join cost_dll_cu_denda as g on g.bulan_dibayar = d.bulan_dibayar
    WHERE d.selesai = 'Y' and d.penutup = 'T' and d.no_box not in(SELECT a.no_box FROM formulir_sarang as a group by a.no_box) AND d.no_box != 9999 and e.baru = 'baru'
    group by d.no_box

   

    ");

        return $result;
    }


    public static function cetak_stok()
    {
        $result = DB::select("SELECT e.name, a.no_box, c.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, 
        sum(c.hrga_satuan  * c.gr_awal) as ttl_rp, 
        sum(COALESCE(d.ttl_rp,0) + COALESCE(f.ttl_rp,0)) as cost_kerja, 
        sum(COALESCE(d.gr_akhir * g.rp_gr,0) + COALESCE(f.gr_eo_awal * h.rp_gr,0)) as cost_op, 
        sum(COALESCE(d.gr_awal * i.rp_gr,0) + COALESCE(f.gr_eo_awal * j.rp_gr,0)) as cost_dll,
        sum(d.gr_akhir) as gr_akhir_cabut, sum(f.gr_eo_akhir) as gr_eo_akhir_cabut, sum(z.ttl_rp) as cu
        FROM formulir_sarang as a 
        left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
        left join cabut as d on d.no_box = a.no_box
        left join users as e on e.id = a.id_penerima
        left join eo as f on f.no_box = a.no_box
        left join oprasional as g on g.bulan = d.bulan_dibayar
        left join oprasional as h on h.bulan = f.bulan_dibayar
        left join cost_dll_cu_denda as i on i.bulan_dibayar = d.bulan_dibayar
        left join cost_dll_cu_denda as j on j.bulan_dibayar = f.bulan_dibayar
        left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp, sum((a.gr_akhir + a.gr_tdk_cetak) * i.rp_gr) as cost_op_cetak, sum((a.gr_akhir + a.gr_tdk_cetak) * j.rp_gr) as cost_dll_cetak
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    left join oprasional as i on i.bulan = a.bulan_dibayar
                    left join cost_dll_cu_denda as j on j.bulan_dibayar = a.bulan_dibayar
                    where b.kategori = 'CU'
                    group by a.no_box
        ) as z on z.no_box = a.no_box
        WHERE a.kategori = 'cetak'   
        and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 0) and a.no_box != 0
        group by a.no_box
        order by e.name ASC
        ");

        return $result;
    }
    public static function cetak_proses()
    {
        $result = DB::select("SELECT e.name, a.no_box, d.nm_partai, sum(a.pcs_awal_ctk) as pcs, 
        sum(a.gr_awal_ctk) as gr, 
        sum(d.gr_awal * d.hrga_satuan) as ttl_rp, 

        sum(COALESCE(c.ttl_rp,0) + COALESCE(f.ttl_rp,0)) as cost_kerja , 
        sum(COALESCE(c.gr_akhir * h.rp_gr,0) + COALESCE(f.gr_eo_akhir * i.rp_gr,0)) as cost_op,
        sum(COALESCE(c.gr_akhir * j.rp_gr,0) + COALESCE(f.gr_eo_akhir * k.rp_gr,0)) as cost_dll,
        sum(c.gr_akhir) as gr_akhir_cabut, sum(f.gr_eo_akhir) as gr_eo_akhir_cabut,sum(z.ttl_rp) as cu

            FROM cetak_new as a 
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            left join cabut as c on c.no_box = a.no_box
            left join eo as f on f.no_box = a.no_box
            left join users as e on e.id = a.id_pengawas
            left join oprasional as h on h.bulan = c.bulan_dibayar
            left join oprasional as i on i.bulan = f.bulan_dibayar
            left join cost_dll_cu_denda as j on j.bulan_dibayar = c.bulan_dibayar
            left join cost_dll_cu_denda as k on k.bulan_dibayar = f.bulan_dibayar
            left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp, sum((a.gr_akhir + a.gr_tdk_cetak) * i.rp_gr) as cost_op_cetak, sum((a.gr_akhir + a.gr_tdk_cetak) * j.rp_gr) as cost_dll_cetak
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    left join oprasional as i on i.bulan = a.bulan_dibayar
                    left join cost_dll_cu_denda as j on j.bulan_dibayar = a.bulan_dibayar
                    where b.kategori = 'CU'
                    group by a.no_box
        ) as z on z.no_box = a.no_box
            where a.selesai = 'T' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            group by a.no_box
            order by e.name ASC
        ");

        return $result;
    }

    public static function cetak_selesai()
    {
        $result = DB::select("SELECT a.no_box, d.nm_partai, sum(a.pcs_awal_ctk) as pcs, sum(a.gr_awal_ctk) as gr, sum(d.gr_awal * d.hrga_satuan) as ttl_rp,  e.name, 
        sum(COALESCE(c.ttl_rp,0) + COALESCE(a.ttl_rp,0) + COALESCE(f.ttl_rp,0)) as cost_kerja ,
        sum(COALESCE(c.gr_akhir * h.rp_gr,0) + COALESCE(a.gr_akhir * i.rp_gr,0) + COALESCE(f.gr_eo_akhir * j.rp_gr)) as cost_op,
        sum(COALESCE(a.gr_akhir * k.rp_gr,0) + COALESCE(c.gr_akhir * l.rp_gr,0) + COALESCE(f.gr_eo_akhir * m.rp_gr)) as cost_dll
            FROM cetak_new as a 
            left join oprasional as i on i.bulan = a.bulan_dibayar
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            left join cabut as c on c.no_box = a.no_box
            left join oprasional as h on h.bulan = c.bulan_dibayar
            left join users as e on e.id = a.id_pengawas
            left join eo as f on f.no_box = a.no_box
            left join oprasional as j on j.bulan = f.bulan_dibayar

            left join cost_dll_cu_denda as k on k.bulan_dibayar = a.bulan_dibayar
            left join cost_dll_cu_denda as l on l.bulan_dibayar = c.bulan_dibayar
            left join cost_dll_cu_denda as m on m.bulan_dibayar = f.bulan_dibayar

            where a.selesai = 'Y' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            and a.no_box not in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'sortir')
            group by a.no_box
            order by e.name ASC;
        ");

        return $result;
    }
    public static function sortir_stock()
    {
        $result = DB::select("SELECT a.no_box, f.name, b.nm_partai, SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr, SUM(b.gr_awal * b.hrga_satuan) as ttl_rp, 
        sum(COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0)) as cost_kerja, 
        sum(COALESCE(c.gr_akhir * g.rp_gr,0) + COALESCE(h.rp_gr * d.gr_eo_akhir,0) + COALESCE(e.cost_op_ctk,0)) as cost_op,
        sum(COALESCE(c.gr_akhir * i.rp_gr,0) + COALESCE(j.rp_gr * d.gr_eo_akhir,0) + COALESCE(e.cost_dll_ctk,0)) as cost_dll,
        sum(c.gr_akhir) as gr_akhir_cabut, sum(d.gr_eo_akhir) as gr_eo_akhir_cabut, sum(z.ttl_rp) as cu
        FROM formulir_sarang as a 
        LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join cabut as c on c.no_box = a.no_box
        left join oprasional as g on g.bulan = c.bulan_dibayar
        left join cost_dll_cu_denda as i on i.bulan_dibayar = c.bulan_dibayar
        left join eo as d on d.no_box = a.no_box
        left join oprasional as h on h.bulan = d.bulan_dibayar
        left join cost_dll_cu_denda as j on j.bulan_dibayar = c.bulan_dibayar
        left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp, sum((a.gr_akhir + a.gr_tdk_cetak) * h.rp_gr) as cost_op_ctk, sum((a.gr_akhir + a.gr_tdk_cetak) * j.rp_gr) as cost_dll_ctk
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            		left join oprasional as h on h.bulan = a.bulan_dibayar
                    left join cost_dll_cu_denda as j on j.bulan_dibayar = a.bulan_dibayar
                    where b.kategori = 'CTK' and a.selesai = 'Y'
                    group by a.no_box
        ) as e on e.no_box = a.no_box
        left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp, sum((a.gr_akhir + a.gr_tdk_cetak) * h.rp_gr) as cost_op_ctk, sum((a.gr_akhir + a.gr_tdk_cetak) * j.rp_gr) as cost_dll_ctk
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            		left join oprasional as h on h.bulan = a.bulan_dibayar
                    left join cost_dll_cu_denda as j on j.bulan_dibayar = a.bulan_dibayar
                    where b.kategori = 'CU' and a.selesai = 'Y'
                    group by a.no_box
        ) as z on z.no_box = a.no_box

        left join users as f on f.id = a.id_penerima


        WHERE b.baru = 'baru' AND b.kategori = 'cabut'  
        AND a.kategori = 'sortir' 
        AND a.no_box NOT IN (SELECT b.no_box FROM sortir as b WHERE b.id_anak != 0)

        group by a.no_box
        order by f.name ASC;
        ");

        return $result;
    }

    public static function sortir_proses()
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai, g.name, SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr, SUM(b.hrga_satuan * b.gr_awal) as ttl_rp, 
        sum( COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) ) as cost_kerja,
        sum(COALESCE(h.rp_gr * d.gr_akhir,0) + COALESCE(i.rp_gr * e.gr_eo_akhir,0) + COALESCE(f.cost_op_cetak,0)) as cost_op,
        sum(COALESCE(j.rp_gr * d.gr_akhir,0) + COALESCE(k.rp_gr * e.gr_eo_akhir,0) + COALESCE(f.cost_dll_cetak,0)) as cost_dll, sum(z.ttl_rp) as cu
            FROM sortir as a 
            LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            JOIN formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
            
            left join cabut as d on d.no_box = a.no_box
            left join oprasional as h on h.bulan = d.bulan_dibayar
            left join cost_dll_cu_denda as j on j.bulan_dibayar = d.bulan_dibayar
            
			left join eo as e on e.no_box = a.no_box
            left join cost_dll_cu_denda as k on k.bulan_dibayar = e.bulan_dibayar
            left join oprasional as i on i.bulan = e.bulan_dibayar
left join (
	SELECT a.no_box, sum(a.ttl_rp) as ttl_rp, sum((a.gr_akhir + a.gr_tdk_cetak) * i.rp_gr) as cost_op_cetak, sum((a.gr_akhir + a.gr_tdk_cetak) * j.rp_gr) as cost_dll_cetak
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
    		left join oprasional as i on i.bulan = a.bulan_dibayar
            left join cost_dll_cu_denda as j on j.bulan_dibayar = a.bulan_dibayar
            where b.kategori = 'CTK'
            group by a.no_box
) as f on f.no_box = a.no_box
left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp, sum((a.gr_akhir + a.gr_tdk_cetak) * i.rp_gr) as cost_op_cetak, sum((a.gr_akhir + a.gr_tdk_cetak) * j.rp_gr) as cost_dll_cetak
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    left join oprasional as i on i.bulan = a.bulan_dibayar
                    left join cost_dll_cu_denda as j on j.bulan_dibayar = a.bulan_dibayar
                    where b.kategori = 'CU'
                    group by a.no_box
        ) as z on z.no_box = a.no_box
left join users as g on g.id = a.id_pengawas
            
            WHERE a.selesai = 'T' AND a.id_anak != 0
            
            group by a.no_box
            order by g.name ASC;
        ");

        return $result;
    }
    public static function sortir_selesai()
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai, g.name, SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr, SUM(b.hrga_satuan * b.gr_awal) as ttl_rp, sum(COALESCE(a.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) ) as cost_kerja,

        sum(COALESCE(h.rp_gr * d.gr_akhir,0) + COALESCE(i.rp_gr * e.gr_eo_akhir,0) + COALESCE(f.cost_op_cetak,0) + COALESCE(j.rp_gr * a.gr_akhir,0)) as cost_op,
        sum(COALESCE(k.rp_gr * d.gr_akhir,0) + COALESCE(l.rp_gr * e.gr_eo_akhir,0) + COALESCE(m.rp_gr * a.gr_akhir,0) + COALESCE(f.cost_dll_cetak,0)) as cost_dll, sum(z.ttl_rp) as cu
            FROM sortir as a 
            left join cost_dll_cu_denda as m on m.bulan_dibayar = a.bulan
            left join oprasional as j on j.bulan = a.bulan
            LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            JOIN formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
            
            left join cabut as d on d.no_box = a.no_box
            left join oprasional as h on h.bulan = d.bulan_dibayar
            left join cost_dll_cu_denda as k on k.bulan_dibayar = d.bulan_dibayar
			left join eo as e on e.no_box = a.no_box
            left join oprasional as i on i.bulan = e.bulan_dibayar
            left join cost_dll_cu_denda as l on l.bulan_dibayar = e.bulan_dibayar
left join (
	SELECT a.no_box, sum(a.ttl_rp) as ttl_rp ,sum((a.gr_akhir + a.gr_tdk_cetak) * i.rp_gr) as cost_op_cetak, sum((a.gr_akhir + a.gr_tdk_cetak) * j.rp_gr) as cost_dll_cetak
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
    		left join oprasional as i on i.bulan = a.bulan_dibayar
            left join cost_dll_cu_denda as j on j.bulan_dibayar = a.bulan_dibayar
            where b.kategori = 'CTK'
            group by a.no_box
) as f on f.no_box = a.no_box
left join (
            SELECT a.no_box, sum(a.ttl_rp) as ttl_rp, sum((a.gr_akhir + a.gr_tdk_cetak) * h.rp_gr) as cost_op_ctk, sum((a.gr_akhir + a.gr_tdk_cetak) * j.rp_gr) as cost_dll_ctk
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            		left join oprasional as h on h.bulan = a.bulan_dibayar
                    left join cost_dll_cu_denda as j on j.bulan_dibayar = a.bulan_dibayar
                    where b.kategori = 'CU' and a.selesai = 'Y'
                    group by a.no_box
        ) as z on z.no_box = a.no_box
left join users as g on g.id = a.id_pengawas
            
            WHERE a.no_box not in (SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'grade') and a.selesai = 'Y' and b.baru = 'baru'
            group by a.no_box
            order by g.name ASC;
        ");

        return $result;
    }


    public static function cetak_stok_awal()
    {
        $result = DB::select("SELECT a.no_box, a.nm_partai, a.name, sum(a.pcs) as pcs, sum(a.gr_awal) as gr_awal, sum(a.gr_akhir) as gr_akhir, sum(a.ttl_rp) as ttl_rp, sum(a.cost) as cost_kerja
FROM (
        SELECT a.no_box, b.nm_partai, c.name, a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_awal, a.gr_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
    	left join formulir_sarang as d on d.no_box = a.no_box and d.kategori = 'cetak'
        left join users as c on c.id = d.id_penerima
        where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal != 0

        UNION ALL

        SELECT a.no_box, b.nm_partai, c.name,  a.ttl_rp as cost, 0 as pcs, a.gr_eo_awal as gr_awal, a.gr_eo_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp
        FROM eo as a 
        LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join formulir_sarang as d on d.no_box = a.no_box and d.kategori = 'cetak'
        left join users as c on c.id = d.id_penerima
        WHERE a.selesai = 'Y' AND b.baru = 'baru'

        UNION ALL 
        SELECT a.no_box, b.nm_partai, c.name, a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_awal, a.gr_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join formulir_sarang as d on d.no_box = a.no_box and d.kategori = 'cetak'
        left join users as c on c.id = d.id_penerima
        where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal = 0

) as a
group by a.no_box;
        ");

        return $result;
    }



    public static function sortir_selesai_akhir()
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai, g.name, 
            b.tipe, b.ket,
            SUM(a.pcs_akhir) as pcs, 
            SUM(a.gr_akhir) as gr, 
            SUM(b.hrga_satuan * b.gr_awal) as ttl_rp, 
            sum(COALESCE(a.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) + COALESCE(f.ttl_rp,0) ) as cost_kerja,
            sum(z.cost_cu) as cost_cu
            FROM sortir as a 
            LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            JOIN formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
            left join cabut as d on d.no_box = a.no_box
			left join eo as e on e.no_box = a.no_box
            left join (
                SELECT a.no_box, sum(a.ttl_rp) as ttl_rp 
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak

                        where b.kategori = 'CTK'
                        group by a.no_box
            ) as f on f.no_box = a.no_box
            left join (
                        SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                                FROM cetak_new as a 
                                left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                                where b.kategori = 'CU' and a.selesai = 'Y'
                                group by a.no_box
                    ) as z on z.no_box = a.no_box
            left join users as g on g.id = a.id_pengawas
            
            WHERE  a.selesai = 'Y' and b.baru = 'baru'
            group by a.no_box
            order by g.name ASC;
        ");

        return $result;
    }
}
