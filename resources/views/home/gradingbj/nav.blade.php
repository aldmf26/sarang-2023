
@php
    $namaRoute = request()->route()->getName();
    $routes = [
        'gradingbj.index' => [
            'route' => 'gradingbj.index',
            'teks' => 'Grading',
        ],
        'gradingbj.gudang_siap_kirim' => [
            'route' => 'gradingbj.gudang_siap_kirim',
            'teks' => 'Gudang Siap Kirim',
        ],
        'pengiriman.add' => [
            'route' => 'pengiriman.add',
            'teks' => 'Buat box kirim',
            'icon' => 'fa-box-open',
        ],
        'pengiriman.index' => [
            'route' => 'pengiriman.index',
            'teks' => 'Box kirim',
        ],
        'packinglist.index' => [
            'route' => 'packinglist.index',
            'teks' => 'Packinglist',
        ],
        'packinglist.pengiriman' => [
            'route' => 'packinglist.pengiriman',
            'teks' => 'Pengiriman',
        ],
    ];
@endphp

@foreach ($routes as $key => $route)
    {{-- @if ($namaRoute != $key) --}}
    <x-theme.button variant="{{ $namaRoute == $key ? 'info' : 'primary' }}" href="{{ route($route['route']) }}"
        icon="{{ $route['icon'] ?? 'fa-warehouse' }}" teks="{{ $route['teks'] }}" />
    {{-- @endif --}}
@endforeach
<h6 class="mt-2" style="margin-bottom: -8px">{{ $title }}</h6>
