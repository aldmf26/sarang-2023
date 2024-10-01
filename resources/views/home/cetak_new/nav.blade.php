<div class="col-lg-12">
    <ul class="nav nav-pills float-start">
        <li class="nav-item">
            <a class="nav-link  {{ $hal == 'cetak' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cetaknew.index', ['hal' => 'cetak', 'tgl1' => $tgl1, 'tgl2' => $tgl2]) }}">Cetak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $hal == 'cu' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cetaknew.index', ['hal' => 'cu', 'tgl1' => $tgl1, 'tgl2' => $tgl2]) }}">Cu</a>
        </li>

    </ul>
</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>
