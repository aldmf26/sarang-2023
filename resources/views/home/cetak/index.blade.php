<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <a href="{{ route('cetak.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="float-end btn btn-sm btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>
        <x-theme.button href="#" modal="Y" idModal="gaji_global" icon="fa-file-excel" addClass="float-end"
            teks="Export Gaji Global" />
        <x-theme.button href="#" modal="Y" idModal="import" icon="fa-upload" addClass="float-end"
            teks="Import" />

        {{-- <x-theme.button href="#" modal="Y" idModal="tambah_awal" icon="fa-plus" addClass="float-end tbh_cetak"
            teks="Cetak" /> --}}
        <a href="#" data-bs-target="#tambah_awal" class="btn btn-sm btn-primary me-2 float-end tbh_cetak"
            data-bs-toggle="modal"><i class="fas fa-plus"></i> Cetak <span class="badge bg-danger"
                id="anakBelum"></span></a>

        <a href="#" data-bs-target="#tambahAnak" data-bs-toggle="modal"
            class="btn tambahAnak btn-primary btn-sm float-end me-2"><i class="fas fa-plus"></i> kry kerja
        </a>
        <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end"
            teks="kry baru" />
        <form action="{{ route('cetak.import') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-theme.modal title="Cetak" idModal="import" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">File</label>
                        <input type="file" class="form-control" name="file">
                    </div>
                </div>

            </x-theme.modal>
        </form>
        <form action="{{ route('cetak.export_gaji_global') }}" method="get" enctype="multipart/form-data">
            <x-theme.modal title="Gaji Global Anak" idModal="gaji_global" btnSave="Y">
                <div class="row">
                    <div class="col-lg-6">
                        <select name="bulan" id="" class="select2_add">
                            <option value="">Pilih Bulan</option>
                            @foreach ($bulan as $b)
                                <option value="{{ $b->bulan }}">{{ $b->nm_bulan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <select name="tahun" id="" class="select2_add">
                            <option value="">Pilih Tahun</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t->tahun }}">{{ $t->tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </x-theme.modal>
        </form>

    </x-slot>

    <x-slot name="cardBody">
        <style>
            .btn-xs {
                font-size: 10px;
                /* Sesuaikan ukuran font sesuai keinginan Anda */
                padding: 4px 8px;
                /* Sesuaikan padding sesuai keinginan Anda */
            }

            td {
                font-size: 12px;
            }

            th {
                font-size: 12px;
            }

            .form-control {
                font-size: 12px !important;
            }
        </style>
        <section class="row">
            <div id="load-cetak"></div>
        </section>
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

        <form id="save_kerja">
            <x-theme.modal idModal="tambahAnak" title="Tambah anak" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="load_anak_cetak"></div>
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <form id="save_awal">
            <x-theme.modal idModal="tambah_awal" title="Tambah anak" size="modal-lg-max" btnSave="Y">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="load_ambil_awal"></div>
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <x-theme.modal idModal="inputAkhir" title="Input Akhir Cetak" btnSave="T" size="modal-lg-max-nanda">
            <div class="row">
                <p class="fw-bold fst-italic">Note : Untuk pcs cuc tidak dibayar</p>
                <div class="col-lg-12">
                    <div id="load_ambil_akhir"></div>
                </div>
            </div>
        </x-theme.modal>

        <form id="save_selesai">
            <x-theme.modal idModal="selesai" title="Selesai" btnSave="Y" color_header="modal-success">
                <div class="row">
                    <div class="col-lg-12">
                        <p class="text-center">Apakah anda yakin ingin menyelesaikannya ?</p>
                        <p class="text-center fw-bold">Note : </p>
                        <p class="text-center fw-bold fst-italic">Data yang sudah diselesaikan akan hilang dari form
                            input
                        </p>
                        <input type="hidden" name="id_cetak" class="cetak">
                    </div>
                </div>
            </x-theme.modal>
        </form>


        @section('scripts')
            <script>
                $(document).on('click', '.selesai', function() {
                    var id_cetak = $(this).attr('id_cetak');

                    $('.cetak').val(id_cetak);
                });
                $(document).on('click', '.btnKembaliTambahCetak', function() {
                    $('#tambahAnak').modal('show');
                    $('#tambah_awal').modal('hide');
                });


                loadTotalAnak();

                function loadTotalAnak() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('cetak.get_total_anak') }}",
                        success: function(response) {
                            var totalAnak = response.total_anak;
                            $('#anakBelum').text(totalAnak);
                        }
                    });
                }
                load_anak();

                function load_anak() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('cetak.load_anak_kerja_belum') }}",
                        success: function(r) {
                            pencarian('pencarian', 'table_cbt_spesial')
                            $('#load_anak_cetak').html(r);
                            inputChecked('cekSemuaTutup', 'cek')
                        }
                    });
                }
                load_gr_awal();

                function load_gr_awal() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('cetak.ambil_awal') }}",
                        success: function(r) {
                            $('#load_ambil_awal').html(r);
                            $(".select2-add").select2({
                                dropdownParent: $('#tambah_awal .modal-content'),


                            });
                        }
                    });
                }
                $(document).on('click', '.tbh_cetak', function() {
                    load_gr_awal();
                });

                $(document).on('click', '.hapusCetakRow', function() {
                    var id_cetak = $(this).attr('id_cetak');

                    $.ajax({
                        type: "get",
                        url: "{{ route('cetak.delete_awal_cetak') }}",
                        data: {
                            id_cetak: id_cetak
                        },
                        success: function(r) {
                            alertToast('sukses', 'Berhasil dihapus');
                            load_gr_awal();
                        }
                    });
                });


                $(document).on('change', '.pilihkelas', function() {
                    var count = $(this).attr('count');
                    var id_kelas_cetak = $(this).val();


                    $.ajax({
                        type: "get",
                        url: "{{ route('cetak.get_kelas') }}",
                        data: {
                            id_kelas_cetak: id_kelas_cetak
                        },
                        success: function(r) {
                            $('.rp_pcs' + count).val(r);

                            var target = r;
                            var pcs = $('.pcs_awal' + count).val();
                            var total = parseFloat(target) * parseFloat(pcs);
                            $('.total_rp' + count).val(total);
                        }
                    });
                });
                $('#save_kerja').submit(function(e) {
                    e.preventDefault();

                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    var formData = $(this).serialize();
                    formData += "&_token=" + csrfToken;

                    $.ajax({
                        type: "POST",
                        url: "{{ route('cetak.save_kerja') }}",
                        data: formData,
                        success: function(response) {
                            alertToast('sukses', 'Berhasil ditambahkan');
                            load_gr_awal();
                            $('#tambah_awal').modal('show');
                            $('#tambahAnak').modal('hide');
                            // load_anak_kerja();
                        },
                    });

                });
                $('#save_awal').submit(function(e) {
                    e.preventDefault();

                    var isValid = true;
                    $('tbody tr').each(function(index, row) {
                        var tgl = $(row).find('input[name="tgl[]"]').val();
                        var no_box = $(row).find('select[name="no_box[]"]').val();
                        var paket = $(row).find('select[name="paket[]"]').val();
                        var pcs_awal = $(row).find('input[name="pcs_awal[]"]').val();
                        var gr_awal = $(row).find('input[name="gr_awal[]"]').val();
                        if (tgl === '' || no_box === '' || paket === '' || pcs_awal === '' || gr_awal ===
                            '') {
                            isValid = false;
                            alertToast('error', 'Isi semua data terlebih dahulu');
                            return false;
                        }
                    });

                    if (isValid) {
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        var formData = $(this).serialize();
                        formData += "&_token=" + csrfToken;

                        $.ajax({
                            type: "POST",
                            url: "{{ route('cetak.add_target') }}",
                            data: formData,
                            success: function(response) {
                                alertToast('sukses', 'Berhasil ditambahkan');
                                $('#tambah_awal').modal('hide');
                                $('#tambahAnak').modal('hide');
                                load_cetak();
                            },
                        });
                    }

                });



                $(document).on('click', '.akhir', function() {
                    var id_cetak = $(this).attr('id_cetak');
                    $.ajax({
                        type: "get",
                        url: "{{ route('cetak.akhir') }}",
                        data: {
                            id_cetak: id_cetak,
                        },
                        success: function(r) {
                            $('#akhir_detail').html(r)
                        }
                    });
                });
                $(document).on('click', '.selesai', function() {
                    var id_cetak = $(this).attr('id_cetak');

                    $('.cetak').val(id_cetak);
                });

                load_cetak();

                function load_cetak() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('cetak.get_cetak') }}",
                        success: function(r) {
                            $("#load-cetak").html(r);
                            $('#tableHalaman').DataTable({
                                "searching": true,
                                scrollY: '400px',
                                scrollX: true,
                                scrollCollapse: true,
                                "autoWidth": false,
                                "paging": false,
                                "ordering": false
                            });
                        }
                    });




                }

                function input_akhir() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('cetak.input_akhir') }}",
                        success: function(r) {
                            $("#load_ambil_akhir").html(r);
                            $(".select2-add").select2({
                                dropdownParent: $('#inputAkhir .modal-content'),
                            });
                        }
                    });
                }

                $(document).on('click', '.inputAkhir', function() {
                    input_akhir();
                });

                $(document).on("click", ".btn_simpan", function() {

                    var row = $(this).closest("tr"); // Mendapatkan baris terkait dengan tombol "Simpan"
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    var formData = row.find('input, select').serialize();
                    formData += "&_token=" + csrfToken;


                    // Kirim data ke server menggunakan AJAX
                    $.ajax({
                        url: "{{ route('cetak.save_akhir') }}", // Ganti dengan URL endpoint penyimpanan data
                        method: "POST",
                        data: formData,
                        success: function(response) {

                            var savedRowId = row.data('id');

                            // Muat kembali data baris yang disimpan
                            $.ajax({
                                url: "{{ route('cetak.load_row') }}", // Ganti dengan URL endpoint untuk memuat satu baris
                                method: "GET",
                                data: {
                                    id: savedRowId
                                }, // Sertakan ID baris yang disimpan
                                success: function(data) {
                                    var updatedRow = $(data);
                                    row.replaceWith(
                                        updatedRow
                                    );
                                    $(".select2-add").select2({
                                        dropdownParent: $('#inputAkhir .modal-content'),
                                    });
                                    alertToast('sukses', 'Berhasil ditambahkan');
                                },
                            });

                            load_cetak();
                        },
                    });
                });
                $(document).on('click', '.btn_hapus', function() {
                    var id_cetak = $(this).attr('id_cetak');
                    var konfirmasi = confirm("Apakah Anda yakin?");

                    if (konfirmasi) {
                        $.ajax({
                            type: "get",
                            url: "{{ route('cetak.delete_cetak') }}",
                            data: {
                                id_cetak: id_cetak
                            },
                            success: function(response) {
                                alertToast('sukses', 'Berhasil dihapus');
                                load_cetak();
                                input_akhir();
                            }
                        });
                    } else {

                    }

                });
                $('#save_selesai').submit(function(e) {
                    e.preventDefault();

                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    var formData = $(this).serialize();
                    formData += "&_token=" + csrfToken;

                    $.ajax({
                        type: "Post",
                        url: "{{ route('cetak.selesai_cetak') }}",
                        data: formData,
                        success: function(response) {
                            alertToast('sukses', 'Data berhasil diselesaikan');
                            load_cetak();
                            input_akhir();
                            $('#inputAkhir').modal('show');
                            $('#selesai').modal('hide');
                        },
                    });

                });
            </script>
            <script>
                $(document).on('keyup', '.pcs_awal', function() {
                    var count = $(this).attr('count');
                    var target = $('.rp_pcs' + count).val();
                    var pcs_awal = $(this).val();




                    var batas_susut = $('.batas_susut' + count).val();
                    var denda_susut = $('.denda_susut' + count).val();

                    var gr_awal = $('.gr_awal' + count).val();
                    var gr_akhir = $('.gr_akhir' + count).val();
                    var gr_cu = $('.gr_cu' + count).val();

                    var susut = (1 - ((parseFloat(gr_akhir) + parseFloat(gr_cu)) / parseFloat(gr_awal))) * 100;
                    var susut2 = Math.round(susut);
                    if (susut2 >= batas_susut) {
                        var denda = susut2 * denda_susut;
                    } else {
                        var denda = 0;
                    }
                    var pcs_hcr = $('.pcs_hcr' + count).val();
                    var denda_hcr = $('.denda_hcr' + count).val();


                    var rp_hcr = parseFloat(pcs_hcr) * parseFloat(denda_hcr);


                    var total = parseFloat(target) * parseFloat(pcs_awal) - parseFloat(denda) - rp_hcr;
                    $('.ttl_rp' + count).text(total);

                });
                $(document).on('keyup', '.pcs_awal', function() {
                    var count = $(this).attr('count');
                    var target = parseFloat($('.rp_pcs' + count).val()); // Ubah string ke angka
                    var pcs = parseFloat($(this).val()); // Ubah string ke angka
                    var pcs_awal_ctk = parseFloat($('.pcs_awal_ctk' + count).val()); // Ubah string ke angka
                    var pcs_cu = parseFloat($('.pcs_cu' + count).val()); // Ubah string ke angka

                    var gr_awal = parseFloat($('.gr_awal' + count).val());
                    var gr_cu = parseFloat($('.gr_cu' + count).val());
                    var gr_akhir = parseFloat($('.gr_akhir' + count).val());

                    if (pcs_awal_ctk - pcs_cu - pcs < 0 || pcs_cu + pcs < pcs_awal_ctk || gr_akhir + gr_cu > gr_awal) {
                        $('.btn_simpan' + count).hide();
                        $('.selesai' + count).hide();
                    } else {
                        $('.btn_simpan' + count).show();
                        $('.selesai' + count).show();
                    }

                    var total = target * pcs; // Tidak perlu mengonversi ke float lagi

                    $('.total_rp' + count).val(total);
                });

                $(document).on('keyup', '.pcs_cu', function() {
                    var count = $(this).attr('count');
                    var pcs_cu = parseFloat($(this).val());
                    var pcs_awal_ctk = parseFloat($('.pcs_awal_ctk' + count).val());
                    var pcs = parseFloat($('.pcs_awal' + count).val());

                    var gr_awal = parseFloat($('.gr_awal' + count).val());
                    var gr_cu = parseFloat($('.gr_cu' + count).val());
                    var gr_akhir = parseFloat($('.gr_akhir' + count).val());

                    if (pcs_awal_ctk - pcs_cu - pcs < 0 || pcs_cu + pcs < pcs_awal_ctk || gr_akhir + gr_cu > gr_awal) {
                        $('.btn_simpan' + count).hide();
                        $('.selesai' + count).hide();
                    } else {
                        $('.btn_simpan' + count).show();
                        $('.selesai' + count).show();
                    }


                });
                $(document).on('keyup', '.gr_akhir', function() {
                    var count = $(this).attr('count');
                    var gr_awal = $('.gr_awal' + count).val();
                    var gr_cu = $('.gr_cu' + count).val();
                    var gr_akhir = $(this).val();




                    var susut = (1 - ((parseFloat(gr_akhir) + parseFloat(gr_cu)) / parseFloat(gr_awal))) * 100;

                    var batas_susut = $('.batas_susut' + count).val();
                    var denda_susut = $('.denda_susut' + count).val();

                    var target = $('.rp_pcs' + count).val();
                    var pcs_awal = $('.pcs_awal' + count).val();
                    var pcs_awal_ctk = parseFloat($('.pcs_awal_ctk' + count).val()); // Ubah string ke angka
                    var pcs_cu = parseFloat($('.pcs_cu' + count).val()); // Ubah string ke angka



                    var susut2 = Math.round(susut);
                    if (susut2 >= batas_susut) {
                        var denda = susut2 * denda_susut;
                    } else {
                        var denda = 0;
                    }
                    $('.susut' + count).text(susut2 + '%');

                    var pcs_hcr = $('.pcs_hcr' + count).val();
                    var denda_hcr = $('.denda_hcr' + count).val();

                    var rp_hcr = parseFloat(pcs_hcr) * parseFloat(denda_hcr);

                    var total = parseFloat(target) * parseFloat(pcs_awal) - parseFloat(denda) - rp_hcr;
                    $('.ttl_rp' + count).text(total);

                    if (pcs_awal_ctk - pcs_cu - parseFloat(pcs_awal) < 0 || pcs_cu + parseFloat(pcs_awal) < pcs_awal_ctk ||
                        parseFloat(gr_akhir) + parseFloat(gr_cu) > parseFloat(gr_awal)) {
                        $('.btn_simpan' + count).hide();
                        $('.selesai' + count).hide();
                    } else {
                        $('.btn_simpan' + count).show();
                        $('.selesai' + count).show();
                    }

                });
                $(document).on('keyup', '.gr_cu', function() {
                    var count = $(this).attr('count');
                    var gr_awal = $('.gr_awal' + count).val();
                    var gr_akhir = $('.gr_akhir' + count).val();
                    var gr_cu = $(this).val();


                    var susut = (1 - ((parseFloat(gr_akhir) + parseFloat(gr_cu)) / parseFloat(gr_awal))) * 100;

                    var batas_susut = $('.batas_susut' + count).val();
                    var denda_susut = $('.denda_susut' + count).val();

                    var target = $('.rp_pcs' + count).val();
                    var pcs_awal = $('.pcs_awal' + count).val();
                    var pcs_awal_ctk = parseFloat($('.pcs_awal_ctk' + count).val()); // Ubah string ke angka
                    var pcs_cu = parseFloat($('.pcs_cu' + count).val()); // Ubah string ke angka

                    var susut2 = Math.round(susut);
                    if (susut2 >= batas_susut) {
                        var denda = susut2 * denda_susut;
                    } else {
                        var denda = 0;
                    }
                    $('.susut' + count).text(susut2 + '%');

                    var pcs_hcr = $('.pcs_hcr' + count).val();
                    var denda_hcr = $('.denda_hcr' + count).val();

                    var rp_hcr = parseFloat(pcs_hcr) * parseFloat(denda_hcr);

                    var total = parseFloat(target) * parseFloat(pcs_awal) - parseFloat(denda) - rp_hcr;
                    $('.ttl_rp' + count).text(total);

                    if (pcs_awal_ctk - pcs_cu - parseFloat(pcs_awal) < 0 || pcs_cu + parseFloat(pcs_awal) < pcs_awal_ctk ||
                        parseFloat(gr_akhir) + parseFloat(gr_cu) > parseFloat(gr_awal)) {
                        $('.btn_simpan' + count).hide();
                        $('.selesai' + count).hide();
                    } else {
                        $('.btn_simpan' + count).show();
                        $('.selesai' + count).show();
                    }

                });
                $(document).on('keyup', '.pcs_hcr', function() {
                    var count = $(this).attr('count');
                    var gr_awal = $('.gr_awal' + count).val();
                    var gr_akhir = $('.gr_akhir' + count).val();
                    var gr_cu = $('.gr_cu' + count).val();

                    var susut = (1 - ((parseFloat(gr_akhir) + parseFloat(gr_cu)) / parseFloat(gr_awal))) * 100;
                    var batas_susut = $('.batas_susut' + count).val();
                    var denda_susut = $('.denda_susut' + count).val();

                    var target = $('.rp_pcs' + count).val();
                    var pcs_awal = $('.pcs_awal' + count).val();

                    var susut2 = Math.round(susut);
                    if (susut2 >= batas_susut) {
                        var denda = susut2 * denda_susut;
                    } else {
                        var denda = 0;
                    }
                    $('.susut' + count).text(susut2 + '%');
                    var pcs_hcr = $(this).val();
                    var denda_hcr = $('.denda_hcr' + count).val();

                    var rp_hcr = parseFloat(pcs_hcr) * parseFloat(denda_hcr);

                    var total = parseFloat(target) * parseFloat(pcs_awal) - parseFloat(denda) - rp_hcr;
                    $('.ttl_rp' + count).text(total);

                });

                $('.btn_tutup').hide(); // Menampilkan tombol jika checkbox dicentang
                $(document).on('change', '.cekTutup, #cekSemuaTutup', function() {
                    $('.btn_tutup').removeClass('d-none');

                    $('.btn_tutup').toggle(this.checked);
                })

                $(document).on('click', '.btn_tutup', function() {
                    var tipe = $(this).attr('tipe')
                    var selectedRows = [];
                    $('input[name="cekTutup[]"]:checked').each(function() {
                        var anakId = $(this).attr('id_cetak');
                        selectedRows.push(anakId);
                    });
                    if (confirm('Apakah anda yakin ?')) {
                        $.ajax({
                            type: "GET",
                            url: "{{ route('cetak.ditutup') }}",
                            data: {
                                datas: selectedRows,
                                tipe: tipe
                            },
                            success: function(r) {
                                alertToast('sukses', 'Berhasil save')
                                load_cetak();
                                $('.btn_tutup').hide();
                            }
                        });
                    }

                })
                load_anak_nopengawas()

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
                load_anak()

                function load_anak() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_anak') }}",
                        success: function(r) {
                            $("#load_anak").html(r);
                        }
                    });
                }
                $(document).on('click', '#add_anak', function() {
                    var id_anak = $(".anakNoPengawas").val()
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.add_delete_anak') }}?id_anak=" + id_anak,
                        success: function(r) {
                            alertToast('sukses', 'Berhasil tambah anak')
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
                            alertToast('sukses', 'Berhasil tambah anak')
                            load_anak()
                            load_anak_nopengawas()
                        }
                    });
                })
            </script>

            <script>
                $(document).on('change', '.tgl_urut', function() {
                    var tgl = $(this).val();
                    var count = $(this).attr('count');
                    var no = count;
                    $('.tgl_urut').each(function() {
                        no++;
                        $('.tgl_urut' + no).val(tgl);
                    });

                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
