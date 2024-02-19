<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{$kategori == 'box' || empty($kategori) ? 'active' : ''}}" aria-current="page"
            href="{{ route("packinglist.$name", ['kategori' => 'box']) }}">History Box Kirim</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$kategori == 'packing' ? 'active' : ''}}" aria-current="page"
            href="{{ route("packinglist.$name", ['kategori' => 'packing']) }}">History Packing</a>
    </li>
 
</ul>