<?php

namespace App\Http\Controllers;

use App\Exports\CabutExport;
use App\Exports\CabutRekapExport;
use App\Models\Cabut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
        $anakBelum = count(DB::table('cabut')->where([['no_box', 9999],['id_pengawas', auth()->user()->id]])->get());
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
        $get = DB::table('tb_kelas as a')->join('paket_cabut as b', 'a.id_paket', 'b.id_paket')->where([[$kolom, $jenis], ['id_kategori', '!=', 3]])->where('a.nonaktif', 'T')->get();
        echo "
                <option value=''>Pilih</option>
            ";
        foreach ($get as $d) {
            $jenis = $d->jenis == 1 ? "$d->pcs pcs" : "$d->gr gr";
            echo "
                <option value='" . $d->id_kelas . "'>$d->paket $d->kelas ~ $jenis </option>
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

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->no_box); $i++) {
            $no_box = $r->no_box[$i];
            $box = Cabut::getStokBk($no_box);

            DB::table('cabut')->where('id_cabut', $r->id_cabut[$i])->update([
                'no_box' => $r->no_box[$i] ?? '9999',
                'pcs_awal' => $r->pcs_awal[$i],
                'gr_awal' => $r->gr_awal[$i],
                'rupiah' => $r->rupiah[$i],
                'id_kelas' => $r->id_paket[$i],
                'tgl_terima' => $r->tgl_terima[$i],
            ]);
        }
        // return redirect()->route('cabut.index')->with('sukses', 'Berhasil tambah Data');
        return json_encode([
            'pesan' => "Berhasil tambah data cabut"
        ]);
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
        DB::table('cabut')->where('id_cabut', $r->id_cabut)->update([
            'pcs_akhir' => $r->pcs_akhir,
            'tgl_serah' => $r->tgl_serah,
            'gr_akhir' => $r->gr_akhir,
            'gr_flx' => $r->gr_flx,
            'pcs_hcr' => $r->pcs_hcr,
            'bulan_dibayar' => $r->bulan,
            'eot' => $r->eot,
            'ttl_rp' => $r->ttl_rp,
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

        DB::table('cabut')->where('id_cabut', $r->id_cabut)->update(['selesai' => 'Y']);
        return redirect()->route('cabut.index')->with('sukses', 'Data telah diselesaikan');
    }

    public function ditutup(Request $r)
    {

        $data = $r->tipe == 'tutup' ? ['penutup' => 'Y'] : ['selesai' => 'T'];
        foreach ($r->datas as $d) {
            DB::table('cabut')->where('id_cabut', $d)->update($data);
        }
    }

    public function export(Request $r)
    {

        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.cabut.export';
        $tbl = Cabut::getQueryExport();
        return Excel::download(new CabutExport($tbl, $view), 'Export CABUT.xlsx');
    }

    public function rekap(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $ttlPcsBk = 0;
        $ttlGrBk = 0;
        $ttlPcsAwal = 0;
        $ttlGrAwal = 0;
        $ttlPcsAkhir = 0;
        $ttlGrAkhir = 0;
        $ttlFlx = 0;
        $ttlEot = 0;
        $ttlRp = 0;
        $cabutGroup = Cabut::queryRekapGroup($tgl1, $tgl2);

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
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
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
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.cabut.export_rekap';
        $tbl = Cabut::queryRekap($tgl1, $tgl2);

        return Excel::download(new CabutRekapExport($tbl, $view), 'Export REKAP CABUT.xlsx');
    }

    public function export_global(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $bulan =  date('m', strtotime($tgl1));
        $tahun =  date('Y', strtotime($tgl1));
        $view = 'home.cabut.export_rekap';
        $tbl = DB::select("SELECT b.name as pgws,
        absen.ttl as hariMasuk,
        a.nama as nm_anak, 
        a.id_kelas as kelas,
        cabut.pcs_awal,
        cabut.gr_awal,
        cabut.pcs_akhir,
        cabut.gr_akhir,
        cabut.eot,
        cabut.gr_flx,
        cabut.ttl_rp
        FROM tb_anak as a
        JOIN users as b on a.id_pengawas = b.id
        LEFT JOIN (
            SELECT *, count(*) as ttl FROM absen AS a 
            WHERE MONTH(a.tgl) = '$bulan' AND YEAR(a.tgl) = '$tahun' group BY a.id_anak
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
        WHERE b.id = 90;");

        return Excel::download(new CabutRekapExport($tbl, $view), 'Export REKAP CABUT.xlsx');
    }
}
