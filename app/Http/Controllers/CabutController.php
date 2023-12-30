<?php

namespace App\Http\Controllers;

use App\Exports\CabutExport;
use App\Exports\CabutGlobalExport;
use App\Exports\CabutRekapExport;
use App\Models\Cabut;
use App\Models\Eo;
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
            $id_cabut = $r->id_cabut[$i];
            $data = [
                'no_box' => $r->no_box[$i] ?? '9999',
                'pcs_awal' => $r->pcs_awal[$i],
                'gr_awal' => $r->gr_awal[$i],
                'id_anak' => $r->id_anak[$i],
                'id_pengawas' => $r->id_pengawas[$i],
                'rupiah' => $r->rupiah[$i],
                'id_kelas' => $r->id_paket[$i],
                'tgl_terima' => $r->tgl_terima[$i],
            ];
            if($id_cabut == 9999) {
                DB::table('cabut')->insert($data);
            } else {
                DB::table('cabut')->where('id_cabut', $id_cabut)->update($data);
            }
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

        $pengawas = DB::select("SELECT b.id as id_pengawas,b.name FROM bk as a
        JOIN users as b on a.penerima = b.id
        WHERE a.kategori != 'cetak'
        group by b.id");
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        foreach ($pengawas as $i => $d) {
            $sheet = $spreadsheet->createSheet($i);
            $sheet->setTitle(strtoupper($d->name));

            $koloms = [
                'A1' => 'cabut',
                'M1' => 'cabut eo',
                'Q1' => 'sortir',
                'X1' => 'gajih',
                'A2' => 'pgws',
                'B2' => 'hari masuk',
                'C2' => 'nama',
                'D2' => 'Kelas',
                'E2' => 'pcs awal',
                'F2' => 'gr awal',
                'G2' => 'pcs akhir',
                'H2' => 'gr akhir',
                'I2' => 'eot gr',
                'J2' => 'gr flx',
                'K2' => 'susut',
                'L2' => 'ttl rp',

                'M2' => 'gr eo awal',
                'N2' => 'gr eo akhir',
                'O2' => 'susut',
                'P2' => 'ttl rp',

                'Q2' => 'pcs awal',
                'R2' => 'gr akhir',
                'S2' => 'pcs awal',
                'T2' => 'gr eo akhir',
                'U2' => 'susut',
                'V2' => 'ttl rp',

                'W2' => 'kerja dll',
                'X2' => 'rp denda',

                'Y2' => 'ttl gaji',
                'Z2' => 'rata2',
            ];

            foreach ($koloms as $kolom => $isiKolom) {
                $sheet->setCellValue($kolom, ucwords($isiKolom));
            }

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

            $warnaBg = [
                'A1:L1' => 'D9D9D9',
                'M1:P1' => 'F79646',
                'Q1:V1' => '8DB4E2',

                'L2' => 'FF0000',
                'P2' => 'FF0000',
                'V2' => 'FF0000',
                'W2' => 'FF0000',
                'Y2' => 'FF0000',
            ];
            
            foreach ($warnaBg as $b => $i) {
                $sheet->getStyle($b)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($i);
            }

            $sheet->mergeCells('A1:L1');
            $sheet->mergeCells('M1:P1');
            $sheet->mergeCells('Q1:V1');
            $sheet->mergeCells('X1:Z1');

            $style = $sheet->getStyle('A1:X1');
            $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $style->getFont()->setBold(true);
            $sheet->getStyle('A2:Z2')->applyFromArray($styleBold);

            $TtlRp = 0;
            $eoTtlRp = 0;
            $sortirTtlRp = 0;
            $dllTtlRp = 0;
            $dendaTtlRp = 0;
            $ttlTtlRp = 0;

            $ttlCbtPcsAwal = 0;
            $ttlCbtGrAwal = 0;
            $ttlCbtPcsAkhir = 0;
            $ttlCbtGrAkhir = 0;
            $ttlCbtEotGr = 0;
            $ttlCbtGrFlx = 0;
            $ttlCbtTtlRp = 0;

            $ttlSoritrPcsAwal = 0;
            $ttlSoritrGrAwal = 0;
            $ttlSoritrPcsAkhir = 0;
            $ttlSoritrGrAkhir = 0;
            $ttlSortirRp = 0;

            $ttlEoGrAwal  = 0;
            $ttlEoGrAkhir  = 0;
            $ttlEoRp  = 0;

             $bulanDibayar = date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun))));
            $row = 3;
            $tbl = Cabut::getRekapGlobal($bulan, $tahun, $d->id_pengawas);
            foreach ($tbl as $data) {
                $sheet->setCellValue('A' . $row, $data->pgws)
                    ->setCellValue('B' . $row, $data->hariMasuk)
                    ->setCellValue('C' . $row, $data->nm_anak)
                    ->setCellValue('D' . $row, $data->kelas)
                    ->setCellValue('E' . $row, $data->pcs_awal)
                    ->setCellValue('F' . $row, $data->gr_awal)
                    ->setCellValue('G' . $row, $data->pcs_akhir)
                    ->setCellValue('H' . $row, $data->gr_akhir)
                    ->setCellValue('I' . $row, $data->eot)
                    ->setCellValue('J' . $row, $data->gr_flx);
                $susutCbt = empty($data->gr_akhir) ? 0 : (1 - (($data->gr_akhir + $data->gr_flx) / $data->gr_awal)) * 100;
                $sheet->setCellValue('K' . $row, $susutCbt)
                    ->setCellValue('L' . $row, $data->ttl_rp)

                    ->setCellValue('M' . $row, $data->eo_awal)
                    ->setCellValue('N' . $row, $data->eo_akhir);
                $susutEo =  empty($data->eo_akhir) ? 0 : (1 - ($data->eo_akhir / $data->eo_awal)) * 100;

                $sheet->setCellValue('O' . $row, $susutEo)
                    ->setCellValue('P' . $row, $data->eo_ttl_rp)
                    ->setCellValue('Q' . $row, $data->sortir_pcs_awal)
                    ->setCellValue('R' . $row, $data->sortir_gr_awal)
                    ->setCellValue('S' . $row, $data->sortir_pcs_akhir)
                    ->setCellValue('T' . $row, $data->sortir_gr_akhir);
                $susutSortir = empty($data->sortir_gr_akhir) ? 0 : (1 - ($data->sortir_gr_akhir / $data->sortir_gr_awal)) * 100;

                $sheet->setCellValue('U' . $row, $susutSortir)
                    ->setCellValue('V' . $row, $data->sortir_ttl_rp)
                    ->setCellValue('W' . $row, $data->ttl_rp_dll)
                    ->setCellValue('X' . $row, $data->ttl_rp_denda);
                $ttl = $data->ttl_rp + $data->eo_ttl_rp + $data->sortir_ttl_rp + $data->ttl_rp_dll - $data->ttl_rp_denda;
                $rata = empty($data->hariMasuk) ? 0 : $ttl / $data->hariMasuk;
                $sheet->setCellValue('Y' . $row, $ttl)
                    ->setCellValue('Z' . $row, $rata);

                $ttlCbtPcsAwal += $data->pcs_awal;
                $ttlCbtGrAwal += $data->gr_awal;
                $ttlCbtPcsAkhir += $data->pcs_akhir;
                $ttlCbtGrAkhir += $data->gr_akhir;
                $ttlCbtEotGr += $data->eot;
                $ttlCbtGrFlx += $data->gr_flx;
                $ttlCbtTtlRp += $data->ttl_rp;

                $ttlEoGrAwal  += $data->eo_awal;
                $ttlEoGrAkhir  += $data->eo_akhir;
                $ttlEoRp  += $data->eo_ttl_rp;

                $ttlSoritrPcsAwal += $data->sortir_pcs_awal;
                $ttlSoritrGrAwal += $data->sortir_gr_awal;
                $ttlSoritrPcsAkhir += $data->sortir_pcs_akhir;
                $ttlSoritrGrAkhir += $data->sortir_gr_akhir;
                $ttlSortirRp += $data->sortir_ttl_rp;

                $TtlRp += $data->ttl_rp;
                $eoTtlRp += $data->eo_ttl_rp;
                $sortirTtlRp += $data->sortir_ttl_rp;
                $dllTtlRp += $data->ttl_rp_dll;
                $dendaTtlRp += $data->ttl_rp_denda;
                $ttlTtlRp += $ttl;

                $row++;
            }

            $rowTotal = $row;


            $sheet->setCellValue('A' . $rowTotal, 'TOTAL');
            $sheet->setCellValue('E' . $rowTotal, $ttlCbtPcsAwal);
            $sheet->setCellValue('F' . $rowTotal, $ttlCbtGrAwal);
            $sheet->setCellValue('G' . $rowTotal, $ttlCbtPcsAkhir);
            $sheet->setCellValue('H' . $rowTotal, $ttlCbtGrAkhir);
            $sheet->setCellValue('I' . $rowTotal, $ttlCbtEotGr);
            $sheet->setCellValue('J' . $rowTotal, $ttlCbtGrFlx);
            $sheet->setCellValue('L' . $rowTotal, $ttlCbtTtlRp);

            $sheet->setCellValue('M' . $rowTotal, $ttlEoGrAwal);
            $sheet->setCellValue('N' . $rowTotal, $ttlEoGrAkhir);
            $sheet->setCellValue('P' . $rowTotal, $ttlEoRp);



            $sheet->setCellValue('Q' . $rowTotal, $ttlSoritrPcsAwal);
            $sheet->setCellValue('R' . $rowTotal, $ttlSoritrGrAwal);
            $sheet->setCellValue('S' . $rowTotal, $ttlSoritrPcsAkhir);
            $sheet->setCellValue('T' . $rowTotal, $ttlSoritrGrAkhir);
            $sheet->setCellValue('V' . $rowTotal, $ttlSortirRp);

            $sheet->setCellValue('W' . $rowTotal, $dllTtlRp);
            $sheet->setCellValue('X' . $rowTotal, $dendaTtlRp);
            $sheet->setCellValue('Y' . $rowTotal, $ttlTtlRp);

            $sheet->getStyle("A$rowTotal:Z$rowTotal")->applyFromArray($styleBold);

            $baris = $rowTotal - 1;
            $sheet->getStyle('A2:Z' . $baris)->applyFromArray($styleBaris);
        }
        $writer = new Xlsx($spreadsheet);

        // Menggunakan response untuk mengirimkan file ke browser
        $fileName = "Gaji Export Global $bulanDibayar $tahun";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
            ]
        );

        // $view = 'home.cabut.export_global';
        // $tbl = Cabut::getRekapGlobal($bulan, $tahun, $id_pengawas);
        // $fileName = "Export Rekap Global " . auth()->user()->name;
        // return Excel::download(new CabutGlobalExport($tbl, $view), "$fileName.xlsx");
    }
    public function cekBgSisa($sheet, $pcsBk, $pcsAwal, $grBk, $grAwal, $row)
    {
        $cekSisaPcs = $pcsBk - $pcsAwal > 0 ? true : false;
        $cekSisaGr = $grBk - $grAwal > 0 ? true : false;

        if ($cekSisaGr || $cekSisaPcs) {
            return $sheet->getStyle("A$row:P$row")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
        }
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
            $ttlPcsBk = 0;
            $ttlGrBk = 0;

            $ttlPcsAwal = 0;
            $ttlGrAwal = 0;
            $ttlPcsAkhir = 0;
            $ttlGrAkhir = 0;

            $ttlFlx = 0;
            $ttlEot = 0;
            $ttlSusut = 0;

            $ttlRp = 0;

            $ttlPcsSisa = 0;
            $ttlGrSisa = 0;
            // cabut
            $bulanDibayar = date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun))));
            $row = 2;
            $cabut = Cabut::queryRekap($d->id_pengawas, $bulan, $tahun);
            foreach ($cabut as $data) {
                $sheet->setCellValue('A' . $row, $data->no_box);
                $sheet->setCellValue('B' . $row, $data->pcs_bk);
                $sheet->setCellValue('C' . $row, $data->gr_bk);
                $sheet->setCellValue('D' . $row, $bulanDibayar);
                $sheet->setCellValue('E' . $row, $data->pengawas);
                $sheet->setCellValue('F' . $row, $data->pcs_awal);
                $sheet->setCellValue('G' . $row, $data->gr_awal);
                $sheet->setCellValue('H' . $row, $data->pcs_akhir);
                $sheet->setCellValue('I' . $row, $data->gr_akhir);
                $sheet->setCellValue('J' . $row, $data->eot);
                $sheet->setCellValue('K' . $row, $data->gr_flx);
                $susut = empty($data->gr_awal) ? 0 : (1 - ($data->gr_flx + $data->gr_akhir) / $data->gr_awal) * 100;
                $sheet->setCellValue('L' . $row, number_format($susut, 0));
                $sheet->setCellValue('M' . $row, $data->ttl_rp);
                $sheet->setCellValue('N' . $row, $data->pcs_bk - $data->pcs_awal);
                $sheet->setCellValue('O' . $row, $data->gr_bk - $data->gr_awal);
                $this->cekBgSisa($sheet, $data->pcs_bk, $data->pcs_awal, $data->gr_bk, $data->gr_awal,  $row);
                $sheet->setCellValue('P' . $row, $data->kategori);

                $ttlPcsBk += $data->pcs_bk;
                $ttlGrBk += $data->gr_bk;

                $ttlPcsAwal += $data->pcs_awal;
                $ttlGrAwal += $data->gr_awal;
                $ttlPcsAkhir += $data->pcs_akhir;
                $ttlGrAkhir += $data->gr_akhir;

                $ttlFlx += $data->gr_flx;
                $ttlEot += $data->eot;

                $ttlRp += $data->rupiah;

                $ttlPcsSisa += $data->pcs_bk - $data->pcs_awal;
                $ttlGrSisa += $data->gr_bk - $data->gr_awal;
                $row++;
            }

            // eo
            $rowEo = $row;
            $eo = Eo::queryRekap($d->id_pengawas);
            foreach ($eo as $data) {
                $sheet->setCellValue('A' . $rowEo, $data->no_box);
                $sheet->setCellValue('B' . $rowEo, 0);
                $sheet->setCellValue('C' . $rowEo, $data->gr_bk);
                $sheet->setCellValue('D' . $rowEo, $bulanDibayar);
                $sheet->setCellValue('E' . $rowEo, $d->name);
                $sheet->setCellValue('F' . $rowEo, 0);
                $sheet->setCellValue('G' . $rowEo, $data->gr_eo_awal);
                $sheet->setCellValue('H' . $rowEo, 0);
                $sheet->setCellValue('I' . $rowEo, $data->gr_eo_akhir);
                $sheet->setCellValue('J' . $rowEo, 0);
                $sheet->setCellValue('K' . $rowEo, 0);
                $susut = empty($data->gr_eo_awal) ? 0 : (1 - ($data->gr_eo_akhir / $data->gr_eo_awal)) * 100;
                $sheet->setCellValue('L' . $rowEo, number_format($susut, 0));
                $sheet->setCellValue('M' . $rowEo, $data->rupiah);
                $sheet->setCellValue('N' . $rowEo, 0);
                $sheet->setCellValue('O' . $rowEo, $data->gr_bk - $data->gr_eo_awal);

                $this->cekBgSisa(
                    $sheet,
                    0,
                    0,
                    $data->gr_bk,
                    $data->gr_eo_awal,
                    $rowEo
                );

                $sheet->setCellValue('P' . $rowEo, 'Eo');

                $ttlPcsBk += 0;
                $ttlGrBk += $data->gr_bk;

                $ttlPcsAwal += 0;
                $ttlGrAwal += $data->gr_eo_awal;
                $ttlPcsAkhir += 0;
                $ttlGrAkhir += $data->gr_eo_akhir;

                $ttlFlx += 0;
                $ttlEot += 0;

                $ttlRp += $data->rupiah;

                $ttlPcsSisa += 0;
                $ttlGrSisa += $data->gr_bk - $data->gr_eo_awal;
                $rowEo++;
            }

            // sortir
            $rowSortir = $rowEo;
            $sortir = Sortir::queryRekap($d->id_pengawas);
            foreach ($sortir as $data) {
                $sheet->setCellValue('A' . $rowSortir, $data->no_box);
                $sheet->setCellValue('B' . $rowSortir, $data->pcs_bk);
                $sheet->setCellValue('C' . $rowSortir, $data->gr_bk);
                $sheet->setCellValue('D' . $rowSortir, $bulanDibayar);
                $sheet->setCellValue('E' . $rowSortir, $data->pengawas);
                $sheet->setCellValue('F' . $rowSortir, $data->pcs_awal);
                $sheet->setCellValue('G' . $rowSortir, $data->gr_awal);
                $sheet->setCellValue('H' . $rowSortir, $data->pcs_akhir);
                $sheet->setCellValue('I' . $rowSortir, $data->gr_akhir);
                $sheet->setCellValue('J' . $rowSortir, 0);
                $sheet->setCellValue('K' . $rowSortir, 0);
                $susut = empty($data->gr_awal) ? 0 : (1 - $data->gr_akhir / $data->gr_awal) * 100;
                $sheet->setCellValue('L' . $rowSortir, number_format($susut, 0));
                $sheet->setCellValue('M' . $rowSortir, $data->rupiah);
                $sheet->setCellValue('N' . $rowSortir, $data->pcs_bk - $data->pcs_awal);
                $sheet->setCellValue('O' . $rowSortir, $data->gr_bk - $data->gr_awal);
                $sheet->setCellValue('P' . $rowSortir, $data->kategori);
                $this->cekBgSisa(
                    $sheet,
                    $data->pcs_bk,
                    $data->pcs_awal,
                    $data->gr_bk,
                    $data->gr_awal,
                    $rowEo
                );
                $ttlPcsBk += $data->pcs_bk;
                $ttlGrBk += $data->gr_bk;

                $ttlPcsAwal += $data->pcs_awal;
                $ttlGrAwal += $data->gr_awal;
                $ttlPcsAkhir += $data->pcs_akhir;
                $ttlGrAkhir += $data->gr_akhir;

                $ttlFlx += 0;
                $ttlEot += 0;

                $ttlRp += $data->rupiah;

                $ttlPcsSisa += $data->pcs_bk - $data->pcs_awal;
                $ttlGrSisa += $data->gr_bk - $data->gr_awal;

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
            $sheet->setCellValue('D' . $rowDll, $bulanDibayar);
            $sheet->setCellValue('E' . $rowDll, $d->name);
            $sheet->setCellValue('M' . $rowDll, $rupiahDll);
            $ttlRp += $rupiahDll;


            $ttlSusut = empty($ttlGrAwal) ? 0 : (1 - ($ttlFlx + $ttlGrAkhir) / $ttlGrAwal) * 100;
            $rowTotal = $rowDll + 1;
            $sheet->setCellValue('A' . $rowTotal, 'TOTAL');
            $sheet->setCellValue('B' . $rowTotal, $ttlPcsBk);
            $sheet->setCellValue('C' . $rowTotal, $ttlGrBk);
            $sheet->setCellValue('D' . $rowTotal, $bulanDibayar);
            $sheet->setCellValue('E' . $rowTotal, $d->name);
            $sheet->setCellValue('F' . $rowTotal, $ttlPcsAwal);
            $sheet->setCellValue('G' . $rowTotal, $ttlGrAwal);
            $sheet->setCellValue('H' . $rowTotal, $ttlPcsAkhir);
            $sheet->setCellValue('I' . $rowTotal, $ttlGrAkhir);
            $sheet->setCellValue('J' . $rowTotal, $ttlEot);
            $sheet->setCellValue('K' . $rowTotal, $ttlFlx);
            $sheet->setCellValue('L' . $rowTotal, number_format($ttlSusut, 0));
            $sheet->setCellValue('M' . $rowTotal, $ttlRp);
            $sheet->setCellValue('N' . $rowTotal, $ttlPcsSisa);
            $sheet->setCellValue('O' . $rowTotal, $ttlGrSisa);
            $sheet->getStyle("A$rowTotal:P$rowTotal")->applyFromArray($styleBold);

            $rowDenda = $rowTotal + 1;
            $denda = DB::selectOne("SELECT sum(nominal) as rupiah FROM `tb_denda`
            WHERE bulan_dibayar = '$bulan' AND YEAR(tgl) = '$tahun' AND admin = '$d->name'
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
        $fileName = "Gaji Sarang $bulanDibayar $tahun Kasih Ibu Linda";
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xlsx"',
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
