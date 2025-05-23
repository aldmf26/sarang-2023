<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Susut extends Model
{
    use HasFactory;
    protected $table = 'tb_susut';
    protected $guarded = [];

    public function pemberi()
    {
        return $this->belongsTo(User::class, 'id_pemberi');
    }

    public function getSusutAktualAttribute()
    {
        return $this->rambangan_1 +
            $this->rambangan_2 +
            $this->rambangan_3 +
            $this->sapuan_lantai +
            $this->sesetan +
            $this->bulu +
            $this->pasir +
            $this->rontokan_bk;
    }

    // Scope untuk memfilter berdasarkan kategori
    public function scopeKategori($query, $category)
    {
        return $query->where('kategori', $category);
    }

    public static function getSum($kategori)
    {
        $bulan = 3;
        $cabutKeCetak =  DB::select("SELECT 
        b.name,
        b.id,
        SUM(c.pcs_awal) as pcs_awal,
        SUM(COALESCE(c.gr_awal, d.gr_eo_awal)) as gr_awal,
        SUM(a.pcs_awal) as pcs_akhir,
        SUM(a.gr_awal) as gr_akhir,
        -- SUM(a.sst_aktual) as sst_aktual,
        e.ttl_aktual as sst_aktual,
        a.kategori 
        FROM formulir_sarang as a
        join users as b on a.id_pemberi = b.id
        left join cabut as c on a.no_box = c.no_box
        LEFT JOIN eo as d ON a.no_box = d.no_box
        LEFT join (
            select id_pemberi, sum(ttl_aktual) as ttl_aktual from tb_susut where divisi = 'cabut' group by id_pemberi        
        ) as e on e.id_pemberi = b.id
        WHERE a.kategori = 'cetak' and month(a.tanggal) >= '$bulan'
        GROUP BY a.id_pemberi;");

        $cetakKeSortir =  DB::select("SELECT 
        b.name,
        b.id,
        SUM(c.pcs_awal_ctk) as pcs_awal,
        SUM(c.gr_awal_ctk) as gr_awal,
        SUM(a.gr_awal) as gr_akhir,
        SUM(a.sst_aktual) as sst_aktual,
        a.kategori 
        FROM formulir_sarang as a
        join users as b on a.id_pemberi = b.id
        left join cetak_new as c on a.no_box = c.no_box
        WHERE a.kategori = 'sortir' and month(a.tanggal) >= '$bulan'
        GROUP BY a.id_pemberi;");

        $sortirKeGrading =  DB::select("SELECT 
        b.name,
        b.id,
        SUM(c.pcs_awal) as pcs_awal,
        SUM(c.gr_awal) as gr_awal,
        SUM(a.gr_awal) as gr_akhir,
        SUM(a.sst_aktual) as sst_aktual,
        a.kategori 
        FROM formulir_sarang as a
        join users as b on a.id_pemberi = b.id
        left join sortir as c on a.no_box = c.no_box
        WHERE a.kategori = 'grade' and month(a.tanggal) >= '$bulan'
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
