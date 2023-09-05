<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button href="{{ route('cabut.add') }}" icon="fa-plus" addClass="float-end" teks="Tambah" />
        <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Krywn" />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Box</th>
                        <th>Pengawas</th>
                        <th>Anak</th>
                        <th>Tgl Terima</th>
                        <th class="text-end">Pcs Awal</th>
                        <th class="text-end">Gr Awal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cabut as $no => $d)
                    <tr>
                        <td>{{ $no+1 }}</td>
                        <td>{{ $d->no_box }}</td>
                        <td>{{ ucwords(auth()->user()->name) }}</td>
                        <td>{{ $d->nama }}</td>
                        <td>{{ $d->tgl_terima }}</td>
                        <td align="right">{{ $d->pcs_awal }}</td>
                        <td align="right">{{ $d->gr_awal }}</td>
                        <td align="center">
                            <div class="btn-group" role="group">
                                <span class="btn btn-sm" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-primary"></i>
                                </span>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                    <li><a class="dropdown-item text-info inputAkhir" href="#"
                                            no_box="{{ $d->no_box }}" id_anak="{{ $d->id_anak }}" href="#" data-bs-toggle="modal"
                                            data-bs-target="#inputAkhir"><i class="me-2 fas fa-pen"></i>Akhir</a>
                                    </li>

                                    {{-- <li>
                                        <a class="dropdown-item text-info edit_akun"
                                            href="{{ route('cabut.edit', ['no_box' => $d->no_box]) }}"><i
                                                class="me-2 fas fa-pen"></i>Edit</a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item text-danger delete_nota" no_nota="{{ $d->no_box }}"
                                            href="#" data-bs-toggle="modal" data-bs-target="#delete"><i
                                                class="me-2 fas fa-trash"></i>Delete
                                        </a>
                                    </li> --}}
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </section>

        <form action="{{ route('cabut.input_akhir') }}" method="post">
            @csrf
            <x-theme.modal idModal="inputAkhir" title="tambah cabut akhir" btnSave="Y" size="modal-lg">
                <div id="load_modal_akhir"></div>
            </x-theme.modal>
        </form>

        <form action="{{ route('cabut.create_anak') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="tambah Anak" btnSave="Y">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group">
                            <label for="">Tambah Anak</label>
                            <div id="load_anak_nopengawas"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Aksi</label><br>
                        <button class="btn btn-sm btn-primary" type="button" id="add_anak">Edit/Save</button>
                    </div>
                </div>
                <div id="load_anak"></div>
            </x-theme.modal>
        </form>
        @section('scripts')
            <script>
                $(".select3").select2()

                load_anak()
                load_anak_nopengawas()

                function load_anak() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_anak') }}",
                        success: function(r) {
                            $("#load_anak").html(r);
                        }
                    });
                }

                function load_anak_nopengawas() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_anak_nopengawas') }}",
                        success: function(r) {
                            $("#load_anak_nopengawas").html(r)
                            $(".select3-load").select2()

                        }
                    });
                }
                $(document).on('click', '#add_anak', function() {
                    var id_anak = $(".anakNoPengawas").val()
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.add_delete_anak') }}?id_anak=" + id_anak,
                        success: function(r) {
                            alertToast('Berhasil tambah anak')
                            load_anak()
                            load_anak_nopengawas()
                        }
                    });
                })
                $(document).on('click', '#delete_anak', function(e) {

                    var id_anak = $(this).attr('id_anak')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.add_delete_anak') }}",
                        data: {
                            id_anak: id_anak,
                            delete: 1,
                        },
                        success: function(r) {
                            alertToast('Berhasil tambah anak')
                            load_anak()
                            load_anak_nopengawas()
                        }
                    });
                })

                $(document).on('click', '.inputAkhir', function(){
                    var no_box = $(this).attr('no_box')
                    var id_anak = $(this).attr('id_anak')
                    $.ajax({
                        type: "GET",
                        url: "cabut/load_modal_akhir",
                        data: {
                            no_box:no_box,
                            id_anak:id_anak,
                        },
                        success: function (r) {
                            $("#load_modal_akhir").html(r);
                        }
                    });
                })
            </script>
        @endsection
    </x-slot>
</x-theme.app>
