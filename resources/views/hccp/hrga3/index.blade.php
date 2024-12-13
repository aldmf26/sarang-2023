<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <a class="btn btn-sm btn-primary" href="{{ route('hrga3.create') }}"><i class="fa fa-plus"></i> Tambah Hasil
                Evaluasi Karyawan</a>
        </div>
    </x-slot>
    <x-slot name="cardBody">

        <div class="mb-3 d-flex justify-content-between">
            <div>
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link  {{ $keputusan == 'lulus' ? 'active' : '' }}" aria-current="page"
                            href="{{ route('hrga3.index', ['keputusan' => 'lulus']) }}">Lulus</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link  {{ $keputusan == 'tidak lulus' ? 'active' : '' }}" aria-current="page"
                            href="{{ route('hrga3.index', ['keputusan' => 'tidak lulus']) }}">Tidak Lulus</a>
                    </li>

                </ul>
            </div>

        </div>

        <table class="table" id="table1">
            <thead>
                <tr>
                    <th class="dhead">#</th>
                    <th class="dhead">Nama Karyawan</th>
                    <th class="dhead">Usia</th>
                    <th class="dhead">Jenis Kelamin</th>
                    <th class="dhead">Posisi</th>
                    <th class="dhead">Periode Masa Percobaan</th>
                    <th class="dhead">Penilaian</th>
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
                        <td>{{ $d->divisi }}</td>
                        <td>{{ $d->periode_masa_percobaan }} Bulan</td>
                        <td><a href="#" class="penilaianShow" data-id="{{ $d->id }}">Lihat Penilaian</a>
                        </td>
                        <td class="d-flex gap-1">
                            <a target="_blank" href="{{ route('hrga3.export', $d->id) }}"
                                class="btn btn-sm btn-primary"><i class="fa fa-print"></i></a>
                            {{-- <a href="{{ route('hrga1.edit', $d->id) }}" class="btn btn-sm btn-primary"><i
                                    class="fa fa-edit"></i></a>
                            <form action="{{ route('hrga1.delete', $d->id) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger"><i
                                        class="fa fa-trash"></i></button>
                            </form> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <x-theme.modal idModal="penilaianModal" title="Penilaian" btnSave="T" size="modal-lg">
            <div id="load_penilaian"></div>
        </x-theme.modal>

        @section('scripts')
            <script>
                $(document).ready(function() {
                    $('.penilaianShow').click(function(e) {
                        e.preventDefault();
                        var karyawanId = $(this).data('id');
                        $("#penilaianModal").modal('show');
                        $.ajax({
                            url: "{{route('hrga3.penilaianShow')}}?id=" + karyawanId,
                            type: 'GET',
                            success: function(response) {
                                $('#load_penilaian').html(response);
                            },
                            error: function(xhr) {
                                alert('Error fetching penilaian data.');
                            }
                        });
                    });
                });
            </script>
        @endsection
    </x-slot>

</x-theme.app>
