<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>

    </x-slot>

    <x-slot name="cardBody">
        @livewire('ceklist-pengecekan-air')
    </x-slot>
</x-theme.app>
