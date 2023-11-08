<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapGajiPeranakController extends Controller
{
    public function index(Request $r)
    {
        $pengawas = DB::select("SELECT 
        a.id,
        a.name,
        c.ttl as ttl_anak,
        kerja.pcs_awal,
        kerja.gr_awal,
        kerja.pcs_akhir,
        kerja.gr_akhir,
        kerja.gr_flx,
        kerja.eot,
        kerja.susut,
        kerja.ttl_rp,
        absen.total_ttl as total_absen,
        denda.total_nominal
        FROM users as a
        JOIN tb_anak as b on a.id = b.id_pengawas
        LEFT JOIN (
            SELECT id_pengawas,count(*) as ttl FROM tb_anak GROUP BY id_pengawas
        ) as c ON a.id = c.id_pengawas
        LEFT JOIN (
            SELECT b.id, SUM(ttl) as total_ttl
                FROM (
                    SELECT a.id_pengawas, COUNT(*) as ttl
                    FROM absen AS a
                    WHERE MONTH(a.tgl) = '11' AND YEAR(a.tgl) = '2023'
                    GROUP BY a.id_pengawas
                ) as absen_count
                LEFT JOIN users as b ON absen_count.id_pengawas = b.id
                GROUP BY b.id
        ) as absen ON absen.id = a.id
        LEFT JOIN (
            SELECT b.id, SUM(nominal) as total_nominal
            FROM (
                SELECT b.id_pengawas,sum(nominal) as nominal FROM `tb_denda` as a
                join tb_anak as b on a.id_anak = b.id_anak
                    WHERE MONTH(tgl) = '11' AND YEAR(tgl) = '2023'
                    GROUP BY b.id_pengawas
            ) absen_count 
            LEFT JOIN users as b ON absen_count.id_pengawas = b.id
            GROUP BY b.id
        ) as denda ON denda.id = a.id
        LEFT JOIN (
            SELECT 
            max(b.name) as pengawas, 
            a.id_pengawas,
            c.pcs_awal,
            c.gr_awal,
            c.pcs_hcr,
            c.eot,
            c.gr_flx,
            c.gr_akhir,
            c.pcs_akhir,
            c.ttl_rp,
            sum((1 - (c.gr_flx + c.gr_akhir) / c.gr_awal) * 100) as susut,
            c.rupiah
            FROM cabut as a 
            left join users as b on b.id = a.id_pengawas 
            LEFT JOIN (
            SELECT 
                id_pengawas,no_box, 
                sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal, 
                sum(gr_akhir) as gr_akhir, sum(pcs_akhir) as pcs_akhir,
                sum(pcs_hcr) as pcs_hcr,
                sum(eot) as eot,
                sum(gr_flx) as gr_flx,
                SUM(rupiah) as rupiah,
                SUM(ttl_rp) as ttl_rp
                FROM cabut WHERE no_box != 9999 GROUP BY id_pengawas
            ) as c ON c.id_pengawas = a.id_pengawas                   
            WHERE  a.no_box != 9999 AND a.penutup = 'T' 
            GROUP BY a.id_pengawas
        ) as kerja ON kerja.id_pengawas = a.id
        GROUP BY a.id;
        ");
        $kategori = $r->kategori ?? 'cabut';
        $data = [
            'title' => 'Rekap Gaji Peranak',
            'kategori' => $kategori,
            'pengawas' => $pengawas
        ];
        return view('home.rekap.rekap', $data);
    }

}
