<div class="row">
    <div class="col-lg-3 mb-2">
        <label for="">Pencarian :</label>
        <input autofocus type="text" id="pencarian2" class="form-control float-end">
    </div>
    <div class="col-lg-12">
        <table class="table table-striped" id="tablealdi2">
            <thead>
                <tr>
                    <th class="dhead" width="100">Nama Anak</th>
                    <th class="dhead" width="90">No Box</th>
                    <th class="dhead text-end" width="80">Pcs Awal</th>
                    <th class="dhead text-end" width="80">Gr Awal</th>
                    <th class="dhead text-end" width="80">Pcs Akhir</th>
                    <th class="dhead text-end" width="80">Gr Akhir</th>
                    <th class="dhead text-end" width="80">Pcus</th>
                    <th class="dhead text-end">Susut</th>
                    <th class="dhead" width="80">Bulan</th>
                    <th class="dhead text-center" width="140">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $i => $v)
                <tr>
                    <td class="d-none">
                            <input type="hidden" name="id_anak{{$i}}[]" value="{{ $v->id_anak }}">
                            <input type="hidden" name="no_box{{$i}}[]" value="{{ $v->no_box }}">
                            <input type="hidden" name="id_sortir{{$i}}[]" value="{{ $v->id_sortir }}">

                        </td>
                        <td class="fs-bold">{{ strtoupper($v->nama) }} / {{ $v->id_kelas }}</td>
                        <td class="fs-bold">{{ $v->no_box }}</td>

                        <td>
                            <input readonly value="{{ $v->pcs_awal }}" type="text" class="form-control text-end">
                        </td>
                        <td>
                            <input readonly value="{{ $v->gr_awal }}" type="text"
                                class="form-control text-end grAwalVal{{ $i }}">
                        </td>
                        <td>
                            <input value="{{ $v->pcs_akhir ?? $v->pcs_awal }}" required name="pcs_akhir{{$i}}[]" type="text"
                                class="form-control text-end">
                        </td>
                        <td>
                            <input value="{{ $v->gr_akhir ?? 0 }}" required name="gr_akhir{{$i}}[]" type="text"
                                class="form-control text-end grAkhirKeyup" count="{{ $i }}">
                        </td>
                        <td>
                            <input value="{{ $v->pcus ?? 0 }}" required name="pcus{{$i}}[]" type="text"
                                class="form-control text-end">
                        </td>
                        @php
                            $susut = empty($v->gr_akhir) ? 0 : (1 - $v->gr_akhir / $v->gr_awal) * 100;
                        @endphp
                        <td class="susut{{ $i }} text-end">
                            {{ number_format($susut,0) }} %
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
                                        {{ substr($l->nm_bulan,0,3) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td align="center">
                            <button style="font-size: 12px"
                                class="mt-1 btn btn-sm btn-{{ empty($v->gr_akhir) ? 'warning' : 'primary' }} saveSortirAkhir"
                                type="button" count="{{ $i }}">Save</button><br>
                            <a style="font-size: 12px" class="mt-1 btn btn-success btn-sm selesai" href="#"
                                id_sortir="{{ $v->id_sortir }}" href="#">Selesai </a><br>
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
