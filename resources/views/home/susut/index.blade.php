<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">

        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}
                    {{ date('M Y', strtotime('01-' . $bulan . '-' . date('Y', strtotime($tahun)))) }}
                    <span class="text-warning" style="font-size: 12px"><em>jika data tidak ada silahkan view dulu
                            !</em></span>
                </h6>
            </div>

            <div class="col-lg-6">
                @include('home.cabut.btn_export_global')
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

            thead {
                position: sticky;
                top: 0;
                background-color: #f1f1f1;
                /* Warna latar belakang header yang tetap */
                z-index: 1;
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
                <style>
                    .sticky-header th {
                        position: sticky;
                        top: 0;
                        background-color: #f2f2f2;
                        /* Add a background color to distinguish the header */
                        z-index: 100;
                        /* Ensure the header stays on top of other elements */
                    }
                </style>
                <table  class="border-dark table table-bordered" id="tblAld"
                    x-data="{
                        openRows: [],
                    }">
                    <thead>
                        <tr class="sticky-header">
                            <th width="85" class="text-center dhead">Divisi</th>
                            <th width="185" class="text-center dhead">Pgws</th>
                            <th width="85" class="text-center dhead">Pcs</th>
                            <th width="85" class="text-center dhead">Gr</th>
                            <th width="85" class="text-center dhead">Gr Akhir</th>
                            <th width="85" class="text-center dhead">Sst %</th>
                            <th width="85" class="text-center dhead">Sst Program</th>
                            <th width="85" class="text-center dhead">Sst Aktual</th>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="sticky-header">
                            <th class="dhead text-center">Cabut</th>
                            <th class="dhead text-center"></th>
                            <th class="dhead text-end">80</th>
                            <th class="dhead text-end">1000</th>
                            <th class="dhead text-end">80</th>
                            <th class="dhead text-end">2%</th>
                            <th class="dhead text-end">200</th>
                            <th class="dhead text-end">180</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </section>

    </x-slot>

</x-theme.app>
