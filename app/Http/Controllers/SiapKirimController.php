<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiapKirimController extends Controller
{
    public function getDataMaster($jenis)
    {

        $arr = [
            'gudangkirim' => DB::select("SELECT grade, sum(pcs) as pcs, sum(gr) as gr, sum(gr * rp_gram) as ttl_rp, sum(pcs_kredit) as pcs_kredit, sum(gr_kredit) as gr_kredit, sum(gr_kredit * rp_gram_kredit) as ttl_rp_kredit
                        FROM `siapkirim_list_grading` 
                        GROUP BY grade 
                        HAVING pcs - pcs_kredit <> 0 OR gr - gr_kredit <> 0"),
            'pengawas' => DB::table('users')->where('posisi_id', 13)->get()
        ];
        return $arr[$jenis];
    }
    public function index(Request $r)
    {
        $data = [
            'title' => 'Siap Kirim'
        ];
        return view('home.siapkirim.index', $data);
    }

    public function history_box_kecil()
    {
        $boxKecil = DB::select("SELECT 
                    a.no_box,
                    a.grade,
                    a.pcs_kredit as pcs,
                     a.gr_kredit as gr,
                    a.rp_gram_kredit as rp_gram,
                    a.pengawas,
                    a.no_grading,
                    b.pcs as pcs_sortir,
                    b.gr as gr_sortir,
                    b.ttl_rp as ttlrp_sortir
                    FROM `pengiriman_list_gradingbj` as a 
                    LEFT JOIN (
                        SELECT no_box,sum(pcs_akhir) as pcs, sum(gr_akhir) as gr, sum(ttl_rp) as ttl_rp 
                        FROM `sortir` 
                        WHERE selesai = 'Y'
                        GROUP BY no_box
                    ) as b on a.no_box = b.no_box
                    WHERE a.no_box is not null ");
        $data = [
            'title' => 'History Box Kecil',
            'box_kecil' => $boxKecil
        ];
        return view('home.gradingbj.history_box_kecil', $data);
    }
}
