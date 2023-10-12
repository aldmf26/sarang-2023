<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #000000;
        line-height: 36px;
        font-size: 12px;
        width: auto;
    }
</style>
<section class="row">
    <div class="col-lg-4">
        <button type="button" class="btn btn-sm btn-primary mb-3 btnKembaliTambahCabut"><i class="fas fa-arrow-left"></i>
            Kembali</button>
    </div>
    <div class="col-lg-12">

        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead" width="100">Pgws</th>
                    <th class="dhead" width="100">Tgl Terima</th>
                    <th class="dhead" width="90">No Box</th>
                    <th class="dhead" width="150">Nama Anak</th>
                    <th class="dhead" width="100">Pcs/Gr</th>
                    <th class="dhead">Kelas / Paket</th>
                    <th class="dhead text-end" width="110">Pcs Awal</th>
                    <th class="dhead text-end" width="110">Gr Awal</th>
                    <th class="dhead text-end" width="130">Ttl Rp</th>
                    <th class="dhead">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($getAnak as $i => $x)
                    <tr class="baris{{ $i + 1 }}">
                        <td>
                            <input type="text" class="form-control" readonly value="{{ auth()->user()->name }}">
                            <input type="hidden" class="form-control" name="id_pengawas[]" readonly
                                value="{{ auth()->user()->id }}">
                        </td>
                        <td>
                            <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl_terima[]">
                            <input type="hidden" value="{{ $x->id_cabut }}" class="form-control" name="id_cabut[]">
                        </td>
                        <td>
                            <select name="no_box[]" id="" class="select3 pilihBox" count="{{ $i + 1 }}">
                                <option value="">Pilih Box</option>
                                @foreach ($boxBk as $d)
                                    @if ($d->gr_awal - $d->gr_cabut > 1)
                                        <option value="{{ $d->no_box }}">{{ ucwords($d->no_box) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <span class="h6">{{ strtoupper($x->nama) }} <span
                                    class="float-end">({{ $x->kelas }})</span></span>
                            <input type="hidden" name="id_anak[]" value="{{ $x->id_anak }}">

                            <input type="hidden" class="setHargaSatuanGr{{ $i + 1 }}">
                            <input type="hidden" class="setHargaSatuanPcs{{ $i + 1 }}">
                        </td>
                        <td>
                            <select name="hitung[]" id="" class="form-control pilihHitung pilihHitung{{$i+1}}" count="{{ $i + 1 }}">
                                <option value="">Pilih</option>
                                <option value="1">Pcs</option>
                                <option value="2">Gr</option>
                                {{-- <option value="3">Spesial</option>
                                <option value="4">Eo</option> --}}
                            </select>
                        </td>
                        <td>
                           
                            <select name="id_paket[]" id="" class="select3 pilihAnak pilihAnak{{$i+1}}"
                                count="{{ $i + 1 }}">
                                <option value="">Pilih</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control text-end setPcs setPcs{{ $i + 1 }}"
                                value="0" id="pcsInput" name="pcs_awal[]" count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input type="text" class="form-control text-end setGr setGr{{ $i + 1 }}"
                                count="{{ $i + 1 }}" value="0" id="grInput" name="gr_awal[]">
                        </td>
                        <td>
                            <input readonly type="text"
                                class="form-control rupiahInput text-end setRupiah{{ $i + 1 }}" value="0">
                            <input readonly type="hidden"
                                class="form-control rupiahInput text-end rupiahBiasa{{ $i + 1 }}" value="0"
                                name="rupiah[]">
                        </td>
                        <td align="center">
                            <button type="button" class="btn rounded-pill hapusCabutRow"
                                id_cabut="{{ $x->id_cabut }}" id_anak="{{ $x->id_anak }}"
                                count="{{ $i + 1 }}"><i class="fas fa-trash text-danger"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tbody id="tbh_baris">
            </tbody>
            {{-- <tfoot>
                <tr>
                    <th colspan="9">
                        <button type="button" class="btn btn-block btn-lg tbh_baris"
                            style="background-color: #F4F7F9; color: #435EBE; font-size: 14px; padding: 13px;">
                            <i class="fas fa-plus"></i> Tambah Baris Baru
                        </button>
                    </th>
                </tr>
            </tfoot> --}}
        </table>
        {{-- <button class="btn btn-primary btn-md saveCabut" type="button">Simpan</button> --}}
    </div>
</section>
