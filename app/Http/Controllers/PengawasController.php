<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengawasController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Pengawas',
            'user' => User::with('posisi')->where('posisi_id', 13)->get(),
            'posisi' => Posisi::all()
        ];
        return view('data_master.pengawas.index', $data);
    }

    public function anak()
    {
        $data = [
            'title' => 'Data Anak',
            'user' => DB::table('tb_anak as a')
                ->leftJoin('users as b', 'a.id_pengawas', 'b.id')
                ->leftJoin('uang_makan as d', 'a.id_uang_makan', 'd.id_uang_makan')
                ->where(function ($query) {
                    $query->where('b.posisi_id', '!=', 1)
                        ->orWhereNull('a.id_pengawas');
                })
                ->leftJoin('invoice_karyawan as c', 'a.id_anak', 'c.id_anak')
                ->where('b.id', auth()->user()->id)
                ->orderBy('a.id_anak', 'DESC')
                ->selectRaw("a.*, b.name as name, d.nominal as nominal, c.no_invoice, c.tgl_lunas, c.pembayar")
                ->get(),

            'pengawas' => User::with('posisi')->whereIn('posisi_id', [13, 14])->get(),
            'uang_makan' => DB::table('uang_makan')->where('aktiv', 'Y')->get(),
            'divisi' => DB::table('divisis')->get(),
            'cth_wawancara' => DB::table('cth_wawancara')->where('id_cth_wawancara', '1')->first(),
            'cth2' => DB::table('cth_penialain_karyawan')->where('id', '1')->first(),


        ];
        return view('data_master.pengawas.anak', $data);
    }

    public function create_anak(Request $r)
    {
        $id_anak = DB::table('tb_anak')->insertGetId([
            'tgl_masuk' => $r->tgl_masuk,
            'nama' => $r->nama,
            'pembawa' => $r->pembawa,
            'periode' => $r->periode,
            'komisi' => $r->komisi,
            'tgl_dibayar' => $r->tgl_dibayar,
            'id_kelas' => $r->kelas,
            'id_pengawas' => $r->id_pengawas,
        ]);

        $data = [
            'id_anak' => $id_anak,
            'nama' => $r->nama_lengkap,
            'nik' => $r->nik,
            'tgl_lahir' => $r->tgl_lahir,
            'jenis_kelamin' => $r->jenis_kelamin,
            'id_divisi' => $r->id_divisi,
            'posisi2' => $r->posisi,
            'kesimpulan' => $r->kesimpulan,
            'keputusan' => 'dilanjutkan',
            'tgl_masuk' => $r->tgl_masuk,
        ];
        $id_karyawan = DB::table('hasil_wawancara')->insertGetId($data);

        $data = [
            'id_anak' => $id_karyawan,
            'periode' => $r->periode,
            'pendidikan_standar' => $r->pendidikan_standar,
            'pendidikan_hasil' => $r->pendidikan_hasil,
            'pelatihan_standar' => $r->pelatihan_standar,
            'pelatihan_hasil' => $r->pelatihan_hasil,
            'pengalaman_standar' => $r->pengalaman_standar,
            'pengalaman_hasil' => $r->pengalaman_hasil,
            'keterampilan_standar' => $r->keterampilan_standar,
            'keterampilan_hasil' => $r->keterampilan_hasil,
            'kompetensi_inti_standar' => $r->kompetensi_inti_standar,
            'kompetensi_inti_hasil' => $r->kompetensi_inti_hasil
        ];
        DB::table('penilaian_karyawan')->insert($data);

        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function create_invoice(Request $r)
    {
        $submit = $r->submit;
        $id_anak = explode(',', $r->id_anak);

        if ($submit == 'berhenti') {
            DB::table('tb_anak')->whereIn('id_anak', $id_anak)->update(['berhenti' => 'Y']);
            return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
        }

        if ($submit == 'bayar') {

            $no_invoice = DB::table('invoice_karyawan')->max('no_invoice');
            $no_invoice = !$no_invoice ? 1001 : $no_invoice + 1;
            $anak = DB::table('tb_anak as a')
                ->leftJoin('users as b', 'a.id_pengawas', 'b.id')
                ->where(function ($query) {
                    $query->where('b.posisi_id', '!=', 1)
                        ->orWhereNull('a.id_pengawas');
                })
                ->whereIn('a.id_anak', $id_anak)
                ->orderBy('a.id_anak', 'DESC')
                ->get()->toArray();

            $cekSudahSave = DB::table('invoice_karyawan')->where('no_invoice', $no_invoice)->get();

            $data = [
                'title' => 'Tambah Invoice',
                'no_invoice' => $no_invoice,
                'anak' => $anak,
                'cekSudahSave' => $cekSudahSave,
            ];

            return view('data_master.pengawas.create_invoice', $data);
        }
    }

    public function invoice(Request $r)
    {
        $no_invoice = $r->no_invoice;
        $anak = DB::table('tb_anak as a')
            ->leftJoin('users as b', 'a.id_pengawas', 'b.id')
            ->where(function ($query) {
                $query->where('b.posisi_id', '!=', 1)
                    ->orWhereNull('a.id_pengawas');
            })
            ->leftJoin('invoice_karyawan as c', 'a.id_anak', 'c.id_anak')
            ->where('c.no_invoice', $no_invoice)
            ->orderBy('a.id_anak', 'DESC')
            ->get()->toArray();

        $cekSudahSave = DB::table('invoice_karyawan')->where('no_invoice', $no_invoice)->get();

        $data = [
            'title' => 'Tambah Invoice',
            'no_invoice' => $no_invoice,
            'anak' => $anak,
            'cekSudahSave' => $cekSudahSave,
        ];

        return view('data_master.pengawas.create_invoice', $data);
    }

    public function save_invoice(Request $r)
    {
        foreach ($r->id_anak as $id) {
            $data[] = [
                'tgl_lunas' => $r->tgl_lunas,
                'pembayar' => $r->pembayar,
                'no_invoice' => $r->no_invoice,
                'id_anak' => $id,
                'admin' => auth()->user()->name
            ];
        }

        DB::table('invoice_karyawan')->insert($data);
        return redirect()->route('pengawas.invoice', ['no_invoice' => $r->no_invoice])->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function anak_detail($id)
    {
        $detail = DB::table('tb_anak')->where('id_anak', $id)->first();
        if (empty($detail)) {
            abort(404);
        }
        $data = [
            'detail' => $detail,
            'pengawas' => User::with('posisi')->whereIn('posisi_id', [13, 14])->get(),
            'uang_makan' => DB::table('uang_makan')->where('aktiv', 'Y')->get()

        ];
        return view("data_master.pengawas.anak_detail", $data);
    }

    public function update_anak(Request $r)
    {
        DB::table('tb_anak')->where('id_anak', $r->id)->update([
            'tgl_masuk' => $r->tgl_masuk,
            'nama' => $r->nama,
            'id_kelas' => $r->kelas,
            'id_pengawas' => $r->id_pengawas,
            'id_uang_makan' => $r->id_uang_makan,
            'pembawa' => $r->pembawa,
            'periode' => $r->periode,
            'komisi' => $r->komisi,
            'tgl_dibayar' => $r->tgl_dibayar,
        ]);

        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil ditambahkan');
    }
    public function destroy_anak($id)
    {
        DB::table('tb_anak')->where('id_anak', $id)->delete();
        return redirect()->route('pengawas.anak')->with('sukses', 'Data Berhasil dihapus');
    }
}
