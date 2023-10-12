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
            'title' => 'Divisi Cabut Spesial',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,

        ];
        return view('home.cabut_spesial.index', $data);
    }

    function load_cabut(Request $r)
    {
        $data = [
            'cabut' => DB::table('cabut_spesial as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where('a.id_pengawas', auth()->user()->id)
                ->where('a.penutup', 'T')
                ->orderBY('a.id_cabut_spesial', 'DESC')
                ->get(),
        ];
        return view('home.cabut_spesial.load_cabut', $data);
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
            'target' => DB::table('grade_spesial')->get()
        ];
        return view('home.cabut_spesial.create', $data);
    }

    public function getrp_target(Request $r)
    {
        $target = DB::table('tb_kelas')->where('id_kelas', $r->id_target)->first();

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
        for ($i = 0; $i < count($r->id_absen); $i++) {
            $data = [
                'tgl' => $r->tgl_terima[$i]
            ];
            DB::table('absen')->where('id_absen', $r->id_absen[$i])->update($data);
        }
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
                'id_kelas' => $r->id_target[$i],
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
        // $detail = DB::table('cabut_spesial as a')
        //     ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
        //     ->where([['a.id_cabut_spesial', $r->id_cabut]])
        //     ->first();

        $cabut_spesial = DB::select("SELECT *
        FROM cabut_spesial as a
        left join tb_anak as b on b.id_anak = a.id_anak
        where a.selesai = 'T'
        ");
        $data = [
            'cabut_spesial' => $cabut_spesial,
            'bulan' => DB::table('bulan')->get()
        ];
        return view('home.cabut_spesial.load_modal_akhir', $data);
    }
    public function input_akhir(Request $r)
    {
        DB::table('cabut_spesial')->where([['id_cabut_spesial', $r->id_cabut_spesial]])->update([
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
            'gr_flex' => $r->gr_flex,
            'pcs_hcr' => $r->pcs_hcr,
            'eot' => $r->eot,
            'tgl_terima' => $r->tgl_terima,
            'bulan_dibayar' => $r->bulan_dibayar,
            'ttl_rp' =>  $r->pcs_hcr > 0 ? 0 : $r->ttl_rp
        ]);


        // return redirect()->route('cabutSpesial.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }
    public function selesai_cabut(Request $r)
    {
        // dd($r->id_cabut);
        DB::table('cabut_spesial')->where('id_cabut_spesial', $r->id_cabut)->update(['selesai' => 'Y']);
        // return redirect()->route('cabutSpesial.index')->with('sukses', 'Data telah diselesaikan');
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
            'cabut_spesial' => DB::select("SELECT max(b.name) as pengawas, max(a.tgl) as tgl, a.no_box, 
            SUM(a.pcs_awal) as pcs_awal , sum(a.gr_awal) as gr_awal,
            SUM(a.pcs_akhir) as pcs_akhir, SUM(a.gr_akhir) as gr_akhir, sum(c.pcs_awal) as pcs_bk, sum(c.gr_awal) as gr_bk, sum(a.eot) as eot, sum(a.rp_target) as rupiah
            FROM cabut_spesial as a
            left join users as b on b.id = a.id_pengawas
            left JOIN bk as c on c.no_box = a.no_box 
            WHERE a.tgl BETWEEN '$tgl1' and '$tgl2' $pengawas
            GROUP by a.no_box;
            "),
        ];
        return view('home.cabut_spesial.rekap', $data);
    }

    function load_anak_kerja(Request $r)
    {
        $now =  date('Y-m-d');
        $id_pengawas =  auth()->user()->id;

        $total_anak = DB::selectOne("SELECT count(a.id_anak) as jumlah
        FROM tb_anak as a
        where a.id_pengawas =  $id_pengawas and a.id_anak not in (SELECT a.id_anak FROM absen as a where a.tgl = '$now' and a.ket = 'cabut spesial')
        ");

        $data = [
            'total_anak' => $total_anak->jumlah
        ];
        echo json_encode($data);
    }

    function load_anak_kerja_belum(Request $r)
    {
        $now =  date('Y-m-d');
        $id_pengawas =  auth()->user()->id;

        $anak_spesial = DB::select("SELECT *
        FROM tb_anak as a
        where a.id_pengawas =  $id_pengawas and a.id_anak not in(SELECT a.id_anak FROM absen as a where a.tgl = '$now' and a.ket = 'cabut spesial') ");

        $data = [
            'anak_spesial' => $anak_spesial,
        ];
        return view('home.cabut_spesial.load_anak', $data);
    }

    public function load_detail_cabut(Request $r)
    {
        $detail = DB::table('cabut_spesial as a')
            ->select(
                'a.id_cabut_spesial',
                'a.tgl',
                'a.tgl_terima',
                'a.no_box',
                'b.id_anak',
                'a.ttl_rp',
                'a.rp_target',
                'c.gr as gr_kelas',
                'c.rupiah as rupiah_kelas',
                'c.kelas',
                'b.id_kelas',
                'c.rp_bonus',
                'a.selesai',
                'b.nama',
                'a.pcs_awal',
                'a.gr_awal',
                'a.gr_flex',
                'a.pcs_akhir',
                'a.gr_akhir',
                'a.pcs_hcr',
                'a.eot',
            )
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->join('tb_kelas as c', 'a.id_kelas', 'c.id_kelas')
            ->where([['a.id_cabut_spesial', $r->id_cabut]])
            ->first();
        $data = [
            'detail' => $detail
        ];
        return view('home.cabut_spesial.load_modal_detail', $data);
    }

    function load_ambil_cbt(Request $r)
    {
        $now =  date('Y-m-d');
        $id_pengawas =  auth()->user()->id;

        $anak_spesial = DB::select("SELECT * 
        FROM absen as a
        left join tb_anak as b on b.id_anak = a.id_anak
        where a.tgl = '$now' and a.id_pengawas = $id_pengawas and a.ket = 'cabut spesial' and a.id_anak not in(SELECT c.id_anak FROM cabut_spesial as c where c.tgl = '$now')
        ");

        $data = [
            'anak_spesial' => $anak_spesial,
            'boxBk' => $this->getStokBk(),
            'anak' => $this->getAnak(),
            'target' => DB::table('tb_kelas')->where([['id_kategori',2],['nonaktif', 'T'],['jenis', '!=', 2]])->get()
        ];
        return view('home.cabut_spesial.gram_awal', $data);
    }

    function save_absen(Request $r)
    {
        for ($x = 0; $x < count($r->id_anak); $x++) {
            $data = [
                'id_anak' => $r->id_anak[$x],
                'tgl' => date('Y-m-d'),
                'id_pengawas' =>  auth()->user()->id,
                'ket' => 'cabut spesial'
            ];
            DB::table('absen')->insert($data);
        }
    }

    function delete_absen(Request $r)
    {

        DB::table('absen')->where('id_absen', $r->id_absen)->delete();
    }
    function get_box(Request $r)
    {
        $id_pengawas =  auth()->user()->id;
        $box = DB::selectOne("SELECT *
        FROM bk as a where a.no_box =  '$r->no_box' and a.penerima = '$id_pengawas'");

        $cabut_spesial = DB::selectOne("SELECT sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal FROM cabut_spesial as a where a.no_box = '$r->no_box' and a.id_pengawas = '$id_pengawas' group by a.no_box");

        $pcs_awal = empty($cabut_spesial->pcs_awal) ? 0 : $cabut_spesial->pcs_awal;
        $gr_awal = empty($cabut_spesial->gr_awal) ? 0 : $cabut_spesial->gr_awal;
        $data = [
            'pcs' => $box->pcs_awal - $pcs_awal,
            'gram' => $box->gr_awal - $gr_awal
        ];
        echo json_encode($data);
    }

    function load_row(Request $r)
    {
        $cabut_spesial = DB::selectOne("SELECT *
        FROM cabut_spesial as a
        left join tb_anak as b on b.id_anak = a.id_anak
        where a.id_cabut_spesial = $r->id
        ");
        $data = [
            'detail' => $cabut_spesial,
            'bulan' => DB::table('bulan')->get()
        ];
        return view('home.cabut_spesial.load_row', $data);
    }

    function ditutup(Request $r)
    {
        foreach ($r->datas as $d) {
            DB::table('cabut_spesial')->where('id_cabut_spesial', $d)->update(['penutup' => 'Y']);
        }
    }

    function history(Request $r)
    {
        if (empty($r->tgl1)) {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-t');
        } else {
            $tgl1 = $r->tgl1;
            $tgl2 = $r->tgl2;
        }
        $data = [
            'cabut' => DB::table('cabut_spesial as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where('a.id_pengawas', auth()->user()->id)
                ->orderBY('a.id_cabut_spesial', 'DESC')
                ->get(),
        ];
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
                'c.batas_susut',
                'c.bonus_susut',
                'c.denda_hcr',
                'c.eot as eot_rp',
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
}
