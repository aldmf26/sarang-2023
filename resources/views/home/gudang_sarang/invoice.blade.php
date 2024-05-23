<x-theme.app title="{{ $title }} " table="Y" sizeCard="7">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
                <x-theme.btn_filter />
            </div>
        </div>
       
        <ul class="nav nav-pills float-start">
            <li class="nav-item">
                <a class="nav-link {{ $route == $routeSekarang && $kategori == 'cetak' ? 'active' : '' }}" aria-current="page"
                    href="{{ route($routeSekarang) }}">Cetak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $route == $routeSekarang && $kategori == 'sortir'  ? 'active' : '' }}" aria-current="page"
                    href="{{ route($routeSekarang,['kategori' => 'sortir']) }}">Sortir</a>
            </li>


        </ul>
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table" id="nanda">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Tanggal</th>
                        <th>No Invoice</th>
                        <th>Nama Pemberi</th>
                        <th>Nama Penerima</th>
                        <th class="text-end">Pcs</th>
                        <th class="text-end">Gr</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($formulir as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                            <td>
                                <a href="{{ $kategori == 'cetak' ? route('gudangsarang.print_formulir', ['no_invoice' => $d->no_invoice]) : "/home/cetaknew/formulir/$d->no_invoice" }}"
                                    target="_blank">
                                    {{ $d->no_invoice }}
                                </a>
                            </td>
                            <td>{{ $d->pemberi }}</td>
                            <td>{{ $d->penerima }}</td>
                            <td class="text-end">{{ $d->pcs }}</td>
                            <td class="text-end">{{ $d->gr }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </section>
    </x-slot>

</x-theme.app>
