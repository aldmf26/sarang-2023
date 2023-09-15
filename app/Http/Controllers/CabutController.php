<?php

namespace App\Http\Controllers;

use App\Exports\CabutExport;
use App\Models\User;
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
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $data = [
            'title' => 'Divisi Cabut',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cabut' => DB::table('cabut as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where('a.id_pengawas', auth()->user()->id)
                ->whereBetween('a.tgl_terima', [$tgl1, $tgl2])
                ->orderBY('a.id_cabut', 'DESC')
                ->get(),
        ];
        return view('home.cabut.index', $data);
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

    public function add()
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
        ];
        return view('home.cabut.create', $data);
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

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->no_box); $i++) {
            $no_box = $r->no_box[$i];
            $box = $this->getStokBk($no_box);

            if ($box->pcs_awal - $box->pcs_cabut - $r->pcs_awal[$i] < 0 || $box->gr_awal - $box->gr_cabut - $r->gr_awal[$i] < 0) {
                return redirect()->route('cabut.add')->with('error', 'Total Pcs / Gr Melebihi Ambil Bk');
            } else {

                DB::table('cabut')->insert([
                    'no_box' => $r->no_box[$i],
                    'id_pengawas' => $r->id_pengawas[$i],
                    'id_anak' => $r->id_anak[$i],
                    'tgl_terima' => $r->tgl_terima[$i],
                    'pcs_awal' => $r->pcs_awal[$i],
                    'gr_awal' => $r->gr_awal[$i],
                    'rupiah' => $r->rupiah[$i],
                ]);
            }
        }
        return redirect()->route('cabut.index')->with('sukses', 'Berhasil tambah Data');
    }

    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('cabut as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['a.id_anak', $r->id_anak], ['a.no_box', $r->no_box]])
            ->first();
        $data = [
            'detail' => $detail
        ];
        return view('home.cabut.load_modal_akhir', $data);
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
        ]);

        return redirect()->route('cabut.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function load_detail_cabut(Request $r)
    {
        $detail = DB::table('cabut as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
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

    public function export(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.cabut.export';
        $tbl = DB::table('cabut as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where('a.id_pengawas', auth()->user()->id)
            ->whereBetween('a.tgl_terima', [$tgl1, $tgl2])
            ->orderBY('a.id_cabut', 'DESC')
            ->get();

        return Excel::download(new CabutExport($tbl, $view), 'Export CABUT.xlsx');
    }

    public function rekap(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];
        $id = auth()->user()->id;

        $data = [
            'title' => 'Divisi Cabut',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cabut' => DB::select("SELECT max(b.name) as pengawas, max(a.tgl_terima) as tgl, a.no_box, 
            SUM(a.pcs_awal) as pcs_awal , sum(a.gr_awal) as gr_awal,
            SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, sum(c.pcs_awal) as pcs_bk, sum(c.gr_awal) as gr_bk,
            sum(a.pcs_hcr) as pcs_hcr, sum(a.eot) as eot, sum(a.rupiah) as rupiah, sum(a.gr_flx) as gr_flx
            FROM cabut as a
            left join users as b on b.id = a.id_pengawas
            left JOIN bk as c on c.no_box = a.no_box 
            WHERE a.tgl_terima BETWEEN '$tgl1' and '$tgl2' and a.id_pengawas = $id
            GROUP by a.no_box;
            "),
        ];
        return view('home.cabut.rekap', $data);
    }
}
