<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">Stok belum diopname</h6>
        <a class="btn btn-sm btn-outline-secondary float-end" href="{{ route('opname_menu.bulanan', ['bulan' => $bulan]) }}">Daftar invoice</a>
    </x-slot>
    <x-slot name="cardBody">
        @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="alert alert-info">
            Daftar ini membandingkan <strong>stok sistem saat ini</strong> dengan invoice opname periode <strong>{{ \Carbon\Carbon::parse($bulan.'-01')->format('m-Y') }}</strong>.
            Box yang sudah masuk invoice draft, diajukan, atau terkunci tidak ditampilkan. Box baru hasil penguncian pada periode yang sama juga dikecualikan.
        </div>
        <form method="GET" action="{{ route('opname_menu.bulanan.belum_opname') }}" class="row g-3 mb-4">
            <div class="col-6 col-md-2"><label for="uncounted-month" class="form-label">Periode opname</label><input id="uncounted-month" type="month" name="bulan" class="form-control" value="{{ $bulan }}" required></div>
            <div class="col-6 col-md-2"><label for="uncounted-position" class="form-label">Posisi stok</label><select id="uncounted-position" name="posisi" class="form-select"><option value="">Semua posisi</option>@foreach ($positions as $key => $label)<option value="{{ $key }}" @selected(request('posisi') === $key)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-3"><label for="uncounted-owner" class="form-label">Pengawas</label><select id="uncounted-owner" name="pengawas_id" class="form-select"><option value="">Semua pengawas</option><option value="0" @selected((string) request('pengawas_id') === '0')>Tanpa pengawas</option>@foreach ($pengawas as $user)<option value="{{ $user->id }}" @selected((string) request('pengawas_id') === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
            <div class="col-md-3"><label for="uncounted-search" class="form-label">Cari box, partai, grade</label><input id="uncounted-search" name="q" class="form-control" maxlength="100" value="{{ request('q') }}"></div>
            <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Tampilkan</button></div>
        </form>
        <div class="row g-3 mb-4">
            <div class="col-4"><div class="border rounded p-3"><small class="text-muted">Box belum tercatat</small><div class="fs-4 fw-bold">{{ number_format($totals->boxes) }}</div></div></div>
            <div class="col-4"><div class="border rounded p-3"><small class="text-muted">Pcs sistem</small><div class="fs-4 fw-bold">{{ number_format($totals->pcs) }}</div></div></div>
            <div class="col-4"><div class="border rounded p-3"><small class="text-muted">Gram sistem</small><div class="fs-4 fw-bold">{{ number_format($totals->gr) }}</div></div></div>
        </div>
        <p class="small text-muted">Jika seluruh fisik sudah dihitung tetapi box masih muncul, telusuri apakah box terlewat, perpindahannya belum tercatat, atau fisiknya tidak ditemukan. Invoice draft tetap perlu diajukan dan disetujui pengawas.</p>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead><tr><th>No. box</th><th>Partai</th><th>Posisi</th><th>Pengawas</th><th>Grade asal</th><th class="text-end">Pcs sistem</th><th class="text-end">Gram sistem</th><th>Status / tindak lanjut</th></tr></thead>
                <tbody>
                    @forelse ($stocks as $row)
                        <tr><td class="fw-bold">{{ $row->no_box }}</td><td>{{ $row->nm_partai }}</td><td>{{ $positions[$row->posisi] }}</td><td>{{ $row->pengawas }}</td><td>{{ $row->grade }}</td><td class="text-end">{{ number_format($row->pcs) }}</td><td class="text-end">{{ number_format($row->gr) }}</td><td><span class="badge bg-warning text-dark">Belum diopname</span>@if ($row->issue)<small class="d-block text-danger mt-1">{{ $row->issue }}</small>@endif</td></tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada stok belum diopname sesuai filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $stocks->links() }}
    </x-slot>
</x-theme.app>
