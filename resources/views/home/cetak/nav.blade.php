<ul class="nav nav-pills float-start">
    @php
        $rot = request()
            ->route()
            ->getName();
        $navMenu = [
            [
                'rot' => 'cabut.rekap',
                'nama' => 'Cabut',
            ],
            // [
            //     'rot' => 'cabutSpesial.rekap',
            //     'nama' => 'Cabut Spesial',
            // ],
            [
                'rot' => 'eo.rekap',
                'nama' => 'EO',
            ],
            [
                'rot' => 'cetak.rekap',
                'nama' => 'Cetak',
            ],
            [
                'rot' => 'sortir.rekap',
                'nama' => 'Sortir',
            ],
            [
                'rot' => 'hariandll.rekap',
                'nama' => 'DLL',
            ],
            [
                'rot' => 'cabut.global',
                'nama' => 'Global',
            ],
            [
                'rot' => 'susut.index',
                'nama' => 'Cek Susut',
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
