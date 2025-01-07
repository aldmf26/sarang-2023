<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">

        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
                <a href="#" data-bs-toggle="modal" data-bs-target="#tambah" class="btn btn-primary btn-sm">Foothbath
                    Template</a>

            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <table class="table table-hover" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Bulan</th>
                        <th>Area</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $d)
                        @php
                            $param = ['bulan' => $d->bulan, 'tahun' => $d->tahun, 'id_lokasi' => $d->id_lokasi];
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><a
                                    href="{{ route('hrga6_4.create', $param) }}">{{ formatTglGaji($d->bulan, $d->tahun) }}</a>
                            </td>
                            <td>{{ $d->lokasi }}</td>
                            <td>
                                <a target="_blank" class="btn btn-sm btn-primary"
                                    href="{{ route('hrga6_4.print', $param) }}"><i class="fas fa-print"></i> Cetak</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <x-theme.modal btnSave="T" idModal="tambah" title="Tambah Data">
            @livewire('foothbath-template')
        </x-theme.modal>
    </x-slot>
</x-theme.app>
