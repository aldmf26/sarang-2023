<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}</h6>
            <a href="{{ route('hrga4.export') }}" class="btn btn-primary float-end"><i class="fas fa-file-excel"></i>
                Export</a>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Divisi / Dept</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Jenis Kelamin/ <br>Tanggal lahir</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Tanggal Masuk</th>
                            <th class="text-center">Posisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawans as $k)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $k->divisi }}</td>
                                <td class="text-center">{{ $k->nama }}</td>
                                <td class="text-center">{{ $k->jenis_kelamin }} / {{ tanggal($k->tgl_lahir) }}</td>
                                <td class="text-center">{{ $k->posisi2 }}</td>
                                <td class="text-center">01 Februari 2023</td>
                                <td class="text-center">Pengawas</td>
                            </tr>
                        @endforeach



                    </tbody>

                </table>
            </div>


        </section>



        @section('scripts')
        @endsection
    </x-slot>

</x-theme.app>
