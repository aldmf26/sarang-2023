<x-theme.app title="{{ $title }}" table="Y" sizeCard="7">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
                {{-- <a href="#" data-bs-target="#tambah" data-bs-toggle="modal" class="btn btn-primary"><i
                        class="fas fa-plus"></i>Data</a> --}}

            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <div>

            <table id="table1" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="dhead" width="5">#</th>
                        <th class="dhead">Ruangan</th>
                        <th class="dhead">Bulan</th>
                        <th class="dhead">Standard Suhu</th>
                        <th class="dhead" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $d)
                        @php
                            $param = [
                                'bulan' => $d->bulan,
                                'tahun' => $d->tahun,
                                'ruangan' => $d->ruangan,
                                'standardSuhu' => $d->standar_suhu,
                            ];
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->ruangan }}</td>
                            <td><a
                                    href="{{ route('hrga8_6.create', $param) }}">{{ formatTglGaji($d->bulan, $d->tahun) }}</a>
                            </td>
                            <td>{{ $d->standar_suhu }}</td>
                            <td>
                                <a target="_blank" class="btn btn-sm btn-primary"
                                    href="{{ route('hrga8_6.print', $param) }}"><i class="fas fa-print"></i> Cetak</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
       

        @section('scripts')
            <script>
                pencarian('pencarian', 'tblBox')
            </script>
        @endsection
    </x-slot>

</x-theme.app>
