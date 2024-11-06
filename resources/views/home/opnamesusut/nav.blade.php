@php
    $rot = request()->route()->getName();
@endphp
<div class="d-flex justify-content-between">
    <div>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.opname' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.opname') }}">Cabut</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.cetak' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.cetak') }}">Cetak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.sortir' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.sortir') }}">Sortir</a>
            </li>
        </ul>
    </div>


</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>
