<?php

namespace App\Http\Controllers;

use App\Exports\SortirExport;
use App\Exports\SortirRekapExport;
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
            SELECT max(no_box) as no_box,sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal  FROM `sortir` GROUP BY no_box,id_pengawas
        ) as b ON a.no_box = b.no_box WHERE  $noBoxAda a.penerima = '$id_user' AND a.kategori = 'sortir3'");
    }

    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->join('tb_kelas_sortir as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
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
            'tgl' => date('Y-m-d'),
        ]);
    }

    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('sortir as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['selesai', 'T'],['no_box', '!=', 9999]])
            ->get();
        $data = [
            'detail' => $detail
        ];
        return view('home.sortir.load_modal_akhir', $data);
    }

    public function input_akhir(Request $r)
    {
        $id_anak = $r->id_anak;
        $no_box = $r->no_box;
        $gr_akhir = $r->gr_akhir;
        $pcs_akhir = $r->pcs_akhir;
        $id_sortir = $r->id_sortir;
        $bulan = $r->bulan;

        $getSortir = DB::table('sortir')->where([['id_anak', $id_anak], ['no_box', $no_box]]);
        $get = $getSortir->first();
        $susut = $gr_akhir == 0  ? 0 : (1 - $gr_akhir / $get->gr_awal) * 100;

        $kelas = DB::table('tb_kelas_sortir')->where('id_kelas', $get->id_kelas)->first();

        $rupiah = $get->rp_target;
        $denda = 0;
        if ($susut > $kelas->denda_susut) {
            $denda = (number_format($susut) - $kelas->denda_susut) * $kelas->denda;
            $rupiah = $rupiah - $denda;
        }

        $getSortir->update([
            'pcs_akhir' => $pcs_akhir,
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
                ->where('a.id_pengawas', auth()->user()->id)
                ->where([['a.no_box', '!=', '9999'], ['a.penutup', 'T']])
                ->orderBy('id_sortir', 'DESC')
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
            ->where([['a.id_pengawas', auth()->user()->id],['no_box', '!=', 9999]])
            ->orderBy('id_sortir', 'DESC')
            ->get();

        return Excel::download(new SortirExport($tbl, $view), 'Export SORTIR.xlsx');
    }

    public function queryRekap($tgl1, $tgl2)
    {
        $id = auth()->user()->id;
        $posisi = auth()->user()->posisi_id;
        $pengawas = $posisi == 13 ? "AND a.id_pengawas = '$id'" : '';

        return DB::select("SELECT
        MAX(a.no_box) as no_box,
        MAX(a.tgl) as tgl,
        sum(a.pcs_awal) as pcs_awal,
        sum(a.gr_awal) as gr_awal,
        sum(a.gr_akhir) as gr_akhir,
        sum(a.ttl_rp) as ttl_rp,
        b.name,
        c.pcs_akhir as cabut_pcs_akhir,
        c.gr_akhir as cabut_gr_akhir
        FROM sortir as a
        LEFT JOIN users as b ON a.id_pengawas = b.id
        LEFT JOIN (
            SELECT no_box, SUM(pcs_akhir) as pcs_akhir, SUM(gr_akhir) as  gr_akhir
            FROM cetak
            GROUP BY no_box
        ) as c ON a.no_box = c.no_box
        WHERE a.selesai = 'Y' AND a.tgl BETWEEN '$tgl1' AND '$tgl2' $pengawas
        GROUP BY a.pcs_awal, a.gr_awal, b.name, c.pcs_akhir, c.gr_akhir;
        ");
    }

    public function rekap(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 =  $tgl['tgl1'];
        $tgl2 =  $tgl['tgl2'];
        $datas = $this->queryRekap($tgl1, $tgl2);

        $data = [
            'title' => 'Rekap Summary Sortir',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'datas' => $datas,
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
