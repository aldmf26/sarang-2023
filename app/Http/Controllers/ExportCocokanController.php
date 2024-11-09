<?php

namespace App\Http\Controllers;

use App\Models\BalanceModel;
use App\Models\CocokanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\DetailCabutModel;
use App\Models\DetailCetakModel;
use App\Models\DetailSortirModel;
use App\Models\Grading;
use App\Models\OpnameNewModel;
use App\Models\SummaryModel;
use stdClass;

class ExportCocokanController extends Controller
{

    public function cetak_cetakakhir()
    {
        $model2 = new DetailCetakModel();

        $ca2 = $model2::cetak_stok_awal();
        $ca16suntik = $this->getSuntikan(26);

        $model2 = new DetailCabutModel();
        $data = [
            'title' => 'Cetak Akhir',
            'query' => $ca2,
            'suntik' => $ca16suntik,
        ];
        return view('home.opnamenew.cetak.cetak_akhir', $data);
    }
    public function cetak_sedangproses()
    {
        $model2 = new OpnameNewModel();

        $ca2 = $model2::cetak_proses();


        $model2 = new DetailCabutModel();
        $data = [
            'title' => 'Cetak sedang proses',
            'query' => $ca2,
        ];
        return view('home.opnamenew.cetak.cetak_sedang_proses', $data);
    }
    public function cetak_sisa()
    {
        $model = new OpnameNewModel();

        $ca2 = $model::cetak_stok();


        $model2 = new DetailCabutModel();
        $data = [
            'title' => 'Cetak sedang proses',
            'query' => $ca2,
        ];
        return view('home.opnamenew.cetak.cetak_sisa', $data);
    }
    public function cabut($spreadsheet, $style_atas, $style, $model)
    {
        $model2 = new DetailCabutModel();
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
    }
    public function cetak($spreadsheet, $style_atas, $style, $model)
    {
        $model2 = new DetailCetakModel();
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Gudang Cetak');


        $koloms = [
            'A' => 'awal cetak',
            'B' => 'nama partai',
            'C' => 'pengawas',
            'D' => 'no box',
            'E' => 'pcs',
            'F' => 'gr',
            'G' => 'ttl rp bk',
            'H' => 'cost kerja',
            'I' => 'ttl rp',
            'J' => 'rp/gr',

            'L' => 'akhir cetak',
            'M' => 'nama partai',
            'N' => 'pengawas',
            'O' => 'no box',
            'P' => 'pcs',
            'Q' => 'gr',
            'R' => 'ttl rp bk',
            'S' => 'cost kerja',
            'T' => 'ttl rp',
            'U' => 'rp/gr',

            'W' => 'sedang proses',
            'X' => 'nama partai',
            'Y' => 'pengawas',
            'Z' => 'no box',
            'AA' => 'pcs',
            'AB' => 'gr',
            'AC' => 'ttl rp bk',
            'AD' => 'cost kerja',
            'AE' => 'ttl rp',
            'AF' => 'rp/gr',

            'AH' => 'sisa cetak',
            'AI' => 'nama partai',
            'AJ' => 'pengawas',
            'AK' => 'no box',
            'AL' => 'pcs',
            'AM' => 'gr',
            'AN' => 'ttl rp bk',
            'AO' => 'cost kerja',
            'AP' => 'ttl rp',
            'AQ' => 'rp/gr',

        ];
        foreach ($koloms as $k => $v) {
            $sheet->getStyle($k . "1" . ":$k" . "1")->applyFromArray($style_atas);
            $sheet->setCellValue($k . '1', $v);
        }

        // awal ctk
        $ca2 = $model2::cetak_stok_awal();
        $ca11 = $this->getSuntikan(21);
        $ca12suntik = $this->getSuntikan(23);


        // dd($ca11, $ca2, $ca12suntik);
        $row = 2;
        foreach ($ca2 as $i => $v) {
            $sheet->setCellValue("B$row", $v->nm_partai);
            $sheet->setCellValue("C$row", $v->name);
            $sheet->setCellValue("D$row", $v->no_box);
            $sheet->setCellValue("E$row", $v->pcs_awal);
            $sheet->setCellValue("F$row", $v->gr_awal);
            $sheet->setCellValue("G$row", $v->ttl_rp);
            $sheet->setCellValue("H$row", 0);
            $sheet->setCellValue("I$row", $v->ttl_rp);
            $sheet->setCellValue("J$row", $v->ttl_rp / $v->gr_awal);

            $row++;
        }
        $sheet->setCellValue("B" . $row, 'suntikan');
        $sheet->setCellValue("C" . $row, 'suntikan');
        $sheet->setCellValue("D" . $row, '-');
        $sheet->setCellValue("E" . $row, $ca11->pcs);
        $sheet->setCellValue("F" . $row, $ca11->gr);
        $sheet->setCellValue("G" . $row, $ca11->ttl_rp);
        $sheet->setCellValue("H" . $row, 0);
        $sheet->setCellValue("I$row", $ca11->ttl_rp);
        $sheet->setCellValue("J$row", $ca11->ttl_rp / $v->gr_awal);

        $row = $row + 1;
        $sheet->setCellValue("B" . $row, 'suntikan');
        $sheet->setCellValue("C" . $row, 'suntikan');
        $sheet->setCellValue("D" . $row, '-');
        $sheet->setCellValue("E" . $row, $ca12suntik->pcs);
        $sheet->setCellValue("F" . $row, $ca12suntik->gr);
        $sheet->setCellValue("G" . $row, $ca12suntik->ttl_rp);
        $sheet->setCellValue("H" . $row, 0);
        $sheet->setCellValue("I$row", $ca12suntik->ttl_rp);
        $sheet->setCellValue("J$row", $ca12suntik->ttl_rp / $v->gr_awal);

        $sheet->getStyle('B2:J' . $row)->applyFromArray($style);

        // akhir ctk
        $ca2_selesai = $model2::stok_selesai();

        $row = 2;
        foreach ($ca2_selesai as $v) {
            $sheet->setCellValue("M$row", $v->nm_partai);
            $sheet->setCellValue("N$row", $v->name);
            $sheet->setCellValue("O$row", $v->no_box);
            $sheet->setCellValue("P$row", $v->pcs_akhir);
            $sheet->setCellValue("Q$row", $v->gr_akhir);
            $sheet->setCellValue("R$row", $v->ttl_rp);
            $sheet->setCellValue("S$row", $v->cost_kerja);
            $sheet->setCellValue("T$row", $v->ttl_rp + $v->cost_kerja);
            $sheet->setCellValue("U$row", ($v->ttl_rp + $v->cost_kerja) / $v->gr_akhir);

            $row++;
        }
        $sheet->setCellValue("M" . $row, 'suntikan');
        $sheet->setCellValue("N" . $row, 'suntikan');
        $sheet->setCellValue("O" . $row, '-');
        $sheet->setCellValue("P" . $row, $ca11->pcs);
        $sheet->setCellValue("Q" . $row, $ca11->gr);
        $sheet->setCellValue("R" . $row, $ca11->ttl_rp);
        $sheet->setCellValue("S" . $row, 0);
        $sheet->setCellValue("T$row", $ca11->ttl_rp);
        $sheet->setCellValue("U$row", empty($ca11->gr) ? 0 : $ca11->ttl_rp / $ca11->gr);
        $row = $row + 1;


        // proses ctk
        $proses = $model::cetak_proses();
        $row = 2;
        foreach ($proses as $v) {
            $sheet->setCellValue("X$row", $v->nm_partai);
            $sheet->setCellValue("Y$row", $v->name);
            $sheet->setCellValue("Z$row", $v->no_box);
            $sheet->setCellValue("AA$row", $v->pcs);
            $sheet->setCellValue("AB$row", $v->gr);
            $sheet->setCellValue("AC$row", $v->ttl_rp);
            $sheet->setCellValue("AD$row", 0);
            $sheet->setCellValue("AE$row", $v->ttl_rp);
            $sheet->setCellValue("AF$row", $v->ttl_rp / $v->gr);

            $row++;
        }
        $sheet->getStyle('X2:AF' . $row)->applyFromArray($style);

        // proses ctk
        $proses = $model::cetak_stok();
        $row = 2;
        foreach ($proses as $v) {
            $sheet->setCellValue("AI$row", $v->nm_partai);
            $sheet->setCellValue("AJ$row", $v->name);
            $sheet->setCellValue("AK$row", $v->no_box);
            $sheet->setCellValue("AL$row", $v->pcs);
            $sheet->setCellValue("AM$row", $v->gr);
            $sheet->setCellValue("AN$row", $v->ttl_rp);
            $sheet->setCellValue("AO$row", 0);
            $sheet->setCellValue("AP$row", 0);
            $sheet->setCellValue("AQ$row", 0);

            $row++;
        }
        $sheet->getStyle('AI2:AQ' . $row)->applyFromArray($style);
    }

    public function sortir($spreadsheet, $style_atas, $style, $model)
    {
        $model2 = new DetailSortirModel();
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Gudang Sortir');
        $sheet->getStyle("B1:J1")->applyFromArray($style_atas);
        $sheet->getStyle("M1:W1")->applyFromArray($style_atas);
        $sheet->getStyle("Z1:AH1")->applyFromArray($style_atas);
        $sheet->getStyle("AK1:AS1")->applyFromArray($style_atas);
        $koloms = [
            'A' => 'awal sortir',
            'B' => 'nama partai',
            'C' => 'pengawas',
            'D' => 'no box',
            'E' => 'pcs',
            'F' => 'gr',
            'G' => 'ttl rp bk',
            'H' => 'cost kerja',
            'I' => 'ttl rp',
            'J' => 'rp/gr',

            'L' => 'akhir sortir',
            'M' => 'nama partai',
            'N' => 'pengawas',
            'O' => 'no box',
            'P' => 'pcs',
            'Q' => 'gr',
            'R' => 'ttl rp bk',
            'S' => 'cost kerja',
            'T' => 'cost cu',
            'U' => 'cost operasional',
            'V' => 'ttl rp',
            'W' => 'rp/gr',

            'Y' => 'sedang proses',
            'Z' => 'nama partai',
            'AA' => 'pengawas',
            'AB' => 'no box',
            'AC' => 'pcs',
            'AD' => 'gr',
            'AE' => 'ttl rp bk',
            'AF' => 'cost kerja',
            'AG' => 'ttl rp',
            'AH' => 'rp/gr',

            'AJ' => 'sisa pengawas',
            'AK' => 'nama partai',
            'AL' => 'pengawas',
            'AM' => 'no box',
            'AN' => 'pcs',
            'AO' => 'gr',
            'AP' => 'ttl rp bk',
            'AQ' => 'cost kerja',
            'AR' => 'ttl rp',
            'AS' => 'rp/gr',

        ];
        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }

