<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="">{{ $title }}</h6>

    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th class="text-center dhead">No</th>
                            <th class="text-center dhead">Nama Mesin</th>
                            <th class="text-center dhead">Merek</th>
                            <th class="text-center dhead">No. Mesin</th>
                            <th class="text-center dhead">Lokasi</th>
                            <th class="text-center dhead">Frekuensi Perawatan</th>
                            <th class="text-center dhead">Penanggung Jawab</th>
                            <th class="text-center dhead">Ceklist</th>
                        </tr>

                    </thead>
                    <tbody>

                        @foreach ($pemeliharaan as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nama }}</td>
                                <td class="text-center">{{ $p->merk }}</td>
                                <td class="text-center">{{ $p->no_identifikasi }}</td>
                                <td class="text-center">{{ $p->lokasi }}</td>
                                <td class="text-center">Setiap {{ $p->frekuensi_perawatan }} bulan</td>
                                <td class="text-center"> {{ $p->penanggung_jawab }} </td>
                                <td>
                                    <a href="{{ route('hrga8_2.print', $p->id) }}" target="_blank"
                                        class="btn btn-warning btn-sm"><i class="fa fa-print"></i>
                                    </a>
                                    <a href="#" class="btn btn-success btn-sm btn_selesai" data-bs-toggle="modal"
                                        data-bs-target="#selesaikan" id="{{ $p->id }}"><i
                                            class="fa fa-clipboard"></i></a>
                                </td>
                            </tr>
                        @endforeach


                    </tbody>

                </table>
            </div>

            <style>
                .modal-lg-max {
                    max-width: 80% !important;
                }
            </style>
            <form action="{{ route('hrga8_2.store') }}" method="post">
                @csrf
                <x-theme.modal idModal="selesaikan" title="{{ $title }}" size="modal-lg-max" btnSave="Y">
                    <div class="row">
                        <input type="hidden" class="id_perawatan" name="id">
                        <div class="col-lg-2">
                            <label for="">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal">
                        </div>

                        <div class="col-lg-12">
                            <hr style="border: 1px solid black">
                        </div>

                        <div class="col-lg-2">
                            <label for="">Kriteria pemeriksaan</label>
                            <input type="text" class="form-control" name="kriteria[]">
                        </div>
                        <div class="col-lg-2">
                            <label for="">Metode</label>
                            <input type="text" class="form-control" name="metode[]">
                        </div>
                        <div class="col-lg-2">
                            <label for="">Hasil Pemeriksaan</label>
                            <select name="hasil_pemeriksaan[]" class="form-control" id="">
                                <option value="Ok">Ok</option>
                                <option value="Tidak Ok">Tidak Ok</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="">Status</label>
                            <input type="text" class="form-control" name="status[]">
                        </div>
                        <div class="col-lg-2">
                            <label for="">Ket</label>
                            <input type="text" class="form-control" name="ket[]">
                        </div>
                        <div class="col-lg-1">
                            <label for="">Aksi</label> <br>
                            <button type="button" onclick="" class="btn btn-sm btn-primary tambh_baris"><i
                                    class="fa fa-plus"></i></button>
                        </div>

                    </div>
                    <div class="row_baru"></div>
                </x-theme.modal>
            </form>


        </section>







        @section('scripts')
            <script>
                $(document).ready(function() {
                    $(document).on('click', '.btn_selesai', function() {
                        var id = $(this).attr('id');

                        $('.id_perawatan').val(id);

                    })
                    var count = 0;
                    $(document).on('click', '.tambh_baris', function() {
                        count = count + 1;

                        $.ajax({
                            type: "get",
                            url: "{{ route('hrga8_2.tambah_baris') }}",
                            data: {
                                count: count
                            },

                            success: function(response) {
                                $('.row_baru').append(response);
                            }
                        });
                    })
                    $(document).on('click', '.delete_baris', function() {
                        var count = $(this).attr('count');
                        $('.baris' + count).remove();
                    })


                })
            </script>
        @endsection
    </x-slot>

</x-theme.app>
