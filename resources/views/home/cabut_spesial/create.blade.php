<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>

        <div class="row">

        </div>

    </x-slot>


    <x-slot name="cardBody">
        <style>

        </style>
        <form action="{{ route('cabutSpesial.create') }}" method="post" id="rupiahForm">
            @csrf
            <section class="row">
                <div class="col-lg-12">
                    <x-theme.alert pesan="{{ session()->get('error') }}" />
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="dhead">No Box</th>
                                <th class="dhead">Pgws</th>
                                <th class="dhead">Nama Anak</th>
                                <th class="dhead">Tgl Terima</th>
                                <th class="dhead">Target</th>
                                <th class="dhead text-end" width="110">Pcs Awal</th>
                                <th class="dhead text-end" width="110">Gr Awal</th>
                                <th class="dhead text-end" width="130">Ttl Rp</th>
                                <th class="dhead">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>

                                <td>
                                    <select name="no_box[]" id="" class="select3 pilihBox" count="1">
                                        <option value="">Pilih Box</option>
                                        <option value="h103">h103</option>
                                        <option value="h108">h108</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" readonly value="{{ auth()->user()->name }}">
                                    <input type="hidden" class="form-control" name="id_pengawas[]" readonly
                                        value="{{ auth()->user()->id }}">
                                </td>
                                <td>
                                    <select name="id_anak[]" id="" class="select3 pilihAnak" count="1">
                                        <option value="">Pilih Anak</option>
                                        @foreach ($anak as $d)
                                        <option data-kelas="{{ $d->kelas }}" value="{{ $d->id_anak }}">
                                            ({{ $d->kelas }}) {{ ucwords($d->nama) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" class="setHargaSatuan1">
                                </td>
                                <td>
                                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control"
                                        name="tgl_terima[]">
                                </td>
                                <td>
                                    <select name="id_target[]" id="" class="select3 pilihTarget" count="1">
                                        <option value="">Pilih Target</option>
                                        @foreach ($target as $d)
                                        <option value="{{ $d->id_grade_spesial }}">{{$d->ket}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end setPcs setPcs1" count='1' value="0"
                                        id="pcsInput" name="pcs_awal[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end setGr setGr1" count="1" value="0"
                                        id="grInput" name="gr_awal[]">
                                </td>
                                <td>
                                    <input readonly type="text" class="form-control rupiahInput text-end setRupiah1"
                                        value="0" name="ttl_rp[]">
                                    <input readonly type="hidden" class="form-control rupiahInput text-end rupiahBiasa1"
                                        value="0" name="rp_target[]">
                                    <input readonly type="hidden" class="form-control rupiahInput text-end pcsTarget1"
                                        value="0" name="pcs_target[]">
                                </td>
                            </tr>
                        </tbody>
                        <tbody id="tbh_baris">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="9">
                                    <button type="button" class="btn btn-block btn-lg tbh_baris"
                                        style="background-color: #F4F7F9; color: #435EBE; font-size: 14px; padding: 13px;">
                                        <i class="fas fa-plus"></i> Tambah Baris Baru
                                    </button>
                                </th>
                            </tr>
                        </tfoot>
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
        <a href="{{ route('cabut.index') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>
    </x-slot>
    @section('scripts')
    <script>
        $(".select3").select2()
            plusRow(1, 'tbh_baris', "tbh_baris")
            // formatRibuan('rupiah')

            $(document).on('change', '.pilihBox', function() {
                var no_box = $(this).val()
                var count = $(this).attr('count')
                $.ajax({
                    type: "GET",
                    url: "get_box_sinta",
                    data: {
                        no_box: no_box
                    },
                    dataType: "json",
                    success: function(r) {
                        console.log(r)
                        $(".setGr" + count).val(r.gr_awal - r.gr_cabut)
                        $(".setPcs" + count).val(r.pcs_awal - r.pcs_cabut)
                    }
                });
            })

            $(document).on('change', '.pilihTarget', function() {
               var count = $(this).attr('count');
               var id_target = $(this).val();
               $.ajax({
                type: "get",
                url: "{{route('cabutSpesial.getrp_target')}}",
                data: {
                    id_target : id_target,
                },
                dataType: "json",
                success: function (r) {
                    $('.pcsTarget'+ count).val(r['pcs']);
                    $('.setPcs'+ count).val(r['pcs']);
                    $('.rupiahBiasa' + count).val(r['rupiah']);
                    $('.setRupiah' + count).val(r['rupiah']);
                }
               });

                
            })

            $(document).on('keyup', '.setPcs', function() {
                var count = $(this).attr('count');
                var setpcs = $(this).val();
                var rupiah = $('.rupiahBiasa' + count).val()
                var pcs = $('.pcsTarget' + count).val()

                var rp = parseFloat(setpcs) *  (parseFloat(rupiah) / parseFloat(pcs));
                $('.setRupiah' + count).val(rp);



            })
    </script>
    @endsection
</x-theme.app>