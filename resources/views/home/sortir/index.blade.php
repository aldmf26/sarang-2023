<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>



        <a href="{{ route('sortir.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="float-end btn btn-sm btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>


        <a href="#" data-bs-target="#tambah2" data-bs-toggle="modal"
            class="btn btn-primary btn-sm float-end me-2"><i class="fas fa-plus"></i> Sortir <span
                class="badge bg-danger" id="anakBelum"></span>
        </a>

        <a href="#" data-bs-target="#tambahAnak" data-bs-toggle="modal"
            class="btn btn-primary btn-sm float-end me-2"><i class="fas fa-plus"></i> kry kerja


            <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end"
                teks="kry baru" />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div id="loadHalaman"></div>
        </section>

        <form id="createTambahAnakSortir">
            @csrf
            <x-theme.modal idModal="tambahAnak" title="tambah anak" btnSave="Y" size="modal-md">
                <div id="load_tambah_anak"></div>
            </x-theme.modal>
        </form>

        <form id="createSortir">
            @csrf
            <x-theme.modal idModal="tambah2" title="Tambah Sortir" size="modal-lg-max">
                <div id="load_tambah_sortir"></div>
            </x-theme.modal>
        </form>

        <form action="{{ route('sortir.input_akhir') }}" method="post">
            @csrf
            <x-theme.modal idModal="inputAkhir" title="tambah sortir akhir" btnSave="T" size="modal-lg-max">
                <div id="load_modal_akhir"></div>
            </x-theme.modal>
        </form>

        <form action="{{ route('sortir.create_anak') }}" method="post">
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

        <x-theme.modal idModal="detail" title="Detail Sortir" size="modal-lg-max" btnSave="T">
            <div class="row">
                <div class="col-lg-12">
                    <div id="load_detail_sortir"></div>
                </div>
            </div>
        </x-theme.modal>
        @section('scripts')
            <script>
                $(document).ready(function() {
                    function plusSortir(count, classPlus, url) {
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
                    plusSortir(1, 'tbh_baris', "sortir/tbh_baris")
                    $(document).on('change', '.pilihBox', function() {
                        var no_box = $(this).val()
                        var count = $(this).attr('count')
                        $.ajax({
                            type: "GET",
                            url: "sortir/get_box_sinta",
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

                    $(document).on('input', '.setGr', function() {
                        var count = $(this).attr('count')
                        var hasil = $(this).val()
                        var rupiah = (120000 / 500) * parseFloat(hasil)
                        rupiah = rupiah.toLocaleString('id-ID', {
                            maximumFractionDigits: 0
                        })
                        $(".setRupiah" + count).val(rupiah)
                    })

                    function updateAnakBelum() {
                        $.ajax({
                            type: 'GET',
                            url: "{{ route('sortir.updateAnakBelum') }}", // Sesuaikan dengan URL rute yang telah Anda buat
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
                    $(".select3").select2()
                    $(".select2-add").select2({
                        dropdownParent: $('#tambah2 .modal-content')
                    })
                    load_anak()
                    load_anak_nopengawas()
                    loadTambahsortir()

                    function loadTambahsortir() {
                        updateAnakBelum()
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.load_tambah_sortir') }}",
                            success: function(r) {
                                $("#load_tambah_sortir").html(r);
                                $(".select3").select2({
                                    dropdownParent: $('#tambah2 .modal-content')
                                })
                            }
                        });
                    }
                    loadTambahAnak()

                    function loadTambahAnak() {
                        updateAnakBelum()

                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.load_tambah_anak') }}",
                            success: function(r) {
                                $("#load_tambah_anak").html(r);
                                pencarian('pencarian', 'tablealdi')
                                inputChecked('cekSemua', 'cek')
                            }
                        });
                    }

                    function load_anak() {
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.load_anak') }}",
                            success: function(r) {
                                $("#load_anak").html(r);
                            }
                        });
                    }

                    function load_anak_nopengawas() {
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.load_anak_nopengawas') }}",
                            success: function(r) {
                                $("#load_anak_nopengawas").html(r)
                                $(".select3-load").select2()

                            }
                        });
                    }

                    function loadInputAkhir() {
                        updateAnakBelum()
                        $.ajax({
                            type: "GET",
                            url: "sortir/load_modal_akhir",

                            success: function(r) {
                                $("#load_modal_akhir").html(r);
                                pencarian('pencarian2', 'tablealdi2')
                            }
                        });
                    }
                    loadHalaman()
                    loadInputAkhir()

                    function loadHalaman() {
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.load_halaman') }}",
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
                                    "ordering": true
                                });
                                inputChecked('cekSemuaTutup', 'cekTutup')
                            }
                        });


                    }
                    $(document).on('click', '.detail', function() {
                        var id_sortir = $(this).attr('id_sortir')
                        $.ajax({
                            type: "GET",
                            url: "sortir/load_detail_sortir",
                            data: {
                                id_sortir: id_sortir,
                            },
                            success: function(r) {
                                $("#load_detail_sortir").html(r);
                            }
                        });
                    })
                    $(document).on('submit', '#createTambahAnakSortir', function(e) {
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
                            url: "{{ route('sortir.createTambahAnakSortir') }}",
                            data: {
                                rows: selectedRows,
                                tipe: tipe
                            },
                            success: function(r) {
                                alertToast('sukses', 'Berhasil tambah')
                                $('#tambahAnak').modal('hide')
                                loadTambahsortir()
                                loadHalaman()
                                loadTambahAnak()
                                $('#tambah2').modal('show')
                            }
                        });
                    })

                    $(document).on('click', '#add_anak', function() {
                        var id_anak = $(".anakNoPengawas").val()
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.add_delete_anak') }}?id_anak=" + id_anak,
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
                            url: "{{ route('sortir.add_delete_anak') }}",
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
                        var no_box = $(this).attr('no_box')
                        var id_anak = $(this).attr('id_anak')
                        loadInputAkhir()
                    })
                    $(document).on('click', '.selesai', function(e) {
                        e.preventDefault()
                        var id_sortir = $(this).attr('id_sortir')
                        var row = $(this).closest('tr');
                        var grAkhirValue = row.find('.grAkhirKeyup').val();
                        if(grAkhirValue == 0 || grAkhirValue == '') {
                            alertToast('error', 'Gagal Selesai')
                            
                        } else {
                            row.hide();
                            $.ajax({
                                type: "GET",
                                url: "{{ route('sortir.selesai_sortir') }}",
                                data: {
                                    id_sortir: id_sortir
                                },
                                success: function(r) {
                                    alertToast('sukses', 'Berhasil menyelesaikan')
                                    loadHalaman()
                                    // loadInputAkhir()
                                }
                            });
                        }
                    });
                    $(document).on('keyup', '.grAkhirKeyup', function() {
                        var count = $(this).attr('count')
                        var nilai = parseFloat($(this).val())
                        var grAwal = parseFloat($('.grAwalVal' + count).val())

                        var total = (1 - nilai / grAwal) * 100
                        total = total.toFixed(0)
                        $(".susut" + count).text(total + ' %')

                        var rpTarget = parseFloat($(`.rpTarget${count}`).val())
                        var bts_denda_sst = parseFloat($(`.bts_denda_sst${count}`).val())
                        var batas_denda_rp = parseFloat($(`.batas_denda_rp${count}`).val())
                        var denda_susut = parseFloat($(`.denda_susut${count}`).val())
                        var dendaKelas = parseFloat($(`.dendaKelas${count}`).val())
                        var denda = 0
                        if (total > denda_susut) {
                            denda = total > bts_denda_sst ? batas_denda_rp : (total - denda_susut) * dendaKelas
                            rpTarget = rpTarget - denda
                        }
                        console.log(`
                            total = ${total} 
                            denda = ${denda} 
                            rpTarget = ${rpTarget} 
                            dendaSusut = ${denda_susut} 
                            btsDendaSusut = ${bts_denda_sst} 
                            btsDendaRp = ${batas_denda_rp} 
                            dendaKelas = ${dendaKelas}
                        `)
                        var setRupiah = rpTarget.toLocaleString('id-ID', {
                            maximumFractionDigits: 0
                        });
                        $('.ttlRpKeyup' + count).text(setRupiah)

                        $('.ttlRpSet' + count).val(rpTarget)
                    })
                    $(document).on('click', '.saveSortirAkhir', function(e) {
                        e.preventDefault()
                        var count = $(this).attr('count')
                        var row = $(this).closest("tr");
                        var data = {
                            id_anak: row.find(`input[name='id_anak${count}[]']`).val(),
                            tgl: row.find(`input[name='tgl${count}[]']`).val(),
                            id_sortir: row.find(`input[name='id_sortir${count}[]']`).val(),
                            no_box: row.find(`input[name='no_box${count}[]']`).val(),
                            gr_akhir: row.find(`input[name='gr_akhir${count}[]']`).val(),
                            pcs_akhir: row.find(`input[name='pcs_akhir${count}[]']`).val(),
                            pcus: row.find(`input[name='pcus${count}[]']`).val(),
                            bulan: row.find(`select[name='bulan${count}[]']`).val(),
                            count: count,
                        };
                        // var datas = $(this).serialize()
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.input_akhir') }}",
                            data: data,
                            dataType: 'json',
                            success: function(r) {
                                alertToast(r.tipe, r.pesan)
                                loadHalaman()
                                if(r.tipe == 'sukses')  {
                                    $(".btn" + count).removeClass('btn-warning');
                                    $(".btn" + count).addClass('btn-primary');
                                }
                            }
                        });

                    })

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
                            var anakId = $(this).attr('id_sortir');

                            // Tambahkan ID anak ke dalam array
                            selectedRows.push(anakId);
                        });
                        if (confirm('Apakah anda yakin ?')) {
                            $.ajax({
                                type: "GET",
                                url: "{{ route('sortir.ditutup') }}",
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

                    $(document).on('click', '.hapusKerjSortir', function() {
                        var id_sortir = $(this).attr('id_sortir')
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.hapusKerjaSortir') }}",
                            data: {
                                id_sortir: id_sortir
                            },
                            dataType: "dataType",
                            success: function(r) {}
                        });
                        loadTambahsortir()

                    })

                    $(document).on('submit', '#createSortir', function(e) {
                        e.preventDefault()
                        var datas = $(this).serialize()
                        $.ajax({
                            type: "get",
                            url: "{{ route('sortir.create') }}",
                            data: datas,
                            success: function(r) {
                                if(r === 'berhasil') {
                                    alertToast('sukses', 'Berhasil tambah kerja')
                                    $('#tambah2').modal('hide')
                                    loadHalaman()
                                    loadTambahsortir()
                                } else {
                                    alertToast('error', r)
                                }

                            }
                        });
                    })
                    $(document).on('click', '.cancelSortirAkhir', function() {
                        var id_sortir = $(this).attr('id_sortir')
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.cancel') }}",
                            data: {
                                id_sortir: id_sortir
                            },
                            success: function(r) {
                                loadHalaman()
                                loadInputAkhir()
                                loadTambahsortir()
                            }
                        });
                    })
                });

                var inputNya = ['.grFlexKeyup',
                    '.pcsAwalKeyup',
                    '.grAwalKeyup',
                    '.pcucKeyup',
                    '.grAkhirKeyup',
                    '.pcsAkhirKeyup',
                    '.pcucAkhirKeyup',
                ]
                clickSelectInput(inputNya)
            </script>
        @endsection
    </x-slot>
</x-theme.app>
