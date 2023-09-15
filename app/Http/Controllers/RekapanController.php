<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapExport;

class RekapanController extends Controller
{
    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $id = auth()->user()->id;

        $rekap = DB::select("SELECT a.nama, a.id_kelas, b.name, c.absen, d.rupiah, d.d_susut, d.d_hcr, d.eot_lebih, e.rp_pcs_cetak, 
        e.d_cetak, f.rp_spesial, g.rp_eo, h.rp_sortir,i.rp_dll, f.pcs_w_spc, f.gr_w_spc, f.pcs_k_spc, f.gr_k_spc, f.eot_spc
                FROM tb_anak as a 
                left join users as b on b.id = a.id_pengawas
                left join (
                    SELECT c.id_anak , sum(c.nilai) as absen
                    FROM absen as c 
                    where c.tgl between '$tgl1' and '$tgl2'
                    group by c.id_anak
                ) as c on c.id_anak = a.id_anak
                
                left join (
                    SELECT d.id_anak, sum(round(d.rupiah,0)) as rupiah, sum(d.gr_akhir) AS gr_akhir,
                    sum(if(d.gr_akhir = 0 , 0 ,if(ROUND((1-(d.gr_akhir / d.gr_awal))*100 ,1) > 23.4 , ROUND((((1-(d.gr_akhir / d.gr_awal))*100) - 23.4) * 0.03 * d.rupiah,0),0 )))  d_susut,
                    SUM(d.pcs_hcr * 5000) AS d_hcr,
                    SUM( if(d.eot = 0 ,0, (d.eot - (d.gr_awal * 0.02)) * 750) ) AS eot_lebih
                    FROM cabut as d 
                    where d.tgl_terima BETWEEN '$tgl1' and '$tgl2' AND d.selesai = 'Y'
                    group by d.id_anak
                ) as d on d.id_anak = a.id_anak
                
                LEFT JOIN (
                      SELECT e.id_anak, SUM(e.rp_pcs * e.pcs_awal) AS rp_pcs_cetak, 
                    SUM(if(e.gr_akhir = 0 , 0 , (ROUND((1-(e.gr_akhir / e.gr_awal)) * 100,0) * 50000) )) AS d_cetak
                    FROM cetak AS e 
                    WHERE e.tgl BETWEEN '$tgl1' AND '$tgl2' AND e.selesai = 'Y'
                    GROUP BY e.id_anak
                  ) AS e ON e.id_anak = a.id_anak
                  
                  LEFT JOIN (
                      SELECT f.id_anak , 
                      sum(f.pcs_awal) as pcs_w_spc,
                      sum(f.gr_awal) as gr_w_spc,
                      sum(f.pcs_akhir) as pcs_k_spc,
                      sum(f.gr_akhir) as gr_k_spc,
                      sum(f.eot) as eot_spc,
                      SUM(f.ttl_rp) AS rp_spesial
                    FROM cabut_spesial AS f 
                    WHERE f.tgl BETWEEN '$tgl1' AND '$tgl2' AND f.selesai = 'Y'
                    GROUP BY f.id_anak
                  ) AS f ON f.id_anak = a.id_anak
                  
                  LEFT JOIN (
                      SELECT g.id_anak,
                      SUM(g.ttl_rp) AS rp_eo
                      FROM eo AS g 
                      WHERE g.tgl_ambil BETWEEN '$tgl1' AND '$tgl2' AND g.selesai = 'Y'
                    GROUP BY g.id_anak
                  ) AS g ON g.id_anak = a.id_anak
                  
                  LEFT JOIN (
                      SELECT h.id_anak , SUM(h.ttl_rp) AS rp_sortir
                      FROM sortir AS h
                      WHERE h.tgl BETWEEN '$tgl1' AND '$tgl2' AND h.selesai = 'Y'
                    GROUP BY h.id_anak
                  ) AS h ON h.id_anak = a.id_anak
                  
                  LEFT JOIN (
                      SELECT i.id_anak , SUM(i.rupiah) AS rp_dll
                      FROM tb_hariandll AS i
                      WHERE i.tgl BETWEEN '$tgl1' AND '$tgl2'
                    GROUP BY i.id_anak
                  ) AS i ON i.id_anak = a.id_anak
                
                where a.id_pengawas = $id;");

        $data = [
            'title' => 'Rekap Gaji',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'anak' => $rekap
        ];
        return view('home.rekap.index', $data);
    }

