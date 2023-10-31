<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DendaController extends Controller
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
        $denda = DB::table('tb_denda as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->orderBy('a.id_denda', 'DESC')
            ->get();
        $ttlNominal = 0;
        foreach ($denda as $d) {
            $ttlNominal += $d->nominal;
        }
        $data = [
            'title' => 'Data Denda',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'ttlNominal' => $ttlNominal,
            'anak' => DB::table('tb_anak')->get(),
            'denda' => $denda,
        ];
        return view('home.denda.index', $data);
    }

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->id_anak); $i++) {
            DB::table('tb_denda')->insert([
                'id_anak' => $r->id_anak[$i],
                'tgl' => $r->tgl,
                'nominal' => $r->nominal,
                'ket' => $r->ket,
                'admin' => auth()->user()->name
            ]);
        }
        return redirect()->route('denda.index')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function detail($id)
    {
        $detail = DB::table('tb_denda')->where('id_denda', $id)->first();
        if (empty($detail)) {
            abort(404);
        }
        $data = [
            'detail' => $detail,
            'anak' => DB::table('tb_anak')->get(),
        ];
        return view("home.denda.detail", $data);
    }

    public function update(Request $r)
    {
        DB::table('tb_denda')->where('id_denda', $r->id)->update([
            'id_anak' => $r->id_anak,
            'tgl' => $r->tgl,
            'nominal' => $r->nominal,
            'ket' => $r->ket,
            'admin' => auth()->user()->name
        ]);
        return redirect()->route('denda.index')->with('sukses', 'Data Berhasil diupdate');
    }

    public function delete(Request $r)
    {
        DB::table('tb_denda')->where('id_denda', $r->id_denda)->delete();
        return redirect()->route('denda.index')->with('sukses', 'Data Berhasil dihapus');
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
