<?php

namespace App\Http\Controllers;

use App\Exports\EoExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EoController extends Controller
{
    public function getStokBk($no_box = null)
    {
        $id_user = auth()->user()->id;
        $query = !empty($no_box) ? "selectOne" : 'select';
        $noBoxAda = !empty($no_box) ? "a.no_box = '$no_box' AND" : '';

        return DB::$query("SELECT a.no_box, a.pcs_awal,a.gr_awal FROM `bk` as a
        WHERE $noBoxAda a.no_box NOT IN (select no_box FROM cabut) AND a.no_box NOT IN (select no_box FROM cabut_spesial) AND a.penerima = '$id_user'");
    }

    public function getAnak($id = null)
    {
        return DB::table('tb_anak as a')
            ->where('id_pengawas', empty($id) ? auth()->user()->id : null)
            ->get();
    }

    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $data = [
            'title' => 'Data EO',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'eo' => DB::table('eo as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->join('tb_kelas_eo as c', 'a.id_kelas', 'c.id_kelas')
                ->whereBetween('a.tgl_input', [$tgl1, $tgl2])
                ->orderBy('a.id_eo', 'DESC')->get(),
            'nobox' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'kelas' => DB::table('tb_kelas_eo')->get()
        ];
        return view('home.eo.index', $data);
    }
    public function getAnakTambah($cabut = null)
    {

        $id_user = auth()->user()->id;
        $whereQ = empty($cabut) ? "AND c.no_box = 9999" : '';
        return DB::select("SELECT c.id_eo,a.id_anak,a.nama,b.kelas FROM `tb_anak` as a
        LEFT JOIN tb_kelas as b ON a.id_kelas = b.id_kelas
        LEFT JOIN eo as c ON a.id_anak = c.id_anak AND DATE(c.tgl_ambil) = CURDATE()
        WHERE a.id_pengawas = '$id_user' $whereQ AND a.id_anak $cabut IN (
            SELECT id_anak
            FROM eo
            WHERE DATE(tgl_ambil) = CURDATE()
        ) AND a.id_anak NOT IN (
            SELECT id_anak
            FROM absen
            WHERE DATE(tgl) = CURDATE() AND ket = 'eo sisa'
        )");
    }

    public function load_tambah_anak(Request $r)
    {
        $data = [
            'anak' => $this->getAnakTambah('NOT')
        ];
        return view('home.eo.load_tambah_anak', $data);
    }

    public function updateAnakBelum()
    {
        $anakBelum = count($this->getAnakTambah('NOT'));
        return response()->json(['anakBelum' => $anakBelum]);
    }
    public function createTambahAnakCabut(Request $r)
    {
        $tgl = date('Y-m-d');
        $id_pengawas = auth()->user()->id;
        // DB::table('cabut')->where([['tgl_terima', $tgl], ['id_pengawas', $id_pengawas], ['no_box', '9999']])->delete();
        // DB::table('absen')->where([['tgl', $tgl], ['ket', 'cabut']])->delete();
        foreach ($r->all()['rows'] as $d) {
            // DB::table('absen')->insert([
            //     'tgl' => $tgl,
            //     'id_pengawas' => $id_pengawas,
            //     'id_anak' => $d,
            //     'ket' => $r->tipe == 'eo' ? 'eo' : 'eo sisa'
            // ]);
            if ($r->tipe == 'eo') {
                DB::table('eo')->insert([
                    'no_box' => 9999,
                    'id_pengawas' => $id_pengawas,
                    'id_anak' => $d,
                    'tgl_ambil' => $tgl
                ]);
            }
        }
        return 'Berhasil tambah anak';
    }

    public function load_tambah_cabut(Request $r)
    {
        $data = [
            'title' => 'Tambah Divisi Eo',
            'nobox' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'getAnak' => $this->getAnakTambah(),
            'kelas' => DB::table('tb_kelas')->where([['nonaktif', 'T'], ['id_kategori', 3]])->get()
        ];

        return view('home.eo.load_tambah_cabut', $data);
    }
    public function get_box_sinta(Request $r)
    {
        $bk = $this->getStokBk($r->no_box);

        $data = [
            'pcs_awal' => $bk->pcs_awal,
            'gr_awal' => $bk->gr_awal,
        ];
        return json_encode($data);
    }

    // 

    public function tbh_baris(Request $r)
    {
        $data = [
            'nobox' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'kelas' => DB::table('tb_kelas_eo')->get(),
            'count' => $r->count,
        ];
        return view('home.eo.tbh_baris', $data);
    }

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->no_box); $i++) {
            $no_box = $r->no_box[$i];

            // if ($box->pcs_awal - $box->pcs_cabut - $r->pcs_awal[$i] < 0 || $box->gr_awal - $box->gr_cabut - $r->gr_awal[$i] < 0) {
            //     // return redirect()->route('cabut.add')->with('error', 'Total Pcs / Gr Melebihi Ambil Bk');
            // } else {
            // }
            DB::table('absen')->where([['id_anak', $r->id_anak[$i]], ['tgl', date('Y-m-d')]])->update([
                'tgl' => $r->tgl_ambil[$i]
            ]);
            DB::table('eo')->where('id_eo', $r->id_eo[$i])->update([
                'no_box' => $r->no_box[$i] ?? '9999',
                'gr_eo_awal' => $r->gr_eo_awal[$i],
                'id_kelas' => $r->id_kelas[$i],
                'tgl_ambil' => $r->tgl_ambil[$i],
                'tgl_input' => date('Y-m-d'),
            ]);
        }
        return json_encode([
            'pesan' => "Berhasil tambah data cabut"
        ]);
    }
    public function load_halaman(Request $r)
    {
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $id = auth()->user()->id;

        $cabut = DB::table('eo as a')
            ->select(
                'a.id_anak',
                'a.id_eo',
                'a.no_box',
                'a.ttl_rp',
                'a.id_pengawas',
                'a.id_kelas',
                'a.tgl_ambil',
                'a.tgl_serah',
                'a.tgl_input',
                'a.gr_eo_awal',
                'a.gr_eo_akhir',
                'a.selesai',
                'a.penutup',
                'a.bulan_dibayar',
                'b.nama',
                'c.kelas',
                'c.rupiah',
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
        return view('home.eo.load_halaman', $data);
    }
    public function hapusCabutRow(Request $r)
    {
        DB::table('eo')->where('id_eo', $r->id_cabut)->delete();
        DB::table('absen')->where([['id_anak', $r->id_anak], ['tgl', date('Y-m-d')], ['ket', 'eo']])->delete();
        return 'Berhasil hapus baris';
    }

    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('cabut as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['a.id_anak', $r->id_anak], ['a.no_box', $r->no_box]])
            ->first();

        $datas = DB::table('eo as a')
            ->select(
                'a.id_anak',
                'a.id_eo',
                'a.no_box',
                'a.ttl_rp',
                'a.bulan_dibayar as bulan',
                'a.id_pengawas',
                'a.id_kelas',
                'a.tgl_ambil',
                'a.tgl_serah',
                'a.tgl_input',
                'a.gr_eo_awal',
                'a.gr_eo_akhir',
                'a.selesai',
                'a.penutup',
                'b.nama',
                'c.kelas',
                'c.rupiah',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['a.selesai', 'T'], ['a.id_pengawas', auth()->user()->id]])
            ->orderBy('a.id_eo', 'DESC')
            ->get();
        $data = [
            'detail' => $detail,
            'datas' => $datas
        ];
        return view('home.eo.load_modal_akhir', $data);
    }
    public function cancel(Request $r)
    {
        DB::table('eo')->where('id_eo', $r->id_cabut)->update([
            'no_box' => 9999,
        ]);
    }

    public function input_akhir(Request $r)
    {
        DB::table('eo')->where('id_eo', $r->id_eo)->update([
            'gr_eo_akhir' => $r->gr_eo_akhir,
            'tgl_serah' => $r->tgl_serah,
            'ttl_rp' => $r->ttl_rp,
            'bulan_dibayar' => $r->bulan,
        ]);
    }

    public function selesai(Request $r)
    {
        DB::table('eo')->where('id_eo', $r->id_cabut)->update(['selesai' => empty($r->batal) ? 'Y' : 'T']);
        return redirect()->route('eo.index')->with('sukses', 'Data telah diselesaikan');
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
        return redirect()->route('eo.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function export(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.eo.export';
        $tbl = DB::table('eo as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->join('users as d', 'd.id', 'a.id_pengawas')
            ->whereBetween('a.tgl_input', [$tgl1, $tgl2])
            ->orderBy('a.id_eo', 'DESC')->get();

        return Excel::download(new EoExport($tbl, $view), 'Export EO.xlsx');
    }

    public function rekap(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $id = auth()->user()->id;
        $posisi = auth()->user()->posisi_id;
        $pengawas = $posisi == 13 ? "AND a.id_pengawas = '$id'" : '';

        $data = [
            'title' => 'Divisi Cabut',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'eo' => DB::select("SELECT max(b.name) as pengawas, max(a.tgl_ambil) as tgl, a.no_box, sum(a.gr_eo_awal) as gr_awal , sum(a.gr_eo_akhir) as gr_akhir, sum(a.ttl_rp) as rupiah, sum(c.gr
            FROM eo as a
            left join users as b on b.id = a.id_pengawas
            left JOIN bk as c on c.no_box = a.no_box 
            WHERE a.tgl_ambil BETWEEN '$tgl1' and '$tgl2' $pengawas
            GROUP by a.no_box;
            "),
        ];
        return view('home.eo.rekap', $data);
    }
}
