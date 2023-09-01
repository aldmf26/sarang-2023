<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">

    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <form action="{{ route('bk.create') }}" method="post">
            @csrf
            <section class="row">
                <div class="col-lg-8">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="dhead">No Lot</th>
                                <th class="dhead">No Box</th>
                                <th class="dhead">Tipe</th>
                                <th class="dhead">Ket</th>
                                <th class="dhead">Warna</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input name="no_lot" type="text" class="form-control">
                                </td>
                                <td>
                                    <input name="no_box" type="text" class="form-control">
                                </td>
                                <td>
                                    <input name="tipe" type="text" class="form-control">
                                </td>
                                <td>
                                    <select name="ket" id="" class="select3">
                                        <option value="k">K</option>
                                        <option value="kl">Kl</option>
                                        <option value="ks">Ks</option>
                                        <option value="nil">Nil</option>
                                        <option value="flx">flx</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="warna" id="" class="select3">
                                        <option value="s">S</option>
                                        <option value="a">A</option>
                                        <option value="y">Y</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="col-lg-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="dhead" width="70">Tipe</th>
                                <th class="dhead">Pgws</th>
                                <th class="dhead">Nama</th>
                                <th class="dhead">Tgl Terima</th>
                                <th class="dhead text-end">Pcs Awal</th>
                                <th class="dhead text-end">Gr Awal</th>
                                <th class="dhead text-end">Pcs Hcr</th>
                                <th class="dhead text-end">Gr Hcr</th>
                                <th class="dhead text-end">Pcs Flex</th>
                                <th class="dhead text-end">Gr Flex</th>
                                <th class="dhead text-end">Pcs Ttl</th>
                                <th class="dhead text-end">Gr Ttl</th>
                                {{-- <th class="dhead text-end">Ttl Rp</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" value="BK" readonly name="tipe">
                                </td>
                                <td>
                                    <input type="text" class="form-control" readonly value="{{ auth()->user()->name }}"
                                        name="pgws">
                                </td>
                                <td>
                                    <select name="nama" id="" class="select3">
                                        {{-- @foreach ($pengawas as $d)
                                        <option value="{{ $d->posisi_id }}">{{ ucwords($d->name) }}</option>
                                        @endforeach --}}
                                        <option value="">Pilih Pengawas</option>
                                        <option value="1">Jenah</option>
                                        <option value="2">Nurul</option>
                                        <option value="3">Erna</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control"
                                        name="tgl_terima">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end pcs_awal" name="pcs_awal" value="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end gr_awal" name="gr_awal" value="0">
                                </td>
                                {{-- <td>
                                    <input type="text" class="form-control text-end pcs_hcr" name="pcs_hcr" value="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end gr_hcr" name="gr_hcr" value="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end pcs_flex" name="pcs_flex" value="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end gr_flex" name="gr_flex" value="0">
                                </td> --}}
                                <td>
                                    <input type="text" class="form-control text-end pcs_ttl" name="pcs_ttl" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end gr_ttl" name="gr_ttl" readonly>
                                </td>
                                {{-- <td>
                                    <input type="text" class="form-control text-end" value="0" name="ttl_rp">
                                </td> --}}
                            </tr>
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

        $(document).ready(function () {
            $(document).on("keyup", ".pcs_awal", function () {
                var pcs_awal = $(this).val();
                var pcs_hcr = $('.pcs_hcr').val();
                var pcs_flex = $('.pcs_flex').val();

                var total_pcs = parseFloat(pcs_awal) + parseFloat(pcs_hcr) + parseFloat(pcs_flex);

                $('.pcs_ttl').val(total_pcs);
            });
            $(document).on("keyup", ".pcs_hcr", function () {
                var pcs_hcr = $(this).val();
                var pcs_awal = $('.pcs_awal').val();
                var pcs_flex = $('.pcs_flex').val();

                var total_pcs = parseFloat(pcs_awal) + parseFloat(pcs_hcr) + parseFloat(pcs_flex);

                $('.pcs_ttl').val(total_pcs);
            });
            $(document).on("keyup", ".pcs_flex", function () {
                var pcs_hcr = $('.pcs_hcr').val();
                var pcs_awal = $('.pcs_awal').val();
                var pcs_flex = $(this).val();

                var total_pcs = parseFloat(pcs_awal) + parseFloat(pcs_hcr) + parseFloat(pcs_flex);

                $('.pcs_ttl').val(total_pcs);
            });


            // Gram

            $(document).on("keyup", ".gr_awal", function () {
                var gr_awal = $(this).val();
                var gr_hcr = $('.gr_hcr').val();
                var gr_flex = $('.gr_flex').val();

                var total_gr = parseFloat(gr_awal) + parseFloat(gr_hcr) + parseFloat(gr_flex);

                $('.gr_ttl').val(total_gr);
            });
            $(document).on("keyup", ".gr_hcr", function () {
                var gr_hcr = $(this).val();
                var gr_awal = $('.gr_awal').val();
                var gr_flex = $('.gr_flex').val();

                var total_gr = parseFloat(gr_awal) + parseFloat(gr_hcr) + parseFloat(gr_flex);

                $('.gr_ttl').val(total_gr);
            });
            $(document).on("keyup", ".gr_flex", function () {
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