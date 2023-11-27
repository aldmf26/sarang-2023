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
        <form action="{{ route('cetak.export_gaji_global') }}" method="get" enctype="multipart/form-data">
            <x-theme.modal title="Gaji Global Anak" idModal="gaji_global" btnSave="Y">
                <div class="row">
                    <div class="col-lg-6">
                        <select name="bulan" id="" class="select2_add">
                            <option value="">Pilih Bulan</option>
                            @foreach ($bulan as $b)
                                <option value="{{ $b->bulan }}">{{ $b->nm_bulan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <select name="tahun" id="" class="select2_add">
                            <option value="">Pilih Tahun</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t->tahun }}">{{ $t->tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </x-theme.modal>
        </form>

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

                <table style="border:1px solid #97a1c3" class="table table-bordered" id="tblAld"
                    x-data="{
                        openRows: [],
                    }">
                    <thead>
                        <tr>
                            <th width="150" rowspan="2" class="text-center dhead">Pengawas</th>
                            <th width="85" rowspan="2" class="text-center dhead">No Box</th>
                            <th colspan="2" class="text-center dhead">BK Awal</th>
                            <th colspan="4" class="text-center dhead"> Kerja Awal</th>
                            <th colspan="5" class="text-center dhead"> Kerja Akhir</th>
                            <th width="100" rowspan="2" class="text-center dhead">Ttl Rp <br> (-)</th>
                            <th width="2%" class="text-center dhead" colspan="2">BK Sisa</th>
                        </tr>
                        <tr>
                            <th class="dhead text-center">Pcs <br> (-)</th>
                            <th class="dhead text-center">Gr <br> (-)</th>
                            <th class="dhead text-center">Pcs tdk ctk <br> (-)</th>
                            <th class="dhead text-center">Gr tdk ctk<br> (-)</th>
                            <th class="dhead text-center">Pcs <br> (-)</th>
                            <th class="dhead text-center">Gr<br> (-)</th>
                            <th class="dhead text-center">Pcs <br> (-)</th>
                            <th class="dhead text-center">Gr <br> (-)</th>
                            <th class="dhead text-center">Pcs Cu <br> (-)</th>
                            <th class="dhead text-center">Gr Cu <br> (-)</th>
                            <th class="dhead text-center">Susut </th>
                            <th class="dhead text-center">Pcs </th>
                            <th class="dhead text-center">Gr</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($cetakgroup as $i => $c)
                            <tr>
                                <th>{{ $c->name }} <span class="badge bg-primary float-end"
                                        x-on:click="openRows.includes({{ $i }}) ? openRows = openRows.filter(item => item !== {{ $i }}) : openRows.push({{ $i }})">Buka
                                        <i class="fas fa-caret-down"></i></span></th>
                                <th>Ttl Box :{{ $c->total_bk }}</th>
                                <th class="text-end">{{ $c->pcs_bk }}</th>
                                <th class="text-end">{{ $c->gr_bk }}</th>
                                <th class="text-end">{{ $c->pcs_tdk_ctk }}</th>
                                <th class="text-end">{{ $c->gr_tidak_ctk }}</th>
                                <th class="text-end">{{ $c->pcs_awal }}</th>
                                <th class="text-end">{{ $c->gr_awal }}</th>
                                <th class="text-end">{{ $c->pcs_akhir }}</th>
                                <th class="text-end">{{ $c->gr_akhir }}</th>
                                <th class="text-end">{{ $c->pcs_cu }}</th>
                                <th class="text-end">{{ $c->gr_cu }}</th>
                                <th class="text-end">
                                    {{ number_format((1 - ($c->gr_akhir + $c->gr_cu) / $c->gr_awal) * 100, 0) }} %</th>
                                <th class="text-end">
                                    Rp {{ number_format($c->ttl_rp - $c->denda_susut - $c->denda_hcr, 0) }}
                                </th>
                                <th class="text-end">{{ $c->pcs_bk - $c->pcs_awal - $c->pcs_tdk_ctk }}</th>
                                <th class="text-end">{{ $c->gr_bk - $c->gr_awal - $c->gr_tidak_ctk }}</th>
                            </tr>
                            @php
                                $cetak = app('App\Models\CetakModel')->cetak_keluar($c->penerima);
                            @endphp
                            @foreach ($cetak as $c)
                                <tr x-show="openRows.includes({{ $i }})">
                                    <td>{{ $c->name }}</td>
                                    <td><a class="detail" target="_blank"
                                            href="{{ route('dashboard.detail', $c->no_box) }}">{{ $c->no_box }}
                                            <i class="me-2 fas fa-eye"></i></a>
                                        {{-- {{ $c->no_box }} --}}
                                    </td>
                                    <td class="text-end">{{ $c->pcs_bk }}</td>
                                    <td class="text-end">{{ $c->gr_bk }}</td>
                                    <td class="text-end">{{ $c->pcs_tdk_ctk }}</td>
                                    <td class="text-end">{{ $c->gr_tidak_ctk }}</td>
                                    <td class="text-end">{{ $c->pcs_awal }}</td>
                                    <td class="text-end">{{ $c->gr_awal }}</td>
                                    <td class="text-end">{{ $c->pcs_akhir }}</td>
                                    <td class="text-end">{{ $c->gr_akhir }}</td>
                                    <td class="text-end">{{ $c->pcs_cu }}</td>
                                    <td class="text-end">{{ $c->gr_cu }}</td>
                                    <td class="text-end">
                                        {{ number_format((1 - ($c->gr_akhir + $c->gr_cu) / $c->gr_awal) * 100, 0) }} %
                                    </td>
                                    <td class="text-end">
                                        Rp {{ number_format($c->ttl_rp - $c->denda_susut - $c->denda_hcr, 0) }}
                                    </td>
                                    <td class="text-end">{{ $c->pcs_bk - $c->pcs_awal - $c->pcs_tdk_ctk }}</td>
                                    <td class="text-end">{{ $c->gr_bk - $c->gr_awal - $c->gr_tidak_ctk }}</td>
                                </tr>
                            @endforeach
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
