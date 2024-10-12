<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaketTemplateExport implements WithMultipleSheets
{
    use Exportable;
    public function sheets(): array
    {
        return [
            new SheetCabut(),
            new SheetEo(),
            new SheetCetak(),
            new SheetSortir(),
        ];
    }
}

trait ExcelStyling
{

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1    => ['font' => ['bold' => true]],
            
            // Styling a specific range
            'A1:'.$sheet->getHighestColumn().$sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}

class SheetCabut implements FromQuery, WithTitle, WithHeadings,WithStyles
{
    use ExcelStyling;
    public function query()
    {
        return DB::table('tb_kelas')
            ->selectRaw("tipe as paket, lokasi, gr, rupiah, denda_susut_persen as denda_susut, batas_susut, bonus_susut, rp_bonus as rp_susut, batas_eot, eot, denda_hcr")
            ->where('nonaktif', 'T')
            ->where('jenis', 2)
            ->where('id_kategori', '<>', 3)
            ->orderByDesc('id_kelas');
    }

    public function title(): string
    {
        return 'Cabut';
    }

    public function headings(): array
    {
        return [
            'paket',
            'lokasi',
            'gr',
            'rp',
            'denda susut %',
            'batas susut',
            'bonus susut',
            'rp susut',
            'batas eot',
            'eot',
            'denda hcr',
        ];
    }
}

class SheetEo implements FromQuery, WithTitle, WithHeadings,WithStyles
{
    use ExcelStyling;
    public function query()
    {
        return DB::table('tb_kelas')
            ->selectRaw("kelas, rupiah as rp")
            ->where('nonaktif', 'T')
            ->where('jenis', 2)
            ->where('id_kategori', 3)
            ->orderByDesc('id_kelas');
    }

    public function title(): string
    {
        return 'Eo';
    }

    public function headings(): array
    {
        return [
            'kelas',
            'rp',
        ];
    }
}

class SheetCetak implements FromCollection, WithTitle, WithHeadings,WithStyles
{
    use ExcelStyling;
    public function collection()
    {
        $paketCtk = DB::table('kelas_cetak')->selectRaw("kelas, rp_pcs as rupiah_target, denda_hcr,batas_susut,denda_susut,kategori_hitung,rp_down,kategori")->get();
        $data = new Collection();
        foreach ($paketCtk as $eo) {
            $data->push(
                [
                    $eo->kelas, 
                    $eo->rupiah_target,
                    $eo->denda_hcr,
                    $eo->batas_susut,
                    $eo->denda_susut,
                    'gr',
                    $eo->rp_down,
                    $eo->kategori,
                ]);
        }
        return $data;

    }

    public function title(): string
    {
        return 'Cetak';
    }

    public function headings(): array
    {
        return [
            'kelas',
            'rupiah target',
            'denda hcr',
            'batas susut',
            'denda susut',
            'kategori hitung',
            'rp target susut',
            'kategori',
        ];
    }
}

class SheetSortir implements FromQuery, WithTitle, WithHeadings,WithStyles
{
    use ExcelStyling;
    public function query()
    {
        return DB::table('tb_kelas_sortir')
            ->selectRaw("kelas, rupiah, denda_susut,denda as denda_rp")->where('nonaktif', 'T')->get();
    }

    public function title(): string
    {
        return 'Sortir';
    }

    public function headings(): array
    {
        return [
            'kelas',
            'rupiah',
            'denda susut',
            'denda rp',
        ];
    }
}