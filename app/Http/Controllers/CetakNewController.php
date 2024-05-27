<?php

namespace App\Http\Controllers;

use App\Models\Cabut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function save_formulir(Request $r)
    {
        $no_invoice = str()->random(5);
        $no_box = explode(',', $r->no_box[0]);
        foreach ($no_box as $d) {
            $ambil = DB::selectOne("SELECT 
                        sum(pcs_akhir) as pcs_akhir, sum(gr_akhir) as gr_akhir 
                        FROM cetak_new 
                        WHERE no_box = $d AND selesai = 'Y' GROUP BY no_box ");

            $pcs = $ambil->pcs_akhir;
            $gr = $ambil->gr_akhir;

            $data[] = [
                'no_invoice' => $no_invoice,
                'no_box' => $d,
                'id_pemberi' => auth()->user()->id,
                'id_penerima' => $r->id_penerima,
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'tanggal' => $r->tgl,
                'kategori' => 'sortir',
            ];
        }

        DB::table('formulir_sarang')->insert($data);
        return redirect()->route('cetaknew.formulir_print', $no_invoice)->with('sukses', 'Data Berhasil');
    }

    public function formulir_print($no_invoice)
    {
        $detail = DB::table('formulir_sarang')->where('no_invoice', $no_invoice)->get();

        $data = [
            'title' => 'Formulir Cetak Print',
            'detail' => $detail
        ];
        return view('home.cetak_new.formulir_print', $data);
    }

    public function getData($key)
    {
        $id_user = auth()->user()->id;
        $data = [
            'users' => DB::table('users')->where('posisi_id', '13')->get(),
            'bulan' => DB::table('bulan')->get(),
            'tb_anak' => DB::table('tb_anak')->where('id_pengawas', $id_user)->get(),
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



    public function getCetakQuery($id_anak = 'All', $tgl1, $tgl2, $id_pengawas)
    {
        if ($id_anak == 'All') {
            $cetak = DB::select("SELECT a.id_anak, a.capai,a.id_cetak, a.selesai, c.name, d.name as pgws, b.nama as nm_anak , a.no_box, a.tgl, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_awal_ctk as pcs_awal_ctk, a.gr_awal_ctk, a.pcs_akhir, a.gr_akhir, a.rp_satuan, e.kelas, e.batas_susut , e.denda_susut, e.id_paket, a.rp_tambahan , a.id_kelas_cetak, a.pcs_hcr, e.denda_hcr,a.tipe_bayar, a.bulan_dibayar
            From cetak_new as a  
            LEFT join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pemberi
            left join users as d on d.id = a.id_pengawas
            left join kelas_cetak as e on e.id_kelas_cetak = a.id_kelas_cetak
            where a.tgl between '$tgl1' and '$tgl2' and a.id_pengawas = '$id_pengawas'
            order by a.tgl DESC, b.nama ASC
            ;");
        } else {
            $cetak = DB::select("SELECT a.id_anak, a.capai,a.id_cetak, a.selesai, c.name, d.name as pgws, b.nama as nm_anak , a.no_box, a.tgl, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_awal_ctk as pcs_awal_ctk, a.gr_awal_ctk, a.pcs_akhir, a.gr_akhir, a.rp_satuan, e.kelas, e.batas_susut , e.denda_susut, e.id_paket, a.rp_tambahan , a.id_kelas_cetak , a.pcs_hcr, e.denda_hcr,a.tipe_bayar,a.bulan_dibayar
            From cetak_new as a  
            LEFT join tb_anak as b on b.id_anak = a.id_anak
            left join users as c on c.id = a.id_pemberi
            left join users as d on d.id = a.id_pengawas
            left join kelas_cetak as e on e.id_kelas_cetak = a.id_kelas_cetak
            where a.tgl between '$tgl1' and '$tgl2' and a.id_anak = '$id_anak' and a.id_pengawas = '$id_pengawas'
            order by a.tgl DESC, b.nama ASC
            ;");
        }
        return $cetak;
    }

    public function get_cetak(Request $r)
    {
        $tgl1 = $r->tgl1 ?? date('Y-m-d');
        $tgl2 = $r->tgl2 ?? date('Y-m-t');

        $id_pengawas = auth()->user()->id;
        $id_anak = $r->id_anak;

        $cetak = $this->getCetakQuery($id_anak, $tgl1, $tgl2, $id_pengawas);
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

        if ($r->tipe_bayar == 1) {
            $ttl_rp = $r->pcs_akhir * $kelas_cetak->rp_pcs;
        } else {
            $ttl_rp = $r->gr_akhir * $kelas_cetak->rp_pcs;
        }
        $data = [
            'id_anak' => $r->id_anak,
            'id_kelas_cetak' => $r->id_paket,
            'pcs_hcr' => $r->pcs_hcr,
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
            'pcs_tdk_cetak' => $r->pcs_tdk_ctk,
            'gr_tdk_cetak' => $r->gr_tdk_ctk,
            'rp_satuan' => $kelas_cetak->rp_pcs,
            'ttl_rp' => $ttl_rp,
            'tipe_bayar' => $r->tipe_bayar,
            'bulan_dibayar' => $r->bulan_dibayar
        ];
        DB::table('cetak_new')->where('id_cetak', $r->id_cetak)->update($data);
        // }
    }

    public function getRowData(Request $r)
    {
        $data = [
            'c' => DB::selectOne("SELECT a.id_anak, a.capai,a.id_cetak, a.selesai, c.name, d.name as pgws, b.nama as nm_anak , a.no_box, a.tgl, a.pcs_awal, a.gr_awal, a.pcs_tdk_cetak, a.gr_tdk_cetak, a.pcs_awal_ctk as pcs_awal_ctk, a.gr_awal_ctk, a.pcs_akhir, a.gr_akhir, a.rp_satuan, e.kelas, e.batas_susut , e.denda_susut, e.id_paket, a.rp_tambahan , a.id_kelas_cetak, a.pcs_hcr, e.denda_hcr,a.tipe_bayar, a.bulan_dibayar
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
    public function history_detail(Request $r)
    {
        $id_anak = $r->id_anak;
        $bulan = $r->bulan;
        $tahun = $r->tahun;

        $detail = DB::select("SELECT 
        a.id_cetak,
        a.selesai,
        c.name,
        d.name as pgws,
        b.nama as nm_anak ,
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
        foreach ($detail as $d) {
            $pcs_awal += $d->pcs_awal;
            $gr_awal += $d->gr_awal;
            $pcs_akhir += $d->pcs_akhir;
            $gr_akhir += $d->gr_akhir;
            $ttl_rp += $d->ttl_rp + $d->rp_tambahan;
        }
        foreach ($cabut as $d) {
            $pcs_awal += $d->pcs_awal;
            $gr_awal += $d->gr_awal;
            $pcs_akhir += $d->pcs_akhir;
            $gr_akhir += $d->gr_akhir;
            $ttl_rp += $d->ttl_rp;
        }

        foreach ($sortir as $d) {
            $pcs_awal += $d->pcs_awal;
            $gr_awal += $d->gr_awal;
            $pcs_akhir += $d->pcs_akhir;
            $gr_akhir += $d->gr_akhir;
            $ttl_rp += $d->ttl_rp;
        }

        foreach ($eo as $d) {
            $gr_awal += $d->gr_awal;
            $gr_akhir += $d->gr_akhir;
            $ttl_rp += $d->ttl_rp;
        }

        foreach ($dll as $d) {
            $ttl_rp += $d->ttl_rp;
        }
        foreach ($denda as $d) {
            $ttl_rp -= $d->denda;
        }

        $data = [
            'id_anak' => $r->id_anak,
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
        return view('home.cetak_new.detail_history', $data);
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

        $cetak = $this->getCetakQuery('All', $tgl1, $tgl2, $id_pengawas);
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
            'cabut_selesai' => DB::select("SELECT a.no_box, a.pcs_awal, a.gr_awal
            FROM formulir_sarang as a 
            WHERE a.id_penerima = '$id_pengawas' and a.no_box not in(SELECT b.no_box FROM cetak_new as b);")
        ];
        return view('home.cetak_new.gudangcetak', $data);
    }
}
