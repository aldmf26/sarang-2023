

@php
    $route = request()->route()->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{$route == 'gradingbj.gudang_bj' ? 'active' : ''}}" aria-current="page"
            href="{{ route("gradingbj.gudang_bj") }}">Gudang Sudah Grade</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$route == 'gradingbj.index' ? 'active' : ''}}" aria-current="page"
            href="{{ route("gradingbj.index") }}">History Ambil Cetak</a>
    </li>
 
</ul>