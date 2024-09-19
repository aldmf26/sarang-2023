<?php

namespace App\Http\Controllers;

use App\Models\CocokanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportCocokanController extends Controller
{
    public function index(CocokanModel $model)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cetak');
        $koloms = [
            'A' => 'awal cetak',
            'B' => 'nama partai',
            'C' => 'pengawas',
            'D' => 'no box',
            'E' => 'pcs',
            'F' => 'gr',
            'G' => 'ttl rp',
            'H' => 'cu',

            'J' => 'awal cetak',
            'K' => 'nama partai',
            'L' => 'pengawas',
            'M' => 'no box',
            'N' => 'pcs',
            'O' => 'gr',
            'P' => 'ttl rp',
            'Q' => 'cu',

            'S' => 'sedang proses',
            'T' => 'nama partai',
            'U' => 'pengawas',
            'V' => 'no box',
            'W' => 'pcs',
            'X' => 'gr',
            'Y' => 'ttl rp',
            'Z' => 'cu',

            'AB' => 'sisa pengawas',
            'AC' => 'nama partai',
            'AD' => 'pengawas',
            'AE' => 'no box',
            'AF' => 'pcs',
            'AG' => 'gr',
            'AH' => 'ttl rp',
            'AI' => 'cu',

        ];
        foreach ($koloms as $k => $v) {
            $sheet->setCellValue($k . '1', $v);
        }

        $ctk_opname = $this->getSuntikan(21);
        $sheet->setCellValue("B2", 'suntikan');
        $sheet->setCellValue("C2", 'suntikan');
        $sheet->setCellValue("D2", '-');
        $sheet->setCellValue("E2", $ctk_opname->pcs);
        $sheet->setCellValue("F2", $ctk_opname->gr);
        $sheet->setCellValue("G2", $ctk_opname->ttl_rp);

        // $ca2 = $model::cetak_stok_awal();
        // $ca12suntik = $this->getSuntikan(23);
        
        $ca2 = DB::selectOne("SELECT a.no_box, b.name, sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(c.hrga_satuan  * c.gr_awal) as ttl_rp, e.name as pgws,
                    d.ttl_rp as cost_cbt, c.nm_partai, c.pcs_awal as pcs_bk, (d.gr_akhir * f.rp_gr) as cost_op, z.cost_cu
                FROM formulir_sarang as a 
                left join users as b on b.id = a.id_penerima
                left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
                left join cabut as d on d.no_box = a.no_box
                left join oprasional as f on f.bulan = d.bulan_dibayar
                left join users as e on e.id = a.id_pemberi
                left join (
                        SELECT a.no_box, sum(a.ttl_rp) as cost_cu
                        FROM cetak_new as a 
                        left join kelas_cetak as b on b.id_kelas_cetak = a.id_kelas_cetak
                        where b.kategori = 'CU'
                        group by a.no_box
                    ) as z on z.no_box = a.no_box
                WHERE a.kategori = 'cetak'");

        $row = 3;
        foreach($ca2 as $v){
            $sheet->setCellValue("B$row", $v->nm_partai);
            $sheet->setCellValue("C$row", $v->name);
            $sheet->setCellValue("D$row", $v->no_box);
            $sheet->setCellValue("E$row", $v->pcs);
            $sheet->setCellValue("F$row", $v->gr);
            $sheet->setCellValue("G$row", $v->cost_cbt);

            $row++;
        }
        $ca12suntik = $this->getSuntikan(23);
        $akhirpcs = $ca12suntik->pcs;
        $akhirgr =  $ca12suntik->gr;
        $akhirttl_rp =  $ca12suntik->ttl_rp;

        $sheet->setCellValue("B" . $row+1, 'suntikan');
        $sheet->setCellValue("C" . $row+1, 'suntikan');
        $sheet->setCellValue("D" . $row+1, '-');
        $sheet->setCellValue("E" . $row+1, $akhirpcs);
        $sheet->setCellValue("F" . $row+1, $akhirgr);
        $sheet->setCellValue("G" . $row+1, $akhirttl_rp);

        $cetak_proses = DB::select("SELECT a.no_box,d.nm_partai,c.name,sum(a.ttl_rp) as cost_kerja,sum(a.pcs_awal_ctk) as pcs, sum(a.gr_awal_ctk) as gr, sum(d.gr_awal * d.hrga_satuan) as ttl_rp, sum(a.ttl_rp) as cost_kerja
            FROM cetak_new as a 
            left join bk as d on d.no_box = a.no_box and d.kategori = 'cabut'
            left join users as c on a.id_pengawas = c.id
            left join kelas_cetak as g on g.id_kelas_cetak = a.id_kelas_cetak
            where a.selesai = 'T' and a.id_anak != 0  and g.kategori = 'CTK' and d.baru = 'baru'
            GROUP BY a.no_box
            order by a.no_box ASC;");

        foreach($cetak_proses as $v){
            $sheet->setCellValue("B$row", $v->nm_partai);
            $sheet->setCellValue("C$row", $v->name);
            $sheet->setCellValue("D$row", $v->no_box);
            $sheet->setCellValue("E$row", $v->pcs);
            $sheet->setCellValue("F$row", $v->gr);
            $sheet->setCellValue("G$row", $v->cost_kerja);

            $row++;
        }
        $cetak_sisa = DB::select("SELECT a.no_box,c.nm_partai,b.name,sum(a.pcs_awal) as pcs, sum(a.gr_awal) as gr, sum(c.hrga_satuan  * c.gr_awal) as ttl_rp
                FROM formulir_sarang as a 
                left join bk as c on c.no_box = a.no_box and c.kategori ='cabut'
                LEFT JOIN users as b on a.id_penerima = b.id
                WHERE a.kategori = 'cetak'   
                and a.no_box not in(SELECT b.no_box FROM cetak_new as b where b.id_anak != 0) and a.no_box != 0 GROUP BY a.no_box;");

        foreach($cetak_sisa as $v){
            $sheet->setCellValue("B$row", $v->nm_partai);
            $sheet->setCellValue("C$row", $v->name);
            $sheet->setCellValue("D$row", $v->no_box);
            $sheet->setCellValue("E$row", $v->pcs);
            $sheet->setCellValue("F$row", $v->gr);
            $sheet->setCellValue("G$row", $v->ttl_rp);

            $row++;
        }

        $suntikanSisa = $this->getSuntikan(27);
        $sheet->setCellValue("B" . $row+1, 'suntikan');
        $sheet->setCellValue("C" . $row+1, 'suntikan');
        $sheet->setCellValue("D" . $row+1, '-');
        $sheet->setCellValue("E" . $row+1, $suntikanSisa->pcs);
        $sheet->setCellValue("F" . $row+1, $suntikanSisa->gr);
        $sheet->setCellValue("G" . $row+1, $suntikanSisa->ttl_rp);
        // $ca17 = $model::cetak_stok();
        // $ca17suntik = $this->getSuntikan(27);

        // $cetak_sisa = new stdClass();
        // $cetak_sisa->pcs = $ca17->pcs + $ca17suntik->pcs;
        // $cetak_sisa->gr = $ca17->gr + $ca17suntik->gr;
        // $cetak_sisa->ttl_rp = $ca17->ttl_rp + $ca17suntik->ttl_rp;

        // $ca16suntik = $this->getSuntikan(26);
        // $ca16 = $model::cetak_selesai();
        // $cetak_akhir = new stdClass();
        // $cetak_akhir->pcs = $ca16->pcs + $ca16suntik->pcs;
        // $cetak_akhir->gr = $ca16->gr + $ca16suntik->gr;
        // $cetak_akhir->ttl_rp = $ca16->ttl_rp + $ca16suntik->ttl_rp;
        // $cetak_akhir->cost_kerja = $ca16->cost_kerja;

        // $ttl_gr = $this->getCost($model, 'ttl_gr');
        // $cost_op = $this->getCost($model, 'cost_op');
        // $cost_dll = $this->getCost($model, 'dll');

        // $ctk_opname->pcs + $akhir_cbt->pcs - $cetak_proses->pcs - $cetak_sisa->pcs
        
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
        $styleBaris = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:F1')->applyFromArray($styleBold);
        $styleBackground = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF00',
                ],
            ],
        ];
        // $sheet->getStyle('H1:L1')->applyFromArray($styleBackground);
        $sheet->getStyle('A1:AI1')->applyFromArray($styleBold);

        $no = 2;
       

        
        $sheet->getStyle('N1:N' . $no - 1)->applyFromArray($styleBaris);

        $writer = new Xlsx($spreadsheet);
        $fileName = "Template Grading";
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
