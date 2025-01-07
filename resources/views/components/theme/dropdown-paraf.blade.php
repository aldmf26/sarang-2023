@props([
'tbl',
'items',
'type',
'date',
'selectedArea',
'selectedBulan',
'day'
])

@php
    $parafData = DB::table($tbl)
        ->where('id_lokasi', $selectedArea)
        ->where('tgl', "2025-$selectedBulan-$day")
        ->value($type);

@endphp
<style>
    

    .dropdown-menu {
        background-color: #e4e8ff;
        /* Warna latar belakang */
    }

    .dropdown-item:hover {
        background-color: #d4d4d4;
        /* Warna latar belakang saat hover */
        color: #000;
        /* Warna teks */
    }
</style>
<div class="dropdown dropdown-color-icon" bis_skin_checked="1">
    <button class="btn btn-xs btn-{{ $parafData ? '' : 'outline-' }}info dropdown-toggle" type="button"
        id="dropdownMenuButtonEmoji" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="me-50">🧑‍🚒</span>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonEmoji" bis_skin_checked="1" style="">
        @foreach ($items as $item)
            <a wire:click.prevent="tbhParaf('{{ $type }}', '{{ $item->name }}', '{{ "2025-$selectedBulan-$day" }}')"
                class="dropdown-item">
                {{ $item->name }} {{ $parafData == $item->name ? '☑️' : '' }}
            </a>
        @endforeach
    </div>
</div>
