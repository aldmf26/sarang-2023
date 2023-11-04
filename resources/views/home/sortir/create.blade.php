<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>

        <div class="row">

        </div>

    </x-slot>


    <x-slot name="cardBody">

        <form action="{{ route('sortir.create') }}" method="post" id="rupiahForm">
            @csrf
            <section class="row">
                <x-theme.alert pesan="{{ session()->get('error') }}" />
                <div class="col-lg-6">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead">Pgws</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="no_box" id="" required class="select3 pilihBox"
                                        count="1">
                                        <option value="">Pilih Box</option>
                                        @foreach ($boxBk as $d)
                                            @if ($d->pcs_awal - $d->pcs_cabut > 1)
                                                <option value="{{ $d->no_box }}">{{ ucwords($d->no_box) }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input readonly type="text" class="form-control text-end setPcs1">
                                </td>
                                <td>
                                    <input readonly type="text" class="form-control text-end setGr1">
                                </td>
                                <td>
                                    <input type="text" class="form-control" readonly
                                        value="{{ auth()->user()->name }}">
                                    <input type="hidden" class="form-control" name="id_pengawas" readonly
                                        value="{{ auth()->user()->id }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="dhead" width="300">Nama Anak</th>
                                <th class="dhead">Tipe</th>
                                <th class="dhead">Tgl Terima</th>
                                <th class="dhead text-end" width="110">Pcs Awal</th>
                                <th class="dhead text-end" width="110">Gr Awal</th>
                                <th class="dhead text-end" width="130">Rp Target</th>
                                <th class="dhead">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="id_anak[]" style="width:100%;" id=""
                                        class="select3 pilihAnak" count="1">
                                        <option value="">Pilih Anak</option>
                                        @foreach ($anak as $d)
                                            <option data-kelas="{{ $d->kelas }}" value="{{ $d->id_anak }}">
                                                {{ ucwords($d->nama) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" class="setHargaSatuan1">
                                </td>
                                @php
                                    $kelas = DB::table('tb_kelas_sortir')
                                        ->orderBy('id_kelas', 'ASC')
                                        ->get();
                                @endphp
                                <td>
                                    <select name="tipe[]" id="" class="form-control">
                                        @foreach ($kelas as $i => $d)
                                            <option value="{{ $d->id_kelas }}"
                                                {{ $d->kelas == 'brg' ? 'selected' : '' }}>{{ strtoupper($d->kelas) }}
                                            </option>
                                        @endforeach
                                    </select>

                                </td>
                                <td>
                                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control"
                                        name="tgl_terima[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end" value="0" id="pcsInput"
                                        name="pcs_awal[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control text-end setGr" count="1"
                                        value="0" id="grInput" name="gr_awal[]">
                                </td>
                                <td>
                                    <input readonly type="text" class="form-control rupiahInput text-end setRupiah1"
                                        value="0" name="rupiah[]">
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
        </form>
    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary button-save">Simpan</button>
        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <a href="{{ route('sortir.index') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>
    </x-slot>
    @section('scripts')
        <script>
            $(".select3").select2()
            plusRow(1, 'tbh_baris', "tbh_baris")

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

            $(document).on('input', '.setGr', function() {
                var count = $(this).attr('count')
                var hasil = $(this).val()
                var rupiah = (120000 / 500) * parseFloat(hasil)
                rupiah = rupiah.toLocaleString('id-ID', {
                    maximumFractionDigits: 0
                })
                $(".setRupiah" + count).val(rupiah)
            })
        </script>
    @endsection
</x-theme.app>
