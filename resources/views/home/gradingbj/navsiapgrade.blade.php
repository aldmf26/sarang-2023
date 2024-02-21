@php
    $route = request()->route()->getName();
@endphp
<ul class="nav nav-pills float-start">
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gradingbj.add' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gradingbj.add') }}">Siap Grade</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $route == 'gradingbj.history_ambil' ? 'active' : '' }}" aria-current="page"
            href="{{ route('gradingbj.history_ambil') }}">History Siap Grade</a>
    </li>
</ul>
