<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <a href="{{ route('opname_menu.bulanan') }}" class="btn btn-sm btn-outline-secondary float-end">Daftar invoice</a>
    </x-slot>

    <x-slot name="cardBody">
        @if (session('sukses'))
            <div class="alert alert-success">{{ session('sukses') }}</div>
        @endif
        @php
            $totalPcs = (float) $totals->pcs;
            $totalGr = (float) $totals->gr;
            $totalRp = $canSeeBkValue ? (float) $totals->ttl_rp : null;
        @endphp

        <div class="card border-primary mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gudang BK Sisa</h5>
                <span class="badge bg-secondary">Tanpa pengawas</span>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('opname_menu.bulanan.hitung_bk') }}" class="row g-2 mb-3">
                    <div class="col-12 col-md-3">
                        <label for="bulan_opname" class="form-label">Bulan opname</label>
                        <input type="month" id="bulan_opname" name="bulan" value="{{ $bulan }}" class="form-control">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="search_bk_sisa" class="form-label">Cari partai, no. box, atau grade</label>
                        <input type="search" id="search_bk_sisa" name="q" value="{{ request('q') }}" class="form-control" maxlength="100" placeholder="Masukkan kata kunci">
                    </div>
                    <div class="col-12 col-md-3 d-none d-md-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('opname_menu.bulanan.hitung_bk') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
                <p class="small text-muted d-md-none">Ketik partai, nomor box, atau grade. Hasil akan muncul otomatis.</p>
                <div id="opnameResults" class="opname-results" data-search="{{ request('q', '') }}">
                <form method="POST" action="{{ route('opname_menu.bulanan.simpan') }}">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    @if ($bkSisa->count())
                        <div class="text-end"><button type="submit" class="btn btn-success mb-2 opname-save-button">Simpan hasil hitung halaman ini</button></div>
                    @endif
                    <p class="small text-muted d-md-none">Pcs dan gram real terisi dari stok sistem. Grade juga bisa diedit.</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle opname-bk-table">
                        <thead><tr><th>No</th><th>Partai</th><th>No. Box</th><th>Grade</th><th class="text-end">Pcs Sistem</th><th class="text-end">Gram Sistem</th><th class="text-end">Pcs Real</th><th class="text-end">Gram Real</th>@if ($canSeeBkValue)<th class="text-end">Nilai BK</th>@endif</tr></thead>
                        <tbody>
                            @forelse ($bkSisa as $row)
                                <tr>
                                    <td data-label="No">{{ $loop->iteration }}</td>
                                    <td data-label="Partai">{{ $row->nm_partai }}</td>
                                    <td data-label="No. Box">{{ $row->no_box }}</td>
                                    <td data-label="Grade"><input class="form-control form-control-sm" type="text" maxlength="100" name="counts[{{ $row->no_box }}][grade_real]" value="{{ $row->grade_real ?? $row->grade }}" aria-label="Grade real box {{ $row->no_box }}"></td>
                                    <td data-label="Pcs Sistem" class="text-end">{{ number_format($row->pcs, 0) }}</td>
                                    <td data-label="Gram Sistem" class="text-end">{{ number_format($row->gr, 2) }}</td>
                                    <td data-label="Pcs Real"><input class="form-control form-control-sm" type="number" min="0" step="1" name="counts[{{ $row->no_box }}][pcs_real]" value="{{ $row->pcs_real ?? $row->pcs }}" aria-label="Pcs real box {{ $row->no_box }}"></td>
                                    <td data-label="Gram Real"><input class="form-control form-control-sm" type="number" min="0" step="0.01" name="counts[{{ $row->no_box }}][gr_real]" value="{{ $row->gr_real ?? $row->gr }}" aria-label="Gram real box {{ $row->no_box }}"></td>
                                    @if ($canSeeBkValue)<td data-label="Nilai BK" class="text-end">{{ number_format($row->ttl_rp, 0) }}</td>@endif
                                </tr>
                            @empty
                                <tr><td colspan="{{ $canSeeBkValue ? 9 : 8 }}" class="text-center text-muted">Tidak ada data BK Sisa.</td></tr>
                            @endforelse
                        </tbody>
                        @if ($bkSisa->count())
                            <tfoot><tr class="fw-bold"><td colspan="4">Total sistem</td><td class="text-end">{{ number_format($totalPcs, 0) }}</td><td class="text-end">{{ number_format($totalGr, 2) }}</td><td colspan="2"></td>@if ($canSeeBkValue)<td class="text-end">{{ number_format($totalRp, 0) }}</td>@endif</tr></tfoot>
                        @endif
                    </table>
                </div>
                </form>
                {{ $bkSisa->links() }}
                </div>
            </div>
        </div>
        <style>
            @media (max-width: 767.98px) {
                .opname-bk-table thead { display: none; }
                .opname-bk-table,
                .opname-bk-table tbody,
                .opname-bk-table tbody tr,
                .opname-bk-table tbody td { display: block; width: 100%; }
                .opname-bk-table tbody tr {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    column-gap: .75rem;
                    margin-bottom: 1rem;
                    padding: .5rem .75rem;
                    border: 1px solid #dee2e6;
                    border-radius: .5rem;
                    background: #fff;
                    box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
                }
                .opname-bk-table tbody td {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: .75rem;
                    padding: .5rem 0;
                    width: auto;
                    text-align: right !important;
                    border: 0;
                    border-bottom: 1px solid #f0f0f0;
                }
                .opname-bk-table tbody td:nth-child(1) { display: none; }
                .opname-bk-table tbody td:nth-child(2) {
                    grid-column: 1 / -1;
                    justify-content: flex-start;
                    padding-top: .25rem;
                    color: #25396f;
                    font-size: 1.05rem;
                    font-weight: 700;
                }
                .opname-bk-table tbody td:nth-child(3) { grid-column: 1 / -1; }
                .opname-bk-table tbody td:nth-child(7),
                .opname-bk-table tbody td:nth-child(8) {
                    display: flex;
                    flex-direction: column;
                    align-items: stretch;
                    gap: .35rem;
                    padding: .65rem .25rem;
                    border: 0;
                }
                .opname-bk-table tbody td:nth-child(7)::before,
                .opname-bk-table tbody td:nth-child(8)::before {
                    flex: none;
                    color: #25396f;
                }
                .opname-bk-table tbody td:last-child { border-bottom: 0; }
                .opname-bk-table tbody td::before {
                    content: attr(data-label);
                    flex: 0 0 42%;
                    color: #6c757d;
                    font-size: .875rem;
                    font-weight: 600;
                    text-align: left;
                }
                .opname-bk-table tbody td input { width: 100%; min-height: 50px; text-align: center; font-size: 1.1rem; }
                .opname-bk-table tfoot { display: none; }
                .opname-save-button { position: sticky; bottom: .5rem; width: 100%; min-height: 48px; z-index: 2; box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .2); }
                .opname-mobile-empty .opname-results { display: none; }
            }
        </style>
        @section('scripts')
            <script>
                (() => {
                    const search = document.getElementById('search_bk_sisa');
                    const searchForm = search?.form;
                    const results = document.getElementById('opnameResults');
                    if (!search || !searchForm || !results) return;

                    const setMobileState = () => {
                        document.querySelector('.card.border-primary')?.classList.toggle(
                            'opname-mobile-empty',
                            window.matchMedia('(max-width: 767.98px)').matches &&
                                (!search.value.trim() || search.value.trim() !== results.dataset.search.trim())
                        );
                    };
                    let timer;
                    search.addEventListener('input', () => {
                        setMobileState();
                        if (!window.matchMedia('(max-width: 767.98px)').matches) return;
                        clearTimeout(timer);
                        timer = setTimeout(() => searchForm.requestSubmit(), 450);
                    });
                    setMobileState();
                    window.addEventListener('resize', setMobileState);
                })();
            </script>
        @endsection
    </x-slot>
</x-theme.app>
