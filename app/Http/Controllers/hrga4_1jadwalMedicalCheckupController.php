<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hrga4_1jadwalMedicalCheckupController extends Controller
{
    public function index(Request $r)
    {
        $jadwal = DB::select("SELECT b.nama, c.divisi, a.bulan, a.tahun
        FROM jadwal_medical as a
        left join hasil_wawancara as b on b.id = a.id_karyawan
        left join divisis as c on c.id = b.id_divisi
        where b.id_divisi = $r->divisi
        ");

        $tahun = DB::select("SELECT DISTINCT tahun FROM jadwal_medical ORDER BY tahun DESC");
        $data = [
            'title' => 'Jadwal Medical Check Up',
            'jadwal' => $jadwal,
            'tahun' => $tahun,
            'karyawan' => DB::table('hasil_wawancara')->where('id_divisi', $r->divisi)->get(),
            'bulan' => DB::table('bulan')->get(),
            'divisi' => $r->divisi
        ];
        return view('hccp.hrga4_medical.hrga1.index', $data);
    }

    public function Store(Request $r)
    {
        $data = [
            'id_karyawan' => $r->id_karyawan,
            'bulan' => $r->bulan,
            'tahun' => $r->tahun
        ];

        DB::table('jadwal_medical')->insert($data);
        return redirect()->back()->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function print(Request $r)
    {
        $jadwal = DB::select("SELECT b.nama, c.divisi, a.bulan, a.tahun
        FROM jadwal_medical as a
        left join hasil_wawancara as b on b.id = a.id_karyawan
        left join divisis as c on c.id = b.id_divisi
        where b.id_divisi = $r->divisi and a.tahun = $r->tahun
        ");
        $data = [
            'title' => 'Jadwal Medical Check Up',
            'jadwal' => $jadwal,
            'bulan' => DB::table('bulan')->get(),
            'tahun' => $r->tahun
        ];
        return view('hccp.hrga4_medical.hrga1.print', $data);
    }
}
