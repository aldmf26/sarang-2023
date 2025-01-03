<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="">{{ $title }}</h6>
        <a href="" class="btn btn-primary float-end me-2" data-bs-toggle="modal" data-bs-target="#tambah"><i
                class="fas fa-plus"></i>Data</a>

    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" id="table">
                    <thead>
                        <tr>
                            <th class="dhead">No</th>
                            <th class="dhead">Nama Sarana dan Prasarana</th>
                            <th class="dhead">Lokasi</th>
                            <th class="dhead">No identifikasi</th>
                            <th class="dhead">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item_perbaikan as $i)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $i->nama }}</td>
                                <td>{{ $i->lokasi }} lantai {{ $i->lantai }}</td>
                                <td>{{ $i->no_identifikasi }}</td>
                                <td><a href="{{ route('hrga5_2.print', ['kategori' => $i->kategori, 'id' => $i->id]) }}"
                                        target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print"></i></a>
                                </td>
                            </tr>
                        @endforeach





                    </tbody>

                </table>
            </div>

            <form action="{{ route('hrga5_2.store') }}" method="post">
                @csrf
                <x-theme.modal idModal="tambah" title="{{ $title }}" size="modal-xl" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-2">
                            <label for="">Lokasi</label>
                            <select name="lokasi_id" id="" class="select2 lokasi">
                                <option value="">Pilih Lokasi</option>
                                @foreach ($lokasi as $l)
                                    <option value="{{ $l->id }}">{{ $l->lokasi }} lantai
                                        ({{ $l->lantai }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="">Nama Sarana dan Prasarana Umum</label>
                            <select name="item_id" id="" class="select2 item">

                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="">Tanggal</label>
                            <input type="date" class="form-control" name="tgl" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-lg-2">
                            <label for="">Fungsi</label>
                            <input type="text" class="form-control" name="fungsi">
                        </div>
                        <div class="col-lg-2">
                            <label for="">Kesimpulan</label>
                            <input type="text" class="form-control" name="kesimpulan">
                        </div>







                    </div>
                </x-theme.modal>
            </form>
            <form action="{{ route('hrga5_3.store2') }}" method="post">
                @csrf
                <x-theme.modal idModal="selesaikan" title="{{ $title }}" size="modal-xl" btnSave="Y">
                    <div class="row">
                        <input type="hidden" class="id_permintaan" name="id">
                        <div class="col-lg-6 mt-2">
                            <label for="">Detail Perbaikan yang dilakukan</label>
                            <textarea name="detail" class="form-control" id="" cols="30" rows="5"></textarea>
                        </div>
                        <div class="col-lg-6 mt-2">
                            <label for="">Verifikasi User</label>
                            <textarea name="verifikasi" class="form-control" id="" cols="30" rows="5"></textarea>
                        </div>

                    </div>
                </x-theme.modal>
            </form>

        </section>







        @section('scripts')
            <script>
                $(document).ready(function() {
                    $(document).on('change', '.lokasi', function() {
                        var id = $(this).val();


                        $.ajax({
                            type: "get",
                            url: "{{ route('hrga5_3.get_item') }}",
                            data: {
                                id: id
                            },
                            success: function(response) {
                                $('.item').html(response);
                            }
                        });

                    })
                    $(document).on('change', '.item', function() {
                        var id = $(this).val();
                        $.ajax({
                            type: "get",
                            url: "{{ route('hrga5_3.get_merk') }}",
                            data: {
                                id: id
                            },
                            success: function(response) {
                                $('.merk').val(response.merk);
                                $('.no_identifikasi').val(response.no_identifikasi);
                            }
                        });

                    })

                })
            </script>
        @endsection
    </x-slot>

</x-theme.app>
