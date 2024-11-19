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
                <a class="nav-link  {{ $rot == 'cocokan.opname/cetak' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.opname/cetak') }}">Cetak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ $rot == 'cocokan.opname/sortir' ? 'active' : '' }}" aria-current="page"
                    href="{{ route('cocokan.opname/sortir') }}">Sortir</a>
            </li>
        </ul>
    </div>


</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>