    public function export(Request $r)
    {

        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $id = auth()->user()->id;
        $rekap = DB::select("SELECT a.nama, a.id_kelas, b.name, c.absen, d.rupiah, d.d_susut, d.d_hcr, d.eot_lebih, e.rp_pcs_cetak, 
        e.d_cetak, f.rp_spesial, g.rp_eo, h.rp_sortir,i.rp_dll, d.pcs_awal, d.pcs_akhir, d.gr_awal,d.gr_akhir, d.eot,d.gr_flx,f.pcs_w_spc, f.gr_w_spc, f.pcs_k_spc, f.gr_k_spc, f.eot_spc, g.gr_eo_awal, g.gr_eo_akhir, h.pcs_awal_s, h.gr_awal_s , h.pcs_akhir_s, h.gr_akhir_s
                FROM tb_anak as a 
                left join users as b on b.id = a.id_pengawas
                left join (
                    SELECT c.id_anak , sum(c.nilai) as absen
                    FROM absen as c 
                    where c.tgl between '$tgl1' and '$tgl2'
                    group by c.id_anak
                ) as c on c.id_anak = a.id_anak
                
                left join (
                    SELECT d.id_anak, sum(round(d.rupiah,0)) as rupiah,
                    sum(d.pcs_awal) AS pcs_awal,
                    sum(d.pcs_akhir) AS pcs_akhir,
                    sum(d.gr_awal) AS gr_awal,
                    sum(d.gr_akhir) AS gr_akhir,
                    sum(d.eot) AS eot,
                    sum(d.gr_flx) AS gr_flx,
                    sum(if(d.gr_akhir = 0 , 0 ,if(ROUND((1-(d.gr_akhir / d.gr_awal))*100 ,1) > 23.4 , ROUND((((1-(d.gr_akhir / d.gr_awal))*100) - 23.4) * 0.03 * d.rupiah,0),0 )))  d_susut,
                    SUM(d.pcs_hcr * 5000) AS d_hcr,
                    SUM( if(d.eot = 0 ,0, (d.eot - (d.gr_awal * 0.02)) * 750) ) AS eot_lebih
                    FROM cabut as d 
                    where d.tgl_terima BETWEEN '$tgl1' and '$tgl2' AND d.selesai = 'Y'
                    group by d.id_anak
                ) as d on d.id_anak = a.id_anak
                
                LEFT JOIN (
                      SELECT e.id_anak, SUM(e.rp_pcs * e.pcs_awal) AS rp_pcs_cetak, 
                    SUM(if(e.gr_akhir = 0 , 0 , (ROUND((1-(e.gr_akhir / e.gr_awal)) * 100,0) * 50000) )) AS d_cetak
                    FROM cetak AS e 
                    WHERE e.tgl BETWEEN '$tgl1' AND '$tgl2' AND e.selesai = 'Y'
                    GROUP BY e.id_anak
                  ) AS e ON e.id_anak = a.id_anak
                  
                  LEFT JOIN (
                      SELECT f.id_anak, 
                      sum(f.pcs_awal) as pcs_w_spc,
                      sum(f.gr_awal) as gr_w_spc,
                      sum(f.pcs_akhir) as pcs_k_spc,
                      sum(f.gr_akhir) as gr_k_spc,
                      sum(f.eot) as eot_spc,
                      SUM(f.ttl_rp) AS rp_spesial
                    FROM cabut_spesial AS f 
                    WHERE f.tgl BETWEEN '$tgl1' AND '$tgl2' AND f.selesai = 'Y'
                    GROUP BY f.id_anak
                  ) AS f ON f.id_anak = a.id_anak
                  
                  LEFT JOIN (
                      SELECT g.id_anak , 
                      sum(g.gr_eo_awal) as gr_eo_awal,
                      sum(g.gr_eo_akhir) as gr_eo_akhir,
                      SUM(g.ttl_rp) AS rp_eo
                      FROM eo AS g 
                      WHERE g.tgl_ambil BETWEEN '$tgl1' AND '$tgl2' AND g.selesai = 'Y'
                    GROUP BY g.id_anak
                  ) AS g ON g.id_anak = a.id_anak
                  
                  LEFT JOIN (
                      SELECT h.id_anak, 
                      sum(h.pcs_awal) as pcs_awal_s,
                      sum(h.gr_awal) as gr_awal_s,
                      sum(h.pcs_akhir) as pcs_akhir_s,
                      sum(h.gr_akhir) as gr_akhir_s,
                      SUM(h.ttl_rp) AS rp_sortir
                      FROM sortir AS h
                      WHERE h.tgl BETWEEN '$tgl1' AND '$tgl2' AND h.selesai = 'Y'
                    GROUP BY h.id_anak
                  ) AS h ON h.id_anak = a.id_anak
                  
                  LEFT JOIN (
                      SELECT i.id_anak , SUM(i.rupiah) AS rp_dll
                      FROM tb_hariandll AS i
                      WHERE i.tgl BETWEEN '$tgl1' AND '$tgl2'
                    GROUP BY i.id_anak
                  ) AS i ON i.id_anak = a.id_anak
                
                where a.id_pengawas = $id;");


        $view = 'home.rekap.export';
        $tbl = $rekap;

        return Excel::download(new RekapExport($tbl, $view), 'Export Rekap.xlsx');
    }
}
