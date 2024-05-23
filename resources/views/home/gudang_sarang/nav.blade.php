@php
    $rot = request()->route()->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link  {{ $rot == 'cabut.index' ? 'active' : '' }}" aria-current="page"
            href="{{ route('cabut.index') }}">
            Cetak
        </a>
    </li>
</ul>
