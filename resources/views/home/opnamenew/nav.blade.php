@php
    $rot = request()->route()->getName();
@endphp
<div class="col-lg-12">
    <ul class="nav nav-pills float-start">
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'opnamenew.index' ? 'active' : '' }}" aria-current="page"
                href="{{ route('opnamenew.index') }}">Cabut</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'opnamenew.cetak' ? 'active' : '' }}" aria-current="page"
                href="{{ route('opnamenew.cetak') }}">Cetak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'opnamenew.sortir' ? 'active' : '' }}" aria-current="page"
                href="{{ route('opnamenew.sortir') }}">Sortir</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'opnamenew.grading' ? 'active' : '' }}" aria-current="page"
                href="{{ route('opnamenew.grading') }}">grading & Pengiriman</a>
        </li>

    </ul>
</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>
