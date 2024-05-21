@php
    $route = request()->route()->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gradingbj.add' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gradingbj.add') }}">8. Tambah Bj Grading Awal</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gradingbj.history_ambil' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gradingbj.history_ambil') }}">12. Grading Bj</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'gudang' || empty($kategori) ? 'active' : '' }}" aria-current="page"
            href="{{ route('packinglist.gudangKirim', ['kategori' => 'gudang']) }}">13.Bj siap kirim</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'box' ? 'active' : '' }}" aria-current="page"
            href="{{ route("packinglist.$name", ['kategori' => 'box']) }}">14. Gdng Siap Kirim</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'packing' ? 'active' : '' }}" aria-current="page"
            href="{{ route("packinglist.$name", ['kategori' => 'packing']) }}">15. Packing List</a>
    </li>

</ul>
