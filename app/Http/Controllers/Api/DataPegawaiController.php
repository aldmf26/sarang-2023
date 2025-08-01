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
                        a.id_anak,
                        a.nama,
                        c.id_pengawas,
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
            ->get();
        $dataHasilWawancara = DB::table('hasil_wawancara')
            ->selectRaw("
             nama as nama_lengkap,
             id,
             id_divisi,
             nik,
             tgl_lahir,
             jenis_kelamin,
             tgl_masuk,
             kesimpulan
            ")
            ->get();

        $dataPenilaianKaryawan = DB::table('penilaian_karyawan')
            ->selectRaw('id_anak,
             periode,
             pendidikan_standar,
             pendidikan_hasil,
             pelatihan_standar,
             pelatihan_hasil,
             keterampilan_standar,
             keterampilan_hasil,
             kompetensi_inti_standar,
             kompetensi_inti_hasil
            ')
            ->get();

        $datas = [
            'sumber_data' => 'sarang',
            'pegawai' => $dataPegawai,
            'hasil_wawancara' => $dataHasilWawancara,
            'penilaian_karyawan' => $dataPenilaianKaryawan,
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
        $idPegawai = $dataPegawai->id_anak == 0 ? $dataPegawai->id_pegawai : $dataPegawai->id_anak;
        $absen = $dataPegawai ?
            DB::table('absen as a')
            ->where('a.id_anak', $idPegawai)
            ->selectRaw("
                            a.id_anak,
                            a.id_pengawas,
                            a.tgl,
                            a.bulan_dibayar,
                            a.tahun_dibayar")
            ->orderBy('a.tgl', 'desc')
            ->get()
            : [];

        // Hitung total hari absensi per bulan untuk tahun berjalan
        $absenTotal = $dataPegawai ? DB::table('absen')
            ->where('id_anak', $idPegawai)
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
