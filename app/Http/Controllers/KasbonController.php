<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasbonController extends Controller
{
    protected $tgl1, $tgl2, $id_akun;
    public function __construct(Request $r)
    {
        if (empty($r->period)) {
            $this->tgl1 = date('Y-m-01');
            $this->tgl2 = date('Y-m-t');
        } elseif ($r->period == 'daily') {
            $this->tgl1 = date('Y-m-d');
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'weekly') {
            $this->tgl1 = date('Y-m-d', strtotime("-6 days"));
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'mounthly') {
            $bulan = $r->bulan;
            $tahun = $r->tahun;
            $tglawal = "$tahun" . "-" . "$bulan" . "-" . "01";
            $tglakhir = "$tahun" . "-" . "$bulan" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tglawal));
            $this->tgl2 = date('Y-m-t', strtotime($tglakhir));
        } elseif ($r->period == 'costume') {
            $this->tgl1 = $r->tgl1;
            $this->tgl2 = $r->tgl2;
        } elseif ($r->period == 'years') {
            $tahun = $r->tahunfilter;
            $tgl_awal = "$tahun" . "-" . "01" . "-" . "01";
            $tgl_akhir = "$tahun" . "-" . "12" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tgl_awal));
            $this->tgl2 = date('Y-m-t', strtotime($tgl_akhir));
        }
    }

    public function index(Request $r)
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;


        $kasbon = DB::table('kasbon as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where('a.id_pengawas', auth()->user()->id)
            ->where('bulan_dibayar', date('m'))
            ->whereBetween('a.tgl', [$tgl1, $tgl2])
            ->orderBy('a.id', 'DESC')
            ->get();
        $ttlNominal = 0;
        foreach ($kasbon as $d) {
            $ttlNominal += $d->nominal;
        }
        $data = [
            'title' => 'Data Kasbon',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'anak' => DB::table('tb_anak')->where('id_pengawas', auth()->user()->id)->get(),
            'kasbon' => $kasbon,
            'ttlNominal' => $ttlNominal,
        ];
        return view('home.kasbon.index', $data);
    }

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->id_anak); $i++) {
            DB::table('kasbon')->insert([
                'id_anak' => $r->id_anak[$i],
                'id_pengawas' => auth()->user()->id,
                'bulan_dibayar' => $r->bulan_dibayar,
                'tahun_dibayar' => date('Y'),
                'tgl' => $r->tgl,
                'nominal' => $r->nominal,

            ]);
        }
        return redirect()->route('kasbon.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function detail(Request $r)
    {
        $detail = DB::table('kasbon')->where('id', $r->id)->first();
        if (empty($detail)) {
            abort(404);
        }
        $data = [
            'detail' => $detail,
            'anak' => DB::table('tb_anak')->where('id_pengawas', auth()->user()->id)->orderBy('id_kelas', 'DESC')->get(),
        ];
        return view("home.kasbon.detail", $data);
    }

    public function update(Request $r)
    {
        DB::table('kasbon')->where('id', $r->id)->update([
            'id_anak' => $r->id_anak,
            'tgl' => $r->tgl,
            'nominal' => $r->nominal,
            'bulan_dibayar' => $r->bulan_dibayar,


        ]);
        return redirect()->route('kasbon.index')->with('sukses', 'Data Berhasil diupdate');
    }

    public function delete(Request $r)
    {
        DB::table('kasbon')->where('id', $r->id_denda)->delete();
        return redirect()->route('kasbon.index')->with('sukses', 'Data Berhasil dihapus');
    }

    public function print(Request $r)
    {

        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;

        $data = [
            'title' => 'Data Denda',
            'id_departemen' => $r->id_departemen,
            'tgl1' => $r->tgl1,
            'tgl2' => $r->tgl2,
            'kasbon' => DB::select("SELECT b.nama, sum(a.nominal) as nominal FROM tb_denda a
                LEFT JOIN tb_anak b on a.id_anak = b.id_anak
                WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
                GROUP BY a.id_anak"),
        ];

        return view('home.denda.print', $data);
    }
}
