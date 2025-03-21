<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">

    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <form action="{{ route('bk.update') }}" method="post">
            @csrf
            <section class="row">
                <div class="col-lg-12">
                    <input type="hidden" name="kategori" value="{{ request()->get('kategori') }}">
                    <table class="table table-striped" width="100%">
                        <thead>
                            <tr>
                                <th class="dhead">No</th>

                                <th class="dhead">Nama Partai</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead">Tipe</th>
                                <th class="dhead">Ket</th>
                                <th class="dhead">Warna</th>
                                <th class="dhead">Pgws</th>
                                <th class="dhead">Nama</th>
                                <th class="dhead">Tgl Terima</th>
                                <th class="dhead text-end">Pcs Awal</th>
                                <th class="dhead text-end">Gr Awal</th>
                                <th class="dhead text-end">Susut</th>
                            </tr>
                        </thead>
                        @foreach ($no_nota as $i => $n)
                            @php
                                $detail = DB::table('bk as a')->where('id_bk', $n)->first();
                            @endphp
                            <input type="hidden" name="id_bk[]" value="{{ $detail->id_bk }}">
                            <tbody>
                                <tr>
                                    <td>{{ $i + 1 }}</td>

                                    <td>
                                        <input name="nm_partai[]" value="{{ $detail->nm_partai }}" type="text"
                                            class="form-control">
                                    </td>
                                    <td>
                                        <input name="no_box[]" value="{{ $detail->no_box }}" type="text"
                                            class="form-control">
                                    </td>
                                    <td>
                                        <input name="tipe[]" value="{{ $detail->tipe }}" type="text"
                                            class="form-control">
                                    </td>
                                    <td>
                                        <input name="ket[]" value="{{ $detail->ket }}" type="text"
                                            class="form-control">
                                    </td>
                                    <td>
                                        <input name="warna[]" value="{{ $detail->warna }}" type="text"
                                            class="form-control">

                                    </td>
                                    <td>
                                        <input type="text" class="form-control" readonly
                                            value="{{ auth()->user()->name }}" name="pgws[]">
                                    </td>
                                    <td>
                                        <select name="nama[]" id="" class="select3">
                                            <option value="">Pilih Pengawas</option>
                                            @foreach ($pengawas as $p)
                                                <option {{ $p->id == $detail->penerima ? 'selected' : '' }}
                                                    value="{{ $p->id }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="date" value="{{ $detail->tgl }}" class="form-control"
                                            name="tgl_terima[]">
                                    </td>
                                    <td>
                                        <input type="text" value="{{ $detail->pcs_awal }}"
                                            class="form-control text-end pcs_awal" name="pcs_awal[]">
                                    </td>
                                    <td>
                                        <input type="text" value="{{ $detail->gr_awal }}"
                                            class="form-control text-end gr_awal" name="gr_awal[]">
                                    </td>
                                    <td>
                                        <input type="text" value="{{ $detail->susut }}"
                                            class="form-control text-end " name="susut[]">
                                    </td>

                                </tr>
                            </tbody>
                        @endforeach

                    </table>
                    <br>
                    <hr style="border: 1px solid #435EBE">
                    <br>
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
