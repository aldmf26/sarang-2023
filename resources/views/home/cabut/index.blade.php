<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.button href="#" icon="fa-window-close" variant="danger" addClass="float-end btn_tutup"
            teks="Tutup" />
        <a href="{{ route('cabut.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="float-end btn btn-sm btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>
        {{-- <x-theme.button modal="Y" idModal="listAnakSisa" href="#" icon="fa-users" addClass="float-end"
            teks="List anak sisa" /> --}}
        <x-theme.button modal="Y" idModal="tambah2" href="#" icon="fa-plus" addClass="float-end"
            teks="Cabut" />
        <a href="#" data-bs-target="#tambahAnak" data-bs-toggle="modal"
            class="btn btn-primary btn-sm float-end me-2"><i class="fas fa-plus"></i> kry kerja <span
                class="badge bg-danger" id="anakBelum"></span>
        </a>

        <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end"
            teks="kry baru" />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div id="loadHalaman"></div>
        </section>

        <form id="createCabut">
            @csrf
            <x-theme.modal idModal="tambah2" title="tambah cabut" btnSave="Y" size="modal-lg-max">
                <div id="load_tambah_cabut"></div>
            </x-theme.modal>
        </form>
        <form id="createTambahAnakCabut">
            @csrf
            <x-theme.modal idModal="tambahAnak" title="tambah anak" btnSave="Y" size="modal-md">
                <div id="load_tambah_anak"></div>
            </x-theme.modal>
        </form>


        <form id="createCabutAkhir">
            @csrf
            <x-theme.modal idModal="inputAkhir" title="tambah cabut akhir" btnSave="T" size="modal-full">
                <div id="load_modal_akhir"></div>
            </x-theme.modal>
        </form>

        <form id="selesai_cabut">
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

        <x-theme.modal idModal="listAnakSisa" title="List Anak Kerja Sisa" btnSave="T" size="">
            <div id="load_modal_anak_sisa"></div>
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
        @section('scripts')
            <script>
                $(".select3").select2()

                function updateAnakBelum() {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('cabut.updateAnakBelum') }}", // Sesuaikan dengan URL rute yang telah Anda buat
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

                function loadHalaman() {
                    updateAnakBelum();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_halaman') }}",
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

                function loadTambahcabut() {
                    updateAnakBelum();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_tambah_cabut') }}",
                        success: function(r) {
                            $("#load_tambah_cabut").html(r);
                            $(".select3").select2({
                                dropdownParent: $('#tambah2 .modal-content')
                            })
                            plusRow(1, 'tbh_baris', "cabut/tbh_baris")
                            // formatRibuan('rupiah')

                            $(document).on('change', '.pilihBox', function() {
                                var no_box = $(this).val()
                                var count = $(this).attr('count')
                                $.ajax({
                                    type: "GET",
                                    url: "cabut/get_box_sinta",
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

                            $(document).on('change', '.pilihAnak', function() {
                                var id_kelas = $(this).val()
                                var count = $(this).attr('count')
                                var nilaiGr = $(".setGr" + count).val()
                                var nilaiPcs = $(".setPcs" + count).val()
                                var hitung = $('.pilihHitung' + count).val()

                                // var id_kelas = $('option:selected', this).data('kelas');
                                $.ajax({
                                    type: "GET",
                                    url: "cabut/get_kelas_anak",
                                    data: {
                                        id_kelas: id_kelas,
                                    },
                                    dataType: "json",
                                    success: function(r) {
                                        console.log(r)
                                        var hrga_satuan = (r.rupiah / r.gr)
                                        var hrga_satuan_pcs = (r.rupiah / r.pcs)
                                        var rupiah
                                        if (hitung === 'pcs') {
                                            rupiah = hrga_satuan_pcs * parseFloat(nilaiPcs);
                                        } else {
                                            rupiah = hrga_satuan * parseFloat(nilaiGr);
                                        }
                                        $('.rupiahBiasa' + count).val(rupiah);
                                        $(".setHargaSatuanGr" + count).val(hrga_satuan)
                                        $(".setHargaSatuanPcs" + count).val(hrga_satuan_pcs)
                                        rupiah = rupiah.toLocaleString('id-ID', {
                                            maximumFractionDigits: 0
                                        })
                                        $(".setRupiah" + count).val(rupiah)
                                    }
                                });
                            })

                            $(document).on('change', '.pilihHitung', function() {
                                var selectedVal = $(this).val()
                                var count = $(this).attr('count')
                                var nilaiGr = $(".setGr" + count).val()
                                var nilaiPcs = $(".setPcs" + count).val()
                                var hrga_satuanGr = $('.setHargaSatuanGr' + count).val()
                                var hrga_satuanPcs = $('.setHargaSatuanPcs' + count).val()
                                var rupiah

                                console.log(`${nilaiGr} == ${nilaiPcs}`)
                                if (selectedVal === 'pcs') {
                                    rupiah = hrga_satuanPcs * nilaiPcs
                                    keyupFormAwalCabut('Pcs')
                                } else {
                                    rupiah = hrga_satuanGr * nilaiGr
                                    keyupFormAwalCabut('Gr')
                                }

                                rupiah = rupiah.toLocaleString('id-ID', {
                                    maximumFractionDigits: 0
                                })
                                $(".setRupiah" + count).val(rupiah)
                            })

                            function keyupFormAwalCabut(jenis) {
                                $(document).on('keyup', '.set' + jenis, function() {
                                    var isi = $(this).val()
                                    var count = $(this).attr('count')
                                    var hitung = $('.pilihHitung' + count).val()
                                    var hrga_satuan = $('.setHargaSatuan' + jenis + count).val()
                                    var rupiah = hrga_satuan * isi
                                    $('.rupiahBiasa' + count).val(parseFloat(rupiah));
                                    rupiah = rupiah.toLocaleString('id-ID', {
                                        maximumFractionDigits: 0
                                    })
                                    $(".setRupiah" + count).val(rupiah)
                                })
                            }
                        }
                    });
                }

                function loadTambahAnak() {
                    updateAnakBelum();

                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_tambah_anak') }}",
                        success: function(r) {
                            $("#load_tambah_anak").html(r);
                            pencarian('pencarian', 'tablealdi')
                            inputChecked('cekSemua', 'cek')
                        }
                    });
                }
                $('.btn_tutup').hide(); // Menampilkan tombol jika checkbox dicentang
                $(document).on('change', '.cekTutup, #cekSemuaTutup', function() {
                    $('.btn_tutup').toggle(this.checked);
                })

                $(document).on('click', '.btn_tutup', function() {
                    var selectedRows = [];
                    // Loop melalui semua checkbox yang memiliki atribut 'name="cek[]"'
                    $('input[name="cekTutup[]"]:checked').each(function() {
                        // Ambil ID anak dari atribut 'data-id' atau atribut lain yang sesuai dengan data Anda

                        // Mengambil ID dari kolom pertama (kolom #)
                        var anakId = $(this).attr('id_cabut');

                        // Tambahkan ID anak ke dalam array
                        selectedRows.push(anakId);
                    });
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.ditutup') }}",
                        data: {
                            datas: selectedRows
                        },
                        success: function(r) {
                            alertToast('sukses', 'Berhasil save')
                            loadHalaman()
                            $('.btn_tutup').hide();
                        }
                    });

                })

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

                function setRupiah(kelas) {
                    $(document).on('keyup', '.' + kelas, function() {
                        var count = $(this).attr('count')
                        var row = $(this).closest("tr");
                        var data = {
                            count: count
                        };

                        var floatFields = ['gr_flx', 'gr_kelas', 'id_kelas', 'rp_bonus', 'rupiah_kelas', 'pcs_akhir',
                            'gr_akhir', 'gr_awal', 'eot', 'pcs_hcr', 'rupiah'
                        ];

                        floatFields.forEach((fieldName) => {
                            data[fieldName] = parseFloat(row.find(`input[name='${fieldName}${count}[]']`).val()) ||
                                0;
                        })
                        var susut = (1 - (data.gr_flx + data.gr_akhir) / data.gr_awal) * 100
                        var denda = 0
                        var bonus_susut = 0
                        var rupiah = data.rupiah
                        if (susut > 23.4) {
                            denda = (susut - 23.4) * 0.03 * data.rupiah
                            rupiah = rupiah - denda
                        }
                        if (susut < 19.5) {
                            bonus_susut = (data.rp_bonus * data.gr_awal) / data.gr_kelas
                        }
                        var denda_hcr = data.pcs_hcr * 5000;

                        var eot_bonus = (data.eot - data.gr_awal * 0.02) * 750;
                        var ttl_rp = rupiah - denda_hcr + eot_bonus + bonus_susut
                        console.log(
                            `rp target = ${data.rupiah} rupiah = ${rupiah} denda = ${denda} dnda_hcr = ${denda_hcr} eotbon = ${eot_bonus} bonussut = ${bonus_susut}`
                        )
                        var setRupiah = ttl_rp.toLocaleString('id-ID', {
                            maximumFractionDigits: 0
                        })
                        $('.ttlRpKeyup' + data.count).text(setRupiah)
                        $('.ttlRpSet' + data.count).val(ttl_rp)
                    })
                }

                function loadListAnakSisa() {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.load_modal_anak_sisa') }}",
                        success: function(r) {
                            $("#load_modal_anak_sisa").html(r);
                            $('#tableAnak').DataTable({
                                "paging": true,
                                "pageLength": 10,
                                "lengthChange": true,
                                "stateSave": true,
                                "searching": true,
                            });
                        }
                    });
                }

                function load_input_akhir() {
                    $.ajax({
                        type: "GET",
                        url: "cabut/load_modal_akhir",
                        success: function(r) {
                            $("#load_modal_akhir").html(r);
                            $(".select2-akhir").select2({
                                dropdownParent: $('#inputAkhir .modal-content'),
                            })
                            pencarian('pencarian2', 'tablealdi2')
                        }
                    });
                }
                // Panggil fungsi untuk pertama kali saat halaman dimuat
                updateAnakBelum()
                loadListAnakSisa()
                loadHalaman()
                loadTambahcabut()
                loadTambahAnak()
                load_anak()
                load_anak_nopengawas()
                load_input_akhir()

                $(document).on('submit', '#createCabut', function(e) {
                    e.preventDefault();
                    var datas = $(this).serialize()
                    $.ajax({
                        type: "post",
                        url: "{{ route('cabut.create') }}",
                        data: datas,
                        dataType: 'json',
                        success: function(r) {
                            alertToast('sukses', 'Berhasil tambah data cabut')
                            $('#tambah2').modal('hide')
                            loadHalaman()
                        },
                        error: function(xhr, status, error) {
                            alertToast('error', 'Pcs / Gr Ambil Lebih banyak dari BK Ambil !!')
                            console.log(xhr.responseText);
                            console.log(status);
                            console.log(error);
                        }
                    });
                })

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
                        url: "{{ route('cabut.createTambahAnakCabut') }}",
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
                            loadListAnakSisa()
                            $('#tambah2').modal('show')
                        }
                    });
                })

                $(document).on('click', '.btnKembaliTambahCabut', function() {
                    $('#tambah2').modal('hide')
                    $('#tambahAnak').modal('show')
                    loadTambahAnak()
                })
                $(document).on('click', '.btnLanjutkanTambahCabut', function() {
                    $('#tambah2').modal('show')
                    $('#tambahAnak').modal('hide')
                    loadTambahcabut()
                })

                $(document).on('click', '.hapusCabutRow', function() {
                    var count = $(this).attr('count')
                    if (confirm(
                            'Jika row dihapus, Maka data no box,pcs, dan gr tereset ulang. Apakah Yakin row dihapus ?')) {
                        var id_cabut = $(this).attr('id_cabut')
                        var id_anak = $(this).attr('id_anak')
                        $.ajax({
                            type: "GET",
                            url: "{{ route('cabut.hapusCabutRow') }}",
                            data: {
                                id_cabut: id_cabut,
                                id_anak: id_anak,
                            },
                            success: function(r) {
                                alertToast('sukses', 'Berhasil hapus row')
                                $(".baris" + count).remove();
                                // loadTambahcabut()
                                loadHalaman()
                                loadTambahAnak()
                            }
                        });
                    }
                })
                $(document).on('click', '.hapusAnakSisa', function() {

                    var id_absen = $(this).attr('id_absen')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.hapusAnakSisa') }}",
                        data: {
                            id_absen: id_absen,
                        },
                        success: function(r) {
                            alertToast('sukses', 'Berhasil hapus row')
                            loadListAnakSisa()
                            loadTambahcabut()
                            loadHalaman()
                            loadTambahAnak()
                        }
                    });
                })
                // cabut add end -----------------

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

                // cabut akhur
                $(document).on('click', '.inputAkhir', function() {
                    load_input_akhir()
                })

                setRupiah('grFlexKeyup')
                setRupiah('pcsAkhirKeyup')
                setRupiah('grAkhirKeyup')
                setRupiah('eotKeyup')
                setRupiah('pcsHcrKeyup')

                $(document).on('click', '.saveCabutAkhir', function(e) {
                    e.preventDefault()
                    var count = $(this).attr('count')
                    var row = $(this).closest("tr");
                    var data = {
                        tgl_serah: row.find(`input[name='tgl_serah${count}[]']`).val(),
                        id_anak: row.find(`input[name='id_anak${count}[]']`).val(),
                        id_cabut: row.find(`input[name='id_cabut${count}[]']`).val(),
                        no_box: row.find(`input[name='no_box${count}[]']`).val(),
                        bulan: row.find(`select[name='bulan${count}[]']`).val(),
                        nama: row.find(`.nama`).text(),
                        gr_flx: row.find(`input[name='gr_flx${count}[]']`).val(),
                        pcs_akhir: row.find(`input[name='pcs_akhir${count}[]']`).val(),
                        gr_akhir: row.find(`input[name='gr_akhir${count}[]']`).val(),
                        eot: row.find(`input[name='eot${count}[]']`).val(),
                        pcs_hcr: row.find(`input[name='pcs_hcr${count}[]']`).val(),
                        ttl_rp: row.find(`input[name='ttl_rp${count}[]']`).val(),
                        count: count,
                        _token: row.data("csrf-token")
                    };
                    // var datas = $(this).serialize()
                    $.ajax({
                        type: "GET",
                        url: "{{ route('cabut.input_akhir') }}",
                        data: data,
                        success: function(r) {
                            alertToast('sukses', 'Berhasil input akhir')
                            // $('#inputAkhir').modal('hide')
                            loadHalaman()
                        }
                    });

                })
                // end save cabut akhir
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

                $(document).on('submit', '#selesai_cabut', function(e) {
                    e.preventDefault()
                    var datas = $(this).serialize()
                    $.ajax({
                        type: "POST",
                        url: "{{ route('cabut.selesai_cabut') }}",
                        data: datas,
                        success: function(r) {
                            alertToast('sukses', 'Berhasil menyelesaikan')
                            $('#selesai').modal('hide')
                            loadHalaman()
                            load_input_akhir()
                            $("#inputAkhir").modal('show')
                        }
                    });
                })
            </script>
        @endsection
    </x-slot>
</x-theme.app>
