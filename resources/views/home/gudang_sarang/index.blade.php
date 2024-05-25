<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">
    <x-slot name="slot">
        <div class="row">
            <div class="col-lg-2">
                <a href="{{ route('cetaknew.index') }}" class="btn btn-warning"><i class="fas fa-long-arrow-alt-left"></i>
                    Kembali</a>
            </div>
            <div class="col-lg-12">
                <br>
            </div>
            {{-- <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">

                            <div class="col-lg-6">
                                <h6 class="float-start mt-1">Gudang cabut selesai</h6>
                            </div>
                            <div class="col-lg-6">
                                <x-theme.button modal="Y" idModal="formulir" href="#" icon="fa-plus"
                                    addClass="float-end formulir" teks="Formulir" />
                                <x-theme.button modal="Y" idModal="view" href="#" icon="fa-eye"
                                    addClass="float-end" teks="View" />
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="load_cabut_selesai"></div>
                    </div>
                </div>

            </div> --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">

                            <div class="col-lg-6">
                                <h6 class="float-start mt-1">Gudang siap cetak</h6>
                            </div>
                            <div class="col-lg-6">

                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="load_siap_cetak"></div>
                    </div>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">

                            <div class="col-lg-6">
                                <h6 class="float-start mt-1">Gudang cetak proses</h6>
                            </div>
                            <div class="col-lg-6">

                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="load_cetak_proses"></div>
                    </div>
                </div>

            </div>


            <form action="{{ route('gudangsarang.save_formulir') }}" method="post">
                @csrf
                <x-theme.modal idModal="formulir" btnSave="Y" size="modal-lg" title="Formulir">
                    <div class="row">
                        <div class="col-lg-4 mb-6">
                            <label for="">Tanggal</label>
                            <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <input type="hidden" name="id_pengawas" value="{{ auth()->user()->id }}">
                        {{-- <div class="col-lg-4">
                            <label for="">Pengawas</label>
                            <select name="id_pengawas" class="form-control" id="">
                                <option value="">Pilih Pengawas</option>
                                @foreach ($pengawas as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    </div>
                    <br>
                    <br>
                    <div id="load_formulir"></div>
                </x-theme.modal>
            </form>

            <form id="view_pengawas">
                <x-theme.modal idModal="view" btnSave="Y" size="modal-sm" title="Formulir">
                    <div class="row">
                        <div class="col-lg-12 mb-6">
                            <label for="">Pilih Pengawas</label>
                            <select name="id_pengawas" id="" class="selectView id_pengawas">
                                <option value="">Pilih Pengawas</option>
                                @foreach ($view_pengawas as $v)
                                    <option value="{{ $v->id_pengawas }}">{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </x-theme.modal>
            </form>


            @section('scripts')
                <script>
                    $(document).ready(function() {

                        $(document).on('change', '#checkAll', function(e) {
                            e.preventDefault();
                            var checkedStatus = this.checked;

                            $('.checkbox').each(function() {
                                this.checked = checkedStatus;
                            });
                        });

                        $('.formulir').click(function(e) {
                            e.preventDefault();
                            var checkedBoxes = [];
                            $('.checkbox:checked').each(function() {
                                checkedBoxes.push($(this).val());
                            });

                            $.ajax({
                                type: "get",
                                url: "{{ route('gudangsarang.get_formulir') }}",
                                data: {
                                    no_box: checkedBoxes
                                },
                                success: function(response) {
                                    $('#load_formulir').html(response);
                                }
                            });

                        });
                        load_cabut_selesai();

                        function load_cabut_selesai(id_pengawas) {
                            $.ajax({
                                type: "get",
                                url: "{{ route('gudangsarang.load_cabut_selesai') }}",
                                data: {
                                    id_pengawas: id_pengawas
                                },
                                success: function(response) {
                                    $('#load_cabut_selesai').html(response);
                                    $('#cbt_selesai').DataTable({
                                        "searching": true,
                                        scrollY: '400px',
                                        scrollX: false,
                                        scrollCollapse: true,
                                        "autoWidth": true,
                                        "paging": false,
                                        "info": false
                                    });
                                }
                            });

                        }
                        load_siap_cetak();

                        function load_siap_cetak(id_pengawas) {
                            $.ajax({
                                type: "get",
                                url: "{{ route('gudangsarang.get_siap_cetak') }}",
                                data: {
                                    id_pengawas: id_pengawas
                                },
                                success: function(response) {
                                    $('#load_siap_cetak').html(response);
                                    $('#siap_cetak').DataTable({
                                        "searching": true,
                                        scrollY: '400px',
                                        scrollX: false,
                                        scrollCollapse: true,
                                        "autoWidth": true,
                                        "paging": false,
                                        "info": false
                                    });
                                }
                            });

                        }
                        load_cetak_proses();

                        function load_cetak_proses(id_pengawas) {
                            $.ajax({
                                type: "get",
                                url: "{{ route('gudangsarang.get_cetak_proses') }}",
                                data: {
                                    id_pengawas: id_pengawas
                                },
                                success: function(response) {
                                    $('#load_cetak_proses').html(response);
                                    $('#ctk_proses').DataTable({
                                        "searching": true,
                                        scrollY: '400px',
                                        scrollX: false,
                                        scrollCollapse: true,
                                        "autoWidth": true,
                                        "paging": false,
                                        "info": false
                                    });
                                }
                            });

                        }

                        $(document).on('submit', '#view_pengawas', function(e) {
                            e.preventDefault();
                            var id_pengawas = $(".id_pengawas").val();
                            load_cabut_selesai(id_pengawas);
                            $("#view").modal('hide');
                        })
                    });
                </script>
            @endsection
    </x-slot>
</x-theme.app>
