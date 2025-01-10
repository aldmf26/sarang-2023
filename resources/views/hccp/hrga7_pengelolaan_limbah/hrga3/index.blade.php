<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
                <a target="_blank" class="btn btn-sm btn-primary"
                                    href="{{ route('hrga7_3.print') }}"><i class="fas fa-print"></i> Cetak</a>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        @livewire('identifikasi-limbah')

    </x-slot>

</x-theme.app>
