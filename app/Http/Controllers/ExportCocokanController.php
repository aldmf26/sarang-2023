<?php

namespace App\Http\Controllers;

use App\Models\CocokanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\DetailCabutModel;
use App\Models\DetailCetakModel;
use App\Models\DetailSortirModel;
use App\Models\OpnameNewModel;

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

            'J' => 'akhir cetak',
            'K' => 'nama partai',
            'L' => 'pengawas',
            'M' => 'no box',
            'N' => 'pcs',
            'O' => 'gr',
            'P' => 'ttl rp bk',
            'Q' => 'cost kerja',

            'S' => 'sedang proses',
            'T' => 'nama partai',
            'U' => 'pengawas',
            'V' => 'no box',
            'W' => 'pcs',
            'X' => 'gr',
            'Y' => 'ttl rp bk',
            'Z' => 'cost kerja',

            'AB' => 'sisa pengawas',
            'AC' => 'nama partai',
            'AD' => 'pengawas',
            'AE' => 'no box',
            'AF' => 'pcs',
            'AG' => 'gr',
            'AH' => 'ttl rp bk',
            'AI' => 'cost kerja',

        ];
        foreach ($koloms as $k => $v) {
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

            $row++;
        }
        $sheet->setCellValue("B" . $row, 'suntikan');
        $sheet->setCellValue("C" . $row, 'suntikan');
        $sheet->setCellValue("D" . $row, '-');
        $sheet->setCellValue("E" . $row, $ca11->pcs);
        $sheet->setCellValue("F" . $row, $ca11->gr);
        $sheet->setCellValue("G" . $row, $ca11->ttl_rp);
        $sheet->setCellValue("H" . $row, 0);
        $row = $row + 1;
        $sheet->setCellValue("B" . $row, 'suntikan');
        $sheet->setCellValue("C" . $row, 'suntikan');
        $sheet->setCellValue("D" . $row, '-');
        $sheet->setCellValue("E" . $row, $ca12suntik->pcs);
        $sheet->setCellValue("F" . $row, $ca12suntik->gr);
        $sheet->setCellValue("G" . $row, $ca12suntik->ttl_rp);
        $sheet->setCellValue("H" . $row, 0);

        $sheet->getStyle('B2:H' . $row)->applyFromArray($style);

        // akhir ctk
        $ca2_selesai = $model2::stok_selesai();

        $row = 2;
        foreach ($ca2_selesai as $v) {
            $sheet->setCellValue("K$row", $v->nm_partai);
            $sheet->setCellValue("L$row", $v->name);
            $sheet->setCellValue("M$row", $v->no_box);
            $sheet->setCellValue("N$row", $v->pcs_akhir);
            $sheet->setCellValue("O$row", $v->gr_akhir);
            $sheet->setCellValue("P$row", $v->ttl_rp);
            $sheet->setCellValue("Q$row", $v->cost_kerja);

            $row++;
        }
        $sheet->setCellValue("K" . $row, 'suntikan');
        $sheet->setCellValue("L" . $row, 'suntikan');
        $sheet->setCellValue("M" . $row, '-');
        $sheet->setCellValue("N" . $row, $ca11->pcs);
        $sheet->setCellValue("O" . $row, $ca11->gr);
        $sheet->setCellValue("P" . $row, $ca11->ttl_rp);
        $sheet->setCellValue("Q" . $row, 0);
        $row = $row + 1;

        $sheet->setCellValue("K" . $row, 'suntikan');
        $sheet->setCellValue("L" . $row, 'suntikan');
        $sheet->setCellValue("M" . $row, '-');
        $sheet->setCellValue("N" . $row, $ca12suntik->pcs);
        $sheet->setCellValue("O" . $row, $ca12suntik->gr);
        $sheet->setCellValue("P" . $row, $ca12suntik->ttl_rp);
        $sheet->setCellValue("Q" . $row, 0);
        $sheet->getStyle('K2:Q' . $row)->applyFromArray($style);

        // proses ctk
        $proses = $model::cetak_proses();
        $row = 2;
        foreach ($proses as $v) {
            $sheet->setCellValue("T$row", $v->nm_partai);
            $sheet->setCellValue("U$row", $v->name);
            $sheet->setCellValue("V$row", $v->no_box);
            $sheet->setCellValue("W$row", $v->pcs);
            $sheet->setCellValue("X$row", $v->gr);
            $sheet->setCellValue("Y$row", $v->ttl_rp);
            $sheet->setCellValue("Z$row", 0);

            $row++;
        }
        $sheet->getStyle('T2:Z' . $row)->applyFromArray($style);

        // proses ctk
        $proses = $model::cetak_stok();
        $row = 2;
        foreach ($proses as $v) {
            $sheet->setCellValue("AC$row", $v->nm_partai);
            $sheet->setCellValue("AD$row", $v->name);
            $sheet->setCellValue("AE$row", $v->no_box);
            $sheet->setCellValue("AF$row", $v->pcs);
            $sheet->setCellValue("AG$row", $v->gr);
            $sheet->setCellValue("AH$row", $v->ttl_rp);
            $sheet->setCellValue("AI$row", 0);

            $row++;
        }
        $sheet->getStyle('AC2:AI' . $row)->applyFromArray($style);
    }

    public function sortir($spreadsheet, $style_atas, $style, $model)
    {
        $model2 = new DetailSortirModel();
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Gudang Sortir');
        $koloms = [
            'A' => 'awal sortir',
            'B' => 'nama partai',
            'C' => 'pengawas',
            'D' => 'no box',
            'E' => 'pcs',
            'F' => 'gr',
            'G' => 'ttl rp',
            'H' => 'cost kerja',

            'J' => 'akhir sortir',
            'K' => 'nama partai',
            'L' => 'pengawas',
            'M' => 'no box',
            'N' => 'pcs',
            'O' => 'gr',
            'P' => 'ttl rp',
            'Q' => 'cost kerja',

            'S' => 'sedang proses',
            'T' => 'nama partai',
            'U' => 'pengawas',
            'V' => 'no box',
            'W' => 'pcs',
            'X' => 'gr',
            'Y' => 'ttl rp',
            'Z' => 'cost kerja',

            'AB' => 'sisa pengawas',
            'AC' => 'nama partai',
            'AD' => 'pengawas',
            'AE' => 'no box',
            'AF' => 'pcs',
            'AG' => 'gr',
            'AH' => 'ttl rp',
            'AI' => 'cost kerja',

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

            $row++;
        }

        $sheet->setCellValue("B" . $row, 'suntikan');
        $sheet->setCellValue("C" . $row, 'suntikan');
        $sheet->setCellValue("D" . $row, '-');
        $sheet->setCellValue("E" . $row, $s1suntik->pcs);
        $sheet->setCellValue("F" . $row, $s1suntik->gr);
        $sheet->setCellValue("G" . $row, $s1suntik->ttl_rp);
        $sheet->setCellValue("H$row", $s1suntik->ttl_rp);

        $row = $row + 1;
        $sheet->setCellValue("B" . $row, 'suntikan');
        $sheet->setCellValue("C" . $row, 'suntikan');
        $sheet->setCellValue("D" . $row, '-');
        $sheet->setCellValue("E" . $row, $s1suntik2->pcs);
        $sheet->setCellValue("F" . $row, $s1suntik2->gr);
        $sheet->setCellValue("G" . $row, $s1suntik2->ttl_rp);
        $sheet->setCellValue("H$row", $s1suntik->ttl_rp);


        $sheet->getStyle('B2:H' . $row - 1)->applyFromArray($style);

        // akhir sortir
        $row = 2;
        foreach ($s1_akhir as $v) {
            $sheet->setCellValue("K$row", $v->nm_partai);
            $sheet->setCellValue("L$row", $v->name);
            $sheet->setCellValue("M$row", $v->no_box);
            $sheet->setCellValue("N$row", $v->pcs);
            $sheet->setCellValue("O$row", $v->gr);
            $sheet->setCellValue("P$row", $v->ttl_rp);
            $sheet->setCellValue("Q$row", $v->cost_kerja);


            $row++;
        }

        $sheet->setCellValue("K" . $row, 'suntikan');
        $sheet->setCellValue("L" . $row, 'suntikan');
        $sheet->setCellValue("M" . $row, '-');
        $sheet->setCellValue("N" . $row, $s1suntik_akhir->pcs);
        $sheet->setCellValue("O" . $row, $s1suntik_akhir->gr);
        $sheet->setCellValue("P" . $row, $s1suntik_akhir->ttl_rp);
        $sheet->setCellValue("Q" . $row, $s1suntik_akhir->ttl_rp);

        $sheet->getStyle('K2:Q' . $row - 1)->applyFromArray($style);

        // proses ctk
        $cetak_proses = $model::sortir_proses();
        $row = 2;
        foreach ($cetak_proses as $v) {
            $sheet->setCellValue("T$row", $v->nm_partai);
            $sheet->setCellValue("U$row", $v->name);
            $sheet->setCellValue("V$row", $v->no_box);
            $sheet->setCellValue("W$row", $v->pcs);
            $sheet->setCellValue("X$row", $v->gr);
            $sheet->setCellValue("Y$row", $v->ttl_rp);
            $sheet->setCellValue("Z$row", 0);


            $row++;
        }
        $sheet->getStyle('T2:Z' . $row - 1)->applyFromArray($style);

        // proses ctk
        $cetak_proses = $model::sortir_stock();
        $row = 2;
        foreach ($cetak_proses as $v) {
            $sheet->setCellValue("AC$row", $v->nm_partai);
            $sheet->setCellValue("AD$row", $v->name);
            $sheet->setCellValue("AE$row", $v->no_box);
            $sheet->setCellValue("AF$row", $v->pcs);
            $sheet->setCellValue("AG$row", $v->gr);
            $sheet->setCellValue("AH$row", $v->ttl_rp);
            $sheet->setCellValue("AI$row", 0);


            $row++;
        }
        $sheet->getStyle('AC2:AI' . $row - 1)->applyFromArray($style);
    }

    public function pengiriman($spreadsheet, $style_atas, $style, $model)
    {

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet3 = $spreadsheet->getActiveSheet();
        $sheet3->setTitle('Gudang grading & pengiriman');

        $sheet3->getStyle("B1:L1")->applyFromArray($style_atas);
        $sheet3->setCellValue('A1', 'Pengiriman');
        $sheet3->setCellValue('B1', 'Tanggal pengiriman');
        $sheet3->setCellValue('C1', 'no pengiriman');
        $sheet3->setCellValue('D1', 'grade');
        $sheet3->setCellValue('E1', 'pcs');
        $sheet3->setCellValue('F1', 'gr');
        $sheet3->setCellValue('G1', 'ttl rp bk');
        $sheet3->setCellValue('H1', 'cost kerja');
        $sheet3->setCellValue('I1', 'cost cu dll');
        $sheet3->setCellValue('J1', 'cost operasional');
        $sheet3->setCellValue('K1', 'ttl rp');
        $sheet3->setCellValue('L1', 'rp/gr');

        $pengiriman = DB::select("SELECT a.tgl_input, a.no_barcode, a.grade, sum(a.pcs) as pcs, sum(a.gr) as gr 
        FROM pengiriman as a 
        group by a.id_pengiriman;");
        $kolom = 2;
        foreach ($pengiriman  as $d) {
            $sheet3->setCellValue('B' . $kolom, $d->tgl_input);
            $sheet3->setCellValue('C' . $kolom, $d->no_barcode);
            $sheet3->setCellValue('D' . $kolom, $d->grade);
            $sheet3->setCellValue('E' . $kolom, $d->pcs);
            $sheet3->setCellValue('F' . $kolom, $d->gr);
            $sheet3->setCellValue('G' . $kolom, 0);
            $sheet3->setCellValue('H' . $kolom, 0);
            $sheet3->setCellValue('I' . $kolom, 0);
            $sheet3->setCellValue('J' . $kolom, 0);
            $sheet3->setCellValue('K' . $kolom, 0);
            $sheet3->setCellValue('L' . $kolom, 0);
            $kolom++;
        }
        $sheet3->getStyle('B2:L' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("O1:Y1")->applyFromArray($style_atas);
        $sheet3->setCellValue('N1', 'Sisa grading');
        $sheet3->setCellValue('O1', 'box grading');
        $sheet3->setCellValue('P1', 'pengawas');
        $sheet3->setCellValue('Q1', 'grade');
        $sheet3->setCellValue('R1', 'pcs');
        $sheet3->setCellValue('S1', 'gr');
        $sheet3->setCellValue('T1', 'ttl rp bk');
        $sheet3->setCellValue('U1', 'cost kerja');
        $sheet3->setCellValue('V1', 'cost cu dll');
        $sheet3->setCellValue('W1', 'cost operasional');
        $sheet3->setCellValue('X1', 'ttl rp');
        $sheet3->setCellValue('Y1', 'rp/gr');

        $grading = DB::select("SELECT * FROM `grading_partai` WHERE `box_pengiriman` not in(SELECT a.no_box FROM pengiriman as a )");
        $kolom = 2;
        foreach ($grading  as $d) {
            $sheet3->setCellValue('O' . $kolom, $d->box_pengiriman);
            $sheet3->setCellValue('P' . $kolom, $d->admin);
            $sheet3->setCellValue('Q' . $kolom, $d->grade);
            $sheet3->setCellValue('R' . $kolom, $d->pcs);
            $sheet3->setCellValue('S' . $kolom, $d->gr);
            $sheet3->setCellValue('T' . $kolom, 0);
            $sheet3->setCellValue('U' . $kolom, 0);
            $sheet3->setCellValue('V' . $kolom, 0);
            $sheet3->setCellValue('W' . $kolom, 0);
            $sheet3->setCellValue('X' . $kolom, 0);
            $sheet3->setCellValue('Y' . $kolom, 0);
            $kolom++;
        }
        $sheet3->getStyle('O2:Y' . $kolom - 1)->applyFromArray($style);

        $sheet3->getStyle("AB1:AI1")->applyFromArray($style_atas);
        $sheet3->setCellValue('AA1', 'selisih');
        $sheet3->setCellValue('AB1', 'pcs');
        $sheet3->setCellValue('AC1', 'gr');
        $sheet3->setCellValue('AD1', 'ttl rp bk');
        $sheet3->setCellValue('AE1', 'cost kerja');
        $sheet3->setCellValue('AF1', 'cost cu dll');
        $sheet3->setCellValue('AG1', 'cost operasional');
        $sheet3->setCellValue('AH1', 'ttl rp');
        $sheet3->setCellValue('AI1', 'rp/gr');

        $sa = CocokanModel::akhir_sortir();
        $p2suntik = $this->getSuntikan(42);
        $sortir_akhir = (object)[];
        $sortir_akhir->pcs = $sa->pcs + $p2suntik->pcs;
        $sortir_akhir->gr = $sa->gr + $p2suntik->gr;
        $sortir_akhir->ttl_rp = $sa->ttl_rp + $p2suntik->ttl_rp;

        $pengiriman = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr FROM pengiriman as a ");
        $grading = DB::selectOne("SELECT sum(a.pcs) as pcs, sum(a.gr) as gr FROM grading_partai as a ");
        $opname = $this->getSuntikan(41);

        $kolom = 2;

        $sheet3->setCellValue('AB' . $kolom, round($sortir_akhir->pcs + $opname->pcs - $grading->pcs, 0));
        $sheet3->setCellValue('AC' . $kolom, round($sortir_akhir->gr + $opname->gr - $grading->gr, 0));
        $sheet3->setCellValue('AD' . $kolom, 0);
        $sheet3->setCellValue('AE' . $kolom, 0);
        $sheet3->setCellValue('AF' . $kolom, 0);
        $sheet3->setCellValue('AG' . $kolom, 0);
        $sheet3->setCellValue('AH' . $kolom, 0);
        $sheet3->setCellValue('AI' . $kolom, 0);


        $sheet3->getStyle('AB2:AI2')->applyFromArray($style);
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
        // $this->pengiriman($spreadsheet, $style_atas, $style, $model);

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
}
