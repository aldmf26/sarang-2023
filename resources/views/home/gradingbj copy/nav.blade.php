@php
    $route = request()->route()->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gradingbj.gudang_bj' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gradingbj.gudang_bj') }}">9.Bj Grading Awal</a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link {{ $route == 'gradingbj.index' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gradingbj.index') }}">History Ambil Cetak</a>
    </li> --}}
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gradingbj.history_box_kecil' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gradingbj.history_box_kecil') }}">10.Box Sp</a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link {{ $route == 'gradingbj.gudang_bahan_jadi' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gradingbj.gudang_bahan_jadi') }}">Gudang siap kirim</a>
    </li> --}}

</ul>
