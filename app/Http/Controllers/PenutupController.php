<?php

namespace App\Http\Controllers;

use App\Imports\PenutupImport;
use App\Models\Cabut;
use App\Models\CetakModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PenutupController extends Controller
{


    public function getData($param)
    {
        $bulan = 9;
        $tahun = date('Y');
        $pengawas = DB::select("SELECT b.id as id_pengawas,b.name FROM bk as a
                JOIN users as b on a.penerima = b.id
                WHERE  b.name not in ('yuli',  'siti fatimah')
                group by b.id");

        $datas =  [
            'pengawas' => $pengawas,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];

        return $datas[$param];
    }

    public function index(Request $r)
    {
        $bulan = $this->getData('bulan');
        $tahun =  $this->getData('tahun');


        $gaji = DB::select("SELECT SUM(ttl_gaji) as ttl_gaji, bulan_dibayar, tahun_dibayar
                    FROM tb_gaji_penutup 
                    GROUP BY bulan_dibayar,tahun_dibayar");

        $cekTutup = DB::table('tb_gaji_penutup')->where([['bulan_dibayar', $bulan], ['tahun_dibayar', $tahun]])->exists();
        $pengawas = $this->getData('pengawas');

        $datas = [];
        foreach ($pengawas as $p) {

            $ttlRp = 0;
            $tbl = Cabut::getRekapGlobal($bulan, $tahun, $p->id_pengawas);
            foreach ($tbl as $data) {

                $ttl =
                    $data->ttl_rp +
                    $data->eo_ttl_rp +
                    $data->sortir_ttl_rp +
                    $data->ttl_rp_cetak +
                    $data->ttl_rp_dll -
                    $data->ttl_rp_denda;
                $ttlRp += $ttl;
            }
            $datas[$p->name] = $ttlRp;
        }


        $data = [
            'title' => 'Data Gaji Penutup',
            'gaji' => $gaji,
            'cekTutup' => $cekTutup,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'datas' => $datas
        ];
        return view('data_master.penutup.index', $data);
    }

    public function tutup_gaji()
    {
        $bulan = $this->getData('bulan');
        $tahun =  $this->getData('tahun');
        $pengawas = $this->getData('pengawas');



        foreach ($pengawas as $p) {
            $tbl = Cabut::getRekapGlobal($bulan, $tahun, $p->id_pengawas);
            foreach ($tbl as $data) {
                $susutCbt = empty($data->gr_akhir) ? 0 : (1 - (($data->gr_akhir + $data->gr_flx) / $data->gr_awal)) * 100;
                $susutEo =  empty($data->eo_akhir) ? 0 : (1 - ($data->eo_akhir / $data->eo_awal)) * 100;
                $susutSortir = empty($data->sortir_gr_akhir) ? 0 : (1 - ($data->sortir_gr_akhir / $data->sortir_gr_awal)) * 100;
                $ttl = $data->ttl_rp + $data->eo_ttl_rp + $data->sortir_ttl_rp + $data->ttl_rp_dll + $data->ttl_rp_cetak - $data->ttl_rp_denda;
                $rata = empty($data->hariMasuk) ? 0 : $ttl / $data->hariMasuk;

                $tes[] = [
                    'pgws' => $data->pgws,
                    'hari_masuk' => $data->hariMasuk,
                    'nama' => $data->nm_anak,
                    'kelas' => $data->kelas,
                    'cbt_pcs_awal' => $data->pcs_awal,
                    'cbt_gr_awal' => $data->gr_awal,
                    'cbt_pcs_akhir' => $data->pcs_akhir,
                    'cbt_gr_akhir' => $data->gr_akhir,
                    'cbt_eot' => $data->eot,
                    'cbt_flx' => $data->gr_flx,
                    'cbt_sst' => $susutCbt,
                    'cbt_ttlrp' => $data->ttl_rp,

                    'ctk_pcs_awal' => $data->pcs_awal_ctk,
                    'ctk_gr_awal' => $data->gr_awal_ctk,
                    'ctk_pcs_akhir' => $data->pcs_akhir_ctk,
                    'ctk_gr_akhir' => $data->gr_akhir_ctk,
                    'ctk_ttl_rp' => $data->ttl_rp_cetak,

                    'eo_gr_awal' => $data->eo_awal,
                    'eo_gr_akhir' => $data->eo_akhir,
                    'eo_sst' => $susutEo,
                    'eo_ttlrp' => $data->eo_ttl_rp,
                    'srt_pcs_awal' => $data->sortir_pcs_awal,
                    'srt_gr_awal' => $data->sortir_gr_awal,
                    'srt_pcs_akhir' => $data->sortir_pcs_akhir,
                    'srt_gr_akhir' => $data->sortir_gr_akhir,
                    'srt_sst' => $susutSortir,
                    'srt_ttlrp' => $data->sortir_ttl_rp,
                    'dll' => $data->ttl_rp_dll,
                    'denda' => $data->ttl_rp_denda,
                    'ttl_gaji' => $ttl,
                    'ratarata' => $rata,
                    'tgl_input' => now(),
                    'paid' => 0,
                    'admin' => auth()->user()->name,
                    'bulan_dibayar' => $bulan,
                    'tahun_dibayar' => $tahun
                ];
            }
        }

        DB::table('tb_gaji_penutup')->insert($tes);
        return redirect()->route('penutup.index')->with('sukses', 'Data Gaji Penutup Berhasil');
    }

    public function show($bulan, $tahun)
    {
        $pengawas = $this->getData('pengawas');
        $gaji = DB::table('tb_gaji_penutup')->where([['bulan_dibayar', $bulan], ['tahun_dibayar', $tahun]])->get();

        return view('data_master.penutup.show', [
            'title' => "Data Gaji Penutup " . formatTglGaji($bulan, $tahun),
            'gaji' => $gaji,
            'bulan' => $bulan,
            'pengawas' => $pengawas,
        ]);
    }

    public function import(Request $r)
    {
        $r->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        Excel::import(new PenutupImport, $r->file('file'));
        return redirect()->route('penutup.index')->with('sukses', 'Data berhasil import');
    }
}
