<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use App\Models\CetakModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\BkTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CetakNewController extends Controller
{
    // formulir cetak
    public function formulir(Request $r)
    {
        $id_user = auth()->user()->id;
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');

        $cetak = DB::select("SELECT a.no_box,sum(a.pcs_akhir) as pcs_akhir,sum(a.gr_akhir) as gr_akhir 
        FROM cetak_new as a 
        WHERE a.selesai = 'Y' AND a.bulan_dibayar = '$bulan' 
        AND YEAR(a.tgl) = '$tahun' AND a.id_pengawas = '$id_user'
        AND a.no_box NOT LIKE '%cu%' 
        AND a.no_box NOT IN (select no_box from formulir_sarang where kategori = 'sortir' and no_box = a.no_box) 
        AND a.no_box NOT IN (
            SELECT no_box
            FROM cetak_new
            WHERE selesai = 'T'
        )
        GROUP BY a.no_box");

        $data = [
            'title' => 'Formulir  Sortir',
            'cetak' => $cetak,
            'users' => $this->getData('users'),
        ];
        return view('home.cetak_new.formulir', $data);
    }



    public function formulir_print($no_invoice)
    {


        $halaman = DB::select("SELECT a.id_pemberi, b.name, a.id_penerima
        FROM formulir_sarang as a 
        left join users as b on b.id = a.id_penerima
        where a.no_invoice = '$no_invoice' and a.kategori = 'sortir'
        group by a.id_penerima
        ");

        $data = [
            'title' => 'Formulir Cetak Print',
            'halaman' => $halaman,
            'no_invoice' => $no_invoice
        ];
        return view('home.cetak_new.formulir_print', $data);
    }

    public function getData($key)
    {
        $id_user = auth()->user()->id;
        $posisi_id = auth()->user()->posisi_id;
        if ($posisi_id == '1') {
            $anak = DB::table('tb_anak')->get();
        } else {
            $anak = DB::table('tb_anak')->where('id_pengawas', $id_user)->get();
        }
        $data = [
            'users' => DB::table('users')->where('posisi_id', '13')->get(),
            'bulan' => DB::table('bulan')->get(),
            'tb_anak' => $anak,
            'paket' => DB::table('kelas_cetak')->get(),
            'nobox' => DB::table('formulir_sarang')
                ->select('no_box')
                ->where([['id_penerima', $id_user], ['kategori', 'cetak']])
                ->whereNotNull('no_box')
                ->whereNotIn('no_box', function ($query) {
                    $query->select('no_box')
                        ->from('cetak_new');
                })
                ->get()

        ];
        return $data[$key];
    }
    public function index(Request $r)
    {

        $id_anak = $r->id_anak ?? 'All';

        $tgl1 = $r->tgl1 ?? date('Y-m-d');
        $tgl2 = $r->tgl2 ?? date('Y-m-d');
        $anak = DB::table('tb_anak')->where('id_anak', $id_anak)->first();

        $data = [
            'title' => 'Cetak',
            'users' => $this->getData('users'),
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'id_anak' => $id_anak,
            'bulan' => $this->getData('bulan'),
            'tb_anak' => $this->getData('tb_anak'),
            'anak' => $id_anak == 'All' ? 'Semua Anak' : $anak->nama
        ];
        return view('home.cetak_new.index', $data);
    }

    public function get_no_box(Request $r)
    {
        $box = $r->box;
        $formulir = DB::table('formulir_sarang')->where('no_box', $box)->first();

        $data = [
            'pcs_awal' => $formulir->pcs_awal,
            'gr_awal' => $formulir->gr_awal,
        ];
        return response()->json($data);
    }



    public function get_cetak(Request $r)
    {
        $tgl1 = $r->tgl1 ?? date('Y-m-d');
        $tgl2 = $r->tgl2 ?? date('Y-m-t');

        $id_pengawas = auth()->user()->id;
        $id_anak = $r->id_anak;


        $cetak = CetakModel::getCetakQuery($id_anak, $tgl1, $tgl2, $id_pengawas);


        $data = [
            'cetak' => $cetak,
            'tgl1' => $tgl1,
            'tb_anak' => $this->getData('tb_anak'),
            'paket' => $this->getData('paket'),
            'bulan' => $this->getData('bulan'),


        ];
        return view('home.cetak_new.getdata', $data);
    }

    public function load_tambah_data(Request $r)
    {
        $data = [
            'tb_anak' => $this->getData('tb_anak'),
            'paket' => $this->getData('paket'),
            'bulan' => $this->getData('bulan'),
            'users' => $this->getData('users'),
            'nobox' => $this->getData('nobox'),
        ];
        return view('home.cetak_new.load_tambah_data', $data);
    }

    public function tambah_baris(Request $r)
    {
        $data = [
            'count' => $r->count,
            'tb_anak' => $this->getData('tb_anak'),
            'bulan' => $this->getData('bulan'),
            'paket' => $this->getData('paket'),
            'users' => $this->getData('users'),
            'nobox' => $this->getData('nobox'),
        ];
        return view('home.cetak_new.tambah_baris', $data);
    }

    public function save_target(Request $r)
    {
        for ($x = 0; $x < count($r->no_box); $x++) {

            $rp_satuan = DB::table('kelas_cetak')->where('id_kelas_cetak', $r->id_paket[$x])->first();
            $data = [
                'id_pemberi' => 0,
                'id_pengawas' => auth()->user()->id,
                'no_box' => $r->no_box[$x],
                'tgl' => $r->tgl[$x],
                'id_anak' => $r->id_anak[$x],
                'pcs_awal' => $r->pcs_awal[$x],
                'gr_awal' => $r->gr_awal[$x],
                'pcs_awal_ctk' => $r->pcs_awal[$x],
                'gr_awal_ctk' => $r->gr_awal[$x],
                'id_kelas_cetak' => $r->id_paket[$x],
                'rp_satuan' => $rp_satuan->rp_pcs,
                'bulan_dibayar' => $r->bulan_dibayar[$x]
            ];
            DB::table('cetak_new')->insert($data);
        }
    }

    public function save_akhir(Request $r)
    {
        // if ($r->id_paket == 16) {

        //     $data = [
        //         'pcs_akhir' => $r->pcs_akhir,
        //         'gr_akhir' => $r->gr_akhir,
        //         'pcs_tdk_cetak' => $r->pcs_tdk_ctk,
        //         'gr_tdk_cetak' => $r->gr_tdk_ctk,
        //         'ttl_rp' => $r->pcs_akhir * $r->rp_satuan,
        //     ];
        //     DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->update($data);

        //     $cetak =  DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->first();

        //     $rp_training = DB::selectOne("SELECT a.tgl,  b.nama, COUNT(a.tgl) as ttl, sum(a.ttl_rp) as ttl_rp, c.rp_gaji
        //     FROM cetak_new as a 
        //     left join tb_anak as b on b.id_anak = a.id_anak
        //     left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas_cetak
        //     where a.pcs_akhir != 0 and c.id_paket = 16 and a.id_anak = '$cetak->id_anak' and a.tgl = '$cetak->tgl'
        //     GROUP by a.id_anak , a.tgl;");

        //     $data = [
        //         'rp_tambahan' => $rp_training->rp_gaji == 0 ? 0 : ($rp_training->rp_gaji - $rp_training->ttl_rp) / $rp_training->ttl,
        //     ];
        //     DB::table('cetak_new')->where('id_anak', $cetak->id_anak)->where('tgl', $cetak->tgl)->where('pcs_akhir', '!=', '0')->update($data);
        // } else {
        $kelas_cetak =  DB::table('kelas_cetak')->where('id_kelas_cetak', $r->id_paket)->first();
        $cetak =  DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->first();

        if (empty($r->tipe_bayar) || $r->pcs_akhir == 0) {
            $ttl_rp = 0;
            $rp_hcr = 0;
            $susut =  0;
            $denda_susut = 0;
            $rp_pcs =  0;
        } else {
            if ($r->tipe_bayar == 1) {
                $ttl_rp = $r->pcs_akhir * $kelas_cetak->rp_pcs;
            } else {
                $ttl_rp = $r->gr_akhir * $kelas_cetak->rp_pcs;
            }
            $rp_hcr = $r->pcs_hcr * $kelas_cetak->denda_hcr;

            $susut =  (1 - (($r->gr_tdk_ctk + $r->gr_akhir) / $cetak->gr_awal_ctk)) * 100;
            if (round($susut, 0) >= $kelas_cetak->batas_susut) {
                $denda_susut = round($susut, 0) * $kelas_cetak->denda_susut;
            } else {
                $denda_susut = 0;
            }
            $rp_pcs =  $kelas_cetak->rp_pcs;
        }

        $data = [
            'id_anak' => $r->id_anak,
            'id_kelas_cetak' => $r->id_paket,
            'pcs_hcr' => $r->pcs_hcr,
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
            'pcs_tdk_cetak' => $r->pcs_tdk_ctk,
            'gr_tdk_cetak' => $r->gr_tdk_ctk,
            'rp_satuan' => $rp_pcs,
            'ttl_rp' => $ttl_rp - $rp_hcr - $denda_susut,
            'tipe_bayar' => $r->tipe_bayar,
            'bulan_dibayar' => $r->bulan_dibayar
        ];
        DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->update($data);
        // }
    }

    public function getRowData(Request $r)
    {
        $data = [
            'c' => DB::selectOne("SELECT a.id_anak, a.capai,a.id_cetak, a.selesai, c.name, d.name as pgws, b.nama as nm_anak , a.no_box, a.tgl, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_awal_ctk as pcs_awal_ctk, a.gr_awal_ctk, a.pcs_akhir, a.gr_akhir, a.rp_satuan, e.kelas, e.batas_susut , e.denda_susut, e.id_paket, a.rp_tambahan , a.id_kelas_cetak, a.pcs_hcr, e.denda_hcr,a.tipe_bayar, a.bulan_dibayar,ttl_rp
            From cetak_new as a  
            LEFT join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pemberi
            left join users as d on d.id = a.id_pengawas
            left join kelas_cetak as e on e.id_kelas_cetak = a.id_kelas_cetak
            where a.id_cetak = '$r->id_cetak'
        "),
            'no' => $r->no,
            'tb_anak' => $this->getData('tb_anak'),
            'bulan' => $this->getData('bulan'),
            'paket' => $this->getData('paket'),
        ];

        return view('home.cetak_new.getRowData', $data);
    }

    public function get_paket_cetak(Request $r)
    {
        $kelas = DB::table('kelas_cetak')->where('kategori_hitung', $r->tipe_bayar)->get();

        echo "<option>Pilih Paket</option>";
        foreach ($kelas as $k) {
            echo "<option value='$k->id_kelas_cetak'>$k->kelas / Rp.$k->rp_pcs</option>";
        }
    }

    public function save_selesai(Request $r)
    {
        DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->update(['selesai' => 'Y']);
    }
    public function cancel_selesai(Request $r)
    {
        DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->update(['selesai' => 'T']);
    }

    public function history(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');

        $history = DB::select("SELECT 
        a.id_anak,
        b.id_kelas as kelas,
        count(DISTINCT a.tgl) as ttl_hari,
         b.nama,
         sum(a.pcs_awal) as pcs_awal,
         sum(a.pcs_akhir) as pcs_akhir,
         sum(a.gr_awal) as gr_awal,
         sum(a.gr_akhir) as gr_akhir,
         sum(a.ttl_rp) as ttl_rp
         FROM `cetak_new` as a
        JOIN tb_anak as b on a.id_anak = b.id_anak
        WHERE a.bulan_dibayar = $bulan AND YEAR(a.tgl) = $tahun
        GROUP BY a.id_anak;");

        $pcs_awal = 0;
        $gr_awal = 0;
        $pcs_akhir = 0;
        $gr_akhir = 0;
        $ttl_rp = 0;
        foreach ($history as $d) {
            $pcs_awal += $d->pcs_awal;
            $gr_awal += $d->gr_awal;
            $pcs_akhir += $d->pcs_akhir;
            $gr_akhir += $d->gr_akhir;
            $ttl_rp += $d->ttl_rp;
        }

        $data = [
            'title' => 'History Cetak',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'history' => $history,
            'pcs_awal' => $pcs_awal,
            'gr_awal' => $gr_awal,
            'pcs_akhir' => $pcs_akhir,
            'gr_akhir' => $gr_akhir,
            'ttl_rp' => $ttl_rp,
        ];
        return view('home.cetak_new.history', $data);
    }


    public function hapus_data(Request $r)
    {
        if ($r->id_paket == 16) {
            $cetak =  DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->first();
            DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->delete();
            $rp_training = DB::selectOne("SELECT a.tgl,  b.nama, COUNT(a.tgl) as ttl, sum(a.ttl_rp) as ttl_rp, c.rp_gaji
            FROM cetak_new as a 
            left join tb_anak as b on b.id_anak = a.id_anak
            left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas_cetak
            where a.pcs_akhir != 0 and c.id_paket = 16 and a.id_anak = '$cetak->id_anak' and a.tgl = '$cetak->tgl'
            GROUP by a.id_anak , a.tgl;");
            $data = [
                'rp_tambahan' => $rp_training->rp_gaji == 0 ? 0 : ($rp_training->rp_gaji - $rp_training->ttl_rp) / $rp_training->ttl,
            ];
            DB::table('cetak_new')->where('id_anak', $cetak->id_anak)->where('tgl', $cetak->tgl)->where('pcs_akhir', '!=', '0')->update($data);
        } else {
            DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->delete();
        }
    }

    public function queryHistoryDetail($id_anak, $bulan, $tahun)
    {
        $detail = DB::select("SELECT 
        a.id_cetak,
        a.selesai,
        c.name,
        d.name as pgws,
        b.nama as nm_anak ,
        b.id_kelas ,
        a.no_box,
        a.tgl,
        a.pcs_awal,
        a.gr_awal,
        a.pcs_tdk_cetak,
        a.gr_tdk_cetak,
        a.pcs_awal_ctk as pcs_awal_ctk,
        a.gr_awal_ctk,
        a.pcs_akhir,
        a.gr_akhir,
        a.rp_satuan,
        a.ttl_rp,
        a.rp_tambahan,
        e.kelas
        From cetak_new as a  
        LEFT join tb_anak as b on b.id_anak = a.id_anak
        left join users as c on c.id = a.id_pemberi
        left join users as d on d.id = a.id_pengawas
        left join kelas_cetak as e on e.id_kelas_cetak = a.id_kelas_cetak
        where a.bulan_dibayar = $bulan AND YEAR(a.tgl) = $tahun AND a.id_anak = $id_anak
        order by a.pcs_akhir ASC , a.id_cetak DESC");


        $cabut = DB::Select("SELECT 
                    a.no_box,
                    a.pcs_awal,
                    a.gr_awal,
                    a.tgl_terima as tgl,
                    a.pcs_akhir,
                    a.gr_akhir,
                    (1 - a.gr_akhir / a.gr_awal) * 100 as susut,
                    CASE WHEN a.selesai = 'Y' THEN a.ttl_rp ELSE 0 END as ttl_rp,
                    b.nama as nm_anak,
                    b.id_kelas as kelas
                    FROM `cabut` as a
                    join tb_anak as b on b.id_anak = a.id_anak
                    WHERE a.penutup = 'T' AND a.id_anak = $id_anak and a.no_box != 9999 
                    AND a.bulan_dibayar = '$bulan' AND a.tahun_dibayar = '$tahun'");

        $sortir = DB::Select("SELECT 
                    a.no_box,
                    a.pcs_awal,
                    a.gr_awal,
                    a.tgl   ,
                    a.pcs_akhir,
                    a.gr_akhir,
                    (1 - a.gr_akhir / a.gr_awal) * 100 as susut,
                    CASE WHEN a.selesai = 'Y' THEN a.ttl_rp ELSE 0 END as ttl_rp,
                    b.nama as nm_anak,
                    b.id_kelas as kelas
                    FROM `sortir` as a
                    join tb_anak as b on b.id_anak = a.id_anak
                    WHERE a.penutup = 'T' AND a.id_anak = $id_anak and a.no_box != 9999 
                    AND a.bulan = '$bulan' AND year(a.tgl_input) = '$tahun'");

        $dll = DB::select("SELECT
                a.rupiah as ttl_rp,
                b.nama as nm_anak,
                a.tgl
                FROM tb_hariandll as a
                join tb_anak as b on b.id_anak = a.id_anak
                WHERE a.ditutup = 'T' AND a.id_anak = $id_anak AND a.bulan_dibayar = '$bulan' AND a.tahun_dibayar = '$tahun'");

        $eo = DB::select("SELECT
                a.no_box,
                a.ttl_rp,
                b.nama as nm_anak,
                a.tgl_ambil as tgl,
                a.gr_eo_awal as gr_awal,
                a.gr_eo_akhir as gr_akhir,
                (1 - a.gr_eo_akhir / a.gr_eo_awal) * 100 as susut
                FROM eo as a
                join tb_anak as b on b.id_anak = a.id_anak
                WHERE a.penutup = 'T' AND a.id_anak = $id_anak AND a.bulan_dibayar = '$bulan' AND a.no_box != 9999 AND year(a.tgl_input) = '$tahun'");

        $denda = DB::select("SELECT a.nominal as denda,a.tgl, b.nama as nm_anak FROM tb_denda as a 
                            join tb_anak as b on b.id_anak = a.id_anak 
                            WHERE a.id_anak = $id_anak AND a.bulan_dibayar = '$bulan' AND YEAR(a.tgl) = '$tahun'");

        $pcs_awal = 0;
        $gr_awal = 0;
        $pcs_akhir = 0;
        $gr_akhir = 0;
        $ttl_rp = 0;

        $dataSum = [$detail, $cabut, $sortir, $eo, $dll];
        foreach ($dataSum as $data) {
            foreach ($data as $d) {
                $pcs_awal += $d->pcs_awal ?? 0;
                $pcs_akhir += $d->pcs_akhir ?? 0;

                $gr_awal += $d->gr_awal ?? 0;
                $gr_akhir += $d->gr_akhir ?? 0;

                $ttl_rp += $d->ttl_rp ?? 0;
            }
        }
        
        foreach ($denda as $d) {
            $ttl_rp -= $d->denda;
        }

        return [
            'eo' => $eo,
            'cabut' => $cabut,
            'sortir' => $sortir,
            'dll' => $dll,
            'denda' => $denda,
            'detail' => $detail,
            'ttlpcs_awal' => $pcs_awal,
            'ttlgr_awal' => $gr_awal,
            'ttlpcs_akhir' => $pcs_akhir,
            'ttlgr_akhir' => $gr_akhir,
            'ttlttl_rp' => $ttl_rp,
        ];
    }
    public function history_detail(Request $r)
    {
        $id_anak = $r->id_anak;
        $bulan = $r->bulan;
        $tahun = $r->tahun;
        $ttl_hari = $r->ttl_hari;
        $getAnak = DB::table('tb_anak')->where('id_anak', $id_anak)->first();


        $query = $this->queryHistoryDetail($id_anak, $bulan, $tahun);
        $data = [
            'id_anak' => $id_anak,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'getAnak' => $getAnak,
            'ttl_hari' => $ttl_hari,
            'eo' => $query['eo'],
            'cabut' => $query['cabut'],
            'sortir' => $query['sortir'],
            'dll' => $query['dll'],
            'denda' => $query['denda'],
            'detail' => $query['detail'],
            'ttlpcs_awal' => $query['ttlpcs_awal'],
            'ttlgr_awal' => $query['ttlgr_awal'],
            'ttlpcs_akhir' => $query['ttlpcs_akhir'],
            'ttlgr_akhir' => $query['ttlgr_akhir'],
            'ttlttl_rp' => $query['ttlttl_rp'],
            'ttl_hari' => $ttl_hari,
        ];


        return view('home.cetak_new.detail_history', $data);
    }

    public function print_slipgaji(Request $r)
    {
        $id_anak = $r->id_anak;
        $bulan = $r->bulan;
        $tahun = $r->tahun;
        $ttl_hari = $r->ttl_hari;

        $query = $this->queryHistoryDetail($id_anak, $bulan, $tahun);
        $data = [
            'id_anak' => $r->id_anak,
            'eo' => $query['eo'],
            'cabut' => $query['cabut'],
            'sortir' => $query['sortir'],
            'dll' => $query['dll'],
            'denda' => $query['denda'],
            'detail' => $query['detail'],
            'ttlpcs_awal' => $query['ttlpcs_awal'],
            'ttlgr_awal' => $query['ttlgr_awal'],
            'ttlpcs_akhir' => $query['ttlpcs_akhir'],
            'ttlgr_akhir' => $query['ttlgr_akhir'],
            'ttlttl_rp' => $query['ttlttl_rp'],
            'ttl_hari' => $ttl_hari,
        ];


        return view('home.cetak_new.print_slipgaji', $data);
    }

    public function capai(Request $r)
    {
        DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->update(['capai' => $r->val]);
        return json_encode([
            'status' => $r->val == 'T' ? 'error' : 'sukses',
            'pesan' => $r->val == 'T' ? 'Tidak Capai' : 'Capai',
        ]);
    }

    public function summary(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $id_pengawas = auth()->user()->id;


        // Cabut::getRekapLaporanHarian($bulan, $tahun, $id_pengawas);
        $summary = DB::select("SELECT d.ttl_hari,
        b.name as pgws,
        a.id_anak,
        c.nama,
        sum(a.ttl_rp) as ttl_rp,
        sum(a.rp_tambahan) as rp_tambahan,
        cabut.ttl_rp as ttl_rp_cabut,
        sortir.ttl_rp as ttl_rp_sortir,
        eo.ttl_rp as ttl_rp_eo,
        dll.ttl_rp as ttl_rp_dll,
        denda.ttl_rp as denda
        FROM `cetak_new` as a
        JOIN users as b on a.id_pengawas = b.id
        JOIN tb_anak as c on a.id_anak = c.id_anak
        JOIN (
            SELECT id_anak,count(DISTINCT tgl) as ttl_hari from absen 
            where bulan_dibayar = $bulan AND tahun_dibayar = $tahun GROUP BY id_anak 
        ) as d on d.id_anak = a.id_anak
        LEFT JOIN (
                  SELECT 
                    id_anak, 
                    SUM(CASE WHEN selesai = 'Y' THEN ttl_rp ELSE 0 END) as ttl_rp
                  FROM `cabut` 
                  WHERE penutup = 'T' AND no_box != 9999 AND bulan_dibayar = '$bulan' AND tahun_dibayar = '$tahun'
                  GROUP BY id_anak 
        ) as cabut on a.id_anak = cabut.id_anak
        LEFT join (
            SELECT 
            id_anak,
            sum(CASE WHEN selesai = 'Y' THEN ttl_rp ELSE 0 END ) as ttl_rp
            FROM `sortir` 
            WHERE bulan = '$bulan' AND YEAR(tgl_input) = '$tahun' AND penutup = 'T' AND no_box != 9999 GROUP BY id_anak
        ) as sortir on a.id_anak = sortir.id_anak
        LEFT join (
            SELECT 
            id_anak,
            sum(CASE WHEN selesai = 'Y' THEN ttl_rp ELSE 0 END ) as ttl_rp
            FROM `eo` 
            WHERE bulan_dibayar = '$bulan' AND YEAR(tgl_input) = '$tahun' AND penutup = 'T' AND no_box != 9999 GROUP BY id_anak
        ) as eo on a.id_anak = eo.id_anak
        LEFT join (
            SELECT 
            id_anak,
            sum(rupiah) as ttl_rp
            FROM `tb_hariandll` 
            WHERE bulan_dibayar = '$bulan' AND tahun_dibayar = '$tahun' AND ditutup = 'T' GROUP BY id_anak
        ) as dll on a.id_anak = dll.id_anak
        LEFT join (
            SELECT 
            id_anak,
            sum(nominal) as ttl_rp
            FROM `tb_denda` 
            WHERE bulan_dibayar = '$bulan' AND YEAR(tgl) = '$tahun' GROUP BY id_anak
        ) as denda on a.id_anak = denda.id_anak
        WHERE a.bulan_dibayar = $bulan AND year(a.tgl) = $tahun and c.id_pengawas = '$id_pengawas'
        GROUP BY a.id_anak;");

        $data = [
            'title' => 'Summary Cetak',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'summary' => $summary,
        ];
        return view('home.cetak_new.summary', $data);
    }

    public function export(Request $r)
    {

        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $id_pengawas = auth()->user()->id;

        $cetak = CetakModel::getCetakQuery('All', $tgl1, $tgl2, $id_pengawas);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $kolom = [
            'A' => 'no box',
            'B' => 'tgl terima',
            'C' => 'nama',
            'D' => 'Paket',
            'E' => 'Pcs Awal',
            'F' => 'Gr Awal',
            'G' => 'Pcs TDK Ctk',
            'H' => 'Gr TDK Ctk',
            'I' => 'Pcs Akhir',
            'J' => 'Gr Akhir',
            'K' => 'SST',
            'L' => 'Denda SST',
            'M' => 'Total RP',
            'N' => 'Capai',
        ];

        foreach ($kolom as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }


        $no = 2;
        foreach ($cetak as $item) {
            $sheet->setCellValue('A' . $no, $item->no_box);
            $sheet->setCellValue('B' . $no, $item->tgl);
            $sheet->setCellValue('C' . $no, $item->nm_anak);
            $sheet->setCellValue('D' . $no, "$item->kelas / Rp. $item->rp_satuan");
            $sheet->setCellValue('E' . $no, $item->pcs_awal_ctk);
            $sheet->setCellValue('F' . $no, $item->gr_awal_ctk);
            $sheet->setCellValue('G' . $no, $item->pcs_tdk_cetak);
            $sheet->setCellValue('H' . $no, $item->gr_tdk_cetak);
            $sheet->setCellValue('I' . $no, $item->pcs_akhir);
            $sheet->setCellValue('J' . $no, $item->gr_akhir);
            $susut = empty($item->gr_akhir)
                ? 0
                : round((1 - ($item->gr_akhir + $item->gr_tdk_cetak) / $item->gr_awal_ctk) * 100, 1);

            $denda_susut = $susut >= $item->batas_susut ? $susut * $item->denda_susut : 0;
            $ttl_rp = $item->pcs_akhir * $item->rp_satuan - $denda_susut;
            $sheet->setCellValue('K' . $no, $susut);
            $sheet->setCellValue('L' . $no, $denda_susut);
            $sheet->setCellValue('M' . $no, $ttl_rp);
            $sheet->setCellValue('N' . $no, $item->capai);
            $no++;
        }
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:N' . $no - 1)->applyFromArray($styleBaris);
        $writer = new Xlsx($spreadsheet);

        // Menggunakan response untuk mengirimkan file ke browser
        $fileName = "Kerja Cetak " . $tgl1 . " - " . $tgl2;
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );
    }

    public function gudangcetak(Request $r)
    {
        $id_pengawas = auth()->user()->id;
        $data = [
            'title' => 'Gudang Cetak',
            'cabut_selesai' => CetakModel::cabut_selesai($id_pengawas),

            'cetak_proses' => CetakModel::cetak_proses($id_pengawas),

            'cetak_selesai' => CetakModel::cetak_selesai($id_pengawas),

            'users' => DB::table('users')->where('posisi_id', '!=', '1')->get(),
        ];
        return view('home.cetak_new.gudangcetak', $data);
    }


    public function load_edit_invoice(Request $r)
    {
        $id_user = auth()->user()->id;
        $no_invoice = $r->no_invoice;
        $kategori = $r->kategori;

        $cabutSelesai = CetakModel::cetak_selesai($id_user);
        $formulir = DB::table('formulir_sarang')->where([['kategori', 'sortir'], ['no_invoice', $no_invoice]])->get();
        $data = [
            'title' => 'Gudang Sarang',
            'cabutSelesai' => $cabutSelesai,
            'formulir' => $formulir,
            'users' => DB::table('users')->where('posisi_id', '!=', '1')->get(),
        ];
        return view('home.cetak_new.load_edit_invoice', $data);
    }
    public function export_gudang(Request $r)
    {
        $id_pengawas = auth()->user()->id;

        $cabut_selesai = CetakModel::cabut_selesai(0);
        $cetak_proses = CetakModel::cetak_proses(0);
        $cetak_selesai = CetakModel::cetak_selesai(0);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $style_atas = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
        );
        $style_atas_number = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
        );

        $kolom = [
            'A' => 'Cetak Stok',
            'B' => 'Pemilik',
            'C' => 'Pengawas',
            'D' => 'no box',
            'E' => 'pcs',
            'F' => 'gr',

            'H' => 'Cetak Sedang Proses',
            'I' => 'Pemilik',
            'J' => 'Pengawas',
            'K' => 'No Box',
            'L' => 'Pcs',
            'M' => 'Gr',

            'O' => 'Cetak Selesai Siap Sortir',
            'P' => 'Pemilik',
            'Q' => 'Pengawas',
            'R' => 'no box',
            'S' => 'pcs',
            'T' => 'gr',
        ];

        foreach ($kolom as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }
        $no = 2;
        $ttl_pcs = 0;
        $ttl_gr = 0;
        foreach ($cabut_selesai as $item) {
            $sheet->setCellValue('B' . $no, $item->name);
            $sheet->setCellValue('C' . $no, $item->no_box);
            $sheet->setCellValue('D' . $no, $item->pcs_awal);
            $sheet->setCellValue('E' . $no, $item->gr_awal);

            $no++;
            $ttl_pcs += $item->pcs_awal;
            $ttl_gr += $item->gr_awal;
        }
        $sheet->setCellValue('B' . $no, 'Total');
        $sheet->setCellValue('C' . $no, '');
        $sheet->setCellValue('D' . $no, $ttl_pcs);
        $sheet->setCellValue('E' . $no, $ttl_gr);


        $sheet->getStyle('B1:C1')->applyFromArray($style_atas);
        $sheet->getStyle('D1:E1')->applyFromArray($style_atas_number);
        $sheet->getStyle('B2:E' . $no - 1)->applyFromArray($styleBaris);
        $sheet->getStyle('B' . $no . ':E' . $no)->applyFromArray($style_atas);

        $no2 = 2;
        $ttl_pcs2 = 0;
        $ttl_gr2 = 0;
        foreach ($cetak_proses as $item) {
            $sheet->setCellValue('H' . $no2, $item->name);
            $sheet->setCellValue('I' . $no2, $item->no_box);
            $sheet->setCellValue('J' . $no2, $item->pcs_awal);
            $sheet->setCellValue('K' . $no2, $item->gr_awal);

            $no2++;
            $ttl_pcs2 += $item->pcs_awal;
            $ttl_gr2 += $item->gr_awal;
        }

        $sheet->setCellValue('H' . $no2, 'Total');
        $sheet->setCellValue('I' . $no2, '');
        $sheet->setCellValue('J' . $no2, $ttl_pcs2);
        $sheet->setCellValue('K' . $no2, $ttl_gr2);

        $sheet->getStyle('H1:I1')->applyFromArray($style_atas);
        $sheet->getStyle('J1:K1')->applyFromArray($style_atas_number);
        $sheet->getStyle('H2:K' . $no2 - 1)->applyFromArray($styleBaris);
        $sheet->getStyle('H' . $no2 . ':K' . $no2)->applyFromArray($style_atas);

        $no3 = 2;
        $ttl_pcs3 = 0;
        $ttl_gr3 = 0;
        foreach ($cetak_selesai as $item) {
            $sheet->setCellValue('N' . $no3, $item->name);
            $sheet->setCellValue('O' . $no3, $item->no_box);
            $sheet->setCellValue('P' . $no3, $item->pcs_awal);
            $sheet->setCellValue('Q' . $no3, $item->gr_awal);

            $no3++;
            $ttl_pcs3 += $item->pcs_awal;
            $ttl_gr3 += $item->gr_awal;
        }

        $sheet->setCellValue('N' . $no3, 'Total');
        $sheet->setCellValue('O' . $no3, '');
        $sheet->setCellValue('P' . $no3, $ttl_pcs3);
        $sheet->setCellValue('Q' . $no3, $ttl_gr3);

        $sheet->getStyle('N1:O1')->applyFromArray($style_atas);
        $sheet->getStyle('P1:Q1')->applyFromArray($style_atas_number);
        $sheet->getStyle('N2:Q' . $no3 - 1)->applyFromArray($styleBaris);
        $sheet->getStyle('N' . $no3 . ':Q' . $no3)->applyFromArray($style_atas);



        $writer = new Xlsx($spreadsheet);

        // Menggunakan response untuk mengirimkan file ke browser
        $fileName = "Gudang Cetak";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );
    }

    public function selesai_po_sortir(Request $r)
    {
        $formulir =  DB::table('formulir_sarang')->where('no_invoice', $r->no_invoice)->where('kategori', 'sortir')->get();

        foreach ($formulir as $f) {
            $data = [
                'no_box' => $f->no_box,
                'pcs_awal' => $f->pcs_awal,
                'gr_awal' => $f->gr_awal,
                'kategori' => 'sortir',
                'tgl' => $f->tanggal,
                'penerima' => $f->id_penerima,
            ];
            DB::table('bk')->insert($data);
        }
        return redirect()->route('gudangsarang.invoice_sortir', ['kategori' => 'sortir'])->with('sukses', 'Data Berhasil');
    }


    public function update_invoice(Request $r)
    {
        $no_invoice = $r->no_invoice;
        if (!$r->no_box[0]) {
            return redirect()->route('gudangsarang.invoice_sarang', ['kategori' => 'sortir'])->with('error', 'No Box / Penerima Kosong !');
        }
        DB::table('formulir_sarang')->where('no_invoice', $no_invoice)->where('kategori', 'sortir')->delete();
        $no_box = explode(',', $r->no_box[0]);
        foreach ($no_box as $d) {
            $ambil = DB::selectOne("SELECT 
                        cetak_new.id_pengawas,
                        sum(pcs_akhir) as pcs_akhir, sum(gr_akhir) as gr_akhir , formulir_sarang.id_pemberi
                        FROM cetak_new 
                        left join formulir_sarang on formulir_sarang.no_box = cetak_new.no_box and formulir_sarang.kategori = 'cetak'
                        WHERE cetak_new.no_box = $d AND cetak_new.selesai = 'Y' GROUP BY cetak_new.no_box ");

            $pcs = $ambil->pcs_akhir;
            $gr = $ambil->gr_akhir;
            $id_penerima = $r->id_penerima[$d];
            $data[] = [
                'no_invoice' => $no_invoice,
                'no_box' => $d,
                'id_pemberi' => $ambil->id_pengawas,
                'id_penerima' => $id_penerima,
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'tanggal' => $r->tgl,
                'kategori' => 'sortir',
            ];
        }

        DB::table('formulir_sarang')->insert($data);
        return redirect()->route('gudangsarang.invoice_sortir', ['kategori' => 'sortir'])->with('sukses', 'Data Berhasil');
    }

    public function save_formulir(Request $r)
    {
        $no_box = explode(',', $r->no_box[0]);
        foreach ($no_box as $d) {
            $ambil = DB::selectOne("SELECT 
                        sum(pcs_akhir) as pcs_akhir, sum(gr_akhir) as gr_akhir , formulir_sarang.id_pemberi
                        FROM cetak_new 
                        left join formulir_sarang on formulir_sarang.no_box = cetak_new.no_box and formulir_sarang.kategori = 'cetak'
                        WHERE cetak_new.no_box = $d AND cetak_new.selesai = 'Y' GROUP BY cetak_new.no_box ");
            $pcs = $ambil->pcs_akhir;
            $gr = $ambil->gr_akhir;
            $id_penerima = $r->id_penerima[$d];

            $urutan_invoice = DB::selectOne("SELECT max(a.no_invoice) as no_invoice FROM formulir_sarang as a where a.kategori = 'sortir'");

            if (empty($urutan_invoice->no_invoice)) {
                $inv = 1001;
            } else {
                $inv = $urutan_invoice->no_invoice + 1;
            }

            $data[] = [
                'no_invoice' => $inv,
                'no_box' => $d,
                'id_pemberi' => auth()->user()->id,
                'id_penerima' => $id_penerima,
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'tanggal' => $r->tgl,
                'kategori' => 'sortir',
            ];
        }

        DB::table('formulir_sarang')->insert($data);
        return redirect()->route('cetaknew.gudangcetak')->with('sukses', 'Data Berhasil');
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF00', // Kode warna untuk kuning
                ],
            ],
        ];
        $styleBarisKosong = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],

        ];
        $style_atas = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
        );


        $kolom = [
            'A' => 'Nama Partai',
            'B' => 'Tipe',
            'C' => 'Ket',
            'D' => 'Warna',
            'E' => 'Pcs Awal',
            'F' => 'Gr Awal',
        ];
        foreach ($kolom as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }

        $kolom = [
            'A' => 'Bjm 1001 ',
            'B' => 'd',
            'C' => 'k',
            'D' => 'vs',
            'E' => '40',
            'F' => '250',
        ];
        foreach ($kolom as $k => $v) {
            $sheet->setCellValue($k . '2', $v);
        }

        $sheet->getStyle('A1:F1')->applyFromArray($style_atas);
        $sheet->getStyle('A2:F2')->applyFromArray($styleBaris);
        $sheet->getStyle('A3:F10')->applyFromArray($styleBarisKosong);

        $writer = new Xlsx($spreadsheet);

        // Menggunakan response untuk mengirimkan file ke browser
        $fileName = "Template Cetak BK";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );
    }
    public function getNoBoxTambah()
    {
        $cekBox = DB::selectOne("SELECT no_box FROM `bk` WHERE kategori like '%cabut%' ORDER by no_box DESC limit 1;");
        $nobox = isset($cekBox->no_box) ? $cekBox->no_box + 1 : 1001;
        return $nobox;
    }

    public function import(Request $r)
    {
        if ($r->kategori == 'sortir') {
            $this->importSortir($r);
            return redirect()->route('bk.index', ['kategori' => 'sortir'])->with('sukses', 'Data berhasil import');
        } else {

            $file = $r->file('file');
            $spreadsheet = IOFactory::load($file);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            DB::beginTransaction();
            try {
                foreach (array_slice($sheetData, 1) as $row) {
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $nobox = $this->getNoBoxTambah();


                    // $cekBox = DB::table('bk')->where([['kategori', 'LIKE', '%cabut%'], ['no_box', $nobox]])->first();
                    if (
                        // $cekBox || 
                        empty($row[0]) ||
                        empty($row[5])
                        // empty($row[9]) ||
                        // empty($row[10])
                    ) {
                        $pesan = [
                            // empty($row[0]) => "NO LOT TIDAK BOLEH KOSONG",
                            empty($row[0]) => "NAMA PARTAI TIDAK BOLEH KOSONG",
                            // empty($row[6]) => "PENGAWAS TIDAK BOLEH KOSONG",
                            empty($row[5]) => "GR TIDAK BOLEH KOSONG",

                            // $cekBox ? "NO BOX : $nobox SUDAH ADA" : false,
                        ];
                        DB::rollBack();
                        return redirect()->route('bk.index')->with('error', "ERROR! " . $pesan[true]);
                    } else {
                        DB::table('bk')->insert([
                            'no_lot' => '0',
                            'nm_partai' => $row[0],
                            'no_box' => $nobox,
                            'tipe' => $row[1],
                            'ket' => $row[2],
                            'warna' => $row[3],
                            'tgl' => date('Y-m-d'),
                            'pengawas' => 'sinta',
                            'penerima' => auth()->user()->id,
                            'pcs_awal' => $row[4],
                            'gr_awal' => $row[5],
                            'kategori' => 'cetak',
                        ]);
                        DB::table('formulir_sarang')->insert([
                            'no_box' => $nobox,
                            'id_pemberi' => 265,
                            'id_penerima' => auth()->user()->id,
                            'pcs_awal' => $row[4],
                            'gr_awal' => $row[5],
                        ]);
                        DB::table('cetak_new')->insert([
                            'no_box' => $nobox,
                            'id_pengawas' => auth()->user()->id,
                            'tgl' => date('Y-m-d'),
                            'pcs_awal_ctk' => $row[4],
                            'gr_awal_ctk' => $row[5],
                        ]);
                    }
                }
                DB::commit();
                return redirect()->back()->with('sukses', 'Data berhasil import');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
    }
}
