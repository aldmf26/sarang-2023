<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}</h6>
            @if (!empty($id_divisi))
                <a href="{{ route('hrga2.create', ['id_divisi' => $id_divisi]) }}" class="btn btn-primary float-end"><i
                        class="fas fa-plus"></i>
                    Data</a>
            @endif
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                @if (!empty($id_divisi))
                    <table class="table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th width="15%">Nama Calon Karyawan</th>
                                <th width="15%">NIK</th>
                                <th>Tanggal Lahir</th>

                                <th>Usia Masuk Kerja</th>
                                <th>Jenis Kelamin</th>
                                <th>Posisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hasil_wawancara as $h)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $h->nama }}</td>
                                    <td>{{ $h->nik }}</td>
                                    <td>{{ tanggal($h->tgl_lahir) }}</td>

                                    <td>{{ umur($h->tgl_lahir, $h->tgl_masuk) }} Tahun</td>
                                    <td>{{ $h->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td>{{ $h->divisi }}</td>
                                    <td>
                                        {{-- <a href="{{ route('hrga2.edit', $h->id) }}" class="btn btn-sm btn-warning"><i
                                                class="fas fa-edit"></i></a> --}}
                                        <a href="{{ route('hrga2.export', $h->id) }}" class="btn btn-success btn-sm"><i
                                                class="fas fa-file-excel"></i></a>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                @else
                    <div class="load-data"></div>

                @endif

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

                    function getData() {
                        $.ajax({
                            type: "get",
                            url: "{{ route('hrga2.getData') }}",
                            success: function(response) {
                                $('.load-data').html(response);
                            }
                        });
                    }
                    getData();

                    function save_data(id_anak) {
                        var nik = $('.nik' + id_anak).val();
                        var nama = $('.nama' + id_anak).val();
                        var tgl_lahir = $('.tgl_lahir' + id_anak).val();
                        var tgl_masuk = $('.tgl_masuk' + id_anak).val();
                        var jenis_kelamin = $('.jenis_kelamin' + id_anak).val();
                        var divisi = $('.divisi' + id_anak).val();

                        $.ajax({
                            type: "get",
                            url: "{{ route('hrga2.tambah_data') }}",
                            data: {
                                id_anak: id_anak,
                                nik: nik,
                                nama: nama,
                                tgl_lahir: tgl_lahir,
                                jenis_kelamin: jenis_kelamin,
                                divisi: divisi,
                                tgl_masuk: tgl_masuk
                            },
                            success: function(response) {
                                getData();
                                alertToast('sukses', 'Berhasil ditambahkan');
                            }
                        });

                    }

                    $(document).on("click", ".simpan_data", function(e) {
                        e.preventDefault();
                        var id_anak = $(this).attr('id_anak');

                        save_data(id_anak);

                    });
                });
            </script>
        @endsection
    </x-slot>

</x-theme.app>
