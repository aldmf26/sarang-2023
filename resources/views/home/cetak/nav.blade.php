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
        ];

    @endphp
    @foreach ($navMenu as $d)
        <li class="nav-item">
            <a class="nav-link {{ $rot == $d['rot'] ? 'active' : '' }}" aria-current="page"
                href="{{ route($d['rot']) }}">{{ $d['nama'] }}</a>
        </li>
    @endforeach
</ul>
