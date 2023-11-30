<ul class="nav nav-pills float-start">
    @php
        $rot = request()
            ->route()
            ->getName();
        $navMenu = [
            [
                'rot' => 'global.index',
                'nama' => 'Cabut',
            ],
            [
                'rot' => 'global.cetak',
                'nama' => 'Cetak',
            ],
            [
                'rot' => 'global.sortir',
                'nama' => 'Sortir',
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
