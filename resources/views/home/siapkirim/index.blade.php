<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <h6>{{ $title }}</h6>
        {{-- @include('home.siapkirim.button_nav') --}}

    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8 mb-3">
                @include('home.siapkirim.nav', ['name' => 'index'])
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
                                        $cekGrading = DB::table('siapkirim_list_grading')
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

        <form action="{{ route('siapkirim.create_grading') }}" method="post">
            @csrf
            <x-theme.modal size="modal-lg-max" idModal="grading" title="Grading">
                <div id="load_grading"></div>
            </x-theme.modal>
        </form>

        <x-theme.modal idModal="detail" btnSave="T" title="Detail">
            <div id="load_detail"></div>
        </x-theme.modal>


    </x-slot>
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
                    url: "{{ route('siapkirim.load_grading') }}",
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
                    url: "{{ route('siapkirim.load_detail') }}",
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
                    url: "{{ route('siapkirim.load_ambil_box_kecil') }}",
                    success: function(r) {
                        $("#load_ambil_box_kecil").html(r);
                    }
                });
            });

            $(document).on('change', '.selectGrade', function() {
                const grade = $(this).val()
                $.ajax({
                    type: "GET",
                    url: "{{ route('siapkirim.get_select_grade') }}",
                    data: {
                        grade: grade
                    },
                    dataType: "json",
                    success: function(r) {
                        $('.pcsTtl_ambil').val(r.pcs)
                        $('.grTtl_ambil').val(r.gr)
                        $('.ttlrpTtl_ambil').val(r.ttl_rp.toLocaleString('id-ID'))
                    }
                });
            })

            function validasiAmbil(inputClass, totalInputName) {
                $(document).on('keyup', '.' + inputClass, function() {
                    let totalPcs = 0;
                    $('.' + inputClass).each(function() {
                        let pcsValue = parseInt($(this).val()) || 0;
                        totalPcs += pcsValue;
                    });
                    const ttlPcsGrade = $('.' + totalInputName).val()
                    if (totalPcs > ttlPcsGrade) {
                        alert('Ambil Melebihi stok gudang bj')
                    }
                });
            }
            validasiAmbil('pcsAmbil', 'pcsTtl_ambil')
            validasiAmbil('grAmbil', 'grTtl_ambil')
            // Event listener untuk input pcsAmbil
        </script>
    @endsection
</x-theme.app>
