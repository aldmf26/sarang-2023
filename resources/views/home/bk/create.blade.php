<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">

    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }} {{ ucwords($kategori) }}</h6>
        </div>
        <div class="col-lg-12"><br>
            <hr style="border: 2px solid #435EBE">
        </div>

        @include('home.bk.nav', ['name' => 'add'])

    </x-slot>


    <x-slot name="cardBody">
        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #000000;
                line-height: 36px;
                font-size: 12px;
                width: 120px !important;
            }
        </style>
        <form action="" method="GET">
            <div class="row mb-3">
                <div class="col-lg-1">
                    <label for="">Baris</label>
                    <input type="hidden" name="kategori" value="cabut">
                    @php
                        $baris = Request::get('baris') ?? 10;
                    @endphp
                    <input min="0" name="baris" value="{{ $baris }}" type="number"
                        class="form-control">
                    <input type="hidden" name="kategori" value="{{ $kategori }}" type="text"
                        class="form-control">
                </div>
                <div class="col-lg-1">
                    <label for="">Aksi</label><br>
                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                </div>

            </div>
        </form>
        <form action="{{ route('bk.create') }}" method="post">
            @csrf
            <section class="row">
                <input type="hidden" name="kategori" value="{{ $kategori }}">
                <div class="col-lg-12">
                    <table class="table table-striped" width="100%">
                        <thead>
                            <tr>
                                {{-- <th class="dhead" width="70">Tipe</th> --}}

                                <th class="dhead">No</th>
                                {{-- <th class="dhead">No Lot</th> --}}
                                <th class="dhead">Nama Partai</th>
                                {{-- <th class="dhead">No Box</th> --}}
                                <th class="dhead">Tipe</th>
                                <th class="dhead">Ket</th>
                                <th class="dhead">Warna</th>
                                <th class="dhead">Pgws</th>
                                <th class="dhead" width="120">Nama</th>
                                <th class="dhead" width="120">Pgws Grade</th>
                                <th class="dhead">Tgl Terima</th>
                                <th class="dhead text-end">Pcs Awal</th>
                                <th class="dhead text-end">Gr Awal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= $baris; $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    {{-- <td>
                                        <input required name="no_lot[]" type="text" class="form-control nolot"
                                            count="{{ $i }}">
                                   
                                    </td> --}}
                                    <td>
                                        <input required name="nm_partai[]" count="{{ $i }}" type="text"
                                            class="form-control namaPartai">
                                    </td>
                                    {{-- <td>
                                        <input required name="no_box[]" count="{{ $i }}" type="text"
                                            class="form-control nobox">
                                    </td> --}}
                                    <td>
                                        <input type="text" count="{{ $i }}" name="tipe[]"
                                            class="form-control tipe">
                                    </td>
                                    <td>
                                        <input type="text" count="{{ $i }}" name="ket[]"
                                            class="form-control ket">
                                    </td>
                                    <td>
                                        <input type="text" count="{{ $i }}" name="warna[]"
                                            class="form-control warna">
                                    </td>
                                    {{-- <td>
                                        <div count="{{ $i }}" class="load_tipe"></div>
                                    </td>
                                    <td>
                                        <div count="{{ $i }}" class="load_ket"></div>
                                    </td>
                                    <td>
                                        <div count="{{ $i }}" class="load_warna"></div>
                                    </td> --}}
                                    <td>
                                        <input type="text" class="form-control" readonly
                                            value="{{ auth()->user()->name }}" name="pgws[]">
                                    </td>
                                    <td>
                                        <select pilihan="pengawas" count="{{ $i }}" name="nama[]"
                                            id="" class="select3 selectPengawas selectTipe">
                                            <option value="">Pilih Pengawas</option>
                                            @foreach ($pengawas as $p)
                                                <option {{ $id_pengawas == $p->id ? 'selected' : '' }}
                                                    value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" count="{{ $i }}" name="pgws_grade[]"
                                            class="form-control pgws_grade">
                                    </td>
                                    <td>
                                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control"
                                            name="tgl_terima[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control pcsAwal text-end"
                                            count="{{ $i }}" name="pcs_awal[]" value="0">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control grAwal text-end"
                                            count="{{ $i }}" name="gr_awal[]" value="0">
                                    </td>

                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

            </section>

    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary button-save">Simpan</button>
        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <a href="{{ route('bk.index') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>

        <form id="formSelect">
            <x-theme.modal title="Tambah Data" idModal="selectTipe">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="">Jenis</label>
                            <input readonly type="text" id="pilihanSelect" class="form-control" name="pilihan">

                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <input type="text" name="ket" class="form-control">
                        </div>
                    </div>

                </div>
            </x-theme.modal>
        </form>
    </x-slot>
    @section('scripts')
        <script>
            $(".select3").select2()
            $('.selectPengawas').select2(); // Menginisialisasi semua elemen dengan kelas .selectPengawas sebagai Select2
            function navigateInputs(className, e) {
                if (e.keyCode === 40 || e.keyCode === 38) {
                    e.preventDefault();

                    var $input = $(e.target);
                    var currentCount = parseInt($input.attr('count'));

                    var direction = (e.keyCode === 40) ? 1 : (e.keyCode === 38) ? -1 : 0; // 1 for down arrow, -1 for up arrow

                    var $nextInput = $input.closest('tr').siblings('tr').find('.' + className + '[count="' + (currentCount +
                        direction) + '"]');

                    if ($nextInput.length) {
                        $nextInput.focus();

                        // Memeriksa jika kelas yang di-fokus adalah pcsAwal atau grAwal, lalu memilih seluruh teks di dalamnya.
                        if ($nextInput.hasClass('pcsAwal') || $nextInput.hasClass('grAwal')) {
                            $nextInput.select(); // Memilih seluruh teks dalam input.
                        }
                    }
                }
            }

            // Menggunakan event delegation untuk menangani semua input dalam tabel
            $('table').on('keydown', 'input', function(e) {
                var className = $(this).attr('class').split(' ')[
                    1]; // Mendapatkan kelas kedua (contoh: 'nolot' atau 'nobox')
                navigateInputs(className, e);
            });


            function keyupBp(kelas, ditambah = false) {
                $('.' + kelas).on('keyup', function() {
                    var currentCount = $(this).attr('count');
                    var currentValue = $(this).val();
                    var hasil = 0

                    var shouldUpdate = false;
                    $('.' + kelas).each(function() {
                        var count = $(this).attr('count');

                        if (shouldUpdate) {
                            if (ditambah) {
                                currentValue++
                                hasil = parseFloat(currentValue);
                            } else {
                                hasil = currentValue
                            }
                            $(this).val(hasil);
                        }
                        if (count === currentCount) {
                            shouldUpdate = true;
                        }
                    });
                });
            }
            keyupBp('ket')
            keyupBp('tipe')
            keyupBp('warna')
            keyupBp('nolot')
            keyupBp('namaPartai')
            keyupBp('pgws_grade')
            // keyupBp('pcsAwal')
            // keyupBp('grAwal')
            keyupBp('nobox', true)

            function selectBerubah(tipe) {
                $(document).on('change', `.selectTipe[pilihan=${tipe}]`, function() {
                    var nilai = $(this).val()
                    var currentCount = $(this).attr('count');
                    var shouldUpdate = false;
                    $(`.selectTipe[pilihan=${tipe}]`).each(function() {
                        var count = $(this).attr('count');
                        if (shouldUpdate) {
                            $(this).val(nilai).trigger('change.select2');
                        }
                        if (count === currentCount) {
                            shouldUpdate = true;
                        }
                    });

                })
            }
            selectBerubah('pengawas')


            $(document).on('change', '.selectTipe', function() {
                var nilai = $(this).val()
                var pilihan = $(this).attr('pilihan')
                if (nilai == 'tambah') {
                    $("#selectTipe").modal('show')
                    $("#pilihanSelect").val(pilihan);
                }
            })

            $(document).on('submit', '#formSelect', function(e) {
                e.preventDefault();
                var form = $(this).serialize()
                $.ajax({
                    type: "GET",
                    url: "{{ route('bk.create_select') }}",
                    data: form,
                    success: function(r) {
                        $("#selectTipe").modal('hide')
                        loadSelect('tipe')
                        loadSelect('ket')
                        loadSelect('warna')
                        alertToast('sukses', 'Berhasil  tambah data')
                    }
                });
            });


            // $('.selectPengawas').on('change', function() {
            //     var nilaiselect = $(this).val(); // Nilai terpilih dari select yang diubah

            //     //     // Memperbarui semua elemen dengan kelas .selectPengawas
            //     $('.selectPengawas').not(this).each(function() {
            //         $(this).val(nilaiselect).trigger(
            //             'change.select2'); // Update nilai Select2 dan trigger event change
            //     });
            // });
            $(document).on('change', '.nolot', function() {
                var nolot = $(this).val();
                var count = $(this).attr('count');
                var no = count;
                $('.nolot').each(function() {
                    no++;
                    $('.nomor_lot' + no).val(nolot).trigger(
                        'change.select2');
                });
            });
        </script>
    @endsection
</x-theme.app>
