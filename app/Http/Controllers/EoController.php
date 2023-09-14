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
         ");
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
        for ($i = 0; $i < count($r->id_anak); $i++) {
            DB::table('eo')->insert([
                'tgl_input' => date('Y-m-d'),
                'id_pengawas' => auth()->user()->id,
                'id_anak' => $r->id_anak[$i],
                'no_box' => $r->no_box,
                'id_kelas' => $r->id_kelas[$i],
                'tgl_ambil' => $r->tgl_ambil[$i],
                'gr_eo_awal' => $r->gr_eo_awal[$i],
            ]);
        }
        return redirect()->route('eo.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('eo as a')
            ->select('a.id_kelas as id_kelas', 'b.nama', 'a.*')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['a.id_anak', $r->id_anak], ['a.no_box', $r->no_box]])
            ->first();
        $data = [
            'detail' => $detail
        ];
        return view('home.eo.load_modal_akhir', $data);
    }

    public function input_akhir(Request $r)
    {
        $getKelas = DB::table('tb_kelas_eo')->where('id_kelas', $r->id_kelas)->first();
        $ttl_rp = $getKelas->rupiah * $r->gr_eo_akhir;
        DB::table('eo')->where('id_eo', $r->id_eo)->update([
            'gr_eo_akhir' => $r->gr_eo_akhir,
            'tgl_serah' => $r->tgl_serah,
            'ttl_rp' => $ttl_rp,
        ]);

        return redirect()->route('eo.index')->with('sukses', 'Data Berhasil Ditambahkan');
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
        foreach($idArray as $n) {
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
            ->join('tb_kelas_eo as c', 'a.id_kelas', 'c.id_kelas')
            ->join('users as d', 'd.id', 'a.id_pengawas')
            ->whereBetween('a.tgl_input', [$tgl1, $tgl2])
            ->orderBy('a.id_eo', 'DESC')->get();

        return Excel::download(new EoExport($tbl, $view), 'Export EO.xlsx');
    }
}
