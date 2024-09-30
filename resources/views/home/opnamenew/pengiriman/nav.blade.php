@php
    $rot = request()->route()->getName();

    $navMenu = [
        [
            'rot' => 'detail.pengiriman.awal',
            'nama' => 'Pengiriman',
        ],
        [
            'rot' => 'detail.pengiriman.sisa',
            'nama' => 'Sisa belum kirim',
        ],
    ];

@endphp
<div class="col-lg-8">
    <ul class="nav nav-pills float-start">
        @foreach ($navMenu as $d)
            <li class="nav-item">
                <a class="nav-link  {{ $rot == $d['rot'] ? 'pink-magenta' : '' }}" aria-current="page"
                    href="{{ route($d['rot']) }}">{{ $d['nama'] }}</a>
            </li>
        @endforeach
    </ul>
</div>
<div class="col-lg-4 mb-3">
    <a href="{{ route('cocokan.export') }}" class="btn btn-primary btn-sm float-end"><i
            class="fas fa-file-excel"></i>Export</a>
</div>
