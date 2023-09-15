<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>

            <div class="col-lg-6">
                <x-theme.button modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Tambah" />
            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            <ul class="nav nav-pills float-start">
                @php
                    $rot = request()
                        ->route()
                        ->getName();
                @endphp

                <li class="nav-item">
                    <a class="nav-link {{ $rot == 'cabut.rekap' ? 'active' : '' }}" aria-current="page"
                        href="{{ route('cabut.rekap') }}">Cabut</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'cabutSpesial.rekap' ? 'active' : '' }}" href="{{ route('cabutSpesial.rekap') }}">Cabut Spesial {{ $rot }}</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'eo.rekap' ? 'active' : '' }}" href="{{ route('eo.rekap') }}">EO</a>

                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'cetak.rekap' ? 'active' : '' }}" href="{{ route('cetak.rekap') }}">Cetak</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'sortir.rekap' ? 'active' : '' }}" href="{{ route('sortir.rekap') }}">Sortir</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link {{ $rot == 'hariandll.rekap' ? 'active' : '' }}" href="{{ route('hariandll.rekap') }}">DLL</a>
                </li>
            </ul>
        </div>

    </x-slot>

    <x-slot name="cardBody">
      
        <section class="row">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="dhead text-end">No Box</th>
                        <th class="dhead text-end">Pcs Awal Bk</th>
                        <th class="dhead text-end">Gr Awal Bk</th>
                        <th class="dhead">Bulan</th>
                        <th class="dhead">Pengawas</th>
                        <th class="dhead text-end">Pcs Awal Kerja</th>
                        <th class="dhead text-end">Gr Awal Kerja</th>
                        <th class="dhead text-end">Total Rupiah</th>
                        <th class="dhead text-end">Pcs Sisa Bk</th>
                        <th class="dhead text-end">Gr Sisa Bk</th>
                    </tr>
                </thead>
            </table>
        </section>

    </x-slot>

</x-theme.app>
