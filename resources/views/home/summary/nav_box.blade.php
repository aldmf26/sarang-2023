@php
    $rot = request()->route()->getName();

@endphp
<style>
    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        color: #fff !important;
        background-color: #F7914D !important;
    }

    .nav-link {
        display: block;
        padding: .5rem 1rem;
        color: #0d6efd;
        text-decoration: none;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out;
    }

    @media print {
        .print_hilang {
            display: none;
        }

        .section {
            page-break-after: always;
        }
    }
</style>
<div class="col-lg-12 print_hilang">
    <ul class="nav nav-pills float-start">
        <li class="nav-item ">
            <a class="nav-link {{ $rot == 'summary.history_partai' ? 'active' : '' }}" aria-current="page"
                href="{{ route('summary.history_partai', ['nm_partai' => $nm_partai]) }}">Summary Partai</a>
        </li>
        <li class="nav-item ">
            <a class="nav-link {{ $rot == 'summary.detail_box' ? 'active' : '' }}" aria-current="page"
                href="{{ route('summary.detail_box', ['nm_partai' => $nm_partai]) }}">Perbox</a>
        </li>

    </ul>
</div>
<div class="col-lg-12 print_hilang">
    <hr style="border: 1px solid black;">
</div>
<div class="col-lg-10 print_hilang"></div>
<div class="col-lg-2 print_hilang"><a onclick="window.print()" href="#"
        class="btn btn-sm btn-primary float-end print" {{ $rot == 'summary.detail_box' ? 'hidden' : '' }}><i
            class="fas fa-print"></i> Print</a></div>
