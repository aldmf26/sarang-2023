@php
    $rot = request()->route()->getName();

    $navMenu = [
        [
            'rot' => 'detail.grading.awal',
            'nama' => 'Grading awal',
        ],
        [
            'rot' => 'detail.grading.sisa',
            'nama' => 'Grading Sisa',
        ],
        [
            'rot' => 'detail.grading.akhir',
            'nama' => 'Grading Akhir',
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

