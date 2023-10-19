@php
    $rot = request()
        ->route()
        ->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link  {{ $kategori == 'cabut' ? 'active' : '' }}" aria-current="page"
            href="{{ route('bk.index', ['kategori' => 'cabut']) }}">BK
            Cabut</a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{ $kategori == 'cetak' ? 'active' : '' }}" aria-current="page"
            href="{{ route('bk.index', ['kategori' => 'cetak']) }}">BK
            Cetak</a>
    </li>
</ul>
