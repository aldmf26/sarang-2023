<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HccpController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'HCCP',
        ];
        return view('hccp.index', $data);
    }

    public function sampleAdministrator()
    {
        $datas = [
            [
                'param' => 'hrga1',
                'title' => 'Pemohonan Karyawan Baru',
                'deskripsi' => 'FRM.HRGA.01.01 - Permohonan Karyawan Baru',
            ],
            [
                'param' => 'hrga2',
                'title' => 'Hasil Wawancara',
                'deskripsi' => 'FRM.HRGA.01.02 - Hasil Wawancara',
            ],
            [
                'param' => 'hrga3',
                'title' => 'Hasil Evaluasi Karyawan Baru',
                'deskripsi' => 'FRM.HRGA.01.03 - Hasil Evaluasi Karyawan Baru',
            ],
            [
                'param' => 'hrga4',
                'title' => 'Data Pegawai',
                'deskripsi' => 'FRM.HRGA.01.04 - Data Pegawai',
            ],
        ];
        $data = [
            'title' => 'Sampel Administrator',
            'datas' => $datas
        ];
        return view('hccp.hrga1_penerimaan.index', $data);
    }

    public function evaluasiKompetensiKaryawan()
    {
        $datas = [
           
            [
                'param' => 'hrga2_2',
                'title' => 'Penilaian Kompetensi',
                'deskripsi' => 'FRM.HRGA.02.02 - Penilaian Kompetensi',
            ],
           
            [
                'param' => 'hrga2_5',
                'title' => 'Jadwal GAP Analysis',
                'deskripsi' => 'FRM.HRGA.02.05 -  Jadwal GAP Analysis',
            ],
        ];
        $data = [
            'title' => 'Evaluasi Kompetensi Karyawan',
            'datas' => $datas
        ];
        return view('hccp.hrga2_penilaian.index', $data);
    }

    public function pelatihan()
    {
        $data = [
            'title' => 'Pelatihan',
        ];
        return view('hccp.hrga3_pelatihan.index', $data);
    }

    public function medical()
    {
        $data = [
            'title' => 'Medical',
        ];
        return view('hccp.hrga4_medical.index', $data);
    }
}
