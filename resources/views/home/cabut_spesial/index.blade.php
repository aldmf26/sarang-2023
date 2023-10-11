<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6>

            </div>
            <div class="col-lg-6">
                {{--
        <x-theme.button href="{{ route('cabutSpesial.add') }}" icon="fa-plus" addClass="float-end" teks="Tambah" /> --}}
                <x-theme.button href="#" icon="fa-window-close" variant="danger" addClass="float-end btn_tutup"
                    teks="Tutup" />
                <x-theme.button href="#" modal="Y" idModal="history" icon="fa-history" variant="primary"
                    addClass="float-end history" teks="History" />

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
            </div>
            <div class="col-lg-12">
                <hr style="border: 2px solid #435EBE">

            </div>
        </div>

        @include('home.cabut.nav')
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div id="load_cabut"></div>
        </section>


        <x-theme.modal idModal="inputAkhir" title="tambah cabut spesial akhir" btnSave="T" size="modal-lg-max">
            <div id="load_modal_akhir"></div>
        </x-theme.modal>


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
        <x-theme.modal idModal="history" title="History Cabut Spesial" btnSave="T" size="modal-lg-max">
            <div class="row">
                <div class="col-lg-12">
                    <div id="load_history"></div>
                </div>
            </div>
        </x-theme.modal>
        <form action="{{ route('cabutSpesial.create') }}" method="post">
            @csrf
            <x-theme.modal idModal="tambah_awal" size="modal-lg-max" title="Tambah data cabut spesial" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="load_gr_awal"></div>
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <form id="save_selesai">
            <x-theme.modal idModal="selesai" title="Selesai" btnSave="Y" color_header="modal-success">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="text-center">Apakah anda yakin ingin menyelesaikannya ?</p>
                        <p class="text-center fw-bold">Note : </p>
                        <p class="text-center fw-bold fst-italic">Data yang sudah diselesaikan akan hilang dari form
                            input
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
                load_cabut()

                function load_anak() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_anak') }}",
                        success: function(r) {
                            $("#load_anak").html(r);
                        }
                    });
                }

                function load_cabut() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabutSpesial.load_cabut') }}",
                        success: function(r) {
                            $("#load_cabut").html(r);
                            $('#tableHalaman').DataTable({
                                "searching": true,
                                scrollY: '400px',
                                scrollX: true,
                                scrollCollapse: true,
                                "autoWidth": false,
                                "paging": false,
                                "ordering": false
                            });
                            inputChecked('cekSemuaTutup2', 'cekTutup2')

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

                function load_akhir() {
                    var id_cabut = $(this).attr('id_cabut')
                    $.ajax({
                        type: "GET",
                        url: "cabutSpesial/load_modal_akhir",
                        data: {
                            id_cabut: id_cabut,
                        },
                        success: function(r) {
                            $("#load_modal_akhir").html(r);
                            $(".select3").select2({
                                dropdownParent: $('#inputAkhir .modal-content'),
                            })
                        }
                    });
                }
                $(document).on('click', '.inputAkhir', function() {
                    load_akhir()
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


                $('#save_selesai').submit(function(e) {
                    e.preventDefault();

                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    var formData = $(this).serialize();
                    formData += "&_token=" + csrfToken;

                    $.ajax({
                        type: "Get",
                        url: "{{ route('cabutSpesial.selesai_cabut') }}",
                        data: formData,
                        success: function(response) {
                            alertToast('sukses', 'Data berhasil diselesaikan');
                            load_cabut()
                            load_akhir()
                            $('#inputAkhir').modal('show');
                            $('#selesai').modal('hide');
                        },
                    });

                });

                $(document).on("click", ".btn_simpan", function() {

                    var row = $(this).closest("tr"); // Mendapatkan baris terkait dengan tombol "Simpan"
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    var formData = row.find('input, select').serialize();
                    formData += "&_token=" + csrfToken;

                    // Kirim data ke server menggunakan AJAX
                    $.ajax({
                        url: "{{ route('cabutSpesial.input_akhir') }}", // Ganti dengan URL endpoint penyimpanan data
                        method: "POST",
                        data: formData,
                        success: function(response) {
                            alertToast('sukses', 'Berhasil ditambahkan');
                            var savedRowId = row.data('id');

                            // Muat kembali data baris yang disimpan
                            $.ajax({
                                url: "{{ route('cabutSpesial.load_row') }}", // Ganti dengan URL endpoint untuk memuat satu baris
                                method: "GET",
                                data: {
                                    id: savedRowId
                                }, // Sertakan ID baris yang disimpan
                                success: function(data) {
                                    // Update baris yang ada di tabel dengan data yang dimuat
                                    var updatedRow = $(
                                        data); // Ubah data yang dimuat menjadi elemen jQuery
                                    row.replaceWith(
                                        updatedRow
                                    ); // Gantikan baris yang ada dengan baris yang dimuat
                                    $(".select3").select2({
                                        dropdownParent: $('#inputAkhir .modal-content'),
                                    })
                                },
                            });

                            load_cabut()
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
                            var roundedRp = Math.round(rp);
                            $('.setRupiah' + count).val(roundedRp);
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
                            var roundedRp = Math.round(rp);
                            $('.setRupiah' + count).val(roundedRp);


                        }
                    });
                });

                $(document).on('keyup', '.setPcs', function() {
                    var count = $(this).attr('count');
                    var setpcs = $(this).val();
                    var rupiah = $('.rupiahBiasa' + count).val()
                    var pcs = $('.pcsTarget' + count).val()

                    var rp = parseFloat(setpcs) * (parseFloat(rupiah) / parseFloat(pcs));

                    var roundedRp = Math.round(rp);
                    $('.setRupiah' + count).val(roundedRp);
                });
                $(document).on('keyup', '.pcs_hcr', function() {
                    var count = $(this).attr('count');
                    var pcs = $(this).val();
                    if (pcs > 0) {
                        var ttl_rp = 0;
                    } else {
                        var rp = $('.rp_target' + count).val();
                        var ttl_rp = parseFloat(rp);
                    }

                    var total = ttl_rp.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    $('.ttl_rp' + count).text(total);
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

                $('.btn_tutup').hide();
                $(document).on('change', '.cekTutup2, #cekSemuaTutup2', function() {
                    $('.btn_tutup').toggle(this.checked);
                })

                $(document).on('click', '.btn_tutup', function() {
                    var selectedRows = [];
                    $('input[name="cekTutup[]"]:checked').each(function() {
                        var anakId = $(this).attr('id_cabut');
                        selectedRows.push(anakId);
                    });
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabutSpesial.ditutup') }}",
                        data: {
                            datas: selectedRows
                        },
                        success: function(r) {
                            alertToast('sukses', 'Berhasil save')
                            load_cabut()
                            $('.btn_tutup').hide();
                        }
                    });

                })
                $(document).on('click', '.history', function() {
                    $.ajax({
                        type: "get",
                        url: "url",
                        data: "data",
                        dataType: "dataType",
                        success: function(response) {

                        }
                    });
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
