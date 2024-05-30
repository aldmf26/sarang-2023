<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CetakModel extends Model
{
    use HasFactory;
    public static function cetakGroup()
    {

        $result = DB::select("SELECT a.id_bk, a.penerima, b.name, count(a.id_bk) as total_bk, sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk, c.pcs_awal, c.gr_awal, c.pcs_tdk_ctk, c.gr_tidak_ctk, c.pcs_akhir, c.gr_akhir, c.pcs_cu, c.gr_cu, c.ttl_rp, c.denda_susut,c.denda_hcr
        FROM bk as a 
        LEFT join users as b on b.id = a.penerima
        left join (
            SELECT c.id_pengawas, sum(c.pcs_awal_ctk) as pcs_awal , sum(c.gr_awal_ctk) as gr_awal, sum(c.pcs_tidak_ctk) as pcs_tdk_ctk, 
            sum(c.gr_tidak_ctk) as gr_tidak_ctk, sum(c.pcs_akhir) as pcs_akhir, sum(c.gr_akhir) as gr_akhir, sum(c.pcs_cu) as pcs_cu, sum(c.gr_cu) as gr_cu,
            sum(
                if(c.pcs_akhir = 0, c.pcs_awal_ctk * d.rp_pcs, c.pcs_akhir * c.rp_pcs )
            ) as ttl_rp,
            SUM(
                IF(
                    round((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100) >= d.batas_susut,
                    round(((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100)) * d.denda_susut,0
                )
            ) AS denda_susut,
            sum(c.pcs_hcr * d.denda_hcr) as denda_hcr
            
            FROM cetak as c
            left join kelas_cetak as d on d.id_kelas_cetak = c.id_kelas
            group by c.id_pengawas
        ) as c on c.id_pengawas = a.penerima 
        where a.kategori = 'cetak' AND a.selesai = 'T'
        group by a.penerima;
        ");

        return $result;
    }
    public static function cetak_keluar($penerima)
    {
        $result = DB::select("SELECT a.id_bk, a.no_box, b.name, count(a.id_bk) as total_bk, sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk, c.pcs_awal, c.gr_awal, c.pcs_tdk_ctk, c.gr_tidak_ctk, c.pcs_akhir, c.gr_akhir, c.pcs_cu, c.gr_cu, c.ttl_rp,c.denda_susut,c.denda_hcr
        FROM bk as a 
        LEFT join users as b on b.id = a.penerima
        left join (
            SELECT c.no_box, sum(c.pcs_awal_ctk) as pcs_awal , sum(c.gr_awal_ctk) as gr_awal, sum(c.pcs_tidak_ctk) as pcs_tdk_ctk, 
            sum(c.gr_tidak_ctk) as gr_tidak_ctk, sum(c.pcs_akhir) as pcs_akhir, sum(c.gr_akhir) as gr_akhir, sum(c.pcs_cu) as pcs_cu, sum(c.gr_cu) as gr_cu,
            sum(
                if(c.pcs_akhir = 0, c.pcs_awal_ctk * d.rp_pcs, c.pcs_akhir * c.rp_pcs )
            ) as ttl_rp,
            SUM(
                IF(
                    round((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100) >= d.batas_susut,
                    round(((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100)) * d.denda_susut,0
                )
            ) AS denda_susut,
            sum(c.pcs_hcr * d.denda_hcr) as denda_hcr
            FROM cetak as c
            left join kelas_cetak as d on d.id_kelas_cetak = c.id_kelas
            group by c.no_box
        ) as c on c.no_box = a.no_box
        where a.kategori = 'cetak' and a.penerima = ?
        group by a.no_box;
        ", [$penerima]);

        return $result;
    }
    public static function cetak_export()
    {
        $result = DB::select("SELECT a.id_bk, a.no_box, a.tgl, b.name, count(a.id_bk) as total_bk, sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk, c.pcs_awal, c.gr_awal, c.pcs_tdk_ctk, c.gr_tidak_ctk, c.pcs_akhir, c.gr_akhir, c.pcs_cu, c.gr_cu, c.ttl_rp,c.denda_susut,c.denda_hcr, c.rp_harian
        FROM bk as a 
        LEFT join users as b on b.id = a.penerima
        left join (
            SELECT c.no_box, sum(c.pcs_awal_ctk) as pcs_awal , sum(c.gr_awal_ctk) as gr_awal, sum(c.pcs_tidak_ctk) as pcs_tdk_ctk, 
            sum(c.gr_tidak_ctk) as gr_tidak_ctk, sum(c.pcs_akhir) as pcs_akhir, sum(c.gr_akhir) as gr_akhir, sum(c.pcs_cu) as pcs_cu, sum(c.gr_cu) as gr_cu,
            sum(
                if(c.pcs_akhir = 0, c.pcs_awal_ctk * d.rp_pcs, c.pcs_akhir * c.rp_pcs )
            ) as ttl_rp,
            SUM(
                IF(
                    round((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100) >= d.batas_susut,
                    round(((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100)) * d.denda_susut,0
                )
            ) AS denda_susut,
            sum(c.pcs_hcr * d.denda_hcr) as denda_hcr, sum(c.rp_harian) as rp_harian
            FROM cetak as c
            left join kelas_cetak as d on d.id_kelas_cetak = c.id_kelas
            group by c.no_box
        ) as c on c.no_box = a.no_box
        where a.kategori = 'cetak' 
        group by a.no_box;
        ");

        return $result;
    }

    public static function cetak_export2()
    {
        $result = DB::select("SELECT c.no_box, sum(c.pcs_awal_ctk) as pcs_awal , sum(c.gr_awal_ctk) as gr_awal, sum(c.pcs_tidak_ctk) as pcs_tdk_ctk, 
        sum(c.gr_tidak_ctk) as gr_tidak_ctk, sum(c.pcs_akhir) as pcs_akhir, sum(c.gr_akhir) as gr_akhir, sum(c.pcs_cu) as pcs_cu, sum(c.gr_cu) as gr_cu,
        sum(
            if(c.pcs_akhir = 0, c.pcs_awal_ctk * d.rp_pcs, c.pcs_akhir * c.rp_pcs )
        ) as ttl_rp,
        SUM(
            IF(
                round((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100) >= d.batas_susut,
                round(((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100)) * d.denda_susut,0
            )
        ) AS denda_susut,
        sum(c.pcs_hcr * d.denda_hcr) as denda_hcr, sum(c.rp_harian) as rp_harian, c.tgl, e.name
        FROM cetak as c
        left join kelas_cetak as d on d.id_kelas_cetak = c.id_kelas
        left join users as e on e.id = c.id_pengawas
        group by c.no_box;
        ");

        return $result;
    }

    public static function rekap_harian()
    {
        $result = DB::select("SELECT b.nama, b.id_kelas, a.ttl_anak, c.hari + 1 as hari, c.rp_proses, d.rp_selesai, 
        d.tgl_serah, a.tgl_awal , DATEDIFF(d.tgl_serah,a.tgl_awal) as hari_kerja_selesai
        FROM ( SELECT a.id_anak, count(a.id_anak) as ttl_anak , min(a.tgl) as tgl_awal 
              FROM absen as a 
              where a.bulan_dibayar = '4' 
              group by a.id_anak ) as a 
              left JOIN tb_anak as b on b.id_anak = a.id_anak 
              left join( SELECT c.id_anak, sum(c.pcs_awal_ctk * c.rp_pcs) as rp_proses, DATEDIFF(NOW(), c.tgl) AS hari 
                       from cetak as c 
                        where c.selesai = 'T' and c.bulan_dibayar = '4'
                        GROUP by c.id_anak ) as c on c.id_anak = a.id_anak 
                        left join ( SELECT c.id_anak , sum((c.pcs_akhir * c.rp_pcs) + c.rp_harian) as rp_selesai, max(c.tgl_serah) as tgl_serah 
                        FROM cetak as c 
                        where c.selesai ='Y' and c.bulan_dibayar = '4' 
                        group by c.id_anak ) as d on d.id_anak = a.id_anak;
        ");
        return $result;
    }

    public static function summary_cetak($bulan, $tahun)
    {

        $result = DB::select("SELECT h.name as pgws,  a.id_anak, b.nama, count(a.tgl) as ttl_absen ,if(d.cost_cabut is null , 0 , d.cost_cabut) as cost_cabut , if(c.cost_cetak is null,0,c.cost_cetak) as cost_cetak, if(e.cost_sortir is null ,0,e.cost_sortir) as cost_sortir, 
        if(f.cost_eo is null ,0,f.cost_eo) as cost_eo,if(g.cost_dll is null,0,g.cost_dll) as cost_dll
        FROM absen as a 
        left JOIN tb_anak as b on b.id_anak = a.id_anak
        left join users as h on h.id = b.id_pengawas
        left join (
        SELECT c.id_anak, sum(c.ttl_rp) as cost_cetak
            FROM cetak_new as c
            where c.bulan_dibayar = '$bulan'
            group by c.id_anak
        ) as c on c.id_anak =  a.id_anak
        
        LEFT JOIN (
        SELECT d.id_anak, SUM(CASE WHEN d.selesai = 'Y' THEN d.ttl_rp ELSE 0 END) as cost_cabut
        FROM cabut as d 
        WHERE d.penutup = 'T' AND d.no_box != 9999 AND d.bulan_dibayar = '$bulan' AND d.tahun_dibayar = '$tahun'
        GROUP BY d.id_anak 
        ) as d on a.id_anak = d.id_anak
        
        LEFT join (
            SELECT e.id_anak, sum(CASE WHEN e.selesai = 'Y' THEN e.ttl_rp ELSE 0 END ) as cost_sortir
            FROM sortir as e 
            WHERE e.bulan = '$bulan' AND YEAR(e.tgl_input) = '$tahun' AND e.penutup = 'T' AND e.no_box != 9999 
            GROUP BY e.id_anak
        ) as e on a.id_anak = e.id_anak
        
        LEFT join (
            SELECT f.id_anak, sum(CASE WHEN f.selesai = 'Y' THEN f.ttl_rp ELSE 0 END ) as cost_eo
            FROM eo as f 
            WHERE f.bulan_dibayar = '$bulan' AND YEAR(f.tgl_input) = '$tahun' AND f.penutup = 'T' AND f.no_box != 9999 
            GROUP BY f.id_anak
        ) as f on a.id_anak = f.id_anak
        
        LEFT join (
        SELECT  g.id_anak, sum(g.rupiah) as cost_dll
        FROM tb_hariandll as g
        WHERE bulan_dibayar = '$bulan' AND tahun_dibayar = '$tahun' AND ditutup = 'T' GROUP BY id_anak
        ) as g on a.id_anak = g.id_anak
        
        
        where b.id_pengawas in('285','462','463') and a.bulan_dibayar = '$bulan' and a.tahun_dibayar = '$tahun'
        group by b.id_anak
        order by b.nama ASC
        ");



        return $result;
    }


    public static function cetak_selesai($id_pengawas)
    {
        if ($id_pengawas == 0) {
            $result = DB::select("SELECT c.name, d.name as pgws, a.no_box, a.pcs_akhir as pcs_awal, a.gr_akhir as gr_awal
            FROM cetak_new as a 
            left join formulir_sarang as b on b.no_box = a.no_box and b.kategori = 'cetak'
            left join users as c on c.id = b.id_pemberi
            left join users as d on d.id = a.id_pengawas
            where a.selesai = 'Y'  and a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'sortir')");
        } else {
            $result = DB::select("SELECT c.name, a.no_box, a.pcs_akhir as pcs_awal, a.gr_akhir as gr_awal
            FROM cetak_new as a 
            left join formulir_sarang as b on b.no_box = a.no_box and b.kategori = 'cetak'
            left join users as c on c.id = b.id_pemberi
            where a.selesai = 'Y' and a.id_pengawas = '$id_pengawas' and a.no_box not in(SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'sortir')");
        }

        return $result;
    }

    public static function cabut_selesai($id_pengawas)
    {
        if ($id_pengawas == 0) {
            $result = DB::select("SELECT a.no_box, b.name, a.pcs_awal, a.gr_awal
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_pemberi
        WHERE a.kategori = 'cetak'  and a.no_box not in(SELECT b.no_box FROM cetak_new as b);");
        } else {
            $result = DB::select("SELECT a.no_box, b.name, a.pcs_awal, a.gr_awal
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_pemberi
        WHERE a.kategori = 'cetak' and a.id_penerima = '$id_pengawas' and a.no_box not in(SELECT b.no_box FROM cetak_new as b);");
        }



        return $result;
    }
    public static function cetak_proses($id_pengawas)
    {
        if ($id_pengawas == 0) {
            $result = DB::select("SELECT a.no_box, c.name, a.pcs_awal_ctk as pcs_awal, a.gr_awal_ctk as gr_awal
            FROM cetak_new as a 
            left join formulir_sarang as b on b.no_box = a.no_box and b.kategori = 'cetak'
            left join users as c on c.id = b.id_pemberi
            where a.selesai = 'T' ");
        } else {
            $result = DB::select("SELECT a.no_box, c.name, a.pcs_awal_ctk as pcs_awal, a.gr_awal_ctk as gr_awal
            FROM cetak_new as a 
            left join formulir_sarang as b on b.no_box = a.no_box and b.kategori = 'cetak'
            left join users as c on c.id = b.id_pemberi
            where a.selesai = 'T' and a.id_pengawas = '$id_pengawas'");
        }



        return $result;
    }


    public static function getCetakQuery($id_anak = 'All', $tgl1, $tgl2, $id_pengawas)
    {
        $user = auth()->user()->posisi_id;
        if ($user == '1') {
            $pgws = '';
        } else {
            $pgws = 'and a.id_pengawas = ' . $id_pengawas;
        }


        if ($id_anak == 'All') {
            $cetak = DB::select("SELECT a.id_anak, a.capai,a.id_cetak, a.selesai, c.name, d.name as pgws, b.nama as nm_anak , a.no_box, a.tgl, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_awal_ctk as pcs_awal_ctk, a.gr_awal_ctk, a.pcs_akhir, a.gr_akhir, a.rp_satuan, e.kelas, e.batas_susut , e.denda_susut, e.id_paket, a.rp_tambahan , a.id_kelas_cetak, a.pcs_hcr, e.denda_hcr,a.tipe_bayar, a.bulan_dibayar, a.ttl_rp
            From cetak_new as a  
            LEFT join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pemberi
            left join users as d on d.id = a.id_pengawas
            left join kelas_cetak as e on e.id_kelas_cetak = a.id_kelas_cetak
            where a.tgl between '$tgl1' and '$tgl2' $pgws
            order by a.tgl DESC, b.nama ASC
            ;");
        } else {
            $cetak = DB::select("SELECT a.id_anak, a.capai,a.id_cetak, a.selesai, c.name, d.name as pgws, b.nama as nm_anak , a.no_box, a.tgl, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_awal_ctk as pcs_awal_ctk, a.gr_awal_ctk, a.pcs_akhir, a.gr_akhir, a.rp_satuan, e.kelas, e.batas_susut , e.denda_susut, e.id_paket, a.rp_tambahan , a.id_kelas_cetak , a.pcs_hcr, e.denda_hcr,a.tipe_bayar,a.bulan_dibayar,a.ttl_rp
            From cetak_new as a  
            LEFT join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pemberi
            left join users as d on d.id = a.id_pengawas
            left join kelas_cetak as e on e.id_kelas_cetak = a.id_kelas_cetak
            where a.tgl between '$tgl1' and '$tgl2' and a.id_anak = '$id_anak' $pgws
            order by a.tgl DESC, b.nama ASC
            ;");
        }
        return $cetak;
    }

    public static function gaji_global($bulan_dibayar, $tahun_dibayar, $id_pengawas)
    {
        $result = DB::select("SELECT h.name, a.nama , b.ttl_hari, c.pcs_awal_ctk,c.gr_awal_ctk, c.pcs_akhir_ctk, c.gr_akhir_ctk, c.ttl_rp_cetak, 
        d.pcs_awal_cbt, d.gr_awal_cbt, d.pcs_akhir_cbt, d.gr_akhir_cbt, d.ttl_rp_cbt,
        e.pcs_awal_str, e.gr_awal_str, e.pcs_akhir_str, e.gr_akhir_str, e.ttl_rp_str, f.ttl_harian, g.ttl_rp_denda,
        i.gr_awal_eo, i.gr_akhir_eo, i.ttl_rp_eo
        FROM tb_anak as a 
        left join (
         SELECT b.id_anak, count(b.tgl) as ttl_hari
            FROM absen as b 
            where b.bulan_dibayar = '$bulan_dibayar' and b.tahun_dibayar = '$tahun_dibayar'
            GROUP by b.id_anak
        ) as b on b.id_anak =  a.id_anak
        
        left join (
         SELECT c.id_anak, sum(c.pcs_awal_ctk) as pcs_awal_ctk, 
         sum(c.gr_awal_ctk) as gr_awal_ctk, 
         sum(c.pcs_akhir) as pcs_akhir_ctk, 
         sum(c.gr_akhir) as gr_akhir_ctk, 
         sum(c.ttl_rp) as ttl_rp_cetak
            FROM cetak_new as c
            where c.bulan_dibayar = '$bulan_dibayar' and YEAR(c.tgl) = '$tahun_dibayar'
            GROUP by c.id_anak
        ) as c on c.id_anak = a.id_anak
        
        left join (
            SELECT d.id_anak, 
            sum(d.pcs_awal) as pcs_awal_cbt, 
            sum(d.gr_awal) as gr_awal_cbt, 
            sum(d.pcs_akhir) as pcs_akhir_cbt, 
            sum(d.gr_akhir) as gr_akhir_cbt, 
            sum(d.ttl_rp) as ttl_rp_cbt
            FROM cabut as d 
            where d.bulan_dibayar = '$bulan_dibayar'
            GROUP by d.id_anak
        ) as d on d.id_anak = a.id_anak
        
        left join (
            SELECT e.id_anak, 
            sum(e.pcs_awal) as pcs_awal_str, 
            sum(e.gr_awal) gr_awal_str, 
            sum(e.pcs_akhir) as pcs_akhir_str, 
            sum(e.gr_akhir) gr_akhir_str, 
            sum(e.ttl_rp) as ttl_rp_str 
            FROM sortir as e 
            where e.bulan = '$bulan_dibayar' 
            group by e.id_anak
        ) as e on e.id_anak = a.id_anak
        
        
        left join (
            SELECT f.id_anak, sum(f.rupiah) as ttl_harian
            FROM tb_hariandll as f 
            where f.bulan_dibayar = '$bulan_dibayar' and f.tahun_dibayar = '$tahun_dibayar'
            GROUP by f.id_anak
        ) as f on f.id_anak = a.id_anak
        
        left join (
         SELECT g.id_anak , sum(g.nominal) as ttl_rp_denda
            FROM tb_denda as g 
            where g.bulan_dibayar = '$bulan_dibayar'
            GROUP by g.id_anak
        ) as g on g.id_anak = a.id_anak

        left join users as h on h.id = a.id_pengawas

        left join (
            SELECT i.id_anak, 
            sum(i.gr_eo_awal) gr_awal_eo, 
            sum(i.gr_eo_akhir) as gr_eo_akhir, 
            sum(i.ttl_rp) as ttl_rp_eo
            FROM eo as i
            where i.bulan = '$bulan_dibayar' 
            group by i.id_anak
        ) as i on i.id_anak = a.id_anak
        
        where a.id_pengawas = '$id_pengawas';");
        return $result;
    }
}
