<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SummaryModel extends Model
{
    use HasFactory;

    public static function summarybk()
    {
        $result = DB::select("SELECT a.nm_partai, a.tgl, b.nm_partai_dulu, b.pcs, b.gr, b.grade, sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk, b.ttl_rp
        FROM bk as a 
        left join bk_awal as b on b.nm_partai = a.nm_partai
        where a.baru = 'baru' and a.kategori ='cabut'
        group by a.nm_partai;
        ");

        return $result;
    }

    public static function bksedang_proses()
    {
        $result = DB::select("SELECT a.nm_partai, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp, sum(a.cost_cu) as cost_cu
        FROM (
        SELECT a.no_box, a.pcs_awal as pcs, a.gr_awal as gr , b.hrga_satuan, (b.gr_awal * b.hrga_satuan) as ttl_rp,
                c.name as penerima, b.nm_partai, z.cost_cu
        FROM cabut as a
        left join bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        left join (
                SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                FROM cetak_new as a 
                left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                where b.kategori = 'CU'
                group by a.no_box
            ) as z on z.no_box = a.no_box
        WHERE a.selesai = 'T'  AND a.no_box != 9999 and b.baru = 'baru'     
        UNION ALL
        SELECT d.no_box, 0, d.gr_eo_awal as gr, e.hrga_satuan, (d.gr_eo_awal * e.hrga_satuan) as ttl_rp, f.name as penerima, e.nm_partai, z.cost_cu
        FROM eo as d
        left join bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
        left join users as f on f.id = d.id_pengawas
        left join (
                SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                FROM cetak_new as a 
                left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                where b.kategori = 'CU'
                group by a.no_box
            ) as z on z.no_box = d.no_box
        WHERE d.selesai = 'T'  AND d.no_box != 9999  and e.baru = 'baru'
        ) as a 
        group by a.nm_partai
        ");

        return $result;
    }


    public static function bkselesai_siap_ctk()
    {
        $result = DB::select("SELECT a.no_box, d.name, b.nm_partai, sum(a.pcs_akhir) as pcs, sum(a.gr_akhir) as gr, sum(COALESCE(b.hrga_satuan * b.gr_awal,0) + COALESCE(a.ttl_rp,0) ) as ttl_rp, z.cost_cu, sum(a.ttl_rp) as cost_kerja
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join oprasional as c on c.bulan = a.bulan_dibayar
        left join users as d on d.id = a.id_pengawas
        left join (
            SELECT a.no_box, sum(a.ttl_rp) as cost_cu
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori = 'CU'
            group by a.no_box
        ) as z on z.no_box = a.no_box
        where a.selesai = 'Y' and a.formulir = 'T' and a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'cetak') and a.pcs_awal != 0 and b.baru = 'baru'
        group by b.nm_partai;
        ");

        return $result;
    }

    public static function bkselesai_siap_str()
    {
        $result = DB::select("SELECT a.nm_partai, sum(a.gr) as gr, sum(COALESCE(a.ttl_rp,0)) as ttl_rp, sum(a.cost_kerja) as cost_kerja, sum(a.cost_op_cbt) as cost_op_cbt, sum(a.cost_cu) as cost_cu
            FROM (
            SELECT b.nm_partai, c.name as pengawas, a.no_box, ((b.hrga_satuan * b.gr_awal) + a.ttl_rp) as ttl_rp, a.gr_eo_akhir as gr, a.ttl_rp as cost_kerja, (((b.hrga_satuan * b.gr_awal) + a.ttl_rp) /  a.gr_eo_akhir) as hrga_satuan, (a.gr_eo_akhir * d.rp_gr) as cost_op_cbt, z.cost_cu
                    FROM eo as a 
                    left join (
                        SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CU'
                        group by a.no_box
                        
                    ) as z on z.no_box = a.no_box
                    left join oprasional as d on d.bulan = a.bulan_dibayar
                    left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                    left join users as c on c.id = a.id_pengawas
                    where a.selesai ='Y' and b.baru = 'baru'
                    and a.no_box not in (SELECT c.no_box FROM formulir_sarang as c where c.kategori = 'sortir')
                    
            UNION ALL
            SELECT b.nm_partai, c.name as pengawas, a.no_box, ((b.hrga_satuan * b.gr_awal) + a.ttl_rp) as ttl_rp, a.gr_akhir as gr,  a.ttl_rp as cost_kerja, (((b.hrga_satuan * b.gr_awal) + a.ttl_rp) /  a.gr_akhir) as hrga_satuan, (a.gr_akhir * d.rp_gr) as cost_op_cbt, z.cost_cu
                    FROM cabut as a 
                    left join (
                        SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CU'
                        group by a.no_box
                    ) as z on z.no_box = a.no_box
                    left join oprasional as d on d.bulan = a.bulan_dibayar
                    left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                    left join users as c on c.id = a.id_pengawas
                    where a.selesai ='Y' and b.baru = 'baru' and a.pcs_akhir = 0
                    and a.no_box not in (SELECT c.no_box FROM formulir_sarang as c where c.kategori = 'sortir')
                    
            ORDER by no_box
            ) as a
            GROUP by a.nm_partai 
        
        ");

        return $result;
    }

    public static function bk_sisa_pgws()
    {
        $result = DB::select("SELECT a.nm_partai, b.name, a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, 
        
        sum(a.gr_awal * a.hrga_satuan) as ttl_rp, z.cost_cu
            FROM bk as a 
            left join users as b on b.id = a.penerima
            left join (
                SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                FROM cetak_new as a 
                left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                where b.kategori = 'CU'
                group by a.no_box
            ) as z on z.no_box = a.no_box
            where a.kategori ='cabut' and a.baru ='baru' 
            AND NOT EXISTS (SELECT 1 FROM cabut AS b WHERE b.no_box = a.no_box) 
            AND NOT EXISTS (SELECT 1 FROM eo AS c WHERE c.no_box = a.no_box)
            AND a.baru = 'baru'
            GROUP by a.nm_partai;");
        return $result;
    }

    public static function cetak_proses()
    {
        $result = DB::select("SELECT a.no_box, sum(a.pcs_awal_ctk) as pcs, sum(a.gr_awal_ctk) as gr, sum((d.gr_awal * d.hrga_satuan) + COALESCE(a.ttl_rp,0)) as ttl_rp , f.ttl_rp as cost_cbt, d.nm_partai, (f.gr_akhir * h.rp_gr) as cost_op, i.name, z.cost_cu
            FROM cetak_new as a 
            left join users as i on i.id = a.id_pengawas
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join cabut as f on f.no_box = a.no_box
            left join oprasional as h on h.bulan = f.bulan_dibayar
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            left join (
                        SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CU'
                        group by a.no_box
                    ) as z on z.no_box = a.no_box
            where a.selesai = 'T' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            order by d.nm_partai ASC;
        ");

        return $result;
    }

    public static function cetak_selesai_belum_serah()
    {
        $result = DB::select("SELECT a.no_box, e.nm_partai, sum(a.pcs_akhir + a.pcs_tdk_cetak) as pcs, sum(a.gr_akhir + a.gr_tdk_cetak) as gr, sum((e.gr_awal * e.hrga_satuan) + COALESCE(a.ttl_rp)) as ttl_rp, sum(a.ttl_rp) as cost_kerja
        FROM cetak_new as a 
        
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        
        where a.selesai = 'Y' and g.kategori = 'CTK' and e.baru = 'baru'
        and a.formulir = 'T'  and a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'sortir') 
        group by e.nm_partai;
        ");

        return $result;
    }

    public static function cetak_sisa_pgws()
    {
        $result = DB::select("SELECT a.no_box, b.name, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(c.hrga_satuan  * c.gr_awal) as ttl_rp, e.name as pgws,
                    d.ttl_rp as cost_cbt, c.nm_partai, c.pcs_awal as pcs_bk, (d.gr_akhir * f.rp_gr) as cost_op, z.cost_cu
                FROM formulir_sarang as a 
                left join users as b on b.id = a.id_penerima
                left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
                left join cabut as d on d.no_box = a.no_box
                left join oprasional as f on f.bulan = d.bulan_dibayar
                left join users as e on e.id = a.id_pemberi
                left join (
                        SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CU'
                        group by a.no_box
                    ) as z on z.no_box = a.no_box
                WHERE a.kategori = 'cetak'   
                and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 0) and a.no_box != 0
                group by c.nm_partai
        ");

        return $result;
    }

    public static function sortir_proses()
    {
        $result = DB::select("SELECT b.nm_partai,  a.no_box, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(b.hrga_satuan * b.gr_awal) as ttl_rp,
                d.ttl_rp as cost_cbt, e.ttl_rp as cost_ctk, f.name, g.ttl_rp as cost_eo, (h.rp_gr * d.gr_akhir) as cost_op_cbt, e.cost_op_ctk, (g.gr_eo_akhir * i.rp_gr) as cost_op_eo, z.cost_cu
                FROM sortir as a 
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                left join cabut as d on d.no_box = a.no_box
                left join oprasional as h on h.bulan = d.bulan_dibayar
                left join (
                    SELECT d.no_box, d.ttl_rp , (d.gr_akhir * f.rp_gr) as cost_op_ctk
                    FROM cetak_new as d 
                    left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                    left join oprasional as f on f.bulan = d.bulan_dibayar
                    where h.kategori = 'CTK'
                ) as e on e.no_box = a.no_box
                left join (
                    SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    where b.kategori = 'CU'
                    group by a.no_box
                ) as z on z.no_box = a.no_box
                left join users as f on f.id = a.id_pengawas
                left join eo as g on g.no_box = a.no_box
                left join oprasional as i on i.bulan = g.bulan_dibayar
                WHERE a.selesai = 'T' and a.id_anak != 0

                group by b.nm_partai
        ");

        return $result;
    }

    public static function sortir_selesai()
    {
        $result = DB::select("SELECT b.nm_partai, a.no_box, sum(a.pcs_akhir) as pcs, sum(a.gr_akhir) as gr, 
        sum((b.hrga_satuan * b.gr_awal) + COALESCE(a.ttl_rp)) as ttl_rp, 
        sum(b.hrga_satuan * b.gr_awal) as cost_bk, 
        sum(d.ttl_rp ) as cost_cbt, 
        sum(g.ttl_rp) as cost_eo, 
        sum(e.ttl_rp) as cost_ctk, 
        sum(a.ttl_rp) as cost_kerja,

        sum(COALESCE(a.gr_akhir * h.rp_gr,0) + COALESCE(i.rp_gr * d.gr_akhir,0) + COALESCE(g.gr_eo_akhir * j.rp_gr,0) + COALESCE(e.cost_op_ctk,0) ) as cost_op,
        sum(a.gr_akhir * h.rp_gr) as cost_op_str,
        sum(i.rp_gr * d.gr_akhir) as cost_op_cbt,
        sum(e.cost_op_ctk) as cost_op_ctk,
        sum(g.gr_eo_akhir * j.rp_gr) as cost_op_eo, f.name, z.cost_cu

                FROM sortir as a 
                left join oprasional as h on h.bulan = a.bulan
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                left join cabut as d on d.no_box = a.no_box
                left join oprasional as i on i.bulan = d.bulan_dibayar
                left join (
                    SELECT d.no_box, d.ttl_rp ,(d.gr_akhir * f.rp_gr) as cost_op_ctk
                    FROM cetak_new as d 
                    left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                    left join oprasional as f on f.bulan = d.bulan_dibayar
                    where h.kategori = 'CTK'
                ) as e on e.no_box = a.no_box

                left join (
                    SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    where b.kategori = 'CU'
                    group by a.no_box
                ) as z on z.no_box = a.no_box
                
                left join users as f on f.id = a.id_pengawas
                left join eo as g on g.no_box = a.no_box
                left join oprasional as j on j.bulan = g.bulan_dibayar
                WHERE a.no_box not in (SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'grade') and a.selesai = 'Y' and b.baru = 'baru'
                group by b.nm_partai;
        ");

        return $result;
    }

    public static function stock_sortir()
    {
        $result = DB::select("SELECT h.name, a.no_box, b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr , sum((b.gr_awal * b.hrga_satuan)) as ttl_rp, sum(b.gr_awal * b.hrga_satuan) as cost_bk,
        sum(COALESCE(c.ttl_rp,0) ) as cost_cbt, sum(COALESCE(d.ttl_rp,0)) as cost_eo, sum(e.ttl_rp) as cost_ctk,
        sum(COALESCE(c.gr_akhir * f.rp_gr,0) + COALESCE(d.gr_eo_akhir * g.rp_gr,0) + COALESCE(e.cost_op_ctk,0)) as cost_op, z.cost_cu
        FROM formulir_sarang as a 
        left join users as h on h.id = a.id_penerima
        left join bk as b on b.no_box = a.no_box and b.kategori ='cabut'
        left join cabut as c on c.no_box = a.no_box and b.kategori = 'cabut'
        left join oprasional as f on f.bulan = c.bulan_dibayar
        left join eo as d on d.no_box = a.no_box
        left join oprasional as g on g.bulan = d.bulan_dibayar
        left join (
                    SELECT d.no_box, d.ttl_rp ,(d.gr_akhir * f.rp_gr) as cost_op_ctk
                    FROM cetak_new as d 
                    left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                    left join oprasional as f on f.bulan = d.bulan_dibayar
                    where h.kategori = 'CTK'
        ) as e on e.no_box = a.no_box
        left join (
            SELECT a.no_box, sum(a.ttl_rp) as cost_cu
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori = 'CU'
            group by a.no_box
         ) as z on z.no_box = a.no_box

        where b.baru = 'baru' and b.kategori ='cabut' and a.kategori='sortir' and a.no_box not in(SELECT b.no_box FROM sortir as b where b.id_anak != 0) 
        group by b.nm_partai;
        ");

        return $result;
    }

    public static function grading_stock()
    {
        $result = DB::select("SELECT b.nm_partai, a.no_box, l.name, sum(COALESCE(a.pcs_awal,0) - COALESCE(c.pcs_grading,0)) as pcs, sum(COALESCE(a.gr_awal,0) - COALESCE(c.gr_grading,0)) as gr, 
            
            sum((b.gr_awal * b.hrga_satuan) ) as ttl_rp , 
            sum(b.hrga_satuan * b.gr_awal) as cost_bk, 
            sum(e.ttl_rp) as cost_cbt, 
            sum(h.ttl_rp) as cost_eo, 
            sum(f.ttl_rp) as cost_ctk, 
            sum(g.ttl_rp) as cost_str,
            sum(COALESCE(i.rp_gr * e.gr_akhir,0) + COALESCE(f.cost_op_ctk,0) + COALESCE(g.gr_akhir * j.rp_gr,0) + COALESCE(h.gr_eo_akhir * k.rp_gr,0)) as cost_op, z.cost_cu
            FROM formulir_sarang as a 
            left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
            left join (
            SELECT c.no_box_sortir as no_box, sum(c.pcs) as pcs_grading, sum(c.gr) as gr_grading
                FROM grading as c
                group by c.no_box_sortir
            ) as c on c.no_box = a.no_box

            left join cabut as e on e.no_box = a.no_box
            left join oprasional as i on i.bulan = e.bulan_dibayar
            

            left join (
                SELECT d.no_box, d.ttl_rp, (d.gr_akhir * i.rp_gr) as cost_op_ctk
                FROM cetak_new as d 
                left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
                left join oprasional as i on i.bulan = d.bulan_dibayar 
                where h.kategori = 'CTK'
            ) as f on f.no_box = a.no_box

            left join (
                    SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                    FROM cetak_new as a 
                    left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                    where b.kategori = 'CU'
                    group by a.no_box
                ) as z on z.no_box = a.no_box

            left join sortir as g on g.no_box = a.no_box
            left join oprasional as j on j.bulan = g.bulan
            
            left join eo as h on h.no_box = a.no_box
            left join oprasional as k on k.bulan = h.bulan_dibayar
            left join users as l on l.id = a.id_penerima

            where a.kategori ='grade' 
            group by b.nm_partai;
        
        ");

        return $result;
    }

    public static function bkselesai_siap_ctk_diserahkan()
    {
        $result = DB::select("SELECT sum(a.ttl_rp) as cost_kerja,a.no_box, d.name, b.nm_partai, sum(a.pcs_akhir) as pcs, sum(a.gr_akhir) as gr, sum(COALESCE(b.hrga_satuan * b.gr_awal,0)) as ttl_rp, z.cost_cu, sum(a.ttl_rp) as cost_kerja
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join oprasional as c on c.bulan = a.bulan_dibayar
        left join users as d on d.id = a.id_pengawas
        left join (
            SELECT a.no_box, sum(a.ttl_rp) as cost_cu
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori = 'CU'
            group by a.no_box
        ) as z on z.no_box = a.no_box
        where a.selesai = 'Y'  and a.no_box  in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'cetak') and a.pcs_awal != 0 and b.baru = 'baru'
        group by b.nm_partai;
        ");

        return $result;
    }
    public static function bkselesai_siap_str_diserahkan()
    {
        $result = DB::select("SELECT b.nm_partai, a.no_box, sum(b.hrga_satuan * b.gr_awal) as ttl_rp, sum(a.gr_eo_akhir) as gr, sum(a.ttl_rp) as cost_kerja
                    FROM eo as a                
                    left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                    where a.selesai ='Y' and b.baru = 'baru'
                    and a.no_box  in (SELECT c.no_box FROM formulir_sarang as c where c.kategori = 'sortir')
                    
            UNION ALL
            SELECT b.nm_partai, a.no_box, sum(b.hrga_satuan * b.gr_awal) as ttl_rp, sum(a.gr_akhir) as gr, sum(a.ttl_rp) as cost_kerja
                    FROM cabut as a                    
                    left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                    where a.selesai ='Y' and b.baru = 'baru' and a.pcs_akhir = 0
                    and a.no_box  in (SELECT c.no_box FROM formulir_sarang as c where c.kategori = 'sortir');
        ");

        return $result;
    }
    public static function cetak_selesai_diserahkan()
    {
        $result = DB::select("SELECT a.no_box, e.nm_partai, 
        sum(a.pcs_akhir) as pcs, 
        sum(a.pcs_tdk_cetak) as pcs_tdk_ctk, 
        sum(a.gr_akhir ) as gr, 
        sum(a.gr_tdk_cetak) as gr_tdk_ctk, 
        sum(e.gr_awal * e.hrga_satuan) as cost_bk, 
        sum(a.ttl_rp) as cost_kerja
                FROM cetak_new as a 
                left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
                left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
                where a.selesai = 'Y' and g.kategori = 'CTK' and e.baru = 'baru' 
                and a.formulir = 'T'  and a.no_box  in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'sortir')
                group by e.nm_partai;
        ");

        return $result;
    }


    public static function sortir_selesai_diserahkan()
    {
        $result = DB::select("SELECT b.nm_partai, a.no_box, sum(a.pcs_akhir) as pcs, sum(a.gr_akhir) as gr, 
        sum((b.hrga_satuan * b.gr_awal)) as ttl_rp,
        sum(a.ttl_rp) as cost_kerja
                FROM sortir as a 
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                WHERE a.no_box  in (SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'grade') and a.selesai = 'Y' and b.baru = 'baru'
                GROUP by b.nm_partai;
        ");

        return $result;
    }

    public static function bk_suntik($gudang)
    {
        $result = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = '$gudang'");
        return $result;
    }
    public static function cabut_history($nm_partai)
    {
        $result = DB::selectOne("SELECT min(tgl) as tgl, nm_partai, SUM(pcs) as pcs, SUM(gr) as gr, SUM(pcs_akhir) as pcs_akhir, SUM(gr_akhir) as gr_akhir, SUM(cost_bk) as cost_bk, SUM(cost_cabut) as cost_cabut
            FROM (
                SELECT min(a.tgl_terima) as tgl,
                    d.nm_partai, SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr,  SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir,SUM(d.hrga_satuan * d.gr_awal) as cost_bk, SUM(a.ttl_rp) as cost_cabut
                FROM cabut as a 
                    LEFT JOIN bk as d 
                        ON d.no_box = a.no_box AND d.kategori = 'cabut'
                WHERE d.nm_partai = '$nm_partai' AND a.selesai = 'Y'  AND a.no_box IN (SELECT b.no_box FROM formulir_sarang as b WHERE b.kategori IN('cetak','sortir'))
                GROUP BY 
                    d.nm_partai

                UNION ALL

                SELECT 
                min(e.tgl_ambil) as tgl,
                    f.nm_partai, 
                    0 as pcs, 
                    SUM(e.gr_eo_awal) as gr, 
                    0 as pcs_akhir, 
                    SUM(e.gr_eo_akhir) as gr_akhir, 
                    SUM(f.gr_awal * f.hrga_satuan) as cost_bk, 
                    SUM(e.ttl_rp) as cost_cabut
                FROM 
                    eo as e
                    LEFT JOIN bk as f 
                        ON f.no_box = e.no_box
                WHERE 
                    f.nm_partai = '$nm_partai' 
                    AND e.selesai = 'Y' 
                    AND e.no_box IN (SELECT b.no_box FROM formulir_sarang as b WHERE b.kategori = 'sortir')
                GROUP BY 
                    f.nm_partai
            ) as combined_data
            GROUP BY nm_partai;");
        return $result;
    }
    public static function cabut_sisa_history($nm_partai)
    {
        $result = DB::selectOne("SELECT nm_partai, SUM(pcs) as pcs, SUM(gr) as gr, SUM(pcs_akhir) as pcs_akhir, SUM(gr_akhir) as gr_akhir, SUM(cost_bk) as cost_bk, SUM(cost_cabut) as cost_cabut
            FROM (
                SELECT 
                    d.nm_partai, SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr,  SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir,SUM(d.hrga_satuan * d.gr_awal) as cost_bk, SUM(a.ttl_rp) as cost_cabut
                FROM cabut as a 
                    LEFT JOIN bk as d 
                        ON d.no_box = a.no_box AND d.kategori = 'cabut'
                WHERE d.nm_partai = '$nm_partai' AND a.selesai = 'T'  AND a.no_box NOT IN (SELECT b.no_box FROM formulir_sarang as b WHERE b.kategori IN('cetak','sortir'))
                GROUP BY 
                    d.nm_partai

                UNION ALL

                SELECT 
                    f.nm_partai, 
                    0 as pcs, 
                    SUM(e.gr_eo_awal) as gr, 
                    0 as pcs_akhir, 
                    SUM(e.gr_eo_akhir) as gr_akhir, 
                    SUM(f.gr_awal * f.hrga_satuan) as cost_bk, 
                    SUM(e.ttl_rp) as cost_cabut
                FROM 
                    eo as e
                    LEFT JOIN bk as f 
                        ON f.no_box = e.no_box
                WHERE 
                    f.nm_partai = '$nm_partai' 
                    AND e.selesai = 'T' 
                    AND e.no_box NOT IN (SELECT b.no_box FROM formulir_sarang as b WHERE b.kategori = 'sortir')
                GROUP BY 
                    f.nm_partai
            ) as combined_data
            GROUP BY nm_partai;");
        return $result;
    }

    public static function cetak($nm_partai)
    {
        $result = DB::selectOne("SELECT a.nm_partai, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir,sum(a.gr_td_ctk) as gr_td_ctk,
        sum(a.cost_ctk) as cost_ctk, sum(a.cost_bk) as cost_bk, sum(a.gr_akhir_cbt) as gr_akhir_cbt, sum(a.cost_cbt) as cost_cbt
        FROM(
        SELECT b.nm_partai, 
        sum(a.pcs_awal_ctk) as pcs, 
        sum(a.gr_awal_ctk) as gr, 
        sum(a.pcs_akhir + a.pcs_tdk_cetak) as pcs_akhir, 
        sum(a.gr_akhir + a.gr_tdk_cetak) as gr_akhir, 
        sum(a.gr_tdk_cetak) as gr_td_ctk,
        sum(a.ttl_rp) as cost_ctk,
        sum(b.gr_awal * b.hrga_satuan) as cost_bk,
        sum(d.gr_akhir) as gr_akhir_cbt,
        sum(d.ttl_rp) as cost_cbt
        FROM cetak_new as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas_cetak
        left join cabut as d on d.no_box = a.no_box
        where c.kategori = 'CTK' and b.nm_partai = '$nm_partai' and a.no_box in(SELECT b.no_box from formulir_sarang as b where b.kategori = 'sortir')
        GROUP by b.nm_partai
        
        UNION ALL 
        
        SELECT b.nm_partai, 0 as pcs, sum(e.gr_eo_awal) as gr, 0 as pcs_akhir, sum(e.gr_eo_akhir) as gr_akhir, 0 as gr_td_ctk, 
        sum(e.ttl_rp) as cost_ctk, sum(b.gr_awal * b.hrga_satuan) as cost_bk, 0 as gr_akhir_cbt,  0 as cost_cbt
        FROM eo as e 
        left join bk as b on b.no_box = e.no_box and b.kategori = 'cabut'
        where b.nm_partai = '$nm_partai' and e.no_box in(SELECT b.no_box from formulir_sarang as b where b.kategori = 'sortir')
        
        UNION ALL
        
        SELECT b.nm_partai, sum(f.pcs_awal) as pcs, sum(f.gr_awal) as gr, sum(f.pcs_akhir) as pcs_akhir, sum(f.gr_akhir) , 0 as gr_td_ctk, sum(f.ttl_rp) as cost_ctk, 
        sum(b.gr_awal * b.hrga_satuan) as cost_bk, 0 as gr_akhir_cbt,  0 as cost_cbt
        FROM cabut as f 
        left join bk as b on b.no_box = f.no_box and b.kategori = 'cabut'
        where b.nm_partai = '$nm_partai' and f.no_box in(SELECT b.no_box from formulir_sarang as b where b.kategori = 'sortir') and f.pcs_awal = 0
        
        ) as a 
        where a.nm_partai is not null
        group by a.nm_partai;");

        return $result;
    }
}
