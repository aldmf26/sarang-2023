@php
    $rot = request()->route()->getName();
@endphp
<div class="col-lg-12">
    <ul class="nav nav-pills float-start">
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'gudang.index' ? 'active' : '' }}" aria-current="page"
                href="{{ route('gudang.index') }}">Gudang Bk</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'gudang.cetak' ? 'active' : '' }}" aria-current="page"
                href="{{ route('gudang.cetak') }}">Gudang Cetak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'gudang.sortir' ? 'active' : '' }}" aria-current="page"
                href="{{ route('gudang.sortir') }}">Gudang Sortir</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'gudang.grading' ? 'active' : '' }}" aria-current="page"
                href="{{ route('gudang.grading') }}">Grading</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'gudang.pengiriman' ? 'active' : '' }}" aria-current="page"
                href="{{ route('gudang.pengiriman') }}">Pengiriman</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'gudang.totalan' ? 'active' : '' }}" aria-current="page"
                href="{{ route('gudang.totalan') }}">Totalan</a>
        </li>

    </ul>
</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>
