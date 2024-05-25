<x-theme.app title="{{ $title }} " table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <div>
                <h6 class="">{{ $title }} {{ date('d M y', strtotime($tgl1)) }} ~
                    {{ date('d M y', strtotime($tgl2)) }}</h6>
                <h6>Anak : {{ $anak }}</h6>
                <p class="badge bg-danger">Setor lewat jam 09:30 AM = tidak capai</p>
            </div>
            <div>
                <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus"
                    addClass="float-end tambah_kerja" teks="Kerja" />
                <x-theme.button href="{{ route('cetaknew.summary') }}" icon="fa-clipboard-list" addClass="float-end"
                    teks="Summary" />
                <x-theme.button modal="Y" idModal="export" href="#" icon="fa-file-excel" addClass="float-end"
                    teks="Export" />
                <x-theme.button href="{{ route('cetaknew.history') }}" icon="fa-calendar-week" addClass="float-end"
                    teks="History" />
                <a href="{{ route('gudangsarang.gudang_cbt_selesai') }}" class="float-end btn btn-sm me-2"
                    style="background-color: #E11583; color: white"><i class="fas fa-warehouse"></i> Gudang</a>
                <x-theme.button href="#" modal="Y" idModal="view" icon="fa-calendar-week"
                    addClass="float-end" teks="View" />
                <p class="badge bg-info text-wrap me-2">tekan CTRL + panah ⬅️kiri / kanan➡️ <br> untuk view hari kemarin
                    & selanjutnya</p>

            </div>
        </div>

    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <input type="hidden" id="tgl1" value="{{ $tgl1 }}">
            <input type="hidden" id="tgl2" value="{{ $tgl2 }}">
            <input type="hidden" id="id_anak" value="{{ $id_anak }}">


            <div id="load-cetak"></div>

            <form id="save_awal">
                @csrf
                <x-theme.modal idModal="tambah" title="Tambah Kerjaan" size="modal-lg-max" btnSave="Y">
                    <div class="row">
                        {{-- <div class="col-lg-2">
                            <label for="">Barang Dari</label>
                            <select name="id_pemberi" id="" class="select2">
                                <option value="">Pilih Pengawas</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="col-lg-12 mt-4">
                            <div id="load_menu"></div>
                        </div>
                    </div>
                </x-theme.modal>
            </form>


            <form action="" method="get">
                <x-theme.modal title="Filter Tanggal" idModal="view">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="">Dari</label>
                            <input id="tgl1" type="date" name="tgl1" id="" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label for="">Sampai</label>
                            <input id="tgl2" type="date" name="tgl2" id="" class="form-control">
                        </div>
                        <div class="col-lg-6 mt-2">
                            <label for="">Pilih Anak</label>
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
        </section>

        <form action="{{ route('cetaknew.export') }}" method="get">
            <x-theme.modal title="Export" idModal="export">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="">Dari</label>
                            <input type="date" name="tgl1" class="form-control">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="">Sampai</label>
                            <input type="date" name="tgl2" class="form-control">
                        </div>
                    </div>
                </div>
            </x-theme.modal>
        </form>
        @section('scripts')
            <script>
                $(document).ready(function() {
                    load_cetak();

                    function load_cetak() {
                        var tgl1 = $('#tgl1').val();
                        var tgl2 = $('#tgl2').val();
                        var id_anak = $('#id_anak').val();
                        $.ajax({
                            type: "get",
                            data: {
                                tgl1: tgl1,
                                tgl2: tgl2,
                                id_anak: id_anak
                            },
                            url: "{{ route('cetaknew.get_cetak') }}",
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


                    load_menu();

                    function load_menu() {
                        $.ajax({
                            method: "GET",
                            url: "{{ route('cetaknew.load_tambah_data') }}",
                            dataType: "html",
                            success: function(hasil) {
                                $("#load_menu").html(hasil);
                                $('.select').select2({
                                    dropdownParent: $('#tambah .modal-content')
                                });

                            },
                        });
                    }

                    $(document).on("click", ".remove_baris", function() {
                        var delete_row = $(this).attr("count");
                        $(".baris" + delete_row).remove();
                    });

                    var count = 2;
                    $(document).on("click", ".tbh_baris", function() {
                        count = count + 1;
                        $.ajax({
                            url: "{{ route('cetaknew.tambah_baris') }}",
                            data: {
                                count: count,
                            },
                            type: "Get",
                            success: function(data) {
                                $("#tb_baris").append(data);
                                $('.select').select2({
                                    dropdownParent: $('#tambah .modal-content')
                                });
                            },
                        });
                    });

                    $(document).on("submit", "#save_awal", function(e) {
                        e.preventDefault();
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        var formData = $(this).serialize();
                        formData += "&_token=" + csrfToken;

                        $.ajax({
                            type: "POST",
                            url: "{{ route('cetaknew.save_target') }}",
                            data: formData,
                            success: function(response) {
                                alertToast('sukses', 'Berhasil ditambahkan');
                                // $('.input_awal').val('');
                                load_menu();
                                $('#tambah').modal('hide');
                                load_cetak();
                            },
                        });
                    });
                    $(document).on("click", ".btn_save_akhir", function(e) {
                        e.preventDefault();
                        var id_cetak = $(this).attr('id_cetak');
                        var pcs_akhir = $('.pcs_akhir' + id_cetak).val();
                        var pcs_awal = $('.pcs_awal' + id_cetak).val();
                        var gr_akhir = $('.gr_akhir' + id_cetak).val();
                        var pcs_tdk_ctk = $('.pcs_tdk_ctk' + id_cetak).val();
                        var gr_tdk_ctk = $('.gr_tdk_ctk' + id_cetak).val();
                        var rp_satuan = $('.rp_satuan' + id_cetak).val();
                        var no = $('.no' + id_cetak).val();
                        var id_paket = $('.id_paket' + id_cetak).val();
                        var ttl_pcs = (parseFloat(pcs_akhir) + parseFloat(pcs_tdk_ctk));
                        if (pcs_awal != ttl_pcs) {
                            alertToast('error', 'Jumlah Pcs tidak sama');
                        } else {
                            $.ajax({
                                type: "get",
                                url: "{{ route('cetaknew.save_akhir') }}",
                                data: {
                                    id_cetak: id_cetak,
                                    pcs_akhir: pcs_akhir,
                                    gr_akhir: gr_akhir,
                                    pcs_tdk_ctk: pcs_tdk_ctk,
                                    gr_tdk_ctk: gr_tdk_ctk,
                                    rp_satuan: rp_satuan,
                                    id_paket: id_paket,
                                },
                                success: function(response) {
                                    loadRowData(id_cetak, no)
                                    // load_cetak();
                                    alertToast('sukses', 'Berhasil ditambahkan');


                                }
                            });

                        }

                    });

                    function loadRowData(id_cetak, no) {
                        $.get("{{ route('cetaknew.getRowData') }}", {
                            id_cetak: id_cetak,
                            no: no
                        }, function(data) {
                            var tr = $('tr[data-id="' + id_cetak + '"]');
                            tr.replaceWith(data);
                        });
                    }
                    $(document).on("click", ".btn_selesai", function(e) {
                        e.preventDefault();
                        var id_cetak = $(this).attr('id_cetak');
                        var no = $('.no' + id_cetak).val();

                        $.ajax({
                            type: "get",
                            url: "{{ route('cetaknew.save_selesai') }}",
                            data: {
                                id_cetak: id_cetak,
                            },
                            success: function(response) {

                                loadRowData(id_cetak, no)

                                // load_cetak();
                                alertToast('sukses', 'Berhasil ditambahkan');
                            }
                        });
                    });
                    $(document).on("click", ".btn_cancel", function(e) {
                        e.preventDefault();
                        var id_cetak = $(this).attr('id_cetak');
                        var no = $('.no' + id_cetak).val();

                        $.ajax({
                            type: "get",
                            url: "{{ route('cetaknew.cancel_selesai') }}",
                            data: {
                                id_cetak: id_cetak,
                            },
                            success: function(response) {


                                loadRowData(id_cetak, no)

                                // load_cetak();
                                alertToast('sukses', 'Data berhasil di cancel');
                            }
                        });
                    });
                    $(document).on("click", ".btn_hapus", function(e) {
                        e.preventDefault();
                        var id_cetak = $(this).attr('id_cetak');
                        var id_paket = $(this).attr('id_paket');
                        $.ajax({
                            type: "get",
                            url: "{{ route('cetaknew.hapus_data') }}",
                            data: {
                                id_cetak: id_cetak,
                                id_paket: id_paket
                            },
                            success: function(response) {
                                alertToast('sukses', 'Data berhasil di hapus');
                                load_cetak();
                            }
                        });
                    });

                    $(document).on('click', '.capai', function(e) {
                        e.preventDefault()
                        const val = $(this).attr('capaiVal')
                        const id_cetak = $(this).attr('id_cetak')
                        var no = $('.no' + id_cetak).val();

                        $.ajax({
                            type: "GET",
                            url: "{{ route('cetaknew.capai') }}",
                            data: {
                                val,
                                id_cetak
                            },
                            dataType: 'json',
                            success: function(r) {
                                alertToast(r.status, r.pesan);
                                loadRowData(id_cetak, no)

                                load_cetak();
                            }
                        });
                    })
                });

                var tgl1 = new Date("{{ $tgl1 }}");
                var tgl2 = new Date("{{ $tgl2 }}");


                $(document).keydown(function(event) {
                    if (event.ctrlKey && (event.keyCode === 37 || event.keyCode === 39)) {
                        var offset = event.keyCode === 37 ? -1 : 1;
                        tgl1.setDate(tgl1.getDate() + offset);
                        tgl2.setDate(tgl2.getDate() + offset);


                        var formattedTgl1 = formatDate(tgl1);
                        var formattedTgl2 = formatDate(tgl2);
                        var id_anak = $('#id_anak').val();


                        window.location.href =
                            `{{ route('cetaknew.index') }}?tgl1=${formattedTgl1}&tgl2=${formattedTgl2}&id_anak=${id_anak}`;
                    }
                });

                function formatDate(date) {
                    return date.toISOString().split('T')[0];
                }
            </script>
        @endsection
    </x-slot>
</x-theme.app>
