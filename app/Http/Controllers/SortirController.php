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
            SELECT max(no_box) as no_box,id_pengawas,sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal  FROM `sortir` where penutup = 'T'  GROUP BY no_box,id_pengawas
        ) as b ON a.no_box = b.no_box AND b.id_pengawas = a.penerima WHERE  $noBoxAda a.penerima = '$id_user' AND a.kategori LIKE '%sortir%' AND a.selesai = 'T'");
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
    public function history(Request $r)
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
                ->where([['a.no_box', '!=', '9999'], ['a.penutup', 'Y']])
                ->orderBY('a.selesai', 'ASC')
                ->get()
        ];

        return view('home.sortir.history', $data);
    }
    public function ambil_box_bk(Request $r)
    {
        $idPengwas = auth()->user()->id;
        DB::table('bk')->where('kategori', 'sortir')->whereIn('no_box', $r->no_box)->update([
            'penerima' => $idPengwas
        ]);
        DB::table('pengiriman_list_gradingbj')->whereIn('no_box', $r->no_box)->update([
            'pengawas' => $idPengwas
        ]);

        return redirect()->route('sortir.index')->with('sukses', 'Box berhasil diambil');
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
        $ttlPcs = array_sum($r->pcs_awal);
        $ttlGr = array_sum($r->gr_awal);
        for ($i = 0; $i < count($r->rupiah); $i++) {
            $nobox = $r->no_box[$i];
            $admin = auth()->user()->id;
            $cekStok = DB::selectOne("SELECT 
            sum(a.pcs_awal) - sum(b.pcs) as pcs, 
            sum(a.gr_awal) - sum(b.gr) as gr 
            FROM `bk`  as a
            JOIN (
                SELECT no_box,id_pengawas,sum(pcs_awal) as pcs, sum(gr_awal) as gr FROM `sortir` GROUP BY no_box,id_pengawas
            ) as b on a.no_box = b.no_box AND a.penerima = b.id_pengawas
            WHERE a.no_box = '$nobox' AND a.kategori LIKE '%sortir%' AND a.penerima= '$admin';");
            // if ($ttlPcs <= $cekStok->pcs && $ttlGr <= $cekStok->gr) {
                $rupiah = str()->remove('.', $r->rupiah[$i]);
                $id_sortir = $r->id_sortir[$i];
                $data = [
                    'no_box' => $r->no_box[$i],
                    'tgl' => $r->tgl_terima[$i],
                    'id_pengawas' =>$admin,
                    'id_anak' => $r->id_anak[$i],
                    'id_kelas' => $r->tipe[$i],
                    'pcuc' => $r->pcuc[$i],
                    'pcs_awal' => $r->pcs_awal[$i],
                    'gr_awal' => $r->gr_awal[$i],
                    'rp_target' => $rupiah,
                    'tgl_input' => date('Y-m-d')
                ];
                if ($id_sortir == 9999) {
                    DB::table('sortir')->insert($data);
                } else {
                    DB::table('sortir')->where('id_sortir', $id_sortir)->update($data);
                }
            // } else {
            //     return 'Stok pcs / gr melebihi Bk';
            // }
        }

        return 'berhasil';


        // return redirect()->route('sortir.index')->with('sukses', 'Data Berhasil ditambahkan');
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
            ->select(
                'a.id_anak',
                'a.no_box',
                'a.id_sortir',
                'a.rp_target',
                'a.ttl_rp',
                'a.tgl',
                'a.pcs_awal',
                'a.pcs_akhir',
                'a.gr_awal',
                'a.gr_akhir',
                'a.pcus',
                'a.bulan',
                'b.id_kelas',
                'b.nama',
                'c.kelas',
                'c.denda_susut',
                'c.bts_denda_sst',
                'c.batas_denda_rp',
                'c.denda_susut',
                'c.denda'
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas_sortir as c', 'c.id_kelas', 'a.id_kelas')
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
        $tgl = $r->tgl;
        $gr_akhir = $r->gr_akhir;
        $pcs_akhir = $r->pcs_akhir;
        $pcus = $r->pcus;
        $id_sortir = $r->id_sortir;
        $bulan = $r->bulan;
        if ($gr_akhir == 0) {
            return [
                'tipe' => 'error',
                'pesan' => 'Gr Akhir kosong'
            ];
        }


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
            'tgl' => $tgl,
            'denda_sp' => $denda,
        ]);
        return [
            'tipe' => 'sukses',
            'pesan' => 'Berhasil Input Akhir'
        ];
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
                ->where([['a.no_box', 9999], ['a.id_pengawas', auth()->user()->id]])
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
                'tgl' => $tgl,
                'tgl_input' => date('Y-m-d')
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
            ->where([['a.id_pengawas', auth()->user()->id], ['a.no_box', '!=', 9999]])
            ->orderBy('id_sortir', 'DESC')
            ->get();

        return Excel::download(new SortirExport($tbl, $view), 'Export SORTIR.xlsx');
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
        $ttlRp = 0;
        $sortirGroup = Sortir::queryRekapGroup($bulan, $tahun);

        foreach ($sortirGroup as $d) {
            $ttlPcsBk += $d->pcs_bk;
            $ttlGrBk += $d->gr_bk;
            $ttlPcsAwal += $d->pcs_awal;
            $ttlGrAwal += $d->gr_awal;
            $ttlPcsAkhir += $d->pcs_akhir;
            $ttlGrAkhir += $d->gr_akhir;
            $ttlRp += $d->ttl_rp;
        }

        $data = [
            'title' => 'Rekap Summary Sortir',
            'bulan' => $bulan,
            'tahun' => $tahun,
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
        $bulan =  $r->bulan;
        $tahun =  $r->tahun;
        $view = 'home.sortir.export_rekap';
        $tbl = Sortir::queryRekap($bulan, $tahun);
        return Excel::download(new SortirRekapExport($tbl, $view), 'Export REKAP SORTIR.xlsx');
    }
}
