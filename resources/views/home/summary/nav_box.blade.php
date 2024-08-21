@php
    $rot = request()->route()->getName();

@endphp
<style>
    .nav-item.active {
        background-color: #F7914D;
        color: white !important;
    }

    .nav-link {
        display: block;
        padding: .5rem 1rem;
        color: #0d6efd;
        text-decoration: none;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out;
    }
</style>
<div class="col-lg-12">
    <ul class="nav nav-pills float-start">
        <li class="nav-item {{ $rot == 'summary.history_partai' ? 'active' : '' }}">
            <a class="nav-link" aria-current="page"
                href="{{ route('summary.history_partai', ['nm_partai' => $nm_partai]) }}">Summary Partai</a>
        </li>
        <li class="nav-item {{ $rot == 'summary.detail_box' ? 'active' : '' }}">
            <a class="nav-link" aria-current="page"
                href="{{ route('summary.detail_box', ['nm_partai' => $nm_partai]) }}">Perbox</a>
        </li>

    </ul>
</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>
