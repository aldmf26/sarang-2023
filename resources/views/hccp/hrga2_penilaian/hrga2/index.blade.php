<x-theme.app title="{{ $title }}" table="Y" sizeCard="11">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <a class="btn btn-sm btn-primary" href="{{ route('hrga3.create') }}"><i class="fa fa-plus"></i> Tambah Penilaian</a>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <table class="table" id="table1">
            <thead>
                <tr>
                    <th class="dhead">#</th>
                    <th class="dhead">Nama Karyawan</th>
                    <th class="dhead">Usia</th>
                    <th class="dhead">Jenis Kelamin</th>
                    <th class="dhead">Divisi</th>
                    <th class="dhead">Posisi</th>
                    <th class="dhead">Periode Masa Percobaan</th>
                    <th class="dhead">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($karyawans as $d)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $d->nama }}</td>
                        <td>{{ Umur($d->tgl_lahir, $d->created_at) }}</td>
                        <td>{{ $d->jenis_kelamin }}</td>
                        <td>{{ $d->divisi->divisi }}</td>
                        <td>{{ $d->posisi2 }}</td>
                        <td>{{ $d->periode_masa_percobaan }} Bulan</td>
                        </td>
                        <td class="d-flex gap-1">
                            <a target="_blank" href="{{ route('hrga2_2.penilaian', $d->id) }}"
                                class="btn btn-sm btn-primary">Lihat Penilaian</a>

                            {{-- <a target="_blank" href="{{ route('hrga3.export', $d->id) }}"
                                class="btn btn-sm btn-primary"><i class="fa fa-print"></i></a> --}}
                        
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      
    </x-slot>

</x-theme.app>
