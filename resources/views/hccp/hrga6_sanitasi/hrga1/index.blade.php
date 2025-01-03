<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
                <a href="#" data-bs-target="#tambah" data-bs-toggle="modal" class="btn btn-primary"><i
                        class="fas fa-plus"></i>Data</a>
                <a href="{{route('hrga6_1.print', ['id_lokasi' => $id_lokasi])}}" class="btn btn-primary"><i
                        class="fas fa-print"></i> Print</a>
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <div>
            <div class="mb-3 d-flex justify-content-between">
                <div>
                    Area Concern :
                    <ul class="nav nav-pills">
                        @foreach ($lokasi as $l)
                            <li class="nav-item ">
                                <a class="nav-link @if ($area == $l->lokasi) active @endif"aria-current="page"
                                    href="{{ route('hrga6_1.index', ['area' => $l->lokasi]) }}">{{ ucwords($l->lokasi) }}</a>
                            </li>
                        @endforeach

                    </ul>
                </div>

            </div>

            <table id="table1" class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Alat/Area</th>
                        <th>Identifikasi Alat/Area</th>
                        <th>Metode Sanitasi</th>
                        <th>Penanggung Jawab</th>
                        <th>Frekuensi</th>
                        <th>Sarana Cleaning</th>
                        <th>Sanitizer & Pengenceran</th>
                        <th>Updated</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $d)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $d->nm_alat }}</td>
                        <td>{{ $d->identifikasi_alat }}</td>
                        <td>{{ $d->metode }}</td>
                        <td>{{ $d->penanggung_jawab }}</td>
                        <td>{{ $d->frekuensi }}</td>
                        <td>{{ $d->sarana_cleaning }}</td>
                        <td>{{ $d->sanitizer }}</td>
                        <td>{{ $d->tgl }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        <form action="{{ route('hrga6_1.store') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="Jadwal Sanitasi" size="modal-full" btnSave="Y">
                <table id="tableScroll" class="table table-bordered" x-data="{
                    rows: ['1']
                }">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Alat/Area</th>
                            <th>Identifikasi Alat/Area</th>
                            <th>Metode Sanitasi</th>
                            <th>Penanggung Jawab</th>
                            <th>Frekuensi</th>
                            <th>Sarana Cleaning</th>
                            <th>Sanitizer & Pengenceran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in rows" :key="index">
                            <tr>
                                <td>
                                </td>
                                <td>
                                    <input type="hidden" name="id_lokasi" value="{{ $id_lokasi }}">
                                    <input type="text" class="form-control" name="nm_alat[]"
                                        placeholder="Nama Alat/Area">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="identifikasi_alat[]"
                                        placeholder="Nama Alat/Area">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="metode[]"
                                        placeholder="Nama Alat/Area">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="penanggung_jawab[]"
                                        placeholder="Nama Alat/Area">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="frekuensi[]"
                                        placeholder="Nama Alat/Area">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="sarana_cleaning[]"
                                        placeholder="Nama Alat/Area">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="sanitizer[]"
                                        placeholder="Nama Alat/Area">
                                </td>
                                <td>
                                    <button x-show="index > 0" class="btn btn btn-sm btn-danger" type="button"
                                        @click="rows.splice(index, 1)">-</button>

                                </td>
                            </tr>

                        </template>
                        <tr>
                            <td colspan="9"><button class="btn btn-sm btn-info btn-block" type="button"
                                    @click="rows.push('')">+ Tambah baris</button></td>
                        </tr>
                    </tbody>


                </table>
            </x-theme.modal>
        </form>

        @section('scripts')
            <script>
                pencarian('pencarian', 'tblBox')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
