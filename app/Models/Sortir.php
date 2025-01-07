<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class Sortir extends Model
{
    public static function queryRekapGroup($bulan, $tahun)
    {
        $cabutGroup = DB::select("SELECT 
                        max(b.name) as pengawas, 
                        e.ttl_box,
                        a.id_pengawas,
                        c.pcs_awal,
                        c.gr_awal,
                        c.gr_akhir,
                        c.pcs_akhir,
                        d.gr_bk,
                        d.pcs_bk,
                        c.ttl_rp,
                        sum((1 - c.gr_akhir / c.gr_awal) * 100) as susut,
                        c.rp_target
                        FROM sortir as a 
                        left join users as b on b.id = a.id_pengawas 
                        LEFT JOIN (
                            SELECT 
                                id_pengawas,no_box, 
                                sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal, 
                                sum(gr_akhir) as gr_akhir, sum(pcs_akhir) as pcs_akhir,
                                SUM(rp_target) as rp_target,
                                SUM(ttl_rp) as ttl_rp
                                FROM sortir WHERE no_box != 9999 AND penutup = 'T' AND bulan = '$bulan' AND tahun_dibayar = '$tahun' GROUP BY id_pengawas
                        ) as c ON c.id_pengawas = a.id_pengawas
                        LEFT JOIN (
                            SELECT a.penerima,a.no_box,sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk FROM bk as a
                            JOIN (
                                SELECT no_box,id_pengawas FROM sortir where bulan = '$bulan' AND tahun_dibayar = '$tahun' GROUP BY no_box,id_pengawas
                            ) as b on a.no_box = b.no_box and b.id_pengawas = a.penerima
                            WHERE a.kategori LIKE '%sortir%' and a.selesai = 'T'
                            GROUP by a.penerima
                        ) as d ON d.penerima = a.id_pengawas
                        LEFT JOIN (
                            SELECT id_pengawas, COUNT(DISTINCT no_box) as ttl_box
                            FROM sortir WHERE no_box != 9999 AND penutup = 'T' AND bulan = '$bulan' AND tahun_dibayar = '$tahun'
                            GROUP BY id_pengawas
                        ) as e ON e.id_pengawas = a.id_pengawas
                        WHERE  a.no_box != 9999 AND a.penutup = 'T' AND a.bulan = '$bulan' AND YEAR(a.tgl) = '$tahun'
                        GROUP BY a.id_pengawas");
        return $cabutGroup;
    }

    public static function queryRekap($id_pengawas = null, $bulan = null, $tahun = null)
    {
        $where = $id_pengawas == 'all' ? '' : "AND a.id_pengawas = $id_pengawas";

        return DB::select("SELECT c.kategori,max(b.name) as pengawas, max(a.tgl) as tgl, a.no_box, 
        SUM(a.pcs_awal) as pcs_awal , sum(a.gr_awal) as gr_awal,
        SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, c.pcs_bk, c.gr_bk,
         sum(a.rp_target) as rp_target,sum(a.ttl_rp) as rupiah,sum((1 - a.gr_akhir / a.gr_awal) * 100) as susut
        FROM sortir as a
        left join users as b on b.id = a.id_pengawas
        LEFT JOIN (
            SELECT no_box,penerima, kategori, sum(pcs_awal) as pcs_bk, sum(gr_awal) as gr_bk FROM bk WHERE kategori LIKE '%sortir%' AND selesai = 'T' GROUP BY no_box,penerima
        ) as c on c.no_box = a.no_box and c.penerima = a.id_pengawas
        WHERE  a.no_box != 9999 AND a.penutup = 'T' $where AND a.bulan = '$bulan' AND YEAR(a.tgl) = '$tahun'
        GROUP by a.no_box,a.id_pengawas
        ");
    }

    public static function siap_sortir($id_user)
    {
        if (auth()->user()->posisi_id == 1) {
            $id_pengawas = '';
        } else {
            $id_pengawas = "AND a.id_penerima = $id_user";
        }
        $result = DB::select("SELECT b.nm_partai, a.no_box, a.pcs_awal, a.gr_awal, (b.hrga_satuan * b.gr_awal) as ttl_rp,
        (if(c.ttl_rp is null,0,c.ttl_rp) + if(e.ttl_rp is null,0,e.ttl_rp)) as cost_cbt, d.ttl_rp as cost_ctk, f.name, g.ttl_rp as cost_eo
        FROM formulir_sarang as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join cabut as c on c.no_box = a.no_box
        left join (
        	SELECT d.no_box, d.ttl_rp 
            FROM cetak_new as d 
            left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
            where h.kategori = 'CTK'
        ) as d on d.no_box = a.no_box
        left join eo as e on e.no_box = a.no_box
        left join users as f on f.id = a.id_penerima
        left join eo as g on g.no_box = a.no_box
        WHERE a.no_box not in(SELECT b.no_box FROM sortir as b where b.id_anak != 0) and a.kategori = 'sortir' $id_pengawas;");
        return $result;
    }
    public static function sortir_proses($id_user)
    {
        if (auth()->user()->posisi_id == 1) {
            $id_pengawas = '';
        } else {
            $id_pengawas = "AND a.id_pengawas = $id_user";
        }

        $result = DB::select("SELECT b.nm_partai,  a.no_box, a.pcs_awal, a.gr_awal, (b.hrga_satuan * b.gr_awal) as ttl_rp,
        d.ttl_rp as cost_cbt, e.ttl_rp as cost_ctk, f.name, g.ttl_rp as cost_eo
        FROM sortir as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
        left join cabut as d on d.no_box = a.no_box
        left join (
        	SELECT d.no_box, d.ttl_rp 
            FROM cetak_new as d 
            left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
            where h.kategori = 'CTK'
        ) as e on e.no_box = a.no_box
        left join users as f on f.id = a.id_pengawas
        left join eo as g on g.no_box = a.no_box
        WHERE a.selesai = 'T' and a.id_anak != 0  $id_pengawas ;");
        return $result;
    }
    public static function sortir_selesai($id_user)
    {
        if (auth()->user()->posisi_id == 1) {
            $id_pengawas = '';
        } else {
            $id_pengawas = "AND a.id_pengawas = $id_user";
        }
        $result = DB::select("SELECT b.nm_partai, a.no_box, a.pcs_akhir as pcs_awal, a.gr_akhir as gr_awal,(b.hrga_satuan * b.gr_awal) as ttl_rp, d.ttl_rp as cost_cbt, e.ttl_rp as cost_ctk, f.name, a.ttl_rp as cost_str, g.ttl_rp as cost_eo
        FROM sortir as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        join formulir_sarang as c on c.no_box = a.no_box and c.kategori = 'sortir'
        left join cabut as d on d.no_box = a.no_box
        left join (
        	SELECT d.no_box, d.ttl_rp 
            FROM cetak_new as d 
            left join kelas_cetak as h on h.id_kelas_cetak = d.id_kelas_cetak
            where h.kategori = 'CTK'
        ) as e on e.no_box = a.no_box
        
        left join users as f on f.id = a.id_pengawas
        left join eo as g on g.no_box = a.no_box
        WHERE a.no_box not in (SELECT b.no_box FROM formulir_sarang as b where b.kategori = 'grade') $id_pengawas and a.selesai = 'Y' $id_pengawas; ");

        return $result;
    }
}
