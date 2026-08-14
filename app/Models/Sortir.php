<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class Sortir extends Model
{
    use HasFactory;
    protected $table = 'sortir';

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
                                sum(COALESCE(gr_akhir,0) + COALESCE(gr_tdk_sortir,0)) as gr_akhir, sum(COALESCE(pcs_akhir,0) + COALESCE(pcs_tdk_sortir,0)) as pcs_akhir,
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
        $filterPengawas = auth()->user()->posisi_id == 1 ? '' : 'AND stock.id_penerima = ?';
        $bindings = $filterPengawas === '' ? [] : [$id_user];

        return DB::select("SELECT
                stock.no_box,
                MAX(bk.nm_partai) AS nm_partai,
                MAX(users.name) AS name,
                stock.pcs AS pcs_awal,
                stock.gr AS gr_awal,
                MAX(bk.hrga_satuan) AS hrga_satuan,
                MAX(bk.ttl_rp) AS ttl_rp
            FROM (
                SELECT
                    fs.no_box,
                    MAX(fs.id_penerima) AS id_penerima,
                    SUM(fs.pcs_awal) AS pcs,
                    SUM(fs.gr_awal) AS gr
                FROM formulir_sarang AS fs
                WHERE fs.kategori = 'sortir'
                  AND NOT EXISTS (
                      SELECT 1 FROM sortir AS s
                      WHERE s.no_box = fs.no_box
                        AND s.id_anak != 0
                  )
                GROUP BY fs.no_box
            ) AS stock
            INNER JOIN (
                SELECT
                    b.no_box,
                    MAX(b.nm_partai) AS nm_partai,
                    MAX(b.hrga_satuan) AS hrga_satuan,
                    SUM(b.gr_awal * b.hrga_satuan) AS ttl_rp
                FROM bk AS b
                WHERE b.kategori = 'cabut'
                  AND b.baru = 'baru'
                  AND b.no_box != 9999
                  AND NOT EXISTS (
                      SELECT 1
                      FROM grading AS sent_grading
                      INNER JOIN grading_partai AS sent_result
                          ON sent_result.no_invoice = sent_grading.no_invoice
                      WHERE sent_grading.no_box_sortir = b.no_box
                        AND sent_result.sudah_kirim = 'Y'
                  )
                GROUP BY b.no_box
            ) AS bk ON bk.no_box = stock.no_box
            LEFT JOIN users ON users.id = stock.id_penerima
            WHERE 1 = 1 $filterPengawas
            GROUP BY stock.no_box, stock.pcs, stock.gr
            ORDER BY MAX(users.name), stock.no_box", $bindings);
    }
    public static function sortir_proses($id_user)
    {
        return collect(OpnameNewModel::sortir_proses())->map(function ($row) {
            $row->pcs_awal = $row->pcs;
            $row->gr_awal = $row->gr;
            return $row;
        })->all();
    }
    public static function sortir_selesai($id_user)
    {
        return collect(OpnameNewModel::sortir_selesai())->map(function ($row) {
            $row->pcs_awal = $row->pcs;
            $row->gr_awal = $row->gr;
            $row->pcs_tdk_sortir = 0;
            $row->gr_tdk_sortir = 0;
            return $row;
        })->all();
    }
}
