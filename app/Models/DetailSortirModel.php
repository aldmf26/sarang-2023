<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailSortirModel extends Model
{
    use HasFactory;


    public static function stok_awal()
    {
        $result = DB::select("SELECT sum(b.gr_awal * b.hrga_satuan) as ttl_rp, b.no_box,c.name,b.nm_partai,sum(a.pcs_awal) as pcs,sum(a.gr_awal) as gr FROM sortir as a
            LEFT JOIN bk as b on a.no_box = b.no_box and b.kategori = 'cabut'
            left join users as c on a.id_pengawas = c.id
            WHERE a.no_box in (SELECT a.no_box
                        FROM formulir_sarang as a 
                        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                        left join users as c on a.id_penerima = c.id
                        where a.kategori ='grade'
           group by a.no_box) 
           group by a.no_box    
        ");

        return $result;
    }

    public static function stok_selesai()
    {
        $result = DB::select("SELECT sum(a.ttl_rp) as cost_kerja,sum(b.gr_awal * b.hrga_satuan) as ttl_rp, b.no_box,c.name,b.nm_partai,sum(a.pcs_akhir) as pcs,sum(a.gr_akhir) as gr FROM sortir as a
            LEFT JOIN bk as b on a.no_box = b.no_box and b.kategori = 'cabut'
            left join users as c on a.id_pengawas = c.id
            WHERE a.no_box in (SELECT a.no_box
                        FROM formulir_sarang as a 
                        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
                        left join users as c on a.id_penerima = c.id
                        where a.kategori ='grade'
           group by a.no_box) and a.selesai = 'Y'
           group by a.no_box    
        ");

        return $result;
    }
  
}
