<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPegawaiController extends Controller
{
    public function index()
    {
        // Ambil semua data pegawai
        $dataPegawai = DB::table('hasil_wawancara as a')
            ->leftJoin('divisis as b', 'a.id_divisi', 'b.id')
            ->leftJoin('tb_anak as c', 'a.id_anak', 'c.id_anak')
            ->selectRaw("
                        a.id as id_pegawai,
                        a.nama,
                        c.id_kelas as kelas_cbt,
                        a.nik,
                        a.tgl_lahir,
                        a.jenis_kelamin,
                        a.tgl_masuk,
                        a.id_divisi as divisi_id,
                        a.kesimpulan,
                        a.keputusan,
                        a.periode_masa_percobaan as periode,
                        a.keputusan_lulus as keputusan,
                        a.posisi2 as posisi,
                        a.deleted_at
                        ")
            ->where('c.id_pengawas', '!=', null)
            ->get();

        $datas = [
            'sumber_data' => 'sarang',
            'pegawai' => $dataPegawai,
            'total' => count($dataPegawai)
        ];
        return response()->json($datas, 200);
    }

    public function detail($id)
    {
        // Ambil semua data pegawai
        $dataPegawai = DB::table('hasil_wawancara as a')
            ->leftJoin('divisis as b', 'a.id_divisi', 'b.id')
            ->leftJoin('tb_anak as c', 'a.id_anak', 'c.id_anak')
            ->selectRaw("
                        a.id as id_pegawai,
                        a.nama,
                        c.id_kelas as kelas_cbt,
                        a.nik,
                        a.id_anak,
                        a.tgl_lahir,
                        a.jenis_kelamin,
                        a.tgl_masuk,
                        a.id_divisi as divisi_id,
                        a.kesimpulan,
                        a.keputusan,
                        a.periode_masa_percobaan as periode,
                        a.keputusan_lulus as keputusan,
                        a.posisi2 as posisi,
                        a.deleted_at
                        ")
            ->where('a.id', $id)->first();
        $absen = $dataPegawai ?
            DB::table('absen as a')
            ->join('users as b', 'a.id_pengawas', 'b.id')
            ->where('a.id_anak', $dataPegawai->id_anak)
            ->selectRaw("
                            a.id_anak,
                            a.id_pengawas,
                            a.tgl,
                            a.bulan_dibayar,
                            a.tahun_dibayar,
                            b.name as pengawas")
            ->orderBy('a.tgl', 'desc')
            ->get()
            : [];

        // Hitung total hari absensi per bulan untuk tahun berjalan
        $absenTotal = $dataPegawai ? DB::table('absen')
            ->where('id_anak', $dataPegawai->id_anak)
            ->whereYear('tgl', DB::raw('YEAR(CURDATE())'))
            ->groupBy(DB::raw('MONTH(tgl)'))
            ->selectRaw('MONTH(tgl) as bulan, COUNT(*) as total_hari')
            ->get()
            ->pluck('total_hari', 'bulan')
            ->toArray() : [];

        // Format total hari per bulan (1-12)
        $totalPerBulan = array_fill(1, 12, 0);
        foreach ($absenTotal as $bulan => $total) {
            $totalPerBulan[$bulan] = $total;
        }
        $datas = [
            'sumber_data' => 'sarang',
            'pegawai' => $dataPegawai,
            'absen' => $absen,
            'total_per_bulan' => $totalPerBulan,
        ];
        return response()->json($datas, 200);
    }
}
