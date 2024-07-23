<?php

namespace App\Http\Controllers;

use App\Models\CetakModel;
use App\Models\LaporanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Laporan_akhir extends Controller
{
    public function index(Request $r)
    {
        if (empty($r->bulan)) {
            $bulan = date('m');
        } else {
            $bulan = $r->bulan;
        }
        $data = [
            'title' => 'Laporan Partai',
            'partai' => LaporanModel::LaporanPerPartai(),
            'bulan' => $bulan,
            'cabut' => DB::selectOne("SELECT sum(a.gr_akhir) as gr_akhir FROM cabut as a where a.bulan_dibayar = '$bulan' and a.selesai ='Y'"),
            'eo' => DB::selectOne("SELECT sum(a.gr_eo_akhir) as gr_eo_akhir FROM eo as a where a.bulan_dibayar = '$bulan' and a.selesai ='Y'"),
            'ctk' => DB::selectOne("SELECT sum(a.gr_akhir) as gr_akhir FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where a.bulan_dibayar = '$bulan' and a.selesai ='Y' and b.kategori = 'CTK'"),
            'str' => DB::selectOne("SELECT sum(a.gr_akhir) as gr_akhir FROM sortir as a where a.bulan = '$bulan' and a.selesai ='Y'"),
            'cu' => DB::selectOne("SELECT sum(a.gr_akhir) as gr_akhir FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where a.bulan_dibayar = '$bulan' and a.selesai ='Y' and b.kategori = 'CU'"),
            'oprasional' => DB::table('oprasional')->where('bulan', $bulan)->first(),
            'bulandata' => DB::table('bulan')->get(),
            'gaji' => DB::selectOne("SELECT  sum(a.ttl_rp - a.ttl_denda) as ttl_rp
            FROM(
             SELECT sum(a.ttl_rp) as ttl_rp , 0 as ttl_denda
            FROM cabut as a 
            where a.bulan_dibayar = '6' and a.no_box != '999' and a.id_pengawas != '421' and a.penutup = 'Y'

            UNION ALL

            SELECT sum(b.ttl_rp) as ttl_rp, 0 as ttl_denda
            FROM eo as b 
            where b.bulan_dibayar = '6' and b.no_box != '999'

            UNION ALL 
            SELECT sum(c.ttl_rp) as ttl_rp , 0 as ttl_denda
            FROM cetak_new as c
            where c.bulan_dibayar = '6'

            UNION ALL
            SELECT sum(d.ttl_rp) as ttl_rp , 0 as ttl_denda
            FROM sortir as d 
            where d.bulan = '6'

            UNION ALL
            SELECT sum(e.rupiah) as ttl_rp , 0 as ttl_denda
            FROM tb_hariandll as e 
            where e.bulan_dibayar = '6'

            UNION ALL
            SELECT 0 as ttl_rp, sum(f.nominal) as ttl_denda 
            FROM tb_denda as f 
            where f.bulan_dibayar = '6')  as a
            ;")
        ];
        return view('home.laporan.lapPerpartai', $data);
    }

    public function get_bk_akhir(Request $r)
    {
        $data = [
            'partai' => $r->partai,
            'bk_akhir' => DB::table('bk_akhir')->where('nm_partai', $r->partai)->first()
        ];
        return view('home.laporan.bk_akhir', $data);
    }

    public function save_bk_akhir(Request $r)
    {
        DB::table('bk_akhir')->where('nm_partai', $r->partai)->delete();
        $data = [
            'nm_partai' => $r->partai,
            'pcs' => $r->pcs_akhir,
            'gr' => $r->gr_akhir,
        ];
        DB::table('bk_akhir')->insert($data);
        return redirect()->back()->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function get_detail_cetak(Request $r)
    {
        $data = [
            'title' => 'Detail Cetak',
            'detail' => LaporanModel::LaporanDetailCetak($r->partai, $r->bulan)
        ];
        return view('home.laporan.detail_cetak', $data);
    }
    public function get_detail_cabut(Request $r)
    {
        $data = [
            'title' => 'Detail Cabut',
            'detail' => LaporanModel::LaporanDetailCabut($r->partai, $r->bulan)
        ];
        return view('home.laporan.detail_cabut', $data);
    }
    public function get_detail_sortir(Request $r)
    {
        $data = [
            'title' => 'Detail Cabut',
            'detail' => LaporanModel::LaporanDetailSortir($r->partai, $r->bulan)
        ];
        return view('home.laporan.detail_sortir', $data);
    }

    public function summaryCetak(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $id_pengawas = '0';
        $summary = CetakModel::summary_cetak($bulan, $tahun);
        $data = [
            'title' => 'Summary Cetak',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'summary' => $summary,
        ];
        return view('home.laporan.summary', $data);
    }

    public function save_oprasional(Request $r)
    {
        DB::table('oprasional')->where('bulan_dibayar', $r->bulan_dibayar)->where('tahun_dibayar', $r->tahun_dibayar)->delete();

        $data = [
            'rupiah' => $r->total_rp,
            'bulan_dibayar' => $r->bulan_dibayar,
            'tahun_dibayar' => $r->tahun_dibayar,
            'admin' => auth()->user()->name
        ];

        DB::table('oprasional')->insert($data);
        return redirect()->back()->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function get_detail(Request $r)
    {
        $data = [
            'title' => 'Detail Box',
            'no_box' => $r->no_box,
            'partai' => $r->partai,
            'cabut' => LaporanModel::LaporanDetailBox($r->no_box)->cabut,
            'cetak' => LaporanModel::LaporanDetailBox($r->no_box)->cetak,
            'sortir' => LaporanModel::LaporanDetailBox($r->no_box)->sortir,
        ];

        return view('home.laporan.detail', $data);
    }

    public function lapPartai(Request $r)
    {
        if (empty($r->bulan)) {
            $bulan = date('m');
        } else {
            $bulan = $r->bulan;
        }
        $data = [
            'title' => 'Laporan Partai',
            'partai' => LaporanModel::LaporanPartai(),
            'bulan' => $bulan,
            // 'oprasional' => DB::table('oprasional')->where('bulan_dibayar', $bulan)->first()
        ];
        return view('home.laporan.lapPartai', $data);
    }

    public function saveoprasional(Request $r)
    {
        $formattedNumber = $r->biaya_oprasional;
        // Hapus pemisah ribuan untuk mendapatkan angka mentah
        $rawNumber = str_replace(',', '', $formattedNumber);
        // Validasi angka mentah
        if (!is_numeric($rawNumber)) {
            return redirect()->back()->withErrors(['biaya_oprasional' => 'The number is not valid.']);
        }
        DB::table('oprasional')->where('bulan', $r->bulan)->where('tahun', $r->tahun)->delete();
        $data = [
            'rp_oprasional' => $rawNumber,
            'bulan' => $r->bulan,
            'tahun' => $r->tahun,
            'rp_gr' => $rawNumber / $r->gr_akhir,
            'gr' => $r->gr_akhir
        ];
        DB::table('oprasional')->insert($data);
        return redirect()->back()->with('sukses', 'Data Berhasil ditambahkan');
    }
}
