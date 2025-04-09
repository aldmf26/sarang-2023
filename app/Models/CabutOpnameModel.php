<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CabutOpnameModel extends Model
{
    use HasFactory;

    public static function cabut_susut()
    {
        return DB::select("SELECT a.id_pengawas, a.no_box, a.nm_partai, a.tipe, a.name, sum(a.pcs) as pcs, sum(a.gr_awal) as gr_awal, sum(a.gr_akhir) as gr_akhir, a.batas_susut,
        b.pcs as pcs_proses, b.gr as gr_proses , c.pcs as pcs_sisa, c.gr as gr_sisa, d.pcs as pcs_siap_cetak, d.gr as gr_siap_cetak
        FROM (
                SELECT a.id_pengawas, concat(d.tipe) as tipe , a.no_box, b.nm_partai, c.name, a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_awal, a.gr_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp, d.batas_susut
                FROM cabut as a 
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                left join users as c on c.id = a.id_pengawas
            	left join tb_kelas as d on d.id_kelas = a.id_kelas
                where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal != 0 and a.no_box in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'cetak' group by a.no_box)

                UNION ALL

                SELECT a.id_pengawas, concat(d.kelas) as tipe, a.no_box,  b.nm_partai, c.name,  a.ttl_rp as cost, 0 as pcs, a.gr_eo_awal as gr_awal, a.gr_eo_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp, d.batas_susut
                FROM eo as a 
                LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                left join users as c on c.id = a.id_pengawas
            left join tb_kelas as d on d.id_kelas = a.id_kelas
                WHERE a.selesai = 'Y' AND b.baru = 'baru' and a.no_box in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'cetak' group by a.no_box)

                UNION ALL 
                SELECT a.id_pengawas,concat(d.tipe ) as tipe, a.no_box,  b.nm_partai, c.name, a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_awal, a.gr_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp, d.batas_susut
                FROM cabut as a 
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                left join users as c on c.id = a.id_pengawas
            	left join tb_kelas as d on d.id_kelas = a.id_kelas
                where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal = 0 and a.no_box in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'cetak' group by a.no_box)

        ) as a
        left join (
            SELECT a.id_pengawas, a.no_box, b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr ,sum(b.gr_awal * b.hrga_satuan) as ttl_rp, sum(if(a.ttl_rp < 0 , 0 , a.ttl_rp)) as cost_kerja, c.name
            FROM cabut as a
            LEFT JOIN bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
            left join users as c on c.id = a.id_pengawas
            WHERE a.selesai = 'T' AND a.no_box != 9999 and b.baru = 'baru'
            group by a.id_pengawas
            
            UNION ALL
            
            SELECT d.id_pengawas, d.no_box, e.nm_partai, 0 as pcs, sum(d.gr_eo_awal) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp, sum(if(d.ttl_rp < 0 , 0 , d.ttl_rp)) as cost_kerja,c.name
            FROM eo as d
            LEFT JOIN bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
            left join users as c on c.id = d.id_pengawas
            WHERE d.selesai = 'T' AND d.no_box != 9999 and e.baru = 'baru'
            group by d.id_pengawas
        ) AS b on b.id_pengawas = a.id_pengawas

        left join (
            SELECT a.penerima, a.no_box, b.name, a.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, 
            sum(a.gr_awal * a.hrga_satuan) as ttl_rp
            FROM bk as a 
            left join users as b on b.id = a.penerima
            where a.kategori ='cabut' and a.baru ='baru' 
            AND NOT EXISTS (SELECT 1 FROM cabut AS b WHERE b.no_box = a.no_box) 
            AND NOT EXISTS (SELECT 1 FROM eo AS c WHERE c.no_box = a.no_box)
            AND a.baru = 'baru'
            group by a.penerima
        ) as c on c.penerima = a.id_pengawas

        left join (
            SELECT a.id_pengawas,  c.name, a.no_box, b.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr ,sum(b.gr_awal * b.hrga_satuan) as ttl_rp, sum(a.ttl_rp) as cost_kerja
            FROM cabut as a
            LEFT JOIN bk as b on  b.no_box = a.no_box and b.kategori = 'cabut'
            left join users as c on c.id = a.id_pengawas
            WHERE a.selesai = 'Y' and a.formulir = 'T' and a.no_box not in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'cetak' group by a.no_box) AND a.no_box != 9999 and b.baru = 'baru'
            group by a.id_pengawas
            
            UNION ALL
            
            SELECT d.id_pengawas, c.name, d.no_box, e.nm_partai, 0 as pcs, sum(d.gr_eo_akhir) as gr, sum(e.gr_awal * e.hrga_satuan) as ttl_rp,

            sum(d.ttl_rp) as cost_kerja

            FROM eo as d
            LEFT JOIN bk as e on  e.no_box = d.no_box and e.kategori = 'cabut'
            left join users as c on c.id = d.id_pengawas
            
            WHERE d.selesai = 'Y' and d.no_box not in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'cetak' group by a.no_box) AND d.no_box != 9999 and e.baru = 'baru'
            group by d.id_pengawas
        ) as d on d.id_pengawas = a.id_pengawas


        group by a.id_pengawas, a.tipe
        Order by a.name ASC
        ;");
    }
    public static function cabut_susut_detail($id_pengawas, $tipe)
    {
        return DB::select("SELECT a.id_pengawas, a.no_box, a.nm_partai, a.tipe, a.name, sum(a.pcs) as pcs, sum(a.gr_awal) as gr_awal, sum(a.gr_akhir) as gr_akhir, a.batas_susut, a.nm_anak
        FROM (
                SELECT a.id_pengawas, concat(d.tipe) as tipe , a.no_box, b.nm_partai, c.name, a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_awal, a.gr_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp, d.batas_susut, e.nama as nm_anak, a.id_anak
                FROM cabut as a 
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                left join users as c on c.id = a.id_pengawas
            	left join tb_kelas as d on d.id_kelas = a.id_kelas
            	left join tb_anak as e on e.id_anak = a.id_anak
                where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal != 0 and a.no_box in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'cetak' group by a.no_box)

                UNION ALL

                SELECT a.id_pengawas, concat(d.kelas) as tipe, a.no_box,  b.nm_partai, c.name,  a.ttl_rp as cost, 0 as pcs, a.gr_eo_awal as gr_awal, a.gr_eo_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp, d.batas_susut,e.nama as nm_anak, a.id_anak
                FROM eo as a 
                LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                left join users as c on c.id = a.id_pengawas
            	left join tb_kelas as d on d.id_kelas = a.id_kelas
            	left join tb_anak as e on e.id_anak = a.id_anak
                WHERE a.selesai = 'Y' AND b.baru = 'baru' and a.no_box in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'cetak' group by a.no_box)

                UNION ALL 
                SELECT a.id_pengawas,concat(d.tipe ) as tipe, a.no_box,  b.nm_partai, c.name, a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_awal, a.gr_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp, d.batas_susut,e.nama as nm_anak, a.id_anak
                FROM cabut as a 
                left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                left join users as c on c.id = a.id_pengawas
            	left join tb_kelas as d on d.id_kelas = a.id_kelas
            	left join tb_anak as e on e.id_anak = a.id_anak
                where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal = 0 and a.no_box in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'cetak' group by a.no_box)

        ) as a
        where a.id_pengawas = '$id_pengawas' and a.tipe = '$tipe'
        group by a.id_anak, a.no_box
        Order by a.nm_anak ASC;
        ;");
    }


    public static function cabutPartai($partai)
    {
        $all = $partai == 'all' ? '' : "and b.nm_partai = '$partai' group by b.nm_partai";
        return DB::selectOne("SELECT b.nm_partai, sum(a.gr_awal) as gr_awal, sum(a.pcs_akhir) as pcs , sum(a.gr_akhir) as gr, sum(a.ttl_rp) as ttl_rp, sum(b.hrga_satuan * b.gr_awal) as modal_rp
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        where a.selesai = 'Y' and b.baru='baru' and a.no_box != '9999' 
        $all
        ");

        
    }
    public static function cabutPartaiDetail($partai)
    {
        return DB::select("SELECT a.no_box, c.name, d.nama,  b.nm_partai, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir,  (b.hrga_satuan * b.gr_awal) as modal_rp , a.ttl_rp as cost_kerja, 'cabut' as kerja
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        left join tb_anak as d on d.id_anak = a.id_anak
        where a.selesai = 'Y' and b.baru='baru' and a.no_box != '9999' and b.nm_partai = '$partai'
        

union all 

SELECT a.no_box, c.name, d.nama, b.nm_partai, 0 as pcs_awal , a.gr_eo_awal as gr_awal, 0 as pcs_akhir, a.gr_eo_akhir as gr_akhir,  (b.hrga_satuan * b.gr_awal) as modal_rp , a.ttl_rp as cost_kerja, 'eo' as kerja
        FROM eo as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        left join tb_anak as d on d.id_anak = a.id_anak
        where a.selesai = 'Y' and b.baru='baru' and a.no_box != '9999' and b.nm_partai = '$partai';
        ");
    }
    public static function eotPartai($partai)
    {
        $partai = $partai == 'all' ? '' : " and b.nm_partai = '$partai' group by b.nm_partai";
        return DB::selectOne("SELECT b.nm_partai, sum(a.gr_eo_awal) as gr_eo_awal,  sum(a.gr_eo_akhir) as gr, sum(a.ttl_rp) as ttl_rp
        , sum(b.hrga_satuan * b.gr_awal) as modal_rp
        FROM eo as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        where a.selesai = 'Y' and b.baru='baru' and a.no_box != '9999' $partai
        ");
    }
    public static function bkPaartaiDetail($partai)
    {
        return DB::select("SELECT b.name, a.no_box, a.nm_partai, a.tipe, a.ket, a.pcs_awal , a.gr_awal, (a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a 
        left join users as b on b.id = a.penerima
        where a.kategori = 'cabut' and a.baru = 'baru' and a.nm_partai = '$partai';
        ");
    }
    public static function cetakPartai($partai)
    {
        $partai = $partai == 'all' ? '' : "and c.nm_partai = '$partai'";
        return DB::selectOne("SELECT c.nm_partai, sum(a.pcs_tdk_cetak ) as pcs_tdk, sum(a.pcs_akhir) as pcs , sum(a.gr_tdk_cetak ) as gr_tdk, sum(a.gr_akhir) as gr, sum(a.ttl_rp) as ttl_rp, sum(a.gr_awal_ctk) as gr_awal, sum(c.hrga_satuan * c.gr_awal) as modal_rp,
        sum(COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) ) as cost_kerja
        FROM cetak_new as a 
        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
        left join bk as c on c.no_box = a.no_box and c.kategori = 'cabut'
        left join cabut as d on d.no_box = a.no_box
        left join eo as e on e.no_box = a.no_box
        where b.kategori ='CTK' and c.baru='baru' $partai;
        ");
    }
    public static function cetakPartaiDetail($partai)
    {
        return DB::select("SELECT a.no_box, f.name, g.nama, c.nm_partai, a.pcs_awal_ctk, a.gr_awal_ctk, a.pcs_tdk_cetak ,  a.gr_tdk_cetak, a.pcs_akhir , a.gr_akhir,  (c.hrga_satuan * c.gr_awal) as modal_rp, a.ttl_rp as cost_kerja,
        (COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0) ) as cost_kerja_sebelum
        FROM cetak_new as a 
        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
        left join bk as c on c.no_box = a.no_box and c.kategori = 'cabut'
        left join cabut as d on d.no_box = a.no_box
        left join eo as e on e.no_box = a.no_box
        left join users as f on f.id = a.id_pengawas
        left join tb_anak as g on g.id_anak = a.id_anak
        where b.kategori ='CTK' and c.baru='baru' and c.nm_partai ='$partai';
        ");
    }
    public static function sortirPartai($partai)
    {
        $partai = $partai == 'all' ? '' : "and b.nm_partai = '$partai'
        group by b.nm_partai";
        return DB::selectOne("SELECT b.nm_partai, sum(a.pcs_akhir) as pcs , sum(a.gr_awal) as gr_awal,  sum(a.gr_akhir) as gr, sum(a.ttl_rp) as ttl_rp, sum(b.hrga_satuan * b.gr_awal) as modal_rp, sum( COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0)) as cost_kerja
        FROM sortir as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join cabut as c on c.no_box = a.no_box
        left join eo as d on d.no_box = a.no_box
        left join (
            SELECT e.no_box, sum(e.ttl_rp) as ttl_rp
            FROM cetak_new as e
            left join kelas_cetak as f on f.id_kelas_cetak = e.id_kelas_cetak 
            where f.kategori = 'CTK'
            group by e.no_box
        ) as e on e.no_box = a.no_box
        where a.selesai = 'Y' and b.baru = 'baru' and a.no_box != '9999' $partai
        ");
    }
    public static function sortirPartaiDetail($partai)
    {
        return DB::select("SELECT a.no_box, g.name, h.nama, b.nm_partai, a.pcs_awal , a.gr_awal , a.pcs_akhir,  a.gr_akhir, (b.hrga_satuan * b.gr_awal) as modal_rp, ( COALESCE(c.ttl_rp,0) + COALESCE(d.ttl_rp,0) + COALESCE(e.ttl_rp,0)) as cost_kerja_sebelum, a.ttl_rp as cost_kerja
        FROM sortir as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join cabut as c on c.no_box = a.no_box
        left join eo as d on d.no_box = a.no_box
        left join (
            SELECT e.no_box, sum(e.ttl_rp) as ttl_rp
            FROM cetak_new as e
            left join kelas_cetak as f on f.id_kelas_cetak = e.id_kelas_cetak 
            where f.kategori = 'CTK'
            group by e.no_box
        ) as e on e.no_box = a.no_box
        
        left join users as g on g.id = a.id_pengawas
        left join tb_anak as h on h.id_anak = a.id_anak
        where a.selesai = 'Y' and b.baru = 'baru' and a.no_box != '9999' and b.nm_partai = '$partai';
        ");
    }
    public static function gradingPartai($partai)
    {
        $partai = $partai == 'all' ? '' : "and a.nm_partai = '$partai'";
        return DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.cost_bk) as cost_bk, sum(a.cost_op) as cost_op, sum(a.cost_kerja) as cost_kerja
        FROM grading_partai as a
        where a.grade != 'susut' $partai
        ");
    }
    public static function gradingPartai_susut($partai)
    {
        $partai = $partai == 'all' ? '' : "and a.nm_partai = '$partai'";
        return DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.cost_bk) as cost_bk, sum(a.cost_op) as cost_op, sum(a.cost_kerja) as cost_kerja
        FROM grading_partai as a
        where a.grade = 'susut' $partai
        ");
    }
    public static function pengiriman($partai)
    {
        $partai = $partai == 'all' ? '' : "and a.nm_partai = '$partai'";
        return DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.cost_bk) as cost_bk, sum(a.cost_op) as cost_op, sum(a.cost_kerja) as cost_kerja
        FROM grading_partai as a
        where  a.sudah_kirim = 'Y'$partai
        ");
    }

    public static function table_grade($partai)
    {
        return DB::select("SELECT a.grade, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.cost_bk) as cost_bk, sum(a.cost_op) as cost_op, sum(a.cost_kerja) as cost_kerja
        FROM grading_partai as a WHERE a.nm_partai = '$partai'
        group by a.grade
        ");
    }
    public static function table_grade2($partai)
    {
        return DB::select("SELECT a.grade, sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.cost_bk) as cost_bk, sum(a.cost_op) as cost_op, sum(a.cost_kerja) as cost_kerja
        FROM grading_partai as a WHERE a.nm_partai = '$partai' and a.sudah_kirim = 'Y'
        group by a.grade
        ");
    }

    public static function cetak_susut_tampilan()
    {
        return DB::select("SELECT 
        e.no_box, c.name,e.nm_partai, g.kelas, a.id_pengawas,
        sum(a.pcs_awal_ctk) as pcs_awal,
        sum(a.gr_awal_ctk) as gr_awal,
        sum(a.pcs_akhir + a.pcs_tdk_cetak) as pcs_akhir,
        sum(a.gr_akhir + a.gr_tdk_cetak) as gr_akhir,
        sum(e.gr_awal * e.hrga_satuan) as ttl_rp,
        sum(a.ttl_rp) as cost_kerja, g.batas_susut,
        h.pcs as pcs_proses, h.gr as gr_proses, i.pcs as pcs_sisa, i.gr as gr_sisa,
        j.pcs as pcs_selesai , j.gr as gr_selesai
        FROM cetak_new as a 
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        left join users as c on a.id_pengawas = c.id
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        left join (
        	SELECT a.id_pengawas, e.name, a.no_box, d.nm_partai, sum(a.pcs_awal_ctk) as pcs, 
        sum(a.gr_awal_ctk) as gr
            FROM cetak_new as a 
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            left join users as e on e.id = a.id_pengawas
            where a.selesai = 'T' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            group by a.id_pengawas
            order by e.name ASC
        ) as h on h.id_pengawas = a.id_pengawas
        
        left join (
            SELECT a.id_penerima, e.name, a.no_box, c.nm_partai, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr
            FROM formulir_sarang as a 
            left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
            left join users as e on e.id = a.id_penerima
            WHERE a.kategori = 'cetak'   
            and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 0) and a.no_box != 0
            group by a.id_penerima
            order by e.name ASC
        ) as i on i.id_penerima = a.id_pengawas
        
        left join (
        	SELECT a.id_pengawas, a.no_box, d.nm_partai, sum(a.pcs_awal_ctk) as pcs, sum(a.gr_awal_ctk) as gr
            FROM cetak_new as a 
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            left join users as e on e.id = a.id_pengawas
            where a.selesai = 'Y' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            and a.no_box not in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'sortir')
            group by a.id_pengawas
            order by e.name ASC
        ) as j on j.id_pengawas = a.id_pengawas
        
        where 
            a.selesai = 'Y' 
            and a.no_box != 9999 
            and a.id_anak != 0 
            and g.kategori = 'CTK' 
            and e.baru = 'baru' 
            and a.no_box in(SELECT a.no_box FROM formulir_sarang as a where a.kategori = 'sortir' group by a.no_box)
        group by a.id_pengawas, g.kelas
        order by c.name ASC;");
    }

    public static function sortir_tampilan()
    {
        return DB::select("SELECT c.name,b.nm_partai, d.kelas, a.id_pengawas,
            sum(a.pcs_awal) as pcs,sum(a.gr_awal) as gr_awal, sum(a.gr_akhir) as gr_akhir, d.bts_denda_sst,
            e.pcs as pcs_proses, e.gr as gr_proses, f.pcs as pcs_sisa, f.gr as gr_sisa, g.pcs as pcs_selesai , g.gr as gr_selesai
            FROM sortir as a
            LEFT JOIN bk as b on a.no_box = b.no_box and b.kategori = 'cabut' and b.baru = 'baru'
            left join users as c on a.id_pengawas = c.id
            left join tb_kelas_sortir as d on d.id_kelas =  a.id_kelas
            left join (
            	SELECT a.id_pengawas, a.no_box, b.nm_partai, g.name, SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr
                FROM sortir as a 
                LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                JOIN formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                left join users as g on g.id = a.id_pengawas
                WHERE a.selesai = 'T' AND a.id_anak != 0
                group by a.id_pengawas
                order by g.name ASC
            ) as e on e.id_pengawas = a.id_pengawas
            left join (
            	SELECT a.id_penerima, a.no_box, f.name, b.nm_partai, SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr
                FROM formulir_sarang as a 
                LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                left join users as f on f.id = a.id_penerima

                WHERE b.baru = 'baru' AND b.kategori = 'cabut'  
                AND a.kategori = 'sortir' 
                AND a.no_box NOT IN (SELECT b.no_box FROM sortir as b WHERE b.id_anak != 0)

                group by a.id_penerima
                order by f.name ASC
            ) as f on f.id_penerima = a.id_pengawas
            
            left join (
              	SELECT a.id_pengawas, a.no_box, b.nm_partai, g.name, SUM(a.pcs_awal) as pcs, SUM(a.gr_awal) as gr
                FROM sortir as a 
                LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                JOIN formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
                left join users as g on g.id = a.id_pengawas
                WHERE a.no_box not in (SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'grade') and a.selesai = 'Y' and b.baru = 'baru'
                group by a.id_pengawas
                order by g.name ASC
            ) as g on g.id_pengawas =  a.id_pengawas
            
            
            WHERE a.no_box in (SELECT a.no_box
                        FROM formulir_sarang as a 
                        where a.kategori ='grade'
           group by a.no_box) 
           group by a.id_pengawas, d.kelas
           order by c.name ASC;");
    }
}
