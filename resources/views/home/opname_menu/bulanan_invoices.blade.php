<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">Opname Bulanan</h6>
        <a href="{{ route('opname.index') }}" class="btn btn-sm btn-outline-secondary float-end">Kembali</a>
    </x-slot>
    <x-slot name="cardBody">
        @if (session('sukses'))<div class="alert alert-success">{{ session('sukses') }}</div>@endif
        @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="alert alert-light border mb-4">
            <strong>Hitung box → Ajukan ke pengawas → Kunci invoice</strong>
            <p class="mb-0 mt-1">Saat dikunci, box lama diteruskan menjadi satu pengiriman ke tujuan invoice. Box baru dibuat di posisi asal dengan pcs, gram, dan grade hasil hitung. Akumulasi biaya menjadi modal BK awal box baru.</p>
        </div>
        <div class="card border-primary mb-4">
            <div class="card-header"><h6 class="mb-0">Buat invoice opname</h6></div>
            <div class="card-body">
                <form action="{{ route('opname_bulanan.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-6 col-md-2"><label for="create-bulan" class="form-label">Periode</label><input id="create-bulan" type="month" name="bulan" value="{{ old('bulan', $bulan) }}" class="form-control" required></div>
                    <div class="col-6 col-md-2"><label for="create-tanggal" class="form-label">Tanggal opname</label><input id="create-tanggal" type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="form-control" required></div>
                    <div class="col-md-3"><label for="create-posisi" class="form-label">Posisi stok</label><select id="create-posisi" name="posisi" class="form-select" required>@foreach ($positions as $key => $label)<option value="{{ $key }}" @selected(old('posisi', 'cabut') === $key)>{{ $label }}</option>@endforeach</select></div>
                    <div class="col-md-3"><label for="create-pengawas" class="form-label">Pengawas pemegang / pemeriksa</label><select id="create-pengawas" name="pengawas_id" class="form-select" required><option value="">Pilih pengawas</option>@foreach ($pengawas as $user)<option value="{{ $user->id }}" @selected((string) old('pengawas_id') === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
                    <div class="col-md-2"><label for="create-tujuan" class="form-label">Tujuan pengiriman</label><input id="create-tujuan" name="tujuan" value="{{ old('tujuan') }}" placeholder="Misalnya Joe" maxlength="100" class="form-control" required></div>
                    <div class="col-md-4"><label for="create-partai" class="form-label">Nama partai</label><input id="create-partai" name="nm_partai" value="{{ old('nm_partai') }}" placeholder="Contoh: BJM 1013" maxlength="100" class="form-control" required></div>
                    <div class="col-md-6"><label for="create-catatan" class="form-label">Catatan</label><input id="create-catatan" name="catatan" value="{{ old('catatan') }}" maxlength="2000" class="form-control"></div>
                    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Buat invoice</button></div>
                </form>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="mb-0">Daftar invoice</h6>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('opname_menu.bulanan.belum_opname', ['bulan' => $bulan]) }}">Stok belum diopname</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('opname_menu.bulanan.hitung_bk', ['bulan' => $bulan]) }}">Hitungan BK sebelumnya</a>
            </div>
        </div>
        <form method="GET" action="{{ route('opname_menu.bulanan') }}" class="row g-2 mb-3">
            <div class="col-6 col-md-2"><label for="filter-bulan" class="form-label">Periode</label><input id="filter-bulan" type="month" name="bulan" value="{{ $bulan }}" class="form-control"></div>
            <div class="col-6 col-md-2"><label for="filter-status" class="form-label">Status</label><select id="filter-status" name="status" class="form-select"><option value="">Semua</option><option value="draft" @selected(request('status') === 'draft')>Draft</option><option value="submitted" @selected(request('status') === 'submitted')>Diajukan</option><option value="locked" @selected(request('status') === 'locked')>Terkunci</option></select></div>
            <div class="col-md-6"><label for="filter-q" class="form-label">Cari invoice, partai, tujuan, box lama / baru</label><input id="filter-q" name="q" value="{{ request('q') }}" maxlength="100" class="form-control"></div>
            <div class="col-md-2 d-flex align-items-end"><button class="btn btn-outline-primary w-100">Cari</button></div>
        </form>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead><tr><th>Invoice</th><th>Tanggal</th><th>Partai</th><th>Posisi</th><th>Pengawas</th><th>Tujuan</th><th>Status</th><th class="text-end">Box</th><th class="text-end">Pcs real</th><th class="text-end">Gram real</th><th>Pengiriman</th><th></th></tr></thead>
                <tbody>
                    @forelse ($invoices as $record)
                        <tr><td>{{ $record->no_invoice }}<small class="d-block text-muted">{{ $record->pembuat?->name }}</small></td><td>{{ \Carbon\Carbon::parse($record->tanggal)->format('d-m-Y') }}</td><td>{{ $record->nm_partai ?? '—' }}</td><td>{{ $positions[$record->posisi] }}</td><td>{{ $record->pengawas?->name }}</td><td>{{ $record->tujuan }}</td><td><span class="badge bg-{{ $record->status === 'locked' ? 'success' : ($record->status === 'submitted' ? 'warning text-dark' : 'secondary') }}">{{ ['draft' => 'Draft', 'submitted' => 'Diajukan', 'locked' => 'Terkunci'][$record->status] }}</span></td><td class="text-end">{{ $record->lines_count }}</td><td class="text-end">{{ number_format($record->lines_sum_pcs_real ?? 0) }}</td><td class="text-end">{{ number_format($record->lines_sum_gr_real ?? 0) }}</td><td>{{ $record->no_pengiriman ?? '—' }}</td><td><div class="d-flex gap-1"><a class="btn btn-sm btn-outline-primary" href="{{ route('opname_bulanan.show', $record->id) }}">Buka</a>@if ($record->status === 'draft' && (int) auth()->id() === (int) $record->created_by)<form action="{{ route('opname_bulanan.destroy', $record->id) }}" method="POST" onsubmit="return confirm('Hapus seluruh invoice draft ini beserta semua baris box-nya?')">@csrf @method('DELETE')<input type="hidden" name="bulan" value="{{ $bulan }}"><button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button></form>@endif</div></td></tr>
                    @empty
                        <tr><td colspan="12" class="text-center text-muted py-4">Belum ada invoice opname untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $invoices->links() }}
    </x-slot>
</x-theme.app>
