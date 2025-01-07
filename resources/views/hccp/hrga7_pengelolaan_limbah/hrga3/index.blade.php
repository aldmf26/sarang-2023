<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        @livewire('identifikasi-limbah')

    </x-slot>

</x-theme.app>
