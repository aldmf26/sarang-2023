<?php

namespace App\Http\Controllers;

use App\Exports\CabutRekapExport;
use App\Exports\CetakExport;
use App\Exports\CetakRekapExport;
use App\Models\CetakModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CetakController extends Controller
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
            SELECT max(no_box) as no_box,sum(pcs_awal) as pcs_awal,sum(gr_awal) as gr_awal  FROM `cetak` GROUP BY no_box,id_pengawas
        ) as b ON a.no_box = b.no_box WHERE  $noBoxAda a.penerima = '$id_user'");
    }
    public function index(Request $r)
    {

        $id = auth()->user()->id;

        $tgl = tanggalFilter($r);
        $tgl1 =  $tgl['tgl1'];
        $tgl2 =  $tgl['tgl2'];

        $data = [
            'title' => 'Divisi Cetak',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'bulan' => DB::table('bulan')->get(),
            'tahun' => DB::select("SELECT YEAR(a.tgl) as tahun FROM cetak as a group by YEAR(a.tgl)"),
            'cetak' => DB::select("SELECT *
            FROM cetak as a
            LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
            where a.id_pengawas = '$id' and a.tgl between '$tgl1' and '$tgl2'
            "),
            'anakNoPengawas' => $this->getAnak(1),
            'anak' => $this->getAnak(),
            'cabut' => DB::select("SELECT a.no_box, (a.pcs_awal - IFNULL(b.pcs_awal_ctk, 0)) as pcs, (a.gr_awal - IFNULL(b.gr_awal_ctk, 0)) as gr
            FROM cabut as a 
            LEFT JOIN (
                SELECT b.no_box, SUM(b.pcs_awal) as pcs_awal_ctk, SUM(b.gr_awal) as gr_awal_ctk
                FROM cetak as b
                GROUP BY b.no_box
            ) as b ON b.no_box = a.no_box
            WHERE a.selesai = 'Y' AND (a.pcs_awal - IFNULL(b.pcs_awal_ctk, 0)) != 0")
        ];
        return view('home.cetak.index', $data);
    }
    public function ditutup(Request $r)
    {

        $data = $r->tipe == 'tutup' ? ['penutup' => 'Y'] : ['selesai' => 'T'];
        foreach ($r->datas as $d) {
            DB::table('cetak')->where('id_cetak', $d)->update($data);
        }
    }

    function get_cetak(Request $r)
    {
        $id = auth()->user()->id;



        $data = [
            'cetak' => DB::select("SELECT a.*,b.id_anak, b.nama,b.id_kelas,c.*, d.ket, a.rp_pcs as rp_per_pcs
            FROM cetak as a
            LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
            left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cetak'
            
            where a.id_pengawas = '$id' and a.penutup = 'T'
            order by a.selesai ASC, a.tgl ASC
            "),
        ];
        return view('home.cetak.get', $data);
    }



    function get_box(Request $r)
    {
        $no_box = DB::table('bk')->where('no_box', $r->no_box)->where('kategori', 'cetak')->first();

        $data = [
            'pcs' => $no_box->pcs_awal,
            'gr' => $no_box->gr_awal,
        ];
        echo json_encode($data);
    }

    function input_akhir()
    {
        $id = auth()->user()->id;
        $cetak = DB::select("SELECT a.*,b.id_anak, b.nama,b.id_kelas,c.* , d.ket, a.rp_pcs as rp_per_pcs
        FROM cetak as a
        LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
        left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cetak'
        
        where a.id_pengawas = '$id' and a.penutup = 'T' and a.selesai = 'T'
        order by a.selesai ASC
        ");

        $data = [
            'cetak' => $cetak,
            'bulan' => DB::table('bulan')->get()
        ];
        return view('home.cetak.input_akhir', $data);
    }
    public function getTotalAnak()
    {
        $tgl = date('Y-m-d');
        $id = auth()->user()->id;
        $totalAnak = DB::table('cetak as a')
            ->where('a.id_pengawas', $id)
            ->where('a.status', 'awal')
            ->count();

        return response()->json(['total_anak' => $totalAnak]);
    }

    function ambil_awal()
    {
        $id = auth()->user()->id;
        $cetak = DB::select("SELECT *
        FROM cetak as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join users as c on c.id = a.id_pengawas
        where a.status = 'awal' and a.id_pengawas = '$id'
        ");


        $data = [
            'cetak' => $cetak,
            'bk' => $this->getStokBk(),
            'kelas' => DB::select("SELECT *
            FROM kelas_cetak as a
            left join paket_cabut as b on b.id_paket = a.id_paket
            left join tipe_cabut as c on c.id_tipe = a.tipe
            ")
        ];
        return view('home.cetak.ambil_awal', $data);
    }

    function delete_awal_cetak(Request $r)
    {
        DB::table('cetak')->where('id_cetak', $r->id_cetak)->delete();
    }

    function load_anak_kerja_belum(Request $r)
    {
        $tgl = date('Y-m-d');
        $id = auth()->user()->id;
        $absen = DB::select("SELECT a.id_anak, a.nama, a.id_kelas
        From tb_anak as a 
        where a.id_pengawas = $id
        group by a.id_anak");

        $data = [
            'anak' => $absen,
            'status' => 'awal'
        ];
        return view('home.cetak.get_anak', $data);
    }

    function save_kerja(Request $r)
    {
        for ($x = 0; $x < count($r->id_anak); $x++) {
            $data = [
                'id_anak' => $r->id_anak[$x],
                'id_pengawas' => auth()->user()->id
            ];
            DB::table('cetak')->insert($data);
        }
    }

    function selesai_cetak(Request $r)
    {
        DB::table('cetak')->where('id_cetak', $r->id_cetak)->update(['selesai' => 'Y']);
    }

    public function add_target(Request $r)
    {
        for ($x = 0; $x < count($r->no_box); $x++) {
            $data = [
                'tgl' => $r->tgl[$x],
                'no_box' => $r->no_box[$x],
                'grade' => $r->grade[$x],
                'id_kelas' => $r->id_kelas_cetak[$x],
                'pcs_awal_ctk' => $r->pcs_awal[$x],
                'gr_awal_ctk' => $r->gr_awal[$x],
                'pcs_tidak_ctk' => $r->pcs_tidak_ctk[$x],
                'gr_tidak_ctk' => $r->gr_tidak_ctk[$x],
                'pcs_awal' => $r->pcs_awal[$x] + $r->pcs_tidak_ctk[$x],
                'gr_awal' => $r->gr_awal[$x] + $r->gr_tidak_ctk[$x],
                'rp_pcs' => $r->rp_pcs[$x],
                'status' => 'akhir'
            ];
            DB::table('cetak')->where('id_cetak', $r->id_cetak[$x])->update($data);
        }
        return redirect()->route('cetak.index')->with('sukses', 'Berhasil tambah Data');
    }

    function get_kelas(Request $r)
    {
        $kelas = DB::table('kelas_cetak')->where('id_kelas_cetak', $r->id_kelas_cetak)->first();

        echo $kelas->rp_pcs;
    }


    public function akhir(Request $r)
    {
        $data = [
            'cetak' => DB::table('cetak as a')
                ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
                ->where('a.id_cetak', $r->id_cetak)
                ->first(),
        ];
        return view('home.cetak.akhir', $data);
    }

    function save_akhir(Request $r)
    {
        DB::table('cetak')->where([['id_cetak', $r->id_cetak]])->update([
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
            'pcs_cu' => $r->pcs_cu,
            'gr_cu' => $r->gr_cu,
            'pcs_hcr' => $r->pcs_hcr,
            'bulan_dibayar' => $r->bulan_dibayar,
            'tgl_serah' => $r->tgl_serah
        ]);
    }

    function load_row(Request $r)
    {
        $cetak = DB::selectOne("SELECT *
        FROM cetak as a 
        left join tb_anak as b on b.id_anak = a.id_anak
        left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
        where a.id_cetak = $r->id
        ");

        $data = [
            'c' => $cetak,
            'bulan' => DB::table('bulan')->get()
        ];
        return view('home.cetak.load_row', $data);
    }

    public function add_akhir(Request $r)
    {
        $data = [
            'pcs_tidak_ctk' => $r->pcs_tidak_ctk,
            'gr_tidak_ctk' => $r->gr_tidak_ctk,
            'pcs_akhir' => $r->pcs_akhir,
            'gr_akhir' => $r->gr_akhir,
        ];

        DB::table('cetak')->where('id_cetak', $r->id_cetak)->update($data);
        return redirect()->route('cetak.index')->with('sukses', 'Berhasil tambah Data');
    }

    public function selesai(Request $r)
    {
        DB::table('cetak')->where('id_cetak', $r->id_cetak)->update(['selesai' => 'Y']);
        return redirect()->route('cetak.index')->with('sukses', 'Data telah diselesaikan');
    }

    public function tbh_baris(Request $r)
    {
        $data = [
            'count' => $r->count,
            'anakNoPengawas' => $this->getAnak(1),
            'anak' => $this->getAnak(),
            'cabut' => DB::select("SELECT a.no_box, (a.pcs_awal - IFNULL(b.pcs_awal_ctk, 0)) as pcs, (a.gr_awal - IFNULL(b.gr_awal_ctk, 0)) as gr
            FROM cabut as a 
            LEFT JOIN (
                SELECT b.no_box, SUM(b.pcs_awal) as pcs_awal_ctk, SUM(b.gr_awal) as gr_awal_ctk
                FROM cetak as b
                GROUP BY b.no_box
            ) as b ON b.no_box = a.no_box
            WHERE a.selesai = 'Y' AND (a.pcs_awal - IFNULL(b.pcs_awal_ctk, 0)) != 0")
        ];
        return view('home.cetak.tbh_baris', $data);
    }

    // public function export(Request $r)
    // {
    //     $tgl1 =  $r->tgl1;
    //     $tgl2 =  $r->tgl2;
    //     $view = 'home.cetak.export';
    //     $id = auth()->user()->id;

    //     $tbl = DB::select("SELECT a.*,b.id_anak, b.nama,b.id_kelas,c.*, d.ket
    //     FROM cetak as a
    //     LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
    //     left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
    //     left join bk as d on d.no_box = a.no_box and d.kategori = 'cetak'

    //     where a.id_pengawas = '$id' and a.penutup = 'T'
    //     order by a.selesai ASC
    //         ");

    //     $totalrow = count($tbl) + 1;

    //     return Excel::download(new CetakExport($tbl, $totalrow, $view), 'Export CETAK.xlsx');
    // }

    function export(Request $r)
    {
        $style_atas = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
        );

        $style = [
            'borders' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];
        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Cetak');


        $sheet1->getStyle("A1:W1")->applyFromArray($style_atas);

        $sheet1->setCellValue('A1', 'ID');
        $sheet1->setCellValue('B1', 'Bulan');
        $sheet1->setCellValue('C1', 'No Box');
        $sheet1->setCellValue('D1', 'Grade');
        $sheet1->setCellValue('E1', 'ID anak');
        $sheet1->setCellValue('F1', 'Nama');
        $sheet1->setCellValue('G1', 'Kelas');
        $sheet1->setCellValue('H1', 'ID Paket');
        $sheet1->setCellValue('I1', 'Pcs Tdk Ctk');
        $sheet1->setCellValue('J1', 'Gr Tdk Ctk');
        $sheet1->setCellValue('K1', 'Tgl Serah Ctk');
        $sheet1->setCellValue('L1', 'Pcs Awal');
        $sheet1->setCellValue('M1', 'Gr Awal');
        $sheet1->setCellValue('N1', 'Pcs Cu');
        $sheet1->setCellValue('O1', 'Gr Cu');
        $sheet1->setCellValue('P1', 'Pcs Akhir');
        $sheet1->setCellValue('Q1', 'Gr Akhir');
        $sheet1->setCellValue('R1', 'Harga/pcs');
        $sheet1->setCellValue('S1', 'Pcs Hcr');
        $sheet1->setCellValue('T1', 'Susut');
        $sheet1->setCellValue('U1', 'Harian');
        $sheet1->setCellValue('V1', 'Ttl Gaji');
        $sheet1->setCellValue('W1', 'Status');


        $kolom = 2;
        $id = auth()->user()->id;

        $tbl = DB::select("SELECT a.*,b.id_anak, b.nama,b.id_kelas,c.*, d.ket, a.rp_pcs as rp_per_pcs
        FROM cetak as a
        LEFT JOIN tb_anak as b on b.id_anak = a.id_anak
        left join kelas_cetak as c on c.id_kelas_cetak = a.id_kelas
        left join bk as d on d.no_box = a.no_box and d.kategori = 'cetak'
        
        where a.id_pengawas = '$id' and a.penutup = 'T'
        order by a.selesai ASC
            ");

        foreach ($tbl as $c) {
            $susut = empty($c->gr_akhir) ? '0' : (1 - ($c->gr_akhir + $c->gr_cu) / ($c->gr_awal - $c->gr_tidak_ctk)) * 100;
            $denda = round($susut, 0) >= $c->batas_susut ? round($susut) * $c->denda_susut : 0;
            $denda_hcr = $c->pcs_hcr * $c->denda_hcr;
            $ttl_rp = $c->pcs_akhir == '0' ? $c->pcs_awal_ctk * $c->rp_per_pcs : $c->pcs_akhir * $c->rp_per_pcs;


            $sheet1->setCellValue('A' . $kolom, $c->id_cetak);
            $sheet1->setCellValue('B' . $kolom, !empty($c->bulan_dibayar) ? $c->bulan_dibayar : '');
            $sheet1->setCellValue('C' . $kolom, $c->no_box);
            $sheet1->setCellValue('D' . $kolom, $c->grade);
            $sheet1->setCellValue('E' . $kolom, $c->id_anak);
            $sheet1->setCellValue('F' . $kolom, $c->nama);
            $sheet1->setCellValue('G' . $kolom, $c->id_kelas);
            $sheet1->setCellValue('H' . $kolom, $c->id_kelas_cetak);
            $sheet1->setCellValue('I' . $kolom, $c->pcs_tidak_ctk);
            $sheet1->setCellValue('J' . $kolom, $c->gr_tidak_ctk);
            $sheet1->setCellValue('K' . $kolom, $c->tgl);
            $sheet1->setCellValue('L' . $kolom, $c->pcs_awal_ctk);
            $sheet1->setCellValue('M' . $kolom, $c->gr_awal_ctk);
            $sheet1->setCellValue('N' . $kolom, $c->pcs_cu);
            $sheet1->setCellValue('O' . $kolom, $c->gr_cu);
            $sheet1->setCellValue('P' . $kolom, $c->pcs_akhir);
            $sheet1->setCellValue('Q' . $kolom, $c->gr_akhir);
            $sheet1->setCellValue('R' . $kolom, $c->rp_per_pcs);
            $sheet1->setCellValue('S' . $kolom, $c->pcs_hcr);
            $sheet1->setCellValue('T' . $kolom, round($susut) . '%');
            $sheet1->setCellValue('U' . $kolom, $c->rp_harian);
            $sheet1->setCellValue('V' . $kolom, $ttl_rp - $denda - $denda_hcr);
            $sheet1->setCellValue('W' . $kolom, $c->selesai == 'Y' ? 'Selesai' : 'Akhir');


            $kolom++;
        }
        $sheet1->getStyle('A2:W' . $kolom - 1)->applyFromArray($style);

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet(1);
        $sheet2->setTitle('Data Anak');
        $sheet2->getStyle('A1:C1')->applyFromArray($style_atas);

        $sheet2->setCellValue('A1', 'ID Anak');
        $sheet2->setCellValue('B1', 'Nama');
        $sheet2->setCellValue('C1', 'Kelas');

        $id = auth()->user()->id;
        $anak = DB::table('tb_anak as a')
            ->join('tb_kelas as b', 'a.id_kelas', 'b.id_kelas')
            ->where('id_pengawas', $id)
            ->get();
        $kolom2 = 2;
        foreach ($anak as $b) {
            $sheet2->setCellValue('A' . $kolom2, $b->id_anak);
            $sheet2->setCellValue('B' . $kolom2, $b->nama);
            $sheet2->setCellValue('C' . $kolom2, $b->id_kelas);
            $kolom2++;
        }
        $sheet2->getStyle('A2:C' . $kolom2 - 1)->applyFromArray($style);

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet(2);
        $sheet3->setTitle('Data Paket');
        $sheet3->getStyle('A1:H1')->applyFromArray($style_atas);

        $sheet3->setCellValue('A1', 'ID Paket');
        $sheet3->setCellValue('B1', 'Paket');
        $sheet3->setCellValue('C1', 'Kelas');
        $sheet3->setCellValue('D1', 'Tipe');
        $sheet3->setCellValue('E1', 'Rp Pcs');
        $sheet3->setCellValue('F1', 'Denda Hcr');
        $sheet3->setCellValue('G1', 'Bts Sst');
        $sheet3->setCellValue('H1', 'Denda Sst');

        $id = auth()->user()->id;
        $paket = DB::table('kelas_cetak as a')
            ->join('paket_cabut as b', 'a.id_paket', 'b.id_paket')
            ->join('tipe_cabut as c', 'a.tipe', 'c.id_tipe')
            ->get();
        $kolom2 = 2;
        foreach ($paket as $p) {
            $sheet3->setCellValue('A' . $kolom2, $p->id_kelas_cetak);
            $sheet3->setCellValue('B' . $kolom2, $p->paket);
            $sheet3->setCellValue('C' . $kolom2, $p->kelas);
            $sheet3->setCellValue('D' . $kolom2, $p->tipe);
            $sheet3->setCellValue('E' . $kolom2, $p->rp_pcs);
            $sheet3->setCellValue('F' . $kolom2, $p->denda_hcr);
            $sheet3->setCellValue('G' . $kolom2, $p->batas_susut);
            $sheet3->setCellValue('H' . $kolom2, $p->denda_susut);
            $kolom2++;
        }
        $sheet3->getStyle('A2:H' . $kolom2 - 1)->applyFromArray($style);

        $namafile = "Cetak.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function queryRekap($tgl1, $tgl2)
    {
        $id = auth()->user()->id;
        $posisi = auth()->user()->posisi_id;
        $pengawas = $posisi == 13 ? "AND a.id_pengawas = '$id'" : '';

        return DB::select("SELECT
                    MAX(a.no_box) as no_box,
                    MAX(a.tgl) as tgl,
                    a.pcs_awal,
                    a.gr_awal,
                    a.gr_akhir,
                    a.gr_tidak_ctk,
                    a.rp_pcs,
                    b.name,
                    c.pcs_akhir as cabut_pcs_akhir,
                    c.gr_akhir as cabut_gr_akhir
                FROM cetak as a
                LEFT JOIN users as b ON a.id_pengawas = b.id
                LEFT JOIN (
                    SELECT no_box, SUM(pcs_akhir) as pcs_akhir, SUM(gr_akhir) as  gr_akhir
                    FROM cabut
                    GROUP BY no_box
                ) as c ON a.no_box = c.no_box
                WHERE a.selesai = 'Y' AND a.tgl BETWEEN '$tgl1' AND '$tgl2' $pengawas
                GROUP BY a.pcs_awal, a.gr_awal, b.name, c.pcs_akhir, c.gr_akhir;
            ");
    }

    public function rekap(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 =  $tgl['tgl1'];
        $tgl2 =  $tgl['tgl2'];
        $cetakgroup = CetakModel::cetakGroup();

        $pcs_bk = 0;
        $gr_bk = 0;
        $pcs_tdk_ctk = 0;
        $gr_tdk_ctk = 0;
        $pcs_awal = 0;
        $gr_awal = 0;
        $pcs_akhir = 0;
        $gr_akhir = 0;
        $pcs_cu = 0;
        $gr_cu = 0;
        $ttl_rp = 0;
        foreach ($cetakgroup as $c) {
            $pcs_bk += $c->pcs_bk;
            $gr_bk += $c->gr_bk;
            $pcs_tdk_ctk += $c->pcs_tdk_ctk;
            $gr_tdk_ctk += $c->gr_tidak_ctk;
            $pcs_awal += $c->pcs_awal;
            $gr_awal += $c->gr_awal;
            $pcs_akhir += $c->pcs_akhir;
            $gr_akhir += $c->gr_akhir;
            $pcs_cu += $c->pcs_cu;
            $gr_cu += $c->gr_cu;

            $ttl_rp += $c->ttl_rp - $c->denda_susut - $c->denda_hcr;
        }

        $data = [
            'title' => 'Rekap Summary Cetak',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cetakgroup' => $cetakgroup,
            'bulan' => DB::table('bulan')->get(),
            'tahun' => DB::select("SELECT YEAR(a.tgl) as tahun FROM cetak as a group by YEAR(a.tgl)"),
            'pcs_bk' => $pcs_bk,
            'gr_bk' => $gr_bk,
            'pcs_tdk_ctk' => $pcs_tdk_ctk,
            'gr_tdk_ctk' => $gr_tdk_ctk,
            'pcs_awal' => $pcs_awal,
            'gr_awal' => $gr_awal,
            'pcs_akhir' => $pcs_akhir,
            'gr_akhir' => $gr_akhir,
            'pcs_cu' => $pcs_cu,
            'gr_cu' => $gr_cu,
            'ttl_rp' => $ttl_rp
        ];
        return view('home.cetak.rekap', $data);
    }

    public function export_rekap(Request $r)
    {
        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.cetak.export_rekap';
        $tbl = CetakModel::cetak_export();

        return Excel::download(new CetakRekapExport($tbl, $view), 'Export REKAP CETAK.xlsx');
    }

    function delete_cetak(Request $r)
    {
        DB::table('cetak')->where('id_cetak', $r->id_cetak)->delete();
    }

    function import(Request $r)
    {
        $uploadedFile = $r->file('file');
        $allowedExtensions = ['xlsx'];
        $extension = $uploadedFile->getClientOriginalExtension();

        if (in_array($extension, $allowedExtensions)) {
            $spreadsheet = IOFactory::load($uploadedFile->getPathname());
            $sheet = $spreadsheet->getSheetByName('Cetak');
            $data = [];

            foreach ($sheet->getRowIterator() as $index => $row) {
                if ($index === 1) {
                    continue;
                }

                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

            // $importGagal = false;

            DB::beginTransaction(); // Mulai transaksi database

            try {
                foreach ($data as $rowData) {
                    $tgl = $rowData[10];
                    if (is_numeric($tgl)) {
                        // Jika nilai berupa angka, konversi ke format tanggal
                        $tanggalExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tgl);
                        $tanggalFormatted = $tanggalExcel->format('Y-m-d');
                    } else {
                        // Jika nilai sudah dalam format tanggal, pastikan formatnya adalah 'Y-m-d'
                        $tanggalFormatted = date('Y-m-d', strtotime($tgl));
                    }
                    $id = auth()->user()->id;
                    if (empty($rowData[0])) {

                        DB::table('cetak')->insert([
                            'bulan_dibayar' => $rowData[1],
                            'no_box' => $rowData[2],
                            'grade' => $rowData[3],
                            'id_anak' => $rowData[4],
                            'id_kelas' => $rowData[7],
                            'pcs_awal' => $rowData[8] + $rowData[11],
                            'gr_awal' => $rowData[9] + $rowData[12],
                            'pcs_tidak_ctk' => $rowData[8],
                            'gr_tidak_ctk' => $rowData[9],
                            'tgl' => $tanggalFormatted,
                            'tgl_serah' => $tanggalFormatted,
                            'pcs_awal_ctk' => $rowData[11],
                            'gr_awal_ctk' => $rowData[12],
                            'pcs_cu' => $rowData[13],
                            'gr_cu' => $rowData[14],
                            'pcs_akhir' => $rowData[15],
                            'gr_akhir' => $rowData[16],
                            'rp_pcs' => $rowData[17],
                            'pcs_hcr' => $rowData[18],
                            'id_pengawas' => $id,
                            'status' => empty($rowData[11]) ? 'awal' : 'akhir',
                            'selesai' => $rowData[21] == 'Selesai' ? 'Y' : 'T'
                        ]);
                    } else {

                        DB::table('cetak')->where('id_cetak', $rowData[0])->update([
                            'bulan_dibayar' => $rowData[1],
                            'no_box' => $rowData[2],
                            'grade' => $rowData[3],
                            'id_anak' => $rowData[4],
                            'id_kelas' => $rowData[7],
                            'pcs_awal' => $rowData[8] + $rowData[11],
                            'gr_awal' => $rowData[9] + $rowData[12],
                            'pcs_tidak_ctk' => $rowData[8],
                            'gr_tidak_ctk' => $rowData[9],
                            'tgl' => $tanggalFormatted,
                            'pcs_awal_ctk' => $rowData[11],
                            'gr_awal_ctk' => $rowData[12],
                            'pcs_cu' => $rowData[13],
                            'gr_cu' => $rowData[14],
                            'pcs_akhir' => $rowData[15],
                            'gr_akhir' => $rowData[16],
                            'rp_pcs' => $rowData[17],
                            'pcs_hcr' => $rowData[18],
                            'id_pengawas' => $id,
                            'status' => empty($rowData[11]) ? 'awal' : 'akhir',
                            'selesai' => $rowData[21] == 'Selesai' ? 'Y' : 'T'
                        ]);
                    }
                }
                DB::commit(); // Konfirmasi transaksi jika berhasil
                return redirect()->route('cetak.index')->with('sukses', 'Data berhasil import');
            } catch (\Exception $e) {
                DB::rollback(); // Batalkan transaksi jika terjadi kesalahan lain
                return redirect()->route('cetak.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
            }
        } else {
            return redirect()->route('cetak.index')->with('error', 'File yang diunggah bukan file Excel yang valid');
        }
    }

    function export_gaji_global(Request $r)
    {
        $style_atas = array(
            'font' => [
                'bold' => true, // Mengatur teks menjadi tebal
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ],
        );

        $style = [
            'borders' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];
        $spreadsheet = new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Gaji Global Cetak');


        $sheet1->getStyle("A1:Q1")->applyFromArray($style_atas);

        $sheet1->setCellValue('A1', 'Pengawas');
        $sheet1->setCellValue('B1', 'Hari Masuk');
        $sheet1->setCellValue('C1', 'Nama Anak');
        $sheet1->setCellValue('D1', 'Kelas');
        $sheet1->setCellValue('E1', 'Pcs Awal Cetak');
        $sheet1->setCellValue('F1', 'Gr Awal Cetak');
        $sheet1->setCellValue('G1', 'Pcs Akhir Cetak');
        $sheet1->setCellValue('H1', 'Gr Akhir Cetak');
        $sheet1->setCellValue('I1', 'Eo t gr');
        $sheet1->setCellValue('J1', 'Ttl rp');
        $sheet1->setCellValue('K1', 'Gr eo awal');
        $sheet1->setCellValue('L1', 'Gr eo akhir');
        $sheet1->setCellValue('M1', 'Ttl Rp Eo');
        $sheet1->setCellValue('N1', 'Kerja dll');
        $sheet1->setCellValue('O1', 'Rp denda');
        $sheet1->setCellValue('P1', 'Total Gaji');
        $sheet1->setCellValue('Q1', 'Rata2');


        $kolom = 2;
        $id = auth()->user()->id;
        $nama = auth()->user()->name;
        $bulan = $r->bulan;
        $tahun = $r->tahun;
        $tgl_awal = '01-' . $bulan . '-' . $tahun;

        $tgl = date('Y-m-27', strtotime('-1 month', strtotime($tgl_awal)));
        $tgl2 = date('Y-m-26', strtotime($tgl_awal));


        $tbl = DB::select("SELECT a.*, b.total_absen, c.pcs_awal_cetak, c.gr_awal_cetak, c.pcs_akhir, c.gr_akhir,c.total_rp,c.denda_susut,c.denda_hcr, e.rp_eo, e.gr_eo_awal, e.gr_eo_akhir, f.rp_harian, g.rp_denda, c.rp_harian_cetak, f.pcs_harian, f.gr_harian
        FROM tb_anak as a
        left join (
            SELECT b.id_anak, count(b.id_absen) as total_absen
            FROM absen as b
            where b.tgl between '$tgl' and '$tgl2'
            group by b.id_anak
        ) as b on b.id_anak = a.id_anak
        left join (
            SELECT
            c.id_anak,
            SUM(c.pcs_awal_ctk) AS pcs_awal_cetak,
            SUM(c.gr_awal_ctk) AS gr_awal_cetak,
            SUM(c.pcs_akhir) AS pcs_akhir,
            SUM(c.gr_akhir) AS gr_akhir,
            SUM(
                IF(
                    round((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100) >= d.batas_susut,
                    round(((1 - ((c.gr_akhir + c.gr_cu) / c.gr_awal_ctk)) * 100)) * d.denda_susut,0
                )
            ) AS denda_susut,
            sum(c.pcs_hcr * d.denda_hcr) as denda_hcr,
            sum(c.pcs_akhir * c.rp_pcs) as total_rp,
            sum(c.rp_harian) as rp_harian_cetak
            FROM
                cetak AS c
            LEFT JOIN
                kelas_cetak AS d ON d.id_kelas_cetak = c.id_kelas
            WHERE
                c.bulan_dibayar = '$bulan' AND YEAR(c.tgl) = '$tahun' AND c.selesai = 'Y'
            GROUP BY
                c.id_anak
        )  as c on c.id_anak = a.id_anak
        left join (
            SELECT e.id_anak, sum(if(e.ttl_rp is null ,0,e.ttl_rp)) as rp_eo, sum(e.gr_eo_awal) as gr_eo_awal, sum(e.gr_eo_akhir) as gr_eo_akhir
			FROM eo as e
            where e.bulan_dibayar = '$bulan' and YEAR(e.tgl_ambil) = '$tahun' and e.selesai = 'Y' and e.id_pengawas = '$id'
            group by e.id_anak
        ) as e on e.id_anak = a.id_anak
        left join (
            SELECT f.id_anak, sum(if(f.rupiah is null ,0,f.rupiah)) as rp_harian, sum(f.pcs) as pcs_harian, sum(f.gr) as gr_harian
			FROM tb_hariandll as f
            where f.tgl BETWEEN '$tgl' and '$tgl2'
            group by f.id_anak
        ) as f on f.id_anak = a.id_anak
        left join (
            SELECT g.id_anak, sum(if(g.nominal is null ,0,g.nominal)) as rp_denda
			FROM tb_denda as g
            where g.tgl BETWEEN '$tgl' and '$tgl2'
            group by g.id_anak
        ) as g on g.id_anak = a.id_anak
        where a.id_pengawas = '$id'
        order by a.id_kelas DESC, a.nama ASC
            ");

        $pcs_awal_ctk = 0;
        $gr_awal_ctk = 0;
        $pcs_akhir = 0;
        $gr_akhir = 0;
        $ttl_rp = 0;
        $rp_denda = 0;
        $rata2 = 0;
        $ttl_absen = 0;

        $gr_awal_eo = 0;
        $gr_akhir_eo = 0;
        $rp_eo = 0;
        $rp_harian = 0;
        foreach ($tbl as $c) {
            $sheet1->setCellValue('A' . $kolom, $nama);
            $sheet1->setCellValue('B' . $kolom, $c->total_absen);
            $sheet1->setCellValue('C' . $kolom, $c->nama);
            $sheet1->setCellValue('D' . $kolom, $c->id_kelas);
            $sheet1->setCellValue('E' . $kolom, $c->pcs_awal_cetak + $c->pcs_harian);
            $sheet1->setCellValue('F' . $kolom, $c->gr_awal_cetak + $c->gr_harian);
            $sheet1->setCellValue('G' . $kolom, $c->pcs_akhir + $c->pcs_harian);
            $sheet1->setCellValue('H' . $kolom, $c->gr_akhir + $c->gr_harian);
            $sheet1->setCellValue('I' . $kolom, '');
            $sheet1->setCellValue('J' . $kolom, $c->total_rp + $c->rp_harian_cetak);
            $sheet1->setCellValue('K' . $kolom, $c->gr_eo_awal);
            $sheet1->setCellValue('L' . $kolom, $c->gr_eo_akhir);
            $sheet1->setCellValue('M' . $kolom, $c->rp_eo);
            $sheet1->setCellValue('N' . $kolom, $c->rp_harian);
            $sheet1->setCellValue('O' . $kolom, $c->denda_hcr + $c->denda_susut + $c->rp_denda);
            $sheet1->setCellValue('P' . $kolom, $c->total_rp + $c->rp_harian_cetak + $c->rp_eo + $c->rp_harian  - $c->denda_hcr - $c->denda_susut - $c->rp_denda);
            $sheet1->setCellValue('Q' . $kolom, ($c->total_rp + $c->rp_harian_cetak + $c->rp_eo + $c->rp_harian - $c->denda_hcr - $c->denda_susut - $c->rp_denda) / $c->total_absen);

            $kolom++;

            $pcs_awal_ctk += $c->pcs_awal_cetak + $c->pcs_harian;
            $gr_awal_ctk += $c->gr_awal_cetak + $c->gr_harian;
            $pcs_akhir += $c->pcs_akhir + +$c->pcs_harian;
            $gr_akhir += $c->gr_akhir + $c->gr_harian;
            $ttl_rp += $c->total_rp + $c->rp_harian_cetak;
            $rp_denda += $c->denda_hcr + $c->denda_susut + $c->rp_denda;
            $rata2 += ($c->total_rp + $c->rp_harian_cetak + $c->rp_eo + $c->rp_harian - $c->denda_hcr - $c->denda_susut - $c->rp_denda) / $c->total_absen;
            $ttl_absen += $c->total_absen;

            $gr_awal_eo += $c->gr_eo_awal;
            $gr_akhir_eo += $c->gr_eo_akhir;
            $rp_eo += $c->rp_eo;
            $rp_harian += $c->rp_harian;
        }
        $sheet1->getStyle('A2:Q' . $kolom - 1)->applyFromArray($style);
        $sheet1->setCellValue('A' . $kolom, 'Total');
        $sheet1->setCellValue('B' . $kolom, $ttl_absen);
        $sheet1->setCellValue('C' . $kolom, '');
        $sheet1->setCellValue('D' . $kolom, '');
        $sheet1->setCellValue('E' . $kolom, $pcs_awal_ctk);
        $sheet1->setCellValue('F' . $kolom, $gr_awal_ctk);
        $sheet1->setCellValue('G' . $kolom,  $pcs_akhir);
        $sheet1->setCellValue('H' . $kolom,  $gr_akhir);
        $sheet1->setCellValue('I' . $kolom, '0');
        $sheet1->setCellValue('J' . $kolom, $ttl_rp);
        $sheet1->setCellValue('K' . $kolom, $gr_awal_eo);
        $sheet1->setCellValue('L' . $kolom, $gr_akhir_eo);
        $sheet1->setCellValue('M' . $kolom, $rp_eo);
        $sheet1->setCellValue('N' . $kolom, $rp_harian);
        $sheet1->setCellValue('O' . $kolom, $rp_denda);
        $sheet1->setCellValue('P' . $kolom, $ttl_rp + $rp_eo + $rp_harian - $rp_denda);
        $sheet1->setCellValue('Q' . $kolom, $rata2);




        $sheet1->getStyle("A" . $kolom . ':' .  "Q" . $kolom)->applyFromArray($style_atas);


        $namafile = "Gaji global cetak.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
