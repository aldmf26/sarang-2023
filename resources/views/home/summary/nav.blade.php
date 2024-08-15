@php
    $rot = request()->route()->getName();
@endphp
<div class="col-lg-12">
    <ul class="nav nav-pills float-start">
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'summary.index' ? 'active' : '' }}" aria-current="page"
                href="{{ route('summary.index') }}">Gudang Bk</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $rot == 'summary.bk_sisa' ? 'active' : '' }}" aria-current="page"
                href="{{ route('summary.bk_sisa') }}">Gudang Opname</a>
        </li>

    </ul>
</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>
