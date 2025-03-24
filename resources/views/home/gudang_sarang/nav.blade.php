<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{ $route == 'bkbaru.invoice' && $kategori == 'cabut' ? 'active' : '' }}" aria-current="page"
            href="{{ route('bkbaru.invoice') }}">Cabut</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gudangsarang.invoice' && $kategori == 'cetak' ? 'active' : '' }}"
            aria-current="page" href="{{ route('gudangsarang.invoice') }}">Cetak</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gudangsarang.invoice_sortir' && $kategori == 'sortir' ? 'active' : '' }}"
            aria-current="page" href="{{ route('gudangsarang.invoice_sortir', ['kategori' => 'sortir']) }}">Sortir</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gudangsarang.invoice_grade' && $kategori == 'grade' ? 'active' : '' }}"
            aria-current="page" href="{{ route('gudangsarang.invoice_grade', ['kategori' => 'grade']) }}">Grade</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gudangsarang.invoice_grading' && $kategori == 'grading' ? 'active' : '' }}"
            aria-current="page"
            href="{{ route('gudangsarang.invoice_grading', ['kategori' => 'grading']) }}">Grading</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gudangsarang.invoice_wip' && $kategori == 'wip' ? 'active' : '' }}"
            aria-current="page" href="{{ route('gudangsarang.invoice_wip', ['kategori' => 'wip']) }}">Wip1</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gudangsarang.invoice_qc' && $kategori == 'qc' ? 'active' : '' }}"
            aria-current="page" href="{{ route('gudangsarang.invoice_qc', ['kategori' => 'qc']) }}">Qc</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gudangsarang.invoice_wip2' && $kategori == 'wip2' ? 'active' : '' }}"
            aria-current="page" href="{{ route('gudangsarang.invoice_wip2', ['kategori' => 'wip2']) }}">Wip2</a>
    </li>


</ul>
