<ul class="nav nav-pills float-start">
    @php
        $rot = request()
            ->route()
            ->getName();
        $navMenu = [
            [
                'rot' => 'gudang.sum',
                'nama' => 'Cabut',
            ],
            // [
            //     'rot' => 'cabutSpesial.rekap',
            //     'nama' => 'Cabut Spesial',
            // ],
            [
                'rot' => 'gudang.sum_cetak',
                'nama' => 'Cetak',
            ],
            [
                'rot' => 'gudang.sum_sortir',
                'nama' => 'Sortir',
            ],
            
        ];

    @endphp
    @foreach ($navMenu as $d)
        <li class="nav-item">
            <a class="nav-link {{ $rot == $d['rot'] ? 'active' : '' }}" aria-current="page"
                href="{{ route($d['rot'], ['bulan' => request()->get('bulan'), 'tahun' => request()->get('tahun')]) }}">{{ $d['nama'] }}</a>
        </li>
    @endforeach
</ul>