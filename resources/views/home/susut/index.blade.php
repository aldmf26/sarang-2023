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
                            <th class="text-center dhead">Selisih</th>
                            <th class="text-center dhead">Sst %</th>
                            <th class="text-center dhead">Aksi</th>
                        </tr>
                    </thead>

                    {{-- cabut --}}
                    <thead>
                        <tr class="sticky-header" >
                            <th class="dhead text-center">Cabut ke Cetak <i class="fas fa-caret-down"></i></th>
                            <th class="dhead text-center"></th>
                            <th class="dhead text-end">{{ number_format(sumCol($cabutKeCetak, 'pcs_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($cabutKeCetak, 'gr_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($cabutKeCetak, 'gr_akhir'), 0) }}</th>
                            <th class="dhead text-end">-</th>
                            <th class="dhead text-end">
                                @php
                                    $ttlSstProgram = sumCol($cabutKeCetak, 'gr_awal') - sumCol($cabutKeCetak, 'gr_akhir');
                                    $ttlSelisih = $ttlSstProgram - $tbSusut->ttl_sst_aktual;

                                @endphp
                                {{ number_format($ttlSstProgram, 0) }}
                            </th>
                            <th class="dhead text-end">{{ number_format($tbSusut->ttl_sst_aktual, 0) }}</th>
                            <th class="dhead text-end">{{ number_format($ttlSelisih, 0) }}</th>
                            <th class="dhead text-end">-</th>
                            <th class="dhead text-end"></th>
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
                                    $sstProgram = $d->gr_awal - $d->gr_akhir;
                                @endphp
                                <td align="right">{{ number_format($sstPersen, 0) }}%</td>
                                <td align="right"><a href="#" class="createAktualSusut"
                                        data-pcs_awal="{{ $d->pcs_awal }}" data-gr_awal="{{ $d->gr_awal }}"
                                        data-gr_akhir="{{ $d->gr_akhir }}" data-id_pengawas="{{ $d->id }}"
                                        data-sst_program="{{ $sstProgram }}">{{ number_format($sstProgram, 0) }}</a>
                                </td>
                                @php
                                    $getSusut = DB::table('tb_susut')->where('id_pemberi', $d->id)->first();
                                    $totalSusutAktual = 0;

                                    if ($getSusut) {
                                        $totalSusutAktual =
                                            $getSusut->rambangan_1 +
                                            $getSusut->rambangan_2 +
                                            $getSusut->rambangan_3 +
                                            $getSusut->sapuan_lantai +
                                            $getSusut->sesetan +
                                            $getSusut->bulu +
                                            $getSusut->pasir +
                                            $getSusut->rontokan_bk;
                                    }

                                    $sstPersenAktual = (1 - $totalSusutAktual / $sstProgram) * 100;
                                    $aktualNol = $totalSusutAktual == 0;

                                @endphp
                                <td align="right">{{ number_format($totalSusutAktual, 0) }}</td>
                                <td align="right">{{ number_format($aktualNol ? 0 : $sstProgram - $totalSusutAktual, 0) }}</td>
                                <td align="right">{{ number_format($sstPersenAktual, 0) }}%</td>
                                <td align="right">
                                    @if (!$aktualNol)
                                    <a href="{{ route('susut.print', $d->id) }}"
                                        target="_blank">
                                        <span class="badge bg-primary">Print</span>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    {{-- cetak --}}
                    {{-- <thead>
                        <tr class="sticky-header" @click="cetak = !cetak">
                            <th class="dhead text-center">Cetak ke Sortir <i class="fas fa-caret-down"></i></th>
                            <th class="dhead text-center"></th>
                            <th class="dhead text-end">{{ number_format(sumCol($cetakKeSortir, 'pcs_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($cetakKeSortir, 'gr_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($cetakKeSortir, 'gr_akhir'), 0) }}</th>
                            <th class="dhead text-end">-</th>
                            <th class="dhead text-end">
                                {{ number_format(sumCol($cetakKeSortir, 'gr_awal') - sumCol($cetakKeSortir, 'gr_akhir'), 0) }}
                            </th>
                            <th class="dhead text-end">{{ number_format(sumCol($cetakKeSortir, 'sst_aktual'), 0) }}
                            </th>
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
                    </tbody> --}}

                    {{-- sortir --}}
                    {{-- <thead>
                        <tr class="sticky-header" @click="sortir = !sortir">
                            <th class="dhead text-center">Sortir ke Grading <i class="fas fa-caret-down"></i></th>
                            <th class="dhead text-center"></th>
                            <th class="dhead text-end">{{ number_format(sumCol($sortirKeGrading, 'pcs_awal'), 0) }}
                            </th>
                            <th class="dhead text-end">{{ number_format(sumCol($sortirKeGrading, 'gr_awal'), 0) }}</th>
                            <th class="dhead text-end">{{ number_format(sumCol($sortirKeGrading, 'gr_akhir'), 0) }}
                            </th>
                            <th class="dhead text-end">-</th>
                            <th class="dhead text-end">
                                {{ number_format(sumCol($sortirKeGrading, 'gr_awal') - sumCol($sortirKeGrading, 'gr_akhir'), 0) }}
                            </th>
                            <th class="dhead text-end">{{ number_format(sumCol($sortirKeGrading, 'sst_aktual'), 0) }}
                            </th>
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
                    </tbody> --}}
                </table>
            </div>

        </section>
        <form action="{{ route('susut.createAktualSusut') }}" method="post">
            @csrf
            <x-theme.modal title="Detail" idModal="detail">
                <div id="createAktualSusut"></div>
            </x-theme.modal>
        </form>
        @section('scripts')
            <script>
                $(document).ready(function() {
                    $('.createAktualSusut').click(function(e) {
                        e.preventDefault();
                        const pcs_awal = $(this).data('pcs_awal');
                        const gr_awal = $(this).data('gr_awal');
                        const gr_akhir = $(this).data('gr_akhir');

                        const id_pengawas = $(this).data('id_pengawas');
                        const sst_program = $(this).data('sst_program');

                        $('#detail').modal('show');
                        $.ajax({
                            type: "GET",
                            url: "{{ route('susut.detail') }}",
                            data: {
                                id_pengawas,
                                sst_program,
                                pcs_awal,
                                gr_awal,
                                gr_akhir
                            },
                            success: function(r) {
                                $('#createAktualSusut').html(r);
                            }
                        });

                    });
                });
            </script>
        @endsection
    </x-slot>

</x-theme.app>
