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
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </section>

        <form action="{{ route('cabut.create_anak') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah" title="tambah Anak" btnSave="Y">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group">
                            <label for="">Tambah Anak</label>
                            {{-- <select class="select3 anakNoPengawas" name="" multiple id="">
                                @foreach ($anakNoPengawas as $d)
                                <option value="{{ $d->id_anak }}">{{ ucwords($d->nama) }}</option>
                                @endforeach
                            </select> --}}
                            <div id="load_anak_nopengawas"></div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Aksi</label><br>
                        <button class="btn btn-sm btn-primary" type="button" id="add_anak">Edit/Save</button>
                    </div>
                </div>
                <div id="load_anak"></div>
                {{-- <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <tr>
                                <th width="180">Nama</th>
                                <th width="80">Kelas</th>
                                <th>Tgl Masuk</th>
                                <th>Aksi</th>
                            </tr>
                            @foreach ($anak as $d)
                            <tr>
                                <td>{{ ucwords($d->nama) }}</td>
                                <td><input type="text" value="{{ $d->kelas }}" class="form-control"></td>
                                <td><input type="date" class="form-control"></td>
                                <td><button class="btn btn-sm btn-danger"><i class="fas fa-window-close"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div> --}}
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
            </script>
        @endsection
    </x-slot>
</x-theme.app>
