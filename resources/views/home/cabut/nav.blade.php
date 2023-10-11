@php
    $rot = request()
        ->route()
        ->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link  {{ $rot == 'cabut.index' ? 'active' : '' }}"
            aria-current="page" href="{{ route('cabut.index') }}">Cabut</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'cabutSpesial.index' ? 'active' : '' }}"
            aria-current="page" href="{{ route('cabutSpesial.index') }}">Spesial</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $rot == 'eo.index' ? 'active' : '' }}" aria-current="page"
            href="{{ route('eo.index') }}">Eo</a>
    </li>
</ul>
