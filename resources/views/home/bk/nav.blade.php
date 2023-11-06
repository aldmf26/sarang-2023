<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link  {{ $kategori == 'cabut' ? 'active' : '' }}" aria-current="page"
            href="{{ route("bk.$name", ['kategori' => 'cabut']) }}">Cabut</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'sortir' ? 'active' : '' }}" aria-current="page"
        href="{{ route("bk.$name", ['kategori' => 'sortir']) }}">Sortir</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'cetak' ? 'active' : '' }}" aria-current="page"
        href="{{ route("bk.$name", ['kategori' => 'cetak']) }}">Cetak</a>
    </li>
</ul>