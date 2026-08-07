<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'cabut' ? 'active' : '' }}" aria-current="page"
            href="{{ route('po.cabut') }}">Cabut</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'cetak' ? 'active' : '' }}"
            aria-current="page" href="{{ route('po.cetak') }}">Cetak</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'sortir' ? 'active' : '' }}"
            aria-current="page" href="{{ route('po.sortir') }}">Sortir</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'grade' ? 'active' : '' }}"
            aria-current="page" href="{{ route('po.grade') }}">Grade</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'grading' ? 'active' : '' }}"
            aria-current="page" href="{{ route('po.grading') }}">Grading</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'wip' ? 'active' : '' }}"
            aria-current="page" href="{{ route('po.wip') }}">Wip1</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'qc' ? 'active' : '' }}"
            aria-current="page" href="{{ route('po.qc') }}">Qc</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'wip2' ? 'active' : '' }}"
            aria-current="page" href="{{ route('po.wip2') }}">Wip2</a>
    </li>


</ul>
