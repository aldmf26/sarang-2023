<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <h6 class="float-start">{{ $title }}</h6>
        <x-theme.button href="#" idModal="gudang" modal="Y" icon="fa-warehouse" addClass="float-end"
            teks="Gudang Sudah Grade" />
        <x-theme.button href="#" icon="fa-plus" addClass="float-end ambil_box_kecil" teks="Ambil Box Kecil" />
        <x-theme.button href="{{ route('gradingbj.add') }}" icon="fa-plus" addClass="float-end" teks="Ambil dari ctk" />
        <x-theme.button modal="Y" idModal="import" href="#" icon="fa-upload" addClass="float-end"
            teks="Import" />
        <form action="{{ route('gradingbj.import') }}" enctype="multipart/form-data" method="post">
            @csrf
            <x-theme.modal size="modal-lg" idModal="import" title="Import Pengiriman">
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
                                <a href="{{ route('gradingbj.template') }}" class="btn btn-primary btn-sm"><i
                                        class="fa fa-download"></i> DOWNLOAD
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
                            </td>
                        </tr>
                    </table>

                </div>
            </x-theme.modal>
        </form>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8 mb-3">
                @include('home.packing.nav', ['name' => 'index'])
            </div>
            <div class="col-lg-12">
                <table class="table" id="table1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tgl</th>
                            <th>Partai</th>
                            <th>No Grading</th>
                            <th class="text-end">Ttl Pcs</th>
                            <th class="text-end">Ttl Gr</th>
                            <th class="text-end">Ttl Rp</th>
                            <th width="20%" class="text-center">Grading</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ tanggal($d->tgl) }}</td>
                                <td>{{ $d->partai }}</td>
                                <td>{{ "GRDBJ-$d->no_grading" }}</td>
                                <td align="right">{{ number_format($d->pcs_awal, 0) }}</td>
                                <td align="right">{{ number_format($d->gr_awal, 0) }}</td>
                                <td align="right">{{ number_format($d->ttl_rp, 0) }}</td>
                                <td align="center">
                                    <span class="btn btn-sm btn-primary detail" no_grading="{{ $d->no_grading }}"><i
                                            class="fas fa-eye"></i></span>
                                    @php
                                        $cekGrading = DB::table('pengiriman_list_gradingbj')
                                            ->where('no_grading', $d->no_grading)
                                            ->first();
                                    @endphp
                                    @if (!$cekGrading)
                                        <span class="btn btn-sm btn-primary grading"
                                            no_grading="{{ $d->no_grading }}"><i
                                                class="fas fa-hourglass-half "></i></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </section>
        <style>
            .modal-lg-max {
                max-width: 1500px
            }
        </style>
        <form action="{{ route('gradingbj.create_grading') }}" method="post">
            @csrf
            <x-theme.modal size="modal-lg-max" idModal="grading" title="Grading">
                <div id="load_grading"></div>
            </x-theme.modal>
        </form>
        <x-theme.modal idModal="detail" btnSave="T" title="Detail">
            <div id="load_detail"></div>
        </x-theme.modal>
        {{-- <x-theme.modal idModal="gudang" btnSave="T" title="Gudang Bahan Jadi">
            @include('home.gradingbj.gudang_bj')
        </x-theme.modal> --}}
        <x-theme.modal idModal="ambil_box_kecil" size="modal-lg" title="Ambil Box Kecil">
            <div id="load_ambil_box_kecil"></div>
        </x-theme.modal>
        @section('scripts')
            <script>
                pencarian('pencarianGudang', 'gudang')

                function tekanBawah() {
                    function navigateInputs(className, e) {
                        if (e.keyCode === 40 || e.keyCode === 38) {
                            e.preventDefault();
                            var $input = $(e.target);
                            var currentCount = parseInt($input.attr('count'));
                            var direction = (e.keyCode === 40) ? 1 : (e.keyCode === 38) ? -1 :
                                0; // 1 for down arrow, -1 for up arrow

                            var $nextInput = $input.closest('tr').siblings('tr').find('.' + className + '[count="' + (currentCount +
                                direction) + '"]');
                            if ($nextInput.length) {
                                $nextInput.focus();

                                // Memeriksa jika kelas yang di-fokus adalah pcsAwal atau grAwal, lalu memilih seluruh teks di dalamnya.
                                if ($nextInput.hasClass('pcs') || $nextInput.hasClass('gr')) {
                                    $nextInput.select(); // Memilih seluruh teks dalam input.
                                }
                            }
                        }
                    }

                    $('table').on('keydown', 'input', function(e) {
                        var className = $(this).attr('class').split(' ')[
                            1]; // Mendapatkan kelas kedua (contoh: 'nolot' atau 'nobox')
                        navigateInputs(className, e);
                    });
                }

                $(document).on('click', '.grading', function(e) {
                    e.preventDefault();

                    const no_grading = $(this).attr('no_grading')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.load_grading') }}",
                        data: {
                            no_grading: no_grading
                        },
                        success: function(r) {
                            $("#load_grading").html(r);
                            $('#grading').modal('show')
                            tekanBawah()


                        }
                    });
                })

                $(document).on('click', '.detail', function(e) {
                    e.preventDefault();

                    const no_grading = $(this).attr('no_grading')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.load_detail') }}",
                        data: {
                            no_grading: no_grading
                        },
                        success: function(r) {
                            $("#load_detail").html(r);
                            $('#detail').modal('show')
                        }
                    });
                })

                $(".ambil_box_kecil").click(function(e) {
                    e.preventDefault();
                    $('#ambil_box_kecil').modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.load_ambil_box_kecil') }}",
                        success: function(r) {
                            $("#load_ambil_box_kecil").html(r);
                            $('.selectGrade').select2({
                                dropdownParent: $('#ambil_box_kecil .modal-content')
                            })
                        }
                    });
                });

                $(document).on('change', '.selectGrade', function() {
                    const grade = $(this).val()
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.index') }}",
                        data: "data",
                        dataType: "dataType",
                        success: function(response) {

                        }
                    });
                    $('.pcs_ambil').val(1)
                    $('.gr_ambil').val(100)
                })
            </script>
        @endsection
    </x-slot>
</x-theme.app>
