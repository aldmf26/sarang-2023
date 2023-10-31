<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">

    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }} {{ $kategori }}</h6>
        </div>
        
    </x-slot>


    <x-slot name="cardBody">
        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #000000;
                line-height: 36px;
                font-size: 12px;
                width: auto;
            }
        </style>
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
                                <th class="dhead">No Lot</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead">Tipe</th>
                                <th class="dhead" width="80">Ket</th>
                                <th class="dhead" width="60">Warna</th>
                                <th class="dhead">Pgws</th>
                                <th class="dhead" width="120">Nama</th>
                                <th class="dhead">Tgl Terima</th>
                                <th class="dhead text-end">Pcs Awal</th>
                                <th class="dhead text-end">Gr Awal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 10; $i++)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <input name="no_lot[]" type="text" class="form-control">
                                    </td>
                                    <td>
                                        <input name="no_box[]" type="text" class="form-control">
                                    </td>
                                    <td>
                                        <input name="tipe[]" type="text" class="form-control">
                                    </td>
                                    <td>
                                        <select name="id_ket[]" id="" class="select3">
                                            @foreach ($ket_bk as $k)
                                                <option value="{{ $k->id_ket_bk }}">{{ $k->ket_bk }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="id_warna[]" id="" class="select3">
                                            @foreach ($warna as $w)
                                                <option value="{{ $w->id_warna }}">{{ $w->nm_warna }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" readonly
                                            value="{{ auth()->user()->name }}" name="pgws[]">
                                    </td>
                                    <td>
                                        <select name="nama[]" id="" class="select3 selectPengawas">
                                            <option value="">Pilih Pengawas</option>
                                            @foreach ($pengawas as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control"
                                            name="tgl_terima[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-end pcs_awal" name="pcs_awal[]"
                                            value="0">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-end gr_awal" name="gr_awal[]"
                                            value="0">
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
    </x-slot>
    @section('scripts')
        <script>
            $(".select3").select2()
            $('.selectPengawas').select2(); // Menginisialisasi semua elemen dengan kelas .selectPengawas sebagai Select2

            $('.selectPengawas').on('change', function() {
                var nilaiselect = $(this).val(); // Nilai terpilih dari select yang diubah

                // Memperbarui semua elemen dengan kelas .selectPengawas
                $('.selectPengawas').not(this).each(function() {
                    $(this).val(nilaiselect).trigger(
                    'change.select2'); // Update nilai Select2 dan trigger event change
                });
            });
            $(document).ready(function() {
                $(document).on("keyup", ".pcs_awal", function() {
                    var pcs_awal = $(this).val();
                    var pcs_hcr = $('.pcs_hcr').val();
                    var pcs_flex = $('.pcs_flex').val();

                    var total_pcs = parseFloat(pcs_awal) + parseFloat(pcs_hcr) + parseFloat(pcs_flex);

                    $('.pcs_ttl').val(total_pcs);
                });
                $(document).on("keyup", ".pcs_hcr", function() {
                    var pcs_hcr = $(this).val();
                    var pcs_awal = $('.pcs_awal').val();
                    var pcs_flex = $('.pcs_flex').val();

                    var total_pcs = parseFloat(pcs_awal) + parseFloat(pcs_hcr) + parseFloat(pcs_flex);

                    $('.pcs_ttl').val(total_pcs);
                });
                $(document).on("keyup", ".pcs_flex", function() {
                    var pcs_hcr = $('.pcs_hcr').val();
                    var pcs_awal = $('.pcs_awal').val();
                    var pcs_flex = $(this).val();

                    var total_pcs = parseFloat(pcs_awal) + parseFloat(pcs_hcr) + parseFloat(pcs_flex);

                    $('.pcs_ttl').val(total_pcs);
                });


                // Gram

                $(document).on("keyup", ".gr_awal", function() {
                    var gr_awal = $(this).val();
                    var gr_hcr = $('.gr_hcr').val();
                    var gr_flex = $('.gr_flex').val();

                    var total_gr = parseFloat(gr_awal) + parseFloat(gr_hcr) + parseFloat(gr_flex);

                    $('.gr_ttl').val(total_gr);
                });
                $(document).on("keyup", ".gr_hcr", function() {
                    var gr_hcr = $(this).val();
                    var gr_awal = $('.gr_awal').val();
                    var gr_flex = $('.gr_flex').val();

                    var total_gr = parseFloat(gr_awal) + parseFloat(gr_hcr) + parseFloat(gr_flex);

                    $('.gr_ttl').val(total_gr);
                });
                $(document).on("keyup", ".gr_flex", function() {
                    var gr_hcr = $('.gr_hcr').val();
                    var gr_awal = $('.gr_awal').val();
                    var gr_flex = $(this).val();

                    var total_gr = parseFloat(gr_awal) + parseFloat(gr_hcr) + parseFloat(gr_flex);

                    $('.gr_ttl').val(total_gr);
                });
            });
        </script>
    @endsection
</x-theme.app>
