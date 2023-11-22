<?php

namespace App\Http\Controllers;

use App\Exports\SortirExport;
use App\Exports\SortirRekapExport;
use App\Models\Sortir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SortirController extends Controller
{
    public function getStokBk($no_box = null)
    {
        $id_user = auth()->user()->id;
        $query = !empty($no_box) ? "selectOne" : 'select';
        $noBoxAda = !empty($no_box) ? "a.no_box = '$no_box' AND" : '';
        return DB::$query("SELECT a.no_box, a.pcs_awal,b.pcs_awal as pcs_cabut,a.gr_awal,b.gr_awal as gr_cabut FROM `bk` as a
        LEFT JOIN (
            SELECT max(no_box) as no_box,id_pengawas,sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal  FROM `sortir` GROUP BY no_box,id_pengawas
        ) as b ON a.no_box = b.no_box AND b.id_pengawas = a.penerima WHERE  $noBoxAda a.penerima = '$id_user' AND a.kategori = 'sortir'");
    }

    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas_sortir as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }
    public function updateAnakBelum()
    {
        $anakBelum = count(DB::table('sortir')->where([['no_box', 9999], ['id_pengawas', auth()->user()->id]])->get());
        return response()->json(['anakBelum' => $anakBelum]);
    }
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];


        $data = [
            'title' => 'Sortir Divisi',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'cabut' => DB::table('sortir as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->join('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
                ->where('a.id_pengawas', auth()->user()->id)
                ->whereBetween('a.tgl', [$tgl1, $tgl2])
                ->orderBy('id_sortir', 'DESC')
                ->get()
        ];

        return view('home.sortir.index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Divisi Sortir',
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
        ];
        return view('home.sortir.create', $data);
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'count' => $r->count,
        ];
        return view('home.sortir.tbh_baris', $data);
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

    public function create(Request $r)
    {

        for ($i = 0; $i < count($r->rupiah); $i++) {
            $rupiah = str()->remove('.', $r->rupiah[$i]);

            DB::table('sortir')->where('id_sortir', $r->id_sortir[$i])->update([
                'no_box' => $r->no_box,
                'tgl' => $r->tgl_terima[$i],
                'id_pengawas' => $r->id_pengawas,
                'id_anak' => $r->id_anak[$i],
                'id_kelas' => $r->tipe[$i],
                'pcuc' => $r->pcuc[$i],
                'pcs_awal' => $r->pcs_awal[$i],
                'gr_awal' => $r->gr_awal[$i],
                'rp_target' => $rupiah,
            ]);
        }

        return redirect()->route('sortir.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function cancel(Request $r)
    {
        DB::table('sortir')->where('id_sortir', $r->id_sortir)->update([
            'no_box' => 9999,
            'pcs_akhir' => '',
            'gr_akhir' => '',
            'tgl' => date('Y-m-d'),
        ]);
    }

    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('sortir as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['selesai', 'T'], ['no_box', '!=', 9999], ['a.id_pengawas', auth()->user()->id]])
            ->get();
        $data = [
            'detail' => $detail
        ];
        return view('home.sortir.load_modal_akhir', $data);
    }

    public function load_detail_sortir(Request $r)
    {
        $detail = DB::selectOne("SELECT 
        a.pcs_awal,a.gr_awal,a.pcs_akhir,
        a.gr_akhir,a.tgl,a.no_box,b.gr,
        b.kelas as nm_kelas,b.rupiah as rp_kelas,
        c.nama,c.id_kelas
        FROM sortir as a 
        JOIN tb_kelas_sortir as b ON a.id_kelas = b.id_kelas
        JOIN tb_anak as c ON c.id_anak = a.id_anak
        WHERE a.id_sortir = '$r->id_sortir'");
        $data = [
            'detail' => $detail
        ];
        return view('home.sortir.load_modal_detail', $data);
    }

    public function input_akhir(Request $r)
    {
        $id_anak = $r->id_anak;
        $no_box = $r->no_box;
        $gr_akhir = $r->gr_akhir;
        $pcs_akhir = $r->pcs_akhir;
        $pcus = $r->pcus;
        $id_sortir = $r->id_sortir;
        $bulan = $r->bulan;

        $getSortir = DB::table('sortir')->where('id_sortir', $id_sortir);
        $get = $getSortir->first();
        $susut = $gr_akhir == 0  ? 0 : (1 - $gr_akhir / $get->gr_awal) * 100;

        $kelas = DB::table('tb_kelas_sortir')->where('id_kelas', $get->id_kelas)->first();

        $rupiah = $get->rp_target;
        $denda = 0;
        if ($susut > $kelas->denda_susut) {
            $denda = $susut > $kelas->bts_denda_sst ? $kelas->batas_denda_rp : (number_format($susut) - $kelas->denda_susut) * $kelas->denda;
            $rupiah = $rupiah - $denda;
        }

        $getSortir->update([
            'pcs_akhir' => $pcs_akhir,
            'pcus' => $pcus,
            'gr_akhir' => $gr_akhir,
            'bulan' => $bulan,
            'ttl_rp' => $rupiah,
            'denda_sp' => $denda,
        ]);

        // return redirect()->route('sortir.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function load_halaman(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];


        $data = [
            'title' => 'Sortir Divisi',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,

            'cabut' => DB::table('sortir as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->join('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
                ->where('b.id_pengawas', auth()->user()->id)
                ->where([['a.no_box', '!=', '9999'], ['a.penutup', 'T']])
                ->orderBY('a.selesai', 'ASC')
                ->get()
        ];

        return view('home.sortir.load_halaman', $data);
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
                                <th>Tgl Masuk</th>
                                <th>Aksi</th>
                            </tr>";
        foreach ($anak as $d) {
            echo "
                                <tr>
                                    <td>" . ucwords($d->nama) . "</td>
                                    <input type='hidden' value='" . $d->id_anak . "' name='id_anak[]' class='form-control'>
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
                    'tgl_masuk' => $r->tgl_masuk[$i],
                ]
            );
        }
        return redirect()->route('cabut.index')->with('sukses', 'Data Berhasil ditambahkan');
    }
    public function selesai_sortir(Request $r)
    {
        DB::table('sortir')->where('id_sortir', $r->id_sortir)->update(['selesai' => 'Y']);
    }

    public function ditutup(Request $r)
    {
        $data = $r->tipe == 'tutup' ? ['penutup' => 'Y'] : ['selesai' => 'T'];
        foreach ($r->datas as $d) {
            DB::table('sortir')->where('id_sortir', $d)->update($data);
        }
    }

    public function load_tambah_sortir()
    {
        $data = [
            'boxBk' => $this->getStokBk(),
            'datas' => DB::table('sortir as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where('a.no_box', 9999)
                ->get()
        ];
        return view('home.sortir.load_tambah_sortir', $data);
    }

    public function hapusKerjaSortir(Request $r)
    {
        DB::table('sortir')->where('id_sortir', $r->id_sortir)->delete();
        return 'berhasil';
    }

    public function load_tambah_anak()
    {
        $data = [
            'anak' => $this->getAnak()
        ];
        return view('home.sortir.load_tambah_anak', $data);
    }

    public function createTambahAnakSortir(Request $r)
    {
        $tgl = date('Y-m-d');
        $id_pengawas = auth()->user()->id;
        foreach ($r->all()['rows'] as $d) {

            DB::table('sortir')->insertGetId([
                'no_box' => 9999,
                'id_pengawas' => $id_pengawas,
                'id_anak' => $d,
                'tgl' => $tgl
            ]);
        }
        return 'Berhasil tambah anak';
    }

    public function export(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.sortir.export';
        $tbl = DB::table('sortir as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas_sortir as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['a.id_pengawas', auth()->user()->id], ['no_box', '!=', 9999]])
            ->orderBy('id_sortir', 'DESC')
            ->get();

        return Excel::download(new SortirExport($tbl, $view), 'Export SORTIR.xlsx');
    }

    public function queryRekap($tgl1, $tgl2)
    {
        $id = auth()->user()->id;
        $posisi = auth()->user()->posisi_id;
        $pengawas = $posisi == 13 ? "AND a.id_pengawas = '$id'" : '';

        return DB::select("SELECT max(b.name) as pengawas, max(a.tgl) as tgl, a.no_box, 
        SUM(a.pcs_awal) as pcs_awal , sum(a.gr_awal) as gr_awal,
        SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, c.pcs_bk, c.gr_bk,
         sum(a.rp_target) as rupiah,sum(a.ttl_rp) as ttl_rp,sum((1 - a.gr_akhir / a.gr_awal) * 100) as susut
        FROM sortir as a
        left join users as b on b.id = a.id_pengawas
        LEFT JOIN (
            SELECT no_box,penerima, sum(pcs_awal) as pcs_bk, sum(gr_awal) as gr_bk FROM bk GROUP BY no_box,penerima
        ) as c on c.no_box = a.no_box and c.penerima = a.id_pengawas
        WHERE  a.no_box != 9999 AND a.penutup = 'T'
        GROUP by a.no_box,a.id_pengawas
        ");
    }

    public function rekap(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 =  $tgl['tgl1'];
        $tgl2 =  $tgl['tgl2'];
        $datas = $this->queryRekap($tgl1, $tgl2);

        $ttlPcsBk = 0;
        $ttlGrBk = 0;
        $ttlPcsAwal = 0;
        $ttlGrAwal = 0;
        $ttlPcsAkhir = 0;
        $ttlGrAkhir = 0;
        $ttlRp = 0;
        $sortirGroup = Sortir::queryRekapGroup($tgl1, $tgl2);

        foreach ($sortirGroup as $d) {
            $ttlPcsBk += $d->pcs_bk;
            $ttlGrBk += $d->gr_bk;
            $ttlPcsAwal += $d->pcs_awal;
            $ttlGrAwal += $d->gr_awal;
            $ttlPcsAkhir += $d->pcs_akhir;
            $ttlGrAkhir += $d->gr_akhir;
            $ttlRp += $d->rp_target;
        }

        $data = [
            'title' => 'Rekap Summary Sortir',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'datas' => $datas,
            'ttlPcsBk' => $ttlPcsBk,
            'ttlGrBk' => $ttlGrBk,
            'ttlPcsAwal' => $ttlPcsAwal,
            'ttlGrAwal' => $ttlGrAwal,
            'ttlPcsAkhir' => $ttlPcsAkhir,
            'ttlGrAkhir' => $ttlGrAkhir,
            'ttlRp' => $ttlRp,
            'sortirGroup' => $sortirGroup
        ];
        return view('home.sortir.rekap', $data);
    }

    public function export_rekap(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.sortir.export_rekap';
        $tbl = $this->queryRekap($tgl1, $tgl2);
        return Excel::download(new SortirRekapExport($tbl, $view), 'Export REKAP SORTIR.xlsx');
    }
}
