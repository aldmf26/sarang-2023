@php
    $rot = request()->route()->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link  {{ $rot == 'gudangsarang.cabut' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gudangsarang.cabut') }}">
            Cabut
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{ $rot == 'gudangsarang.gudang_cbt_selesai' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gudangsarang.gudang_cbt_selesai') }}">
            Cetak
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{ $rot == 'gudangsarang.gudang_cbt_selesai' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gudangsarang.gudang_cbt_selesai') }}">
            Sortir
        </a>
    </li>
</ul>
