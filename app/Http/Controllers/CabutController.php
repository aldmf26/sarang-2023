<?php

namespace App\Http\Controllers;

use App\Exports\CabutExport;
use App\Exports\CabutRekapExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CabutController extends Controller
{
    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }
    public function getAnakTambah($cabut = null)
    {

        $id_user = auth()->user()->id;
        $whereQ = empty($cabut) ? "AND c.no_box = 9999" : '';
        return DB::select("SELECT c.id_cabut,a.id_anak,a.nama,b.kelas FROM `tb_anak` as a
        LEFT JOIN tb_kelas as b ON a.id_kelas = b.id_kelas
        LEFT JOIN cabut as c ON a.id_anak = c.id_anak AND DATE(c.tgl_terima) = CURDATE()
        WHERE a.id_pengawas = '$id_user' $whereQ AND a.id_anak $cabut IN (
            SELECT id_anak
            FROM cabut
            WHERE DATE(tgl_terima) = CURDATE()
        ) AND a.id_anak NOT IN (
            SELECT id_anak
            FROM absen
            WHERE DATE(tgl) = CURDATE() AND ket = 'cabut sisa'
        )");
    }
    public function queryRekap($tgl1, $tgl2)
    {
        $id = auth()->user()->id;
        $posisi = auth()->user()->posisi_id;
        $pengawas = $posisi == 13 ? "AND a.id_pengawas = '$id'" : '';

        return DB::select("SELECT max(b.name) as pengawas, max(a.tgl_terima) as tgl, a.no_box, 
        SUM(a.pcs_awal) as pcs_awal , sum(a.gr_awal) as gr_awal,
        SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, c.pcs_awal as pcs_bk, c.gr_awal as gr_bk,
        sum(a.pcs_hcr) as pcs_hcr, sum(a.eot) as eot, sum(a.ttl_rp) as rupiah, sum(a.gr_flx) as gr_flx
        FROM cabut as a
        left join users as b on b.id = a.id_pengawas
        left JOIN bk as c on c.no_box = a.no_box 
        WHERE a.tgl_terima BETWEEN '$tgl1' and '$tgl2' $pengawas
        GROUP by a.no_box;");
    }
    public function queryRekapGroup($tgl1, $tgl2)
    {
        $cabutGroup = DB::select("SELECT 
                        max(b.name) as pengawas, 
                        e.ttl_box,
                        a.id_pengawas,
                        c.pcs_awal,
                        c.gr_awal,
                        c.pcs_hcr,
                        c.eot,
                        c.gr_flx,
                        c.gr_akhir,
                        c.pcs_akhir,
                        d.gr_bk,
                        d.pcs_bk,
                        c.ttl_rp,
                        c.rupiah
                        FROM cabut as a 
                        left join users as b on b.id = a.id_pengawas 
                        LEFT JOIN (
                            SELECT 
                                id_pengawas,no_box, 
                                sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal, 
                                sum(gr_akhir) as gr_akhir, sum(pcs_akhir) as pcs_akhir,
                                sum(pcs_hcr) as pcs_hcr,
                                sum(eot) as eot,
                                sum(gr_flx) as gr_flx,
                                SUM(rupiah) as rupiah,
                                SUM(ttl_rp) as ttl_rp
                                FROM cabut GROUP BY id_pengawas
                        ) as c ON c.id_pengawas = a.id_pengawas
                        LEFT JOIN (
                            SELECT penerima,no_box,sum(pcs_awal) as pcs_bk, sum(gr_awal) as gr_bk FROM `bk` GROUP BY penerima
                        ) as d ON d.penerima = a.id_pengawas
                        LEFT JOIN (
                            SELECT id_pengawas, COUNT(DISTINCT no_box) as ttl_box
                            FROM cabut
                            GROUP BY id_pengawas
                        ) as e ON e.id_pengawas = a.id_pengawas
                        WHERE a.tgl_terima BETWEEN '$tgl1' AND '$tgl2'
                        GROUP BY a.id_pengawas");
        return $cabutGroup;
    }
    public function getStokBk($no_box = null)
    {
        $id_user = auth()->user()->id;
        $query = !empty($no_box) ? "selectOne" : 'select';
        $noBoxAda = !empty($no_box) ? "a.no_box = '$no_box' AND" : '';

        return DB::$query("SELECT a.no_box, a.pcs_awal,b.pcs_awal as pcs_cabut,a.gr_awal,b.gr_awal as gr_cabut FROM `bk` as a
        LEFT JOIN (
            SELECT max(no_box) as no_box,sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal  FROM `cabut` GROUP BY no_box,id_pengawas
        ) as b ON a.no_box = b.no_box WHERE  $noBoxAda a.penerima = '$id_user'");
    }
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
        $anakBelum = count($this->getAnakTambah('NOT'));
        return response()->json(['anakBelum' => $anakBelum]);
    }
    public function load_halaman(Request $r)
    {
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $id = auth()->user()->id;

        $cabut = DB::table('cabut as a')
            ->select(
                'b.id_anak',
                'a.no_box',
                'a.rupiah',
                'c.gr as gr_kelas',
                'c.rupiah as rupiah_kelas',
                'b.id_kelas',
                'c.rp_bonus',
                'a.tgl_serah',
                'a.selesai',
                'a.tgl_terima',
                'a.id_cabut',
                'a.selesai',
                'b.nama',
                'a.pcs_awal',
                'a.gr_awal',
                'a.gr_flx',
                'a.pcs_akhir',
                'a.pcs_hcr',
                'a.gr_akhir',
                'a.gr_awal',
                'a.eot',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['a.no_box', '!=', '9999'], ['a.penutup', 'T']])
            ->orderBY('a.selesai', 'ASC');

        if (auth()->user()->posisi_id != 1) {
            $cabut->where('a.id_pengawas', $id);
        }

        $query = $cabut->get();
        $data = [
            'title' => 'Divisi Cabut',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cabut' => $query,
        ];
        return view('home.cabut.load_halaman', $data);
    }
    public function load_tambah_anak(Request $r)
    {
        $data = [
            'anak' => $this->getAnakTambah('NOT')
        ];
        return view('home.cabut.load_tambah_anak', $data);
    }
    public function createTambahAnakCabut(Request $r)
    {
        $tgl = date('Y-m-d');
        $id_pengawas = auth()->user()->id;
        // DB::table('cabut')->where([['tgl_terima', $tgl], ['id_pengawas', $id_pengawas], ['no_box', '9999']])->delete();
        // DB::table('absen')->where([['tgl', $tgl], ['ket', 'cabut']])->delete();
        foreach ($r->all()['rows'] as $d) {
            DB::table('absen')->insert([
                'tgl' => $tgl,
                'id_pengawas' => $id_pengawas,
                'id_anak' => $d,
                'ket' => $r->tipe == 'cbt' ? 'cabut' : 'cabut sisa'
            ]);
            if ($r->tipe == 'cbt') {
                DB::table('cabut')->insert([
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
        DB::table('absen')->where([['id_anak', $r->id_anak], ['tgl', date('Y-m-d')], ['ket', 'Cabut']])->delete();
        return 'Berhasil hapus baris';
    }
    public function load_tambah_cabut(Request $r)
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => $this->getStokBk(),
            'getAnak' => $this->getAnakTambah()
        ];
        return view('home.cabut.load_tambah_cabut', $data);
    }
    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('cabut as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['a.id_anak', $r->id_anak], ['a.no_box', $r->no_box]])
            ->first();

        $datas = DB::table('cabut as a')
            ->select(
                'b.id_anak',
                'a.no_box',
                'a.id_cabut',
                'a.rupiah',
                'c.gr as gr_kelas',
                'c.rupiah as rupiah_kelas',
                'b.id_kelas',
                'c.rp_bonus',
                'a.tgl_serah',
                'b.nama',
                'a.pcs_awal',
                'a.gr_awal',
                'a.gr_flx',
                'a.pcs_akhir',
                'a.pcs_hcr',
                'a.gr_akhir',
                'a.gr_awal',
                'a.ttl_rp',
                'a.eot',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['a.selesai', 'T'], ['a.id_pengawas', auth()->user()->id]])
            ->orderBy('a.id_cabut', 'DESC')
            ->get();
        $data = [
            'detail' => $detail,
            'datas' => $datas
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
        $anak = $this->getAnak();
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
        $anakNoPengawas = $this->getAnak(1);

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
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'count' => $r->count,

        ];
        return view('home.cabut.tbh_baris', $data);
    }

    public function get_box_sinta(Request $r)
    {
        $bk = $this->getStokBk($r->no_box);

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
            'gr' => $bk->gr,
            'rupiah' => $bk->rupiah,
            'lokasi' => $bk->lokasi,
        ];
        return json_encode($data);
    }

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->no_box); $i++) {
            $no_box = $r->no_box[$i];
            $box = $this->getStokBk($no_box);

            // if ($box->pcs_awal - $box->pcs_cabut - $r->pcs_awal[$i] < 0 || $box->gr_awal - $box->gr_cabut - $r->gr_awal[$i] < 0) {
            //     // return redirect()->route('cabut.add')->with('error', 'Total Pcs / Gr Melebihi Ambil Bk');
            // } else {
            // }
            DB::table('absen')->where([['id_anak', $r->id_anak[$i]], ['tgl', date('Y-m-d')]])->update([
                'tgl' => $r->tgl_terima[$i]
            ]);
            DB::table('cabut')->where([['id_pengawas', $r->id_pengawas[$i]], ['id_anak', $r->id_anak[$i]], ['tgl_terima', date('Y-m-d')], ['no_box', '9999']])->update([
                'no_box' => $r->no_box[$i] ?? '9999',
                'pcs_awal' => $r->pcs_awal[$i],
                'gr_awal' => $r->gr_awal[$i],
                'rupiah' => $r->rupiah[$i],
                'id_kelas' => $r->kelas_tipe[$i],
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
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
        ];
        return view('home.cabut.create', $data);
    }

    public function input_akhir(Request $r)
    {
        DB::table('cabut')->where([['id_anak', $r->id_anak], ['no_box', $r->no_box]])->update([
            'pcs_akhir' => $r->pcs_akhir,
            'tgl_serah' => $r->tgl_serah,
            'gr_akhir' => $r->gr_akhir,
            'gr_flx' => $r->gr_flx,
            'pcs_hcr' => $r->pcs_hcr,
            'eot' => $r->eot,
            'ttl_rp' => $r->ttl_rp,
        ]);
    }

    public function load_detail_cabut(Request $r)
    {
        $detail = DB::table('cabut as a')
            ->select(
                'b.id_anak',
                'a.no_box',
                'a.rupiah',
                'c.gr as gr_kelas',
                'c.rupiah as rupiah_kelas',
                'b.id_kelas',
                'c.rp_bonus',
                'a.tgl_serah',
                'a.tgl_terima',
                'a.id_cabut',
                'a.selesai',
                'b.nama',
                'a.pcs_awal',
                'a.gr_awal',
                'a.gr_flx',
                'a.pcs_akhir',
                'a.pcs_hcr',
                'a.gr_akhir',
                'a.gr_awal',
                'a.eot',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['a.id_cabut', $r->id_cabut]])
            ->first();
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
        foreach($r->datas as $d) {
            DB::table('cabut')->where('id_cabut', $d)->update(['penutup'=> 'Y']);
        }
    }

    public function export(Request $r)
    {

        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.cabut.export';
        $tbl = DB::table('cabut as a')
            ->select(
                'b.id_anak',
                'a.no_box',
                'a.rupiah',
                'c.gr as gr_kelas',
                'c.rupiah as rupiah_kelas',
                'b.id_kelas',
                'c.rp_bonus',
                'a.tgl_serah',
                'a.tgl_terima',
                'a.id_cabut',
                'a.selesai',
                'b.nama',
                'a.pcs_awal',
                'a.gr_awal',
                'a.gr_flx',
                'a.pcs_akhir',
                'a.pcs_hcr',
                'a.gr_akhir',
                'a.gr_awal',
                'a.eot',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where('no_box', '!=', '9999')
            ->orderBY('a.id_cabut', 'DESC')
            ->get();
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
        $ttlRp = 0;
        $cabutGroup = $this->queryRekapGroup($tgl1, $tgl2);

        foreach ($cabutGroup as $d) {
            $ttlPcsBk += $d->pcs_bk;
            $ttlGrBk += $d->gr_bk;
            $ttlPcsAwal += $d->pcs_awal;
            $ttlGrAwal += $d->gr_awal;
            $ttlRp += $d->ttl_rp;
        }

        $data = [
            'title' => 'Divisi Cabut',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'ttlPcsBk' => $ttlPcsBk,
            'ttlGrBk' => $ttlGrBk,
            'ttlPcsAwal' => $ttlPcsAwal,
            'ttlGrAwal' => $ttlGrAwal,
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
        $tbl = $this->queryRekap($tgl1, $tgl2);

        return Excel::download(new CabutRekapExport($tbl, $view), 'Export REKAP CABUT.xlsx');
    }
}
