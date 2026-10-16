<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <h4 class="mb-4">Opname</h4>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('opname_menu.index') }}" class="text-decoration-none">
                    <div class="card cardHover pointer border border-primary h-100">
                        <div class="card-body text-center p-4">
                            <img src="{{ asset('img/storage-stacks.png') }}" width="96" height="96" alt="" class="mb-3">
                            <h5 class="mb-2">Import Opname</h5>
                            <p class="text-muted mb-3">Import BK sisa, Cabut, Cetak, Sortir, dan sisa belum grading. Lihat history serta batalkan import.</p>
                            <span class="btn btn-primary">Buka Import Opname <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('opname_menu.bulanan') }}" class="text-decoration-none">
                    <div class="card cardHover pointer border border-primary h-100">
                        <div class="card-body text-center p-4">
                            <img src="{{ asset('img/storage-stacks.png') }}" width="96" height="96" alt="" class="mb-3">
                            <h5 class="mb-2">Opname Bulanan</h5>
                            <p class="text-muted mb-3">Hitung box per invoice, ajukan kepada pengawas, lalu kunci untuk pengiriman dan modal awal box baru.</p>
                            <span class="btn btn-primary">Buka Opname Bulanan <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </x-slot>
</x-theme.app>
