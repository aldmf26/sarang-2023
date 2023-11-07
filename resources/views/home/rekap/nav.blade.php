@php
    $rot = request()
        ->route()
        ->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link  {{ $rot == 'rekap.index' ? 'active' : '' }}" aria-current="page"
            href="{{ route('rekap.index') }}">Cabut</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'rekap.cetak' ? 'active' : '' }}" aria-current="page"
            href="{{ route('rekap.cetak') }}">Cetak</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'rekap.sortir' ? 'active' : '' }}" aria-current="page"
            href="{{ route('rekap.sortir') }}">Sortir</a>
    </li>
</ul>
