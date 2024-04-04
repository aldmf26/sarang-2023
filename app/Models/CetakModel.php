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
}
