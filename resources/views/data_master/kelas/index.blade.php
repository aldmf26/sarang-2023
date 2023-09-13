<x-theme.app title="{{ $title }}" table="Y" sizeCard="7">
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
                    <a class="nav-link  {{ $rot == 'kelas.index' ? 'active' : '' }}" aria-current="page"
                        href="{{ route('kelas.index') }}">Paket Cabut</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $rot == 'kelas.eo' ? 'active' : '' }}" aria-current="page"
                        href="{{ route('kelas.eo') }}">Paket EO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $rot == 'kelas.sortir' ? 'active' : '' }}" aria-current="page"
                        href="{{ route('kelas.sortir') }}">Paket Sortir</a>
                </li>
            </ul>
        </div>

    </x-slot>

    <x-slot name="cardBody">
      
        <section class="row">
            @include("data_master.kelas.tbl_$routeRemove")
        </section>
        


        <x-theme.btn_alert_delete route="kelas.delete" name="urutan" :tgl1="1" :tgl2="2" />

        @section('js')
            <script>
                plusRow(1, 'tbh_baris', "hariandll/tbh_baris")
                detail('edit-btn', 'id_hariandll', 'hariandll/edit_load', 'editBody')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
