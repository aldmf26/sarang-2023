<?php

namespace App\Http\Controllers;

use App\Exports\CabutExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CabutSpecialController extends Controller
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
            'title' => 'Divisi Cabut Spesial',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cabut' => DB::table('cabut_spesial as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where('a.id_pengawas', auth()->user()->id)
                ->whereBetween('a.tgl', [$tgl1, $tgl2])
                ->orderBY('a.id_cabut_spesial', 'DESC')
                ->get(),
        ];
        return view('home.cabut_spesial.index', $data);
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
            // 'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'target' => DB::table('grade_spesial')->get()
        ];
        return view('home.cabut_spesial.create', $data);
    }

    public function getrp_target(Request $r)
    {
        $target = DB::table('grade_spesial')->where('id_grade_spesial', $r->id_target)->first();

        $data = [
            'rupiah' => $target->rupiah,
            'pcs' => $target->pcs,
        ];
        echo json_encode($data);
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'target' => DB::table('grade_spesial')->get(),
            'anak' => $this->getAnak(),
            'count' => $r->count,

        ];
        return view('home.cabut_spesial.tbh_baris', $data);
    }

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->no_box); $i++) {
            // $no_box = $r->no_box[$i];
            // $box = $this->getStokBk($no_box);

            // if ($box->pcs_awal - $box->pcs_cabut - $r->pcs_awal[$i] < 0 || $box->gr_awal - $box->gr_cabut - $r->gr_awal[$i] < 0) {
            //     return redirect()->route('cabut.add')->with('error', 'Total Pcs / Gr Melebihi Ambil Bk');
            // } else {

            DB::table('cabut_spesial')->insert([
                'no_box' => $r->no_box[$i],
                'id_pengawas' => $r->id_pengawas[$i],
                'id_anak' => $r->id_anak[$i],
                'tgl' => $r->tgl_terima[$i],
                'pcs_awal' => $r->pcs_awal[$i],
                'gr_awal' => $r->gr_awal[$i],
                'ttl_rp' => $r->ttl_rp[$i],
                'rp_target' => $r->rp_target[$i],
                'pcs_target' => $r->pcs_target[$i],
            ]);
            // }
        }
        return redirect()->route('cabutSpesial.index')->with('sukses', 'Berhasil tambah Data');
    }

    public function load_modal_akhir(Request $r)
    {
        $detail = DB::table('cabut_spesial as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['a.id_cabut_spesial', $r->id_cabut]])
            ->first();
        $data = [
            'detail' => $detail
        ];
        return view('home.cabut_spesial.load_modal_akhir', $data);
    }
    public function input_akhir(Request $r)
    {
        DB::table('cabut_spesial')->where([['id_anak', $r->id_anak]])->update([
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
            // 'pcs_hcr' => $r->pcs_hcr,
            'eot' => $r->eot,
        ]);

        return redirect()->route('cabutSpesial.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }
    public function selesai_cabut(Request $r)
    {
        DB::table('cabut_spesial')->where('id_cabut_spesial', $r->id_cabut)->update(['selesai' => 'Y']);
        return redirect()->route('cabutSpesial.index')->with('sukses', 'Data telah diselesaikan');
    }
}
