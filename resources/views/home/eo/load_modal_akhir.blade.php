<div class="row">
    {{-- <input type="hidden" name="id_eo" value="{{ $detail->id_eo }}">
    <input type="hidden" name="id_anak" value="{{ $detail->id_anak }}">
    <input type="hidden" name="no_box" value="{{ $detail->no_box }}"> --}}
    <div class="col-lg-3 mb-2">
        <label for="">Pencarian :</label>
        <input autofocus type="text" id="pencarian2" class="form-control">
    </div>

    <div class="col-lg-12">
        <table class="table table-striped" id="tablealdi2">
            <thead>
                <tr>
                    <th class="dhead">Tgl Terima</th>
                    <th class="dhead" width="80">No Box</th>
                    <th class="dhead" width="80">Nama</th>
                    <th class="dhead">Kelas</th>
                    <th class="dhead text-end">Gr EO Awal</th>
                    <th class="dhead text-end">Gr EO Akhir</th>
                    <th class="dhead text-end" width="120">Ttl Rp</th>
                    <th class="dhead" width="120">Dibayar</th>
                    <th class="dhead text-center" width="180">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $i => $x)
                    <tr>
                        <td>
                            <input name="tgl_serah{{ $i + 1 }}[]" style="font-size: 12px;" type="date"
                                value="{{ date('Y-m-d') }}" class="form-control">
                        </td>
                        <td>
                            <input type="text" readonly value="{{ $x->no_box }}" class="form-control">
                            <input type="hidden" value="{{ $x->id_eo }}" name="id_eo{{ $i + 1 }}[]">
                        </td>
                        <td class="fs-bold">{{ strtoupper($x->nama) }}</td>
                        <td>
                            <input type="text" readonly value="{{ $x->kelas }}" class="form-control">
                            <input type="hidden" name="id_paket{{ $i + 1 }}[]" readonly
                                value="{{ $x->id_paket }}" class="form-control">
                            <input type="hidden" name="rp_target{{ $i + 1 }}[]" readonly
                                value="{{ $x->rp_target }}" class="form-control">
                        </td>
                        <td>
                            <input readonly value="{{ $x->gr_eo_awal }}" name="gr_eo_awal{{ $i + 1 }}[]"
                                type="text" class="form-control text-end">
                            <input value="{{ $x->rupiah }}" type="hidden" name="rupiah{{ $i + 1 }}[]"
                                class="rupiah_kelas{{ $i + 1 }}">
                        </td>

                        <td>
                            <input name="gr_eo_akhir{{ $i + 1 }}[]" value="{{ $x->gr_eo_akhir }}"
                                type="text" class="form-control text-end grEoAkhirKeyup"
                                count="{{ $i + 1 }}">
                        </td>
                        <td align="right">

                            <input type="hidden" name="ttl_rp{{ $i + 1 }}[]" value="{{ $x->ttl_rp ?? 0 }}"
                                class="ttlRpSet{{ $i + 1 }}">
                            <span
                                class="ttlRpKeyup{{ $i + 1 }} h6">{{ number_format($x->ttl_rp ?? 0, 0) }}</span>
                        </td>
                        <td>
                            <select name="bulan{{ $i + 1 }}[]" class="form-control">
                                <option value="0">Pilih Bulan </option>
                               
                                @foreach (getListBulan() as $l)
                                    <option value="{{ $l->bulan }}"
                                        {{ $x->bulan == $l->bulan ? 'selected' : '' }}>
                                        {{ $l->nm_bulan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button style="font-size: 12px"
                                class="btn btn-sm btn-{{ empty($x->gr_eo_akhir) ? 'warning' : 'primary' }} saveCabutAkhir"
                                type="button" count="{{ $i + 1 }}">Save</button>
                            <a style="font-size: 12px" class="btn btn-success btn-sm selesai" href="#"
                                id_cabut="{{ $x->id_eo }}" href="#">Selesai</a>
                            <button style="font-size: 12px" class="btn btn-sm btn-danger cancelCabutAkhir"
                                type="button" count="{{ $i + 1 }}"
                                id_cabut="{{ $x->id_eo }}">Cancel</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
