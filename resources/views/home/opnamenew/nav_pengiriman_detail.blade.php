@php
    $rot = request()->route()->getName();
    $reqPer = request('per') ?? 'partai';
    $no_nota = request('no_nota');

    $navMenu = [
        [
            'rot' => 'detail.list_pengiriman',
            'active' => 'partai',
            'nama' => 'Partai',
        ],
        [
            'rot' => 'detail.list_pengiriman',
            'active' => 'no_box',
            'nama' => 'Box Grading',
        ],
    ];
@endphp
<div class="col-lg-12">
    <ul class="nav nav-pills float-start">
        @foreach ($navMenu as $d)
            @php
                $active = $d['active'];
            @endphp
            <li class="nav-item">
                <a class="nav-link  {{ $reqPer == $active ? 'active' : '' }}" aria-current="page"
                    href="{{ route($d['rot'], ['no_nota' => $no_nota, 'per' => $active]) }}">{{ $d['nama'] }}</a>
            </li>
        @endforeach
    </ul>
</div>
