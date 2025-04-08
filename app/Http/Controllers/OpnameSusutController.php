<?php

namespace App\Http\Controllers;

use App\Models\BalanceModel;
use App\Models\Cabut;
use App\Models\CabutOpnameModel;
use App\Models\OpnameNewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OpnameSusutController extends Controller
{
    public function index(Request $request)
    {
        $pgws_cabut = CabutOpnameModel::cabut_susut();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.index', $data);
    }


    public function detail_cabut(Request $r)
    {
        $data = [
            'title' => 'Data Opname',
            'tipe' => $r->tipe,
            'nm_pengawas' => DB::table('users')->where('id', $r->id_pengawas)->first()->name,
            'box_stock' => CabutOpnameModel::cabut_susut_detail($r->id_pengawas, $r->tipe),
        ];
        return view('home.opnamesusut.detail_cabut', $data);
    }

    public function cetak(Request $request)
    {
        $pgws_cabut = CabutOpnameModel::cetak_susut_tampilan();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.cetak', $data);
    }
    public function sortir(Request $request)
    {
        $pgws_cabut = CabutOpnameModel::sortir_tampilan();
        $data = [
            'title' => 'Data Opname',
            'pgws_cabut' => $pgws_cabut,

        ];
        return view('home.opnamesusut.sortir', $data);
    }


    public function costPartai(Request $r)
    {
        $bk = DB::select("SELECT a.nm_partai FROM bk as a  where a.kategori = 'cabut' and a.baru = 'baru' and a.no_box != 9999 group by a.nm_partai order by a.nm_partai ASC;");
        $data = [
            'title' => 'Cost Partai',
            'partai' => $bk,
        ];
        return view('home.opnamesusut.cost_partai', $data);
    }


    public function getCostpartai(Request $r)
    {
        $bk = DB::selectOne("SELECT a.nm_partai, a.tipe, a.ket, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a 
        where a.kategori = 'cabut' and a.baru = 'baru' and a.nm_partai = '$r->partai';");

        $data = [
            'bk' => $bk,
            'cabut' => CabutOpnameModel::cabutPartai($r->partai),
            'eo' => CabutOpnameModel::eotPartai($r->partai),
            'cetak' => CabutOpnameModel::cetakPartai($r->partai),
            'sortir' => CabutOpnameModel::sortirPartai($r->partai),
            'grading' => CabutOpnameModel::gradingPartai($r->partai),
            'pengiriman' => CabutOpnameModel::pengiriman($r->partai),
        ];
        return view('home.opnamesusut.getcost_partai', $data);
    }

    public function exportCostperpartai(Request $r)
    {
        $bk = DB::selectOne("SELECT a.nm_partai, a.tipe, a.ket, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a 
        where a.kategori = 'cabut' and a.baru = 'baru' and a.nm_partai = '$r->partai';");

        $data = [
            'bk' => $bk,
            'cabut' => CabutOpnameModel::cabutPartai($r->partai),
            'eo' => CabutOpnameModel::eotPartai($r->partai),
            'cetak' => CabutOpnameModel::cetakPartai($r->partai),
            'sortir' => CabutOpnameModel::sortirPartai($r->partai),
            'grading' => CabutOpnameModel::gradingPartai($r->partai),
            'pengiriman' => CabutOpnameModel::pengiriman($r->partai),
        ];
        return view('home.opnamesusut.getcost_partai_excel', $data);
    }

    public function getDetailbkpartai(Request $r)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => CabutOpnameModel::bkPaartaiDetail($r->nm_partai),
        ];
        return view('home.opnamesusut.detail_bk_partai', $data);
    }
    public function getDetailCabutpartai(Request $r)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => CabutOpnameModel::cabutPartaiDetail($r->nm_partai),
        ];
        return view('home.opnamesusut.detail_cabut_partai', $data);
    }
    public function getDetailCetakpartai(Request $r)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => CabutOpnameModel::cetakPartaiDetail($r->nm_partai),
        ];
        return view('home.opnamesusut.detail_cetak_partai', $data);
    }
    public function getDetailSortirpartai(Request $r)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => CabutOpnameModel::sortirPartaiDetail($r->nm_partai),
        ];
        return view('home.opnamesusut.detail_sortir_partai', $data);
    }

    public function detailGrade(Request $r)
    {
        $data = [
            'grade' => CabutOpnameModel::table_grade($r->nm_partai),
            'partai' => $r->nm_partai
        ];
        return view('home.opnamesusut.detail_grade', $data);
    }
    public function detailGrade2(Request $r)
    {
        $data = [
            'grade' => CabutOpnameModel::table_grade2($r->nm_partai),
            'partai' => $r->nm_partai
        ];
        return view('home.opnamesusut.detail_grade2', $data);
    }

    public function exportCostpartai(Request $r)
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
        $dataBulan = DB::table('oprasional')->groupBy('bulan')->selectRaw('bulan, tahun')->get();
        $bk = DB::select("SELECT a.nm_partai, a.tipe, a.ket, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.gr_awal * a.hrga_satuan) as ttl_rp
        FROM bk as a 
        where a.kategori = 'cabut' and a.baru = 'baru' and a.no_box != 9999 group by a.nm_partai ");



        $title = 'Cost Partai All';
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "$title");
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("A1:R2")->applyFromArray($style_atas);
        $sheet->setCellValue('A1', 'Kerja');
        $sheet->setCellValue('N1', 'Gudang');
        $sheet->setCellValue('R1', 'Pencocokan');
        $sheet->setCellValue('A2', 'Partai');
        $sheet->setCellValue('B2', 'Ket');
        $sheet->setCellValue('C2', 'Grade');
        $sheet->setCellValue('D2', 'Pcs Awal');
        $sheet->setCellValue('E2', 'Gr Awal');
        $sheet->setCellValue('F2', 'Pcs Akhir');
        $sheet->setCellValue('G2', 'Gr Akhir');
        $sheet->setCellValue('H2', 'Susut%');
        $sheet->setCellValue('I2', 'Modal Rp');
        $sheet->setCellValue('J2', 'Cost Kerja');
        $sheet->setCellValue('K2', 'Cost Operasional');
        $sheet->setCellValue('L2', 'Modal Tambah Cost');
        $sheet->setCellValue('M2', 'Modal rata-rata');

        $sheet->setCellValue('N2', 'Sisa Pcs');
        $sheet->setCellValue('O2', 'Sisa Gr');
        $sheet->setCellValue('P2', 'Total Rp');
        $sheet->setCellValue('Q2', 'Rata-rata');
        $sheet->setCellValue('R2', '');

        $kolom = 3;
        foreach ($bk as $i => $d) {
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, "Bk Awal");
            $sheet->setCellValue('C' . $kolom, "$d->tipe");
            $sheet->setCellValue('D' . $kolom, "$d->pcs_awal");
            $sheet->setCellValue('E' . $kolom, "$d->gr_awal");
            $sheet->setCellValue('F' . $kolom, "0");
            $sheet->setCellValue('G' . $kolom, "0");
            $sheet->setCellValue('H' . $kolom, "0");
            $sheet->setCellValue('I' . $kolom, "$d->ttl_rp");
            $sheet->setCellValue('J' . $kolom, "0");
            $sheet->setCellValue('K' . $kolom, "0");
            $sheet->setCellValue('L' . $kolom, "$d->ttl_rp");
            $sheet->setCellValue('M' . $kolom, round($d->ttl_rp / $d->gr_awal, 0));
            $kolom++;
            $cabut = CabutOpnameModel::cabutPartai($d->nm_partai);
            $eo = CabutOpnameModel::eotPartai($d->nm_partai);

            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, "Cabut");
            $sheet->setCellValue('C' . $kolom, "$d->tipe");
            $sheet->setCellValue('D' . $kolom, $cabut->pcs ?? 0);
            $sheet->setCellValue('E' . $kolom, ($cabut->gr_awal ?? 0) + ($eo->gr_eo_awal ?? 0));
            $sheet->setCellValue('F' . $kolom, $cabut->pcs ?? 0);
            $sheet->setCellValue('G' . $kolom, ($cabut->gr ?? 0) + ($eo->gr ?? 0));
            $sheet->setCellValue('H' . $kolom, round((1 - (($cabut->gr ?? 0) + ($eo->gr ?? 0)) / (($cabut->gr_awal ?? 0) + ($eo->gr_eo_awal ?? 0))) * 100, 0) . "%");
            $sheet->setCellValue('I' . $kolom, ($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0));
            $sheet->setCellValue('J' . $kolom, ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0));
            $sheet->setCellValue('K' . $kolom, 0);
            $sheet->setCellValue('L' . $kolom, ($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0));
            $sheet->setCellValue('M' . $kolom, ($cabut->gr ?? 0) + ($eo->gr ?? 0) == 0 ? 0 : round((($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0)) / (($cabut->gr ?? 0) + ($eo->gr ?? 0)), 0));
            $sheet->setCellValue('N' . $kolom, ($d->pcs_awal ?? 0) - ($cabut->pcs ?? 0));
            $sheet->setCellValue('O' . $kolom, $d->gr_awal - ($cabut->gr_awal ?? 0) - ($eo->gr_eo_awal ?? 0));
            $sheet->setCellValue('P' . $kolom, $d->ttl_rp - ($cabut->modal_rp ?? 0) - ($eo->modal_rp ?? 0));
            $sheet->setCellValue('Q' . $kolom, $d->gr_awal - ($cabut->gr_awal ?? 0) - ($eo->gr_eo_awal ?? 0) == 0 ? 0 : round(($d->ttl_rp - ($cabut->modal_rp ?? 0) - ($eo->modal_rp ?? 0)) / ($d->gr_awal - ($cabut->gr_awal ?? 0) - ($eo->gr_eo_awal ?? 0)), 0));
            $sheet->setCellValue('R' . $kolom, ($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($d->ttl_rp - ($cabut->modal_rp ?? 0) - ($eo->modal_rp ?? 0)));
            $kolom++;

            $cetak = CabutOpnameModel::cetakPartai($d->nm_partai);

            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, "Cetak");
            $sheet->setCellValue('C' . $kolom, "$d->tipe");
            $sheet->setCellValue('D' . $kolom, $cetak->pcs_tdk + $cetak->pcs);
            $sheet->setCellValue('E' . $kolom, $cetak->gr_awal);
            $sheet->setCellValue('F' . $kolom, $cetak->pcs_tdk + $cetak->pcs);
            $sheet->setCellValue('G' . $kolom, $cetak->gr_tdk + $cetak->gr);
            $sheet->setCellValue('H' . $kolom, empty($cetak->gr_awal) ? 0 : round((1 - ($cetak->gr_tdk + $cetak->gr) / $cetak->gr_awal) * 100, 0) . "%");
            $sheet->setCellValue('I' . $kolom, $cetak->modal_rp + $cetak->cost_kerja);
            $sheet->setCellValue('J' . $kolom, $cetak->ttl_rp);
            $sheet->setCellValue('K' . $kolom, 0);
            $sheet->setCellValue('L' . $kolom, $cetak->modal_rp + $cetak->cost_kerja + $cetak->ttl_rp);
            $sheet->setCellValue('M' . $kolom, $cetak->gr_tdk + $cetak->gr == 0 ? 0 : round(($cetak->modal_rp + $cetak->cost_kerja + $cetak->ttl_rp) / ($cetak->gr_tdk + $cetak->gr), 0));
            $sheet->setCellValue('N' . $kolom, ($cabut->pcs ?? 0) - ($cetak->pcs_tdk ?? 0) - ($cetak->pcs ?? 0));
            $sheet->setCellValue('O' . $kolom, ($cabut->gr ?? 0) + ($eo->gr ?? 0) - $cetak->gr_awal);
            $sheet->setCellValue('P' . $kolom, ($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) - ($cetak->modal_rp + $cetak->cost_kerja));
            $sheet->setCellValue('Q' . $kolom, ($cabut->gr ?? 0) + ($eo->gr ?? 0) - $cetak->gr_awal == 0 ? 0 : round((($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) - ($cetak->modal_rp + $cetak->cost_kerja)) / (($cabut->gr ?? 0) + ($eo->gr ?? 0) - $cetak->gr_awal), 0));
            $sheet->setCellValue('R' . $kolom, ($cabut->modal_rp ?? 0) + ($eo->modal_rp ?? 0) + ($cabut->ttl_rp ?? 0) + ($eo->ttl_rp ?? 0) - ($cetak->modal_rp + $cetak->cost_kerja) + ($cetak->modal_rp + $cetak->cost_kerja));
            $kolom++;

            $sortir = CabutOpnameModel::sortirPartai($d->nm_partai);
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, "Sortir");
            $sheet->setCellValue('C' . $kolom, "$d->tipe");
            $sheet->setCellValue('D' . $kolom, $sortir->pcs ?? 0);
            $sheet->setCellValue('E' . $kolom, $sortir->gr_awal ?? 0);
            $sheet->setCellValue('F' . $kolom, $sortir->pcs ?? 0);
            $sheet->setCellValue('G' . $kolom, $sortir->gr ?? 0);
            $sheet->setCellValue('H' . $kolom, empty($sortir->gr_awal) ? 0 : round((1 - $sortir->gr / $sortir->gr_awal) * 100, 0) . "%");
            $sheet->setCellValue('I' . $kolom, empty($sortir->modal_rp) ? 0 : round($sortir->modal_rp + $sortir->cost_kerja, 0));
            $sheet->setCellValue('J' . $kolom, $sortir->ttl_rp ?? 0);
            $sheet->setCellValue('K' . $kolom, 0);
            $sheet->setCellValue('L' . $kolom, empty($sortir->modal_rp) ? 0 : round($sortir->modal_rp + $sortir->cost_kerja + $sortir->ttl_rp, 0));
            $sheet->setCellValue('M' . $kolom, empty($sortir->gr) ? 0 : round(($sortir->modal_rp + $sortir->cost_kerja + $sortir->ttl_rp) / $sortir->gr, 0));
            $sheet->setCellValue('N' . $kolom, ($cetak->pcs_tdk ?? 0) + ($cetak->pcs ?? 0) - ($sortir->pcs ?? 0));
            $sheet->setCellValue('O' . $kolom, ($cetak->gr_tdk ?? 0) + ($cetak->gr ?? 0) - ($sortir->gr_awal ?? 0));
            $sheet->setCellValue('P' . $kolom, ($cetak->modal_rp ?? 0) + ($cetak->cost_kerja ?? 0) + ($cetak->ttl_rp ?? 0) - (($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0)));
            $pembagi = ($cetak->gr_tdk ?? 0) + ($cetak->gr ?? 0) - ($sortir->gr_awal ?? 0);

            $sheet->setCellValue('Q' . $kolom, $pembagi == 0 ? 0 : round((($cetak->modal_rp ?? 0) + ($cetak->cost_kerja ?? 0) + ($cetak->ttl_rp ?? 0) - (($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0))) / (($cetak->gr_tdk ?? 0) + ($cetak->gr ?? 0) - ($sortir->gr_awal ?? 0)), 0));
            $sheet->setCellValue('R' . $kolom, ($cetak->modal_rp ?? 0) + ($cetak->cost_kerja ?? 0) + ($cetak->ttl_rp ?? 0) - (($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0)) + (($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0)));
            $kolom++;

            $grading = CabutOpnameModel::gradingPartai($d->nm_partai);
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, "Grading");
            $sheet->setCellValue('C' . $kolom, "$d->tipe");
            $sheet->setCellValue('D' . $kolom, $grading->pcs);
            $sheet->setCellValue('E' . $kolom, $grading->gr);
            $sheet->setCellValue('F' . $kolom, $grading->pcs);
            $sheet->setCellValue('G' . $kolom, $grading->gr);
            $sheet->setCellValue('H' . $kolom,  "0%");
            $sheet->setCellValue('I' . $kolom, $grading->cost_bk + $grading->cost_kerja);
            $sheet->setCellValue('J' . $kolom, 0);
            $sheet->setCellValue('K' . $kolom, $grading->cost_op);
            $sheet->setCellValue('L' . $kolom, $grading->cost_bk + $grading->cost_kerja + $grading->cost_op);
            $sheet->setCellValue('M' . $kolom, empty($grading->gr) ? 0 : round(($grading->cost_bk + $grading->cost_kerja + $grading->cost_op) / $grading->gr, 0));

            $sheet->setCellValue('N' . $kolom, ($sortir->pcs ?? 0) - ($grading->pcs ?? 0));
            $sheet->setCellValue('O' . $kolom, ($sortir->gr ?? 0) - ($grading->gr ?? 0));
            $sheet->setCellValue('P' . $kolom, ($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0) + ($sortir->ttl_rp ?? 0) - (($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0)));
            $grading_gr = $grading->gr ?? 0;
            $sortir_gr = $sortir->gr ?? 0;
            $selisih_gr = $sortir_gr - $grading_gr;

            $sheet->setCellValue('Q' . $kolom, $selisih_gr == 0
                ? 0
                : number_format(
                    (($sortir->modal_rp ?? 0) +
                        ($sortir->cost_kerja ?? 0) +
                        ($sortir->ttl_rp ?? 0) -
                        (($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0))) /
                        $selisih_gr,
                    0,
                ));
            $sheet->setCellValue('R' . $kolom, ($sortir->modal_rp ?? 0) + ($sortir->cost_kerja ?? 0) + ($sortir->ttl_rp ?? 0) - (($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0)) + (($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0)));
            $kolom++;

            $pengiriman = CabutOpnameModel::pengiriman($d->nm_partai);
            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, "Sisa Pengiriman");
            $sheet->setCellValue('C' . $kolom, "$d->tipe");
            $sheet->setCellValue('D' . $kolom, $pengiriman->pcs);
            $sheet->setCellValue('E' . $kolom, $pengiriman->gr);
            $sheet->setCellValue('F' . $kolom, $pengiriman->pcs);
            $sheet->setCellValue('G' . $kolom, $pengiriman->gr);
            $sheet->setCellValue('H' . $kolom,  "0%");
            $sheet->setCellValue('I' . $kolom, $pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op);
            $sheet->setCellValue('J' . $kolom, 0);
            $sheet->setCellValue('K' . $kolom, 0);
            $sheet->setCellValue('L' . $kolom, $pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op);
            $sheet->setCellValue('M' . $kolom, empty($pengiriman->gr) ? 0 : round(($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op) / $pengiriman->gr, 0));

            $sheet->setCellValue('N' . $kolom, ($grading->pcs ?? 0) - ($pengiriman->pcs ?? 0));

            $sheet->setCellValue('O' . $kolom, ($grading->gr ?? 0) - ($pengiriman->gr ?? 0));


            $sheet->setCellValue('P' . $kolom, ($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0) + ($grading->cost_op ?? 0) - (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0)));


            $sheet->setCellValue('Q' . $kolom, $grading->gr - $pengiriman->gr == 0 ? 0 : round((($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0) + ($grading->cost_op ?? 0) - (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0))) / (($grading->gr ?? 0) - ($pengiriman->gr ?? 0)), 0));

            $sheet->setCellValue('R' . $kolom, ($grading->cost_bk ?? 0) + ($grading->cost_kerja ?? 0) + ($grading->cost_op ?? 0) - (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0)) + (($pengiriman->cost_bk ?? 0) + ($pengiriman->cost_kerja ?? 0) + ($pengiriman->cost_op ?? 0)));
            $kolom++;

            $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
            $sheet->setCellValue('B' . $kolom, "Sudah Terkirim");
            $sheet->setCellValue('C' . $kolom, "$d->tipe");
            $sheet->setCellValue('D' . $kolom, 0);
            $sheet->setCellValue('E' . $kolom, 0);
            $sheet->setCellValue('F' . $kolom, 0);
            $sheet->setCellValue('G' . $kolom, 0);
            $sheet->setCellValue('H' . $kolom,  "0%");
            $sheet->setCellValue('I' . $kolom, 0);
            $sheet->setCellValue('J' . $kolom, 0);
            $sheet->setCellValue('K' . $kolom, 0);
            $sheet->setCellValue('L' . $kolom, 0);

            $sheet->setCellValue('N' . $kolom, $pengiriman->pcs);

            $sheet->setCellValue('O' . $kolom, $pengiriman->gr);


            $sheet->setCellValue('P' . $kolom, $pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op);


            $sheet->setCellValue('Q' . $kolom, empty($pengiriman->gr) ? 0 : number_format(($pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_op) / $pengiriman->gr, 0));

            $sheet->setCellValue('R' . $kolom, 0);
            $kolom++;
        }


        $sheet->getStyle("A1:R" . $kolom - 1)->applyFromArray($style);
        $namafile = "Cost Per Partai.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
    // public function exportCostperpartai(Request $r)
    // {
    //     $style_atas = array(
    //         'font' => [
    //             'bold' => true, // Mengatur teks menjadi tebal
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //             ]
    //         ],
    //     );

    //     $style = [
    //         'borders' => [
    //             'alignment' => [
    //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             ],
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //             ],
    //         ],
    //     ];
    //     $style_footer = [
    //         'font' => [
    //             'bold' => true, // Mengatur teks menjadi tebal
    //         ],
    //         'borders' => [
    //             'alignment' => [
    //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //             ],
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //             ],
    //         ],
    //     ];
    //     $spreadsheet = new Spreadsheet();
    //     $dataBulan = DB::table('oprasional')->groupBy('bulan')->selectRaw('bulan, tahun')->get();

    //     $bk = DB::select("SELECT a.nm_partai, a.tipe, a.ket, sum(a.pcs_awal) as pcs_awal, sum(a.gr_awal) as gr_awal, sum(a.gr_awal * a.hrga_satuan) as ttl_rp
    //     FROM bk as a 
    //     where a.kategori = 'cabut' and a.baru = 'baru' and a.no_box != 9999 and a.nm_partai = '$r->partai'
    //     group by a.nm_partai ");



    //     $title = 'Cost Partai';
    //     $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "$title");
    //     $sheet  = $spreadsheet->addSheet($worksheet);
    //     $sheet->getStyle("A1:M1")->applyFromArray($style_atas);
    //     $sheet->setCellValue('A1', 'Partai');
    //     $sheet->setCellValue('B1', 'Ket');
    //     $sheet->setCellValue('C1', 'Grade');
    //     $sheet->setCellValue('D1', 'Pcs');
    //     $sheet->setCellValue('E1', 'Gr');
    //     $sheet->setCellValue('F1', 'Rp');
    //     $sheet->setCellValue('G1', 'Pcs tidak cetak');
    //     $sheet->setCellValue('H1', 'Gr tidak cetak');
    //     $sheet->setCellValue('I1', 'Pcs akhir');
    //     $sheet->setCellValue('J1', 'Gr akhir');
    //     $sheet->setCellValue('K1', 'Susut');
    //     $sheet->setCellValue('L1', 'Cost Rp');
    //     $sheet->setCellValue('M1', 'Rp/gr');

    //     $kolom = 2;
    //     foreach ($bk as $i => $d) {
    //         $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
    //         $sheet->setCellValue('B' . $kolom, 'bk');
    //         $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
    //         $sheet->setCellValue('D' . $kolom, round($d->pcs_awal, 0));
    //         $sheet->setCellValue('E' . $kolom, round($d->gr_awal, 0));
    //         $sheet->setCellValue('F' . $kolom, round($d->ttl_rp, 0));
    //         $sheet->setCellValue('G' . $kolom, 0);
    //         $sheet->setCellValue('H' . $kolom, 0);
    //         $sheet->setCellValue('I' . $kolom, 0);
    //         $sheet->setCellValue('J' . $kolom, 0);
    //         $sheet->setCellValue('K' . $kolom, 0);
    //         $sheet->setCellValue('L' . $kolom, round($d->ttl_rp, 0));
    //         $sheet->setCellValue('M' . $kolom, empty($d->gr_awal) ? 0 : round($d->ttl_rp / $d->gr_awal, 0));

    //         $kolom++;

    //         $cabut = CabutOpnameModel::cabutPartai($d->nm_partai);
    //         $eo = CabutOpnameModel::eotPartai($d->nm_partai);

    //         $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
    //         $sheet->setCellValue('B' . $kolom, 'cabut');
    //         $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
    //         $sheet->setCellValue('D' . $kolom, 0);
    //         $sheet->setCellValue('E' . $kolom, 0);
    //         $sheet->setCellValue('F' . $kolom, 0);
    //         $sheet->setCellValue('G' . $kolom, 0);
    //         $sheet->setCellValue('H' . $kolom, 0);
    //         $ttl_rp_cabut = $cabut->ttl_rp ?? 0;
    //         $ttl_rp_eo = $eo->ttl_rp ?? 0;

    //         $gr_eo = $eo->gr ?? 0;
    //         $gr_cabut =  $cabut->gr ?? 0;
    //         $gr_awal_cabut = $cabut->gr_awal ?? 0;
    //         $gr_eo_awal = $eo->gr_eo_awal ?? 0;

    //         $sheet->setCellValue('I' . $kolom, round($cabut->pcs ?? 0, 0));
    //         $sheet->setCellValue('J' . $kolom, round($gr_cabut + $gr_eo, 0));
    //         $sheet->setCellValue('K' . $kolom, round((1 - ($gr_cabut + $gr_eo) / ($gr_awal_cabut + $gr_eo_awal)) * 100, 0));
    //         $sheet->setCellValue('L' . $kolom, round($ttl_rp_cabut + $ttl_rp_eo, 0));
    //         $sheet->setCellValue('M' . $kolom, "=SUM(L2:L3)/J3");
    //         $kolom++;

    //         $cetak = CabutOpnameModel::cetakPartai($d->nm_partai);
    //         $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
    //         $sheet->setCellValue('B' . $kolom, 'cetak');
    //         $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
    //         $sheet->setCellValue('D' . $kolom, 0);
    //         $sheet->setCellValue('E' . $kolom, 0);
    //         $sheet->setCellValue('F' . $kolom, 0);
    //         $sheet->setCellValue('G' . $kolom, round($cetak->pcs_tdk, 0));
    //         $sheet->setCellValue('H' . $kolom, round($cetak->gr_tdk, 0));
    //         $sheet->setCellValue('I' . $kolom, round($cetak->pcs ?? 0, 0));
    //         $sheet->setCellValue('J' . $kolom, round($cetak->gr ?? 0, 0));
    //         $sheet->setCellValue('K' . $kolom, round((1 - $cetak->gr / $cetak->gr_awal) * 100, 0));
    //         $sheet->setCellValue('L' . $kolom, round($cetak->ttl_rp, 0));
    //         $sheet->setCellValue('M' . $kolom, "=SUM(L2:L4)/J4");
    //         $kolom++;

    //         $sortir = CabutOpnameModel::sortirPartai($d->nm_partai);
    //         $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
    //         $sheet->setCellValue('B' . $kolom, 'sortir');
    //         $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
    //         $sheet->setCellValue('D' . $kolom, 0);
    //         $sheet->setCellValue('E' . $kolom, 0);
    //         $sheet->setCellValue('F' . $kolom, 0);
    //         $sheet->setCellValue('G' . $kolom, 0);
    //         $sheet->setCellValue('H' . $kolom, 0);
    //         $sheet->setCellValue('I' . $kolom, round($sortir->pcs ?? 0, 0));
    //         $gr_awal_sortir = $sortir->gr_awal ?? 0;
    //         $gr_akhir_sortir = $sortir->gr ?? 0;
    //         $sheet->setCellValue('J' . $kolom, round($sortir->gr ?? 0, 0));
    //         $sheet->setCellValue('K' . $kolom, round((1 - $gr_akhir_sortir / $gr_awal_sortir) * 100, 0));
    //         $sheet->setCellValue('L' . $kolom, round($sortir->ttl_rp ?? 0, 0));
    //         $sheet->setCellValue('M' . $kolom, "=SUM(L2:L5)/J5");
    //         $kolom++;

    //         $grading = CabutOpnameModel::gradingPartai($d->nm_partai);
    //         $gradingsusut = CabutOpnameModel::gradingPartai_susut($d->nm_partai);

    //         $gr_susut = $gradingsusut->gr ?? 0;
    //         $gr_grading = $grading->gr ?? 0;
    //         $sheet->setCellValue('A' . $kolom, "$d->nm_partai");
    //         $sheet->setCellValue('B' . $kolom, 'grading');
    //         $sheet->setCellValue('C' . $kolom, strtoupper($d->tipe));
    //         $sheet->setCellValue('D' . $kolom, 0);
    //         $sheet->setCellValue('E' . $kolom, 0);
    //         $sheet->setCellValue('F' . $kolom, 0);
    //         $sheet->setCellValue('G' . $kolom, 0);
    //         $sheet->setCellValue('H' . $kolom, 0);
    //         $sheet->setCellValue('I' . $kolom, round($grading->pcs ?? 0, 0));
    //         $sheet->setCellValue('J' . $kolom, round($grading->gr ?? 0, 0));
    //         $sheet->setCellValue('K' . $kolom, round((1 - $grading->gr / ($gr_susut + $gr_grading)) * 100, 0));
    //         $sheet->setCellValue('L' . $kolom, round($grading->ttl_rp ?? 0, 0));
    //         $sheet->setCellValue('M' . $kolom, "=SUM(L2:L6)/J6");
    //         $kolom++;
    //     }
    //     $sheet->getStyle("A1:M" . $kolom - 1)->applyFromArray($style);
    //     $sheet->setCellValue('A' . $kolom, 'Total');
    //     $sheet->setCellValue('B' . $kolom, '');
    //     $sheet->setCellValue('C' . $kolom, '');
    //     $sheet->setCellValue('D' . $kolom, "=SUM(D2:D6)");
    //     $sheet->setCellValue('E' . $kolom, "=SUM(E2:E6)");
    //     $sheet->setCellValue('F' . $kolom, "=SUM(F2:F6)");
    //     $sheet->setCellValue('G' . $kolom, "=SUM(G2:G6)");
    //     $sheet->setCellValue('H' . $kolom, "=SUM(H2:H6)");
    //     $sheet->setCellValue('I' . $kolom, "=I6");
    //     $sheet->setCellValue('J' . $kolom, "=J6");
    //     $sheet->setCellValue('K' . $kolom, 0);
    //     $sheet->setCellValue('L' . $kolom, "=SUM(K2:K6)");
    //     $sheet->setCellValue('M' . $kolom, "=K7/J7");
    //     $sheet->getStyle("A$kolom:M$kolom")->applyFromArray($style_footer);



    //     $sheet->getStyle("A" . $kolom + 2 . ":C" .  $kolom + 2)->applyFromArray($style_atas);
    //     $kolom2 = $kolom + 2;
    //     $sheet->setCellValue('A' . $kolom2, 'Grade');
    //     $sheet->setCellValue('B' . $kolom2, 'Pcs');
    //     $sheet->setCellValue('C' . $kolom2, 'Gr');

    //     $grade = CabutOpnameModel::table_grade($r->partai);

    //     $kolom_awal = $kolom2 + 1;
    //     $kolom3 = $kolom2 + 1;
    //     foreach ($grade as $d) {
    //         $sheet->setCellValue('A' . $kolom3, $d->grade);
    //         $sheet->setCellValue('B' . $kolom3, round($d->pcs, 0));
    //         $sheet->setCellValue('C' . $kolom3, round($d->gr, 0));
    //         $kolom3++;
    //     }
    //     $sheet->setCellValue('A' . $kolom3, "Total");
    //     $sheet->setCellValue('B' . $kolom3, "=sum(B$kolom_awal:B$kolom3)");
    //     $sheet->setCellValue('C' . $kolom3, "=sum(C$kolom_awal:C$kolom3)");

    //     $sheet->getStyle("A" . $kolom3 . ":C" .  $kolom3)->applyFromArray($style_footer);
    //     $sheet->getStyle("A" . $kolom + 3 . ":C" .  $kolom3 - 1)->applyFromArray($style);






    //     $namafile = "Cost Per Partai.xlsx";

    //     $writer = new Xlsx($spreadsheet);
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment;filename=' . $namafile);
    //     header('Cache-Control: max-age=0');


    //     $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    //     $writer->save('php://output');
    //     exit();
    // }
}
