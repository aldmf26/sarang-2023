<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SortirController extends Controller
{
    public function getStokBk($no_box = null)
    {
        $id_user = auth()->user()->id;
        $query = !empty($no_box) ? "selectOne" : 'select';
        $noBoxAda = !empty($no_box) ? "a.no_box = '$no_box' AND" : '';

        return DB::$query("SELECT a.no_box, a.pcs_awal,b.pcs_awal as pcs_cabut,a.gr_awal,b.gr_awal as gr_cabut FROM `cetak` as a
        LEFT JOIN (
            SELECT max(no_box) as no_box,sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal  FROM `sortir` GROUP BY no_box,id_pengawas
        ) as b ON a.no_box = b.no_box WHERE  $noBoxAda a.id_pengawas = '$id_user'");
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
        $tgl1 =  $tgl['tgl1'];
        $tgl2 =  $tgl['tgl2'];
        
        $data = [
            'title' => 'Sortir Divisi',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
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

            DB::table('sortir')->insert([
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

    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('sortir as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['a.id_anak', $r->id_anak], ['a.no_box', $r->no_box]])
            ->first();
        $data = [
            'detail' => $detail
        ];
        return view('home.sortir.load_modal_akhir', $data);
    }

    public function input_akhir(Request $r)
    {
        $getSortir = DB::table('sortir')->where([['id_anak', $r->id_anak], ['no_box', $r->no_box]]);
        $get = $getSortir->first();
        $susut = $r->gr_akhir == 0  ? 0 : (1 - $r->gr_akhir / $get->gr_awal) * 100;

        $kelas = DB::table('tb_kelas_sortir')->where('id_kelas', $get->id_kelas)->first();

        $rupiah = $get->rp_target;
        $denda = 0;
        if ($susut > $kelas->denda_susut) {
            $denda = (number_format($susut) - $kelas->denda_susut) * $kelas->denda;
            $rupiah = $rupiah - $denda;
        }

        $getSortir->update([
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
            'ttl_rp' => $rupiah,
            'denda_sp' => $denda,
        ]);

        return redirect()->route('sortir.index')->with('sukses', 'Data Berhasil Ditambahkan');
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
        DB::table('tb_anak')->where('id_anak', $r->id_anak)->update(
            ['id_pengawas' => empty($r->delete) ? auth()->user()->id : null]
        );
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
    public function selesai_cabut(Request $r)
    {
        DB::table('sortir')->where('id_sortir', $r->id_cabut)->update(['selesai' => empty($r->batal) ? 'Y' : 'T']);
        return redirect()->route('sortir.index')->with('sukses', 'Data telah diselesaikan');
    }
}
