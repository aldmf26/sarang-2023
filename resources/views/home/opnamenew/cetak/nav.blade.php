@php
    $rot = request()->route()->getName();

    $navMenu = [
        [
            'rot' => 'detail.cetak.cetak_awal',
            'nama' => 'Cetak awal',
        ],
        [
            'rot' => 'detail.cetak.cetak_akhir',
            'nama' => 'Cetak akhir',
        ],
        [
            'rot' => 'detail.cetak.proses',
            'nama' => 'Sedang proses',
        ],
        [
            'rot' => 'detail.cetak.sisa',
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

