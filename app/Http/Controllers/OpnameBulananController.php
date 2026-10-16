<?php

namespace App\Http\Controllers;

use App\Models\OpnameBulananInvoice;
use App\Services\OpnameBulananPosting;
use App\Services\OpnameBulananSource;
use App\Services\OpnameBulananUncounted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OpnameBulananController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['bulan' => 'nullable|date_format:Y-m',
            'status' => 'nullable|in:draft,submitted,locked', 'q' => 'nullable|string|max:100']);
        $bulan = $filters['bulan'] ?? now()->format('Y-m');
        $query = OpnameBulananInvoice::with(['pengawas', 'pembuat'])->withCount('lines')
            ->withSum('lines', 'pcs_real')->withSum('lines', 'gr_real')
            ->where('bulan', $bulan.'-01');
        if ((int) $request->user()->posisi_id !== 1) {
            $query->where(fn ($q) => $q->where('created_by', $request->user()->id)->orWhere('pengawas_id', $request->user()->id));
        }
        $invoices = $query->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['q'] ?? null, fn ($q, $v) => $q->where(fn ($q) => $q
                ->where('no_invoice', 'like', '%'.$v.'%')->orWhere('nm_partai', 'like', '%'.$v.'%')->orWhere('tujuan', 'like', '%'.$v.'%')
                ->orWhereHas('lines', fn ($q) => $q->where('no_box', 'like', '%'.$v.'%')->orWhere('no_box_baru', 'like', '%'.$v.'%'))))
            ->orderByDesc('id')->paginate(20)->withQueryString();

        return view('home.opname_menu.bulanan_invoices', [
            'title' => 'Opname Bulanan', 'bulan' => $bulan, 'invoices' => $invoices,
            'positions' => OpnameBulananSource::POSITIONS,
            'pengawas' => DB::table('users')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $values = $request->validate([
            'bulan' => 'required|date_format:Y-m', 'tanggal' => 'required|date_format:Y-m-d',
            'posisi' => ['required', Rule::in(array_keys(OpnameBulananSource::POSITIONS))],
            'pengawas_id' => 'required|integer|exists:users,id', 'tujuan' => 'required|string|max:100',
            'nm_partai' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:2000',
        ]);
        $values['bulan'] .= '-01';
        $values['nm_partai'] = trim($values['nm_partai']);
        $values['tujuan'] = trim($values['tujuan']);
        if ($values['nm_partai'] === '' || $values['tujuan'] === '') {
            app(OpnameBulananSource::class)->reject('Nama partai dan tujuan pengiriman wajib diisi.');
        }
        $invoice = OpnameBulananInvoice::create($values + [
            'no_invoice' => 'OPN-'.str_replace('-', '', substr($values['bulan'], 0, 7)).'-'.strtoupper(Str::random(8)),
            'created_by' => $request->user()->id, 'status' => 'draft',
        ]);

        return redirect()->route('opname_bulanan.show', $invoice->id)->with('sukses', 'Invoice dibuat. Isi baris box hasil hitung.');
    }

    public function uncounted(Request $request, OpnameBulananUncounted $stock)
    {
        $filters = $request->validate([
            'bulan' => 'nullable|date_format:Y-m',
            'posisi' => ['nullable', Rule::in(array_keys(OpnameBulananSource::POSITIONS))],
            'pengawas_id' => ['nullable', 'integer', 'min:0', ...((string) $request->input('pengawas_id') === '0' ? [] : [Rule::exists('users', 'id')])],
            'q' => 'nullable|string|max:100',
        ]);
        $month = $filters['bulan'] ?? now()->format('Y-m');
        $query = $stock->query($month, $filters);
        $totals = (clone $query)->select([DB::raw('COUNT(*) AS boxes'),
            DB::raw('COALESCE(SUM(stock.pcs), 0) AS pcs'), DB::raw('COALESCE(SUM(stock.gr), 0) AS gr')])->first();

        return view('home.opname_menu.bulanan_uncounted', [
            'title' => 'Stok Belum Diopname', 'bulan' => $month,
            'stocks' => $query->orderBy('stock.posisi')->orderBy('owner.name')->orderBy('stock.no_box')->paginate(25)->withQueryString(),
            'totals' => $totals, 'positions' => OpnameBulananSource::POSITIONS,
            'pengawas' => DB::table('users')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Request $request, int $invoice)
    {
        $record = OpnameBulananInvoice::with(['lines', 'pengawas', 'pembuat'])->findOrFail($invoice);
        $this->viewable($request, $record);

        return view('home.opname_menu.bulanan_invoice', [
            'title' => $record->no_invoice, 'invoice' => $record, 'positions' => OpnameBulananSource::POSITIONS,
            'editable' => $record->status === 'draft' && (int) $request->user()->id === (int) $record->created_by,
            'approver' => (int) $request->user()->posisi_id === 1 || (int) $request->user()->id === (int) $record->pengawas_id,
            'canSeeValue' => (int) $request->user()->posisi_id === 1,
        ]);
    }

    public function print(Request $request, int $invoice)
    {
        $record = OpnameBulananInvoice::with(['lines', 'pengawas', 'pembuat'])->findOrFail($invoice);
        $this->viewable($request, $record);

        return view('home.opname_menu.bulanan_invoice_print', [
            'invoice' => $record,
            'position' => OpnameBulananSource::POSITIONS[$record->posisi],
            'approvedBy' => $record->locked_by ? DB::table('users')->where('id', $record->locked_by)->value('name') : null,
        ]);
    }

    public function source(Request $request, int $invoice, OpnameBulananSource $source)
    {
        $record = OpnameBulananInvoice::findOrFail($invoice);
        $this->editable($request, $record);
        $values = $request->validate(['no_box' => ['required', 'regex:/^[1-9][0-9]{0,8}$/', 'not_in:9999']]);
        $existingLine = $record->lines()->where('no_box', $values['no_box'])->first(['baris']);
        if ($existingLine) {
            $source->reject("Box {$values['no_box']} sudah ada pada baris {$existingLine->baris} di invoice ini.");
        }
        $data = $source->read($values['no_box'], $record->posisi, $invoice);
        $this->owner($data, $record, $source);

        // Biaya dan baris transaksi hanya boleh dibaca owner, bukan dari JSON lookup petugas.
        return response()->json(array_intersect_key($data, array_flip(['no_box', 'nm_partai', 'grade', 'pcs', 'gr', 'posisi'])));
    }

    public function saveLine(Request $request, int $invoice, OpnameBulananSource $source)
    {
        $values = $request->validate([
            'line_id' => 'nullable|integer', 'baris' => ['required', 'regex:/^[1-9][0-9]{0,3}-[1-9][0-9]{0,3}$/'],
            'no_box' => ['required', 'regex:/^[1-9][0-9]{0,8}$/', 'not_in:9999'],
            'pcs_real' => 'required|integer|min:0|max:1000000000',
            'gr_real' => 'required|numeric|gt:0|max:1000000000|decimal:0,2',
            'grade_real' => 'required|string|max:50',
        ]);
        DB::transaction(function () use ($request, $invoice, $source, $values) {
            $record = OpnameBulananInvoice::lockForUpdate()->findOrFail($invoice);
            $this->editable($request, $record);
            $line = ! empty($values['line_id']) ? $record->lines()->findOrFail($values['line_id']) : null;
            if ($record->lines()->when($line, fn ($q) => $q->where('id', '!=', $line->id))
                ->where(fn ($q) => $q->where('baris', $values['baris'])->orWhere('no_box', $values['no_box']))->exists()) {
                $source->reject('Nomor baris atau nomor box sudah tercatat pada invoice ini.');
            }
            if (! $line && $record->lines()->count() >= 500) {
                $source->reject('Maksimal 500 box per invoice.');
            }
            $data = $source->read($values['no_box'], $record->posisi, $invoice, true);
            $this->owner($data, $record, $source);
            $grade = trim($values['grade_real']);
            if ($grade === '' || strtolower($grade) === 'susut') {
                $source->reject('Isi grade aktual barang; grade susut tidak digunakan untuk box opname.');
            }
            $partai = trim((string) $record->nm_partai);
            if ($partai === '') {
                $source->reject('Nama partai invoice wajib diisi sebelum menambah box.');
            }
            $costs = [];
            foreach (['cost_bk', 'cost_kerja', 'cost_op', 'cost_cu'] as $field) {
                $costs[$field] = round((float) $data[$field], 2);
                if ($costs[$field] < 0 || $costs[$field] > 9999999999999999) {
                    $source->reject('Biaya sumber tidak valid.');
                }
            }
            $attributes = $costs + [
                'baris' => $values['baris'], 'no_box' => $values['no_box'], 'grade_real' => $grade,
                'pcs_real' => $values['pcs_real'], 'gr_real' => $values['gr_real'],
                'nm_partai' => $partai, 'grade_sistem' => $data['grade'],
                'pcs_sistem' => $data['pcs'], 'gr_sistem' => $data['gr'], 'snapshot' => $data,
                'modal_baru' => array_sum($costs), 'harga_baru' => array_sum($costs) / (float) $values['gr_real'],
            ];
            if ($attributes['modal_baru'] >= 10000000000000000) {
                $source->reject('Total modal melebihi kapasitas pencatatan biaya.');
            }
            $line ? $line->update($attributes) : $record->lines()->create($attributes);
        });

        return redirect()->route('opname_bulanan.show', $invoice)->with('sukses', 'Baris opname disimpan beserta snapshot stok dan biaya terkini.');
    }

    public function deleteLine(Request $request, int $invoice, int $line)
    {
        DB::transaction(function () use ($request, $invoice, $line) {
            $record = OpnameBulananInvoice::lockForUpdate()->findOrFail($invoice);
            $this->editable($request, $record);
            $record->lines()->findOrFail($line)->delete();
        });

        return redirect()->route('opname_bulanan.show', $invoice)->with('sukses', 'Baris dihapus dari draft.');
    }

    public function update(Request $request, int $invoice)
    {
        $values = $request->validate(['tanggal' => 'required|date_format:Y-m-d',
            'nm_partai' => 'required|string|max:100', 'tujuan' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:2000']);
        $values['nm_partai'] = trim($values['nm_partai']);
        $values['tujuan'] = trim($values['tujuan']);
        if ($values['nm_partai'] === '' || $values['tujuan'] === '') {
            app(OpnameBulananSource::class)->reject('Nama partai dan tujuan pengiriman wajib diisi.');
        }
        DB::transaction(function () use ($request, $invoice, $values) {
            $record = OpnameBulananInvoice::lockForUpdate()->findOrFail($invoice);
            $this->editable($request, $record);
            $record->update($values);
            $record->lines()->update(['nm_partai' => $values['nm_partai']]);
        });

        return redirect()->route('opname_bulanan.show', $invoice)->with('sukses', 'Data invoice diperbarui.');
    }

    public function destroy(Request $request, int $invoice)
    {
        DB::transaction(function () use ($request, $invoice) {
            $record = OpnameBulananInvoice::lockForUpdate()->findOrFail($invoice);
            $this->editable($request, $record);
            $record->delete();
        });

        return redirect()->route('opname_menu.bulanan', ['bulan' => $request->input('bulan')])
            ->with('sukses', 'Invoice draft berhasil dihapus.');
    }

    public function submit(Request $request, int $invoice, OpnameBulananSource $source)
    {
        DB::transaction(function () use ($request, $invoice, $source) {
            DB::table('opname_bulanan_sequences')->where('name', 'posting')->lockForUpdate()->first();
            DB::table('opname_bulanan_sequences')->where('name', 'posting')->increment('value');
            $record = OpnameBulananInvoice::lockForUpdate()->findOrFail($invoice);
            $this->editable($request, $record);
            $lines = $record->lines()->lockForUpdate()->get();
            if ($lines->isEmpty()) {
                $source->reject('Isi minimal satu box sebelum mengajukan invoice.');
            }
            foreach ($lines as $line) {
                $data = $source->read($line->no_box, $record->posisi, $invoice, true);
                $this->owner($data, $record, $source);
                if ($data['fingerprint'] !== $line->snapshot['fingerprint']) {
                    $source->reject("Box {$line->no_box} berubah sejak dihitung. Simpan ulang baris box tersebut.");
                }
            }
            $record->update(['status' => 'submitted', 'submitted_at' => now(), 'alasan_kembali' => null]);
        });

        return redirect()->route('opname_bulanan.show', $invoice)->with('sukses', 'Invoice diajukan kepada pengawas.');
    }

    public function returnDraft(Request $request, int $invoice)
    {
        $values = $request->validate(['alasan' => 'required|string|max:2000']);
        DB::transaction(function () use ($request, $invoice, $values) {
            $record = OpnameBulananInvoice::lockForUpdate()->findOrFail($invoice);
            abort_unless((int) $request->user()->posisi_id === 1 || (int) $request->user()->id === (int) $record->pengawas_id, 403);
            abort_unless($record->status === 'submitted', 409, 'Hanya invoice diajukan yang dapat dikembalikan.');
            $record->update(['status' => 'draft', 'submitted_at' => null, 'alasan_kembali' => $values['alasan']]);
        });

        return redirect()->route('opname_bulanan.show', $invoice)->with('sukses', 'Invoice dikembalikan kepada pembuat.');
    }

    public function lock(Request $request, int $invoice, OpnameBulananPosting $posting)
    {
        try {
            $record = $posting->lock($invoice, $request->user());
        } catch (\Illuminate\Database\QueryException $error) {
            report($error);

            return redirect()->route('opname_bulanan.show', $invoice)
                ->withErrors(['opname' => 'Penguncian gagal disimpan. Seluruh transaksi dibatalkan; invoice masih dapat diperiksa kembali.']);
        }

        return redirect()->route('opname_bulanan.show', $invoice)
            ->with('sukses', "Invoice terkunci. Pengiriman {$record->no_pengiriman} ke {$record->tujuan} tercatat dan box baru sudah dibuat di posisi asal.");
    }

    private function viewable(Request $request, $invoice): void
    {
        abort_unless((int) $request->user()->posisi_id === 1
            || in_array((int) $request->user()->id, [(int) $invoice->created_by, (int) $invoice->pengawas_id], true), 403);
    }

    private function editable(Request $request, $invoice): void
    {
        abort_unless((int) $request->user()->id === (int) $invoice->created_by, 403);
        abort_unless($invoice->status === 'draft', 409, 'Invoice yang diajukan / terkunci tidak dapat diedit.');
    }

    private function owner(array $data, $invoice, OpnameBulananSource $source): void
    {
        if ($data['pengawas_id'] && (int) $data['pengawas_id'] !== (int) $invoice->pengawas_id) {
            $source->reject('Pengawas invoice harus sesuai pengawas pemegang box.');
        }
    }
}
