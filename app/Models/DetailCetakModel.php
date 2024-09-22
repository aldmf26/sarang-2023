<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailCetakModel extends Model
{
    use HasFactory;


    public static function cetak_stok_awal()
    {
        $result = DB::select("SELECT 
        e.no_box, c.name,e.nm_partai,
        sum(a.pcs_awal_ctk) as pcs_awal,
        sum(a.gr_awal_ctk) as gr_awal,
        sum(a.pcs_akhir + a.pcs_tdk_cetak) as pcs_akhir,
        sum(a.gr_akhir + a.gr_tdk_cetak) as gr_akhir,
        sum(e.gr_awal * e.hrga_satuan) as ttl_rp,
        sum(a.ttl_rp) as cost_kerja
        FROM cetak_new as a 
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        left join users as c on a.id_pengawas = c.id
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        where 
            a.selesai = 'Y' 
            and a.no_box != 9999 
            and a.id_anak != 0 
            and g.kategori = 'CTK' 
            and e.baru = 'baru' 
        group by a.no_box
            ") ;
        return $result;
    }

    public static function stok_selesai()
    {
        $result = DB::select("SELECT 
        e.no_box, c.name,e.nm_partai,
        sum(a.pcs_awal_ctk) as pcs_awal, 
        sum(a.gr_awal_ctk) as gr_awal, 
        sum(a.pcs_akhir + a.pcs_tdk_cetak) as pcs_akhir, 
        sum(a.gr_akhir + a.gr_tdk_cetak) as gr_akhir, 
        sum(e.gr_awal * e.hrga_satuan) as ttl_rp, 
        sum(a.ttl_rp) as cost_kerja
        FROM cetak_new as a 
        left join bk as e on e.no_box = a.no_box and e.kategori = 'cabut'
        left join users as c on a.id_pengawas = c.id
        left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
        where a.selesai = 'Y' and a.no_box != 9999 and a.id_anak != 0 and g.kategori = 'CTK' and e.baru = 'baru' 
        group by a.no_box
            ");
        return $result;
    }

}
