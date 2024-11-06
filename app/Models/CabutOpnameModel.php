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
        Order by a.name ASC;
        ;");
    }
}
