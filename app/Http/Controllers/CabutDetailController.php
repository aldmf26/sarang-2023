<?php

namespace App\Http\Controllers;

use App\Models\CocokanModel;
use App\Models\DetailCabutModel;
use App\Models\DetailCetakModel;
use App\Models\DetailSortirModel;
use App\Models\Grading;
use App\Models\OpnameNewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CabutDetailController extends Controller
{
    public function cabutAwal(DetailCabutModel $model)
    {
        $a11 = $model::bkstockawal_sum();
        $data = [
            'cabut' => $a11
        ];
        return view();
    }

    public function export(OpnameNewModel $model, DetailCabutModel $model2)
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
        $sheet1->setTitle('Gudang Cabut');

        $sheet1->getStyle("B1:K1")->applyFromArray($style_atas);
        $sheet1->setCellValue('A1', 'Cabut awal');
        $sheet1->setCellValue('B1', 'partai');
        $sheet1->setCellValue('C1', 'pengawas');
        $sheet1->setCellValue('D1', 'no box');
        $sheet1->setCellValue('E1', 'pcs');
        $sheet1->setCellValue('F1', 'gr');
        $sheet1->setCellValue('G1', 'ttl rp bk');
        $sheet1->setCellValue('H1', 'cost kerja');
        $sheet1->setCellValue('I1', 'cost cu');
        $sheet1->setCellValue('J1', 'ttl rp');
        $sheet1->setCellValue('K1', 'rp/gr');

        $gdcabutawal = $model2::bkstockawal_sum();
        $a11suntik = $this->getSuntikan(11);
        $kolom = 2;
        foreach ($gdcabutawal as $d) {
            $sheet1->setCellValue('B' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('C' . $kolom, $d->name);
            $sheet1->setCellValue('D' . $kolom, $d->no_box);
            $sheet1->setCellValue('E' . $kolom, $d->pcs);
            $sheet1->setCellValue('F' . $kolom, $d->gr_awal);
            $sheet1->setCellValue('G' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('H' . $kolom, 0);
            $sheet1->setCellValue('I' . $kolom, 0);
            $sheet1->setCellValue('J' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('K' . $kolom, ($d->ttl_rp) / $d->gr_awal);
            $kolom++;
        }

        $sheet1->setCellValue('B' . $kolom, 'partai suntik');
        $sheet1->setCellValue('C' . $kolom, '-');
        $sheet1->setCellValue('D' . $kolom, 'suntik');
        $sheet1->setCellValue('E' . $kolom, $a11suntik->pcs);
        $sheet1->setCellValue('F' . $kolom, $a11suntik->gr);
        $sheet1->setCellValue('G' . $kolom, $a11suntik->ttl_rp);
        $sheet1->setCellValue('H' . $kolom, 0);
        $sheet1->setCellValue('I' . $kolom, 0);
        $sheet1->setCellValue('J' . $kolom, $a11suntik->ttl_rp);
        $sheet1->setCellValue('K' . $kolom, ($a11suntik->ttl_rp) / ($a11suntik->gr));

        $sheet1->getStyle('B2:K' . $kolom)->applyFromArray($style);

        $sheet1->getStyle("N1:W1")->applyFromArray($style_atas);
        $sheet1->setCellValue('M1', 'Cabut akhir');
        $sheet1->setCellValue('N1', 'partai');
        $sheet1->setCellValue('O1', 'pengawas');
        $sheet1->setCellValue('P1', 'no box');
        $sheet1->setCellValue('Q1', 'pcs');
        $sheet1->setCellValue('R1', 'gr');
        $sheet1->setCellValue('S1', 'ttl rp bk');
        $sheet1->setCellValue('T1', 'cost kerja');
        $sheet1->setCellValue('U1', 'cost cu');
        $sheet1->setCellValue('V1', 'ttl rp');
        $sheet1->setCellValue('W1', 'rp/gr');

        $gdcabutawal = $model2::bkstockawal_sum();
        $a14suntik = $this->getSuntikan(14);
        $a16suntik = $this->getSuntikan(16);
        $kolom = 2;
        foreach ($gdcabutawal as $d) {
            $sheet1->setCellValue('N' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('O' . $kolom, $d->name);
            $sheet1->setCellValue('P' . $kolom, $d->no_box);
            $sheet1->setCellValue('Q' . $kolom, $d->pcs);
            $sheet1->setCellValue('R' . $kolom, $d->gr_akhir);
            $sheet1->setCellValue('S' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('T' . $kolom, $d->cost_kerja);
            $sheet1->setCellValue('U' . $kolom, 0);
            $sheet1->setCellValue('V' . $kolom, $d->ttl_rp + $d->cost_kerja);
            $sheet1->setCellValue('W' . $kolom, ($d->ttl_rp + $d->cost_kerja) / $d->gr_akhir);
            $kolom++;
        }

        $sheet1->setCellValue('N' . $kolom, 'partai suntik');
        $sheet1->setCellValue('O' . $kolom, '-');
        $sheet1->setCellValue('P' . $kolom, 'suntik');
        $sheet1->setCellValue('Q' . $kolom, $a14suntik->pcs + $a16suntik->pcs);
        $sheet1->setCellValue('R' . $kolom, $a14suntik->gr + $a16suntik->gr);
        $sheet1->setCellValue('S' . $kolom, $a14suntik->ttl_rp + $a16suntik->ttl_rp);
        $sheet1->setCellValue('T' . $kolom, 0);
        $sheet1->setCellValue('U' . $kolom, 0);
        $sheet1->setCellValue('V' . $kolom, $a14suntik->ttl_rp + $a16suntik->ttl_rp);
        $sheet1->setCellValue('W' . $kolom, ($a14suntik->ttl_rp + $a16suntik->ttl_rp) / ($a14suntik->gr + $a16suntik->gr));

        $sheet1->getStyle('N2:W' . $kolom)->applyFromArray($style);

        $sheet1->getStyle("Z1:AJ1")->applyFromArray($style_atas);
        $sheet1->setCellValue('Y1', 'Cabut sedang proses');
        $sheet1->setCellValue('Z1', 'partai');
        $sheet1->setCellValue('AA1', 'pengawas');
        $sheet1->setCellValue('AB1', 'no box');
        $sheet1->setCellValue('AC1', 'pcs');
        $sheet1->setCellValue('AD1', 'gr');
        $sheet1->setCellValue('AE1', 'ttl rp bk');
        $sheet1->setCellValue('AF1', 'cost kerja');
        $sheet1->setCellValue('AG1', 'cost cu dll');
        $sheet1->setCellValue('AH1', 'cost operasional');
        $sheet1->setCellValue('AI1', 'ttl rp');
        $sheet1->setCellValue('AJ1', 'rp/gr');

        $gudangbk = $model::bksedang_proses_sum();

        $kolom = 2;
        foreach ($gudangbk as $d) {
            $sheet1->setCellValue('Z' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('AA' . $kolom, $d->name);
            $sheet1->setCellValue('AB' . $kolom, $d->no_box);
            $sheet1->setCellValue('AC' . $kolom, $d->pcs);
            $sheet1->setCellValue('AD' . $kolom, $d->gr);
            $sheet1->setCellValue('AE' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('AF' . $kolom, 0);
            $sheet1->setCellValue('AG' . $kolom, 0);
            $sheet1->setCellValue('AH' . $kolom, 0);
            $sheet1->setCellValue('AI' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('AJ' . $kolom, $d->ttl_rp / $d->gr);
            $kolom++;
        }

        $sheet1->getStyle('Z2:AJ' . $kolom - 1)->applyFromArray($style);


        $sheet1->getStyle("AM1:Y1")->applyFromArray($style_atas);
        $sheet1->setCellValue('AL1', 'Cabut sisa pengawas');
        $sheet1->setCellValue('AM1', 'partai');
        $sheet1->setCellValue('AN1', 'pengawas');
        $sheet1->setCellValue('AO1', 'no box');
        $sheet1->setCellValue('AP1', 'pcs');
        $sheet1->setCellValue('AQ1', 'gr');
        $sheet1->setCellValue('AR1', 'ttl rp bk');
        $sheet1->setCellValue('AS1', 'cost kerja');
        $sheet1->setCellValue('AT1', 'cost cu dll');
        $sheet1->setCellValue('AU1', 'cost operasional');
        $sheet1->setCellValue('AV1', 'ttl rp');
        $sheet1->setCellValue('AW1', 'rp/gr');

        $gudangbksisa = $model::bksisapgws();

        $kolom = 2;
        foreach ($gudangbksisa as $d) {
            $sheet1->setCellValue('AM' . $kolom, $d->nm_partai);
            $sheet1->setCellValue('AN' . $kolom, $d->name);
            $sheet1->setCellValue('AO' . $kolom, $d->no_box);
            $sheet1->setCellValue('AP' . $kolom, $d->pcs);
            $sheet1->setCellValue('AQ' . $kolom, $d->gr);
            $sheet1->setCellValue('AR' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('AS' . $kolom, 0);
            $sheet1->setCellValue('AT' . $kolom, 0);
            $sheet1->setCellValue('AU' . $kolom, 0);
            $sheet1->setCellValue('AV' . $kolom, $d->ttl_rp);
            $sheet1->setCellValue('AW' . $kolom, $d->ttl_rp / $d->gr);
            $kolom++;
        }

        $sheet1->getStyle('AM2:AW' . $kolom - 1)->applyFromArray($style);




        $namafile = "Opname Gudang.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function getSuntikan($index)
    {
        $datas = [
            11 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_cbt_awal'"),
            14  => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_siap_cetak_diserahkan'"),
            16  => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'stock_eo_diserahkan'"),
            26 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_serah'"),
            21 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_awal_stock' and opname = 'Y'"),
            22 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_awal_stock' "),
            23 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_awal_stock' and opname = 'T'"),
            24 => DB::selectOne("SELECT sum(a.pcs) as pcs,sum(a.gr) as gr,sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a WHERE a.ket = 'cetak_selesai_siap_sortir_diserahkan' and opname = 'T'"),
            27 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'cetak_sisa'"),
            31 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_stok_awal' and opname = 'Y'"),
            32 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_stok_awal' and opname = 'T'"),
            35 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'sortir_selesai_diserahkan'"),
            41 => DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr, sum(a.ttl_rp) as ttl_rp FROM opname_suntik as a where a.ket = 'grading' and opname = 'Y'"),
            42 => DB::selectOne("SELECT sum(pcs) as pcs, sum(gr) as gr, sum(ttl_rp) as ttl_rp FROM `opname_suntik` WHERE ket ='grading' and opname = 'T';"),
            // 43 => DB::selectOne("SELECT sum(pcs) as pcs, sum(gr) as gr, sum(ttl_rp) as ttl_rp FROM `opname_suntik` WHERE ket ='cetak_selesai' and opname = 'T';"),
        ];
        if (array_key_exists($index, $datas)) {
            return $datas[$index];
        } else {
            return false;
        }
    }

    public function cabut_cabutAwal()
    {
        $model2 = new DetailCabutModel();
        $data = [
            'title' => 'Cabut Awal',
            'cabut_awal' => $model2::bkstockawal_sum(),
            'a11suntik' => $this->getSuntikan(11),
        ];
        return view('home.opnamenew.cabut.cabut_awal', $data);
    }
    public function cabut_cabutAkhir()
    {
        $model2 = new DetailCabutModel();

        $data = [
            'title' => 'Cabut Akhir',
            'cabut_awal' => $model2::bkstockawal_sum(),
            'a14suntik' => $this->getSuntikan(14),
            'a16suntik' => $this->getSuntikan(16),
        ];
        return view('home.opnamenew.cabut.cabut_akhir', $data);
    }

    public function cabut_cabutProses(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box_proses' => $model::bksedang_proses_sum(),
        ];
        return view('home.opnamenew.cabut.cabut_proses', $data);
    }

    public function cabut_cabutSisa(OpnameNewModel $model)
    {
        $data = [
            'title' => 'Data Opname',
            'box_stock' => $model::bksisapgws(),
            'box_proses' => $model::bksedang_proses_sum(),
            'box_selesai' => $model::bksedang_selesai_sum(),
        ];
        return view('home.opnamenew.cabut.cabut_sisa', $data);
    }



    public function cetak_cetakAwal()
    {
        $model2 = new DetailCetakModel();

        $ca2 = $model2::cetak_stok_awal();
        $ca11 = $this->getSuntikan(21);
        $ca12suntik = $this->getSuntikan(23);

        $model2 = new DetailCabutModel();
        $data = [
            'title' => 'Cetak Awal',
            'query' => $ca2,
            'suntik' => $ca11,
            'suntik2' => $ca12suntik,
        ];
        return view('home.opnamenew.cetak.cetak_awal', $data);
    }

    public function cetak_cetakAkhir()
    {
        $model2 = new DetailCetakModel();

        $ca2 = $model2::stok_selesai();
        $ca11 = $this->getSuntikan(21);
        $ca12suntik = $this->getSuntikan(23);

        $model2 = new DetailCabutModel();
        $data = [
            'title' => 'Cetak Akhir',
            'query' => $ca2,
            'suntik' => $ca11,
            'suntik2' => $ca12suntik,
        ];
        return view('home.opnamenew.cetak.cetak_akhir', $data);
    }

    public function cetak_cetakProses()
    {
        $model2 = new OpnameNewModel();
        $data = [ 
            'title' => 'Cetak Proses',
            'query' => $model2::cetak_proses(),
        ];
        return view('home.opnamenew.cetak.cetak_proses', $data);
    }

    public function cetak_cetakSisa()
    {
        $model2 = new OpnameNewModel();
        $data = [
            'title' => 'Cetak Sisa Pengawas',
            'query' => $model2::cetak_stok(),
        ];
        return view('home.opnamenew.cetak.cetak_sisa', $data);
    }

    public function sortir_sortirAwal()
    {
        $model2 = new DetailSortirModel();

        $data = [ 
            'title' => 'Sortir Awal',
            'query' => $model2::stok_awal(),
            'suntik' => $this->getSuntikan(31),
            'suntik2' => $this->getSuntikan(32),
        ];
        return view('home.opnamenew.sortir.sortir_awal', $data);
    }
    public function sortir_sortirAkhir()
    {
        $model2 = new DetailSortirModel();
        $data = [ 
            'title' => 'Sortir Akhir',
            'query' => $model2::stok_selesai(),
            'suntik' => $this->getSuntikan(35),
        ];
        return view('home.opnamenew.sortir.sortir_akhir', $data);
    }
    public function sortir_sortirProses()
    {
        $model2 = new OpnameNewModel();
        $data = [ 
            'title' => 'Sortir Proses',
            'query' => $model2::sortir_proses(),
        ];
        return view('home.opnamenew.sortir.sortir_proses', $data);
    }
    public function sortir_sortirSisa()
    {
        $model2 = new OpnameNewModel();
        $data = [ 
            'title' => 'Sortir Sisa',
            'query' => $model2::sortir_stock(),
        ];
        return view('home.opnamenew.sortir.sortir_sisa', $data);
    }

    public function gradingAwal()
    {
        $model2 = new DetailSortirModel();
        $gradingAwal = $model2::stok_selesai();
        $s1suntik2 = $this->getSuntikan(41);
        $s1suntik_akhir = $this->getSuntikan(35);

        $data = [ 
            'title' => 'Grading Awal',
            'query' => $gradingAwal,
            'suntik' => $s1suntik_akhir,
            'suntik2' => $s1suntik2,
        ];
        return view('home.opnamenew.grading.awal', $data);
    }

    public function gradingSisa()
    {
        $sisaGrading = Grading::dapatkanStokBox('formulir');

        $data = [ 
            'title' => 'Grading Awal',
            'query' => $sisaGrading,
        ];
        return view('home.opnamenew.grading.sisa', $data);
    }
    
    public function gradingAkhir()
    {
        $selesaiGrading = Grading::selesai();
        $hrgaSatuan = Grading::gradingSum()->hrga_satuan;

        $data = [ 
            'title' => 'Grading akhir',
            'query' => $selesaiGrading,
            'hrgaSatuan' => $hrgaSatuan,
        ];
        return view('home.opnamenew.grading.akhir', $data);
    }
    public function pengirimanAwal()
    {
        $pengiriman = Grading::pengirimanAll();
        $hrgaSatuan = Grading::gradingSum()->hrga_satuan;

        $data = [ 
            'title' => 'Pengiriman',
            'query' => $pengiriman,
            'hrgaSatuan' => $hrgaSatuan,
        ];
        return view('home.opnamenew.pengiriman.awal', $data);
    }
    public function pengirimanSisa()
    {
        $belumKirim = Grading::belumKirimAll();
        $hrgaSatuan = Grading::gradingSum()->hrga_satuan;

        $data = [ 
            'title' => 'Pengiriman Sisa',
            'query' => $belumKirim,
            'hrgaSatuan' => $hrgaSatuan,
        ];
        return view('home.opnamenew.pengiriman.sisa', $data);
    }
    public function list_pengiriman()
    {
        $belumKirim = Grading::belumKirimAll();
        $hrgaSatuan = Grading::gradingSum()->hrga_satuan;

        $data = [ 
            'title' => 'Pengiriman Sisa',
            'query' => $belumKirim,
            'hrgaSatuan' => $hrgaSatuan,
        ];
        return view('home.opnamenew.list_pengiriman.awal', $data);
    }
}
