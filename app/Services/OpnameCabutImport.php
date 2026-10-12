<?php

namespace App\Services;

use App\Models\OpnameImportHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OpnameCabutImport
{
    public const STATUSES = [
        'stok' => 'Cabut sisa pengawas',
    ];

    public const HEADERS = [
        'no_box', 'nm_partai', 'tipe', 'ket', 'warna', 'tgl',
        'id_pengawas', 'pcs_awal', 'gr_awal', 'hrga_satuan',
    ];

    public function read(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $book = $reader->load($path);
        try {
            $sheet = $book->getSheet(0);
            if ($sheet->getHighestDataRow() > 501) {
                throw ValidationException::withMessages(['file' => 'Maksimal 500 baris data per import.']);
            }
            $data = $sheet->toArray(null, true, false, false);
            $headers = array_map(fn ($value) => strtolower(trim((string) $value)), array_shift($data) ?? []);
            while ($headers && end($headers) === '') {
                array_pop($headers);
            }
            if ($headers !== static::HEADERS) {
                throw ValidationException::withMessages(['file' => 'Kolom tidak sesuai. Gunakan template sesuai gudang tujuan.']);
            }
            $rows = [];
            foreach ($data as $index => $values) {
                $values = array_slice(array_pad($values, count(static::HEADERS), null), 0, count(static::HEADERS));
                if (! count(array_filter($values, fn ($value) => $value !== null && trim((string) $value) !== ''))) {
                    continue;
                }
                $row = array_combine(static::HEADERS, $values);
                $row['_baris'] = $index + 2;
                $rows[] = $row;
            }

            return $rows;
        } finally {
            $book->disconnectWorksheets();
        }
    }

    public function validateRows(array $rows, string $status): array
    {
        if (! isset(static::STATUSES[$status]) || ! $rows || count($rows) > 500) {
            throw ValidationException::withMessages(['file' => 'Pilih status yang benar dan isi 1–500 baris data.']);
        }
        $errors = [];
        $seen = [];
        $result = [];
        foreach ($rows as $index => $row) {
            $line = $row['_baris'] ?? $index + 2;
            foreach (static::HEADERS as $key) {
                $row[$key] = isset($row[$key]) ? trim((string) $row[$key]) : null;
                if ($row[$key] === '') {
                    $row[$key] = null;
                }
            }
            foreach ($this->dateFields() as $key) {
                if ($row[$key] !== null) {
                    $row[$key] = $this->date($row[$key]);
                }
            }
            $rules = $this->rules();
            $validator = Validator::make($row, $rules, [
                'required' => ':attribute wajib diisi.',
                'prohibited' => ':attribute harus dikosongkan untuk status ini.',
                'integer' => ':attribute harus berupa bilangan bulat.',
                'numeric' => ':attribute harus berupa angka tanpa pemisah ribuan.',
                'date_format' => ':attribute harus berupa tanggal valid YYYY-MM-DD.',
                'after_or_equal' => ':attribute tidak boleh sebelum :date.',
                'exists' => ':attribute tidak ditemukan atau tidak sesuai kategori/pengawas yang diperlukan.',
                'id_anak_cetak.required' => 'Pengawas Cetak belum memiliki anak aktif. Pilih pengawas yang memiliki anak aktif.',
                'same' => ':attribute harus sama dengan :other.',
                'gt' => ':attribute harus lebih dari :value.',
                'lte' => ':attribute tidak boleh melebihi :value.',
                'min' => ':attribute minimal :min.',
                'max' => ':attribute maksimal :max karakter.',
                'regex' => ':attribute harus berupa nomor box positif tanpa nol di depan (maksimal 9 digit).',
                'not_in' => ':attribute tidak boleh menggunakan nomor 9999.',
                'in' => ':attribute harus kosong atau 0 untuk status ini.',
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = "Baris {$line}: {$message}";
                }
            } else {
                array_push($errors, ...$this->rowErrors($row, $line));
                $box = $row['no_box'];
                if (isset($seen[$box])) {
                    $errors[] = "Baris {$line}: box {$box} berulang. Satu baris untuk satu box.";
                }
                $seen[$box] = true;
                $result[] = $row;
            }
        }
        // Periksa seluruh tahapan, termasuk riwayat lama: jangan menimpa nomor box.
        if ($seen) {
            foreach (['bk' => 'no_box', 'cabut' => 'no_box', 'eo' => 'no_box',
                'cetak_new' => 'no_box', 'sortir' => 'no_box', 'grading' => 'no_box_sortir',
                'formulir_sarang' => 'no_box', 'tb_hancuran' => 'no_box', 'pengiriman' => 'no_box'] as $table => $column) {
                $existing = DB::table($table)->whereIn($column, array_keys($seen))->distinct()->pluck($column);
                foreach ($existing as $box) {
                    $errors[] = "Box {$box} sudah tercatat di {$table}. Gunakan nomor box baru.";
                }
            }
        }
        if ($errors) {
            throw ValidationException::withMessages(['file' => array_slice(array_unique($errors), 0, 100)]);
        }

        return $result;
    }

    public function store(array $rows, string $status, int $giver, ?string $fileName = null): array
    {
        // Serialisasi import agar dua tab tidak mengimpor box sama secara bersamaan.
        $mysql = DB::connection()->getDriverName() === 'mysql';
        if ($mysql && (int) DB::selectOne("SELECT GET_LOCK('opname_cabut_import', 10) AS acquired")->acquired !== 1) {
            throw ValidationException::withMessages(['file' => 'Import lain sedang berjalan. Coba lagi.']);
        }
        try {
            return DB::transaction(function () use ($rows, $status, $giver, $fileName) {
                $rows = $this->validateRows($rows, $status);
                $user = DB::table('users')->where('id', $giver)->first();
                if (! $user) {
                    throw ValidationException::withMessages(['file' => 'Pemberi tidak ditemukan.']);
                }
                $invoices = [];
                $lastInvoice = DB::table('formulir_sarang')->where('kategori', 'cabut')
                    ->get(['no_invoice'])->pluck('no_invoice')->filter(fn ($value) => ctype_digit((string) $value))
                    ->map(fn ($value) => (int) $value)->max();
                $nextInvoice = max(1000, (int) $lastInvoice);
                foreach ($rows as $row) {
                    $this->startRow($row, $user, $giver, $invoices, $nextInvoice);
                    $this->finishRow($row, $invoices);
                }

                $history = $this->recordHistory($rows, $status, $user, $fileName, $invoices);

                return ['boxes' => count($rows), 'history_id' => $history->id,
                    'invoices' => array_values(array_filter($invoices, fn ($key) => str_starts_with($key, 'cabut|'), ARRAY_FILTER_USE_KEY)),
                    'grade_invoices' => array_values(array_filter($invoices, fn ($key) => str_starts_with($key, 'grade|'), ARRAY_FILTER_USE_KEY)),
                    'sortir_invoices' => array_values(array_filter($invoices, fn ($key) => str_starts_with($key, 'sortir|'), ARRAY_FILTER_USE_KEY)),
                    'cetak_invoices' => array_values(array_filter($invoices, fn ($key) => str_starts_with($key, 'cetak|'), ARRAY_FILTER_USE_KEY)),
                ];
            });
        } finally {
            if ($mysql) {
                DB::select("SELECT RELEASE_LOCK('opname_cabut_import')");
            }
        }
    }

    protected function createBk(array $row, object $user, ?string $invoice): void
    {
        DB::table('bk')->insert([
            'no_box' => $row['no_box'], 'nm_partai' => $row['nm_partai'],
            'no_lot' => '0', 'tipe' => $row['tipe'], 'ket' => $row['ket'], 'warna' => $row['warna'],
            'tgl' => $row['tgl'], 'tgl_input' => $row['tgl'],
            'pengawas' => mb_substr($user->name, 0, 20), 'penerima' => $row['id_pengawas'],
            'pcs_awal' => $row['pcs_awal'], 'gr_awal' => $row['gr_awal'],
            'hrga_satuan' => $row['hrga_satuan'], 'ttl_rp' => $row['gr_awal'] * $row['hrga_satuan'],
            'kategori' => 'cabut', 'baru' => 'baru', 'selesai' => 'T',
            'formulir' => $invoice === null ? 'T' : 'Y', 'invoice_formulir' => $invoice ?? '', 'pgws_grade' => '', 'susut' => 0,
        ]);
    }

    protected function startRow(array $row, object $user, int $giver, array &$invoices, int &$nextInvoice): void
    {
        // Kelompok invoice mengikuti pengawas dan tanggal penyerahan.
        $group = 'cabut|'.$row['id_pengawas'].'|'.$row['tgl'];
        $invoice = $invoices[$group] ??= (string) ++$nextInvoice;
        $this->createBk($row, $user, $invoice);
        DB::table('formulir_sarang')->insert([
            'no_invoice' => $invoice, 'no_box' => $row['no_box'],
            'id_pemberi' => $giver, 'id_penerima' => $row['id_pengawas'],
            'pcs_awal' => $row['pcs_awal'], 'gr_awal' => $row['gr_awal'],
            'tanggal' => $row['tgl'], 'kategori' => 'cabut',
            'selesai' => 'T', 'print' => 'T', 'sst_aktual' => 0,
        ]);
        $this->recordHancuran($row['no_box'], $invoice, 'cabut');
    }

    protected function rules(): array
    {
        return [
            'no_box' => ['required', 'regex:/^[1-9][0-9]{0,8}$/', 'not_in:9999'],
            'nm_partai' => 'required|string|max:100',
            'tipe' => 'nullable|string|max:100', 'ket' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:100',
            'tgl' => 'required|date_format:Y-m-d',
            'tgl_bk' => 'prohibited', 'tgl_terima' => 'prohibited',
            'id_pengawas' => 'required|integer|exists:users,id,posisi_id,13',
            'pcs_awal' => 'required|integer|min:0', 'gr_awal' => 'required|numeric|gt:0',
            'hrga_satuan' => 'required|numeric|min:0',
            'id_anak' => 'prohibited', 'id_kelas' => 'prohibited', 'tgl_serah' => 'prohibited',
            'pcs_akhir' => 'prohibited', 'gr_akhir' => 'prohibited',
            'biaya_cabut' => 'prohibited', 'cost_op' => 'prohibited',
        ];
    }

    private function recordHistory(array $rows, string $status, object $user, ?string $fileName, array $invoices): OpnameImportHistory
    {
        $userIds = array_unique([...array_column($rows, 'id_pengawas'), ...array_column($rows, 'id_pengawas_cetak'), ...array_column($rows, 'id_pengawas_sortir'), ...array_column($rows, 'id_pengawas_grading')]);
        $users = DB::table('users')->whereIn('id', $userIds)->pluck('name', 'id');
        $children = DB::table('tb_anak')->whereIn('id_anak', array_column($rows, 'id_anak_cabut'))->pluck('nama', 'id_anak');
        $classes = DB::table('tb_kelas')->whereIn('id_kelas', array_column($rows, 'id_kelas_cabut'))->pluck('kelas', 'id_kelas');
        $cetakChildren = DB::table('tb_anak')->whereIn('id_anak', array_column($rows, 'id_anak_cetak'))->pluck('nama', 'id_anak');
        $cetakClasses = array_column($rows, 'id_kelas_cetak') ? DB::table('kelas_cetak')->whereIn('id_kelas_cetak', array_column($rows, 'id_kelas_cetak'))->pluck('kelas', 'id_kelas_cetak') : collect();
        $sortirChildren = DB::table('tb_anak')->whereIn('id_anak', array_column($rows, 'id_anak_sortir'))->pluck('nama', 'id_anak');
        $sortirClasses = array_column($rows, 'id_kelas_sortir') ? DB::table('tb_kelas_sortir')->whereIn('id_kelas', array_column($rows, 'id_kelas_sortir'))->pluck('kelas', 'id_kelas') : collect();
        $transactionIds = [];
        foreach (['bk' => 'id_bk', 'cabut' => 'id_cabut', 'cetak_new' => 'id_cetak', 'sortir' => 'id_sortir', 'formulir_sarang' => 'id_formulir', 'tb_hancuran' => 'id'] as $table => $idColumn) {
            $transactionIds[$table] = DB::table($table)->whereIn('no_box', array_column($rows, 'no_box'))
                ->get(['no_box', $idColumn])->groupBy('no_box')->map(fn ($items) => $items->pluck($idColumn)->all());
        }
        $snapshot = array_map(function ($row) use ($users, $children, $classes, $cetakChildren, $cetakClasses, $sortirChildren, $sortirClasses, $transactionIds) {
            foreach ($transactionIds as $table => $idsByBox) {
                $row['_transaction_ids'][$table] = $idsByBox->get($row['no_box'], []);
            }
            $row['pengawas_grading_name'] = $users[$row['id_pengawas_grading'] ?? 0] ?? null;
            $row['anak_sortir_name'] = $sortirChildren[$row['id_anak_sortir'] ?? 0] ?? null;
            $row['kelas_sortir_name'] = $sortirClasses[$row['id_kelas_sortir'] ?? 0] ?? null;
            $row['pengawas_cabut_name'] = $users[$row['id_pengawas']] ?? null;
            $row['pengawas_sortir_name'] = $users[$row['id_pengawas_sortir'] ?? 0] ?? null;
            $row['anak_cetak_name'] = $cetakChildren[$row['id_anak_cetak'] ?? 0] ?? null;
            $row['kelas_cetak_name'] = $cetakClasses[$row['id_kelas_cetak'] ?? 0] ?? null;
            $row['pengawas_cetak_name'] = $users[$row['id_pengawas_cetak'] ?? 0] ?? null;
            $row['anak_cabut_name'] = $children[$row['id_anak_cabut'] ?? 0] ?? null;
            $row['kelas_cabut_name'] = $classes[$row['id_kelas_cabut'] ?? 0] ?? null;

            return $row;
        }, $rows);

        return OpnameImportHistory::create([
            'tujuan' => $status, 'user_id' => $user->id, 'user_name' => $user->name,
            'file_name' => $fileName === null ? null : mb_substr(basename(str_replace('\\', '/', $fileName)), 0, 255),
            'jumlah_box' => count($rows), 'total_pcs' => array_sum(array_column($rows, 'pcs_awal')),
            'total_gr' => array_sum(array_column($rows, 'gr_awal')),
            'total_rp' => array_sum(array_map(fn ($row) => $row['gr_awal'] * $row['hrga_satuan'], $rows)),
            'invoices' => [
                'cabut' => array_values(array_filter($invoices, fn ($key) => str_starts_with($key, 'cabut|'), ARRAY_FILTER_USE_KEY)),
                'grade' => array_values(array_filter($invoices, fn ($key) => str_starts_with($key, 'grade|'), ARRAY_FILTER_USE_KEY)),
                'sortir' => array_values(array_filter($invoices, fn ($key) => str_starts_with($key, 'sortir|'), ARRAY_FILTER_USE_KEY)),
                'cetak' => array_values(array_filter($invoices, fn ($key) => str_starts_with($key, 'cetak|'), ARRAY_FILTER_USE_KEY)),
            ],
            'rows' => $snapshot,
        ]);
    }

    protected function dateFields(): array
    {
        return ['tgl'];
    }

    protected function rowErrors(array $row, int $line): array
    {
        return [];
    }

    protected function recordHancuran(string $box, string $invoice, string $category): void
    {
        DB::table('tb_hancuran')->insert([
            'no_box' => $box, 'no_invoice' => $invoice, 'kategori' => $category, 'pcs' => 0,
        ]);
    }

    protected function finishRow(array $row, array &$invoices): void
    {
        // Cabut sisa pengawas berhenti pada penyerahan BK ke pengawas.
    }

    private function date(string $value): string
    {
        if (is_numeric($value) && (float) $value > 0 && (float) $value < 100000) {
            return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        return $value;
    }
}
