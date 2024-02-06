<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">

    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }} </h6>
        </div>
        <div class="col-lg-12"><br>
            <hr style="border: 2px solid #435EBE">
        </div>


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
                        $baris = Request::get('baris') ?? 5;
                    @endphp
                    <input min="0" name="baris" value="{{ $baris }}" type="number"
                        class="form-control">

                </div>
                <div class="col-lg-1">
                    <label for="">Aksi</label><br>
                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                </div>

            </div>
        </form>
        <form action="{{ route('pengiriman.create') }}" method="post">
            @csrf
            <section class="row">
                <div class="col-lg-12">
                    <table class="table table-stripped table-hover">
                        <thead>
                            <tr>
                                <th class="dhead">Partai</th>
                                <th class="dhead">Grade</th>
                                <th class="dhead">Tipe</th>
                                <th class="dhead">Pcs</th>
                                <th class="dhead">Gr</th>
                                <th class="dhead">No Box CFM</th>
                                <th class="dhead">Cek Akhir</th>
                                <th class="dhead">Ket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < $baris; $i++)
                                <tr>
                                    <td class="d-none">
                                        <input count="{{ $i }}" type="date" value="{{ date('Y-m-d') }}"
                                            required name="tgl[]" class="form-control">
                                    </td>
                                    <td>
                                        <input count="{{ $i }}" type="text" required name="partai[]"
                                            class="form-control partai">
                                    </td>
                                    <td>

                                        <input count="{{ $i }}" type="text" required name="grade[]"
                                            class="form-control grade">
                                    </td>
                                    <td>
                                        <input count="{{ $i }}" type="text" required name="tipe[]"
                                            class="form-control tipe">
                                    </td>
                                    <td>
                                        <input count="{{ $i }}" type="text" name="pcs[]"
                                            class="form-control pcs">
                                    </td>
                                    <td>
                                        <input count="{{ $i }}" type="text" required name="gr[]"
                                            class="form-control gr">
                                    </td>
                                    <td>
                                        <input count="{{ $i }}" type="text" name="no_box[]"
                                            class="form-control no_box">
                                    </td>
                                    <td>
                                        <input count="{{ $i }}" type="text" required name="cek_akhir[]"
                                            class="form-control cek_akhir">
                                        {{-- <select count="{{ $i }}" required name="cek_akhir[]"
                                            class="select2cek admin" id="">
                                            <option value="">Pilih Admin Cek</option>
                                            @foreach ($pengawas as $p)
                                                <option value="{{ $p->id }}">{{ strtoupper($p->name) }}
                                                </option>
                                            @endforeach
                                        </select> --}}
                                    </td>
                                    <td>
                                        <input count="{{ $i }}" type="text" name="ket[]"
                                            class="form-control ket">
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

        <a href="{{ route('pengiriman.index') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>


    </x-slot>
    @section('scripts')
        <script>
            $('.select2cek').select2()

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

            keyupBp('partai')
            keyupBp('tipe')
            keyupBp('grade')
            keyupBp('gr')
            keyupBp('no_box', true)
            keyupBp('ket')
            keyupBp('cek_akhir')
        </script>
    @endsection
</x-theme.app>
