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
        ->where('tgl', "2024-$selectedBulan-$day")
        ->value($type);

@endphp
<style>
    .btn-xs {
        padding: 2px 6px;
        font-size: 0.75rem;
        /* Sesuaikan ukuran font */
        line-height: 1.5;
    }

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
            <a wire:click.prevent="tbhParaf('{{ $type }}', '{{ $item->name }}', '{{ "2024-$selectedBulan-$day" }}')"
                class="dropdown-item">
                {{ $item->name }} {{ $parafData == $item->name ? '☑️' : '' }}
            </a>
        @endforeach
    </div>
</div>
