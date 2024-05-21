@php
    $rot = request()->route()->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'laporanakhir.index' ? 'active' : '' }}" aria-current="page"
            href="{{ route('laporanakhir.index') }}">Laporan Perpartai</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'laporanakhir.summaryCetak' ? 'active' : '' }}" aria-current="page"
            href="{{ route('laporanakhir.summaryCetak') }}">Laporan Perhari</a>
    </li>
</ul>
