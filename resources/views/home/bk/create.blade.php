<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <div class="col-lg-6">
            <h6 class="float-start mt-1">{{ $title }}</h6>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <style>
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #000000;
                line-height: 36px;
                /* font-size: 12px; */
                width: 170px;
            }
        </style>
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
                                    <select name="ket" id="" class="select2">
                                        <option value="k">K</option>
                                        <option value="kl">Kl</option>
                                        <option value="ks">Ks</option>
                                        <option value="nil">Nil</option>
                                        <option value="flx">flx</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="warna" id="" class="select2">
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
                                <th class="dhead">Tipe</th>
                                <th class="dhead">Pgws</th>
                                <th class="dhead">Nama</th>
                                <th class="dhead">Tgl Terima</th>
                                <th class="dhead text-end">Pcs Awal</th>
                                <th class="dhead text-end">Pcs Hcr</th>
                                <th class="dhead text-end">Pcs Flex</th>
                                <th class="dhead text-end">Pcs Ttl</th>
                                <th class="dhead text-end">Ttl Rp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" value="BK" readonly name="tipe">
                                </td>
                                <td>
                                    <input type="text" class="form-control" readonly value="{{ 'Sinta' }}" name="pgws">
                                </td>
                                <td>
                                    <select name="nama" id="" class="select2">
                                        <option value="jenah">Jenah</option>
                                        <option value="jenah">Jenah Aldi</option>
                                        <option value="jenah">Jenah Aldi Aldi</option>
                                        <option value="jenah">Jenah Aldi Aldi Aldi</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl_terima">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end" name="pcs_awal">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end" name="pcs_hcr">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end" name="pcs_flex">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end" name="pcs_ttl">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end" readonly value="0" name="ttl_rp">
                                </td>
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
        $(".select2").select2()
    </script>
    @endsection
</x-theme.app>
