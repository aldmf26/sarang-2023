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
        $datas = [
            [
                'param' => 'hrga3_1',
                'title' => 'Informasi Tawaran Pelatihan',
                'deskripsi' => 'FRM.HRGA.03.01 - Informasi Tawaran Pelatihan',
            ],
            [
                'param' => 'hrga3_2',
                'title' => 'Program Pelatihan tahunan',
                'deskripsi' => 'FRM.HRGA.03.02 -  Program Pelatihan tahunan',
            ],
            [
                'param' => 'hrga3_3',
                'title' => 'Usulan dan Identifikasi Kebutuhan Pelatihan',
                'deskripsi' => 'FRM.HRGA.03.03 - Usulan dan Identifikasi Kebutuhan Pelatihan',
            ],
            [
                'param' => 'hrga3_6',
                'title' => 'Evaluasi Pelatihan',
                'deskripsi' => 'FRM.HRGA.03.06 - Evaluasi Pelatihan',
            ],
        ];
        $data = [
            'title' => 'Pelatihan',
            'datas' => $datas
        ];
        return view('hccp.hrga3_pelatihan.index', $data);
    }

    public function medical()
    {
        $datas = [
            [
                'param' => 'hrga4_1',
                'title' => 'Jadwal Medical Check Up',
                'deskripsi' => 'FRM.HRGA.04.01 - Jadwal Medical Check Up',
            ],

        ];
        $data = [
            'title' => 'Medical',
            'datas' => $datas
        ];
        return view('hccp.hrga4_medical.index', $data);
    }
    public function pemeliharaanBangunan()
    {
        $datas = [
            [
                'param' => 'hrga5_1',
                'title' => 'Program Perawatan Sarana dan Prasarana Umum',
                'deskripsi' => 'FRM.HRGA.05.01 - Program Perawatan Sarana dan Prasarana Umum',
            ],
            [
                'param' => 'hrga5_2',
                'title' => 'Riwayat Pemeliharaan Sarana dan Prasarana Umum',
                'deskripsi' => 'FRM.HRGA.05.02 - Riwayat Pemeliharaan Sarana dan Prasarana Umum',
            ],
            [
                'param' => 'hrga5_3',
                'title' => 'Permintaan Perbaikan Sarana dan Prasarana Umum',
                'deskripsi' => 'FRM.HRGA.05.03 - Permintaan Perbaikan Sarana dan Prasarana Umum',
            ],

        ];
        $data = [
            'title' => 'Pemeliharaan bangunan',
            'datas' => $datas
        ];
        return view('hccp.hrga5_pemeliharaan.index', $data);
    }

    public function sanitasi()
    {
        $datas = [
            [
                'param' => 'hrga6_1',
                'title' => 'Perencanaan Kebersihan',
                'deskripsi' => 'FRM.HRGA.06.01 - Perencanaan Kebersihan',
            ],
            [
                'param' => 'hrga6_2',
                'title' => 'Ceklis Sanitasi',
                'deskripsi' => 'FRM.HRGA.06.02 - Ceklis Sanitasi',
            ],
            [
                'param' => 'hrga6_4',
                'title' => 'Ceklis Foot Bath',
                'deskripsi' => 'FRM.HRGA.06.04 - Ceklis Foot Bath',
            ],

        ];
        $data = [
            'title' => 'Sanitasi',
            'datas' => $datas
        ];
        return view('hccp.hrga6_sanitasi.index', $data);
    }
    public function pembuangan_sampah()
    {
        $datas = [
            [
                'param' => 'hrga7_1',
                'title' => 'Schedule Pembuangan Sampah',
                'deskripsi' => 'FRM.HRGA.07.01, Rev.00 - Schedule Pembuangan Sampah',
            ],
            [
                'param' => 'hrga7_2',
                'title' => 'Schedule pembuangan TPS',
                'deskripsi' => 'FRM.HRGA.07.02 - Schedule pembuangan TPS',
            ],
            [
                'param' => 'hrga7_3',
                'title' => 'Identifikasi Limbah',
                'deskripsi' => 'FRM.HRGA.07.03 - Identifikasi Limbah',
            ],


        ];
        $data = [
            'title' => 'Pengelolaan Limbah',
            'datas' => $datas
        ];
        return view('hccp.hrga7_pengelolaan_limbah.index', $data);
    }
    public function perawatan_dan_perbaikan_mesin()
    {
        $datas = [
            [
                'param' => 'hrga8_1',
                'title' => 'PROGRAM PERAWATAN MESIN PROSES PRODUKSI',
                'deskripsi' => 'FRM.HRGA.08.01 - PROGRAM PERAWATAN MESIN PROSES PRODUKSI',
            ],
            [
                'param' => 'hrga8_2',
                'title' => 'CEKLIST PERAWATAN MESIN PROSES PRODUKSI',
                'deskripsi' => 'FRM.HRGA.08.02 - CEKLIST PERAWATAN MESIN PROSES PRODUKSI',
            ],
            [
                'param' => 'hrga8_3',
                'title' => 'PERMINTAAN PERBAIKAN MESIN PROSES PRODUKSI',
                'deskripsi' => 'FRM.HRGA.08.03 - PERMINTAAN PERBAIKAN MESIN PROSES PRODUKSI',
            ],
        ];
        $data = [
            'title' => 'Perawatan dan Perbaikan Mesin',
            'datas' => $datas
        ];
        return view('hccp.hrga8_perawatan_perbaikan_mesin.index', $data);
    }
}
