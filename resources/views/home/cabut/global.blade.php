<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">

        <div class="row justify-content-end" x-data="">
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
            <ul class="bg-info nav nav-pills dhead mt-4">
                @foreach ($pengawas as $d)
                    <li class="nav-item">
                        <a class="nav-link text-white {{ $d->id_pengawas == $id_pengawas ? 'active' : '' }}"
                            aria-current="page"
                            href="{{ route('cabut.global', [
                                'bulan' => $bulan,
                                'tahun' => $tahun,
                                'id_pengawas' => $d->id_pengawas,
                            ]) }}">{{ $d->name }}</a>
                    </li>
                @endforeach

            </ul>


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

        <section class="row" x-data="{
            cabut: true,
            eo: true,
            sortir: true,
            sum: false,
            data: {{ json_encode($sumPgws) }},
            totalPerLokasi: {},
            totalPerLokasiPotongKasbon: {},
        
            init() {
                // Menghitung total per lokasi
                this.data.forEach(d => {
                    if (!this.totalPerLokasi[d.lokasi]) {
                        this.totalPerLokasi[d.lokasi] = 0;
                    }
                    this.totalPerLokasi[d.lokasi] += d.ttlRp;
                });
            },
        
            formatRupiah(value) {
                // Membulatkan nilai sebelum diformat
                value = Math.round(value);
                return new Intl.NumberFormat().format(value);
            }
        }">
            <div class="col-lg-3">
                <h6 @click="sum = !sum">Summary Gaji <span class="badge bg-primary text-white">tampilkan <i
                            class="fas fa-eye"></i></span></h6>
                <table x-transition x-show="sum" class="table table-stripped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="bg-primary text-white">Lokasi</th>
                            <th class="bg-primary text-white">Pgws</th>
                            <th class="bg-primary text-white text-end">Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sumPgws as $d)
                            <tr>
                                <td class="text-start">{{ $d['lokasi'] }}</td>
                                <td class="text-start">{{ $d['pgws'] }}</td>
                                <td class="text-end">{{ number_format($d['ttlRp']) }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                    <tfoot class="bg-info text-white">
                        <tr>
                            <th colspan="2" class="text-center">Total</th>
                            <th class="text-end">
                                <h6 class="text-white">{{ number_format(sumCol($sumPgws, 'ttlRp')) }}</h6>
                            </th>

                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-lg-2">
                <h6 x-transition x-show="sum">Summary Gaji Perlokasi </h6>
                <table x-transition x-show="sum" class="table table-stripped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="bg-primary text-white">Lokasi</th>
                            <th class="bg-primary text-white text-end">Ttl Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="Object.keys(totalPerLokasi).length > 0">
                            <template x-for="(total, lokasi) in totalPerLokasi" :key="lokasi">
                                <tr>
                                    <td x-text="lokasi" class="text-start"></td>
                                    <td x-text="formatRupiah(total)" class="text-end"></td>
                                    <td x-text="formatRupiah(total)" class="text-end"></td>
                                </tr>
                            </template>
                        </template>

                        <!-- Tampilan jika array kosong -->
                        <template x-if="Object.keys(totalPerLokasi).length === 0">
                            <tr>
                                <td colspan="2" class="text-center">Data tidak tersedia</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">
            </div>
            <div class="col-lg-6">
                <span class="me-2">Filter : </span>
                <div class="form-check form-check-inline">
                    <input x-model="cabut" type="checkbox" class="pointer form-check-input" id="cabutCheck">
                    <label class="pointer form-check-label" for="cabutCheck">Cabut</label>
                </div>
                <div class="form-check form-check-inline">
                    <input x-model="eo" type="checkbox" class="pointer form-check-input" id="eoCheck">
                    <label class="pointer form-check-label" for="eoCheck">Eo</label>
                </div>
                <div class="form-check form-check-inline">
                    <input x-model="sortir" type="checkbox" class="pointer form-check-input" id="sortirCheck">
                    <label class="pointer form-check-label" for="sortirCheck">Sortir</label>
                </div>
            </div>
            <div class="col-lg-6 mb-2">
                <table class="float-end">
                    <tbody>
                        <tr>
                            <td>Pencarian :</td>
                            <td><input autofocus type="text" id="pencarian" class="form-control float-end"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <style>
                tr td {
                    text-align: right
                }
            </style>
            <div class="col-lg-12">
                <table id="tblAldi" class="table table-stripped table-bordered table-responsive">
                    @php
                        $bgDanger = 'text-white bg-danger';
                        // $buka = "<span class='badge bg-secondary float-end'>Buka <i class='fas fa-caret-down'></i></span>";
                        $buka = '';
                    @endphp
                    <thead>

                        <tr>
                            <th class="text-center " colspan="4">#</th>
                            <th @click="cabut = ! cabut" :class="{ 'd-none': !cabut }"
                                class="text-center text-white bg-info"
                                :colspan="cabut ? '8' : ''" ">
                                Cabut
                                {!! $buka !!}
                            </th>
                            <th @click="eo = ! eo" :class="{ 'd-none': !eo }" class="text-center text-white bg-success" :colspan="eo ? '4' : ''" ">
                                Cabut Eo
                                {!! $buka !!}
                            </th>
                            <th @click="sortir= ! sortir" :class="{ 'd-none': !sortir }"
                                class="text-center text-white bg-primary" :colspan="sortir ? '6' : ''" " >
                                Sortir {!! $buka !!}
                            </th>
                            <th class="text-center {{ $bgDanger }}" colspan="7">Gajih</th>
                        </tr>
                        <tr>
                            <th class="dhead">Pgws</th>
                            <th class="dhead">Hari Masuk</th>
                            <th class="dhead">Nama Karyawan</th>
                            <th class="dhead">Kelas</th>

                            <th x-show="cabut" class="dhead">Pcs Awal Cbt</th>
                            <th x-show="cabut" class="dhead">Gr Awal</th>
                            <th x-show="cabut" class="dhead">Pcs Akhir</th>
                            <th x-show="cabut" class="dhead">Gr Akhir</th>
                            <th x-show="cabut" class="dhead">Eot Gr</th>
                            <th x-show="cabut" class="dhead">Gr Flx</th>
                            <th x-show="cabut" class="dhead">Susut %</th>
                            <th x-show="cabut" class="{{ $bgDanger }}">Ttl Rp</th>

                            <th x-show="eo" class="dhead">Gr Eo Awal</th>
                            <th x-show="eo" class="dhead">Gr Eo Akhir</th>
                            <th x-show="eo" class="dhead">Susut %</th>
                            <th x-show="eo" class="{{ $bgDanger }}">Ttl Rp</th>

                            <th x-show="sortir" class="dhead">Pcs Awal Srt</th>
                            <th x-show="sortir" class="dhead">Gr Awal</th>
                            <th x-show="sortir" class="dhead">Pcs Akhir</th>
                            <th x-show="sortir" class="dhead">Gr Akhir</th>
                            <th x-show="sortir" class="dhead">Susut %</th>
                            <th x-show="sortir" class="{{ $bgDanger }}">Ttl Rp</th>

                            <th class="dhead">Kerja Dll</th>
                            <th class="dhead">Uang Makan</th>
                            <th class="dhead">Rp Denda</th>
                            <th class="{{ $bgDanger }}">Ttl Gaji</th>
                            <th class="dhead">Rata2</th>
                            <th class="dhead">Kasbon</th>
                            <th class="{{ $bgDanger }}">Sisa Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php

                            $TtlRp = 0;
                            $eoTtlRp = 0;
                            $sortirTtlRp = 0;
                            $dllTtlRp = 0;
                            $dendaTtlRp = 0;
                            $ttlTtlRp = 0;

                            $ttlCbtPcsAwal = 0;
                            $ttlCbtGrAwal = 0;
                            $ttlCbtPcsAkhir = 0;
                            $ttlCbtGrAkhir = 0;
                            $ttlCbtEotGr = 0;
                            $ttlCbtGrFlx = 0;
                            $ttlCbtTtlRp = 0;

                            $ttlSoritrPcsAwal = 0;
                            $ttlSoritrGrAwal = 0;
                            $ttlSoritrPcsAkhir = 0;
                            $ttlSoritrGrAkhir = 0;
                            $ttlSortirRp = 0;

                            $ttlEoGrAwal = 0;
                            $ttlEoGrAkhir = 0;
                            $ttlEoRp = 0;
                            $ttlUangMakan = 0;

                            $ttlKasbon = 0;
                            $ttlSisaGaji = 0;

                        @endphp
                                             @foreach ($tbl as $data)
                        <tr>
                            <td>{{ $data->pgws }}</td>
                            <td>{{ $data->hariMasuk }}</td>
                            <td>{{ $data->nm_anak }}</td>
                            <td>{{ $data->kelas }}</td>

                            <td x-show="cabut">{{ number_format($data->pcs_awal, 0) }}</td>
                            <td x-show="cabut">{{ number_format($data->gr_awal, 0) }}</td>
                            <td x-show="cabut">{{ number_format($data->pcs_akhir, 0) }}</td>
                            <td x-show="cabut">{{ number_format($data->gr_akhir, 0) }}</td>
                            <td x-show="cabut">{{ number_format($data->eot, 0) }}</td>
                            <td x-show="cabut">{{ number_format($data->gr_flx, 0) }}</td>
                            @php
                                $susutCbt = empty($data->gr_akhir)
                                    ? 0
                                    : (1 - ($data->gr_akhir + $data->gr_flx) / $data->gr_awal) * 100;
                            @endphp
                            <td x-show="cabut">{{ number_format($susutCbt, 0) }}</td>
                            <td x-show="cabut">{{ number_format($data->ttl_rp, 0) }}</td>


                            <td x-show="eo">{{ number_format($data->eo_awal, 0) }}</td>
                            <td x-show="eo">{{ number_format($data->eo_akhir, 0) }}</td>
                            @php
                                $susutEo = empty($data->eo_akhir) ? 0 : (1 - $data->eo_akhir / $data->eo_awal) * 100;
                            @endphp
                            <td x-show="eo">{{ number_format($susutEo, 0) }}</td>
                            <td x-show="eo">{{ number_format($data->eo_ttl_rp, 0) }}</td>


                            <td x-show="sortir">{{ number_format($data->sortir_pcs_awal, 0) }}</td>
                            <td x-show="sortir">{{ number_format($data->sortir_gr_awal, 0) }}</td>
                            <td x-show="sortir">{{ number_format($data->sortir_pcs_akhir, 0) }}</td>
                            <td x-show="sortir">{{ number_format($data->sortir_gr_akhir, 0) }}</td>
                            @php
                                $susutSortir = empty($data->sortir_gr_akhir)
                                    ? 0
                                    : (1 - $data->sortir_gr_akhir / $data->sortir_gr_awal) * 100;
                            @endphp
                            <td x-show="sortir">{{ number_format($susutSortir, 0) }}</td>
                            <td x-show="sortir">{{ number_format($data->sortir_ttl_rp, 0) }}</td>


                            <td>{{ number_format($data->ttl_rp_dll, 0) }}</td>
                            <td>{{ number_format(empty($data->umk_nominal) ? 0 : $data->umk_nominal * $data->hariMasuk, 0) }}
                            </td>
                            <td>{{ number_format($data->ttl_rp_denda, 0) }}</td>
                            @php
                                $uang_makan = empty($data->umk_nominal) ? 0 : $data->umk_nominal * $data->hariMasuk;
                                $ttl =
                                    $data->ttl_rp +
                                    $data->eo_ttl_rp +
                                    $data->sortir_ttl_rp +
                                    $uang_makan +
                                    $data->ttl_rp_dll -
                                    $data->ttl_rp_denda;
                                $rata = empty($data->hariMasuk) ? 0 : $ttl / $data->hariMasuk;

                                $kasbon = $data->kasbon;
                                $sisaGaji = $ttl - $kasbon;

                            @endphp
                            <td>{{ number_format($ttl, 0) }}</td>
                            <td>{{ number_format($rata, 0) }}</td>
                            <td>{{ number_format($kasbon, 0) }}</td>
                            <td>{{ number_format($sisaGaji, 0) }}</td>
                        </tr>

                        @php
                            $ttlCbtPcsAwal += $data->pcs_awal;
                            $ttlCbtGrAwal += $data->gr_awal;
                            $ttlCbtPcsAkhir += $data->pcs_akhir;
                            $ttlCbtGrAkhir += $data->gr_akhir;
                            $ttlCbtEotGr += $data->eot;
                            $ttlCbtGrFlx += $data->gr_flx;
                            $ttlCbtTtlRp += $data->ttl_rp;

                            $ttlEoGrAwal += $data->eo_awal;
                            $ttlEoGrAkhir += $data->eo_akhir;
                            $ttlEoRp += $data->eo_ttl_rp;

                            $ttlSoritrPcsAwal += $data->sortir_pcs_awal;
                            $ttlSoritrGrAwal += $data->sortir_gr_awal;
                            $ttlSoritrPcsAkhir += $data->sortir_pcs_akhir;
                            $ttlSoritrGrAkhir += $data->sortir_gr_akhir;
                            $ttlSortirRp += $data->sortir_ttl_rp;

                            $TtlRp += $data->ttl_rp;
                            $eoTtlRp += $data->eo_ttl_rp;
                            $sortirTtlRp += $data->sortir_ttl_rp;
                            $dllTtlRp += $data->ttl_rp_dll;
                            $dendaTtlRp += $data->ttl_rp_denda;
                            $ttlUangMakan += $uang_makan;

                            $ttlTtlRp += $ttl;

                            $ttlKasbon += $kasbon;
                            $ttlSisaGaji += $sisaGaji;
                        @endphp
                        @endforeach
                        </tbody>
                    <tfoot class="bg-info text-white">
                        <tr>
                            <th>TOTAL</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th x-show="cabut">{{ number_format($ttlCbtPcsAwal, 0) }}</th>
                            <th x-show="cabut">{{ number_format($ttlCbtGrAwal, 0) }}</th>
                            <th x-show="cabut">{{ number_format($ttlCbtPcsAkhir, 0) }}</th>
                            <th x-show="cabut">{{ number_format($ttlCbtGrAkhir, 0) }}</th>
                            <th x-show="cabut">{{ number_format($ttlCbtEotGr, 0) }}</th>
                            <th x-show="cabut">{{ number_format($ttlCbtGrFlx, 0) }}</th>
                            <th x-show="cabut"></th>
                            <th x-show="cabut">{{ number_format($ttlCbtTtlRp, 0) }}</th>

                            <th x-show="eo">{{ number_format($ttlEoGrAwal, 0) }}</th>
                            <th x-show="eo">{{ number_format($ttlEoGrAkhir, 0) }}</th>
                            <th x-show="eo"></th>
                            <th x-show="eo">{{ number_format($ttlEoRp, 0) }}</th>

                            <th x-show="sortir">{{ number_format($ttlSoritrPcsAwal, 0) }}</th>
                            <th x-show="sortir">{{ number_format($ttlSoritrGrAwal, 0) }}</th>
                            <th x-show="sortir">{{ number_format($ttlSoritrPcsAkhir, 0) }}</th>
                            <th x-show="sortir">{{ number_format($ttlSoritrGrAkhir, 0) }}</th>
                            <th x-show="sortir"></th>
                            <th x-show="sortir">{{ number_format($ttlSortirRp, 0) }}</th>

                            <th>{{ number_format($dllTtlRp, 0) }}</th>
                            <th>{{ number_format($ttlUangMakan, 0) }}</th>
                            <th>{{ number_format($dendaTtlRp, 0) }}</th>
                            <th>{{ number_format($ttlTtlRp, 0) }}</th>

                            <th></th>
                            <th>{{ number_format($ttlKasbon, 0) }}</th>
                            <th>{{ number_format($ttlSisaGaji, 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </section>

        @section('scripts')
            <script>
                pencarian('pencarian', 'tblAldi')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
