@php
    $rot = request()->route()->getName();
@endphp
<div class="col-lg-12">
    <ul class="nav nav-pills float-start">
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'cocokan.index' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cocokan.index') }}">Cabut</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'cocokan.cetak' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cocokan.cetak') }}">Cetak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'cocokan.sortir' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cocokan.sortir') }}">Sortir</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'cocokan.grading' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cocokan.grading') }}">Grading & Pengiriman</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'cocokan.balancesheet' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cocokan.balancesheet') }}">Balance Sheet</a>
        </li>
    </ul>
</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>
