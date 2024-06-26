<x-theme.app title="{{ $title }}" table="Y" sizeCard="11">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="mt-1">{{ $title }}</h6>
            <div class="d-flex gap-1">
                <x-theme.button href="#" modal="Y" idModal="history" icon="fa-history" addClass="history"
                    teks="History" />
                <div>
                    <x-theme.btn_filter />
                </div>
                <x-theme.button href="#" modal="Y" idModal="anak" icon="fa-plus" teks="kry baru" />
                <x-theme.button modal="Y" idModal="tambah2" href="#" icon="fa-plus" teks="Eo" />
                <a href="{{ route('eo.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                <a href="{{ route('cabut.gudang') }}" style="color: white;background-color: #D722A9;"
                    class="btn btn-sm">
                    <i class="fas fa-clipboard-list"></i> Gudang
                </a>

                {{-- <a href="#" data-bs-target="#tambahAnak" data-bs-toggle="modal"
                    class="btn btn-primary btn-sm float-end me-2"><i class="fas fa-plus"></i> kry kerja <span
                        class="badge bg-danger" id="anakBelum"></span>
                </a> --}}

            </div>

        </div>
        <hr class="mt-2" style="border: 2px solid #435EBE">
        @include('home.cabut.nav')
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div id="loadHalaman"></div>
        </section>
        <form id="createCabut">
            @csrf
            <x-theme.modal idModal="tambah2" title="tambah eo" btnSave="Y" size="modal-lg">
                <div id="load_tambah_cabut"></div>
            </x-theme.modal>
        </form>
        <form id="createTambahAnakCabut">
            @csrf
            <x-theme.modal idModal="tambahAnak" title="tambah anak" btnSave="Y" size="modal-md">
                <div id="load_tambah_anak"></div>
            </x-theme.modal>
        </form>
        <style>
            .modal-eo-akhir {
                max-width: 1000px;
            }
        </style>
        <form id="createCabutAkhir">
            @csrf
            <x-theme.modal idModal="inputAkhir" title="tambah cabut akhir" btnSave="T" size="modal-eo-akhir">
                <div id="load_modal_akhir"></div>
            </x-theme.modal>
        </form>
        <x-theme.modal idModal="history" title="History Eo" btnSave="T" size="modal-eo-akhir">
            <div id="load_history"></div>
        </x-theme.modal>

        {{-- <form action="{{ route('eo.input_akhir') }}" method="post">
            @csrf
            <x-theme.modal idModal="inputAkhir" title="tambah cabut akhir" size="modal-lg" btnSave="Y">
                <div id="load_modal_akhir"></div>
            </x-theme.modal>
        </form> --}}

        <form action="{{ route('sortir.create_anak') }}" method="post">
            @csrf
            <x-theme.modal idModal="anak" title="tambah Anak" btnSave="Y">
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
    </x-slot>
    @section('scripts')
        <script>
            function plusCabut(count, classPlus, url) {
                $(document).on("click", "." + classPlus, function() {
                    count = count + 1;
                    $.ajax({
                        url: `${url}?count=` + count,
                        type: "GET",
                        success: function(data) {
                            $("#" + classPlus).append(data);
                            $(".select2-tambah").select2({
                                dropdownParent: $(`#tambah2 .modal-content`)
                            });
                        },
                    });
                });

                $(document).on('click', '.remove_baris', function() {
                    var delete_row = $(this).attr("count");
                    $(".baris" + delete_row).remove();

                })
            }
            plusCabut(1, 'tbh_baris', "eo/tbh_baris")
            loadHalaman()
            loadTambahAnak()
            loadTambahcabut()
            // kry kerja
            function updateAnakBelum() {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('eo.updateAnakBelum') }}", // Sesuaikan dengan URL rute yang telah Anda buat
                    dataType: 'json',
                    success: function(response) {
                        // Perbarui nilai di dalam <span> dengan ID "anakBelum"
                        $('#anakBelum').text(response.anakBelum);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            function loadTambahAnak() {
                updateAnakBelum();

                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.load_tambah_anak') }}",
                    success: function(r) {
                        $("#load_tambah_anak").html(r);
                        pencarian('pencarian', 'tablealdi')
                        inputChecked('cekSemuaTutup', 'cekTutup')
                    }
                });
            }
            $(document).on('submit', '#createTambahAnakCabut', function(e) {
                e.preventDefault();
                var selectedRows = [];
                // Loop melalui semua checkbox yang memiliki atribut 'name="cek[]"'
                $('input[name="cek[]"]:checked').each(function() {
                    // Ambil ID anak dari atribut 'data-id' atau atribut lain yang sesuai dengan data Anda

                    // Mengambil ID dari kolom pertama (kolom #)
                    var anakId = $(this).closest('tr').find('td:eq(0)').text();

                    // Tambahkan ID anak ke dalam array
                    selectedRows.push(anakId);
                });
                var tipe = $('.tipe').val()
                $.ajax({
                    type: "get",
                    url: "{{ route('eo.createTambahAnakCabut') }}",
                    data: {
                        rows: selectedRows,
                        tipe: tipe
                    },
                    success: function(r) {
                        alertToast('sukses', 'Berhasil tambah')
                        $('#tambahAnak').modal('hide')
                        loadTambahcabut()
                        loadHalaman()
                        loadTambahAnak()
                        $('#tambah2').modal('show')
                    }
                });
            })
            $(document).on('click', '.history', function(e) {
                e.preventDefault()
                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.history') }}",
                    success: function(r) {
                        $("#load_history").html(r);

                        $('#tblHistory').DataTable({
                            "searching": true,
                            "autoWidth": false,
                            "paging": true,
                            "ordering": true
                        });
                    }
                });
            })

            function loadHalaman() {
                updateAnakBelum();
                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.load_halaman') }}",
                    data: {
                        tgl1: "{{ $tgl1 }}",
                        tgl2: "{{ $tgl2 }}",
                    },
                    success: function(r) {
                        $("#loadHalaman").html(r);
                        $('#tableHalaman').DataTable({
                            "searching": true,
                            scrollY: '400px',
                            scrollX: true,
                            scrollCollapse: true,
                            "autoWidth": false,
                            "paging": false,
                            "ordering": false
                        });
                        inputChecked('cekSemuaTutup', 'cekTutup')
                    }
                });


            }
            $('.btn_tutup').hide(); // Menampilkan tombol jika checkbox dicentang
            $(document).on('change', '.cekTutup, #cekSemuaTutup', function() {
                $('.btn_tutup').removeClass('d-none');

                $('.btn_tutup').toggle(this.checked);
            })

            $(document).on('click', '.btn_tutup', function() {
                var tipe = $(this).attr('tipe')
                var selectedRows = [];
                // Loop melalui semua checkbox yang memiliki atribut 'name="cek[]"'
                $('input[name="cekTutup[]"]:checked').each(function() {
                    // Ambil ID anak dari atribut 'data-id' atau atribut lain yang sesuai dengan data Anda

                    // Mengambil ID dari kolom pertama (kolom #)
                    var anakId = $(this).attr('id_cabut');

                    // Tambahkan ID anak ke dalam array
                    selectedRows.push(anakId);
                });
                if (confirm('Apakah anda yakin ?')) {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('eo.ditutup') }}",
                        data: {
                            datas: selectedRows,
                            tipe: tipe
                        },
                        success: function(r) {
                            alertToast('sukses', 'Berhasil save')
                            loadHalaman()
                            $('.btn_tutup').hide();
                        }
                    });
                }

            })

            function loadTambahcabut() {
                updateAnakBelum();
                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.load_tambah_cabut') }}",
                    success: function(r) {
                        $("#load_tambah_cabut").html(r);
                        $(".select2-add").select2({
                            dropdownParent: $('#tambah2 .modal-content')
                        })

                        $(document).on('change', '.pilihBox', function() {
                            var no_box = $(this).val()
                            var count = $(this).attr('count')
                            $.ajax({
                                type: "GET",
                                url: "eo/get_box_sinta",
                                data: {
                                    no_box: no_box
                                },
                                dataType: "json",
                                success: function(r) {
                                    console.log(r)
                                    $(".ttlPcs").text(r.pcs_awal)
                                    $(".ttlGr" + count).text(r.gr_awal)
                                }
                            });
                        })

                    }
                });
            }
            $(document).on('submit', '#createCabut', function(e) {
                e.preventDefault();
                var datas = $(this).serialize()
                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.create') }}",
                    data: datas,
                    dataType: 'json',
                    success: function(r) {
                        alertToast('sukses', 'Berhasil tambah data cabut')
                        $('#tambah2').modal('hide')
                        loadTambahcabut()
                        loadHalaman()
                        loadTambahAnak()
                    }
                });
            })
            // ---------------
            function load_akhir() {
                $.ajax({
                    type: "GET",
                    url: "eo/load_modal_akhir",
                    success: function(r) {
                        $("#load_modal_akhir").html(r);
                        pencarian('pencarian2', 'tablealdi2')
                    }
                });
            }
            $(document).on('click', '.inputAkhir', function() {
                var no_box = $(this).attr('no_box')
                var id_anak = $(this).attr('id_anak')
                load_akhir()
            })

            $(document).on('click', '.selesai', function(e) {
                e.preventDefault()
                var id_cabut = $(this).attr('id_cabut');
                $(this).closest('tr').hide();
                $('.cetak').val(id_cabut);

                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.selesai') }}?id_cabut=" + id_cabut,
                    success: function(r) {
                        load_akhir()
                        loadHalaman()
                    }
                });
            });

            load_anak()
            load_anak_nopengawas()

            function load_anak() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.load_anak') }}",
                    success: function(r) {
                        $("#load_anak").html(r);
                    }
                });
            }

            function setRupiah(kelas) {
                $(document).on('keyup', '.' + kelas, function() {
                    var count = $(this).attr('count')
                    var row = $(this).closest("tr");

                    var data = {
                        count: count
                    };

                    var floatFields = ['gr_eo_akhir', 'gr_eo_awal', 'rupiah', 'ttl_rp', 'id_eo', 'tgl_serah', 'bulan',
                        'id_paket', 'rp_target'
                    ];

                    floatFields.forEach((fieldName) => {
                        data[fieldName] = parseFloat(row.find(`input[name='${fieldName}${count}[]']`).val()) ||
                            0;
                    })

                    var ttl_rp = data.id_paket == 15 ? data.rp_target : data.rupiah * data.gr_eo_akhir

                    var setRupiah = ttl_rp.toLocaleString('id-ID', {
                        maximumFractionDigits: 0
                    })
                    $('.ttlRpKeyup' + data.count).text(setRupiah)
                    $('.ttlRpSet' + data.count).val(ttl_rp)
                })
            }
            setRupiah('grEoAkhirKeyup')
            $(document).on('click', '.saveCabutAkhir', function(e) {
                e.preventDefault()
                var count = $(this).attr('count')
                var row = $(this).closest("tr");
                var data = {
                    tgl_serah: row.find(`input[name='tgl_serah${count}[]']`).val(),
                    ttl_rp: row.find(`input[name='ttl_rp${count}[]']`).val(),
                    id_eo: row.find(`input[name='id_eo${count}[]']`).val(),
                    bulan: row.find(`select[name='bulan${count}[]']`).val(),
                    gr_eo_akhir: row.find(`input[name='gr_eo_akhir${count}[]']`).val(),
                    count: count,
                    _token: row.data("csrf-token")
                };
                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.input_akhir') }}",
                    data: data,
                    success: function(r) {
                        alertToast('sukses', 'Berhasil input akhir')
                        // $('#inputAkhir').modal('hide')
                        loadHalaman()
                    }
                });
            })
            $(document).on('click', '.hapusCabutRow', function() {
                var count = $(this).attr('count')
                if (confirm(
                        'Jika row dihapus, Maka data no box,pcs, dan gr tereset ulang. Apakah Yakin row dihapus ?')) {
                    var id_cabut = $(this).attr('id_cabut')
                    var id_anak = $(this).attr('id_anak')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('eo.hapusCabutRow') }}",
                        data: {
                            id_cabut: id_cabut,
                            id_anak: id_anak,
                        },
                        success: function(r) {
                            alertToast('sukses', 'Berhasil hapus row')
                            $(".baris" + count).remove();
                            loadTambahcabut()
                            loadHalaman()
                            loadTambahAnak()
                        }
                    });
                }
            })

            function load_anak_nopengawas() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.load_anak_nopengawas') }}",
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
                    url: "{{ route('eo.add_delete_anak') }}?id_anak=" + id_anak,
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
                    url: "{{ route('eo.add_delete_anak') }}",
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

            $(document).on('click', '.cancelCabutAkhir', function() {
                var id_cabut = $(this).attr('id_cabut')
                $.ajax({
                    type: "GET",
                    url: "{{ route('eo.cancel') }}?id_cabut=" + id_cabut,
                    success: function(r) {
                        loadTambahcabut()
                        loadHalaman()
                        loadTambahAnak()
                        load_akhir()
                    }
                });
            })
        </script>
    @endsection
</x-theme.app>
