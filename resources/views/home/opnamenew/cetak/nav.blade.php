@php
    $rot = request()->route()->getName();

    $navMenu = [
        [
            'rot' => 'detail.cetak.cetak_awal',
            'nama' => 'Cetak awal',
        ],
        [
            'rot' => 'cocokan.cetak_cetakakhir',
            'nama' => 'Cetak akhir',
        ],
        [
            'rot' => 'cocokan.cetak_sedangproses',
            'nama' => 'Sedang proses',
        ],
        [
            'rot' => 'cocokan.cetak_sisa',
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
<div class="col-lg-4 mb-3">
    <a href="{{ route('cocokan.export') }}" class="btn btn-primary btn-sm float-end"><i
            class="fas fa-file-excel"></i>Export</a>
</div>
