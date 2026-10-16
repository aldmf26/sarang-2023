<?php

namespace App\Http\Controllers;

use App\Models\OpnameImportHistory;
use App\Services\OpnameCabutImport;
use App\Services\OpnameBkImport;
use App\Services\OpnameCetakImport;
use App\Services\OpnameSortirImport;
use App\Services\OpnameGradingImport;
use App\Services\OpnameImportCancellation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OpnameMenuController extends Controller
{
    public function bulanan()
    {
        return app(OpnameBulananController::class)->index(request());
    }

    public function hitungBkBulanan()
    {
        $filters = request()->validate([
            'q' => 'nullable|string|max:100',
            'bulan' => 'nullable|date_format:Y-m',
        ]);
        $bulan = $filters['bulan'] ?? now()->format('Y-m');
        $bulanTanggal = $bulan.'-01';
        $query = DB::table('bk')
            ->where('penerima', 0)
            ->where('kategori', 'cabut')
            ->where('formulir', 'T')
            ->when($filters['q'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('bk.nm_partai', 'like', '%'.$search.'%')
                        ->orWhere('bk.no_box', 'like', '%'.$search.'%')
                        ->orWhere('bk.ket', 'like', '%'.$search.'%');
                });
            });
        $canSeeBkValue = (int) auth()->user()->posisi_id === 1;
        $totals = (clone $query)->selectRaw(
            'COALESCE(SUM(pcs_awal), 0) AS pcs, COALESCE(SUM(gr_awal), 0) AS gr'.
            ($canSeeBkValue ? ', COALESCE(SUM(gr_awal * hrga_satuan), 0) AS ttl_rp' : '')
        )->first();
        $bkSisa = $query
            ->leftJoin('opname_bulanan_counts as opname_count', function ($join) use ($bulanTanggal) {
                $join->on('opname_count.no_box', '=', 'bk.no_box')
                    ->where('opname_count.bulan', '=', $bulanTanggal);
            })
            ->select([
                'bk.nm_partai',
                'bk.no_box',
                'bk.ket as grade',
                'bk.pcs_awal as pcs',
                'bk.gr_awal as gr',
                'opname_count.grade_real',
                'opname_count.pcs_real',
                'opname_count.gr_real',
            ])
            ->when($canSeeBkValue, fn ($query) => $query->selectRaw('(bk.gr_awal * bk.hrga_satuan) AS ttl_rp'))
            ->orderBy('bk.nm_partai')
            ->orderBy('bk.no_box')
            ->paginate(25)
            ->withQueryString();

        return view('home.opname_menu.bulanan', [
            'title' => 'Opname Bulanan',
            'bkSisa' => $bkSisa,
            'totals' => $totals,
            'canSeeBkValue' => $canSeeBkValue,
            'bulan' => $bulan,
        ]);
    }

    public function simpanBulanan(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|date_format:Y-m',
            'counts' => 'nullable|array',
            'counts.*.pcs_real' => 'nullable|integer|min:0',
            'counts.*.gr_real' => 'nullable|numeric|min:0',
            'counts.*.grade_real' => 'nullable|string|max:100',
        ]);

        $bulan = $validated['bulan'].'-01';
        $allowedBoxes = DB::table('bk')
            ->where('penerima', 0)
            ->where('kategori', 'cabut')
            ->where('formulir', 'T')
            ->whereIn('no_box', array_keys($validated['counts'] ?? []))
            ->pluck('no_box')->map(fn ($box) => (string) $box)->all();

        foreach (($validated['counts'] ?? []) as $noBox => $count) {
            if (!in_array((string) $noBox, $allowedBoxes, true)) {
                continue;
            }
            if (($count['pcs_real'] ?? null) === null && ($count['gr_real'] ?? null) === null && ($count['grade_real'] ?? null) === null) {
                DB::table('opname_bulanan_counts')
                    ->where('bulan', $bulan)
                    ->where('no_box', $noBox)
                    ->delete();
                continue;
            }

            DB::table('opname_bulanan_counts')->updateOrInsert(
                ['bulan' => $bulan, 'no_box' => $noBox],
                [
                    'pcs_real' => $count['pcs_real'] ?? null,
                    'gr_real' => $count['gr_real'] ?? null,
                    'grade_real' => $count['grade_real'] ?? null,
                    'user_id' => $request->user()->id,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return redirect()->route('opname_menu.bulanan.hitung_bk', ['bulan' => $validated['bulan'], 'q' => $request->input('q')])
            ->with('sukses', 'Hasil hitung opname berhasil disimpan.');
    }

    public function index()
    {
        $filters = request()->validate([
            'history_tujuan' => 'nullable|in:stok,cetak,sortir,grade,bk',
            'history_dari' => 'nullable|date_format:Y-m-d',
            'history_sampai' => ['nullable', 'date_format:Y-m-d', ...request()->filled('history_dari') ? ['after_or_equal:history_dari'] : []],
        ]);
        $history = OpnameImportHistory::query()
            ->when($filters['history_tujuan'] ?? null, fn ($query, $value) => $query->where('tujuan', $value))
            ->when($filters['history_dari'] ?? null, fn ($query, $value) => $query->where('created_at', '>=', $value.' 00:00:00'))
            ->when($filters['history_sampai'] ?? null, fn ($query, $value) => $query->where('created_at', '<', \Carbon\Carbon::parse($value)->addDay()->format('Y-m-d')))
            ->orderByDesc('id')->paginate(15)->withQueryString();
        $preview = session('opname_cabut_preview');
        if ($preview && (($preview['version'] ?? 0) !== 10 || $preview['user_id'] !== (int) auth()->id() || $preview['expires_at'] < now()->timestamp)) {
            session()->forget('opname_cabut_preview');
            $preview = null;
        }
        $data = [
            'title' => 'Opname',
            'statuses' => OpnameBkImport::STATUSES + OpnameCabutImport::STATUSES + OpnameCetakImport::STATUSES + OpnameSortirImport::STATUSES + OpnameGradingImport::STATUSES,
            'preview' => $preview,
            'pengawas' => DB::table('users')->where('posisi_id', 13)->orderBy('name')->get(['id', 'name']),
            'anakCabut' => DB::table('tb_anak')->where('id_pengawas', 285)->get(['id_anak', 'nama']),
            'pengawasCetak' => DB::table('users')->where('posisi_id', 14)->orderBy('name')->get(['id', 'name']),
            'history' => $history,
            'anakCetak' => DB::table('tb_anak')->where('berhenti', 'T')->get(['id_anak', 'nama']),
            'kelasCetak' => DB::table('kelas_cetak')->where('kategori', 'CTK')->get(['id_kelas_cetak', 'kelas']),
            'pengawasSortir' => DB::table('users')->where('posisi_id', '!=', 1)->orderBy('name')->get(['id', 'name']),
        ];

        return view('home.opname_menu.index', $data);
    }

    public function templateBk()
    {
        return $this->template('bk');
    }

    public function templateCabut()
    {
        return $this->template('stok');
    }

    public function templateCetak()
    {
        return $this->template('cetak');
    }

    public function templateSortir()
    {
        return $this->template('sortir');
    }

    public function templateGrading()
    {
        return $this->template('grade');
    }

    private function template(string $status)
    {
        $bk = $status === 'bk';
        $cetak = in_array($status, ['cetak', 'sortir', 'grade']);
        $grade = $status === 'grade';
        $sortir = in_array($status, ['sortir', 'grade']);
        $label = $bk ? 'BK Sisa' : ($grade ? 'Belum Grading' : ($sortir ? 'Sortir' : ($cetak ? 'Cetak' : 'Cabut')));
        return response()->streamDownload(function () use ($bk, $cetak, $sortir, $grade, $label, $status) {
            $book = new Spreadsheet();
            $sheet = $book->getActiveSheet()->setTitle('Import '.$label);
            $headers = $this->importer($status)::HEADERS;
            $lastColumn = $bk ? 'I' : ($grade ? 'M' : ($sortir ? 'L' : 'J'));
            $sheet->fromArray($headers, null, 'A1');
            $sheet->getStyle('A1:'.$lastColumn.'1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle('A1:'.$lastColumn.'1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF435EBE');
            $sheet->freezePane('A2');
            foreach (range('A', $lastColumn) as $column) {
                $sheet->getColumnDimension($column)->setWidth(20);
            }
            foreach (['F'] as $column) {
                $sheet->getStyle($column.'2:'.$column.'501')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            }
            $guide = $book->createSheet()->setTitle('Petunjuk');
            $guide->fromArray([
                [$bk ? 'Import BK sisa - satu baris per box' : ($grade ? 'Import Sisa belum grading - satu baris per box' : 'Import '.$label.' sisa pengawas - satu baris per box')],
                ['Isi sheet pertama. Maksimal 500 box per import.'],
                ['no_box', 'Nomor box baru berupa angka, tanpa nol di depan. Nomor 9999 tidak boleh dipakai.'],
                ['tgl', 'Wajib. Satu tanggal untuk seluruh tahapan dan tanggal input BK. Format YYYY-MM-DD.'],
                [$bk ? 'Pengawas penerima' : ($cetak ? 'Pengawas Cabut otomatis' : 'id_pengawas'), $bk ? 'Belum diserahkan ke pengawas. Penerima 0, formulir T.' : ($cetak ? 'Arbayah (ID 285). Tidak perlu mengisi pengawas Cabut.' : 'ID pengawas Cabut, lihat sheet Pengawas.')],
                ['pcs_awal / gr_awal / hrga_satuan', 'Wajib. Pcs bilangan bulat >= 0, gram wajib > 0, harga per gram >= 0.'],
                ['Angka', 'Gunakan angka tanpa Rp/pemisah ribuan. Gunakan titik sebagai pemisah desimal.'],
                ['Alur', $bk ? 'Stok BK sisa. Belum ada penyerahan, formulir sarang, atau hancuran.' : ($grade ? 'BK -> Cabut selesai -> Cetak selesai -> Sortir selesai -> formulir grade -> sisa belum grading. Setiap tahap tercatat di formulir sarang dan tb_hancuran (pcs 0).' : ($sortir ? 'BK -> formulir Cabut -> Cabut selesai -> formulir Cetak -> Cetak selesai -> formulir Sortir -> Sortir sisa pengawas.' : ($cetak ? 'BK -> formulir Cabut -> Cabut selesai -> formulir Cetak -> Cetak sisa pengawas.' : 'BK -> formulir sarang kategori cabut -> Cabut sisa pengawas.')))],
                ['Tujuan', $bk ? 'BK sisa, belum diserahkan ke pengawas Cabut.' : ($grade ? 'Sortir sudah selesai. Barang diterima pengawas grading dan belum memiliki hasil grading.' : ($sortir ? 'Barang diterima pengawas Sortir dan belum dibagikan ke anak Sortir.' : ($cetak ? 'Barang diterima pengawas Cetak dan belum dibagikan ke anak Cetak.' : 'Barang diterima pengawas Cabut dan belum dibagikan ke anak.')))],
                ['Validasi', 'Nomor box yang sudah tercatat ditolak. Seluruh baris disimpan sekaligus atau dibatalkan.'],
            ], null, 'A1');
            if ($cetak) {
                $guide->fromArray([
                    ['Tanggal Cabut', 'Penerimaan dan selesai Cabut otomatis memakai tgl.'],
                    ['Anak / kelas Cabut otomatis', 'Anak dipilih acak dari anak aktif Arbayah. Kelas Cabut selalu ID 126. Pilihan terlihat di pratinjau dan tetap saat disimpan.'],
                    ['Tanggal Cetak', 'Penerimaan Cetak otomatis memakai tgl yang sama.'],
                    ['Gram Cabut otomatis', 'Gram awal dan akhir Cabut otomatis sama dengan gr_awal. Tidak ada perubahan berat.'],
                    ['Pcs Cabut', 'Pcs awal dan pcs akhir Cabut otomatis sama dengan pcs_awal.'],
                    ['Nilai barang', 'Hanya gram awal BK x hrga_satuan. Biaya, tarif Cabut, dan operasional otomatis 0.'],
                    ['id_pengawas_cetak', 'ID pengawas penerima Cetak, lihat sheet Pengawas Cetak.'],
                    ['Penerimaan Cetak', 'Pcs memakai pcs_awal (boleh 0); gram memakai gr_awal (wajib > 0).'],
                ], null, 'A11');
            }
            if ($sortir) {
                $guide->fromArray([
                    ['id_kelas_cetak', 'Pilih kelas kategori CTK dari sheet Kelas Cetak.'],
                    ['id_pengawas_sortir', 'Semua pengawas boleh menerima Sortir, tanpa batasan bagian. Lihat sheet Pengawas Sortir.'],
                    ['Anak Cetak', 'Dipilih acak dari anak aktif pengawas Cetak. Pengawas harus memiliki anak aktif. Pilihan dipertahankan dari pratinjau.'],
                    ['Cetak selesai', 'Pcs dan gram awal/akhir sama. Tanggal memakai tgl. Seluruh tarif dan biaya Cetak 0.'],
                ], null, 'A19');
                foreach (['Pengawas Sortir' => ['users', 'id', 'name', 'posisi_id', 15, 'id_pengawas_sortir'], 'Kelas Cetak' => ['kelas_cetak', 'id_kelas_cetak', 'kelas', 'kategori', 'CTK', 'id_kelas_cetak']] as $title => $ref) {
                    $reference = $book->createSheet()->setTitle($title);
                    $reference->fromArray([$ref[5], 'nama'], null, 'A1');
                    $items = $title === 'Pengawas Sortir'
                        ? DB::table('users')->where('posisi_id', '!=', 1)->orderBy('name')->get()
                        : DB::table($ref[0])->where($ref[3], $ref[4])->get();
                    foreach ($items as $index => $item) {
                        $reference->fromArray([$item->{$ref[1]}, $item->{$ref[2]}], null, 'A'.($index + 2), true);
                    }
                    $reference->getStyle('A1:B1')->getFont()->setBold(true);
                    $reference->freezePane('A2');
                    $reference->getColumnDimension('A')->setAutoSize(true);
                    $reference->getColumnDimension('B')->setAutoSize(true);
                }
            }
            if ($grade) {
                $guide->fromArray([
                    ['id_pengawas_grading', 'Pengawas penerima grading, lihat sheet Pengawas Grading. Semua pengawas boleh menerima.'],
                    ['Sortir lewat otomatis', 'Anak Emo (ID 2), kelas lewat (ID 11), mengikuti alur penyerahan semua Sortir ke grading.'],
                    ['Sortir selesai', 'Pcs dan gram awal/akhir sama. Tanggal dan tanggal input memakai tgl. Biaya dan denda 0.'],
                    ['Sisa belum grading', 'Formulir kategori grade dan tb_hancuran pcs 0 dibuat. Hasil grading belum dibuat.'],
                ], null, 'A23');
                $reference = $book->createSheet()->setTitle('Pengawas Grading');
                $reference->fromArray(['id_pengawas_grading', 'nama'], null, 'A1');
                foreach (DB::table('users')->where('posisi_id', '!=', 1)->orderBy('name')->get() as $index => $user) {
                    $reference->fromArray([$user->id, $user->name], null, 'A'.($index + 2), true);
                }
                $reference->getStyle('A1:B1')->getFont()->setBold(true);
                $reference->freezePane('A2');
                $reference->getColumnDimension('A')->setAutoSize(true);
                $reference->getColumnDimension('B')->setAutoSize(true);
            }
            $guide->getColumnDimension('A')->setWidth(32);
            $guide->getColumnDimension('B')->setWidth(105);
            $guide->getStyle('A1:B'.($grade ? 26 : ($sortir ? 22 : ($cetak ? 18 : 10))))->getAlignment()->setWrapText(true);
            if (! $bk) {
                $users = $book->createSheet()->setTitle('Pengawas');
                $users->fromArray(['id_pengawas', 'nama'], null, 'A1');
                foreach (DB::table('users')->where('posisi_id', 13)->orderBy('name')->get() as $index => $user) {
                    $users->fromArray([$user->id, $user->name], null, 'A'.($index + 2));
                }
                $users->getStyle('A1:B1')->getFont()->setBold(true);
                $users->freezePane('A2');
                foreach (['A', 'B'] as $column) {
                    $users->getColumnDimension($column)->setAutoSize(true);
                }
            }
            if ($cetak) {
                $receivers = $book->createSheet()->setTitle('Pengawas Cetak');
                $receivers->fromArray($sortir ? ['id_pengawas_cetak', 'nama', 'jumlah_anak_aktif'] : ['id_pengawas_cetak', 'nama'], null, 'A1');
                foreach (DB::table('users')->where('posisi_id', 14)->orderBy('name')->get() as $index => $user) {
                    $values = [$user->id, $user->name];
                    if ($sortir) {
                        $values[] = DB::table('tb_anak')->where('id_pengawas', $user->id)->where('berhenti', 'T')->count();
                    }
                    $receivers->fromArray($values, null, 'A'.($index + 2), true);
                }
                $children = $book->createSheet()->setTitle('Anak Cabut');
                $children->fromArray(['id_anak_cabut', 'nama', 'id_pengawas', 'id_kelas_cabut'], null, 'A1');
                foreach (DB::table('tb_anak')->where('id_pengawas', 285)->where('berhenti', 'T')
                    ->orderBy('nama')->get() as $index => $child) {
                    $children->fromArray([$child->id_anak, $child->nama, $child->id_pengawas, 126], null, 'A'.($index + 2), true);
                }
                $classes = $book->createSheet()->setTitle('Kelas Cabut');
                $classes->fromArray(['id_kelas_cabut', 'kelas', 'tipe'], null, 'A1');
                foreach (DB::table('tb_kelas')->where('id_kelas', 126)->get() as $index => $class) {
                    $classes->fromArray([$class->id_kelas, $class->kelas, $class->tipe], null, 'A'.($index + 2), true);
                }
                foreach ([$receivers, $children, $classes] as $reference) {
                    $reference->getStyle('A1:D1')->getFont()->setBold(true);
                    $reference->freezePane('A2');
                    foreach (range('A', 'D') as $column) {
                        $reference->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            $book->setActiveSheetIndex(0);
            (new Xlsx($book))->save('php://output');
            $book->disconnectWorksheets();
        }, 'Template Import '.$label.'.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function previewCabut(Request $request, OpnameCabutImport $import)
    {
        // Unggah baru menggantikan pratinjau sebelumnya, termasuk jika validasi gagal.
        $request->session()->forget('opname_cabut_preview');
        $request->validate([
            'status' => 'required|in:stok,cetak,sortir,grade,bk',
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);
        $import = $this->importer($request->status);
        try {
            $rows = $import->validateRows($import->read($request->file('file')->getRealPath()), $request->status);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors(['file' => 'File Excel tidak dapat dibaca. Gunakan template yang disediakan.']);
        }
        $request->session()->put('opname_cabut_preview', [
            'token' => (string) Str::uuid(), 'user_id' => (int) auth()->id(),
            'version' => 10, 'status' => $request->status, 'rows' => $rows,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'expires_at' => now()->addMinutes(30)->timestamp,
        ]);

        return redirect()->route('opname_menu.index');
    }

    public function importCabut(Request $request, OpnameCabutImport $import)
    {
        $request->validate(['token' => 'required|string']);
        $preview = $request->session()->get('opname_cabut_preview');
        if (! $preview || ($preview['version'] ?? 0) !== 10 || ! hash_equals($preview['token'], $request->token)
            || $preview['user_id'] !== (int) auth()->id() || $preview['expires_at'] < now()->timestamp) {
            return redirect()->route('opname_menu.index')->withErrors(['file' => 'Pratinjau sudah tidak berlaku. Unggah ulang file Anda.']);
        }
        $import = $this->importer($preview['status']);
        try {
            $result = $import->store($preview['rows'], $preview['status'], (int) auth()->id(), $preview['file_name'] ?? null);
        } catch (ValidationException $exception) {
            $request->session()->forget('opname_cabut_preview');
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('opname_menu.index')->withErrors(['file' => 'Import gagal disimpan. Seluruh perubahan dibatalkan. Silakan coba lagi.']);
        }
        $request->session()->forget('opname_cabut_preview');

        return redirect()->route('opname_menu.index')->with('sukses',
            "Berhasil import {$result['boxes']} box ke ".(OpnameBkImport::STATUSES + OpnameCabutImport::STATUSES + OpnameCetakImport::STATUSES + OpnameSortirImport::STATUSES + OpnameGradingImport::STATUSES)[$preview['status']].
            ($preview['status'] === 'bk' ? '. Stok BK sisa sudah tercatat.' : '. BK dan formulir sarang sudah tercatat.'))->with('opname_cabut_invoices', $result['invoices'])->with('opname_cetak_invoices', $result['cetak_invoices'])
            ->with('opname_sortir_invoices', $result['sortir_invoices'])->with('opname_grade_invoices', $result['grade_invoices'])->with('opname_history_id', $result['history_id']);
    }

    public function history(OpnameImportHistory $history)
    {
        return view('home.opname_menu.history', [
            'title' => 'Detail riwayat import #'.$history->id,
            'history' => $history,
            'statuses' => OpnameBkImport::STATUSES + OpnameCabutImport::STATUSES + OpnameCetakImport::STATUSES + OpnameSortirImport::STATUSES + OpnameGradingImport::STATUSES,
        ]);
    }

    public function destroy(OpnameImportHistory $history, OpnameImportCancellation $cancellation)
    {
        try {
            $boxes = $cancellation->cancel($history->id);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors(['history' => 'Pembatalan gagal. Seluruh perubahan dibatalkan dan data tetap tersimpan.']);
        }

        return redirect()->route('opname_menu.index')->with('sukses',
            "Import #{$history->id} dibatalkan. Seluruh alur transaksi {$boxes} box dan riwayat importnya sudah dihapus.");
    }

    private function importer(string $status): OpnameCabutImport
    {
        return match ($status) {
            'bk' => app(OpnameBkImport::class),
            'grade' => app(OpnameGradingImport::class),
            'sortir' => app(OpnameSortirImport::class),
            'cetak' => app(OpnameCetakImport::class),
            default => app(OpnameCabutImport::class),
        };
    }
}
