<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Susut extends Model
{
    use HasFactory;
    protected $table = 'formulir_sarang';
    protected $guarded = [];

    // Scope untuk memfilter berdasarkan kategori
    public function scopeKategori($query, $category)
    {
        return $query->where('kategori', $category);
    }

    public static function getSum($kategori)
    {
        $cabutKeCetak =  DB::select("SELECT 
        b.name,
        SUM(c.pcs_awal) as pcs_awal,
        SUM(COALESCE(c.gr_awal, d.gr_eo_awal)) as gr_awal,
        SUM(a.pcs_awal) as pcs_akhir,
        SUM(a.gr_awal) as gr_akhir,
        SUM(a.sst_aktual) as sst_aktual,
        a.kategori 
        FROM formulir_sarang as a
        join users as b on a.id_pemberi = b.id
        left join cabut as c on a.no_box = c.no_box
        LEFT JOIN eo as d ON a.no_box = d.no_box
        WHERE a.kategori = 'cetak'
        GROUP BY a.id_pemberi;");

        $cetakKeSortir =  DB::select("SELECT 
        b.name,
        SUM(c.pcs_awal_ctk) as pcs_awal,
        SUM(c.gr_awal_ctk) as gr_awal,
        SUM(a.gr_awal) as gr_akhir,
        SUM(a.sst_aktual) as sst_aktual,
        a.kategori 
        FROM formulir_sarang as a
        join users as b on a.id_pemberi = b.id
        left join cetak_new as c on a.no_box = c.no_box
        WHERE a.kategori = 'sortir'
        GROUP BY a.id_pemberi;");

        $sortirKeGrading =  DB::select("SELECT 
        b.name,
        SUM(c.pcs_awal) as pcs_awal,
        SUM(c.gr_awal) as gr_awal,
        SUM(a.gr_awal) as gr_akhir,
        SUM(a.sst_aktual) as sst_aktual,
        a.kategori 
        FROM formulir_sarang as a
        join users as b on a.id_pemberi = b.id
        left join sortir as c on a.no_box = c.no_box
        WHERE a.kategori = 'grade'
        GROUP BY a.id_pemberi;");

        $datas = [
            'cabut' => $cabutKeCetak,
            'cetak' => $cetakKeSortir,
            'sortir' => $sortirKeGrading
        ];

        return $datas[$kategori];
    }

    public static function getSumDetail()
    {
        return DB::select("SELECT 
        b.name,
        a.no_box,
        c.pcs_awal,
        COALESCE(c.gr_awal, d.gr_eo_awal) as gr_awal,
        a.pcs_awal as pcs_akhir,
        a.gr_awal as gr_akhir,
        a.sst_aktual,
        a.kategori 
        FROM `formulir_sarang` as a
        join users as b on a.id_pemberi = b.id
        left join cabut as c on a.no_box = c.no_box
        LEFT JOIN eo as d ON a.no_box = d.no_box
        WHERE a.kategori in ('cetak')
        GROUP BY a.kategori,a.no_box");
    }
}
