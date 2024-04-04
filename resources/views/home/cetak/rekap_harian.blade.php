<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>

            <div class="col-lg-6">
                <a href="{{ route('cetak.export_rekap') }}" class="float-end btn btn-sm btn-primary me-2">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                <x-theme.button href="#" modal="Y" idModal="gaji_global" icon="fa-file-excel"
                    addClass="float-end" teks="Export Gaji Global" />
                <x-theme.btn_filter />
            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            @include('home.cetak.nav')

        </div>


    </x-slot>

    <x-slot name="cardBody">
        <style>
            .badge {
                cursor: pointer;
            }
        </style>

        <section class="row">
            <div class="col-lg-12 mb-2">
                <table class="float-end">
                    <tbody>
                        <tr>
                            <td>Pencarian :</td>
                            <td><input autofocus type="text" id="pencarian" class="form-control float-end"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">

                <table class="table table-bordered" id="tblAld">
                    <thead>
                        <tr>
                            <th class="dhead">No</th>
                            <th class="dhead">Nama</th>
                            <th class="dhead" width='3%'>Kelas</th>
                            <th class="dhead">Hari Kerja</th>
                            <th class="dhead">Rp Selesai</th>
                            <th class="dhead">Rata2 Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekap as $no => $r)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $r->nama }}</td>
                                <td>{{ $r->id_kelas }}</td>
                                <td>{{ $r->hari_kerja_selesai }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>

        @section('scripts')
            <script>
                pencarian('pencarian', 'tblAld')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
