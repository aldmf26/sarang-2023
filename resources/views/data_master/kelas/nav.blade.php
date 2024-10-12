@php
    $rot = request()->route()->getName();
    $menuItems = [
        // ['route' => 'kelas.index', 'label' => 'Pcs Cbt', 'params' => []],
        ['route' => 'kelas.index', 'label' => 'Gr Cbt', 'params' => []],
        // ['route' => 'kelas.spesial', 'label' => 'Spesial', 'params' => []],
        ['route' => 'kelas.eo', 'label' => 'Eo', 'params' => []],
        ['route' => 'kelas.cetak', 'label' => 'Cetak', 'params' => []],
        ['route' => 'kelas.sortir', 'label' => 'Sortir', 'params' => []],
    ];
@endphp

<div class="d-flex justify-content-between">
    <div>
        <ul class="nav nav-pills float-start">
            @foreach ($menuItems as $item)
                @php
                    $isActive = $rot == $item['route'] && request()->get('jenis') == ($item['params']['jenis'] ?? '');
                @endphp
                <li class="nav-item">
                    <a class="nav-link  {{ $isActive ? 'active' : '' }}"
                        href="{{ route($item['route'], $item['params']) }}">
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div>
        <span class="btn btn-sm btn-primary" data-bs-target="#import"
                data-bs-toggle="modal"><i class="fas fa-file-excel"></i>Import</span>
            <x-theme.import title="Import paket" route="kelas.import"
                routeTemplate="kelas.template_import" />
    </div>
</div>
