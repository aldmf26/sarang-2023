@php
    $rot = request()->route()->getName();

    $navMenu = [
        [
            'rot' => 'detail.cabut.cabut_awal',
            'nama' => 'Cabut awal',
        ],
        [
            'rot' => 'detail.cabut.cabut_akhir',
            'nama' => 'Cabut akhir',
        ],
        [
            'rot' => 'detail.cabut.proses',
            'nama' => 'Sedang proses',
        ],
        [
            'rot' => 'detail.cabut.sisa',
            'nama' => 'Sisa pengawas',
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

