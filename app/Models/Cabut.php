<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cabut extends Model
{
    public static function getCabut()
    {
        $id_user = auth()->user()->id;
        return DB::table('cabut as a')
            ->select(
                'b.id_anak',
                'a.no_box',
                'a.rupiah',
                'c.gr as gr_kelas',
                'c.rupiah as rupiah_kelas',
                'c.batas_susut',
                'c.bonus_susut',
                'c.denda_hcr',
                'c.eot as eot_rp',
                'c.batas_eot',
                'b.id_kelas',
                'c.rp_bonus',
                'a.tgl_serah',
                'a.selesai',
                'a.bulan_dibayar',
                'a.tgl_terima',
                'a.id_cabut',
                'a.selesai',
                'b.nama',
                'a.pcs_awal',
                'a.gr_awal',
                'a.gr_flx',
                'a.pcs_akhir',
                'a.pcs_hcr',
                'a.gr_akhir',
                'a.gr_awal',
                'a.eot',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['a.no_box', '!=', '9999'], ['a.penutup', 'T'], ['a.id_pengawas', $id_user]])
            ->orderBY('a.selesai', 'ASC')
            ->orderBY('a.tgl_terima', 'ASC')
            ->get();
    }

    
    public static function getCabutAkhir($orderBy)
    {
        $datas =  DB::table('cabut as a')
            ->select(
                'b.id_anak',
                'a.no_box',
                'a.id_cabut',
                'a.rupiah',
                'c.pcs as pcs_kelas',
                'c.gr as gr_kelas',
                'c.rupiah as rupiah_kelas',
                'c.rp_bonus',
                'c.id_kategori as kategori',
                'c.jenis',
                'c.id_kategori',
                'c.denda_susut_persen',
                'c.denda_hcr',
                'c.batas_susut',
                'c.bonus_susut',
                'c.eot as eot_rp',
                'c.batas_eot',
                'b.id_kelas',
                'a.tgl_serah',
                'a.tgl_terima',
                'b.nama',
                'a.pcs_awal',
                'a.gr_awal',
                'a.bulan_dibayar as bulan',
                'a.gr_flx',
                'a.pcs_akhir',
                'a.pcs_hcr',
                'a.gr_akhir',
                'a.gr_awal',
                'a.ttl_rp',
                'a.eot',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['no_box', '!=', '9999'], ['a.selesai', 'T'], ['a.id_pengawas', auth()->user()->id]]);
        switch ($orderBy) {
            case 'nobox':
                $datas->orderBy('a.no_box', 'ASC');
                break;
            case 'tgl_terima':
                $datas->orderBy('a.tgl_terima', 'ASC');
                break;
            case 'kelas':
                $datas->orderBy('b.id_kelas', 'DESC');
                break;
            case 'nama':
                $datas->orderBy('b.nama', 'ASC');
                break;

            default:
                $datas->orderBy('a.id_cabut', 'DESC');
                break;
        }
        return $datas->get();
    }
    public static function getQueryExport()
    {
        return DB::table('cabut as a')
            ->select(
                'b.id_anak',
                'a.no_box',
                'a.rupiah',
                'c.gr as gr_kelas',
                'c.rupiah as rupiah_kelas',
                'b.id_kelas',
                'c.rp_bonus',
                'c.batas_susut',
                'c.bonus_susut',
                'c.denda_hcr',
                'c.eot as eot_rp',
                'c.batas_eot',
                'a.tgl_serah',
                'a.tgl_terima',
                'a.id_cabut',
                'a.selesai',
                'b.nama',
                'a.pcs_awal',
                'a.gr_awal',
                'a.gr_flx',
                'a.pcs_akhir',
                'a.pcs_hcr',
                'a.gr_akhir',
                'a.gr_awal',
                'a.eot',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where('no_box', '!=', '9999')
            ->orderBY('a.id_cabut', 'DESC')
            ->get();
    }
    public static function getDetailPerId($id_cabut)
    {
        return DB::table('cabut as a')
            ->select(
                'b.id_anak',
                'a.no_box',
                'a.rupiah',
                'c.gr as gr_kelas',
                'c.gr',
                'c.rupiah as rupiah_kelas',
                'b.id_kelas',
                'c.rp_bonus',
                'c.batas_susut',
                'c.bonus_susut',
                'c.denda_hcr',
                'c.kelas as nm_kelas',
                'c.jenis as jenis_kelas',
                'c.eot as eot_rp',
                'c.batas_eot',
                'a.tgl_serah',
                'a.tgl_terima',
                'a.id_cabut',
                'a.selesai',
                'b.nama',
                'a.pcs_awal',
                'a.gr_awal',
                'a.gr_flx',
                'a.pcs_akhir',
                'a.pcs_hcr',
                'a.gr_akhir',
                'a.gr_awal',
                'a.eot',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['a.id_cabut', $id_cabut]])
            ->first();
    }
    public static function queryRekap()
    {
        $id = auth()->user()->id;
        $posisi = auth()->user()->posisi_id;
        $pengawas = $posisi == 13 ? "AND a.id_pengawas = '$id'" : '';

        return DB::select("SELECT max(b.name) as pengawas, 
        max(a.tgl_terima) as tgl, 
        a.no_box, 
        SUM(a.pcs_awal) as pcs_awal , 
        sum(a.gr_awal) as gr_awal,
        SUM(a.pcs_akhir) as pcs_akhir, 
        SUM(a.gr_akhir) as gr_akhir, c.pcs_awal as pcs_bk, c.gr_awal as gr_bk,
        sum(a.pcs_hcr) as pcs_hcr, sum(a.eot) as eot, sum(a.ttl_rp) as rupiah, sum(a.gr_flx) as gr_flx
        FROM cabut as a
        left join users as b on b.id = a.id_pengawas
        left JOIN bk as c on c.no_box = a.no_box 
        WHERE a.penutup = 'T'
        GROUP by a.no_box;");
    }
    public static function queryRekapGroup($tgl1, $tgl2)
    {
        $cabutGroup = DB::select("SELECT 
                        max(b.name) as pengawas, 
                        e.ttl_box,
                        a.id_pengawas,
                        c.pcs_awal,
                        c.gr_awal,
                        c.pcs_hcr,
                        c.eot,
                        c.gr_flx,
                        c.gr_akhir,
                        c.pcs_akhir,
                        d.gr_bk,
                        d.pcs_bk,
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
                                FROM cabut WHERE no_box != 9999 AND penutup = 'T'  GROUP BY id_pengawas
                        ) as c ON c.id_pengawas = a.id_pengawas
                        LEFT JOIN (
                            SELECT a.penerima,a.no_box,sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk FROM `bk`  as a
INNER join cabut as b on a.no_box = b.no_box
GROUP BY a.penerima
                        ) as d ON d.penerima = a.id_pengawas
                        LEFT JOIN (
                            SELECT id_pengawas, COUNT(DISTINCT no_box) as ttl_box
                            FROM cabut WHERE no_box != 9999
                            GROUP BY id_pengawas
                        ) as e ON e.id_pengawas = a.id_pengawas
                        WHERE  a.no_box != 9999 AND a.penutup = 'T' 
                        GROUP BY a.id_pengawas");
        return $cabutGroup;
    }
    public static function getGajiAnak($id, $bulan, $tahun)
    {
        $query = DB::select("SELECT 
        a.id_anak, 
        a.nama, 
        a.id_kelas, 
        absen.ttl, 
        cabut.pcs_awal, 
        cabut.gr_awal, 
        cabut.pcs_akhir, 
        cabut.gr_akhir, 
        cabut.eot, 
        cabut.gr_flx, 
        cabut.susut, 
        cabut.ttl_rp,
        denda.nominal
      FROM 
        tb_anak as a 
      LEFT JOIN (
          SELECT *, count(*) as ttl FROM absen AS a 
          WHERE 
            MONTH(a.tgl) = '$bulan' 
            AND YEAR(a.tgl) = '$tahun' 
          group BY 
            a.id_anak
        ) as absen on absen.id_anak = a.id_anak 
      LEFT JOIN (
          SELECT 
            id_anak, 
            sum(pcs_awal) as pcs_awal, 
            sum(gr_awal) as gr_awal, 
            sum(gr_akhir) as gr_akhir, 
            sum(pcs_akhir) as pcs_akhir, 
            sum(pcs_hcr) as pcs_hcr, 
            sum(eot) as eot, 
            sum(gr_flx) as gr_flx, 
            SUM(rupiah) as rupiah, 
            sum((1 - (gr_flx + gr_akhir) / gr_awal) * 100) as susut, 
            SUM(ttl_rp) as ttl_rp 
          FROM `cabut` 
          WHERE penutup = 'T' 
          GROUP BY id_anak
        ) as cabut on a.id_anak = cabut.id_anak
      LEFT JOIN (
        SELECT id_anak,sum(nominal) as nominal FROM `tb_denda` 
        WHERE MONTH(tgl) = '$bulan' AND YEAR(tgl) = '$tahun'
        GROUP BY id_anak
      ) as denda on a.id_anak = denda.id_anak
      WHERE 
        a.id_pengawas = '$id' 
      HAVING 
        COALESCE(absen.ttl, 0) +
        COALESCE(cabut.pcs_awal, 0) +
        COALESCE(cabut.gr_awal, 0) +
        COALESCE(cabut.pcs_akhir, 0) +
        COALESCE(cabut.gr_akhir, 0) +
        COALESCE(cabut.eot, 0) +
        COALESCE(cabut.gr_flx, 0) +
        COALESCE(cabut.susut, 0) +
        COALESCE(cabut.ttl_rp, 0) +
        COALESCE(denda.nominal, 0) != 0
      ");

        return $query;
    }

    public static function getRekapGlobal($tgl1, $tgl2, $id_pengawas)
    {
        return DB::select("SELECT a.id_anak,b.name as pgws,
        absen.ttl as hariMasuk,
        a.nama as nm_anak, 
        a.id_kelas as kelas,
        cabut.pcs_awal,
        cabut.gr_awal,
        cabut.pcs_akhir,
        cabut.gr_akhir,
        cabut.eot,
        cabut.gr_flx,
        cabut.susut,
        cabut.ttl_rp,
        eo.eo_awal,
        eo.eo_akhir,
        eo.susut as eo_susut,
        eo.ttl_rp as eo_ttl_rp,
        sortir.pcs_awal as sortir_pcs_awal,
        sortir.pcs_akhir as sortir_pcs_akhir,
        sortir.gr_awal as sortir_gr_awal,
        sortir.gr_akhir as sortir_gr_akhir,
        sortir.susut as sortir_susut,
        sortir.ttl_rp as sortir_ttl_rp,
        dll.ttl_rp_dll,
        denda.ttl_rp_denda
        FROM tb_anak as a
        JOIN users as b on a.id_pengawas = b.id
        LEFT JOIN (
                  SELECT 
                    id_anak, 
                    sum(pcs_awal) as pcs_awal, 
                    sum(gr_awal) as gr_awal, 
                    sum(gr_akhir) as gr_akhir, 
                    sum(pcs_akhir) as pcs_akhir, 
                    sum(pcs_hcr) as pcs_hcr, 
                    sum(eot) as eot, 
                    sum(gr_flx) as gr_flx, 
                    SUM(rupiah) as rupiah, 
                    sum((1 - (gr_flx + gr_akhir) / gr_awal) * 100) as susut, 
                    SUM(ttl_rp) as ttl_rp 
                  FROM `cabut` 
                  WHERE penutup = 'T' 
                  GROUP BY id_anak
        ) as cabut on a.id_anak = cabut.id_anak
        LEFT join (
            SELECT 
            id_anak,
            sum(gr_eo_awal) as eo_awal,
            sum(gr_eo_akhir) as eo_akhir,
            sum(ttl_rp) as ttl_rp,
            sum((1 - (gr_eo_akhir / gr_eo_awal)) * 100) as susut
            FROM eo 
            WHERE penutup = 'T' 
            GROUP by id_anak
        ) as eo on eo.id_anak = a.id_anak
        LEFT join (
            SELECT 
            id_anak,
            sum(pcs_awal) as pcs_awal, 
            sum(gr_awal) as gr_awal, 
            sum(pcs_akhir) as pcs_akhir, 
            sum(gr_akhir) as gr_akhir, 
            sum(ttl_rp) as ttl_rp,
            sum((1 - gr_akhir / gr_awal) * 100) as susut
            FROM `sortir` WHERE penutup = 'T' GROUP BY id_anak
        ) as sortir on a.id_anak = sortir.id_anak
        JOIN (
            SELECT *, count(*) as ttl FROM absen AS a 
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
             group BY a.id_anak
        ) as absen on absen.id_anak = cabut.id_anak 
        LEFT JOIN (
            SELECT id_anak,sum(rupiah) as ttl_rp_dll 
            FROM `tb_hariandll` 
            WHERE tgl BETWEEN '$tgl1' AND '$tgl2' GROUP by id_anak
        ) as dll on a.id_anak = dll.id_anak
        LEFT JOIN (
            SELECT id_anak, sum(nominal) as ttl_rp_denda 
            FROM `tb_denda` 
            WHERE tgl BETWEEN '$tgl1' AND '$tgl2' GROUP by id_anak
        ) as denda ON a.id_anak = denda.id_anak
        WHERE b.id = '$id_pengawas'");
    }
    public static function getPengawasRekap($bulan, $tahun)
    {
        return DB::select("SELECT 
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
                    WHERE MONTH(a.tgl) = '$bulan' AND YEAR(a.tgl) = '$tahun'
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
                    WHERE MONTH(tgl) = '$bulan' AND YEAR(tgl) = '$tahun'
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
    }
    public static function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }
    public static function getAnakTambah($cabut = null)
    {
        $id_user = auth()->user()->id;
        $whereQ = empty($cabut) ? "AND c.no_box = 9999" : '';
        return DB::select("SELECT c.id_cabut,a.id_anak,a.nama,b.kelas FROM `tb_anak` as a
        LEFT JOIN tb_kelas as b ON a.id_kelas = b.id_kelas
        LEFT JOIN cabut as c ON a.id_anak = c.id_anak AND DATE(c.tgl_terima) = CURDATE()
        WHERE a.id_pengawas = '$id_user' $whereQ AND a.id_anak $cabut IN (
            SELECT id_anak
            FROM cabut
            WHERE DATE(tgl_terima) = CURDATE()
        ) AND a.id_anak NOT IN (
            SELECT id_anak
            FROM absen
            WHERE DATE(tgl) = CURDATE() AND ket = 'cabut sisa'
        )");
    }
    public static function getStokBk($no_box = null)
    {
        $id_user = auth()->user()->id;
        $query = !empty($no_box) ? "selectOne" : 'select';
        $noBoxAda = !empty($no_box) ? "a.no_box = '$no_box' AND" : '';
        return DB::$query("SELECT a.no_box, a.pcs_awal,b.pcs_awal as pcs_cabut,a.gr_awal,b.gr_awal as gr_cabut FROM `bk` as a
        LEFT JOIN (
            SELECT max(no_box) as no_box,sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal  FROM `cabut` GROUP BY no_box,id_pengawas
        ) as b ON a.no_box = b.no_box WHERE  $noBoxAda a.penerima = '$id_user' AND a.kategori LIKE '%cabut%'");
    }
}