        // awal ctk
        $s1 = $model2::stok_awal();
        $s1_akhir = $model2::stok_selesai();
        $s1suntik = $this->getSuntikan(31);
        $s1suntik2 = $this->getSuntikan(32);
        $s1suntik_akhir = $this->getSuntikan(35);


        $row = 2;
        foreach ($s1 as $v) {
            $sheet->setCellValue("B$row", $v->nm_partai);
            $sheet->setCellValue("C$row", $v->name);
            $sheet->setCellValue("D$row", $v->no_box);
            $sheet->setCellValue("E$row", $v->pcs);
            $sheet->setCellValue("F$row", $v->gr);
            $sheet->setCellValue("G$row", $v->ttl_rp);
            $sheet->setCellValue("H$row", 0);
            $sheet->setCellValue("I$row", $v->ttl_rp);
            $sheet->setCellValue("J$row", $v->ttl_rp / $v->gr);

            $row++;
        }



        $sheet->getStyle('B2:J' . $row - 1)->applyFromArray($style);

        // akhir sortir
        $row = 2;
        foreach ($s1_akhir as $v) {
            $sheet->setCellValue("M$row", $v->nm_partai);
            $sheet->setCellValue("N$row", $v->name);
            $sheet->setCellValue("O$row", $v->no_box);
            $sheet->setCellValue("P$row", $v->pcs);
            $sheet->setCellValue("Q$row", $v->gr);
            $sheet->setCellValue("R$row", $v->ttl_rp);
            $sheet->setCellValue("S$row", $v->cost_kerja);
            $sheet->setCellValue("T$row", 0);
            $sheet->setCellValue("U$row", 0);
            $sheet->setCellValue("V$row", $v->ttl_rp +  $v->cost_kerja);
            $sheet->setCellValue("W$row", ($v->ttl_rp +  $v->cost_kerja) / $v->gr);
            $row++;
        }



        // proses ctk
        $cetak_proses = $model::sortir_proses();
        $row = 2;
        foreach ($cetak_proses as $v) {
            $sheet->setCellValue("Z$row", $v->nm_partai);
            $sheet->setCellValue("AA$row", $v->name);
            $sheet->setCellValue("AB$row", $v->no_box);
            $sheet->setCellValue("AC$row", $v->pcs);
            $sheet->setCellValue("AD$row", $v->gr);
            $sheet->setCellValue("AE$row", $v->ttl_rp);
            $sheet->setCellValue("AF$row", 0);
            $sheet->setCellValue("AG$row", $v->ttl_rp);
            $sheet->setCellValue("AH$row", $v->ttl_rp / $v->gr);
            $row++;
        }
        $sheet->getStyle('Z2:AH' . $row - 1)->applyFromArray($style);

