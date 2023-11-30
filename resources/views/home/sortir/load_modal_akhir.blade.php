<div class="row">
    <div class="col-lg-3 mb-2">
        <label for="">Pencarian :</label>
        <input autofocus type="text" id="pencarian2" class="form-control float-end">
    </div>
    <div class="col-lg-12">
        <table class="table table-striped" id="tablealdi2">
            <thead>
                <tr>
                    <th class="dhead" width="100">Tgl</th>
                    <th class="dhead" width="100">Nama Anak</th>
                    <th class="dhead" width="90">No Box</th>
                    <th class="dhead" width="90">Paket</th>
                    <th class="dhead text-end" width="80">Pcs Awal</th>
                    <th class="dhead text-end" width="80">Gr Awal</th>
                    <th class="dhead text-end" width="80">Pcs Akhir</th>
                    <th class="dhead text-end" width="80">Gr Akhir</th>
                    <th class="dhead text-end" width="80">Pcus</th>
                    <th class="dhead text-end" width="80">Susut</th>
                    <th class="dhead text-end">Ttl Rp</th>
                    <th class="dhead" width="100">Bulan</th>
                    <th class="dhead text-center" width="180">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $i => $v)
                    <tr>
                        <td class="d-none">
                            <input type="hidden" name="id_anak{{ $i }}[]" value="{{ $v->id_anak }}">
                            <input type="hidden" name="no_box{{ $i }}[]" value="{{ $v->no_box }}">
                            <input type="hidden" name="id_sortir{{ $i }}[]" value="{{ $v->id_sortir }}">
                            <input type="hidden" class="rpTarget{{ $i }}" value="{{ $v->rp_target }}">
                            <input type="hidden" class="dendaSusut{{ $i }}" value="{{ $v->denda_susut }}">
                            <input type="hidden" class="bts_denda_sst{{ $i }}"
                                value="{{ $v->bts_denda_sst }}">
                            <input type="hidden" class="batas_denda_rp{{ $i }}"
                                value="{{ $v->batas_denda_rp }}">
                            <input type="hidden" class="denda_susut{{ $i }}"
                                value="{{ $v->denda_susut }}">
                            <input type="hidden" class="dendakelas{{ $i }}" value="{{ $v->denda }}">
                            <input class="ttlRpSet{{ $i }}" type="text"
                                name="ttl_rp{{ $i }}[]" value="{{ $v->ttl_rp }}">

                        </td>
                        <td>
                            <input value="{{ $v->tgl }}" name="tgl{{ $i }}[]" type="date" class="form-control">
                        </td>
                        <td class="fs-bold">{{ strtoupper($v->nama) }} / {{ $v->id_kelas }}</td>
                        <td class="fs-bold">{{ $v->no_box }}</td>
                        <td class="fs-bold">{{ $v->kelas }}</td>

                        
                        <td>
                            <input readonly value="{{ $v->pcs_awal }}" type="text" class="form-control text-end">
                        </td>
                        <td>
                            <input readonly value="{{ $v->gr_awal }}" type="text"
                                class="form-control text-end grAwalVal{{ $i }}">
                        </td>
                        <td>
                            <input
                                value="{{ empty($v->pcs_akhir) || $v->pcs_akhir == 0 ? $v->pcs_awal : $v->pcs_akhir }}"
                                required name="pcs_akhir{{ $i }}[]" type="text"
                                class="form-control text-end">
                        </td>
                        <td>
                            <input value="{{ $v->gr_akhir ?? 0 }}" required name="gr_akhir{{ $i }}[]"
                                type="text" class="form-control text-end grAkhirKeyup" count="{{ $i }}">
                        </td>
                        <td>
                            <input value="{{ $v->pcus ?? 0 }}" required name="pcus{{ $i }}[]"
                                type="text" class="form-control text-end">
                        </td>
                        @php
                            $susut = empty($v->gr_akhir) ? 0 : (1 - $v->gr_akhir / $v->gr_awal) * 100;
                        @endphp
                        <td class="susut{{ $i }} text-end">
                            {{ number_format($susut, 0) }} %
                        </td>
                        <td align="right" class="h6">
                            <span class="ttlRpKeyup{{ $i }}">{{ number_format($v->ttl_rp, 0) }}</span>
                        </td>
                        <td>
                            <select name="bulan{{ $i }}[]" class="form-control">
                                <option value="0">Pilih Bulan</option>
                                @php
                                    $listBulan = DB::table('bulan')->get();
                                @endphp
                                @foreach ($listBulan as $l)
                                    <option value="{{ $l->bulan }}"
                                        {{ $v->bulan == $l->bulan ? 'selected' : '' }}>
                                        {{ substr($l->nm_bulan, 0, 3) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td align="center">
                            <button style="font-size: 12px"
                                class="mt-1 btn{{$i}} btn btn-sm btn-{{ empty($v->gr_akhir) ? 'warning' : 'primary' }} saveSortirAkhir"
                                type="button" count="{{ $i }}">Save</button>
                            <a style="font-size: 12px" class="mt-1 btn btn-success btn-sm selesai" href="#"
                                id_sortir="{{ $v->id_sortir }}" href="#">Selesai </a>
                            <button style="font-size: 12px" class="mt-1 btn btn-sm btn-danger cancelSortirAkhir"
                                type="button" count="{{ $i }}"
                                id_sortir="{{ $v->id_sortir }}">Cancel</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
