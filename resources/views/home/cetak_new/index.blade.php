<x-theme.app title="{{ $title }} " table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }} {{ date('d M y', strtotime($tgl1)) }} ~
            {{ date('d M y', strtotime($tgl2)) }}</h6>
        <x-theme.button href="#" modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end tambah_kerja"
            teks="Kerja" />
        <x-theme.button href="#" modal="Y" idModal="view" icon="fa-calendar-week" addClass="float-end"
            teks="" />
        <x-theme.button href="#" modal="Y" idModal="view" icon="fa-calendar-week" addClass="float-end"
            teks="History" />
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">
            <input type="hidden" id="tgl1" value="{{ $tgl1 }}">
            <input type="hidden" id="tgl2" value="{{ $tgl2 }}">


            <div id="load-cetak"></div>

            <form id="save_awal">
                @csrf
                <x-theme.modal idModal="tambah" title="Tambah Kerjaan" size="modal-lg" btnSave="Y">
                    <div class="row">
                        <div class="col-lg-3">
                            <label for="">Barang Dari</label>
                            <select name="id_pemberi" id="" class="select2">
                                <option value="">Pilih Pengawas</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-lg-3">
                            <label for="">Tanggal</label>
                            <input type="date" class="form-control" name="tgl" value="{{ date('Y-m-d') }}">
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
                            <input type="date" name="tgl1" id="" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label for="">Sampai</label>
                            <input type="date" name="tgl2" id="" class="form-control">
                        </div>
                    </div>
                </x-theme.modal>
            </form>
        </section>


        @section('scripts')
            <script>
                $(document).ready(function() {
                    load_cetak();

                    function load_cetak() {
                        var tgl1 = $('#tgl1').val();
                        var tgl2 = $('#tgl2').val();
                        $.ajax({
                            type: "get",
                            data: {
                                tgl1: tgl1,
                                tgl2: tgl2
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
                                $('#tambah').modal('hide');
                                load_cetak();
                            },
                        });
                    });


                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>