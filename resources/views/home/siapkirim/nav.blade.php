@php
    $route = request()->route()->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{ $route == 'siapkirim.add' ? 'active' : '' }}" aria-current="page"
            href="{{ route('siapkirim.add') }}">12. Gudang Sp Selesai</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'siapkirim.ambilsortir' ? 'active' : '' }}" aria-current="page"
            href="{{ route('siapkirim.ambilsortir') }}">13. BJ Siap Kirim</a>
    </li>
</ul>
