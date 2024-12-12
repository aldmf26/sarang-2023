<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}</h6>
            <a href="{{ route('hasilwawancara.create') }}" class="btn btn-primary float-end"><i class="fas fa-plus"></i>
                Data</a>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Calon Karyawan</th>
                            <th>Tanggal Lahir</th>
                            <th>Usia</th>
                            <th>Jenis Kelamin</th>
                            <th>Posisi</th>

                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hasilWawancara as $h)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $h->nama }}</td>
                                <td>{{ tanggal($h->tgl_lahir) }}</td>
                                <td>
                                    {{ Umur($h->tgl_lahir, $h->created_at) }}
                                </td>
                                <td>{{ $h->jenis_kelamin == 'P' ? 'Perempuan' : 'Laki-laki' }}</td>
                                <td>{{ $h->posisi }}</td>

                                <td>
                                    <a href="{{ route('hasilwawancara.edit', $h->id) }}"
                                        class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('hasilwawancara.export', $h->id) }}"
                                        class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i></a>
                                    <a href="{{ route('hasilwawancara.delete', $h->id) }}"
                                        class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>


        </section>



        @section('scripts')
            <script>
                $(document).ready(function() {
                    $('.read-more').click(function(e) {
                        e.preventDefault();
                        $(this).next('.full-text').slideToggle();
                        $(this).text($(this).text() === 'Read More' ? 'Show Less' : 'Read More');
                    });
                });
            </script>
        @endsection
    </x-slot>

</x-theme.app>
