@php
    $rot = request()->route()->getName();

    $navMenu = [
        [
            'rot' => 'detail.cabut.cabut_awal',
            'rotAktif' => [
                'detail.cabut.cabut_awal',
                'detail.cabut.cabut_akhir',
                'detail.cabut.proses',
                'detail.cabut.sisa',
            ],
            'nama' => 'Cabut',
        ],
        [
            'rot' => 'detail.cetak.cetak_awal',
            'rotAktif' => [
                'detail.cetak.cetak_awal',
            ],
            'nama' => 'Cetak',
        ],
        [
            'rot' => 'opnamenew.sortir',
            'rotAktif' => [
                'opnamenew.cetak',
            ],
            'nama' => 'Sortir',
        ],
        [
            'rot' => 'opnamenew.grading',
            'rotAktif' => [
                'opnamenew.cetak',
            ],
            'nama' => 'Grading & Pengiriman',
        ],
    ];
@endphp
<div class="col-lg-12">
    <ul class="nav nav-pills float-start">
        @foreach ($navMenu as $d)
        <li class="nav-item">
            <a class="nav-link  {{ in_array($rot, $d['rotAktif']) ? 'active' : '' }}" aria-current="page"
                href="{{ route($d['rot']) }}">{{ $d['nama'] }}</a>
        </li>
        @endforeach
    </ul>
</div>
<div class="col-lg-12">
    <hr style="border: 1px solid black;">
</div>

