@php
    $namaRoute = request()->route()->getName();
    $routes = [
        'gradingbj.index' => [
            'route' => 'gradingbj.index',
            'teks' => 'Grading',
            'variant' => 'info',
        ],
        // 'gradingbj.cek_opname' => [
        //     'route' => 'gradingbj.cek_opname',
        //     'teks' => 'Cek Opname',
        //     'variant' => 'warning',
        // ],

        'gradingbj.gudang_siap_kirim' => [
            'route' => 'gradingbj.gudang_siap_kirim',
            'teks' => 'Wip1',
            'variant' => 'info',
        ],
        'qc.index' => [
            'route' => 'qc.index',
            'teks' => 'QC',
            'variant' => 'info',
        ],
        'gradingbj.gudang_siap_kirim2' => [
            'route' => 'gradingbj.gudang_siap_kirim2',
            'teks' => 'Wip2',
            'variant' => 'info',
        ],
        'pengiriman.list_po' => [
            'route' => 'pengiriman.list_po',
            'teks' => 'List Po Pengiriman',
            'variant' => 'info',
        ],
        // 'pengiriman.index' => [
        //     'route' => 'pengiriman.index',
        //     'teks' => 'Box kirim',
        // ],
        // 'packinglist.index' => [
        //     'route' => 'packinglist.index',
        //     'teks' => 'Packinglist',
        // ],
        'packinglist.pengiriman' => [
            'route' => 'packinglist.pengiriman',
            'teks' => 'History Pengiriman',
            'variant' => 'info',
        ],
    ];
@endphp

@foreach ($routes as $key => $route)
    {{-- @if ($namaRoute != $key) --}}
    <x-theme.button variant="{{ $namaRoute == $route['route'] ? 'primary' : $route['variant'] }}"
        href="{{ route($route['route']) }}" icon="{{ $route['icon'] ?? 'fa-warehouse' }}" teks="{{ $route['teks'] }}" />
    {{-- @endif --}}
@endforeach
<h6 class="mt-2" style="margin-bottom: -8px">{{ $title }}</h6>
