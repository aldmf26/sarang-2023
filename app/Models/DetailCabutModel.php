<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailCabutModel extends Model
{
    use HasFactory;


    public static function bkstockawal_sum()
    {
        $result = DB::select("SELECT a.no_box, a.nm_partai, a.name, sum(a.pcs) as pcs, sum(a.gr_awal) as gr_awal, sum(a.gr_akhir) as gr_akhir, sum(a.ttl_rp) as ttl_rp, sum(a.cost) as cost_kerja
FROM (
        SELECT a.no_box, b.nm_partai, c.name, a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_awal, a.gr_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal != 0

        UNION ALL

        SELECT a.no_box, b.nm_partai, c.name,  a.ttl_rp as cost, 0 as pcs, a.gr_eo_awal as gr_awal, a.gr_eo_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp
        FROM eo as a 
        LEFT JOIN bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        WHERE a.selesai = 'Y' AND b.baru = 'baru'

        UNION ALL 
        SELECT a.no_box, b.nm_partai, c.name, a.ttl_rp as cost,a.pcs_akhir as pcs, a.gr_awal, a.gr_akhir as gr_akhir, (b.hrga_satuan * b.gr_awal) as ttl_rp
        FROM cabut as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join users as c on c.id = a.id_pengawas
        where a.selesai = 'Y'   and b.baru = 'baru' and a.pcs_awal = 0

) as a
group by a.no_box;
            ");
        return $result;
    }
}
