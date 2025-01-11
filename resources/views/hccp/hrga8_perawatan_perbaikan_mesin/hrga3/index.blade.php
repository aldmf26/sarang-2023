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
                            <th class="dhead">Nama Mesin Proses Produksi </th>
                            <th class="dhead">Tanggal</th>
                            <th class="dhead">Deadline</th>
                            <th class="dhead">Lokasi</th>
                            <th class="dhead">No identifikasi</th>
                            <th class="dhead">Diajukan oleh bagian</th>
                            <th class="dhead">Deskripsi Masalah</th>
                            <th class="dhead">Selesai</th>
                            <th class="dhead">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permintaan as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nama }}</td>
                                <td>{{ tanggal($p->tgl_mulai) }}</td>
                                <td>{{ tanggal($p->deadline) }}</td>
                                <td>{{ $p->lokasi }}</td>
                                <td>{{ $p->no_identifikasi }}</td>
                                <td>{{ $p->diajukan_oleh }}</td>
                                <td>{{ $p->deskripsi_masalah }}</td>
                                <td>{{ $p->selesai == 'T' ? 'belum' : 'sudah' }}</td>
                                <td>
                                    <a href="{{ route('hrga8_3.print', $p->id) }}" target="_blank"
                                        class="btn btn-warning btn-sm"><i class="fa fa-print"></i>
                                    </a>
                                    @if ($p->selesai == 'T')
                                        <a href="#" class="btn btn-success btn-sm btn_selesai"
                                            data-bs-toggle="modal" data-bs-target="#selesaikan"
                                            id="{{ $p->id }}"><i class="far fa-clipboard"></i>
                                        </a>
                                    @endif

                                </td>
                            </tr>
                        @endforeach




                    </tbody>

                </table>
            </div>

            <form action="{{ route('hrga8_3.store') }}" method="post">
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
                            <select name="id_item" id="" class="select2 item">

                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="">Tanggal</label>
                            <input type="date" class="form-control" name="tgl_mulai" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-lg-2">
                            <label for="">Deadline</label>
                            <input type="date" class="form-control" name="deadline" value="{{ date('Y-m-d') }}">
                        </div>


                        <div class="col-lg-2">
                            <label for="">No identifikasi</label>
                            <input type="text" class="form-control no_identifikasi" name="no_identifikasi">
                        </div>
                        <div class="col-lg-2">
                            <label for="">Diajukan oleh </label>
                            <input type="text" class="form-control" name="diajukan_oleh">
                        </div>
                        <div class="col-lg-12 mt-2">
                            <label for="">Deskripsi Masalah</label>
                            <textarea name="deskripsi_masalah" class="form-control" id="" cols="30" rows="5"></textarea>
                        </div>




                    </div>
                </x-theme.modal>
            </form>
            <form action="{{ route('hrga8_3.store2') }}" method="post">
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
                    $(document).on('click', '.btn_selesai', function() {
                        var id = $(this).attr('id');

                        $('.id_permintaan').val(id);

                    })
                    $(document).on('change', '.lokasi', function() {
                        var id = $(this).val();


                        $.ajax({
                            type: "get",
                            url: "{{ route('hrga8_3.get_item') }}",
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
