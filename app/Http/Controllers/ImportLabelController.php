<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ImportLabelController extends Controller
{
    private const DEFAULT_LABEL = [
        'nama_produsen' => 'Rwb Sekapuk',
        'tanggal_kedatangan' => '2026-08-05',
        'kode_lot' => '05-08206-10.1.109-02-27',
        'kode_grading' => '10.1',
    ];

    public function index()
    {
        return view('home.import_label.index', [
            'title' => 'Import Label Bahan Baku',
            'labels' => DB::table('import_label')->orderBy('id')->get(),
            'bagians' => DB::table('import_label')
                ->whereNotNull('bagian')
                ->where('bagian', '!=', '')
                ->distinct()
                ->orderBy('bagian')
                ->pluck('bagian'),
            'defaults' => self::DEFAULT_LABEL,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'replace_data' => ['nullable', 'boolean'],
        ]);

        try {
            $sheet = IOFactory::load($request->file('file')->getPathname())->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
        } catch (Throwable $e) {
            return back()->with('error', 'File gagal dibaca: ' . $e->getMessage());
        }

        if (count($rows) < 2) {
            return back()->with('error', 'File tidak memiliki data untuk diimport.');
        }

        $headers = collect(array_shift($rows))
            ->map(fn ($header) => $this->normalizeHeader($header))
            ->all();
        $columnMap = $this->resolveColumns($headers);

        $requiredColumns = collect($columnMap)->only(['partai', 'box', 'grade', 'pcs', 'gr', 'bagian']);

        if ($requiredColumns->containsStrict(null)) {
            return back()->with('error', 'Header wajib: partai, box/no_box, grade, pcs, gr/gram, bagian.');
        }

        $data = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            if (!array_filter($row, fn ($value) => $value !== null && $value !== '')) {
                continue;
            }

            $record = [
                'partai' => trim((string) ($row[$columnMap['partai']] ?? '')),
                'box' => trim((string) ($row[$columnMap['box']] ?? '')),
                'grade' => trim((string) ($row[$columnMap['grade']] ?? '')),
                'pcs' => $row[$columnMap['pcs']] ?? null,
                'gr' => $row[$columnMap['gr']] ?? null,
                'bagian' => trim((string) ($row[$columnMap['bagian']] ?? '')),
                'kelompok' => $columnMap['kelompok'] === null
                    ? ''
                    : trim((string) ($row[$columnMap['kelompok']] ?? '')),
            ];
            $validator = Validator::make($record, [
                'partai' => ['required', 'string', 'max:100'],
                'box' => ['required', 'string', 'max:100'],
                'grade' => ['required', 'string', 'max:100'],
                'pcs' => ['required', 'numeric', 'min:0'],
                'gr' => ['required', 'numeric', 'min:0'],
                'bagian' => ['required', 'string', 'max:100'],
                'kelompok' => ['nullable', 'string', 'max:50'],
            ]);

            if ($validator->fails()) {
                $errors[] = 'Baris ' . ($index + 2) . ': ' . $validator->errors()->first();
                continue;
            }

            $record['pcs'] = (float) $record['pcs'];
            $record['gr'] = (float) $record['gr'];
            $data[] = $record;
        }

        if ($errors) {
            return back()->with('error', implode(' | ', array_slice($errors, 0, 5)));
        }

        if (!$data) {
            return back()->with('error', 'Tidak ada baris valid yang dapat diimport.');
        }

        DB::transaction(function () use ($request, $data) {
            if ($request->boolean('replace_data')) {
                DB::table('import_label')->delete();
            }

            foreach (array_chunk($data, 500) as $chunk) {
                DB::table('import_label')->insert($chunk);
            }
        });

        return redirect()->route('import-label.index')
            ->with('sukses', count($data) . ' data label berhasil diimport.');
    }

    public function print(Request $request)
    {
        $labelInfo = $request->validate([
            'nama_produsen' => ['required', 'string', 'max:150'],
            'tanggal_kedatangan' => ['required', 'date'],
            'kode_lot' => ['required', 'string', 'max:150'],
            'kode_grading' => ['required', 'string', 'max:100'],
            'bagian' => ['required', 'string', 'max:100'],
        ]);
        $labels = DB::table('import_label')
            ->where('bagian', $labelInfo['bagian'])
            ->orderBy('id')
            ->get();

        if ($labels->isEmpty()) {
            return redirect()->route('import-label.index')
                ->with('error', 'Tidak ada data label untuk bagian ' . $labelInfo['bagian'] . '.');
        }

        $isPacking = strtolower(trim($labelInfo['bagian'])) === 'packing';

        return view('home.import_label.print', compact('labels', 'labelInfo', 'isPacking'));
    }

    private function normalizeHeader($header): string
    {
        return trim(preg_replace('/[^a-z0-9]+/', '_', strtolower((string) $header)), '_');
    }

    private function resolveColumns(array $headers): array
    {
        $aliases = [
            'partai' => ['partai', 'nama_partai'],
            'box' => ['box', 'no_box', 'nomor_box'],
            'grade' => ['grade', 'grading', 'kode_grading'],
            'pcs' => ['pcs', 'jumlah_pcs'],
            'gr' => ['gr', 'gram', 'berat_gr'],
            'bagian' => ['bagian', 'divisi', 'departemen'],
            'kelompok' => ['kelompok', 'group', 'grup'],
        ];

        return collect($aliases)->map(function (array $names) use ($headers) {
            foreach ($names as $name) {
                $index = array_search($name, $headers, true);
                if ($index !== false) {
                    return $index;
                }
            }

            return null;
        })->all();
    }
}
