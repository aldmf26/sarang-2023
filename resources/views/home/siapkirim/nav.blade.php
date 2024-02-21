@php
    $route = request()->route()->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{ $route == 'siapkirim.add' ? 'active' : '' }}" aria-current="page"
            href="{{ route('siapkirim.add') }}">Siap Kirim</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'siapkirim.index' ? 'active' : '' }}" aria-current="page"
            href="{{ route('siapkirim.index') }}">History Siap Kirim</a>
    </li>
</ul>