        // proses ctk
        $cetak_proses = $model::sortir_stock();
        $row = 2;
        foreach ($cetak_proses as $v) {
            $sheet->setCellValue("AK$row", $v->nm_partai);
            $sheet->setCellValue("AL$row", $v->name);
            $sheet->setCellValue("AM$row", $v->no_box);
            $sheet->setCellValue("AN$row", $v->pcs);
            $sheet->setCellValue("AO$row", $v->gr);
            $sheet->setCellValue("AP$row", $v->ttl_rp);
            $sheet->setCellValue("AQ$row", 0);
            $sheet->setCellValue("AR$row", $v->ttl_rp);
            $sheet->setCellValue("AS$row", $v->ttl_rp / $v->gr);


            $row++;
        }
        $sheet->getStyle('AK2:AS' . $row - 1)->applyFromArray($style);
    }

    public function grading($spreadsheet, $style_atas, $style, $model)
    {
        $model2 = new DetailSortirModel();
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Gudang Grading');
        $sheet->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet->getStyle("K1:Q1")->applyFromArray($style_atas);
        $sheet->getStyle("T1:Z1")->applyFromArray($style_atas);
        $sheet->getStyle("AC1:AF1")->applyFromArray($style_atas);
        $koloms = [
            'A' => 'awal grading',
            'B' => 'nama partai',
            'C' => 'pengawas',
            'D' => 'no box',
            'E' => 'pcs',
            'F' => 'gr',
            'G' => 'ttl rp',
            'H' => 'rp/gr',

            'J' => 'sisa belum grading',
            'K' => 'nama partai',
            'L' => 'pengawas',
            'M' => 'no box',
            'N' => 'pcs',
            'O' => 'gr',
            'P' => 'ttl rp',
            'Q' => 'rp/gr',

            'S' => 'akhir grading',
            'T' => 'nama partai',
            'U' => 'box grading',
            'V' => 'grade',
            'W' => 'pcs',
            'X' => 'gr',
            'Y' => 'ttl rp',
            'Z' => 'rp/gr',

            'AB' => 'selisih',
            'AC' => 'pcs',
            'AD' => 'gr',
            'AE' => 'ttl rp',
            'AF' => 'rp/gr',
        ];
        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }

        // awal ctk
        $gradingAwal = $model2::stok_selesai();
        $s1suntik2 = $this->getSuntikan(41);
        $s1suntik_akhir = $this->getSuntikan(35);
        $sisaGrading = Grading::gradingSisa();
        $selesaiGrading = Grading::gradingAkhir();

        $sumTtlRp = $s1suntik_akhir->ttl_rp + $s1suntik2->ttl_rp;
        $sumTtlGr = $s1suntik_akhir->gr + $s1suntik2->gr;
        $sumttlPcs = $s1suntik_akhir->pcs + $s1suntik2->pcs;

        $sumTtlRp2 = 0;
        $sumTtlGr2 = 0;
        $sumTtlPcs2 = 0;

        // akhir sortir
        $row = 2;
        foreach ($gradingAwal as $v) {
            $sheet->setCellValue("B$row", $v->nm_partai);
            $sheet->setCellValue("C$row", $v->name);
            $sheet->setCellValue("D$row", $v->no_box);
            $sheet->setCellValue("E$row", $v->pcs);
            $sheet->setCellValue("F$row", $v->gr);
            $sheet->setCellValue("G$row", $v->ttl_rp);
            $sheet->setCellValue("H$row", ($v->ttl_rp) / $v->gr);
            $sumTtlRp += $v->ttl_rp;
            $sumTtlGr += $v->gr;
            $sumttlPcs += $v->pcs;

            $row++;
        }
        $hrgaSatuan = $sumTtlRp / $sumTtlGr;
        session()->put('hrga_satuan', $hrgaSatuan);

        $row = 2;
        foreach ($sisaGrading as $v) {
            $sheet->setCellValue("K$row", $v->nm_partai);
            $sheet->setCellValue("L$row", $v->pemberi);
            $sheet->setCellValue("M$row", $v->no_box);
            $sheet->setCellValue("N$row", $v->pcs_awal);
            $sheet->setCellValue("O$row", $v->gr_awal);
            $sheet->setCellValue("P$row", $v->ttl_rp_sortir);
            $sheet->setCellValue("Q$row", !$v->gr_awal ? 0 : ($v->ttl_rp_sortir) / $v->gr_awal);
            $row++;
        }
        $sheet->getStyle('K2:Q' . $row - 1)->applyFromArray($style);

        $row = 2;
        foreach ($selesaiGrading as $v) {
            $sheet->setCellValue("T$row", $v->nm_partai);
            $sheet->setCellValue("U$row", $v->box_pengiriman);
            $sheet->setCellValue("V$row", $v->grade);
            $sheet->setCellValue("W$row", $v->pcs);
            $sheet->setCellValue("X$row", $v->gr);
            $sheet->setCellValue("Y$row", $hrgaSatuan * $v->gr);
            $sheet->setCellValue("Z$row", ($hrgaSatuan * $v->gr) / $v->gr);

            $sumTtlRp2 += $hrgaSatuan * $v->gr;
            $sumTtlGr2 += $v->gr;
            $sumTtlPcs2 += $v->pcs;

            $row++;
        }
        $sheet->getStyle('T2:Z' . $row - 1)->applyFromArray($style);

        $sheet->setCellValue("AC2", $sumttlPcs - $sumTtlPcs2);
        $sheet->setCellValue("AD2", $sumTtlGr - $sumTtlGr2);
        $sheet->setCellValue("AE2", round($sumTtlRp - $sumTtlRp2));
        $sheet->setCellValue("AF2", ($sumTtlGr - $sumTtlGr2) == 0 ? 0 : ($sumTtlRp - $sumTtlRp2) / ($sumTtlGr - $sumTtlGr2));

        $sheet->getStyle('AC1:AF2')->applyFromArray($style);
    }

    public function pengiriman2($spreadsheet, $style_atas, $style, $model)
    {
        $model2 = new DetailSortirModel();
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(4);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Gudang Pengiriman');
        $sheet->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet->getStyle("K1:Q1")->applyFromArray($style_atas);
        $koloms = [
            'A' => 'pengiriman',
            'B' => 'nama partai',
            'C' => 'box grading',
            'D' => 'grade',
            'E' => 'pcs',
            'F' => 'gr',
            'G' => 'ttl rp',
            'H' => 'cost cu',
            'I' => 'cost operasional',
            'J' => 'ttl rp',
            'K' => 'rp/gr',

            'M' => 'sisa belum kirim',
            'N' => 'nama partai',
            'O' => 'box grading',
            'P' => 'grade',
            'Q' => 'pcs',
            'R' => 'gr',
            'S' => 'ttl rp',
            'T' => 'cost cu',
            'U' => 'cost operasional',
            'V' => 'ttl rp',
            'W' => 'rp/gr',
        ];
        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }

        // awal ctk
        $pengiriman = Grading::pengirimanAll();

        $belumKirim = Grading::belumKirimAll();

        $hrgaSatuan = Grading::gradingSum()->hrga_satuan;
        // akhir sortir
        $row = 2;
        foreach ($pengiriman as $v) {
            $sheet->setCellValue("B$row", $v->nm_partai ?? '');
            $sheet->setCellValue("C$row", $v->no_box);
            $sheet->setCellValue("D$row", $v->grade);
            $sheet->setCellValue("E$row", $v->pcs);
            $sheet->setCellValue("F$row", $v->gr);
            $sheet->setCellValue("G$row", $v->cost_bk);
            $sheet->setCellValue("H$row", $v->cost_cu);
            $sheet->setCellValue("I$row", $v->cost_op);
            $tl  = ($v->cost_bk) + ($v->cost_cu) + ($v->cost_op);
            $sheet->setCellValue("J$row", $tl);
            $sheet->setCellValue("K$row", ($tl) / $v->gr);

            $row++;
        }
        $sheet->getStyle('B2:K' . $row - 1)->applyFromArray($style);

        $row = 2;
        foreach ($belumKirim as $v) {
            $sheet->setCellValue("N$row", $v->nm_partai);
            $sheet->setCellValue("O$row", $v->no_box);
            $sheet->setCellValue("P$row", $v->grade);
            $sheet->setCellValue("Q$row", $v->pcs);
            $sheet->setCellValue("R$row", $v->gr);
            $sheet->setCellValue("S$row", $v->cost_bk);
            $sheet->setCellValue("T$row", $v->cost_cu);
            $sheet->setCellValue("U$row", $v->cost_op);
            $tl  = ($v->cost_bk) + ($v->cost_cu) + ($v->cost_op);
            $sheet->setCellValue("V$row", $tl);
            $sheet->setCellValue("W$row", ($tl) / $v->gr);

            $row++;
        }

        $sheet->getStyle('N2:W' . $row - 1)->applyFromArray($style);
    }

    public function rekap($spreadsheet, $style_atas, $style, $model)
    {
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(5);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap');
        $sheet->getStyle("B1:H1")->applyFromArray($style_atas);
        $sheet->getStyle("A20:H20")->applyFromArray($style_atas);

        $koloms = [
            'A' => '',
            'B' => 'pcs',
            'C' => 'gr',
            'D' => 'rp',
            'E' => 'cost kerja',
            'F' => 'cost cu',
            'G' => 'cost operasional',
            'H' => 'ttl rp',
        ];
        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }

        $kolomRekap = [
            // cabut
            'awal cabut' => [
                'pcs' => "=SUM('Gudang Cabut'!E:E)",
                'gr' => "=SUM('Gudang Cabut'!F:F)",
                'rp' => "=SUM('Gudang Cabut'!G:G)",
                'cost_kerja' => "=SUM('Gudang Cabut'!H:H)",
            ],
            'akhir cabut' => [
                'pcs' => "=SUM('Gudang Cabut'!Q:Q)",
                'gr' => "=SUM('Gudang Cabut'!R:R)",
                'rp' => "=SUM('Gudang Cabut'!S:S)",
                'cost_kerja' => "=SUM('Gudang Cabut'!T:T)",
                'ttl_rp' => "=E3",
            ],
            'cabut sedang proses' => [
                'pcs' => "=SUM('Gudang Cabut'!AC:AC)",
                'gr' => "=SUM('Gudang Cabut'!AD:AD)",
                'rp' => "=SUM('Gudang Cabut'!AE:AE)",
                'cost_kerja' => "=SUM('Gudang Cabut'!AF:AF)",
                'ttl_rp' => "=D4",
            ],
            'cabut sisa pengawas' => [
                'pcs' => "=SUM('Gudang Cabut'!AP:AP)",
                'gr' => "=SUM('Gudang Cabut'!AQ:AQ)",
                'rp' => "=SUM('Gudang Cabut'!AR:AR)",
                'cost_kerja' => "=SUM('Gudang Cabut'!AS:AS)",
                'ttl_rp' => "=D5",
            ],
            'Cabut selesai siap cetak belum kirim' => [
                'pcs' => "=SUM('Gudang Cabut'!AP:AP)",
                'gr' => "=SUM('Gudang Cabut'!AQ:AQ)",
                'rp' => "=SUM('Gudang Cabut'!AR:AR)",
                'cost_kerja' => "=SUM('Gudang Cabut'!AS:AS)",
                'ttl_rp' => "=D5",
            ],

            // cetak
            'awal cetak' => [
                'pcs' => "=SUM('Gudang Cetak'!E:E)",
                'gr' => "=SUM('Gudang Cetak'!F:F)",
                'rp' => "=SUM('Gudang Cetak'!G:G)",
                'cost_kerja' => "=SUM('Gudang Cetak'!H:H)",
            ],
            'akhir cetak' => [
                'pcs' => "=SUM('Gudang Cetak'!P:P)",
                'gr' => "=SUM('Gudang Cetak'!Q:Q)",
                'rp' => "=SUM('Gudang Cetak'!R:R)",
                'cost_kerja' => "=SUM('Gudang Cetak'!S:S)",
                'ttl_rp' => "=E7",

            ],
            'cetak sedang proses' => [
                'pcs' => "=SUM('Gudang Cetak'!AA:AA)",
                'gr' => "=SUM('Gudang Cetak'!AB:AB)",
                'rp' => "=SUM('Gudang Cetak'!AC:AC)",
                'cost_kerja' => "=SUM('Gudang Cetak'!AD:AD)",
                'ttl_rp' => "=D8",

            ],
            'cetak sisa pengawas' => [
                'pcs' => "=SUM('Gudang Cetak'!AL:AL)",
                'gr' => "=SUM('Gudang Cetak'!AM:AM)",
                'rp' => "=SUM('Gudang Cetak'!AN:AN)",
                'cost_kerja' => "=SUM('Gudang Cetak'!AO:AO)",
                'ttl_rp' => "=D9",

            ],
            'Cetak selesai siap sortir belum kirim' => [
                'pcs' => "=SUM('Gudang Cetak'!AL:AL)",
                'gr' => "=SUM('Gudang Cetak'!AM:AM)",
                'rp' => "=SUM('Gudang Cetak'!AN:AN)",
                'cost_kerja' => "=SUM('Gudang Cetak'!AO:AO)",
                'ttl_rp' => "=D9",

            ],

            // sortir
            'awal sortir' => [
                'pcs' => "=SUM('Gudang Sortir'!E:E)",
                'gr' => "=SUM('Gudang Sortir'!F:F)",
                'rp' => "=SUM('Gudang Sortir'!G:G)",
                'cost_kerja' => "=SUM('Gudang Sortir'!H:H)",
            ],
            'akhir sortir' => [
                'pcs' => "=SUM('Gudang Sortir'!P:P)",
                'gr' => "=SUM('Gudang Sortir'!Q:Q)",
                'rp' => "=SUM('Gudang Sortir'!R:R)",
                'cost_kerja' => "=SUM('Gudang Sortir'!S:S)",
                'ttl_rp' => "=E11",

            ],
            'sortir sedang proses' => [
                'pcs' => "=SUM('Gudang Sortir'!AC:AC)",
                'gr' => "=SUM('Gudang Sortir'!AD:AD)",
                'rp' => "=SUM('Gudang Sortir'!AE:AE)",
                'cost_kerja' => "=SUM('Gudang Sortir'!AF:AF)",
                'ttl_rp' => "=D12",

            ],
            'sortir sisa pengawas' => [
                'pcs' => "=SUM('Gudang Sortir'!AN:AN)",
                'gr' => "=SUM('Gudang Sortir'!AO:AO)",
                'rp' => "=SUM('Gudang Sortir'!AP:AP)",
                'cost_kerja' => "=SUM('Gudang Sortir'!AQ:AQ)",
                'ttl_rp' => "=D13",

            ],
            'Sortir selesai siap grading belum kirim' => [
                'pcs' => "=SUM('Gudang Sortir'!AN:AN)",
                'gr' => "=SUM('Gudang Sortir'!AO:AO)",
                'rp' => "=SUM('Gudang Sortir'!AP:AP)",
                'cost_kerja' => "=SUM('Gudang Sortir'!AQ:AQ)",
                'ttl_rp' => "=D13",

            ],

            // grading
            'awal grading' => [
                'pcs' => "=SUM('Gudang Grading'!E:E)",
                'gr' => "=SUM('Gudang Grading'!F:F)",
                'rp' => "=SUM('Gudang Grading'!G:G)",
                'cost_kerja' => "=SUM('Gudang Grading'!T:T)",
            ],
            'akhir grading' => [
                'pcs' => "=SUM('Gudang Grading'!W:W)",
                'gr' => "=SUM('Gudang Grading'!X:X)",
                'rp' => "=SUM('Gudang Grading'!Y:Y)",
                'cost_kerja' => "=SUM('Gudang Grading'!T:T)",
            ],
            'sisa belum grading' => [
                'pcs' => "=SUM('Gudang Grading'!N:N)",
                'gr' => "=SUM('Gudang Grading'!O:O)",
                'rp' => "=SUM('Gudang Grading'!P:P)",
                'cost_kerja' => "=SUM('Gudang Grading'!T:T)",
            ],


            'pengiriman' => [
                'pcs' => "=SUM('Gudang Pengiriman'!E:E)",
                'gr' => "=SUM('Gudang Pengiriman'!F:F)",
                'rp' => "=SUM('Gudang Pengiriman'!G:G)",
                'cost_kerja' => 0,
                'cu' => "=SUM('Gudang Pengiriman'!H:H)",
                'op' => "=SUM('Gudang Pengiriman'!I:I)",
                'ttl_rp' => "=SUM(D17:G17)",

            ],
            'belum kirim' => [
                'pcs' => "=SUM('Gudang Pengiriman'!Q:Q)",
                'gr' => "=SUM('Gudang Pengiriman'!R:R)",
                'rp' => "=SUM('Gudang Pengiriman'!S:S)",
                'cost_kerja' => 0,
                'cu' => "=SUM('Gudang Pengiriman'!T:T)",
                'op' => "=SUM('Gudang Pengiriman'!U:U)",
                'ttl_rp' => "=SUM(D18:G18)",

            ],
            'selisih' => [
                'pcs' => "=B13-B19-B20-B21",
                'gr' => "0",
                'rp' => "0",
                'cost_kerja' => 0,

            ],

            'total' => [
                'pcs' => "=B4+B5+B9+B10+B14+B15+B20+B21+B19+B22+B6+B11+B16",
                'gr' => "=C4+C5+C9+C10+C14+C15+C20+C21+C19+C22+C6+C11+C16",
                'rp' => "=D4+D5+D9+D10+D14+D15+D20+D21+D19+D22+D6+D11+D16",
                'cost_kerja' => "=SUM(E2:E22)",
                'cu' => "=SUM(F2:F22)",
                'op' => "=SUM(G2:G22)",
                'ttl_rp' => "=SUM(D23:G23)",
            ],
        ];
        $row = 2;
        foreach ($kolomRekap as $k => $v) {
            $sheet->setCellValue("A$row", $k);
            $sheet->setCellValue("B$row", $v['pcs']);
            $sheet->setCellValue("C$row", $v['gr']);
            $sheet->setCellValue("D$row", $v['rp']);
            $sheet->setCellValue("E$row", $v['cost_kerja']);
            $sheet->setCellValue("F$row", $v['cu'] ?? 0);
            $sheet->setCellValue("G$row", $v['op'] ?? 0);
            $sheet->setCellValue("H$row", $v['ttl_rp'] ?? 0);
            $row++;
        }
        $sheet->getStyle('A1:H19')->applyFromArray($style);

        $warnai = [
            'A4:A6',
            'A9:A11',
            'A14:A16',
            'A19:A22',
        ];
        foreach ($warnai as $w) {
            $sheet->getStyle($w)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('f6a0e0');
        }
    }

    public function index(OpnameNewModel $model)
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

        $this->cabut($spreadsheet, $style_atas, $style, $model);
        $this->cetak($spreadsheet, $style_atas, $style, $model);
        $this->sortir($spreadsheet, $style_atas, $style, $model);
        $this->grading($spreadsheet, $style_atas, $style, $model);
        $this->pengiriman2($spreadsheet, $style_atas, $style, $model);
        $this->rekap($spreadsheet, $style_atas, $style, $model);

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

    public function getCost(CocokanModel $model, $index)
    {
        $a14suntik = $this->getSuntikan(14);
        $a16suntik = $this->getSuntikan(16);
        $a12 = $model::bkselesai_siap_ctk_diserahkan_sum();

        $bk_akhir = new stdClass();
        $bk_akhir->pcs = $a12->pcs + $a14suntik->pcs + $a16suntik->pcs;
        $bk_akhir->gr = $a12->gr + $a14suntik->gr + $a16suntik->gr;
        $bk_akhir->ttl_rp = $a12->ttl_rp + $a14suntik->ttl_rp + $a16suntik->ttl_rp;
        $bk_akhir->cost_kerja = $a12->cost_kerja;

        $ca16suntik = $this->getSuntikan(26);
        $ca16 = $model::cetak_selesai();
        $cetak_akhir = new stdClass();
        $cetak_akhir->pcs = $ca16->pcs + $ca16suntik->pcs;
        $cetak_akhir->gr = $ca16->gr + $ca16suntik->gr;
        $cetak_akhir->ttl_rp = $ca16->ttl_rp + $ca16suntik->ttl_rp;
        $cetak_akhir->cost_kerja = $ca16->cost_kerja;


        $s3 = $model::sortir_akhir();
        $s5suntik = $this->getSuntikan(35);

        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
        $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp;

        $gr_akhir_all = $a12->gr + $a14suntik->gr + $a16suntik->gr + $ca16->gr + $ca16suntik->gr + $s3->gr + $s5suntik->gr;
        $ttl_cost_kerja = $a12->cost_kerja  +  $ca16->cost_kerja +  $s3->cost_kerja;



        $uang_cost = DB::select("SELECT a.* FROM oprasional as a");
        $ttl_cost_op = sumBk($uang_cost, 'total_operasional');





        $cost_dll = DB::selectOne("SELECT sum(`dll`) as dll, max(bulan_dibayar) as bulan FROM `tb_gaji_penutup`");
        $bulan = $cost_dll->bulan;
        $cost_cu = DB::selectOne("SELECT sum(a.ttl_rp) as cost_cu
            FROM cetak_new as a 
            left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
            where b.kategori ='CU' and a.bulan_dibayar BETWEEN '6' and '$bulan';");
        $denda = DB::selectOne("SELECT sum(`nominal`) as ttl_denda FROM `tb_denda` WHERE `bulan_dibayar` BETWEEN '6' and '$bulan';");

        $ttl_semua = $ttl_cost_kerja + $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda;
        $dll = $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda;
        $cost_op = $ttl_cost_op - $ttl_semua;


        $datas = [
            1 => $ttl_cost_kerja,
            'ttl_gr' => $gr_akhir_all,
            'dll' => $cost_dll->dll + $cost_cu->cost_cu - $denda->ttl_denda,
            'cost_op' => $ttl_cost_op - $ttl_semua
        ];
        if (array_key_exists($index, $datas)) {
            return $datas[$index];
        } else {
            return false;
        }
    }

    public function cabutSum($spreadsheet, $style_atas, $style)
    {
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Cabut');
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("H5:N5")->applyFromArray($style_atas);
        $sheet->getStyle('B1:E2')->applyFromArray($style);
        $sheet->getStyle('H1:N5')->applyFromArray($style);
        $sheet->getStyle('Q1:V2')->applyFromArray($style);

        $sheet->getStyle("H3:H4")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFD1DC');

        $koloms = [
            'A1' => 'Awal Cabut',
            'B1' => 'pcs',
            'C1' => 'gr',
            'D1' => 'rp awal',
            'E1' => 'rata2',

            'G1' => 'Cabut Kerja',
            'H1' => 'ket',
            'I1' => 'pcs',
            'J1' => 'gr',
            'K1' => 'rp awal',
            'L1' => 'rata2',
            'M1' => 'cost kerja',
            'N1' => 'total Rp + cost kerja',

            'P1' => 'Akhir Cabut',
            'Q1' => 'Ket',
            'R1' => 'pcs',
            'S1' => 'gr',
            'T1' => 'total rp + cost kerja',
            'U1' => 'rata2 rp + cost kerja',
            'V1' => 'susut'
        ];

        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k, $v);
        }
        $model = new CocokanModel();
        $a11 = $model::bkstockawal_sum();

        $bk_awal = new stdClass();
        $bk_awal->pcs = $a11->pcs;
        $bk_awal->gr = $a11->gr;
        $bk_awal->ttl_rp = $a11->ttl_rp;
        // akhir
        $a12 = $model::bkselesai_siap_ctk_diserahkan_sum();

        $bk_akhir = new stdClass();
        $bk_akhir->pcs = $a12->pcs;
        $bk_akhir->gr = $a12->gr;
        $bk_akhir->ttl_rp = $a12->ttl_rp;
        $bk_akhir->cost_kerja = $a12->cost_kerja;

        $ttl_gr = $this->getCost($model, 'ttl_gr');

        $cbt_proses = $model::bksedang_proses_sum();
        $cbt_sisa_pgws = $model::bksisapgws();

        $sheet->setCellValue('B2', $bk_awal->pcs);
        $sheet->setCellValue('C2', $bk_awal->gr);
        $sheet->setCellValue('D2', $bk_awal->ttl_rp);
        $sheet->setCellValue('E2', $bk_awal->ttl_rp / $bk_awal->gr);


        $datas = [
            [
                'ket' => 'cabut awal',
                'pcs' => $bk_awal->pcs - $cbt_proses->pcs - $cbt_sisa_pgws->pcs,
                'gr' => $bk_awal->gr - $cbt_proses->gr - $cbt_sisa_pgws->gr,
                'ttl_rp' => $bk_awal->ttl_rp - $cbt_proses->ttl_rp - $cbt_sisa_pgws->ttl_rp,
                'rata2' => ($bk_awal->ttl_rp - $cbt_proses->ttl_rp - $cbt_sisa_pgws->ttl_rp) / ($bk_awal->gr - $cbt_proses->gr - $cbt_sisa_pgws->gr),
                'cost_kerja' => $bk_akhir->cost_kerja,
                'ttl' => $bk_awal->ttl_rp - $cbt_proses->ttl_rp - $cbt_sisa_pgws->ttl_rp + $bk_akhir->cost_kerja,
            ],
            [
                'ket' => 'sedang proses',
                'pcs' => $cbt_proses->pcs,
                'gr' => $cbt_proses->gr,
                'ttl_rp' => $cbt_proses->ttl_rp,
                'rata2' => $cbt_proses->ttl_rp / $cbt_proses->gr,
                'cost_kerja' => $cbt_proses->cost_kerja,
                'ttl' => $cbt_proses->cost_kerja + $cbt_proses->ttl_rp,
            ],
            [
                'ket' => 'sisa pengawas',
                'pcs' => $cbt_sisa_pgws->pcs,
                'gr' => $cbt_sisa_pgws->gr,
                'ttl_rp' => $cbt_sisa_pgws->ttl_rp,
                'rata2' => $cbt_sisa_pgws->ttl_rp / $cbt_sisa_pgws->gr,
                'cost_kerja' => 0,
                'ttl' => $cbt_sisa_pgws->ttl_rp,
            ],
            [
                'ket' => 'total',
                'pcs' => "=SUM(I2:I4)",
                'gr' => "=SUM(J2:J4)",
                'ttl_rp' => "=SUM(K2:K4)",
                'rata2' => "=K5/J5",
                'cost_kerja' => "=SUM(M2:M4)",
                'ttl' => "=SUM(N2:N4)",
            ],
        ];

        foreach ($datas as $i => $v) {
            $row = $i + 2;
            $sheet->setCellValue('H' . $row, $v['ket']);
            $sheet->setCellValue('I' . $row, $v['pcs']);
            $sheet->setCellValue('J' . $row, $v['gr']);
            $sheet->setCellValue('K' . $row, $v['ttl_rp']);
            $sheet->setCellValue('L' . $row, $v['rata2']);
            $sheet->setCellValue('M' . $row, $v['cost_kerja']);
            $sheet->setCellValue('N' . $row, $v['ttl']);
        }

        $sheet->setCellValue('Q2', 'akhir cabut');
        $sheet->setCellValue('R2', $bk_akhir->pcs);
        $sheet->setCellValue('S2', $bk_akhir->gr);
        $sheet->setCellValue('T2', $bk_akhir->ttl_rp + $bk_akhir->cost_kerja);
        $sheet->setCellValue('U2', ($bk_akhir->ttl_rp + $bk_akhir->cost_kerja) / $bk_akhir->gr);
        $sheet->setCellValue('V2', number_format((1 - $bk_akhir->gr / ($bk_awal->gr - $cbt_proses->gr - $cbt_sisa_pgws->gr)) * 100, 0) . '%');


        // $sheet->getStyle('A1:V1')->applyFromArray($style);
    }

    public function cetakSum($spreadsheet, $style_atas, $style)
    {
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Cetak');
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("H5:N5")->applyFromArray($style_atas);
        $sheet->getStyle('B1:E2')->applyFromArray($style);
        $sheet->getStyle('H1:N5')->applyFromArray($style);
        $sheet->getStyle('Q1:V2')->applyFromArray($style);

        $sheet->getStyle("H3:H4")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFD1DC');

        $koloms = [
            'A1' => 'Akhir Cabut',
            'B1' => 'pcs',
            'C1' => 'gr',
            'D1' => 'rp awal',
            'E1' => 'rata2',

            'G1' => 'Awal Cetak',
            'H1' => 'ket',
            'I1' => 'pcs',
            'J1' => 'gr',
            'K1' => 'rp awal',
            'L1' => 'rata2',
            'M1' => 'cost kerja',
            'N1' => 'total Rp + cost kerja',

            'P1' => 'Akhir Cetak',
            'Q1' => 'Ket',
            'R1' => 'pcs',
            'S1' => 'gr',
            'T1' => 'total rp + cost kerja',
            'U1' => 'rata2 rp + cost kerja',
            'V1' => 'susut'
        ];

        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k, $v);
        }
        $model = new CocokanModel();
        $ca11 = $this->getSuntikan(21);
        $ctk_opname = new stdClass();
        $ctk_opname->pcs = $ca11->pcs;
        $ctk_opname->gr = $ca11->gr;
        $ctk_opname->ttl_rp = $ca11->ttl_rp;

        $ca2 = $model::cetak_stok_awal();

        $ca12suntik = $this->getSuntikan(23);
        $akhir_cbt = new stdClass();
        $akhir_cbt->pcs = $ca2->pcs + $ca12suntik->pcs;
        $akhir_cbt->gr = $ca2->gr + $ca12suntik->gr;
        $akhir_cbt->ttl_rp = $ca2->ttl_rp + $ca12suntik->ttl_rp + $ca2->cost_kerja;



        $ca17 = $model::cetak_stok();
        $ca17suntik = $this->getSuntikan(27);


        $cetak_sisa = new stdClass();
        $cetak_sisa->pcs = $ca17->pcs + $ca17suntik->pcs;
        $cetak_sisa->gr = $ca17->gr + $ca17suntik->gr;
        $cetak_sisa->ttl_rp = $ca17->ttl_rp + $ca17suntik->ttl_rp;

        $ca16suntik = $this->getSuntikan(26);
        $ca16 = $model::cetak_selesai();
        $cetak_akhir = new stdClass();
        $cetak_akhir->pcs = $ca16->pcs + $ca16suntik->pcs;
        $cetak_akhir->gr = $ca16->gr + $ca16suntik->gr;
        $cetak_akhir->ttl_rp = $ca16->ttl_rp + $ca16suntik->ttl_rp;
        $cetak_akhir->cost_kerja = $ca16->cost_kerja;

        $ttl_gr = $this->getCost($model, 'ttl_gr');
        $cost_op = $this->getCost($model, 'cost_op');
        $cost_dll = $this->getCost($model, 'dll');
        $cetak_proses = $model::cetak_proses();

        $sheet->setCellValue('B2', $akhir_cbt->pcs);
        $sheet->setCellValue('C2', $akhir_cbt->gr);
        $sheet->setCellValue('D2', $akhir_cbt->ttl_rp);
        $sheet->setCellValue('E2', $akhir_cbt->ttl_rp / $akhir_cbt->gr);


        $datas = [
            [
                'ket' => 'cetak awal',
                'pcs' => $ctk_opname->pcs + $akhir_cbt->pcs - $cetak_proses->pcs - $cetak_sisa->pcs,
                'gr' => $ctk_opname->gr + $akhir_cbt->gr - $cetak_proses->gr - $cetak_sisa->gr,
                'ttl_rp' => $ctk_opname->ttl_rp + $akhir_cbt->ttl_rp - $cetak_proses->ttl_rp - $cetak_sisa->ttl_rp,
                'rata2' => ($ctk_opname->ttl_rp + $akhir_cbt->ttl_rp - $cetak_proses->ttl_rp - $cetak_sisa->ttl_rp) / ($ctk_opname->gr + $akhir_cbt->gr - $cetak_proses->gr - $cetak_sisa->gr),
                'cost_kerja' => $cetak_akhir->cost_kerja,
                'ttl' => $ctk_opname->ttl_rp + $akhir_cbt->ttl_rp - $cetak_proses->ttl_rp - $cetak_sisa->ttl_rp + $cetak_akhir->cost_kerja,
            ],
            [
                'ket' => 'sedang proses',
                'pcs' => $cetak_proses->pcs,
                'gr' => $cetak_proses->gr,
                'ttl_rp' => $cetak_proses->ttl_rp,
                'rata2' => $cetak_proses->ttl_rp / $cetak_proses->gr,
                'cost_kerja' => $cetak_proses->cost_kerja,
                'ttl' => $cetak_proses->cost_kerja + $cetak_proses->ttl_rp,
            ],
            [
                'ket' => 'sisa pengawas',
                'pcs' => $cetak_sisa->pcs,
                'gr' => $cetak_sisa->gr,
                'ttl_rp' => $cetak_sisa->ttl_rp,
                'rata2' => $cetak_sisa->ttl_rp / $cetak_sisa->gr,
                'cost_kerja' => 0,
                'ttl' => $cetak_sisa->ttl_rp,
            ],
            [
                'ket' => 'total',
                'pcs' => "=SUM(I2:I4)",
                'gr' => "=SUM(J2:J4)",
                'ttl_rp' => "=SUM(K2:K4)",
                'rata2' => "=K5/J5",
                'cost_kerja' => "=SUM(M2:M4)",
                'ttl' => "=SUM(N2:N4)",
            ],
        ];

        foreach ($datas as $i => $v) {
            $row = $i + 2;
            $sheet->setCellValue('H' . $row, $v['ket']);
            $sheet->setCellValue('I' . $row, $v['pcs']);
            $sheet->setCellValue('J' . $row, $v['gr']);
            $sheet->setCellValue('K' . $row, $v['ttl_rp']);
            $sheet->setCellValue('L' . $row, $v['rata2']);
            $sheet->setCellValue('M' . $row, $v['cost_kerja']);
            $sheet->setCellValue('N' . $row, $v['ttl']);
        }

        $sheet->setCellValue('Q2', 'akhir cetak');
        $sheet->setCellValue('R2', $cetak_akhir->pcs);
        $sheet->setCellValue('S2', $cetak_akhir->gr);
        $sheet->setCellValue('T2', $cetak_akhir->ttl_rp + $cetak_akhir->cost_kerja);
        $sheet->setCellValue('U2', ($cetak_akhir->ttl_rp + $cetak_akhir->cost_kerja) / $cetak_akhir->gr);
        $sheet->setCellValue('V2', number_format((1 - $cetak_akhir->gr / ($ctk_opname->gr + $akhir_cbt->gr - $cetak_proses->gr - $cetak_sisa->gr)) * 100, 0) . '%');
    }

    public function sortirSum($spreadsheet, $style_atas, $style)
    {
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Sortir');
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("H5:N5")->applyFromArray($style_atas);
        $sheet->getStyle('B1:E2')->applyFromArray($style);
        $sheet->getStyle('H1:N5')->applyFromArray($style);
        $sheet->getStyle('Q1:V2')->applyFromArray($style);


        $sheet->getStyle("H3:H4")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFD1DC');

        $koloms = [
            'A1' => 'Akhir Cetak',
            'B1' => 'pcs',
            'C1' => 'gr',
            'D1' => 'rp awal',
            'E1' => 'rata2',

            'G1' => 'Sortir Kerja',
            'H1' => 'ket',
            'I1' => 'pcs',
            'J1' => 'gr',
            'K1' => 'rp awal',
            'L1' => 'rata2',
            'M1' => 'cost kerja',
            'N1' => 'total Rp + cost kerja',

            'P1' => 'Akhir Sortir',
            'Q1' => 'Ket',
            'R1' => 'pcs',
            'S1' => 'gr',
            'T1' => 'total rp + cost kerja',
            'U1' => 'rata2 rp + cost kerja',
            'V1' => 'susut'
        ];

        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k, $v);
        }
        $model = new CocokanModel();
        $s1 = $model::stock_sortir_awal();
        $s2suntik = $this->getSuntikan(32);
        $akhir_cetak = new stdClass();
        $akhir_cetak->pcs = $s1->pcs + $s2suntik->pcs;
        $akhir_cetak->gr = $s1->gr + $s2suntik->gr;
        $akhir_cetak->ttl_rp = $s1->ttl_rp + $s2suntik->ttl_rp;

        $s3 = $model::sortir_akhir();

        $s5suntik = $this->getSuntikan(35);

        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
        $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp;
        $sortir_akhir->cost_kerja = $s3->cost_kerja;

        $ttl_gr = $this->getCost($model, 'ttl_gr');
        $cost_op = $this->getCost($model, 'cost_op');
        $cost_dll = $this->getCost($model, 'dll');
        $opname = $this->getSuntikan(31);
        $sedang_proses = $model::sortir_proses();
        $sortir_sisa = $model::stock_sortir();

        $sheet->setCellValue('B2', $akhir_cetak->pcs);
        $sheet->setCellValue('C2', $akhir_cetak->gr);
        $sheet->setCellValue('D2', $akhir_cetak->ttl_rp);
        $sheet->setCellValue('E2', $akhir_cetak->ttl_rp / $akhir_cetak->gr);


        $datas = [
            [
                'ket' => 'awal sortir',
                'pcs' => $akhir_cetak->pcs + $opname->pcs - $sedang_proses->pcs - $sortir_sisa->pcs,
                'gr' => $akhir_cetak->gr + $opname->gr - $sedang_proses->gr - $sortir_sisa->gr,
                'ttl_rp' => $akhir_cetak->ttl_rp + $opname->ttl_rp - $sedang_proses->ttl_rp - $sortir_sisa->ttl_rp,
                'rata2' => ($akhir_cetak->ttl_rp + $opname->ttl_rp - $sedang_proses->ttl_rp - $sortir_sisa->ttl_rp) / ($akhir_cetak->gr + $opname->gr - $sedang_proses->gr - $sortir_sisa->gr),
                'cost_kerja' => $sortir_akhir->cost_kerja,
                'ttl' => $akhir_cetak->ttl_rp + $opname->ttl_rp - $sedang_proses->ttl_rp - $sortir_sisa->ttl_rp + $sortir_akhir->cost_kerja,
            ],
            [
                'ket' => 'sedang proses',
                'pcs' => $sedang_proses->pcs,
                'gr' => $sedang_proses->gr,
                'ttl_rp' => $sedang_proses->ttl_rp,
                'rata2' => empty($sedang_proses->gr) ? 0 : $sedang_proses->ttl_rp / $sedang_proses->gr,
                'cost_kerja' => 0,
                'ttl' => $sedang_proses->ttl_rp,
            ],
            [
                'ket' => 'sisa pengawas',
                'pcs' => $sortir_sisa->pcs,
                'gr' => $sortir_sisa->gr,
                'ttl_rp' => $sortir_sisa->ttl_rp,
                'rata2' => empty($sortir_sisa->gr) ? 0 : $sortir_sisa->ttl_rp / $sortir_sisa->gr,
                'cost_kerja' => 0,
                'ttl' => $sortir_sisa->ttl_rp,
            ],
            [
                'ket' => 'total',
                'pcs' => "=SUM(I2:I4)",
                'gr' => "=SUM(J2:J4)",
                'ttl_rp' => "=SUM(K2:K4)",
                'rata2' => "=K5/J5",
                'cost_kerja' => "=SUM(M2:M4)",
                'ttl' => "=SUM(N2:N4)",
            ],
        ];

        foreach ($datas as $i => $v) {
            $row = $i + 2;
            $sheet->setCellValue('H' . $row, $v['ket']);
            $sheet->setCellValue('I' . $row, $v['pcs']);
            $sheet->setCellValue('J' . $row, $v['gr']);
            $sheet->setCellValue('K' . $row, $v['ttl_rp']);
            $sheet->setCellValue('L' . $row, $v['rata2']);
            $sheet->setCellValue('M' . $row, $v['cost_kerja']);
            $sheet->setCellValue('N' . $row, $v['ttl']);
        }

        $sheet->setCellValue('Q2', 'akhir sortir');
        $sheet->setCellValue('R2', $sortir_akhir->pcs);
        $sheet->setCellValue('S2', $sortir_akhir->gr);
        $sheet->setCellValue('T2', $sortir_akhir->ttl_rp + $sortir_akhir->cost_kerja);
        $sheet->setCellValue('U2', ($sortir_akhir->ttl_rp + $sortir_akhir->cost_kerja) / $sortir_akhir->gr);
        $sheet->setCellValue('V2', number_format((1 - $sortir_akhir->gr / ($akhir_cetak->gr + $opname->gr - $sedang_proses->gr - $sortir_sisa->gr)) * 100, 0) . '%');
    }

    public function gradingSum($spreadsheet, $style_atas, $style)
    {
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Grading');
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("H5:N5")->applyFromArray($style_atas);
        $sheet->getStyle('B1:E2')->applyFromArray($style);
        $sheet->getStyle('H1:N5')->applyFromArray($style);
        $sheet->getStyle('Q1:V2')->applyFromArray($style);


        $sheet->getStyle("H4:H4")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFD1DC');

        $koloms = [
            'A1' => 'Akhir Sortir',
            'B1' => 'pcs',
            'C1' => 'gr',
            'D1' => 'rp awal',
            'E1' => 'rata2',

            'G1' => 'Grading Kerja',
            'H1' => 'ket',
            'I1' => 'pcs',
            'J1' => 'gr',
            'K1' => 'rp awal',
            'L1' => 'rata2',
            'M1' => 'cost kerja',
            'N1' => 'total Rp + cost kerja',

            'P1' => 'Akhir Grading',
            'Q1' => 'Ket',
            'R1' => 'pcs',
            'S1' => 'gr',
            'T1' => 'total rp + cost kerja',
            'U1' => 'rata2 rp + cost kerja',
            'V1' => 'susut'
        ];

        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k, $v);
        }
        $model = new CocokanModel();
        $s3 = $model::sortir_akhir();

        $s5suntik = $this->getSuntikan(35);
        $opname =  $this->getSuntikan(41);

        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
        $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp + $s3->cost_kerja;

        $grading = $model->gradingOne();
        $grading_sisa = $model->gradingSisaOne();
        $sumTtlRpPengiriman = DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp FROM pengiriman as a ");

        $sheet->setCellValue('B2', $sortir_akhir->pcs);
        $sheet->setCellValue('C2', $sortir_akhir->gr);
        $sheet->setCellValue('D2', $sortir_akhir->ttl_rp);
        $sheet->setCellValue('E2', $sortir_akhir->ttl_rp / $sortir_akhir->gr);


        $datas = [
            [
                'ket' => 'awal grading',
                'pcs' => $sortir_akhir->pcs + $opname->pcs - $grading_sisa->pcs,
                'gr' => $sortir_akhir->gr + $opname->gr - $grading_sisa->gr,
                'ttl_rp' => $sortir_akhir->ttl_rp + $opname->ttl_rp - $grading_sisa->cost_bk,
                'rata2' => ($sortir_akhir->ttl_rp + $opname->ttl_rp - $grading_sisa->cost_bk) / ($sortir_akhir->gr + $opname->gr - $grading_sisa->gr),
                'cost_kerja' => 0,
                'ttl' => $sortir_akhir->ttl_rp + $opname->ttl_rp - $grading_sisa->cost_bk,
            ],
            [
                'ket' => 'selisih pcs',
                'pcs' => $sortir_akhir->pcs + $opname->pcs - $grading->pcs - $grading_sisa->pcs,
                'gr' => 0,
                'ttl_rp' => 0,
                'rata2' => 0,
                'cost_kerja' => 0,
                'ttl' => 0,
            ],
            [
                'ket' => 'sisa belum grading',
                'pcs' => $grading_sisa->pcs,
                'gr' => $grading_sisa->gr,
                'ttl_rp' => $grading_sisa->cost_bk,
                'rata2' => empty($grading_sisa->gr) ? 0 : $grading_sisa->cost_bk / $grading_sisa->gr,
                'cost_kerja' => 0,
                'ttl' => $grading_sisa->cost_bk,
            ],
            [
                'ket' => 'total',
                'pcs' => "=SUM(I2:I4)",
                'gr' => "=SUM(J2:J4)",
                'ttl_rp' => "=SUM(K2:K4)",
                'rata2' => "=K5/J5",
                'cost_kerja' => "=SUM(M2:M4)",
                'ttl' => "=SUM(N2:N4)",
            ],
        ];

        foreach ($datas as $i => $v) {
            $row = $i + 2;
            $sheet->setCellValue('H' . $row, $v['ket']);
            $sheet->setCellValue('I' . $row, $v['pcs']);
            $sheet->setCellValue('J' . $row, $v['gr']);
            $sheet->setCellValue('K' . $row, $v['ttl_rp']);
            $sheet->setCellValue('L' . $row, $v['rata2']);
            $sheet->setCellValue('M' . $row, $v['cost_kerja']);
            $sheet->setCellValue('N' . $row, $v['ttl']);
        }

        $sheet->setCellValue('Q2', 'akhir grading');
        $sheet->setCellValue('R2', $grading->pcs);
        $sheet->setCellValue('S2', $grading->gr);
        $sheet->setCellValue('T2', $grading->cost_bk);
        $sheet->setCellValue('U2', $grading->cost_bk / $grading->gr);
        $sheet->setCellValue('V2', number_format((1 - $grading->gr / ($sortir_akhir->gr + $opname->gr - $grading_sisa->gr)) * 100, 0) . '%');
    }

    public function pengirimanSum($spreadsheet, $style_atas, $style)
    {
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Pengiriman');
        $sheet  = $spreadsheet->addSheet($worksheet);
        $sheet->getStyle("G4:L4")->applyFromArray($style_atas);
        $sheet->getStyle('B1:D2')->applyFromArray($style);
        $sheet->getStyle('G1:L4')->applyFromArray($style);

        $sheet->getStyle("G2:G3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFD1DC');

        $koloms = [
            'A1' => 'Grading akhir',
            'B1' => 'pcs',
            'C1' => 'gr',
            'D1' => 'rp',

            'F1' => 'Pengiriman',
            'G1' => 'ket',
            'H1' => 'pcs',
            'I1' => 'gr',
            'J1' => 'rp',
            'K1' => 'cost kerja',
            'L1' => 'total Rp',

            'N1' => 'List pengiriman',
            'O1' => '#',
            'P1' => 'Tgl kirim',
            'Q1' => 'No packinglist',
            'R1' => 'Nama packinglist',
            'S1' => 'Tujuan',
            'T1' => 'Box',
            'U1' => 'Pcs',
            'V1' => 'Gr',
            'W1' => 'Gr + kadar',
            'X1' => 'Total Rp',
            'Y1' => 'Rat2',
        ];

        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k, $v);
        }
        $model = new CocokanModel();
        $sa = $model::sortir_akhir();

        $p2suntik = $this->getSuntikan(42);
        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $sa->pcs + $p2suntik->pcs;
        $sortir_akhir->gr = $sa->gr + $p2suntik->gr;
        $sortir_akhir->ttl_rp = $sa->ttl_rp + $p2suntik->ttl_rp;

        $pengiriman = Grading::pengirimanSum();

        $grading = $model->gradingOne();
        $grading_sisa = DB::selectOne("SELECT a.no_box_sortir, sum(b.pcs_awal - d.pcs) as pcs , sum(b.gr_awal - d.gr) as gr FROM grading as a left join formulir_sarang as b on b.no_box = a.no_box_sortir AND b.kategori = 'grade' JOIN bk as e on e.no_box = b.no_box AND e.kategori = 'cabut' LEFT JOIN( select no_box_sortir as no_box,sum(pcs) as pcs,sum(gr) as gr from grading group by no_box_sortir ) as d on d.no_box = a.no_box_sortir WHERE a.selesai = 'T';");

        $sumTtlRpPengiriman = DB::selectOne("SELECT sum(a.ttl_rp) as ttl_rp FROM pengiriman as a ");

        $belum_kirim = Grading::belumKirimSum();

        $list_pengiriman = Grading::list_pengiriman_sum();

        $sheet->setCellValue('B2', $grading->pcs);
        $sheet->setCellValue('C2', $grading->gr);
        $sheet->setCellValue('D2', $grading->cost_bk + $grading->cost_kerja + $grading->cost_op);


        $datas = [
            [
                'ket' => 'pengiriman',
                'pcs' => $pengiriman->pcs,
                'gr' => $pengiriman->gr,
                'rp' => $pengiriman->cost_bk + $pengiriman->cost_op + $pengiriman->cost_kerja + $pengiriman->cost_cu,
                'cost_kerja' => 0,
                'ttl_rp' => $pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op,
            ],
            [
                'ket' => 'Sisa belum kirim',
                'pcs' => $belum_kirim->pcs,
                'gr' => $belum_kirim->gr,
                'rp' => $belum_kirim->cost_bk + $belum_kirim->cost_kerja + $belum_kirim->cost_op,
                'cost_kerja' => 0,
                'ttl_rp' => $belum_kirim->cost_op + $belum_kirim->cost_cu + $belum_kirim->cost_kerja + $belum_kirim->cost_bk,
            ],
            [
                'ket' => 'total',
                'pcs' => "=SUM(I2:I4)",
                'gr' => "=SUM(J2:J4)",
                'rp' => "=SUM(K2:K4)",
                'cost_kerja' => "=SUM(M2:M4)",
                'ttl_rp' => "=SUM(N2:N4)",
            ],
        ];

        foreach ($datas as $i => $v) {
            $row = $i + 2;
            $sheet->setCellValue('G' . $row, $v['ket']);
            $sheet->setCellValue('H' . $row, $v['pcs']);
            $sheet->setCellValue('I' . $row, $v['gr']);
            $sheet->setCellValue('J' . $row, $v['rp']);
            $sheet->setCellValue('K' . $row, $v['cost_kerja']);
            $sheet->setCellValue('L' . $row, $v['ttl_rp']);
        }

        foreach ($list_pengiriman as $i => $d) {
            $row = $i + 2;
            $sheet->setCellValue('O' . $row, $i + 1);
            $sheet->setCellValue('P' . $row, tanggal($d->tgl));
            $sheet->setCellValue('Q' . $row, "PI$d->no_nota");
            $sheet->setCellValue('R' . $row, ucwords($d->nm_packing));
            $sheet->setCellValue('S' . $row, strtoupper($d->tujuan));
            $sheet->setCellValue('T' . $row, $d->ttl_box);
            $sheet->setCellValue('U' . $row, $d->pcs);
            $sheet->setCellValue('V' . $row, $d->gr);
            $sheet->setCellValue('W' . $row, $d->gr_naik);
            $sheet->setCellValue('X' . $row, $d->cost_bk + $d->cost_op + $d->cost_kerja + $d->cost_cu);
            $sheet->setCellValue('Y' . $row, ($d->cost_bk + $d->cost_op + $d->cost_kerja + $d->cost_cu) / $d->gr);
        }
        $rowTbh = $row + 1;
        $sheet->setCellValue('O' . $rowTbh, "Total");
        $sheet->setCellValue('T' . $rowTbh, "=SUM(T1:T$row)");
        $sheet->setCellValue('U' . $rowTbh, "=SUM(U1:U$row)");
        $sheet->setCellValue('V' . $rowTbh, "=SUM(V1:V$row)");
        $sheet->setCellValue('W' . $rowTbh, "=SUM(W1:W$row)");
        $sheet->setCellValue('X' . $rowTbh, "=SUM(X1:X$row)");
        $sheet->setCellValue('Y' . $rowTbh, "=SUM(X1:X$row) / SUM(V1:V$row)");

        $sheet->getStyle("O$rowTbh:Y$rowTbh")->applyFromArray($style_atas);

        $sheet->getStyle('O1:Y' . $rowTbh)->applyFromArray($style);
    }

    public function balanceSum($spreadsheet, $style_atas, $style)
    {
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Balancesheet');
        $sheet  = $spreadsheet->addSheet($worksheet);

        $sheet->getStyle("B1:H1")->applyFromArray($style_atas);

        $koloms = [
            'A1' => 'bk kerja',
            'B1' => 'No',
            'C1' => 'Bulan kerja',
            'D1' => 'Nama part',
            'E1' => 'Grade',
            'F1' => 'Pcs',
            'G1' => 'Gr',
            'H1' => 'Ttl Rp',

            'J1' => 'Cost Perbulan',
            'K1' => 'bulan & tahun',
            'L1' => 'gaji',
            'M1' => 'cost operasional',
            'N1' => 'total rp',

            'P1' => 'Bk Rp',
            'Q1' => 'ket',
            'R1' => 'pcs',
            'S1' => 'gr',
            'T1' => 'Total Rp',
        ];

        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k, $v);
        }

        $bk = SummaryModel::summarybk();

        foreach ($bk as $i => $b) {
            $row = $i + 2;
            $sheet->setCellValue('B' . $row, $i + 1);
            $sheet->setCellValue('C' . $row, empty($b->bulan) ? '-' : date('F Y', strtotime('01-' . $b->bulan . '-' . $b->tahun)));
            $sheet->setCellValue('D' . $row, $b->nm_partai);
            $sheet->setCellValue('E' . $row, $b->grade);
            $sheet->setCellValue('F' . $row, $b->pcs_bk);
            $sheet->setCellValue('G' . $row, $b->gr_bk);
            $sheet->setCellValue('H' . $row, $b->cost_bk);
        }

        $rowTbh = $row + 1;
        $sheet->setCellValue('B' . $rowTbh, "Total");
        $sheet->setCellValue('F' . $rowTbh, "=SUM(F2:F$row)");
        $sheet->setCellValue('G' . $rowTbh, "=SUM(G2:G$row)");
        $sheet->setCellValue('H' . $rowTbh, "=SUM(H2:H$row)");

        $sheet->getStyle("B$rowTbh:H$rowTbh")->applyFromArray($style_atas);

        $sheet->getStyle('B1:H' . $rowTbh)->applyFromArray($style);

        $uang_cost = BalanceModel::uangCost();

        foreach ($uang_cost as $i => $u) {
            $row = $i + 2;
            $sheet->setCellValue('K' . $row, date('F Y', strtotime($u->tahun . '-' . $u->bulan . '-01')));
            $sheet->setCellValue('L' . $row, $u->gaji);
            $sheet->setCellValue('M' . $row, $u->total_operasional - $u->gaji);
            $sheet->setCellValue('N' . $row, $u->total_operasional);
        }

        $rowTbhCost = $row + 1;
        $sheet->setCellValue('K' . $rowTbhCost, "Total");
        $sheet->setCellValue('L' . $rowTbhCost, "=SUM(L2:L$row)");
        $sheet->setCellValue('M' . $rowTbhCost, "=SUM(M2:M$row)");
        $sheet->setCellValue('N' . $rowTbhCost, "=SUM(N2:N$row)");

        $sheet->setCellValue('N' . $rowTbhCost, "=SUM(N2:N$row)");

        $model = new CocokanModel();

        $grading = Grading::belumKirimSum();
        $pengiriman = Grading::pengirimanSum();
        $grading_sisa = CocokanModel::gradingSisaOne();
        $cbt_proses = CocokanModel::bksedang_proses_sum();
        $cbt_sisa_pgws = CocokanModel::bksisapgws();
        $cetak_proses = CocokanModel::cetak_proses_balance();
        $cbt_blm_kirim = CocokanModel::bksedang_selesai_sum();
        $ca17 = CocokanModel::cetak_stok_balance();
        $ca17suntik = $this->getSuntikan(27);
        $s3 = $model::sortir_akhir();
        $s5suntik = $this->getSuntikan(35);

        $sortir_akhir = new stdClass();
        $sortir_akhir->pcs = $s3->pcs + $s5suntik->pcs;
        $sortir_akhir->gr = $s3->gr + $s5suntik->gr;
        $sortir_akhir->ttl_rp = $s3->ttl_rp + $s5suntik->ttl_rp;
        $sortir_akhir->cost_kerja = $s3->cost_kerja;
        $cetak_sisa = new stdClass();
        $cetak_sisa->pcs = $ca17->pcs + $ca17suntik->pcs;
        $cetak_sisa->gr = $ca17->gr + $ca17suntik->gr;
        $cetak_sisa->ttl_rp = $ca17->ttl_rp + $ca17suntik->ttl_rp + $ca17->cost_kerja;
        $opname =  $this->getSuntikan(41);

        $cetak_sisa = $cetak_sisa;
        $sedang_proses = CocokanModel::sortir_proses_balance();
        $sortir_sisa = CocokanModel::sortir_stock_balance();
        $cabut_selesai_siap_cetak = OpnameNewModel::bksedang_selesai_sum();
        $cetak_selesai = OpnameNewModel::cetak_selesai();
        $sortir_selesai = OpnameNewModel::sortir_selesai();
        $grading_susut = Grading::belumKirimSumsusut();
        $bk_suntik = DB::select("SELECT * FROM opname_suntik WHERE opname = 'Y'");

        $ttl_sisa_belum_kirim =
            $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op;

        $ttl_pengiriman =
            $pengiriman->cost_bk +
            $pengiriman->cost_kerja +
            $pengiriman->cost_cu +
            $pengiriman->cost_op;

        $ttl_sisa_blum_grading = $grading_sisa->cost_bk;

        $ttl_cost_berjalan =
            $cbt_proses->ttl_rp +
            $cbt_sisa_pgws->ttl_rp +
            $cetak_proses->ttl_rp +
            $cetak_proses->cost_kerja +
            $cbt_blm_kirim->cost_kerja +
            $cetak_sisa->ttl_rp +
            $sedang_proses->ttl_rp +
            $sedang_proses->cost_kerja +
            $sortir_sisa->ttl_rp +
            $sortir_sisa->cost_kerja +
            $ttl_sisa_blum_grading +
            $ttl_pengiriman +
            $ttl_sisa_belum_kirim +
            sumBk($cabut_selesai_siap_cetak, 'ttl_rp') +
            sumBk($sortir_selesai, 'ttl_rp') +
            sumBk($cetak_selesai, 'ttl_rp') +
            sumBk($cabut_selesai_siap_cetak, 'cost_kerja') +
            sumBk($sortir_selesai, 'cost_kerja') +
            sumBk($cetak_selesai, 'cost_kerja') +
            $grading_susut->cost_bk +
            $grading_susut->cost_kerja +
            $grading_susut->cost_cu +
            $grading_susut->cost_op;

        $ttl_berjalan =
            $ttl_cost_berjalan -
            sumBk($uang_cost, 'total_operasional') -
            sumBk($bk, 'cost_bk') -
            sumBk($bk_suntik, 'ttl_rp');


        $rowCost = $rowTbhCost + 1;
        $sheet->setCellValue('K' . $rowCost, "Cost berjalan");
        $sheet->setCellValue('N' . $rowCost, $ttl_cost_berjalan - sumBk($uang_cost, 'total_operasional') - sumBk($bk, 'cost_bk') - sumBk($bk_suntik, 'ttl_rp'));

        $rowTtl = $rowCost + 1;
        $sheet->setCellValue('K' . $rowTtl, "Total Bk + Operasional + cost berjalan");
        $sheet->setCellValue('N' . $rowTtl, sumBk($uang_cost, 'total_operasional') + sumBk($bk, 'cost_bk') + sumBk($bk_suntik, 'ttl_rp') + $ttl_berjalan);

        $sheet->getStyle("K$rowTbhCost:N$rowTbhCost")->applyFromArray($style_atas);

        $sheet->getStyle('K1:N' . $rowTtl)->applyFromArray($style);

        $pcs_sisa_grading = $grading_sisa->pcs ?? 0;
        $datas = [
            [
                'ket' => 'cabut sedang proses',
                'pcs' => $cbt_proses->pcs,
                'gr' => $cbt_proses->gr,
                'ttl_rp' => $cbt_proses->ttl_rp,
            ],
            [
                'ket' => 'cabut sisa pengawas',
                'pcs' => $cbt_sisa_pgws->pcs,
                'gr' => $cbt_sisa_pgws->gr,
                'ttl_rp' => $cbt_sisa_pgws->ttl_rp,
            ],
            [
                'ket' => 'Cabut selesai siap cetak belum kirim',
                'pcs' => sumBk($cabut_selesai_siap_cetak, 'pcs'),
                'gr' => sumBk($cabut_selesai_siap_cetak, 'gr'),
                'ttl_rp' => sumBk($cabut_selesai_siap_cetak, 'ttl_rp') + sumBk($cabut_selesai_siap_cetak, 'cost_kerja'),
            ],
            [
                'ket' => 'Cetak sedang Proses',
                'pcs' => $cetak_proses->pcs,
                'gr' => $cetak_proses->gr,
                'ttl_rp' => $cetak_proses->ttl_rp + $cetak_proses->cost_kerja,
            ],
            [
                'ket' => 'Cetak sisa Pengawas',
                'pcs' => $cetak_sisa->pcs,
                'gr' => $cetak_sisa->gr,
                'ttl_rp' => $cetak_sisa->ttl_rp,
            ],
            [
                'ket' => 'Cetak selesai siap sortir belum kirim',
                'pcs' => sumBk($cetak_selesai, 'pcs'),
                'gr' => sumBk($cetak_selesai, 'gr'),
                'ttl_rp' => sumBk($cetak_selesai, 'ttl_rp') + sumBk($cetak_selesai, 'cost_kerja'),
            ],
            [
                'ket' => 'Sortir sedang Proses',
                'pcs' => $sedang_proses->pcs,
                'gr' => $sedang_proses->gr,
                'ttl_rp' => $sedang_proses->ttl_rp + $sedang_proses->cost_kerja,
            ],
            [
                'ket' => 'Sortir sisa Pengawas',
                'pcs' => $sortir_sisa->pcs,
                'gr' => $sortir_sisa->gr,
                'ttl_rp' => $sortir_sisa->ttl_rp + $sortir_sisa->cost_kerja,
            ],
            [
                'ket' => 'Sortir selesai siap grading belum kirim',
                'pcs' => sumBk($sortir_selesai, 'pcs'),
                'gr' => sumBk($sortir_selesai, 'gr'),
                'ttl_rp' => sumBk($sortir_selesai, 'ttl_rp') + sumBk($sortir_selesai, 'cost_kerja'),
            ],
            [
                'ket' => 'Sisa belum grading',
                'pcs' => $grading_sisa->pcs ?? 0,
                'gr' => $grading_sisa->gr ?? 0,
                'ttl_rp' => $grading_sisa->cost_bk,
            ],
            [
                'ket' => 'Pengiriman',
                'pcs' => $pengiriman->pcs ?? 0,
                'gr' => $pengiriman->gr ?? 0,
                'ttl_rp' => $pengiriman->cost_bk + $pengiriman->cost_kerja + $pengiriman->cost_cu + $pengiriman->cost_op,
            ],
            [
                'ket' => 'Sisa belum kirim ( sisa + qc)',
                'pcs' => $grading->pcs ?? 0,
                'gr' => $grading->gr ?? 0,
                'ttl_rp' => $grading->cost_bk + $grading->cost_kerja + $grading->cost_cu + $grading->cost_op + $grading_susut->cost_bk + $grading_susut->cost_kerja + $grading_susut->cost_cu + $grading_susut->cost_op,
            ],
            [
                'ket' => 'Selisih',
                'pcs' => $sortir_akhir->pcs + $opname->pcs - $grading->pcs - $pengiriman->pcs - $pcs_sisa_grading,
                'gr' => 0,
                'ttl_rp' => 0,
            ],
        ];

        foreach ($datas as $i => $v) {
            $row = $i + 2;
            $sheet->setCellValue('Q' . $row, $v['ket']);
            $sheet->setCellValue('R' . $row, $v['pcs']);
            $sheet->setCellValue('S' . $row, $v['gr']);
            $sheet->setCellValue('T' . $row, $v['ttl_rp']);
        }

        $rowTbhCost = $row + 1;
        $sheet->setCellValue('Q' . $rowTbhCost, "Total");
        $sheet->setCellValue('R' . $rowTbhCost, "=SUM(R2:R$row)");
        $sheet->setCellValue('S' . $rowTbhCost, "=SUM(S2:S$row)");
        $sheet->setCellValue('T' . $rowTbhCost, "=SUM(T2:T$row)");

        $sheet->getStyle("Q$rowTbhCost:T$rowTbhCost")->applyFromArray($style_atas);
        $sheet->getStyle('Q1:T' . $rowTbhCost)->applyFromArray($style);
    }

    public function exportCabut(Request $r)
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
        // Hapus worksheet default
        $spreadsheet->removeSheetByIndex(0);

        $divisi = $r->divisi;

        if (!$divisi) {
            $this->cabutSum($spreadsheet, $style_atas, $style);
            $this->cetakSum($spreadsheet, $style_atas, $style);
            $this->sortirSum($spreadsheet, $style_atas, $style);
            $this->gradingSum($spreadsheet, $style_atas, $style);
            $this->pengirimanSum($spreadsheet, $style_atas, $style);
            $this->balanceSum($spreadsheet, $style_atas, $style);
        } else {
            $methodName = $divisi . 'Sum';
            $this->$methodName($spreadsheet, $style_atas, $style);
        }

        $namafile = "Export gudang summary cocokan " . $divisi ?? '' . ".xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $namafile);
        header('Cache-Control: max-age=0');
        


        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }
}
