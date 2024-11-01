<?php

namespace App\Http\Controllers;

use App\Exports\CabutExport;
use App\Exports\CabutGlobalExport;
use App\Exports\CabutRekapExport;
use App\Models\Cabut;
use App\Models\Eo;
use App\Models\Sortir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CabutController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $data = [
            'title' => 'Divisi Cabut',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
        ];
        return view('home.cabut.index', $data);
    }
    public function updateAnakBelum()
    {
        $anakBelum = count(DB::table('cabut')->where([['no_box', 9999], ['id_pengawas', auth()->user()->id], ['tgl_terima', date('Y-m-d')]])->get());
        return response()->json(['anakBelum' => $anakBelum]);
    }
    public function load_halaman(Request $r)
    {
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $id = auth()->user()->id;

        $cabut = Cabut::getCabut();

        $data = [
            'title' => 'Divisi Cabut',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cabut' => $cabut,
        ];
        return view('home.cabut.load_halaman', $data);
    }
    public function load_tambah_anak(Request $r)
    {
        $data = [
            'anak' => Cabut::getAnak()
        ];
        return view('home.cabut.load_tambah_anak', $data);
    }
    public function createTambahAnakCabut(Request $r)
    {
        $tgl = date('Y-m-d');
        $id_pengawas = auth()->user()->id;
        foreach ($r->all()['rows'] as $d) {
            if ($r->tipe == 'cbt') {
                $id = DB::table('cabut')->insertGetId([
                    'no_box' => 9999,
                    'id_pengawas' => $id_pengawas,
                    'id_anak' => $d,
                    'tgl_terima' => $tgl
                ]);
            }
        }
        return 'Berhasil tambah anak';
    }
    public function hapusCabutRow(Request $r)
    {
        DB::table('cabut')->where('id_cabut', $r->id_cabut)->delete();
        DB::table('absen')->where([['id_kerja', $r->id_cabut], ['ket', 'Cabut']])->delete();
        return 'Berhasil hapus baris';
    }
    public function load_tambah_cabut(Request $r)
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => Cabut::getStokBk(),
            'getAnak' => Cabut::getAnakTambah()
        ];
        return view('home.cabut.load_tambah_cabut', $data);
    }

    public function get_kelas_jenis(Request $r)
    {
        switch ($r->jenis) {
            case '3':
                $kolom = 'a.id_kategori';
                $jenis = 2;
                break;
            case '4':
                $kolom = 'a.id_kategori';
                $jenis = 3;
                break;

            default:
                $kolom = 'a.jenis';
                $jenis = $r->jenis;
                break;
        }
        $lokasi = auth()->user()->lokasi;
        $get = DB::table('tb_kelas as a')->join('paket_cabut as b', 'a.id_paket', 'b.id_paket')->where([[$kolom, $jenis], ['id_kategori', '!=', 3], ['lokasi', $lokasi]])->where('a.nonaktif', 'T')->get();
        echo "
                <option value=''>Pilih</option>
            ";
        foreach ($get as $d) {
            $jenis = $d->jenis == 1 ? "$d->pcs pcs" : "$d->gr gr";
            echo "
                <option value='" . $d->id_kelas . "'>$d->kelas $d->tipe ~ $jenis </option>
            ";
        }
    }

    public function cancel(Request $r)
    {
        DB::table('cabut')->where('id_cabut', $r->id_cabut)->update([
            'no_box' => 9999,
            'tgl_terima' => date('Y-m-d'),
        ]);
    }

    public function load_modal_akhir(Request $r)
    {

        $detail = DB::table('cabut as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['a.id_anak', $r->id_anak], ['a.no_box', $r->no_box]])
            ->first();

        $datas = Cabut::getCabutAkhir($r->orderBy);

        $data = [
            'detail' => $detail,
            'datas' => $datas,
            'orderBy' => $r->orderBy
        ];
        return view('home.cabut.load_modal_akhir', $data);
    }
    public function load_modal_anak_sisa(Request $r)
    {
        $datas = DB::table('absen as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where('a.ket', 'cabut sisa')
            ->get();
        $data = [
            'tittle' => 'tes',
            'datas' => $datas
        ];
        return view('home.cabut.load_modal_anak_sisa', $data);
    }

    public function hapusAnakSisa(Request $r)
    {
        DB::table('absen')->where('id_absen', $r->id_absen)->delete();
    }

    public function load_anak()
    {
        $anak = Cabut::getAnak();
        echo "
        <div class='row'>
                    <div class='col-lg-12'>
                        <table class='table table-striped'>
                            <tr>
                                <th width='180'>Nama</th>
                                <th width='80'>Kelas</th>
                                <th>Tgl Masuk</th>
                                <th>Aksi</th>
                            </tr>";
        foreach ($anak as $d) {
            echo "
                                <tr>
                                    <td>" . ucwords($d->nama) . "</td>
                                    <input type='hidden' value='" . $d->id_anak . "' name='id_anak[]' class='form-control'>
                                    <td><input type='text' value='" . $d->kelas . "' name='id_kelas[]' class='form-control'></td>
                                    <td><input type='date' value='" . $d->tgl_masuk . "' class='form-control' name='tgl_masuk[]'></td>
                                    <td><button type='button' class='btn btn-sm btn-danger' id_anak='" . $d->id_anak . "' id='delete_anak'><i class='fas fa-window-close'></i></button></td>
                                </tr>
                                ";
        }
        echo "
                        </table>
                    </div>
                </div>
        ";
    }

    public function load_anak_nopengawas()
    {
        $anakNoPengawas = Cabut::getAnak(1);

        echo "
        <select class='select3-load anakNoPengawas' name='' multiple id=''>
        ";
        foreach ($anakNoPengawas as $d) {
            echo "<option value='" . $d->id_anak . "'>" . ucwords($d->nama) . "</option>";
        }
        echo "
                            </select>
        ";
    }

    public function add_delete_anak(Request $r)
    {
        $idArray = explode(",", $r->id_anak);
        foreach ($idArray as $n) {
            DB::table('tb_anak')->where('id_anak', $n)->update(
                ['id_pengawas' => empty($r->delete) ? auth()->user()->id : null]
            );
        }
    }

    public function create_anak(Request $r)
    {

        for ($i = 0; $i < count($r->id_anak); $i++) {
            DB::table('tb_anak')->where('id_anak', $r->id_anak[$i])->update(
                [
                    'id_kelas' => $r->id_kelas[$i],
                    'tgl_masuk' => $r->tgl_masuk[$i],
                ]
            );
        }
        return redirect()->route('cabut.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => Cabut::getStokBk(),
            'anak' => Cabut::getAnak(),
            'count' => $r->count,

        ];
        return view('home.cabut.tbh_baris', $data);
    }

    public function get_box_sinta(Request $r)
    {
        $bk = Cabut::getStokBk($r->no_box);

        $data = [
            'pcs_awal' => $bk->pcs_awal,
            'gr_awal' => $bk->gr_awal,
            'pcs_cabut' => $bk->pcs_cabut,
            'gr_cabut' => $bk->gr_cabut,
        ];
        return json_encode($data);
    }

    public function get_kelas_anak(Request $r)
    {
        $bk = DB::table('tb_kelas')->where('id_kelas', $r->id_kelas)->first();
        $data = [
            'gr' => $bk->gr ?? 0,
            'pcs' => $bk->pcs ?? 0,
            'rupiah' => $bk->rupiah,
            'lokasi' => $bk->lokasi,
        ];
        return json_encode($data);
    }

    public function createLama(Request $r)
    {


        for ($i = 0; $i < count($r->no_box); $i++) {
            $no_box = $r->no_box[$i];
            $id_cabut = $r->id_cabut[$i];
            $data = [
                'no_box' => $r->no_box[$i] ?? '9999',
                'pcs_awal' => $r->pcs_awal[$i],
                'gr_awal' => $r->gr_awal[$i],
                'id_anak' => $r->id_anak[$i],
                'id_pengawas' => $r->id_pengawas[$i],
                'rupiah' => $r->rupiah[$i],
                'id_kelas' => $r->id_paket[$i],
                'tgl_terima' => $r->tgl_terima[$i],
            ];
            if ($id_cabut == 9999) {
                DB::table('cabut')->insert($data);
            } else {
                DB::table('cabut')->where('id_cabut', $id_cabut)->update($data);
            }
        }
        return json_encode([
            'pesan' => "Berhasil tambah data cabut"
        ]);
    }

    public function create(Request $r)
    {
        $inputs_by_box = [];

        // Mengumpulkan total PCS dan GR untuk setiap nomor kotak
        for ($i = 0; $i < count($r->no_box); $i++) {
            $no_box = $r->no_box[$i];
            $pcs_awal = $r->pcs_awal[$i];
            $gr_awal = $r->gr_awal[$i];

            // Menambahkan total PCS dan GR ke dalam array sesuai nomor kotak
            if (!isset($inputs_by_box[$no_box])) {
                $inputs_by_box[$no_box] = ['pcs_awal' => $pcs_awal, 'gr_awal' => $gr_awal];
            } else {
                $inputs_by_box[$no_box]['pcs_awal'] += $pcs_awal;
                $inputs_by_box[$no_box]['gr_awal'] += $gr_awal;
            }
        }

        // Memeriksa stok untuk setiap nomor kotak
        foreach ($inputs_by_box as $no_box => $input) {
            if ($no_box == 9999) {
                return json_encode([
                    'pesan' => "Gagal! Tidak dapat menyimpan data untuk nomor box 9999."
                ]);
            }

            // Mendapatkan stok dari database berdasarkan nomor kotak
            $stok = DB::table('bk')->where('no_box', $no_box)->first();

            // Memeriksa apakah stok ditemukan
            if (!$stok) {
                return json_encode([
                    'pesan' => "Gagal! Stok tidak ditemukan untuk nomor box $no_box."
                ]);
            }

            // Memeriksa apakah total PCS dan GR inputan tidak melebihi stok
            // if ($input['pcs_awal'] > $stok->pcs_awal || $input['gr_awal'] > $stok->gr_awal) {
            //     return json_encode([
            //         'pesan' => "Gagal! Total PCS atau GR inputan untuk nomor box $no_box melebihi stok yang tersedia."
            //     ]);
            // }

            // Lanjutkan dengan memasukkan data ke database
            for ($x = 0; $x < count($r->no_box); $x++) {

                if ($r->no_box[$x] == $no_box) {
                    $data = [
                        'no_box' => $r->no_box[$x],
                        'pcs_awal' => $r->pcs_awal[$x],
                        'gr_awal' => $r->gr_awal[$x],
                        'id_anak' => $r->id_anak[$x],
                        'id_pengawas' => $r->id_pengawas[$x],
                        'rupiah' => $r->rupiah[$x],
                        'id_kelas' => $r->id_paket[$x],
                        'tgl_terima' => $r->tgl_terima[$x],
                    ];
                    $id_cabut = $r->id_cabut[$x];
                    $cek = DB::table('cabut')->where([['no_box', $no_box], ['id_pengawas', $r->id_pengawas[$x]]])->exists();

                    if ($id_cabut == 9999 && !$cek) {
                        DB::table('cabut')->insert($data);
                    } else {
                        DB::table('cabut')->where('id_cabut', $id_cabut)->update($data);
                    }
                }
            }
        }

        return json_encode([
            'pesan' => "Berhasil tambah data cabut"
        ]);
    }

    public function history()
    {
        $cabut = Cabut::getCabut(true);
        $data = [
            'title' => 'history cabut',
            'cabut' => $cabut
        ];
        return view('home.cabut.history', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => Cabut::getStokBk(),
            'anak' => Cabut::getAnak(),
        ];
        return view('home.cabut.create', $data);
    }

    public function input_akhir(Request $r)
    {
        $get = DB::table('cabut')->where('id_cabut', $r->id_cabut);
        $pcs_awal = $get->first()->pcs_awal;
        if ($pcs_awal != $r->pcs_akhir) {
            return json_encode([
                'status' => 'error',
                'pesan' => 'pcs tidak sama dengan awal'
            ]);
        }
        $get->update([
            'pcs_akhir' => $r->pcs_akhir,
            'tgl_serah' => $r->tgl_serah,
            'gr_akhir' => $r->gr_akhir,
            'gr_flx' => $r->gr_flx,
            'pcs_hcr' => $r->pcs_hcr,
            'ket_hcr' => $r->ket_hcr,
            'bulan_dibayar' => $r->bulan,
            'eot' => $r->eot ?? 0,
            'ttl_rp' => $r->ttl_rp,
        ]);
        return json_encode([
            'status' => 'sukses',
            'pesan' => 'berhasil save'
        ]);
    }

    public function load_detail_cabut(Request $r)
    {
        $detail = Cabut::getDetailPerId($r->id_cabut);
        $data = [
            'detail' => $detail
        ];
        return view('home.cabut.load_modal_detail', $data);
    }

    public function selesai_cabut(Request $r)
    {
        $cek = DB::table('cabut')->where('id_cabut', $r->id_cabut)->first();
        if ($cek->gr_akhir > 0) {
            DB::table('cabut')->where('id_cabut', $r->id_cabut)->update(['selesai' => 'Y']);
        }
        return redirect()->route('cabut.index')->with('sukses', 'Data telah diselesaikan');
    }

    public function ditutup(Request $r)
    {
        $data = $r->tipe == 'tutup' ? ['penutup' => 'Y'] : ['selesai' => 'T'];
        $pesan = $r->tipe == 'tutup' ? 'ditutup' : 'dibuka';
        $boxAda = '';

        foreach ($r->datas as $d) {
            $getBox = DB::table('cabut')->where('id_cabut', $d);
            $no_box = $getBox->first()->no_box;

            if ($r->tipe != 'tutup') {
                $cekFormulir = DB::table('formulir_sarang')->where('no_box', $no_box)->exists();
                if ($cekFormulir) {
                    $boxAda .= $boxAda == '' ? $no_box : ", $no_box";
                } else {
                    $getBox->update($data);
                }
            }

            if ($r->tipe == 'tutup') {
                $getBox->update($data);
            }
        }

        return json_encode([
            'pesan' => !empty($boxAda) ? "ERROR! box : $boxAda sudah masuk formulir" : "berhasil $pesan"
        ]);
    }

    public function export(Request $r)
    {

        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.cabut.export';
        $tbl = Cabut::getCabut();
        return Excel::download(new CabutExport($tbl, $view), 'Export CABUT.xlsx');
    }

    public function rekap(Request $r)
    {
        $bulan = $r->bulan ?? date('m');
        $tahun = $r->tahun ?? date('Y');
        $ttlPcsBk = 0;
        $ttlGrBk = 0;
        $ttlPcsAwal = 0;
        $ttlGrAwal = 0;
        $ttlPcsAkhir = 0;
        $ttlGrAkhir = 0;
        $ttlFlx = 0;
        $ttlEot = 0;
        $ttlRp = 0;
        $cabutGroup = Cabut::queryRekapGroup($bulan, $tahun);

        foreach ($cabutGroup as $d) {
            $ttlPcsBk += $d->pcs_bk;
            $ttlGrBk += $d->gr_bk;
            $ttlPcsAwal += $d->pcs_awal;
            $ttlGrAwal += $d->gr_awal;
            $ttlPcsAkhir += $d->pcs_akhir;
            $ttlGrAkhir += $d->gr_akhir;
            $ttlRp += $d->ttl_rp;
            $ttlFlx += $d->gr_flx;
            $ttlEot += $d->eot;
        }

        $data = [
            'title' => 'Divisi Cabut',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'ttlPcsBk' => $ttlPcsBk,
            'ttlGrBk' => $ttlGrBk,
            'ttlPcsAwal' => $ttlPcsAwal,
            'ttlGrAwal' => $ttlGrAwal,
            'ttlPcsAkhir' => $ttlPcsAkhir,
            'ttlGrAkhir' => $ttlGrAkhir,
            'ttlFlx' => $ttlFlx,
            'ttlEot' => $ttlEot,
            'ttlRp' => $ttlRp,
            'cabutGroup' => $cabutGroup
        ];
        return view('home.cabut.rekap', $data);
    }

    public function export_rekap(Request $r)
    {
        $id_pengawas =  $r->id_pengawas;
        $view = 'home.cabut.export_rekap';
        $tbl = Cabut::queryRekap($id_pengawas);
        $fileName = "Export Rekap  " . auth()->user()->name;
        return Excel::download(new CabutRekapExport($tbl, $view), "$fileName.xlsx");
    }

    public function global(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');

        $pengawas = DB::select("SELECT b.id as id_pengawas,b.name,b.lokasi FROM bk as a
        JOIN users as b on a.penerima = b.id
        WHERE b.posisi_id = 13
        
        group by b.id ORDER BY b.lokasi ASC");

        $id_pengawas = $r->id_pengawas ?? auth()->user()->id;

        $tbl = Cabut::getRekapGlobal($bulan, $tahun, $id_pengawas);


        $datas = [];
        foreach ($pengawas as $p) {

            $ttlRp = 0;
            $tbl = Cabut::getRekapGlobal($bulan, $tahun, $p->id_pengawas);
            foreach ($tbl as $data) {
                $uangMakan = empty($data->umk_nominal) ? 0 : $data->umk_nominal * $data->hariMasuk;
                $ttl =
                    $data->ttl_rp +
                    $data->eo_ttl_rp +
                    $data->sortir_ttl_rp +
                    $data->ttl_rp_cetak +
                    $uangMakan +
                    $data->ttl_rp_dll -
                    $data->ttl_rp_denda;
                $ttlRp += $ttl;
            }
            if ($ttlRp != 0) {

                $datas[] = [
                    'pgws' => $p->name,
                    'lokasi' => $p->lokasi,
                    'ttlRp' => $ttlRp
                ];
            }
        }

        $data = [
            'title' => 'Global Rekap',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'pengawas' => $pengawas,
            'id_pengawas' => $id_pengawas,
            'tbl' => $tbl,
            'sumPgws' => $datas,
        ];
        return view('home.cabut.global', $data);
    }
    public function laporan_perhari(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $pengawas = DB::select("SELECT b.id as id_pengawas,b.name FROM bk as a
        JOIN users as b on a.penerima = b.id
        WHERE a.kategori != 'cetak'
        group by b.id");
        $id_pengawas = $r->id_pengawas ?? auth()->user()->id;
        $tbl = Cabut::getRekapLaporanHarian($bulan, $tahun, $id_pengawas);

        $data = [
            'title' => 'Laporan Perhari',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'pengawas' => $pengawas,
            'id_pengawas' => $id_pengawas,
            'nm_pengawas' => DB::table('users')->where('id', $id_pengawas)->first()->name,
            'tbl' => $tbl,
        ];
        return view('home.cabut.laporan_perhari', $data);
    }

    public function detailLaporanQuery($id_anak, $bulan, $tahun)
    {
        $cabut = DB::select("SELECT 
        a.no_box,
        a.tgl_terima,
        a.tgl_serah,
        a.pcs_awal,
        a.gr_awal,
        a.pcs_akhir,
        a.gr_akhir,
        CASE WHEN a.selesai = 'Y' THEN ttl_rp ELSE 0 END  as ttl_rp,
        CASE WHEN a.selesai = 'T' THEN rupiah ELSE 0 END as rp_target
        FROM cabut as a
        WHERE id_anak = $id_anak AND no_box != 9999 AND bulan_dibayar = $bulan AND tahun_dibayar = $tahun AND a.penutup = 'T'");

        $eo = DB::select("SELECT 
        no_box,
        gr_eo_awal as gr_awal,
        gr_eo_akhir as gr_akhir,
        CASE WHEN selesai = 'Y' THEN ttl_rp ELSE 0 END as ttl_rp,
        CASE WHEN selesai = 'T' THEN rp_target ELSE 0 END as rp_target,
        (1 - (gr_eo_akhir / gr_eo_awal)) * 100 as susut
        FROM eo 
        WHERE id_anak = $id_anak AND penutup = 'T' AND no_box != 9999 AND bulan_dibayar = '$bulan' AND YEAR(tgl_input) = '$tahun'");

        $sortir = DB::select("SELECT 
        no_box,
        pcs_awal as pcs_awal, 
        gr_awal as gr_awal, 
        pcs_akhir as pcs_akhir, 
        gr_akhir as gr_akhir, 
        CASE WHEN selesai = 'Y' THEN ttl_rp ELSE 0 END  as ttl_rp,
        CASE WHEN selesai = 'T' THEN rp_target ELSE 0 END as rp_target, 
        (1 - gr_akhir / gr_awal) * 100 as susut
        FROM `sortir` WHERE id_anak = $id_anak AND bulan = '$bulan' AND YEAR(tgl_input) = '$tahun' AND penutup = 'T' AND no_box != 9999");

        return [
            'cabut' => $cabut,
            'eo' => $eo,
            'sortir' => $sortir,
        ];
    }

    public function detail_laporan_harian(Request $r)
    {
        $id_anak = $r->id_anak;
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');

        $anak = DB::table('tb_anak')->where('id_anak', $id_anak)->first();

        $absen = DB::selectOne("SELECT count(*) as ttl FROM absen AS a 
        WHERE a.bulan_dibayar = '$bulan' AND a.tahun_dibayar = '$tahun' AND a.id_anak = '$id_anak'
         group BY a.id_anak;");

        $detail = $this->detailLaporanQuery($id_anak, $bulan, $tahun);
        $data = [
            'title' => 'Laporan Detail Perhari',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'id_anak' => $id_anak,
            'anak' => $anak,
            'cabut' => $detail['cabut'],
            'eo' => $detail['eo'],
            'sortir' => $detail['sortir'],
            'absen' => $absen->ttl,
        ];
        return view('home.cabut.detail_laporan_perhari', $data);
    }

    public function export_global(Request $r)
    {
        $bulan =  $r->bulan;
        $tahun =  $r->tahun;
        $id_pengawas = auth()->user()->id;

        $pengawas = DB::select("SELECT b.id as id_pengawas,b.name FROM bk as a
        JOIN users as b on a.penerima = b.id
        WHERE a.kategori != 'cetak' AND b.posisi_id = 13
        group by b.id");
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        foreach ($pengawas as $i => $d) {
            $sheet = $spreadsheet->createSheet($i);
            $sheet->setTitle(strtoupper($d->name));

            $koloms = [
                'A1' => 'cabut',
                'M1' => 'cabut eo',
                'Q1' => 'sortir',
                'X1' => 'gajih',
                'A2' => 'pgws',
                'B2' => 'hari masuk',
                'C2' => 'nama',
                'D2' => 'Kelas',
                'E2' => 'pcs awal',
                'F2' => 'gr awal',
                'G2' => 'pcs akhir',
                'H2' => 'gr akhir',
                'I2' => 'eot gr',
                'J2' => 'gr flx',
                'K2' => 'susut %',
                'L2' => 'ttl rp',

                'M2' => 'gr eo awal',
                'N2' => 'gr eo akhir',
                'O2' => 'susut %',
                'P2' => 'ttl rp',

                'Q2' => 'pcs awal',
                'R2' => 'gr awal',
                'S2' => 'pcs akhir',
                'T2' => 'gr akhir',
                'U2' => 'susut %',
                'V2' => 'ttl rp',

                'W2' => 'kerja dll',
                'X2' => 'uang makan',
                'Y2' => 'rp denda',

                'Z2' => 'ttl gaji',
                'AA2' => 'rata2',
            ];

            foreach ($koloms as $kolom => $isiKolom) {
                $sheet->setCellValue($kolom, ucwords($isiKolom));
            }

            $styleBold = [
                'font' => [
                    'bold' => true,
                ],
            ];
            $styleBaris = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $warnaBg = [
                'A1:L1' => 'D9D9D9',
                'M1:P1' => 'F79646',
                'Q1:V1' => '8DB4E2',

                'L2' => 'FF0000',
                'P2' => 'FF0000',
                'V2' => 'FF0000',
                'W2' => 'FF0000',
                'Y2' => 'FF0000',
            ];

            foreach ($warnaBg as $b => $i) {
                $sheet->getStyle($b)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($i);
            }

            $sheet->mergeCells('A1:L1');
            $sheet->mergeCells('M1:P1');
            $sheet->mergeCells('Q1:V1');
            $sheet->mergeCells('X1:AA1');

            $style = $sheet->getStyle('A1:X1');
            $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $style->getFont()->setBold(true);
            $sheet->getStyle('A2:AA2')->applyFromArray($styleBold);

            $TtlRp = 0;
            $eoTtlRp = 0;
            $sortirTtlRp = 0;
            $dllTtlRp = 0;
            $dendaTtlRp = 0;
            $ttlTtlRp = 0;

            $ttlCbtPcsAwal = 0;
            $ttlCbtGrAwal = 0;
            $ttlCbtPcsAkhir = 0;
            $ttlCbtGrAkhir = 0;
            $ttlCbtEotGr = 0;
            $ttlCbtGrFlx = 0;
            $ttlCbtTtlRp = 0;

            $ttlSoritrPcsAwal = 0;
            $ttlSoritrGrAwal = 0;
            $ttlSoritrPcsAkhir = 0;
            $ttlSoritrGrAkhir = 0;
            $ttlSortirRp = 0;

            $ttlEoGrAwal  = 0;
            $ttlEoGrAkhir  = 0;
            $ttlEoRp  = 0;
            $ttlUangMakan = 0;

            $bulanDibayar = date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun))));
            $row = 3;
            $tbl = Cabut::getRekapGlobal($bulan, $tahun, $d->id_pengawas);
            foreach ($tbl as $data) {
                $sheet->setCellValue('A' . $row, $data->pgws)
                    ->setCellValue('B' . $row, $data->hariMasuk)
                    ->setCellValue('C' . $row, $data->nm_anak)
                    ->setCellValue('D' . $row, $data->kelas)
                    ->setCellValue('E' . $row, $data->pcs_awal)
                    ->setCellValue('F' . $row, $data->gr_awal)
                    ->setCellValue('G' . $row, $data->pcs_akhir)
                    ->setCellValue('H' . $row, $data->gr_akhir)
                    ->setCellValue('I' . $row, $data->eot)
                    ->setCellValue('J' . $row, $data->gr_flx);
                $susutCbt = empty($data->gr_akhir) ? 0 : (1 - (($data->gr_akhir + $data->gr_flx) / $data->gr_awal)) * 100;
                $sheet->setCellValue('K' . $row, $susutCbt)
                    ->setCellValue('L' . $row, $data->ttl_rp)

                    ->setCellValue('M' . $row, $data->eo_awal)
                    ->setCellValue('N' . $row, $data->eo_akhir);
                $susutEo =  empty($data->eo_akhir) ? 0 : (1 - ($data->eo_akhir / $data->eo_awal)) * 100;

                $sheet->setCellValue('O' . $row, $susutEo)
                    ->setCellValue('P' . $row, $data->eo_ttl_rp)
                    ->setCellValue('Q' . $row, $data->sortir_pcs_awal)
                    ->setCellValue('R' . $row, $data->sortir_gr_awal)
                    ->setCellValue('S' . $row, $data->sortir_pcs_akhir)
                    ->setCellValue('T' . $row, $data->sortir_gr_akhir);
                $susutSortir = empty($data->sortir_gr_akhir) ? 0 : (1 - ($data->sortir_gr_akhir / $data->sortir_gr_awal)) * 100;
                $uang_makan = empty($data->umk_nominal) ? 0 : $data->umk_nominal * $data->hariMasuk;
                $sheet->setCellValue('U' . $row, $susutSortir)
                    ->setCellValue('V' . $row, $data->sortir_ttl_rp)
                    ->setCellValue('W' . $row, $data->ttl_rp_dll)
                    ->setCellValue('X' . $row, $uang_makan)
                    ->setCellValue('Y' . $row, $data->ttl_rp_denda);
                $ttl = $data->ttl_rp + $data->eo_ttl_rp + $data->sortir_ttl_rp + $data->ttl_rp_dll + $uang_makan - $data->ttl_rp_denda;
                $rata = empty($data->hariMasuk) ? 0 : $ttl / $data->hariMasuk;
                $sheet->setCellValue('Z' . $row, $ttl)
                    ->setCellValue('AA' . $row, $rata);

                $ttlCbtPcsAwal += $data->pcs_awal;
                $ttlCbtGrAwal += $data->gr_awal;
                $ttlCbtPcsAkhir += $data->pcs_akhir;
                $ttlCbtGrAkhir += $data->gr_akhir;
                $ttlCbtEotGr += $data->eot;
                $ttlCbtGrFlx += $data->gr_flx;
                $ttlCbtTtlRp += $data->ttl_rp;

                $ttlEoGrAwal  += $data->eo_awal;
                $ttlEoGrAkhir  += $data->eo_akhir;
                $ttlEoRp  += $data->eo_ttl_rp;

                $ttlSoritrPcsAwal += $data->sortir_pcs_awal;
                $ttlSoritrGrAwal += $data->sortir_gr_awal;
                $ttlSoritrPcsAkhir += $data->sortir_pcs_akhir;
                $ttlSoritrGrAkhir += $data->sortir_gr_akhir;
                $ttlSortirRp += $data->sortir_ttl_rp;

                $TtlRp += $data->ttl_rp;
                $eoTtlRp += $data->eo_ttl_rp;
                $sortirTtlRp += $data->sortir_ttl_rp;
                $dllTtlRp += $data->ttl_rp_dll;
                $ttlUangMakan += $uang_makan;
                $dendaTtlRp += $data->ttl_rp_denda;
                $ttlTtlRp += $ttl;

                $row++;
            }

            $rowTotal = $row;


            $sheet->setCellValue('A' . $rowTotal, 'TOTAL');
            $sheet->setCellValue('E' . $rowTotal, $ttlCbtPcsAwal);
            $sheet->setCellValue('F' . $rowTotal, $ttlCbtGrAwal);
            $sheet->setCellValue('G' . $rowTotal, $ttlCbtPcsAkhir);
            $sheet->setCellValue('H' . $rowTotal, $ttlCbtGrAkhir);
            $sheet->setCellValue('I' . $rowTotal, $ttlCbtEotGr);
            $sheet->setCellValue('J' . $rowTotal, $ttlCbtGrFlx);
            $sheet->setCellValue('L' . $rowTotal, $ttlCbtTtlRp);

            $sheet->setCellValue('M' . $rowTotal, $ttlEoGrAwal);
            $sheet->setCellValue('N' . $rowTotal, $ttlEoGrAkhir);
            $sheet->setCellValue('P' . $rowTotal, $ttlEoRp);



            $sheet->setCellValue('Q' . $rowTotal, $ttlSoritrPcsAwal);
            $sheet->setCellValue('R' . $rowTotal, $ttlSoritrGrAwal);
            $sheet->setCellValue('S' . $rowTotal, $ttlSoritrPcsAkhir);
            $sheet->setCellValue('T' . $rowTotal, $ttlSoritrGrAkhir);
            $sheet->setCellValue('V' . $rowTotal, $ttlSortirRp);

            $sheet->setCellValue('W' . $rowTotal, $dllTtlRp);
            $sheet->setCellValue('X' . $rowTotal, $ttlUangMakan);
            $sheet->setCellValue('Y' . $rowTotal, $dendaTtlRp);
            $sheet->setCellValue('Z' . $rowTotal, $ttlTtlRp);

            $sheet->getStyle("A$rowTotal:AA$rowTotal")->applyFromArray($styleBold);

            $baris = $rowTotal - 1;
            $sheet->getStyle('A2:AA' . $baris)->applyFromArray($styleBaris);
        }
        $writer = new Xlsx($spreadsheet);

        // Menggunakan response untuk mengirimkan file ke browser
        $fileName = "Gaji Export Global $bulanDibayar $tahun";
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

        // $view = 'home.cabut.export_global';
        // $tbl = Cabut::getRekapGlobal($bulan, $tahun, $id_pengawas);
        // $fileName = "Export Rekap Global " . auth()->user()->name;
        // return Excel::download(new CabutGlobalExport($tbl, $view), "$fileName.xlsx");
    }
    public function cekBgSisa($sheet, $pcsBk, $pcsAwal, $grBk, $grAwal, $row)
    {
        $cekSisaPcs = $pcsBk - $pcsAwal > 0 ? true : false;
        $cekSisaGr = $grBk - $grAwal > 0 ? true : false;

        if ($cekSisaGr || $cekSisaPcs) {
            return $sheet->getStyle("A$row:T$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        }
    }
    public function export_ibu(Request $r)
    {
        $pengawas = DB::select("SELECT b.id as id_pengawas,b.name FROM bk as a
        JOIN users as b on a.penerima = b.id
        WHERE a.kategori != 'cetak' AND a.selesai = 'T'
        group by b.id");

        $bulan = $r->bulan;
        $tahun = $r->tahun;

        // Membuat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        foreach ($pengawas as $i => $d) {
            // Membuat sheet baru
            $sheet = $spreadsheet->createSheet($i);
            $sheet->setTitle(strtoupper($d->name));

            // Mengisi sheet dengan data dari kategori tertentu
            $koloms = [
                'A1' => 'no box',
                'B1' => 'pcs awal bk',
                'C1' => 'gr awal bk',
                'D1' => 'bulan',
                'E1' => 'pgws',
                'F1' => 'pcs awal kerja',
                'G1' => 'gr awal kerja',
                'H1' => 'pcs akhir kerja',
                'I1' => 'gr akhir kerja',
                'J1' => 'eot',
                'K1' => 'flx',
                'L1' => 'susut',
                'M1' => 'ttl rp',
                'N1' => 'pcs sisa bk',
                'O1' => 'gr sisa bk',
                'P1' => 'kategori',
            ];
            foreach ($koloms as $kolom => $isiKolom) {
                $sheet->setCellValue($kolom, ucwords($isiKolom));
            }
            // Mengatur bold dan border untuk A1 dan B1
            $styleBold = [
                'font' => [
                    'bold' => true,
                ],
            ];
            $styleBaris = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A1:P1')->applyFromArray($styleBold);
            $sheet->getStyle('A1:P1')->applyFromArray($styleBaris);
            $ttlPcsBk = 0;
            $ttlGrBk = 0;

            $ttlPcsAwal = 0;
            $ttlGrAwal = 0;
            $ttlPcsAkhir = 0;
            $ttlGrAkhir = 0;

            $ttlFlx = 0;
            $ttlEot = 0;
            $ttlSusut = 0;

            $ttlRp = 0;

            $ttlPcsSisa = 0;
            $ttlGrSisa = 0;

            $ttlRpCabut = 0;
            $ttlRpEo = 0;
            $ttlRpSortir = 0;
            // cabut
            $bulanDibayar = date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun))));
            $row = 2;
            $cabut = Cabut::queryRekap($d->id_pengawas, $bulan, $tahun);
            foreach ($cabut as $data) {
                $sheet->setCellValue('A' . $row, $data->no_box);
                $sheet->setCellValue('B' . $row, $data->pcs_bk);
                $sheet->setCellValue('C' . $row, $data->gr_bk);
                $sheet->setCellValue('D' . $row, $bulanDibayar);
                $sheet->setCellValue('E' . $row, $data->pengawas);
                $sheet->setCellValue('F' . $row, $data->pcs_awal);
                $sheet->setCellValue('G' . $row, $data->gr_awal);
                $sheet->setCellValue('H' . $row, $data->pcs_akhir);
                $sheet->setCellValue('I' . $row, $data->gr_akhir);
                $sheet->setCellValue('J' . $row, $data->eot);
                $sheet->setCellValue('K' . $row, $data->gr_flx);
                $susut = empty($data->gr_awal) ? 0 : (1 - ($data->gr_flx + $data->gr_akhir) / $data->gr_awal) * 100;
                $sheet->setCellValue('L' . $row, number_format($susut, 0));
                $sheet->setCellValue('M' . $row, $data->ttl_rp);
                $sheet->setCellValue('N' . $row, $data->pcs_bk - $data->pcs_awal);
                $sheet->setCellValue('O' . $row, $data->gr_bk - $data->gr_awal);
                $this->cekBgSisa($sheet, $data->pcs_bk, $data->pcs_awal, $data->gr_bk, $data->gr_awal,  $row);
                $sheet->setCellValue('P' . $row, $data->kategori);

                $ttlPcsBk += $data->pcs_bk;
                $ttlGrBk += $data->gr_bk;

                $ttlPcsAwal += $data->pcs_awal;
                $ttlGrAwal += $data->gr_awal;
                $ttlPcsAkhir += $data->pcs_akhir;
                $ttlGrAkhir += $data->gr_akhir;

                $ttlFlx += $data->gr_flx;
                $ttlEot += $data->eot;

                $ttlRp += $data->ttl_rp;
                $ttlRpCabut += $data->rupiah;

                $ttlPcsSisa += $data->pcs_bk - $data->pcs_awal;
                $ttlGrSisa += $data->gr_bk - $data->gr_awal;
                $row++;
            }

            // eo
            $rowEo = $row;
            $eo = Eo::queryRekap($d->id_pengawas, $bulan, $tahun);
            foreach ($eo as $data) {
                $sheet->setCellValue('A' . $rowEo, $data->no_box);
                $sheet->setCellValue('B' . $rowEo, 0);
                $sheet->setCellValue('C' . $rowEo, $data->gr_bk);
                $sheet->setCellValue('D' . $rowEo, $bulanDibayar);
                $sheet->setCellValue('E' . $rowEo, $d->name);
                $sheet->setCellValue('F' . $rowEo, 0);
                $sheet->setCellValue('G' . $rowEo, $data->gr_eo_awal);
                $sheet->setCellValue('H' . $rowEo, 0);
                $sheet->setCellValue('I' . $rowEo, $data->gr_eo_akhir);
                $sheet->setCellValue('J' . $rowEo, 0);
                $sheet->setCellValue('K' . $rowEo, 0);
                $susut = empty($data->gr_eo_awal) ? 0 : (1 - ($data->gr_eo_akhir / $data->gr_eo_awal)) * 100;
                $sheet->setCellValue('L' . $rowEo, number_format($susut, 0));
                $sheet->setCellValue('M' . $rowEo, $data->rupiah);
                $sheet->setCellValue('N' . $rowEo, 0);
                $sheet->setCellValue('O' . $rowEo, $data->gr_bk - $data->gr_eo_awal);

                $this->cekBgSisa(
                    $sheet,
                    0,
                    0,
                    $data->gr_bk,
                    $data->gr_eo_awal,
                    $rowEo
                );

                $sheet->setCellValue('P' . $rowEo, 'Eo');

                $ttlPcsBk += 0;
                $ttlGrBk += $data->gr_bk;

                $ttlPcsAwal += 0;
                $ttlGrAwal += $data->gr_eo_awal;
                $ttlPcsAkhir += 0;
                $ttlGrAkhir += $data->gr_eo_akhir;

                $ttlFlx += 0;
                $ttlEot += 0;

                $ttlRp += $data->rupiah;
                $ttlRpEo += $data->rupiah;

                $ttlPcsSisa += 0;
                $ttlGrSisa += $data->gr_bk - $data->gr_eo_awal;
                $rowEo++;
            }

            // sortir
            $rowSortir = $rowEo;
            $sortir = Sortir::queryRekap($d->id_pengawas, $bulan, $tahun);
            foreach ($sortir as $data) {
                $sheet->setCellValue('A' . $rowSortir, $data->no_box);
                $sheet->setCellValue('B' . $rowSortir, $data->pcs_bk);
                $sheet->setCellValue('C' . $rowSortir, $data->gr_bk);
                $sheet->setCellValue('D' . $rowSortir, $bulanDibayar);
                $sheet->setCellValue('E' . $rowSortir, $data->pengawas);
                $sheet->setCellValue('F' . $rowSortir, $data->pcs_awal);
                $sheet->setCellValue('G' . $rowSortir, $data->gr_awal);
                $sheet->setCellValue('H' . $rowSortir, $data->pcs_akhir);
                $sheet->setCellValue('I' . $rowSortir, $data->gr_akhir);
                $sheet->setCellValue('J' . $rowSortir, 0);
                $sheet->setCellValue('K' . $rowSortir, 0);
                $susut = empty($data->gr_awal) ? 0 : (1 - $data->gr_akhir / $data->gr_awal) * 100;
                $sheet->setCellValue('L' . $rowSortir, number_format($susut, 0));
                $sheet->setCellValue('M' . $rowSortir, $data->rupiah);
                $sheet->setCellValue('N' . $rowSortir, $data->pcs_bk - $data->pcs_awal);
                $sheet->setCellValue('O' . $rowSortir, $data->gr_bk - $data->gr_awal);
                $sheet->setCellValue('P' . $rowSortir, $data->kategori);
                $this->cekBgSisa(
                    $sheet,
                    $data->pcs_bk,
                    $data->pcs_awal,
                    $data->gr_bk,
                    $data->gr_awal,
                    $rowEo
                );
                $ttlPcsBk += $data->pcs_bk;
                $ttlGrBk += $data->gr_bk;

                $ttlPcsAwal += $data->pcs_awal;
                $ttlGrAwal += $data->gr_awal;
                $ttlPcsAkhir += $data->pcs_akhir;
                $ttlGrAkhir += $data->gr_akhir;

                $ttlFlx += 0;
                $ttlEot += 0;

                $ttlRp += $data->rupiah;
                $ttlRpSortir += $data->rupiah;

                $ttlPcsSisa += $data->pcs_bk - $data->pcs_awal;
                $ttlGrSisa += $data->gr_bk - $data->gr_awal;

                $rowSortir++;
            }

            // dll
            $rowDll = $rowSortir;
            $dll = DB::selectOne("SELECT a.bulan_dibayar,a.tgl,b.nama,c.name, SUM(rupiah) AS total_rupiah
            FROM tb_hariandll as a
            LEFT JOIN tb_anak as b on a.id_anak = b.id_anak
            LEFT JOIN users as c on c.id = b.id_pengawas
            WHERE bulan_dibayar = '$bulan' AND tahun_dibayar = '$tahun' AND a.ditutup = 'T' AND b.id_pengawas = '$d->id_pengawas'
            GROUP BY b.id_pengawas");
            $rupiahDll = $dll->total_rupiah ?? 0;
            $sheet->setCellValue('A' . $rowDll, 'Dll');
            $sheet->setCellValue('D' . $rowDll, $bulanDibayar);
            $sheet->setCellValue('E' . $rowDll, $d->name);
            $sheet->setCellValue('M' . $rowDll, $rupiahDll);
            $ttlRp += $rupiahDll;
            $ttlRpDll = $rupiahDll;




            $ttlSusut = empty($ttlGrAwal) ? 0 : (1 - ($ttlFlx + $ttlGrAkhir) / $ttlGrAwal) * 100;
            $rowTotal = $rowDll + 1;
            $sheet->setCellValue('A' . $rowTotal, "TOTAL");
            $sheet->setCellValue('B' . $rowTotal, $ttlPcsBk);
            $sheet->setCellValue('C' . $rowTotal, $ttlGrBk);
            $sheet->setCellValue('D' . $rowTotal, $bulanDibayar);
            $sheet->setCellValue('E' . $rowTotal, $d->name);
            $sheet->setCellValue('F' . $rowTotal, $ttlPcsAwal);
            $sheet->setCellValue('G' . $rowTotal, $ttlGrAwal);
            $sheet->setCellValue('H' . $rowTotal, $ttlPcsAkhir);
            $sheet->setCellValue('I' . $rowTotal, $ttlGrAkhir);
            $sheet->setCellValue('J' . $rowTotal, $ttlEot);
            $sheet->setCellValue('K' . $rowTotal, $ttlFlx);
            $sheet->setCellValue('L' . $rowTotal, number_format($ttlSusut, 0));
            $sheet->setCellValue('M' . $rowTotal, $ttlRp);
            $sheet->setCellValue('N' . $rowTotal, $ttlPcsSisa);
            $sheet->setCellValue('O' . $rowTotal, $ttlGrSisa);
            $sheet->getStyle("A$rowTotal:P$rowTotal")->applyFromArray($styleBold);

            $rowDenda = $rowTotal + 1;
            $denda = DB::selectOne("SELECT sum(nominal) as rupiah FROM `tb_denda` as a
            join tb_anak as b on a.id_anak = b.id_anak
            WHERE a.bulan_dibayar = '$bulan' AND YEAR(a.tgl) = '$tahun' AND a.admin = '$d->name'
            GROUP BY a.admin");
            $rupiahDenda = $denda->rupiah ?? 0;
            $sheet->setCellValue('A' . $rowDenda, 'Denda');
            $sheet->setCellValue('M' . $rowDenda, $rupiahDenda);

            $rowGrandTotal = $rowDenda + 1;
            $grandTotal = $ttlRp - $rupiahDenda;
            $sheet->setCellValue('A' . $rowGrandTotal, 'GRAND TOTAL');
            $sheet->setCellValue('M' . $rowGrandTotal, $grandTotal);
            $sheet->getStyle("A$rowGrandTotal:P$rowGrandTotal")->applyFromArray($styleBold);

            $baris = $rowGrandTotal;
            $sheet->getStyle('A2:P' . $baris)->applyFromArray($styleBaris);
        }

        // Membuat objek writer untuk menulis spreadsheet ke file
        $writer = new Xlsx($spreadsheet);

        // Menggunakan response untuk mengirimkan file ke browser
        $fileName = "Gaji Sarang $bulanDibayar $tahun Kasih Ibu Linda";
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

    public function cabut_ok(Request $r)
    {
        $cabut = Cabut::getCabut();
        foreach ($cabut as $d) {
            $hasil = rumusTotalRp($d);
            DB::table('cabut')->where([['id_cabut', $d->id_cabut], ['bulan_dibayar', 9]])->update([
                'ttl_rp' => $hasil->ttl_rp
            ]);
        }
        return redirect()->route('cabut.index')->with('sukses', 'Data Tercek');
    }

    public function export_sintaFromCabut(Request $r)
    {
        $bulan = $r->bulan;
        $tahun = $r->tahun;
        $bulanDibayar = date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun))));
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary Box');

        $koloms = [
            'A1' => 'no lot',
            'B1' => 'partai herry',
            'C1' => 'no box',
            'D1' => 'pcs awal bk',
            'E1' => 'gr awal bk',
            'F1' => 'bulan',
            'G1' => 'pgws',
            'H1' => 'pcs awal kerja',
            'I1' => 'gr awal kerja',
            'J1' => 'pcs akhir kerja',
            'K1' => 'gr akhir kerja',
            'L1' => 'eot',
            'M1' => 'flx',
            'N1' => 'susut',
            'O1' => 'ttl rp',
            'P1' => 'pcs sisa bk',
            'Q1' => 'gr sisa bk',
            'R1' => 'kategori',
        ];
        foreach ($koloms as $kolom => $isiKolom) {
            $sheet->setCellValue($kolom, ucwords($isiKolom));
        }
        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:R1')->applyFromArray($styleBold);
        $sheet->getStyle('A1:R1')->applyFromArray($styleBaris);

        $row = 2;
        $cabut = DB::select("SELECT 
        a.bulan_dibayar,
        year(a.tgl_serah) as tahun_dibayar,
        a.no_box,
        bk.pcs_awal as pcs_bk,
        bk.gr_awal as gr_bk,
        CONCAT(
            DATE_FORMAT(a.tgl_serah, '%b'), ' ', 
            DATE_FORMAT(a.tgl_serah, '%Y')
        ) AS bulan_dibayar_format ,
        b.name as pengawas,
        sum(a.pcs_awal) as pcs_awal,
        sum(a.gr_awal) as gr_awal,
        sum(a.pcs_akhir) as pcs_akhir,
        sum(a.gr_akhir) as gr_akhir,
        sum(a.eot) as eot,
        sum(a.gr_flx) as gr_flx,
        sum(a.ttl_rp) as ttl_rp,
        bk.kategori,
        bk.no_lot,
        bk.nm_partai
        FROM `cabut`as a
        join users as b on a.id_pengawas = b.id
        left join (
            SELECT no_lot,nm_partai,no_box,pcs_awal,gr_awal,penerima,kategori from bk GROUP BY no_box,penerima
        ) as bk on a.no_box = bk.no_box and a.id_pengawas = bk.penerima
        WHERE a.no_box != 9999 AND a.bulan_dibayar != '' group by a.no_box,a.bulan_dibayar");

        foreach ($cabut as $data) {

            $sheet->setCellValue('A' . $row, $data->no_lot);
            $sheet->setCellValue('B' . $row, $data->nm_partai);
            $sheet->setCellValue('C' . $row, $data->no_box);
            $sheet->setCellValue('D' . $row, $data->pcs_bk);
            $sheet->setCellValue('E' . $row, $data->gr_bk);
            $sheet->setCellValue('F' . $row, $data->bulan_dibayar_format);
            $sheet->setCellValue('G' . $row, $data->pengawas);
            $sheet->setCellValue('H' . $row, $data->pcs_awal);
            $sheet->setCellValue('I' . $row, $data->gr_awal);
            $sheet->setCellValue('J' . $row, $data->pcs_akhir);
            $sheet->setCellValue('K' . $row, $data->gr_akhir);
            $sheet->setCellValue('L' . $row, $data->eot);
            $sheet->setCellValue('M' . $row, $data->gr_flx);
            $susut = empty($data->gr_awal) ? 0 : (1 - ($data->gr_flx + $data->gr_akhir) / $data->gr_awal) * 100;
            $sheet->setCellValue('N' . $row, number_format($susut, 0));
            $sheet->setCellValue('O' . $row, $data->ttl_rp);
            $sheet->setCellValue('P' . $row, $data->pcs_bk - $data->pcs_awal);
            $sheet->setCellValue('Q' . $row, $data->gr_bk - $data->gr_awal);
            $this->cekBgSisa($sheet, $data->pcs_bk, $data->pcs_awal, $data->gr_bk, $data->gr_awal,  $row);
            $sheet->setCellValue('R' . $row, $data->kategori);

            $row++;
        }
        $rowEo = $row;
        $eo = DB::select("SELECT 
        a.no_box,
        a.bulan_dibayar,
        bk.gr_awal as gr_bk,
        year(a.tgl_serah) as tahun_dibayar,
        CONCAT(
            DATE_FORMAT(a.tgl_serah, '%b'), ' ', 
            DATE_FORMAT(a.tgl_serah, '%Y')
        ) AS bulan_dibayar_format,
        b.name as pengawas,
        sum(a.gr_eo_awal) as gr_eo_awal,
        sum(a.gr_eo_akhir) as gr_eo_akhir,
        sum(a.ttl_rp) as rupiah,
        bk.no_lot,
        bk.nm_partai
        FROM `eo` as a
        JOIN users as b on a.id_pengawas = b.id
        left join (
            SELECT no_lot,nm_partai,no_box,pcs_awal,gr_awal,penerima,kategori from bk GROUP BY no_box,penerima
        ) as bk on a.no_box = bk.no_box and a.id_pengawas = bk.penerima
        WHERE a.no_box != 9999 AND a.bulan_dibayar != '' group by a.no_box,a.bulan_dibayar;");
        foreach ($eo as $data) {
            $sheet->setCellValue('A' . $row, $data->no_lot);
            $sheet->setCellValue('B' . $row, $data->nm_partai);
            $sheet->setCellValue('C' . $rowEo, $data->no_box);
            $sheet->setCellValue('D' . $rowEo, 0);
            $sheet->setCellValue('E' . $rowEo, $data->gr_bk);
            $sheet->setCellValue('F' . $rowEo, $data->bulan_dibayar_format);
            $sheet->setCellValue('G' . $rowEo, $data->pengawas);
            $sheet->setCellValue('H' . $rowEo, 0);
            $sheet->setCellValue('I' . $rowEo, $data->gr_eo_awal);
            $sheet->setCellValue('J' . $rowEo, 0);
            $sheet->setCellValue('K' . $rowEo, $data->gr_eo_akhir);
            $sheet->setCellValue('L' . $rowEo, 0);
            $sheet->setCellValue('M' . $rowEo, 0);
            $susut = empty($data->gr_eo_awal) ? 0 : (1 - ($data->gr_eo_akhir / $data->gr_eo_awal)) * 100;
            $sheet->setCellValue('N' . $rowEo, number_format($susut, 0));
            $sheet->setCellValue('O' . $rowEo, $data->rupiah);
            $sheet->setCellValue('P' . $rowEo, 0);
            $sheet->setCellValue('Q' . $rowEo, $data->gr_bk - $data->gr_eo_awal);
            // $this->cekBgSisa(
            //     $sheet,
            //     0,
            //     0,
            //     $data->gr_bk,
            //     $data->gr_eo_awal,
            //     $rowEo
            // );

            $sheet->setCellValue('R' . $rowEo, 'Eo');
            $rowEo++;
        }
        $baris = $rowEo + 1;
        $sheet->getStyle('A2:R' . $baris)->applyFromArray($styleBaris);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Summary Box Cabut Eo $bulanDibayar $tahun";
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

    public function export_sinta(Request $r)
    {
        $bulan = $r->bulan;
        $tahun = $r->tahun;
        $bulanDibayar = date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun))));
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();;
        $sheet->setTitle('Summary Box');

        $koloms = [
            'A1' => 'no lot',
            'B1' => 'partai herry',
            'C1' => 'no box',
            'D1' => 'pcs awal bk',
            'E1' => 'gr awal bk',
            'F1' => 'bulan',
            'G1' => 'pgws',
            'H1' => 'pcs awal kerja',
            'I1' => 'gr awal kerja',
            'J1' => 'pcs akhir kerja',
            'K1' => 'gr akhir kerja',
            'L1' => 'eot',
            'M1' => 'flx',
            'N1' => 'susut',
            'O1' => 'ttl rp',
            'P1' => 'pcs sisa bk',
            'Q1' => 'gr sisa bk',
            'R1' => 'kategori',
            'S1' => 'Tipe',
            'T1' => 'Ket',
        ];
        foreach ($koloms as $kolom => $isiKolom) {
            $sheet->setCellValue($kolom, ucwords($isiKolom));
        }
        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:T1')->applyFromArray($styleBold);
        $sheet->getStyle('A1:T1')->applyFromArray($styleBaris);

        $row = 2;
        $cabut = DB::select("SELECT 
        b.name as pengawas,
         bk.pcs_awal as pcs_bk,
         bk.gr_awal as gr_bk,
         bk.kategori,
         bk.no_lot,
         bk.nm_partai,
         cbt.pcs_awal,
         cbt.gr_awal,
         cbt.pcs_akhir,
         cbt.gr_akhir,
         cbt.eot,
         cbt.gr_flx,
         cbt.ttl_rp,
         cbt.bulan_dibayar_format,
         cbt.bulan_dibayar,
         cbt.tahun_dibayar,
         bk.no_box,
         bk.ket,
         bk.tipe,
         eo.bulan_dibayar as eo_bulan_dibayar,
         eo.tahun_dibayar as eo_tahun_dibayar,
         eo.bulan_dibayar_format as eo_bulan_dibayar_format,
         eo.gr_eo_awal,
         eo.gr_eo_akhir,
         eo.rupiah as eo_rupiah
         FROM bk 
         join users as b on bk.penerima = b.id 
         LEFT JOIN ( 
            select a.bulan_dibayar,
             year(a.tgl_serah) as tahun_dibayar,
             CONCAT( DATE_FORMAT(a.tgl_serah, '%b'), ' ', DATE_FORMAT(a.tgl_serah, '%Y') ) AS bulan_dibayar_format,
              sum(a.pcs_awal) as pcs_awal,
              sum(a.gr_awal) as gr_awal,
              sum(a.pcs_akhir) as pcs_akhir,
              sum(a.gr_akhir) as gr_akhir,
              sum(a.eot) as eot,
              sum(a.gr_flx) as gr_flx,
              sum(a.ttl_rp) as ttl_rp,
              a.id_pengawas,
              a.no_box 
              from cabut as a 
              WHERE a.no_box != 9999 AND a.bulan_dibayar= '$bulan'  and a.tahun_dibayar = '$tahun'
              group by a.no_box, a.bulan_dibayar 
            ) as cbt on bk.no_box = cbt.no_box and cbt.id_pengawas = bk.penerima
        LEFT JOIN (
            SELECT 
            a.no_box,
            a.bulan_dibayar,
            year(a.tgl_serah) as tahun_dibayar,
            CONCAT(
                DATE_FORMAT(a.tgl_serah, '%b'), ' ', 
                DATE_FORMAT(a.tgl_serah, '%Y')
            ) AS bulan_dibayar_format,
            sum(a.gr_eo_awal) as gr_eo_awal,
            sum(a.gr_eo_akhir) as gr_eo_akhir,
            sum(a.ttl_rp) as rupiah,
            a.id_pengawas
            FROM `eo` as a
            WHERE a.no_box != 9999 AND a.bulan_dibayar = '$bulan' and YEAR(a.tgl_input) = '$tahun' group by a.no_box,a.bulan_dibayar,YEAR(a.tgl_input)
        ) as eo ON eo.no_box = bk.no_box and eo.id_pengawas = bk.penerima
        WHERE bk.pengawas = 'sinta' AND bk.kategori LIKE '%cabut%';");

        foreach ($cabut as $data) {

            $sheet->setCellValue('A' . $row, $data->no_lot);
            $sheet->setCellValue('B' . $row, $data->nm_partai);
            $sheet->setCellValue('C' . $row, $data->no_box);
            $sheet->setCellValue('D' . $row, $data->pcs_bk);
            $sheet->setCellValue('E' . $row, $data->gr_bk);
            $sheet->setCellValue('F' . $row, $data->bulan_dibayar_format ?? $data->eo_bulan_dibayar_format);
            $sheet->setCellValue('G' . $row, $data->pengawas);
            $sheet->setCellValue('H' . $row, $data->pcs_awal);
            $sheet->setCellValue('I' . $row, $data->gr_awal + $data->gr_eo_awal);
            $sheet->setCellValue('J' . $row, $data->pcs_akhir);
            $sheet->setCellValue('K' . $row, $data->gr_akhir + $data->gr_eo_akhir);
            $sheet->setCellValue('L' . $row, $data->eot);
            $sheet->setCellValue('M' . $row, $data->gr_flx);

            $susut = empty($data->gr_awal) ? 0 : (1 - ($data->gr_flx + $data->gr_akhir) / $data->gr_awal) * 100;
            $susutEo = empty($data->gr_eo_awal) ? 0 : (1 - ($data->gr_eo_akhir / $data->gr_eo_awal)) * 100;

            $sheet->setCellValue('N' . $row, number_format(!empty($data->gr_eo_awal) ? $susutEo : $susut, 0));
            $sheet->setCellValue('O' . $row, $data->ttl_rp + $data->eo_rupiah);
            $sheet->setCellValue('P' . $row, $data->pcs_bk - $data->pcs_awal);
            $sheet->setCellValue('Q' . $row, $data->gr_bk - ($data->gr_awal + $data->gr_eo_awal));
            $this->cekBgSisa($sheet, $data->pcs_bk, $data->pcs_awal, $data->gr_bk, ($data->gr_awal + $data->gr_eo_awal),  $row);
            $sheet->setCellValue('R' . $row, $data->kategori);
            $sheet->setCellValue('S' . $row, $data->tipe);
            $sheet->setCellValue('T' . $row, $data->ket);

            $row++;
        }

        $baris = $row + 1;
        $sheet->getStyle('A2:T' . $baris)->applyFromArray($styleBaris);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Summary Box Cabut Eo $bulanDibayar $tahun";
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

    public function clear(Request $r)
    {
        return view('home.cabut.clear');
    }

    public function clearSave(Request $r)
    {
        try {
            DB::beginTransaction();

            if ($r->password == 'Takemor.') {
                $admin = auth()->user()->name;
                $datas = [];
                $bulan = $r->bulan;
                $tahun = $r->tahun;
                $bulanDibayar = date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun))));

                $cek = DB::table('cabut')->where([['bulan_dibayar', $bulan], ['tahun_dibayar', $tahun]])->first();
                if (!$cek) {
                    return redirect()->route('cabut.clear')->with('error', 'Data Cabut tidak ada')->withInput();
                }
                DB::table('clear_perbox')->where('bulan', $bulanDibayar)->delete();
                $lokasiId = [
                    93 => 'bjm',
                    90 => 'bjm',
                    457 => 'bjm',
                    458 => 'bjm',
                    85 => 'bjm',
                    460 => 'bjm',
                    421 => 'sby',
                    100 => 'sby',
                    101 => 'sby',
                    99 => 'sby',
                    104 => 'mtd',
                ];

                $pengawas = DB::select("SELECT b.id as id_pengawas,b.name FROM bk as a
                JOIN users as b on a.penerima = b.id
                WHERE a.kategori != 'cetak' AND a.selesai = 'T'
                group by b.id");

                foreach ($pengawas as $d) {
                    $lokasi = $lokasiId[$d->id_pengawas] ?? 'tiyah';
                    $cabut = Cabut::queryRekap($d->id_pengawas, $bulan, $tahun);
                    foreach ($cabut as $data) {
                        $susut = empty($data->gr_awal) ? 0 : (1 - ($data->gr_flx + $data->gr_akhir) / $data->gr_awal) * 100;
                        $datas[] = [
                            'no_box' => $data->no_box,
                            'pcs_awal_bk' => $data->pcs_bk,
                            'gr_awal_bk' => $data->gr_bk,
                            'bulan' => $bulanDibayar,
                            'pgws' => $d->name,
                            'pcs_awal_kerja' => $data->pcs_awal,
                            'gr_awal_kerja' => $data->gr_awal,
                            'pcs_akhir_kerja' => $data->pcs_akhir,
                            'gr_akhir_kerja' => $data->gr_akhir,
                            'eot' => $data->eot,
                            'flx' => $data->flx ?? 0,
                            'susut' => number_format($susut, 0),
                            'ttl_rp' => $data->ttl_rp,
                            'denda' => '',
                            'pcs_sisa_bk' => $data->pcs_bk - $data->pcs_awal,
                            'gr_sisa_bk' => $data->gr_bk - $data->gr_awal,
                            'kategori' => $data->kategori,
                            'admin' => $admin,
                            'tanggal' => now(),
                            'lokasi' => $lokasi
                        ];
                    }

                    $eo = Eo::queryRekap($d->id_pengawas, $bulan, $tahun);
                    foreach ($eo as $data) {
                        $susut = empty($data->gr_eo_awal) ? 0 : (1 - ($data->gr_eo_akhir / $data->gr_eo_awal)) * 100;
                        $datas[] = [
                            'no_box' => $data->no_box,
                            'pcs_awal_bk' => 0,
                            'gr_awal_bk' => $data->gr_bk,
                            'bulan' => $bulanDibayar,
                            'pgws' => $d->name,
                            'pcs_awal_kerja' => 0,
                            'gr_awal_kerja' => $data->gr_eo_awal,
                            'pcs_akhir_kerja' => 0,
                            'gr_akhir_kerja' => $data->gr_eo_akhir,
                            'eot' => 0,
                            'flx' => 0,
                            'susut' => number_format($susut, 0),
                            'ttl_rp' => $data->rupiah,
                            'denda' => '',
                            'pcs_sisa_bk' => 0,
                            'gr_sisa_bk' => $data->gr_bk - $data->gr_eo_awal,
                            'kategori' => 'Eo',
                            'admin' => $admin,
                            'tanggal' => now(),
                            'lokasi' => $lokasi
                        ];
                    }
                    $sortir = Sortir::queryRekap($d->id_pengawas, $bulan, $tahun);
                    foreach ($sortir as $data) {
                        $susut = empty($data->gr_awal) ? 0 : (1 - $data->gr_akhir / $data->gr_awal) * 100;

                        $datas[] = [
                            'no_box' => $data->no_box,
                            'pcs_awal_bk' => $data->pcs_bk,
                            'gr_awal_bk' => $data->gr_bk,
                            'bulan' => $bulanDibayar,
                            'pgws' => $data->pengawas,
                            'pcs_awal_kerja' => $data->pcs_awal,
                            'gr_awal_kerja' => $data->gr_awal,
                            'pcs_akhir_kerja' => $data->pcs_akhir,
                            'gr_akhir_kerja' => $data->gr_akhir,
                            'eot' => 0,
                            'flx' => 0,
                            'susut' => number_format($susut, 0),
                            'ttl_rp' => $data->rupiah,
                            'denda' => '',
                            'pcs_sisa_bk' => $data->pcs_bk - $data->pcs_awal,
                            'gr_sisa_bk' => $data->gr_bk - $data->gr_awal,
                            'kategori' => $data->kategori,
                            'admin' => $admin,
                            'tanggal' => now(),
                            'lokasi' => $lokasi
                        ];
                    }
                    $dll = DB::selectOne("SELECT a.bulan_dibayar,a.tgl,b.nama,c.name, SUM(rupiah) AS total_rupiah
                    FROM tb_hariandll as a
                    LEFT JOIN tb_anak as b on a.id_anak = b.id_anak
                    LEFT JOIN users as c on c.id = b.id_pengawas
                    WHERE bulan_dibayar = '$bulan' AND tahun_dibayar = '$tahun' AND a.ditutup = 'T' AND b.id_pengawas = '$d->id_pengawas'
                    GROUP BY b.id_pengawas");

                    $datas[] = [
                        'no_box' => 'dll',
                        'pcs_awal_bk' => 0,
                        'gr_awal_bk' => 0,
                        'bulan' => $bulanDibayar,
                        'pgws' => $d->name,
                        'pcs_awal_kerja' => 0,
                        'gr_awal_kerja' => 0,
                        'pcs_akhir_kerja' => 0,
                        'gr_akhir_kerja' => 0,
                        'eot' => 0,
                        'flx' => 0,
                        'susut' => 0,
                        'ttl_rp' => $dll->total_rupiah ?? 0,
                        'denda' => '',
                        'pcs_sisa_bk' => 0,
                        'gr_sisa_bk' => 0,
                        'kategori' => 'dll',
                        'admin' => $admin,
                        'tanggal' => now(),
                        'lokasi' => $lokasi
                    ];

                    $denda = DB::selectOne("SELECT sum(nominal) as rupiah FROM `tb_denda` as a
                    join tb_anak as b on a.id_anak = b.id_anak
                    WHERE a.bulan_dibayar = '$bulan' AND YEAR(a.tgl) = '$tahun' AND a.admin = '$d->name'
                    GROUP BY a.admin");

                    $datas[] = [
                        'no_box' => 'denda',
                        'pcs_awal_bk' => 0,
                        'gr_awal_bk' => 0,
                        'bulan' => $bulanDibayar,
                        'pgws' => $d->name,
                        'pcs_awal_kerja' => 0,
                        'gr_awal_kerja' => 0,
                        'pcs_akhir_kerja' => 0,
                        'gr_akhir_kerja' => 0,
                        'eot' => 0,
                        'flx' => 0,
                        'susut' => 0,
                        'ttl_rp' => 0,
                        'denda' => $denda->rupiah ?? 0,
                        'pcs_sisa_bk' => 0,
                        'gr_sisa_bk' => 0,
                        'kategori' => 'denda',
                        'admin' => $admin,
                        'tanggal' => now(),
                        'lokasi' => $lokasi
                    ];
                }
                DB::table('clear_perbox')->insert($datas);
                DB::commit();
                $data = [
                    'sum' => DB::select("SELECT lokasi,sum(ttl_rp - denda) as ttl_rp FROM `clear_perbox` WHERE lokasi != 'tiyah' GROUP BY lokasi;"),
                    'sumPgws' => DB::select("SELECT pgws,sum(ttl_rp - denda) as ttl_rp FROM `clear_perbox` WHERE lokasi != 'tiyah' GROUP BY pgws;")
                ];
                return view('home.cabut.clear_nota', $data);
            }
            return redirect()->route('cabut.clear')->with('error', 'Password salah !');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cabut.clear')->with('error', $e->getMessage())->withInput();
        }
    }


    public function summary(Request $r)
    {
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $id_pengawas = auth()->user()->id;

        $summary = Cabut::getRekapLaporanHarian($bulan, $tahun, $id_pengawas);

        $data = [
            'title' => 'Summary Perkaryawan',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'summary' => $summary,
        ];
        return view('home.cabut.summary', $data);
    }
    public function gudang(Request $r)
    {
        $id_user = auth()->user()->id;
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');
        $users = DB::table('users')->where([['posisi_id', '14']])->get();

        $gudang = Cabut::gudang($bulan, $tahun, $id_user);
        $data = [
            'title' => 'Gudang Cabut',
            'bk' => $gudang->bk,
            'cabut' => $gudang->cabut,
            'cabutSelesai' => $gudang->cabutSelesai,
            'eoSelesai' => $gudang->eoSelesai,
            'users' => $users,
            'users2' => DB::table('users')->where('posisi_id', '!=', '1')->get(),
            'id_user' => $id_user,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'posisi' => auth()->user()->posisi_id
        ];
        return view('home.cabut.gudang', $data);
    }

    public function export_gudang(Request $r)
    {
        $id_user = $r->id_user;
        $bulan =  $r->bulan ?? date('m');
        $tahun =  $r->tahun ?? date('Y');

        $bulanDibayar = date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun))));
        $gudang = Cabut::gudang($bulan, $tahun);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Gudang cabut');

        $koloms = [
            'A' => 'box stock',
            'B' => 'pgws',
            'C' => 'no box',
            'D' => 'pcs',
            'E' => 'gr',
            'F' => 'rp/gr',
            'G' => 'total rp',

            'I' => 'box sedang proses',
            'J' => 'pgws',
            'K' => 'no box',
            'L' => 'pcs',
            'M' => 'gr',
            'N' => 'rp/gr',
            'O' => 'total rp',

            'Q' => 'box selesai siap cetak',
            'R' => 'pgws',
            'S' => 'no box',
            'T' => 'pcs',
            'U' => 'gr',
            'V' => 'rp/gr',
            'W' => 'total rp',


        ];
        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }
        $styleBold = [
            'font' => [
                'bold' => true,
            ],
        ];
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('B1:G1')->applyFromArray($styleBold);
        $sheet->getStyle('B1:G1')->applyFromArray($styleBaris);

        $sheet->getStyle('J1:O1')->applyFromArray($styleBold);
        $sheet->getStyle('J1:O1')->applyFromArray($styleBaris);

        $sheet->getStyle('R1:W1')->applyFromArray($styleBold);
        $sheet->getStyle('R1:W1')->applyFromArray($styleBaris);
        // $sheet->getStyle('A1:R1')->applyFromArray($styleBaris);

        $bk = $gudang->bk;
        $cabut = $gudang->cabut;
        $cabutSelesai = $gudang->cabutSelesai;
        $users = DB::table('users')->whereIn('posisi_id', [13, 14])->pluck('name', 'id');

        $no = 2;
        $ttl_pcs = 0;
        $ttl_gr = 0;
        $ttl_rp = 0;
        foreach ($bk as $item) {
            $sheet->setCellValue('B' . $no, $item->penerima);
            $sheet->setCellValue('C' . $no, $item->no_box);
            $sheet->setCellValue('D' . $no, $item->pcs);
            $sheet->setCellValue('E' . $no, $item->gr);
            $sheet->setCellValue('F' . $no, $item->ttl_rp / $item->gr);
            $sheet->setCellValue('G' . $no, $item->ttl_rp);

            $no++;
            $ttl_pcs += $item->pcs;
            $ttl_gr += $item->gr;
            $ttl_rp += $item->ttl_rp;
        }
        $sheet->setCellValue('B' . $no, 'Total');
        $sheet->setCellValue('C' . $no, '');
        $sheet->setCellValue('D' . $no, $ttl_pcs);
        $sheet->setCellValue('E' . $no, $ttl_gr);
        $sheet->setCellValue('F' . $no, '');
        $sheet->setCellValue('G' . $no, $ttl_rp);

        $sheet->getStyle('B2:G' . $no)->applyFromArray($styleBaris);
        $sheet->getStyle('B' . $no . ':G' . $no)->applyFromArray($styleBold);


        $no = 2;
        $ttl_pcs = 0;
        $ttl_gr = 0;
        $ttl_rp = 0;
        foreach ($cabut as $item) {
            $sheet->setCellValue('J' . $no, $item->penerima);
            $sheet->setCellValue('K' . $no, $item->no_box);
            $sheet->setCellValue('L' . $no, $item->pcs);
            $sheet->setCellValue('M' . $no, $item->gr);
            $sheet->setCellValue('N' . $no, $item->ttl_rp / $item->gr);
            $sheet->setCellValue('O' . $no, $item->ttl_rp);

            $no++;
            $ttl_pcs += $item->pcs;
            $ttl_gr += $item->gr;
            $ttl_rp += $item->ttl_rp;
        }
        $sheet->setCellValue('J' . $no, 'Total');
        $sheet->setCellValue('K' . $no, '');
        $sheet->setCellValue('L' . $no, $ttl_pcs);
        $sheet->setCellValue('M' . $no, $ttl_gr);
        $sheet->setCellValue('N' . $no, '');
        $sheet->setCellValue('O' . $no, $ttl_rp);

        $sheet->getStyle('J2:O' . $no)->applyFromArray($styleBaris);
        $sheet->getStyle('J' . $no . ':O' . $no)->applyFromArray($styleBold);



        $no = 2;
        $ttl_pcs = 0;
        $ttl_gr = 0;
        $ttl_rp = 0;
        foreach ($cabutSelesai as $item) {
            $sheet->setCellValue('R' . $no, $item->pengawas);
            $sheet->setCellValue('S' . $no, $item->no_box);
            $sheet->setCellValue('T' . $no, $item->pcs);
            $sheet->setCellValue('U' . $no, $item->gr);
            $sheet->setCellValue('V' . $no, $item->ttl_rp / $item->gr);
            $sheet->setCellValue('W' . $no, $item->ttl_rp);

            $no++;
            $ttl_pcs += $item->pcs;
            $ttl_gr += $item->gr;
            $ttl_rp += $item->ttl_rp;
        }
        $sheet->setCellValue('R' . $no, 'Total');
        $sheet->setCellValue('S' . $no, '');
        $sheet->setCellValue('T' . $no, $ttl_pcs);
        $sheet->setCellValue('U' . $no, $ttl_gr);
        $sheet->setCellValue('V' . $no, '');
        $sheet->setCellValue('W' . $no, $ttl_rp);

        $sheet->getStyle('R2:W' . $no)->applyFromArray($styleBaris);
        $sheet->getStyle('R' . $no . ':W' . $no)->applyFromArray($styleBold);






        $writer = new Xlsx($spreadsheet);
        $fileName = "Gudang Cabut";
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

    public function save_formulir(Request $r)
    {

        $cekBox = DB::selectOne("SELECT no_invoice FROM `formulir_sarang` WHERE kategori = 'cetak' ORDER by no_invoice DESC limit 1;");
        $no_invoice = isset($cekBox->no_invoice) ? $cekBox->no_invoice + 1 : 1001;
        if (!$r->no_box[0] || !$r->id_penerima) {
            return redirect()->route('cabut.gudang')->with('error', 'No Box / Penerima Kosong !');
        }
        $no_box = explode(',', $r->no_box[0]);
        foreach ($no_box as $d) {
            $ambil = DB::selectOne("SELECT a.no_box, sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir
                        FROM(
                            SELECT 
                            sum(a.pcs_akhir) as pcs_akhir, sum(a.gr_akhir) as gr_akhir , a.no_box
                            FROM cabut as a
                            WHERE a.no_box = $d  AND a.selesai = 'Y' GROUP BY a.no_box 
                        
                        UNION ALL

                            SELECT 0 as pcs_akhir,
                            SUM(a.gr_eo_akhir) AS gr_akhir, a.no_box
                                FROM eo AS a
                                WHERE a.no_box = '$d' AND a.selesai = 'Y'
                                GROUP BY a.no_box
                            ) as a

                        group by a.no_box
                        
                        ");

            $pcs = $ambil->pcs_akhir;
            $gr = $ambil->gr_akhir;

            if ($r->grading) {
                $urutan_invoice = DB::selectOne("SELECT max(a.no_invoice) as no_invoice FROM formulir_sarang as a where a.kategori = 'grade'");

                if (empty($urutan_invoice->no_invoice)) {
                    $inv = 1001;
                } else {
                    $inv = $urutan_invoice->no_invoice + 1;
                }

                $data[] = [
                    'no_invoice' => $inv,
                    'no_box' => $d,
                    'id_pemberi' => auth()->user()->id,
                    'id_penerima' => $r->id_penerima,
                    'pcs_awal' => $pcs,
                    'gr_awal' => $gr,
                    'tanggal' => $r->tgl,
                    'kategori' => 'grade',
                ];
            } else {
            }
            $data[] = [
                'no_invoice' => $no_invoice,
                'no_box' => $d,
                'id_pemberi' => auth()->user()->id,
                'id_penerima' => $r->id_penerima,
                'pcs_awal' => $pcs,
                'gr_awal' => $gr,
                'tanggal' => $r->tgl,
                'kategori' => 'cetak',
            ];

            DB::table('cabut')->where('no_box', $d)->update(['formulir' => 'Y']);
        }

        DB::table('formulir_sarang')->insert($data);
        return redirect()->route('cabut.gudang')->with('sukses', 'Data Berhasil');
    }


    public function save_formulir_eo(Request $r)
    {
        $no_box = explode(',', $r->no_box[0]);

        foreach ($no_box as $d) {
            $ambil = DB::selectOne("SELECT 
            SUM(a.gr_eo_akhir) AS gr_akhir, a.no_box
        FROM eo AS a
        WHERE a.no_box = '$d' AND a.selesai = 'Y'
        GROUP BY a.no_box

        UNION ALL

        SELECT  SUM(a.gr_akhir) AS gr_akhir, a.no_box
        FROM cabut AS a
        WHERE a.no_box = '$d' AND a.selesai = 'Y'
        GROUP BY a.no_box;");



            $gr = $ambil->gr_akhir;

            $kategori = $r->grading ? 'grade' : 'sortir';

            $urutan_invoice = DB::selectOne("SELECT max(a.no_invoice) as no_invoice FROM formulir_sarang as a where a.kategori = '$kategori'");

            if (empty($urutan_invoice->no_invoice)) {
                $inv = 1001;
            } else {
                $inv = $urutan_invoice->no_invoice + 1;
            }

            $data[] = [
                'no_invoice' => $inv,
                'no_box' => $ambil->no_box,
                'id_pemberi' => auth()->user()->id,
                'id_penerima' => $r->id_penerima,
                'pcs_awal' => 0,
                'gr_awal' => $gr,
                'tanggal' => $r->tgl,
                'kategori' => $kategori,
            ];
        }

        DB::table('formulir_sarang')->insert($data);
        return redirect()->route('cabut.gudang')->with('sukses', 'Data Berhasil');
    }
}
