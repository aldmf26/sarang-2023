<x-theme.app title="{{ $title }} " table="Y" sizeCard="9">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
                <x-theme.btn_filter />
            </div>
        </div>

        <ul class="nav nav-pills float-start">
            <li class="nav-item">
                <a class="nav-link {{ $route == $routeSekarang && $kategori == 'cetak' ? 'active' : '' }}"
                    aria-current="page" href="{{ route($routeSekarang) }}">Cetak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $route == $routeSekarang && $kategori == 'sortir' ? 'active' : '' }}"
                    aria-current="page" href="{{ route($routeSekarang, ['kategori' => 'sortir']) }}">Sortir</a>
            </li>


        </ul>
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table" id="nanda">
                <thead>
                    <tr>
                        <th class="dhead" width="5">#</th>
                        <th class="dhead">Tanggal</th>
                        <th class="dhead">No Invoice</th>
                        <th class="dhead">Nama Pemberi</th>
                        <th class="dhead">Nama Penerima</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                        <th class="dhead text-center">Aksi</th>
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
                            <td>
                                <a onclick="return confirm('Yakin dihapus ?')" href="{{ route('gudangsarang.cancel', ['kategori' => 'cetak', 'no_invoice' => $d->no_invoice]) }}">
                                    <span class="badge bg-danger">Cancel</span>
                                </a>
                                <span class="badge bg-primary">Edit</span>
                                <span class="badge bg-success">Selesai</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </section>
    </x-slot>

</x-theme.app>
