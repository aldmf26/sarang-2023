<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga5_2RiwayatPemeliharaanController extends Controller
{
    public function index()
    {

        $item_perbaikan = DB::select("SELECT id, nama, lokasi, lantai, merk, no_identifikasi, kategori
        FROM (
            SELECT if(b.gabung = 'Y',c.id,b.id) id , if(b.gabung = 'Y' , c.lokasi,  CONCAT(b.nama, ' ', b.merk , ' ' , b.no_identifikasi)) as nama, c.lokasi,c.lantai,b.merk,b.no_identifikasi,a.item_id, if(b.gabung = 'Y' ,'lokasi','item') kategori
            FROM hrga5_3permintaan as a
            LEFT JOIN item_perawatan as b ON b.id = a.item_id
            LEFT JOIN lokasi as c ON c.id = b.lokasi_id
            
    
            UNION ALL

            SELECT if(e.gabung = 'Y',f.id,e.id) id , if(e.gabung = 'Y' , f.lokasi,  CONCAT(e.nama, ' ', e.merk,' ',e.no_identifikasi)) as nama ,  f.lokasi,f.lantai,e.merk,e.no_identifikasi,d.item_id, if(e.gabung = 'Y' ,'lokasi','item') kategori
            FROM perawatan as d
            LEFT JOIN item_perawatan as e ON e.id = d.item_id
            LEFT JOIN lokasi as f ON f.id = e.lokasi_id
        ) AS combined
            GROUP BY nama
            order by nama ASC, lokasi ASC
            ");

        $data = [
            'title' => 'Riwayat Pemeliharaan',
            'item_perbaikan' => $item_perbaikan,
            'lokasi' => DB::table('lokasi')->get(),
        ];
        return view('hccp.hrga5_pemeliharaan.hrga2.index', $data);
    }

    public function store(Request $r)
    {
        $data = [
            'item_id' => $r->item_id,
            'tgl' => $r->tgl,
            'fungsi' => $r->fungsi,
            'kesimpulan' => $r->kesimpulan,
        ];
        DB::table('perawatan')->insert($data);
        return redirect()->route('hrga5_2.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function print(Request $r)
    {
        if ($r->kategori == 'item') {
            $item = DB::table('item_perawatan')
                ->leftJoin('lokasi', 'item_perawatan.lokasi_id', '=', 'lokasi.id')
                ->select('item_perawatan.*', 'lokasi.lokasi', 'lokasi.lantai')
                ->where('item_perawatan.id', $r->id)->first();

            $riwayat = DB::select("SELECT  b.nama , a.tgl, c.lokasi,c.lantai,b.merk,b.no_identifikasi,a.item_id, a.deskripsi_masalah as fungsi, d.detail_perbaikan as kesimpulan,'perbaikan' as ket
            FROM hrga5_3permintaan as a
            LEFT JOIN item_perawatan as b ON b.id = a.item_id
            LEFT JOIN lokasi as c ON c.id = b.lokasi_id
            left join detail_perbaikan as d on d.id_permintaan = a.id
            where a.item_id = $r->id
            GROUP by a.id
            
    
            UNION ALL

            SELECT  e.nama ,d.tgl,  f.lokasi,f.lantai,e.merk,e.no_identifikasi,d.item_id, d.fungsi, d.kesimpulan,'perawatan' as ket
            FROM perawatan as d
            LEFT JOIN item_perawatan as e ON e.id = d.item_id
            LEFT JOIN lokasi as f ON f.id = e.lokasi_id
            where d.item_id = $r->id
            group by d.id_perawatan
            
            order by tgl ASC
            ");
        } else {
            $item = DB::table('lokasi')->select('id', 'lokasi as nama', 'lokasi', 'lantai')->where('id', $r->id)->first();
            $riwayat = DB::select("SELECT  b.nama , a.tgl, c.lokasi,c.lantai,b.merk,b.no_identifikasi,a.item_id, a.deskripsi_masalah as fungsi, d.detail_perbaikan as kesimpulan, 'perbaikan' as ket
            FROM hrga5_3permintaan as a
            LEFT JOIN item_perawatan as b ON b.id = a.item_id
            LEFT JOIN lokasi as c ON c.id = b.lokasi_id
            left join detail_perbaikan as d on d.id_permintaan = a.id
            where c.id = $r->id and b.gabung = 'Y'
            GROUP by a.id
            
    
            UNION ALL

            SELECT  e.nama , d.tgl,  f.lokasi,f.lantai,e.merk,e.no_identifikasi,d.item_id, d.fungsi, d.kesimpulan, 'perawatan' as ket
            FROM perawatan as d
            LEFT JOIN item_perawatan as e ON e.id = d.item_id
            LEFT JOIN lokasi as f ON f.id = e.lokasi_id
            where f.id = $r->id and e.gabung = 'Y'
            group by d.id_perawatan
            
            order by tgl ASC
            ");
        }
        $data = [
            'title' => 'Riwayat Pemeliharaan',
            'item' => $item,
            'riwayat' => $riwayat,
        ];
        return view('hccp.hrga5_pemeliharaan.hrga2.print', $data);
    }
}
