@php
    $rot = request()
        ->route()
        ->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link  {{ $rot == 'kelas.index' && request()->get('jenis') == '' ? 'active' : '' }}"
            aria-current="page" href="{{ route('kelas.index') }}">Pcs Cbt</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'kelas.index' && request()->get('jenis') == 'gr' ? 'active' : '' }}"
            aria-current="page" href="{{ route('kelas.index', ['jenis' => 'gr']) }}">Gr Cbt</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'kelas.spesial' ? 'active' : '' }}" aria-current="page"
            href="{{ route('kelas.spesial') }}">Spesial</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'kelas.eo' ? 'active' : '' }}" aria-current="page"
            href="{{ route('kelas.eo') }}">Eo</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'kelas.cetak' ? 'active' : '' }}" aria-current="page"
            href="{{ route('kelas.cetak') }}">Cetak</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'kelas.sortir' ? 'active' : '' }}" aria-current="page"
            href="{{ route('kelas.sortir') }}">Sortir</a>
    </li>
</ul>
