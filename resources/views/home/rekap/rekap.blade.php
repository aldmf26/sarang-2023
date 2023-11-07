<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>
            </div>

            <div class="col-lg-6">

            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">
            </div>
            @include('home.rekap.nav', ['kategori' => $kategori])

        </div>

    </x-slot>

    <x-slot name="cardBody">
        <style>
            .badge {
                cursor: pointer;
            }
        </style>
        <form id="formAbsen" method="POST">
            @csrf
            <section class="row">
                <div class="col-lg-4 mb-2">
                    <div class="form-group">
                        <label for="">Pencarian : </label>
                        <input autofocus type="text" id="pencarian" class="form-control float-end">
                    </div>

                </div>

                <div class="col-lg-12">

                    <table style="border:1px solid #97a1c3" class="table table-bordered" id="tbl2"
                        x-data="{
                            openRows: [],
                        }">
                        <thead>
                            <tr>
                                <th class="dhead">Pengawas </th>
                                <th class="dhead">Nama Anak</th>
                                <th class="dhead text-center" width="50">Hari Masuk</th>
                                <th class="dhead">Pcs Awal</th>
                                <th class="dhead">Gr Awal</th>
                                <th class="dhead">Pcs Akhir</th>
                                <th class="dhead">Gr Akhir</th>
                                <th class="dhead">Gr Flx</th>
                                <th class="dhead">Gr Eot</th>
                                <th class="dhead">Susut</th>
                                <th class="dhead">Ttl Rp</th>
                                <th class="dhead">Rp Denda</th>
                                <th class="dhead">Ttl Gaji</th>
                                <th class="dhead">Rata2</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($pengawas as $i => $d)
                                <tr>
                                    <th><a href="#" class="detailAbsen"
                                            id_pengawas="{{ $d->id }}">{{ $d->name }}</a>
                                        <span class="badge bg-primary float-end"
                                            @click="openRows.includes({{ $i }}) ? openRows = openRows.filter(item => item !== {{ $i }}) : openRows.push({{ $i }})">Buka
                                            <i class="fas fa-caret-down"></i>
                                        </span>
                                    </th>
                                    <th class="text-center">Jml : {{ $d->ttl_anak ?? 0 }}</th>
                                    <th class="text-center">{{ $d->total_absen }}</th>
                                    <th class="text-end">{{ number_format($d->pcs_awal ?? 0,0) }}</th>
                                    <th class="text-end">{{ number_format($d->gr_awal ?? 0,0) }}</th>
                                    <th class="text-end">{{ number_format($d->pcs_akhir ?? 0,0) }}</th>
                                    <th class="text-end">{{ number_format($d->gr_akhir ?? 0,0) }}</th>
                                    <th class="text-end">{{ number_format($d->gr_flx ?? 0,0) }}</th>
                                    <th class="text-end">{{ number_format($d->eot ?? 0,0) }}</th>
                                    <th class="text-end">{{ number_format($d->susut ?? 0,0) }} %</th>
                                    <th class="text-end">{{ number_format($d->ttl_rp ?? 0,0) }}</th>
                                    <th class="text-end">{{ number_format($d->total_nominal ?? 0,0) }}</th>
                                    @php
                                        $gaji = $d->ttl_rp - $d->total_nominal;
                                    @endphp
                                    <th class="text-end">{{ number_format($gaji,0) }}</th>
                                    <th></th>
                                    {{-- <th class="text-end">{{ number_format(!empty($d->total_absen) ? $gaji / $d->total_absen : 0,0) }}</th> --}}

                                </tr>
                                @php
                                    $query = \App\Models\Cabut::getGajiAnak($d->id, date('m'), date('Y'));
                                @endphp
                                @foreach ($query as $x)
                        <tbody x-show="openRows.includes({{ $i }})">
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td>{{ $x->nama }} / {{ $x->id_kelas }}</td>
                                <td class="text-center">{{ $x->ttl ?? 0 }}</td>
                                <td class="text-end">{{ $x->pcs_awal ?? 0 }}</td>
                                <td class="text-end">{{ $x->gr_awal ?? 0 }}</td>
                                <td class="text-end">{{ $x->pcs_akhir ?? 0 }}</td>
                                <td class="text-end">{{ $x->gr_akhir ?? 0 }}</td>
                                <td class="text-end">{{ $x->gr_flx ?? 0 }}</td>
                                <td class="text-end">{{ $x->eot ?? 0 }}</td>
                                <td class="text-end">{{ number_format($x->susut ?? 0, 0) }} %</td>
                                <td class="text-end">{{ number_format($x->ttl_rp ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format($x->nominal ?? 0, 0) }}</td>
                                @php
                                    $ttlGaji = $x->ttl_rp - $x->nominal;
                                @endphp
                                <td class="text-end">{{ number_format($ttlGaji ?? 0, 0) }}</td>
                                <td class="text-end">{{ number_format(!empty($x->ttl) ? $ttlGaji / $x->ttl : 0, 0) }}
                                </td>

                            </tr>
                        </tbody>
                   
                        @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </section>
        </form>

        <x-theme.modal idModal="detailAbsen" btnSave="T" title="Detail Absen Anak">

            <div id="loadDetailAbsen"></div>
        </x-theme.modal>
        @section('scripts')
            <script>
                pencarian('pencarian', 'tbl2')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
