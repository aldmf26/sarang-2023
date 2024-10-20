<?php

namespace App\Http\Controllers;

use App\Exports\GabungExport;
use App\Exports\LaporanDetailPartai;
use App\Models\CetakModel;
use App\Models\LaporanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Laporan_akhir extends Controller
{
    public function index(Request $r)
    {
        $show = $r->show;
        $search = $r->search;
        $now = date('m');

        $bulan = $r->bulan ?? date('m') - 1;
        if ($bulan < 1) {
            $bulan = 12 + $bulan;
        }

        $gaji = collect(DB::select("SELECT 
                    COALESCE(b.lokasi, 'ctk') as lokasi,
                    sum(a.ttl_gaji) as ttl_gaji,
                    sum(a.cbt_gr_akhir) as gr_akhir,
                    sum(a.eo_gr_akhir) as eo_gr_akhir, 
                    sum(a.srt_gr_akhir) as srt_gr_akhir 
                    FROM `tb_gaji_penutup` as a 
                join users as b on a.pgws = b.name	
                WHERE a.bulan_dibayar = $bulan
                GROUP BY b.lokasi;"))->keyBy('lokasi');
        $partai = LaporanModel::LaporanPerPartai($show);
        $data = [
            'title' => 'Laporan Partai',
            'partai' => $partai['paginator'],
            'options' => $partai['options'],
            'total' => $partai['paginator']->total(),
            'bulan' => $bulan,
            'cabutGrAkhir' => $gaji['bjm']->gr_akhir + $gaji['sby']->gr_akhir,
            'gr_eo_akhir' =>   $gaji['bjm']->eo_gr_akhir + $gaji['mtd']->eo_gr_akhir,
            'ctk' => $gaji['ctk']->gr_akhir ?? 0,
            'str' => $gaji['bjm']->srt_gr_akhir ?? 0 + $gaji['ctk']->srt_gr_akhir ?? 0,
            'cu' => DB::selectOne("SELECT sum(a.gr_akhir) as gr_akhir FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where a.bulan_dibayar = '$bulan' and a.selesai ='Y' and b.kategori = 'CU'"),
            'oprasional' => DB::table('oprasional')->where('bulan', $bulan)->first(),
            'bulandata' => DB::table('bulan')->get(),
            'gaji' => DB::selectOne("SELECT sum(a.ttl_gaji) as ttl_gaji
                FROM tb_gaji_penutup as a 
                JOIN users as b on a.pgws = b.name
                where a.bulan_dibayar = '$bulan' and b.posisi_id ;
            "),
        ];
        return view('home.laporan.lapPerpartai', $data);
    }

    public function search(Request $r)
    {
        $search = $r->search;
        $data = [
            'partai' => LaporanModel::LaporanPerPartaiSearch($search),
            'search' => $search,
        ];
        return view('home.laporan.lapPerpartaiSearch', $data);
    }

    public function detail(Request $r)
    {
        $nm_partai = $r->nm_partai;
        $data = [
            'title' => 'Detail Partai',
            'partai' => LaporanModel::LaporanDetailPartai($nm_partai),
            'nm_partai' => $nm_partai
        ];
        return view('home.laporan.detail_partai', $data);
    }

    public function export_partai($nm_partai)
    {
        $query = LaporanModel::LaporanDetailPartai($nm_partai);
        return Excel::download(new LaporanDetailPartai($query), "laporan Detail Partai $nm_partai.xlsx");
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
            'rupiah' => $r->total_rp - $r->gaji,
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
            'rp_oprasional' => $rawNumber - $r->gaji,
            'bulan' => $r->bulan,
            'tahun' => $r->tahun,
            'rp_gr' => ($rawNumber - $r->gaji) / $r->gr_akhir,
            'gr' => $r->gr_akhir
        ];
        DB::table('oprasional')->insert($data);
        return redirect()->back()->with('sukses', 'Data Berhasil ditambahkan');
    }
}
