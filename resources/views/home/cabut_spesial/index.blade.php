<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        {{--
        <x-theme.button href="{{ route('cabutSpesial.add') }}" icon="fa-plus" addClass="float-end" teks="Tambah" /> --}}
        <a href="{{ route('cabut.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="float-end btn btn-sm  btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>
        <x-theme.button href="#" modal="Y" idModal="tambah_awal" icon="fa-plus" addClass="float-end"
            teks="Cbt Spesial" />
        <a href="#" data-bs-target="#tambahAnak" data-bs-toggle="modal"
            class="btn tambahAnak btn-primary btn-sm float-end me-2"><i class="fas fa-plus"></i> kry kerja <span
                class="badge bg-danger" id="anakBelum"></span>
        </a>
        <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end"
            teks="Kry Baru" />
        {{--
        <x-theme.btn_filter /> --}}
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Box</th>
                        {{-- <th>Pengawas</th> --}}
                        <th>Anak</th>
                        <th>Tgl Terima</th>
                        <th class="text-end">Pcs Awal</th>
                        <th class="text-end">Gr Awal</th>
                        <th class="text-end">Pcs Akhir</th>
                        <th class="text-end">Gr Akhir</th>
                        <th class="text-end">EOT</th>
                        <th class="text-end">Susut</th>
                        {{-- <th class="text-end">Denda</th> --}}
                        <th class="text-end">Ttl Gaji</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cabut as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ $d->no_box }}</td>
                            {{-- <td>{{ ucwords(auth()->user()->name) }}</td> --}}
                            <td>{{ $d->nama }}</td>
                            <td>{{ date('d M y', strtotime($d->tgl)) }}</td>
                            <td align="right">{{ $d->pcs_awal }}</td>
                            <td align="right">{{ $d->gr_awal }}</td>
                            <td align="right">{{ $d->pcs_akhir ?? 0 }}</td>
                            <td align="right">{{ $d->gr_akhir ?? 0 }}</td>
                            <td align="right">{{ $d->eot ?? 0 }}</td>
                            @php
                                $susut = empty($d->gr_akhir) ? 0 : (1 - ($d->gr_flex + $d->gr_akhir) / $d->gr_awal) * 100;
                            @endphp
                            <td align="right">{{ number_format($susut, 0) }}%</td>
                            {{-- <td align="right">{{ number_format($denda,0)}}</td> --}}
                            <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                            <td align="center">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#detail"
                                    class="btn btn-sm btn-primary detail" id_cabut="{{ $d->id_cabut_spesial }}"><i
                                        class="fas fa-eye"></i></a>
                                @if ($d->selesai == 'T')
                                    <a class="btn btn-warning btn-sm inputAkhir" href="#"
                                        id_cabut="{{ $d->id_cabut_spesial }}" href="#" data-bs-toggle="modal"
                                        data-bs-target="#inputAkhir"></i>Akhir</a>

                                    @if (!empty($d->eot))
                                        <a class="btn btn-primary btn-sm selesai" href="#"
                                            id_cabut="{{ $d->id_cabut_spesial }}" href="#" data-bs-toggle="modal"
                                            data-bs-target="#selesai"></i>Selesai</a>
                                    @endif
                                @endif



                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </section>

        <form action="{{ route('cabutSpesial.input_akhir') }}" method="post">
            @csrf
            <x-theme.modal idModal="inputAkhir" title="tambah cabut spesial akhir" btnSave="Y" size="modal-lg">
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

        <x-theme.modal idModal="detail" title="Detail Cabut" size="modal-lg-max" btnSave="T">
            <div class="row">
                <div class="col-lg-12">
                    <div id="load_detail_cabut"></div>
                </div>
            </div>
        </x-theme.modal>

        <form id="save_absen">
            <x-theme.modal idModal="tambahAnak" title="Tambah anak" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="load_anak_cabut"></div>
                    </div>
                </div>
            </x-theme.modal>
        </form>
        <form action="{{ route('cabutSpesial.create') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah_awal" size="modal-lg-max" title="Tambah data cabut spesial"
                btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="load_gr_awal"></div>
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <form action="{{ route('cabutSpesial.selesai_cabut') }}" method="post">
            @csrf
            <x-theme.modal idModal="selesai" title="Selesai" btnSave="Y" color_header="modal-success">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="text-center">Apakah anda yakin ingin menyelesaikannya ?</p>
                        <p class="text-center fw-bold">Note : </p>
                        <p class="text-center fw-bold fst-italic">Data yang sudah diselesaikan tidak dapat di edit
                            maupun dihapus
                        </p>
                        <input type="hidden" name="id_cabut" class="cetak">
                    </div>
                </div>
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

                $(document).on('click', '.inputAkhir', function() {
                    var id_cabut = $(this).attr('id_cabut')
                    $.ajax({
                        type: "GET",
                        url: "cabutSpesial/load_modal_akhir",
                        data: {
                            id_cabut: id_cabut,
                        },
                        success: function(r) {
                            $("#load_modal_akhir").html(r);
                        }
                    });
                })
                $(document).on('click', '.detail', function() {
                    var id_cabut = $(this).attr('id_cabut')
                    $.ajax({
                        type: "GET",
                        url: "cabut/load_detail_cabut",
                        data: {
                            id_cabut: id_cabut,
                        },
                        success: function(r) {
                            $("#load_detail_cabut").html(r);
                        }
                    });
                })
                $(document).on('click', '.selesai', function() {
                    var id_cabut = $(this).attr('id_cabut');

                    $('.cetak').val(id_cabut);
                });
            </script>
            <script>
                load_anak_kerja();

                function load_anak_kerja() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('cabutSpesial.load_anak_kerja') }}",
                        dataType: 'json',
                        success: function(r) {
                            $('#anakBelum').text(r.total_anak);
                            $('#load_anak_cabut').html(r.anak_spesial);
                        }
                    });
                }
                load_anak();

                function load_anak() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('cabutSpesial.load_anak_kerja_belum') }}",
                        success: function(r) {
                            pencarian('pencarian', 'table_cbt_spesial')
                            $('#load_anak_cabut').html(r);
                            inputChecked('cekSemuaTutup', 'cek')
                        }
                    });
                }
                load_gr_awal();

                function load_gr_awal() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('cabutSpesial.load_ambil_cbt') }}",
                        success: function(r) {
                            pencarian('pencarian', 'table_cbt_spesial')
                            $('#load_gr_awal').html(r);
                            $(".select2-add").select2({
                                dropdownParent: $('#tambah_awal .modal-content'),


                            });
                        }
                    });
                }
                $('.tambahAnak').click(function() {
                    load_anak()
                });
                $('#save_absen').submit(function(e) {
                    e.preventDefault();

                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    var formData = $(this).serialize();
                    formData += "&_token=" + csrfToken;

                    $.ajax({
                        type: "POST",
                        url: "{{ route('cabutSpesial.save_absen') }}",
                        data: formData,
                        success: function(response) {
                            alertToast('sukses', 'Berhasil ditambahkan');
                            load_gr_awal();
                            $('#tambah_awal').modal('show');
                            $('#tambahAnak').modal('hide');
                            load_anak_kerja();
                        },
                    });

                });

                $(document).on('change', '.pilihBox', function() {
                    var no_box = $(this).val()
                    var count = $(this).attr('count')
                    $.ajax({
                        type: "GET",
                        url: "get_box_sinta",
                        data: {
                            no_box: no_box
                        },
                        dataType: "json",
                        success: function(r) {
                            console.log(r)
                            $(".setGr" + count).val(r.gr_awal - r.gr_cabut)
                            $(".setPcs" + count).val(r.pcs_awal - r.pcs_cabut)
                        }
                    });
                })

                $(document).on('change', '.pilihTarget', function() {
                    var count = $(this).attr('count');
                    var id_target = $(this).val();
                    $.ajax({
                        type: "get",
                        url: "{{ route('cabutSpesial.getrp_target') }}",
                        data: {
                            id_target: id_target,
                        },
                        dataType: "json",
                        success: function(r) {
                            $('.pcsTarget' + count).val(r['pcs']);
                            $('.rupiahBiasa' + count).val(r['rupiah']);
                            var pcs_target = r['pcs'];
                            var rupiahBiasa = r['rupiah'];


                            var pcs = $('.setPcs' + count).val();

                            var rp = parseFloat(pcs) * (parseFloat(rupiahBiasa) / parseFloat(pcs_target));
                            $('.setRupiah' + count).val(rp);
                        }
                    });
                });
                $(document).on('change', '.pilihBox', function() {
                    var count = $(this).attr('count');
                    var no_box = $(this).val();
                    $.ajax({
                        type: "get",
                        url: "{{ route('cabutSpesial.get_box') }}",
                        data: {
                            no_box: no_box,
                        },
                        dataType: "json",
                        success: function(r) {

                            $('.setPcs' + count).val(r['pcs']);
                            $('.setGr' + count).val(r['gram']);

                            var setpcs = r['pcs'];

                            var rupiah = $('.rupiahBiasa' + count).val()
                            var pcs = $('.pcsTarget' + count).val()

                            var rp = parseFloat(setpcs) * (parseFloat(rupiah) / parseFloat(pcs));
                            $('.setRupiah' + count).val(rp);


                        }
                    });
                });

                $(document).on('keyup', '.setPcs', function() {
                    var count = $(this).attr('count');
                    var setpcs = $(this).val();
                    var rupiah = $('.rupiahBiasa' + count).val()
                    var pcs = $('.pcsTarget' + count).val()

                    var rp = parseFloat(setpcs) * (parseFloat(rupiah) / parseFloat(pcs));
                    $('.setRupiah' + count).val(rp);



                });
                $(document).on('click', '.remove_baris', function() {

                    var id_absen = $(this).attr('id_absen');

                    $.ajax({
                        type: "get",
                        url: "{{ route('cabutSpesial.delete_absen') }}",
                        data: {
                            id_absen: id_absen
                        },
                        success: function(r) {
                            alertToast('sukses', 'Data berhasil dihapus');
                            load_anak_kerja();
                        }
                    });

                    var delete_row = $(this).attr("count");
                    $(".baris" + delete_row).remove();



                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
