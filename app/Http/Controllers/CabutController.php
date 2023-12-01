<?php

namespace App\Http\Controllers;

use App\Exports\CabutExport;
use App\Exports\CabutGlobalExport;
use App\Exports\CabutRekapExport;
use App\Models\Cabut;
use App\Models\Sortir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CabutController extends Controller
{
    public function index(Request $r)
    {
        $tgl = tanggalFilter($r);
        $tgl1 = $tgl['tgl1'];
        $tgl2 = $tgl['tgl2'];

        $data = [
            'title' => 'Divisi Cabut',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
        ];
        return view('home.cabut.index', $data);
    }
    public function updateAnakBelum()
    {
        $anakBelum = count(DB::table('cabut')->where([['no_box', 9999], ['id_pengawas', auth()->user()->id], ['tgl_terima', date('Y-m-d')]])->get());
        return response()->json(['anakBelum' => $anakBelum]);
    }
    public function load_halaman(Request $r)
    {
        $tgl1 = $r->tgl1;
        $tgl2 = $r->tgl2;
        $id = auth()->user()->id;

        $cabut = Cabut::getCabut();

        $data = [
            'title' => 'Divisi Cabut',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'cabut' => $cabut,
        ];
        return view('home.cabut.load_halaman', $data);
    }
    public function load_tambah_anak(Request $r)
    {
        $data = [
            'anak' => Cabut::getAnak()
        ];
        return view('home.cabut.load_tambah_anak', $data);
    }
    public function createTambahAnakCabut(Request $r)
    {
        $tgl = date('Y-m-d');
        $id_pengawas = auth()->user()->id;
        foreach ($r->all()['rows'] as $d) {
            if ($r->tipe == 'cbt') {
                $id = DB::table('cabut')->insertGetId([
                    'no_box' => 9999,
                    'id_pengawas' => $id_pengawas,
                    'id_anak' => $d,
                    'tgl_terima' => $tgl
                ]);
            }
        }
        return 'Berhasil tambah anak';
    }
    public function hapusCabutRow(Request $r)
    {
        DB::table('cabut')->where('id_cabut', $r->id_cabut)->delete();
        DB::table('absen')->where([['id_kerja', $r->id_cabut], ['ket', 'Cabut']])->delete();
        return 'Berhasil hapus baris';
    }
    public function load_tambah_cabut(Request $r)
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => Cabut::getStokBk(),
            'getAnak' => Cabut::getAnakTambah()
        ];
        return view('home.cabut.load_tambah_cabut', $data);
    }

    public function get_kelas_jenis(Request $r)
    {
        switch ($r->jenis) {
            case '3':
                $kolom = 'a.id_kategori';
                $jenis = 2;
                break;
            case '4':
                $kolom = 'a.id_kategori';
                $jenis = 3;
                break;

            default:
                $kolom = 'a.jenis';
                $jenis = $r->jenis;
                break;
        }
        $get = DB::table('tb_kelas as a')->join('paket_cabut as b', 'a.id_paket', 'b.id_paket')->where([[$kolom, $jenis], ['id_kategori', '!=', 3]])->where('a.nonaktif', 'T')->get();
        echo "
                <option value=''>Pilih</option>
            ";
        foreach ($get as $d) {
            $jenis = $d->jenis == 1 ? "$d->pcs pcs" : "$d->gr gr";
            echo "
                <option value='" . $d->id_kelas . "'>$d->paket $d->kelas ~ $jenis </option>
            ";
        }
    }

    public function cancel(Request $r)
    {
        DB::table('cabut')->where('id_cabut', $r->id_cabut)->update([
            'no_box' => 9999,
            'tgl_terima' => date('Y-m-d'),
        ]);
    }

    public function load_modal_akhir(Request $r)
    {

        $detail = DB::table('cabut as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where([['a.id_anak', $r->id_anak], ['a.no_box', $r->no_box]])
            ->first();

        $datas = Cabut::getCabutAkhir($r->orderBy);

        $data = [
            'detail' => $detail,
            'datas' => $datas,
            'orderBy' => $r->orderBy
        ];
        return view('home.cabut.load_modal_akhir', $data);
    }
    public function load_modal_anak_sisa(Request $r)
    {
        $datas = DB::table('absen as a')
            ->join('tb_anak as b', 'a.id_anak', 'b.id_anak')
            ->where('a.ket', 'cabut sisa')
            ->get();
        $data = [
            'tittle' => 'tes',
            'datas' => $datas
        ];
        return view('home.cabut.load_modal_anak_sisa', $data);
    }

    public function hapusAnakSisa(Request $r)
    {
        DB::table('absen')->where('id_absen', $r->id_absen)->delete();
    }

    public function load_anak()
    {
        $anak = Cabut::getAnak();
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

    public function load_anak_nopengawas()
    {
        $anakNoPengawas = Cabut::getAnak(1);

        echo "
        <select class='select3-load anakNoPengawas' name='' multiple id=''>
        ";
        foreach ($anakNoPengawas as $d) {
            echo "<option value='" . $d->id_anak . "'>" . ucwords($d->nama) . "</option>";
        }
        echo "
                            </select>
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

    public function tbh_baris(Request $r)
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => Cabut::getStokBk(),
            'anak' => Cabut::getAnak(),
            'count' => $r->count,

        ];
        return view('home.cabut.tbh_baris', $data);
    }

    public function get_box_sinta(Request $r)
    {
        $bk = Cabut::getStokBk($r->no_box);

        $data = [
            'pcs_awal' => $bk->pcs_awal,
            'gr_awal' => $bk->gr_awal,
            'pcs_cabut' => $bk->pcs_cabut,
            'gr_cabut' => $bk->gr_cabut,
        ];
        return json_encode($data);
    }

    public function get_kelas_anak(Request $r)
    {
        $bk = DB::table('tb_kelas')->where('id_kelas', $r->id_kelas)->first();
        $data = [
            'gr' => $bk->gr ?? 0,
            'pcs' => $bk->pcs ?? 0,
            'rupiah' => $bk->rupiah,
            'lokasi' => $bk->lokasi,
        ];
        return json_encode($data);
    }

    public function create(Request $r)
    {
        for ($i = 0; $i < count($r->no_box); $i++) {
            $no_box = $r->no_box[$i];
            $box = Cabut::getStokBk($no_box);

            DB::table('cabut')->where('id_cabut', $r->id_cabut[$i])->update([
                'no_box' => $r->no_box[$i] ?? '9999',
                'pcs_awal' => $r->pcs_awal[$i],
                'gr_awal' => $r->gr_awal[$i],
                'rupiah' => $r->rupiah[$i],
                'id_kelas' => $r->id_paket[$i],
                'tgl_terima' => $r->tgl_terima[$i],
            ]);
        }
        // return redirect()->route('cabut.index')->with('sukses', 'Berhasil tambah Data');
        return json_encode([
            'pesan' => "Berhasil tambah data cabut"
        ]);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Divisi Cabut',
            'boxBk' => Cabut::getStokBk(),
            'anak' => Cabut::getAnak(),
        ];
        return view('home.cabut.create', $data);
    }

    public function input_akhir(Request $r)
    {
        DB::table('cabut')->where('id_cabut', $r->id_cabut)->update([
            'pcs_akhir' => $r->pcs_akhir,
            'tgl_serah' => $r->tgl_serah,
            'gr_akhir' => $r->gr_akhir,
            'gr_flx' => $r->gr_flx,
            'pcs_hcr' => $r->pcs_hcr,
            'bulan_dibayar' => $r->bulan,
            'eot' => $r->eot,
            'ttl_rp' => $r->ttl_rp,
        ]);
    }

    public function load_detail_cabut(Request $r)
    {
        $detail = Cabut::getDetailPerId($r->id_cabut);
        $data = [
            'detail' => $detail
        ];
        return view('home.cabut.load_modal_detail', $data);
    }

    public function selesai_cabut(Request $r)
    {

        DB::table('cabut')->where('id_cabut', $r->id_cabut)->update(['selesai' => 'Y']);
        return redirect()->route('cabut.index')->with('sukses', 'Data telah diselesaikan');
    }

    public function ditutup(Request $r)
    {

        $data = $r->tipe == 'tutup' ? ['penutup' => 'Y'] : ['selesai' => 'T'];
        foreach ($r->datas as $d) {
            DB::table('cabut')->where('id_cabut', $d)->update($data);
        }
    }

    public function export(Request $r)
    {

        $tgl1 =  $r->tgl1;
        $tgl2 =  $r->tgl2;
        $view = 'home.cabut.export';
        $tbl = Cabut::getCabut();
        return Excel::download(new CabutExport($tbl, $view), 'Export CABUT.xlsx');
    }

    public function rekap(Request $r)
    {
        $bulan = $r->bulan ?? date('m');
        $tahun = $r->tahun ?? date('Y');
        $ttlPcsBk = 0;
        $ttlGrBk = 0;
        $ttlPcsAwal = 0;
        $ttlGrAwal = 0;
        $ttlPcsAkhir = 0;
        $ttlGrAkhir = 0;
        $ttlFlx = 0;
        $ttlEot = 0;
        $ttlRp = 0;
        $cabutGroup = Cabut::queryRekapGroup($bulan, $tahun);

        foreach ($cabutGroup as $d) {
            $ttlPcsBk += $d->pcs_bk;
            $ttlGrBk += $d->gr_bk;
            $ttlPcsAwal += $d->pcs_awal;
            $ttlGrAwal += $d->gr_awal;
            $ttlPcsAkhir += $d->pcs_akhir;
            $ttlGrAkhir += $d->gr_akhir;
            $ttlRp += $d->ttl_rp;
            $ttlFlx += $d->gr_flx;
            $ttlEot += $d->eot;
        }

        $data = [
            'title' => 'Divisi Cabut',
            'bulan' => $bulan,
            'tahun' => $tahun,
            'ttlPcsBk' => $ttlPcsBk,
            'ttlGrBk' => $ttlGrBk,
            'ttlPcsAwal' => $ttlPcsAwal,
            'ttlGrAwal' => $ttlGrAwal,
            'ttlPcsAkhir' => $ttlPcsAkhir,
            'ttlGrAkhir' => $ttlGrAkhir,
            'ttlFlx' => $ttlFlx,
            'ttlEot' => $ttlEot,
            'ttlRp' => $ttlRp,
            'cabutGroup' => $cabutGroup
        ];
        return view('home.cabut.rekap', $data);
    }

    public function export_rekap(Request $r)
    {
        $id_pengawas =  $r->id_pengawas;
        $view = 'home.cabut.export_rekap';
        $tbl = Cabut::queryRekap($id_pengawas);
        $fileName = "Export Rekap  " . auth()->user()->name;
        return Excel::download(new CabutRekapExport($tbl, $view), "$fileName.xlsx");
    }

    public function export_global(Request $r)
    {
        $bulan =  $r->bulan;
        $tahun =  $r->tahun;
        $id_pengawas = auth()->user()->id;

        $view = 'home.cabut.export_global';
        $tbl = Cabut::getRekapGlobal($bulan, $tahun, $id_pengawas);
        $fileName = "Export Rekap Global " . auth()->user()->name;
        return Excel::download(new CabutGlobalExport($tbl, $view), "$fileName.xlsx");
    }

    public function export_ibu(Request $r)
    {
        $pengawas = DB::select("SELECT b.id as id_pengawas,b.name FROM bk as a
        JOIN users as b on a.penerima = b.id
        WHERE a.kategori != 'cetak'
        group by b.id");
        $bulan = $r->bulan;
        $tahun = $r->tahun;

        // Membuat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        foreach ($pengawas as $i => $d) {
            // Membuat sheet baru
            $sheet = $spreadsheet->createSheet($i);
            $sheet->setTitle(strtoupper($d->name));

            // Mengisi sheet dengan data dari kategori tertentu
            $koloms = [
                'A1' => 'no box',
                'B1' => 'pcs awal bk',
                'C1' => 'gr awal bk',
                'D1' => 'bulan',
                'E1' => 'pgws',
                'F1' => 'pcs awal kerja',
                'G1' => 'gr awal kerja',
                'H1' => 'pcs akhir kerja',
                'I1' => 'gr akhir kerja',
                'J1' => 'eot',
                'K1' => 'flx',
                'L1' => 'susut',
                'M1' => 'ttl rp',
                'N1' => 'pcs sisa bk',
                'O1' => 'gr sisa bk',
                'P1' => 'kategori',
            ];
            foreach ($koloms as $kolom => $isiKolom) {
                $sheet->setCellValue($kolom, ucwords($isiKolom));
            }
            // Mengatur bold dan border untuk A1 dan B1
            $styleBold = [
                'font' => [
                    'bold' => true,
                ],
            ];
            $styleBaris = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A1:P1')->applyFromArray($styleBold);
            $sheet->getStyle('A1:P1')->applyFromArray($styleBaris);
            $ttlRp = 0;
            // cabut
            $row = 2;
            $cabut = Cabut::queryRekap($d->id_pengawas);
            foreach ($cabut as $data) {
                $sheet->setCellValue('A' . $row, $data->no_box);
                $sheet->setCellValue('B' . $row, $data->pcs_bk);
                $sheet->setCellValue('C' . $row, $data->gr_bk);
                $sheet->setCellValue('D' . $row, date('M y', strtotime($data->tgl)));
                $sheet->setCellValue('E' . $row, $data->pengawas);
                $sheet->setCellValue('F' . $row, $data->pcs_awal);
                $sheet->setCellValue('G' . $row, $data->gr_awal);
                $sheet->setCellValue('H' . $row, $data->pcs_akhir);
                $sheet->setCellValue('I' . $row, $data->gr_akhir);
                $sheet->setCellValue('J' . $row, $data->gr_flx);
                $sheet->setCellValue('K' . $row, $data->eot);
                $susut = empty($data->gr_awal) ? 0 : (1 - ($data->gr_flx + $data->gr_akhir) / $data->gr_awal) * 100;
                $sheet->setCellValue('L' . $row, $susut);
                $sheet->setCellValue('M' . $row, $data->rupiah);
                $sheet->setCellValue('N' . $row, $data->pcs_bk - $data->pcs_awal);
                $sheet->setCellValue('O' . $row, $data->gr_bk - $data->gr_awal);
                $sheet->setCellValue('P' . $row, $data->kategori);

                $ttlRp += $data->rupiah;
                $row++;
            }

            // sortir
            $rowSortir = $row;
            $sortir = Sortir::queryRekap($d->id_pengawas);
            foreach ($sortir as $data) {
                $sheet->setCellValue('A' . $rowSortir, $data->no_box);
                $sheet->setCellValue('B' . $rowSortir, $data->pcs_bk);
                $sheet->setCellValue('C' . $rowSortir, $data->gr_bk);
                $sheet->setCellValue('D' . $rowSortir, date('M y', strtotime($data->tgl)));
                $sheet->setCellValue('E' . $rowSortir, $data->pengawas);
                $sheet->setCellValue('F' . $rowSortir, $data->pcs_awal);
                $sheet->setCellValue('G' . $rowSortir, $data->gr_awal);
                $sheet->setCellValue('H' . $rowSortir, $data->pcs_akhir);
                $sheet->setCellValue('I' . $rowSortir, $data->gr_akhir);
                $sheet->setCellValue('J' . $rowSortir, 0);
                $sheet->setCellValue('K' . $rowSortir, 0);
                $susut = empty($data->gr_awal) ? 0 : (1 - $data->gr_akhir / $data->gr_awal) * 100;
                $sheet->setCellValue('L' . $rowSortir, $susut);
                $sheet->setCellValue('M' . $rowSortir, $data->rupiah);
                $sheet->setCellValue('N' . $rowSortir, $data->pcs_bk - $data->pcs_awal);
                $sheet->setCellValue('O' . $rowSortir, $data->gr_bk - $data->gr_awal);
                $sheet->setCellValue('P' . $rowSortir, $data->kategori);
                $ttlRp += $data->rupiah;
                $rowSortir++;
            }

            // dll
            $rowDll = $rowSortir;
            $dll = DB::selectOne("SELECT a.bulan_dibayar,a.tgl,b.nama,c.name, SUM(rupiah) AS total_rupiah
            FROM tb_hariandll as a
            LEFT JOIN tb_anak as b on a.id_anak = b.id_anak
            LEFT JOIN users as c on c.id = b.id_pengawas
            WHERE bulan_dibayar = '$bulan' AND YEAR(tgl) = '$tahun' AND a.ditutup = 'T' AND b.id_pengawas = '$d->id_pengawas'
            GROUP BY b.id_pengawas");
            $rupiahDll = $dll->total_rupiah ?? 0;
            $sheet->setCellValue('A' . $rowDll, 'Dll');
            $sheet->setCellValue('M' . $rowDll, $rupiahDll);
            $ttlRp += $rupiahDll;


            $rowTotal = $rowDll + 1;
            $sheet->setCellValue('A' . $rowTotal, 'TOTAL');
            $sheet->setCellValue('M' . $rowTotal, $ttlRp);
            $sheet->getStyle("A$rowTotal:P$rowTotal")->applyFromArray($styleBold);

            $rowDenda = $rowTotal + 1;
            $denda = DB::selectOne("SELECT sum(nominal) as rupiah FROM `tb_denda`
            WHERE bulan_dibayar = '11' AND YEAR(tgl) = '2023' AND admin = '$d->name'
            GROUP BY admin");
            $rupiahDenda = $denda->rupiah ?? 0;
            $sheet->setCellValue('A' . $rowDenda, 'Denda');
            $sheet->setCellValue('M' . $rowDenda, $rupiahDenda);

            $rowGrandTotal = $rowDenda + 1;
            $grandTotal = $ttlRp - $rupiahDenda;
            $sheet->setCellValue('A' . $rowGrandTotal, 'GRAND TOTAL');
            $sheet->setCellValue('M' . $rowGrandTotal, $grandTotal);
            $sheet->getStyle("A$rowGrandTotal:P$rowGrandTotal")->applyFromArray($styleBold);

            $baris = $rowGrandTotal;
            $sheet->getStyle('A2:P' . $baris)->applyFromArray($styleBaris);
        }

        // Membuat objek writer untuk menulis spreadsheet ke file
        $writer = new Xlsx($spreadsheet);

        // Menggunakan response untuk mengirimkan file ke browser
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="Gaji Sarang Kasih Ibu Linda.xlsx"',
            ]
        );
    }

    public function cabut_ok(Request $r)
    {
        $cabut = Cabut::getCabut();
        foreach ($cabut as $d) {
            $hasil = rumusTotalRp($d);
            DB::table('cabut')->where('id_cabut', $d->id_cabut)->update([
                'ttl_rp' => $hasil->ttl_rp
            ]);
        }
        return redirect()->route('cabut.index')->with('sukses', 'Data Tercek');
    }
}
