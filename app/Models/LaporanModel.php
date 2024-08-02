<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanModel extends Model
{
    use HasFactory;

    public static function LaporanPerPartai($show)
    {
        // Dapatkan nomor halaman saat ini
        $page = request()->get('page', 1);
        $perPage = $show ?? 10; // Jumlah item per halaman
        $offset = ($page - 1) * $perPage;

        // Jalankan query untuk hasil ter-paginate
        $query = "SELECT a.nm_partai, 
       SUM(a.pcs_awal) as pcs_awal, 
       SUM(a.gr_awal) as gr_awal,
       a.hrga_satuan,

       SUM(b.pcs_akhir) as pcs_cbt, 
       SUM(b.gr_akhir) as gr_cbt, 
       SUM(((a.hrga_satuan * a.gr_awal) + b.ttl_rp ) / b.gr_akhir) as rp_gram_cbt, 
       ((1-(b.gr_akhir / a.gr_awal)) * 100) as sst_cbt,

       SUM(c.pcs_akhir) as pcs_ctk, 
       SUM(c.gr_akhir) as gr_ctk, 
       SUM((((a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp ) / c.gr_akhir)) as rp_gram_ctk, 
       (((1-((c.gr_akhir + c.gr_tdk_cetak) / a.gr_awal)) * 100)) as sst_ctk,

       SUM(d.pcs_akhir) as pcs_str, 
       SUM(d.gr_akhir) as gr_str, 
       SUM((((a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp + d.ttl_rp ) / d.gr_akhir)) as rp_gram_str, 
       (((1-(d.gr_akhir / a.gr_awal)) * 100)) as sst_str,

       SUM(e.gr_eo_akhir) as gr_eo, 
       SUM((((a.hrga_satuan * a.gr_awal) + e.ttl_rp ) / e.gr_eo_akhir)) as rp_gram_eo, 
       (((1-(e.gr_eo_akhir / a.gr_awal)) * 100)) as sst_eo,

       SUM((a.hrga_satuan * a.gr_awal)) as cost_bk, 
       SUM(b.ttl_rp) as cost_cbt, 
       SUM(c.ttl_rp) as cost_ctk, 
       SUM(d.ttl_rp) as cost_str, 
       SUM(e.ttl_rp) as cost_eo, 
       SUM(f.ttl_rp) as cost_cu, 
       SUM(j.cost_dll) as cost_dll,

       SUM((g.rp_gr * b.gr_akhir)) as oprasional_cbt, 
       SUM((d.gr_akhir * h.rp_gr)) as oprasional_str, 
       SUM(f.oprasional_cu) as oprasional_cu,
       SUM(c.oprasional_ctk) as oprasional_ctk, 
       SUM((e.gr_eo_akhir * i.rp_gr)) as oprasional_eo, 
       
       SUM((k.rp_harian_cbt * b.gr_akhir)) as harian_cbt,
       SUM(c.harian_ctk) as harian_ctk, 
       SUM((l.rp_harian_str * d.gr_akhir)) as harian_str, 
       SUM((m.rp_harian_eo * e.gr_eo_akhir)) as harian_eo
        FROM bk as a 
        LEFT JOIN cabut as b ON b.no_box = a.no_box
        LEFT JOIN oprasional as g ON g.bulan = b.bulan_dibayar
        LEFT JOIN (
            SELECT SUM(k.rupiah / b.gr) as rp_harian_cbt, k.bulan_dibayar, k.tahun_dibayar
            FROM tb_hariandll as k 
            LEFT JOIN oprasional as b ON b.bulan = k.bulan_dibayar AND b.tahun = k.tahun_dibayar
            GROUP BY k.bulan_dibayar, k.tahun_dibayar
        ) as k ON k.bulan_dibayar = b.bulan_dibayar AND k.tahun_dibayar = b.tahun_dibayar
        LEFT JOIN (
            SELECT c.no_box, c.pcs_akhir, c.gr_akhir, c.gr_tdk_cetak, c.ttl_rp, (e.rp_gr * c.gr_akhir) as oprasional_ctk, (k.rp_harian_ctk * c.gr_akhir) as harian_ctk
            FROM cetak_new as c
            LEFT JOIN kelas_cetak as d ON d.id_kelas_cetak = c.id_kelas_cetak
            LEFT JOIN oprasional as e ON e.bulan = c.bulan_dibayar
            LEFT JOIN (
                SELECT SUM(k.rupiah / b.gr) as rp_harian_ctk, k.bulan_dibayar
                FROM tb_hariandll as k 
                LEFT JOIN oprasional as b ON b.bulan = k.bulan_dibayar AND b.tahun = k.tahun_dibayar
                GROUP BY k.bulan_dibayar, k.tahun_dibayar
            ) as k ON k.bulan_dibayar = c.bulan_dibayar
            WHERE d.kategori= 'CTK'
        ) as c ON c.no_box = a.no_box
        LEFT JOIN sortir as d ON d.no_box = a.no_box
        LEFT JOIN oprasional as h ON h.bulan = d.bulan
        LEFT JOIN (
            SELECT SUM(k.rupiah / b.gr) as rp_harian_str, k.bulan_dibayar
            FROM tb_hariandll as k 
            LEFT JOIN oprasional as b ON b.bulan = k.bulan_dibayar AND b.tahun = k.tahun_dibayar
            GROUP BY k.bulan_dibayar, k.tahun_dibayar
        ) as l ON l.bulan_dibayar = d.bulan
        LEFT JOIN eo as e ON e.no_box = a.no_box
        LEFT JOIN oprasional as i ON i.bulan = e.bulan_dibayar
        LEFT JOIN (
            SELECT SUM(k.rupiah / b.gr) as rp_harian_eo, k.bulan_dibayar
            FROM tb_hariandll as k 
            LEFT JOIN oprasional as b ON b.bulan = k.bulan_dibayar AND b.tahun = k.tahun_dibayar
            GROUP BY k.bulan_dibayar, k.tahun_dibayar
        ) as m ON m.bulan_dibayar = e.bulan_dibayar
        LEFT JOIN (
            SELECT c.no_box, c.pcs_akhir, c.gr_akhir, c.gr_tdk_cetak, c.ttl_rp, (c.gr_akhir * e.rp_gr) as oprasional_cu
            FROM cetak_new as c
            LEFT JOIN kelas_cetak as d ON d.id_kelas_cetak = c.id_kelas_cetak
            LEFT JOIN oprasional as e ON e.bulan = c.bulan_dibayar
            WHERE d.kategori= 'CU'
        ) as f ON f.no_box = a.no_box
        LEFT JOIN (
            SELECT j.no_box, SUM(j.rupiah) as cost_dll 
            FROM tb_hariandll as j 
            GROUP BY j.no_box
        ) as j ON j.no_box =  a.no_box
        WHERE a.kategori = 'cabut' AND a.baru = 'baru'
        GROUP BY a.nm_partai
        LIMIT :limit OFFSET :offset;
        ";

        // Jalankan query dengan limit dan offset
        $items = DB::select($query, [
            'limit' => $perPage,
            'offset' => $offset,
        ]);

        // Hitung total record
        $totalQuery = "SELECT COUNT(DISTINCT a.nm_partai) as count 
        FROM bk as a 
        LEFT JOIN cabut as b ON b.no_box = a.no_box
        WHERE a.kategori = 'cabut' AND a.baru = 'baru'";

        $total = DB::select($totalQuery)[0]->count;

        $options = [10];
        if ($total >= 50) {
            $options = [10, 50];
            while ($total >= end($options)) {
                $options[] = end($options) * 2;
            }
        }
        $options[] = $total;

        // Buat instance LengthAwarePaginator
        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => array_merge(request()->query(), ['show' => $perPage]), // Menambahkan parameter show
        ]);

        return [
            'paginator' => $paginator,
            'options' => $options,
        ];
    }

    public static function LaporanPerPartaiSearch($search)
    {
        $query = "SELECT a.nm_partai, 
       SUM(a.pcs_awal) as pcs_awal, 
       SUM(a.gr_awal) as gr_awal,
       a.hrga_satuan,

       SUM(b.pcs_akhir) as pcs_cbt, 
       SUM(b.gr_akhir) as gr_cbt, 
       SUM(((a.hrga_satuan * a.gr_awal) + b.ttl_rp ) / b.gr_akhir) as rp_gram_cbt, 
       ((1-(b.gr_akhir / a.gr_awal)) * 100) as sst_cbt,

       SUM(c.pcs_akhir) as pcs_ctk, 
       SUM(c.gr_akhir) as gr_ctk, 
       SUM((((a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp ) / c.gr_akhir)) as rp_gram_ctk, 
       (((1-((c.gr_akhir + c.gr_tdk_cetak) / a.gr_awal)) * 100)) as sst_ctk,

       SUM(d.pcs_akhir) as pcs_str, 
       SUM(d.gr_akhir) as gr_str, 
       SUM((((a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp + d.ttl_rp ) / d.gr_akhir)) as rp_gram_str, 
       (((1-(d.gr_akhir / a.gr_awal)) * 100)) as sst_str,

       SUM(e.gr_eo_akhir) as gr_eo, 
       SUM((((a.hrga_satuan * a.gr_awal) + e.ttl_rp ) / e.gr_eo_akhir)) as rp_gram_eo, 
       (((1-(e.gr_eo_akhir / a.gr_awal)) * 100)) as sst_eo,

       SUM((a.hrga_satuan * a.gr_awal)) as cost_bk, 
       SUM(b.ttl_rp) as cost_cbt, 
       SUM(c.ttl_rp) as cost_ctk, 
       SUM(d.ttl_rp) as cost_str, 
       SUM(e.ttl_rp) as cost_eo, 
       SUM(f.ttl_rp) as cost_cu, 
       SUM(j.cost_dll) as cost_dll,

       SUM((g.rp_gr * b.gr_akhir)) as oprasional_cbt, 
       SUM((d.gr_akhir * h.rp_gr)) as oprasional_str, 
       SUM(f.oprasional_cu) as oprasional_cu,
       SUM(c.oprasional_ctk) as oprasional_ctk, 
       SUM((e.gr_eo_akhir * i.rp_gr)) as oprasional_eo, 
       
       SUM((k.rp_harian_cbt * b.gr_akhir)) as harian_cbt,
       SUM(c.harian_ctk) as harian_ctk, 
       SUM((l.rp_harian_str * d.gr_akhir)) as harian_str, 
       SUM((m.rp_harian_eo * e.gr_eo_akhir)) as harian_eo
        FROM bk as a 
        LEFT JOIN cabut as b ON b.no_box = a.no_box
        LEFT JOIN oprasional as g ON g.bulan = b.bulan_dibayar
        LEFT JOIN (
            SELECT SUM(k.rupiah / b.gr) as rp_harian_cbt, k.bulan_dibayar, k.tahun_dibayar
            FROM tb_hariandll as k 
            LEFT JOIN oprasional as b ON b.bulan = k.bulan_dibayar AND b.tahun = k.tahun_dibayar
            GROUP BY k.bulan_dibayar, k.tahun_dibayar
        ) as k ON k.bulan_dibayar = b.bulan_dibayar AND k.tahun_dibayar = b.tahun_dibayar
        LEFT JOIN (
            SELECT c.no_box, c.pcs_akhir, c.gr_akhir, c.gr_tdk_cetak, c.ttl_rp, (e.rp_gr * c.gr_akhir) as oprasional_ctk, (k.rp_harian_ctk * c.gr_akhir) as harian_ctk
            FROM cetak_new as c
            LEFT JOIN kelas_cetak as d ON d.id_kelas_cetak = c.id_kelas_cetak
            LEFT JOIN oprasional as e ON e.bulan = c.bulan_dibayar
            LEFT JOIN (
                SELECT SUM(k.rupiah / b.gr) as rp_harian_ctk, k.bulan_dibayar
                FROM tb_hariandll as k 
                LEFT JOIN oprasional as b ON b.bulan = k.bulan_dibayar AND b.tahun = k.tahun_dibayar
                GROUP BY k.bulan_dibayar, k.tahun_dibayar
            ) as k ON k.bulan_dibayar = c.bulan_dibayar
            WHERE d.kategori= 'CTK'
        ) as c ON c.no_box = a.no_box
        LEFT JOIN sortir as d ON d.no_box = a.no_box
        LEFT JOIN oprasional as h ON h.bulan = d.bulan
        LEFT JOIN (
            SELECT SUM(k.rupiah / b.gr) as rp_harian_str, k.bulan_dibayar
            FROM tb_hariandll as k 
            LEFT JOIN oprasional as b ON b.bulan = k.bulan_dibayar AND b.tahun = k.tahun_dibayar
            GROUP BY k.bulan_dibayar, k.tahun_dibayar
        ) as l ON l.bulan_dibayar = d.bulan
        LEFT JOIN eo as e ON e.no_box = a.no_box
        LEFT JOIN oprasional as i ON i.bulan = e.bulan_dibayar
        LEFT JOIN (
            SELECT SUM(k.rupiah / b.gr) as rp_harian_eo, k.bulan_dibayar
            FROM tb_hariandll as k 
            LEFT JOIN oprasional as b ON b.bulan = k.bulan_dibayar AND b.tahun = k.tahun_dibayar
            GROUP BY k.bulan_dibayar, k.tahun_dibayar
        ) as m ON m.bulan_dibayar = e.bulan_dibayar
        LEFT JOIN (
            SELECT c.no_box, c.pcs_akhir, c.gr_akhir, c.gr_tdk_cetak, c.ttl_rp, (c.gr_akhir * e.rp_gr) as oprasional_cu
            FROM cetak_new as c
            LEFT JOIN kelas_cetak as d ON d.id_kelas_cetak = c.id_kelas_cetak
            LEFT JOIN oprasional as e ON e.bulan = c.bulan_dibayar
            WHERE d.kategori= 'CU'
        ) as f ON f.no_box = a.no_box
        LEFT JOIN (
            SELECT j.no_box, SUM(j.rupiah) as cost_dll 
            FROM tb_hariandll as j 
            GROUP BY j.no_box
        ) as j ON j.no_box =  a.no_box
        WHERE a.kategori = 'cabut' AND a.baru = 'baru' AND a.nm_partai like '%$search%'
        GROUP BY a.nm_partai";

        return DB::selectOne($query);
    }

    public static function LaporanDetailPartai($partai)
    {
        return DB::select("SELECT 
        a.nm_partai, 
        a.no_box, 
        a.pcs_awal, 
        a.gr_awal, 
        a.hrga_satuan, 
        b.pcs_akhir as pcs_cbt, 
        b.gr_akhir as gr_cbt, 
        (
            (
            (a.hrga_satuan * a.gr_awal) + b.ttl_rp
            ) / b.gr_akhir
        ) as rp_gram_cbt, 
        (
            (
            1 -(b.gr_akhir / a.gr_awal)
            ) * 100
        ) as sst_cbt, 
        c.pcs_akhir as pcs_ctk, 
        c.gr_akhir as gr_ctk, 
        (
            (
            (a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp
            ) / c.gr_akhir
        ) as rp_gram_ctk, 
        (
            (
            1 -
               ( (c.gr_akhir + c.gr_tdk_cetak) / c.gr_awal)
            
            ) * 100
        ) as sst_ctk, 
        d.pcs_akhir as pcs_str, 
        d.gr_akhir as gr_str, 
        (
            (
            (a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp + d.ttl_rp
            ) / d.gr_akhir
        ) as rp_gram_str, 
        (
            (
            1 -(d.gr_akhir / a.gr_awal)
            ) * 100
        ) as sst_str, 
        e.gr_eo_akhir as gr_eo, 
        (
            (
            (a.hrga_satuan * a.gr_awal) + e.ttl_rp
            ) / e.gr_eo_akhir
        ) as rp_gram_eo, 
        (
            (
            1 -(e.gr_eo_akhir / a.gr_awal)
            ) * 100
        ) as sst_eo, 
        (a.hrga_satuan * a.gr_awal) as cost_bk, 
        b.ttl_rp as cost_cbt, 
        c.ttl_rp as cost_ctk, 
        d.ttl_rp as cost_str, 
        e.ttl_rp as cost_eo, 
        -- f.ttl_rp as cost_cu, 
        (g.rp_gr * b.gr_akhir) as oprasional_cbt, 
        (d.gr_akhir * h.rp_gr) as oprasional_str, 
        -- f.oprasional_cu, 
        (c.oprasional_ctk) as oprasional_ctk, 
        (e.gr_eo_akhir * i.rp_gr) as oprasional_eo, 
        j.cost_dll, 
        (k.rp_harian_cbt * b.gr_akhir) as harian_cbt, 
        c.harian_ctk, 
        (l.rp_harian_str * d.gr_akhir) as harian_str, 
        (m.rp_harian_eo * e.gr_eo_akhir) as harian_eo 
        FROM 
        bk as a
 
        left join cabut as b on b.no_box = a.no_box
        left join oprasional as g on g.bulan = b.bulan_dibayar
        left join (
        	SELECT sum(k.rupiah / b.gr) as rp_harian_cbt, k.bulan_dibayar, k.tahun_dibayar
            FROM tb_hariandll as k 
            left join oprasional as b on b.bulan = k.bulan_dibayar and b.tahun = k.tahun_dibayar
            group by k.bulan_dibayar, k.tahun_dibayar
        ) as k on k.bulan_dibayar = b.bulan_dibayar and k.tahun_dibayar = b.tahun_dibayar


        left join (
            SELECT c.no_box, sum(c.pcs_akhir) as pcs_akhir, sum(c.gr_akhir) as gr_akhir,sum(c.gr_awal_ctk) as gr_awal, sum(c.gr_tdk_cetak) as gr_tdk_cetak, sum(c.ttl_rp) as ttl_rp, (sum(e.rp_gr) * sum(c.gr_akhir)) as oprasional_ctk, (sum(k.rp_harian_ctk) * sum(c.gr_akhir)) as harian_ctk
            FROM cetak_new as c
            left join kelas_cetak as d on d.id_kelas_cetak = c.id_kelas_cetak
            left join oprasional as e on e.bulan = c.bulan_dibayar
            left join (
        	SELECT sum(k.rupiah / b.gr) as rp_harian_ctk, k.bulan_dibayar
                FROM tb_hariandll as k 
                left join oprasional as b on b.bulan = k.bulan_dibayar and b.tahun = k.tahun_dibayar
                group by k.bulan_dibayar, k.tahun_dibayar
        	) as k on k.bulan_dibayar = c.bulan_dibayar
            where d.kategori in ('CTK', 'CU')
            GROUP BY c.no_box
        ) as c on c.no_box = a.no_box

        left join sortir as d on d.no_box = a.no_box
        left join oprasional as h on h.bulan = d.bulan
        left join (
        	SELECT sum(k.rupiah / b.gr) as rp_harian_str, k.bulan_dibayar
                FROM tb_hariandll as k 
                left join oprasional as b on b.bulan = k.bulan_dibayar and b.tahun = k.tahun_dibayar
                group by k.bulan_dibayar, k.tahun_dibayar
        ) as l on l.bulan_dibayar = d.bulan

        left join eo as e on e.no_box = a.no_box
        left join oprasional as i on i.bulan = e.bulan_dibayar
        left join (
        	SELECT sum(k.rupiah / b.gr) as rp_harian_eo, k.bulan_dibayar
                FROM tb_hariandll as k 
                left join oprasional as b on b.bulan = k.bulan_dibayar and b.tahun = k.tahun_dibayar
                group by k.bulan_dibayar, k.tahun_dibayar
        ) as m on m.bulan_dibayar = e.bulan_dibayar

        -- left join (
        --     SELECT c.no_box, c.pcs_akhir, c.gr_akhir, c.gr_tdk_cetak, c.ttl_rp, (c.gr_akhir * e.rp_gr) as oprasional_cu
        --     FROM cetak_new as c
        --     left join kelas_cetak as d on d.id_kelas_cetak = c.id_kelas_cetak
        --     left join oprasional as e on e.bulan = c.bulan_dibayar
        --     where d.kategori= 'CU'
        -- ) as f on f.no_box = a.no_box

        left join (
            SELECT j.no_box, sum(j.rupiah) as cost_dll FROM tb_hariandll as j group by j.no_box
        ) as j on j.no_box =  a.no_box
        where a.kategori = 'cabut' and a.baru = 'baru' AND a.nm_partai = '$partai'");
    }

    public static function LaporanPerPartaiLama()
    {
        $result = DB::select("SELECT a.nm_partai, a.no_box, a.pcs_awal, a.gr_awal,a.hrga_satuan,
        b.pcs_akhir as pcs_cbt, b.gr_akhir as gr_cbt, 
        (((a.hrga_satuan * a.gr_awal) + b.ttl_rp ) / b.gr_akhir) as rp_gram_cbt, 
        ((1-(b.gr_akhir / a.gr_awal)) * 100) as sst_cbt,
        c.pcs_akhir as pcs_ctk, c.gr_akhir as gr_ctk, (((a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp ) / c.gr_akhir) as rp_gram_ctk, ((1-((c.gr_akhir + c.gr_tdk_cetak) / a.gr_awal)) * 100) as sst_ctk,
        d.pcs_akhir as pcs_str, d.gr_akhir as gr_str, (((a.hrga_satuan * a.gr_awal) + b.ttl_rp + c.ttl_rp + d.ttl_rp ) / c.gr_akhir) as rp_gram_str, ((1-(d.gr_akhir / a.gr_awal)) * 100) as sst_str,
        e.gr_eo_akhir as gr_eo, (((a.hrga_satuan * a.gr_awal) + e.ttl_rp ) / e.gr_eo_akhir) as rp_gram_eo, ((1-(e.gr_eo_akhir / a.gr_awal)) * 100) as sst_eo,
        (a.hrga_satuan * a.gr_awal) as cost_bk, b.ttl_rp as cost_cbt, c.ttl_rp as cost_ctk, d.ttl_rp as cost_str, e.ttl_rp as cost_eo, f.ttl_rp as cost_cu, (g.rp_gr * b.gr_akhir) as oprasional_cbt, (d.gr_akhir * h.rp_gr) as oprasional_str, f.oprasional_cu,
        c.oprasional_ctk, (e.gr_eo_akhir * i.rp_gr ) as oprasional_eo, j.cost_dll
        FROM bk as a 
        left join cabut as b on b.no_box = a.no_box
        left join oprasional as g on g.bulan = b.bulan_dibayar

        left join (
            SELECT c.no_box, c.pcs_akhir, c.gr_akhir, c.gr_tdk_cetak, c.ttl_rp, (e.rp_gr * c.gr_akhir) as oprasional_ctk
            FROM cetak_new as c
            left join kelas_cetak as d on d.id_kelas_cetak = c.id_kelas_cetak
            left join oprasional as e on e.bulan = c.bulan_dibayar
            where d.kategori= 'CTK'
        ) as c on c.no_box = a.no_box

        left join sortir as d on d.no_box = a.no_box
        left join oprasional as h on h.bulan = d.bulan

        left join eo as e on e.no_box = a.no_box
        left join oprasional as i on i.bulan = e.bulan_dibayar

        left join (
            SELECT c.no_box, c.pcs_akhir, c.gr_akhir, c.gr_tdk_cetak, c.ttl_rp, (c.gr_akhir * e.rp_gr) as oprasional_cu
            FROM cetak_new as c
            left join kelas_cetak as d on d.id_kelas_cetak = c.id_kelas_cetak
            left join oprasional as e on e.bulan = c.bulan_dibayar
            where d.kategori= 'CU'
        ) as f on f.no_box = a.no_box

        left join (
            SELECT j.no_box, sum(j.rupiah) as cost_dll FROM tb_hariandll as j group by j.no_box
        ) as j on j.no_box =  a.no_box


        where a.kategori = 'cabut' and a.baru = 'baru'
        
        ;");
        return $result;
    }

    public static function LaporanDetailCetak($partai, $bulan)
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai,
        a.tgl, d.name as pengawas,  c.nama, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_akhir, a.gr_akhir,
        a.rp_satuan, a.ttl_rp, a.bulan_dibayar
        FROM cetak_new as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai' and a.bulan_dibayar = '$bulan'
        order by a.tgl ASC;");

        return $result;
    }
    public static function LaporanDetailCabut($partai, $bulan)
    {
        $result = DB::select(
            "SELECT * , ((1 - (gr_akhir/gr_awal)) * 100 ) as susut
        FROM (
        SELECT a.no_box, d.name as pengawas, c.nama, a.tgl_terima as tgl, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp, 'cabut' as kerja, a.bulan_dibayar
        FROM cabut as a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai' and a.bulan_dibayar = '$bulan'
        UNION ALL
        SELECT a.no_box, d.name as pengawas, c.nama, a.tgl_ambil as tgl, 0 as pcs_awal, a.gr_eo_awal as gr_awal, 0 as pcs_akhir, a.gr_eo_akhir as gr_akhir, 
        a.ttl_rp, 
        'eo' as kerja , a.bulan_dibayar
        FROM eo as a
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai' and a.bulan_dibayar = '$bulan'
        ) AS combined_result
        order by  ((1 - (gr_akhir/gr_awal)) * 100) DESC ;"
        );

        return $result;
    }

    public static function LaporanDetailSortir($partai, $bulan)
    {
        $result = DB::select("SELECT a.no_box, b.nm_partai,
        a.tgl, d.name as pengawas,  c.nama, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp, a.bulan
        FROM sortir as a 
        left join bk as b on b.no_box = a.no_box and b.kategori = 'cabut'
        left join tb_anak as c on c.id_anak = a.id_anak
        left join users as d on d.id = a.id_pengawas
        where b.nm_partai = '$partai' and a.bulan = '$bulan'
        order by a.tgl ASC;");

        return $result;
    }

    public static function LaporanDetailBox($no_box)
    {
        $cabut = DB::selectOne("SELECT b.name as pgws, c.nama as nm_anak, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp,
        (d.hrga_satuan * d.gr_awal) as cost_bk
        FROM cabut as a 
        left join users as b on b.id = a.id_pengawas
        left join tb_anak as c on c.id_anak = a.id_anak
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
        where a.no_box = '$no_box'
        ");

        $cetak = DB::selectOne("SELECT b.name as pgws, c.nama as nm_anak, a.pcs_awal_ctk as pcs_awal, a.gr_awal_ctk as gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp,
        (d.hrga_satuan * d.gr_awal) as cost_bk, e.ttl_rp as cost_cbt
        FROM cetak_new as a
        left join users as b on b.id = a.id_pengawas
        left join tb_anak as c on c.id_anak = a.id_anak
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
        left join cabut as e on e.no_box = a.no_box
        where a.no_box = '$no_box'
        ");

        $sortir = DB::selectOne("SELECT b.name as pgws, c.nama as nm_anak, a.pcs_awal, a.gr_awal, a.pcs_akhir, a.gr_akhir, a.ttl_rp,
        (d.hrga_satuan * d.gr_awal) as cost_bk, e.ttl_rp as cost_cbt, f.ttl_rp as cost_ctk
        FROM sortir as a
        left join users as b on b.id = a.id_pengawas
        left join tb_anak as c on c.id_anak = a.id_anak
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
        left join cabut as e on e.no_box = a.no_box
        left join cetak_new as f on f.no_box = a.no_box
        where a.no_box = '$no_box'
        ");


        return (object)[

            'cabut' => $cabut,
            'cetak' => $cetak,
            'sortir' => $sortir

        ];
    }

    public static function LaporanPartai()
    {
        $result = DB::select("SELECT a.nm_partai, sum(a.pcs_awal) as pcs_bk, sum(a.gr_awal) as gr_bk , 
        sum(a.gr_awal * a.hrga_satuan) as ttl_rp_bk, sum(b.gr_akhir) as gr_cbt , sum(b.ttl_rp) as cost_cbt,
        sum(c.gr_eo_akhir) as gr_eo_akhir, sum(c.ttl_rp) as cost_eo,
        sum(d.gr_akhir) as gr_ctk , sum(d.ttl_rp) as cost_ctk,
        sum(e.gr_akhir) as gr_str, sum(e.ttl_rp) as cost_str

        FROM bk as a

        left join cabut as b on b.no_box = a.no_box and b.selesai = 'Y'
        left join eo as c on c.no_box = a.no_box and c.selesai ='Y'
        left join cetak_new as d on d.no_box = a.no_box and d.selesai = 'Y'
        left join sortir as e on e.no_box = a.no_box and e.selesai = 'Y'
    
        where a.baru = 'baru' and a.kategori ='cabut' 
        GROUP by a.nm_partai
        order by a.nm_partai ASC
        ;");

        return $result;
    }
}
