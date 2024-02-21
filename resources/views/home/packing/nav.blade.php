<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'gudang' || empty($kategori) ? 'active' : '' }}" aria-current="page"
            href="{{ route('packinglist.gudangKirim', ['kategori' => 'gudang']) }}">Gudang siap kirim</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'box' ? 'active' : '' }}" aria-current="page"
            href="{{ route("packinglist.$name", ['kategori' => 'box']) }}">History Box Kirim</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $kategori == 'packing' ? 'active' : '' }}" aria-current="page"
            href="{{ route("packinglist.$name", ['kategori' => 'packing']) }}">History Packing</a>
    </li>

</ul>
