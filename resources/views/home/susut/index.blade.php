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
                <table class="border-dark table table-bordered table-striped" id="tblAld" x-data="{
                    openRows: [],
                    cabut: true,
                    cetak: false,
                    sortir: false,
                }">
                    <thead>
                        <tr class="sticky-header">
                            <th class="text-center dhead">Divisi</th>
                            <th class="text-center dhead">Pgws</th>
                            <th class="text-center dhead">Pcs Awal</th>
                            <th class="text-center dhead">Gr Awal</th>
                            <th class="text-center dhead">Gr Akhir</th>
                            <th class="text-center dhead">Sst %</th>
                            <th class="text-center dhead">Sst Program</th>
                            <th class="text-center dhead">Sst Aktual</th>
                        </tr>
                    </thead>

                    {{-- cabut --}}
                    <thead>
                        <tr class="sticky-header" @click="cabut = !cabut">
                            <th class="dhead text-center" >Cabut ke Cetak <i class="fas fa-caret-down"></i></th>
                            <th class="dhead text-center"></th>
                            <th class="dhead text-end">{{ number_format(sumCol($cabutKeCetak, 'pcs_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($cabutKeCetak, 'gr_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($cabutKeCetak, 'gr_akhir'), 0) }}</th>
                            <th class="dhead text-end">-</th>
                            <th class="dhead text-end">
                                {{ number_format(sumCol($cabutKeCetak, 'gr_awal') - sumCol($cabutKeCetak, 'gr_akhir'), 0) }}
                            </th>
                            <th class="dhead text-end">{{ number_format(sumCol($cabutKeCetak, 'sst_aktual'), 0) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach ($cabutKeCetak as $d)
                                <tr x-show="cabut">
                                    <td></td>
                                    <td>{{ $d->name }}</td>
                                    <td align="right">{{ number_format($d->pcs_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_akhir, 0) }}</td>
                                    @php
                                        $sstPersen = (1 - $d->gr_akhir / $d->gr_awal) * 100;
                                    @endphp
                                    <td align="right">{{ number_format($sstPersen, 0) }}%</td>
                                    <td align="right">{{ number_format($d->gr_awal - $d->gr_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($d->sst_aktual, 0) }}</td>
                                </tr>
                            @endforeach
                    </tbody>

                    {{-- cetak --}}
                    <thead>
                        <tr class="sticky-header" @click="cetak = !cetak">
                            <th class="dhead text-center" >Cetak ke Sortir <i class="fas fa-caret-down"></i></th>
                            <th class="dhead text-center"></th>
                            <th class="dhead text-end">{{ number_format(sumCol($cetakKeSortir, 'pcs_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($cetakKeSortir, 'gr_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($cetakKeSortir, 'gr_akhir'), 0) }}</th>
                            <th class="dhead text-end">-</th>
                            <th class="dhead text-end">
                                {{ number_format(sumCol($cetakKeSortir, 'gr_awal') - sumCol($cetakKeSortir, 'gr_akhir'), 0) }}
                            </th>
                            <th class="dhead text-end">{{ number_format(sumCol($cetakKeSortir, 'sst_aktual'), 0) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach ($cetakKeSortir as $d)
                                <tr x-show="cetak">
                                    <td></td>
                                    <td>{{ $d->name }}</td>
                                    <td align="right">{{ number_format($d->pcs_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_akhir, 0) }}</td>
                                    @php
                                        $sstPersen = (1 - $d->gr_akhir / $d->gr_awal) * 100;
                                    @endphp
                                    <td align="right">{{ number_format($sstPersen, 0) }}%</td>
                                    <td align="right">{{ number_format($d->gr_awal - $d->gr_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($d->sst_aktual, 0) }}</td>
                                </tr>
                            @endforeach
                    </tbody>

                    {{-- sortir --}}
                    <thead>
                        <tr class="sticky-header" @click="sortir = !sortir">
                            <th class="dhead text-center" >Sortir ke Grading <i class="fas fa-caret-down"></i></th>
                            <th class="dhead text-center"></th>
                            <th class="dhead text-end">{{ number_format(sumCol($sortirKeGrading, 'pcs_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($sortirKeGrading, 'gr_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($sortirKeGrading, 'gr_akhir'), 0) }}</th>
                            <th class="dhead text-end">-</th>
                            <th class="dhead text-end">
                                {{ number_format(sumCol($sortirKeGrading, 'gr_awal') - sumCol($sortirKeGrading, 'gr_akhir'), 0) }}
                            </th>
                            <th class="dhead text-end">{{ number_format(sumCol($sortirKeGrading, 'sst_aktual'), 0) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach ($sortirKeGrading as $d)
                                <tr x-show="sortir">
                                    <td></td>
                                    <td>{{ $d->name }}</td>
                                    <td align="right">{{ number_format($d->pcs_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_awal, 0) }}</td>
                                    <td align="right">{{ number_format($d->gr_akhir, 0) }}</td>
                                    @php
                                        $sstPersen = $d->gr_awal == 0 ? 0 : (1 - $d->gr_akhir / $d->gr_awal) * 100;
                                    @endphp
                                    <td align="right">{{ number_format($sstPersen, 0) }}%</td>
                                    <td align="right">{{ number_format($d->gr_awal - $d->gr_akhir, 0) }}</td>
                                    <td align="right">{{ number_format($d->sst_aktual, 0) }}</td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>

        </section>
    
    </x-slot>

</x-theme.app>
