<div class="col-lg-12">
    <ul class="nav nav-pills float-start">
        <li class="nav-item">
            <a class="nav-link  {{ $hal == 'cetak' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cetaknew.index', ['hal' => 'cetak']) }}">Cetak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link  {{ $hal == 'cu' ? 'active' : '' }}" aria-current="page"
                href="{{ route('cetaknew.index', ['hal' => 'cu']) }}">Cu</a>
        </li>

    </ul>
</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>
