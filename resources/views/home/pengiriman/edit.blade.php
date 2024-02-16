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
       
        <form action="{{ route('pengiriman.update') }}" method="post">
            @csrf
            <section class="row">
                <div class="col-lg-12">
                    <table class="table table-stripped table-hover">
                        <thead>
                            <tr>
                                <th class="dhead">Partai</th>
                                <th class="dhead">Tipe</th>
                                <th class="dhead">Grade</th>
                                <th class="dhead">Pcs Awal</th>
                                <th class="dhead">Gr Awal</th>
                                <th class="dhead">Pcs Akhir</th>
                                <th class="dhead">Gr Akhir</th>
                                <th class="dhead">No Box SP</th>
                                <th class="dhead">Cek QC</th>
                                <th class="dhead">Ket</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                            @foreach ($tbl as $i => $d)
                            <tr>
                                <td class="d-none">
                                    <input type="hidden" name="id_pengiriman[]" value="{{ $d->id_pengiriman }}">
                                    <input count="{{ $i }}" type="date" value="{{ $d->tgl_pengiriman }}" required name="tgl[]" class="form-control selectAll">
                                </td>
                                <td>
                                    <input count="{{ $i }}" value="{{ $d->partai }}" type="text" required name="partai[]" class="form-control selectAll partai">
                                </td>
                                <td>
                                    <input count="{{ $i }}" value="{{ $d->tipe }}" type="text" required name="tipe[]" class="form-control selectAll tipe">
                                </td>
                                <td>
                                    <input count="{{ $i }}" value="{{ $d->grade }}" type="text" required name="grade[]" class="form-control selectAll grade">
                                </td>
                                <td>
                                    <input count="{{ $i }}" value="{{ $d->pcs }}" type="text" required name="pcs[]" class="form-control selectAll pcs">
                                </td>
                                <td>
                                    <input count="{{ $i }}" value="{{ $d->gr }}" type="text" required name="gr[]" class="form-control selectAll gr">
                                </td>
                                <td>
                                    <input count="{{ $i }}" value="{{ $d->pcs }}" type="text" required name="pcs_akhir[]" class="form-control selectAll pcs">
                                </td>
                                <td>
                                    <input count="{{ $i }}" value="{{ $d->gr }}" type="text" required name="gr_akhir[]" class="form-control selectAll gr">
                                </td>
                                <td>
                                    <input count="{{ $i }}" value="{{ $d->no_box }}" type="text" name="no_box[]" class="form-control selectAll no_box">
                                </td>
                                <td>
                                    <input count="{{$i}}" value="{{ $d->cek_akhir }}" type="text" name="cek_akhir[]" class="form-control selectAll admin">

                                   
                                </td>
                                <td>
                                    <input count="{{$i}}" value="{{ $d->tipe }}" type="text" name="ket[]" class="form-control selectAll ket">
                                </td>
                            </tr>
                            @endforeach
                          
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
            keyupBp('no_box', true)
            keyupBp('gr')
            keyupBp('admin')
            keyupBp('ket')

        </script>
    @endsection
</x-theme.app>
