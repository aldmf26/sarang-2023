<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <a href="{{ route('sortir.gudang') }}" class="float-end btn btn-sm me-2"
            style="background-color: #E11583; color: white"><i class="fas fa-warehouse"></i> Gudang</a>

        <a href="{{ route('sortir.export', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="float-end btn btn-sm btn-primary me-2">
            <i class="fas fa-file-excel"></i> Export
        </a>

        <a href="#" data-bs-target="#tambah2" data-bs-toggle="modal"
            class="btn btn-primary btn-sm float-end me-2"><i class="fas fa-plus"></i> Sortir <span
                class="badge bg-danger" id="anakBelum"></span>
        </a>

        {{-- <a href="#" data-bs-target="#tambahAnak" data-bs-toggle="modal"
            class="btn btn-primary btn-sm float-end me-2"><i class="fas fa-plus"></i> kry kerja --}}
        <x-theme.button modal="Y" idModal="import" href="#" icon="fa-plus" addClass="float-end me-2"
            teks="box" />

        <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end me-2"
            teks="kry baru" />
        {{-- <x-theme.button href="#" modal="Y" idModal="ambil_box" icon="fa-plus" addClass="float-end"
                teks="Ambil Box Bk" /> --}}
        <x-theme.button href="#" modal="Y" idModal="history" icon="fa-history"
            addClass="float-end history me-2" teks="History" />
        <x-theme.button href="#" modal="Y" idModal="view" icon="fa-calendar-week" addClass="float-end me-2"
            teks="View" />
    </x-slot>

    <x-slot name="cardBody">
        <style>
            .select2 {
                width: 120px !important;
                font-size: 12px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #000000;
                line-height: 36px;
                /* font-size: 12px; */
                width: 120px !important;
            }

            .kolom_select {
                width: 120px !important;
            }
        </style>
        <section class="row">
            <input type="hidden" class="tgl1" value="{{ $tgl1 }}">
            <input type="hidden" class="tgl2" value="{{ $tgl2 }}">
            <input type="hidden" class="id_anak" value="{{ $id_anak }}">
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
        <x-theme.modal idModal="history" title="History Sortir" btnSave="T" size="modal-lg-max">
            <div id="load_history"></div>
        </x-theme.modal>

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

        <form action="" method="get">
            <x-theme.modal title="Filter Tanggal" idModal="view">
                <div class="row">
                    {{-- <div class="col-lg-6">
                        <label for="">Dari</label>
                        <input id="tgl1" type="date" name="tgl1" id="" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label for="">Sampai</label>
                        <input id="tgl2" type="date" name="tgl2" id="" class="form-control">
                    </div> --}}
                    <div class="col-lg-12 mt-2">
                        <label for="">Pilih Anak</label>
                        <br>
                        <select name="id_anak" id="" class="selectView">
                            <option value="All">All</option>
                            @foreach ($tb_anak as $u)
                                <option value="{{ $u->id_anak }}">{{ $u->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <form action="{{ route('sortir.ambil_box_bk') }}" method="post">
            @csrf
            <x-theme.modal idModal="ambil_box" title="Ambil Box BK" btnSave="Y">
                @php

                    $bk = DB::table('bk')
                        ->where([['kategori', 'sortir'], ['penerima', '0']])
                        ->get();

                @endphp
                <div class="row" x-data="{
                    bk: {{ $bk }},
                    selectedBk: [],
                    tambah(no_box, pcs, gr) {
                        this.selectedBk.push({
                            no_box: no_box,
                            pcs: pcs,
                            gr: gr
                        })
                        const index = this.bk.findIndex((e) => e.no_box === no_box)
                        this.bk.splice(index, 1)
                    },
                    hapus(index) {
                        const item = this.selectedBk[index];
                        this.selectedBk.splice(index, 1);
                
                        this.bk.push({
                            no_box: item.no_box,
                            pcs_awal: item.pcs,
                            gr_awal: item.gr
                        });
                    }
                }">
                    <div class="col-lg-6">
                        <h6>Gudang BK</h6>
                        <div class="scrollable-table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="dhead">No Box</th>
                                        <th class="dhead">Pcs</th>
                                        <th class="dhead">Gr</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="d in bk">
                                        <tr
                                            @click="tambah(
                                            d.no_box,
                                            d.pcs_awal,
                                            d.gr_awal
                                         )">

                                            <td x-text="d.no_box"></td>
                                            <td x-text="d.pcs_awal"></td>
                                            <td x-text="d.gr_awal"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h6>Diambil dari BK</h6>

                        <div class="scrollable-table">
                            <table class="table table-bordered">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th class="">No Box</th>
                                        <th class="">Pcs</th>
                                        <th class="">Gr</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(d,i) in selectedBk">
                                        <tr @click="hapus(i)">
                                            <td class="d-none"><input type="hidden" name="no_box[]"
                                                    :value="d.no_box"></td>
                                            <td x-text="d.no_box"></td>
                                            <td x-text="d.pcs"></td>
                                            <td x-text="d.gr"></td>
                                        </tr>
                                    </template>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <x-theme.modal idModal="detail" title="Detail Sortir" size="modal-lg-max" btnSave="T">
            <div class="row">
                <div class="col-lg-12">
                    <div id="load_detail_sortir"></div>
                </div>
            </div>
        </x-theme.modal>

        <form action="{{ route('sortir.import') }}" enctype="multipart/form-data" method="post">
            @csrf
            <x-theme.modal size="modal-lg" idModal="import" title="Import Bk">
                <div class="row">
                    <table>
                        <tr>
                            <td width="100" class="pl-2">
                                <img width="80px" src="{{ asset('/img/1.png') }}" alt="">
                            </td>
                            <td>
                                <span style="font-size: 20px;"><b> Download Excel template</b></span><br>
                                File ini memiliki kolom header dan isi yang sesuai dengan data menu
                            </td>
                            <td>
                                <a href="{{ route('cetaknew.template', ['kategori' => 'sosrtir']) }}"
                                    class="btn btn-primary btn-sm"><i class="fa fa-download"></i> DOWNLOAD
                                    TEMPLATE</a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <hr>
                            </td>
                        </tr>
                        <tr>
                            <td width="100" class="pl-2">
                                <img width="80px" src="{{ asset('/img/2.png') }}" alt="">
                            </td>
                            <td>
                                <span style="font-size: 20px;"><b> Upload Excel template</b></span><br>
                                Setelah mengubah, silahkan upload file.
                            </td>
                            <td>
                                <input type="file" name="file" class="form-control">
                                <input type="hidden" name="kategori"
                                    value="{{ request()->get('kategori') ?? 'cabut' }}">
                            </td>
                        </tr>
                    </table>

                </div>
            </x-theme.modal>
        </form>
        @section('scripts')
            <script>
                $(document).ready(function() {

                    $(document).on('click', '.history', function(e) {
                        e.preventDefault()
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.history') }}",
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

                    function plusSortir(count, classPlus, url) {
                        $(document).on("click", "." + classPlus, function() {
                            count = count + 1;
                            $.ajax({
                                url: `${url}?count=` + count,
                                type: "GET",
                                success: function(data) {
                                    $("#" + classPlus).append(data);
                                    $(".select2-tambah").select2({
                                        dropdownParent: $(`#tambah2 .modal-content`),
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

                                var hasil = $('.setGr' + count).val()
                                var rupiah = (120000 / 500) * parseFloat(hasil)
                                rupiah = rupiah.toLocaleString('id-ID', {
                                    maximumFractionDigits: 0
                                })
                                $(".setRupiah" + count).val(rupiah)
                            }
                        });
                    })
                    $(document).on('change', '.selectTipe', function(e) {
                        var count = $(this).attr('count')
                        var id_tipe = $(this).val()


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
                                id_anak: "{{ $id_anak }}",
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
                                $('.select2_add').select2({});
                                pencarian('tblinput1', 'tablestr');
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
                        // if (grAkhirValue == 0 || grAkhirValue == '') {
                        //     alertToast('error', 'Gagal Selesai')

                        // } else {
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
                        // }
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
                                if (r.tipe == 'sukses') {
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
                                if (r === 'berhasil') {
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
                    });




                    $(document).on('click', '.save_akhir', function(e) {
                        e.preventDefault();
                        var id_sortir = $(this).attr('id_sortir');
                        var id_anak = $('.id_anak' + id_sortir).val();
                        var id_kelas = $('.id_kelas' + id_sortir).val();
                        var id_paket = $('.id_paket' + id_sortir).val();
                        var pcs_akhir = $('.pcs_akhir' + id_sortir).val();
                        var gr_akhir = $('.gr_akhir' + id_sortir).val();
                        var gr_awal = $('.gr_awal' + id_sortir).val();
                        var tgl = $('.tgl' + id_sortir).val();
                        var bulan_dibayar = $('.bulan_dibayar' + id_sortir).val();
                        var no = $('.no' + id_sortir).val();


                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.save_akhir') }}",
                            data: {
                                id_sortir: id_sortir,
                                id_anak: id_anak,
                                id_kelas: id_kelas,
                                pcs_akhir: pcs_akhir,
                                gr_akhir: gr_akhir,
                                gr_awal: gr_awal,
                                bulan_dibayar: bulan_dibayar,
                                tgl: tgl
                            },
                            success: function(response) {
                                alertToast('sukses', 'Berhasil ditambahkan');
                                loadRowData(id_sortir, no);
                            }
                        });
                    });

                    function loadRowData(id_sortir, no) {
                        $.get("{{ route('sortir.load_halamanrow') }}", {
                            id_sortir: id_sortir,
                            no: no
                        }, function(data) {
                            var tr = $('tr[data-id="' + id_sortir + '"]');
                            tr.replaceWith(data);

                            // Check if the new .select2_add elements exist
                            var newSelectElements = $('.select2_add');
                            if (newSelectElements.length > 0) {
                                newSelectElements.select2({});
                            } else {
                                console.log('No select2_add elements found.');
                            }
                        });
                    }
                    $(document).on('click', '.selesai_new', function(e) {
                        e.preventDefault();
                        var id_sortir = $(this).attr('id_sortir');
                        var pcs_awal = $('.pcs_awal' + id_sortir).val();
                        var pcs_akhir = $('.pcs_akhir' + id_sortir).val();
                        var no = $('.no' + id_sortir).val();




                        if (pcs_awal != pcs_akhir) {
                            alertToast('error', 'Jumlah Pcs tidak sama');
                        } else {
                            $.ajax({
                                type: "GET",
                                url: "{{ route('sortir.selesai_sortir') }}",
                                data: {
                                    id_sortir: id_sortir,
                                },
                                success: function(response) {
                                    loadRowData(id_sortir, no)
                                    alertToast('sukses', 'Berhasil ditambahkan');
                                }
                            });
                        }


                    });
                    $(document).on('click', '.cancel_new', function(e) {
                        e.preventDefault();
                        var id_sortir = $(this).attr('id_sortir');
                        var no = $('.no' + id_sortir).val();
                        $.ajax({
                            type: "GET",
                            url: "{{ route('sortir.cancel_sortir') }}",
                            data: {
                                id_sortir: id_sortir,
                            },
                            success: function(response) {
                                loadRowData(id_sortir, no)
                                alertToast('sukses', 'Berhasil ditambahkan');
                            }
                        });
                    });
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
            <script>
                document.body.style.zoom = "80%";
            </script>
        @endsection
    </x-slot>
</x-theme.app>
