<div class="row">
    <div class="col-lg-3 mb-2">
        <label for="">Pencarian :</label>
        <input autofocus type="text" id="pencarian2" class="form-control float-end">
    </div>
    <div class="col-lg-2 mb-2">
        <div class="form-group">
            <label for="">Urutkan Dengan</label>
            <select class="form-control" name="" id="orderBy">
                <option value="">Pilih</option>
                <option {{ $orderBy == 'nama' ? 'selected' : '' }} value="nama">Nama</option>
                <option {{ $orderBy == 'kelas' ? 'selected' : '' }} value="kelas">Kelas</option>
                <option {{ $orderBy == 'nobox' ? 'selected' : '' }} value="nobox">No Box</option>
                <option {{ $orderBy == 'tgl_terima' ? 'selected' : '' }} value="tgl_terima">Tgl Cabut</option>
            </select>
        </div>
    </div>
    <div class="col-lg-12">
        <table class="table table-bordered" style="border: 0.5px solid #a0a7c1; font-size: 12px" id="tablealdi2">
            <thead>
                <tr>
                    <th class="dhead">Tgl Terima</th>
                    <th class="dhead" width="60">No Box</th>
                    <th class="dhead" width="100">Nama Anak</th>
                    <th class="dhead text-end" width="85">Pcs Awal</th>
                    <th class="dhead text-end" width="85">Gr Awal</th>
                    <th class="dhead text-end" width="75">Gr Flx</th>
                    <th class="dhead text-end" width="75">Pcs Akhir</th>
                    <th class="dhead text-end" width="75">Gr Akhir</th>
                    <th class="dhead text-end" width="75">EOT</th>
                    <th class="dhead text-end" width="75">Pcs Hcr</th>
                    <th class="dhead text-end" width="105">Ket Hcr</th>
                    <th class="dhead text-end" width="55">Susut</th>
                    <th class="dhead text-end" width="120">Ttl Rp</th>
                    <th class="dhead" width="120">Dibayar</th>
                    <th class="dhead text-center" width="140">Aksi</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($datas as $i => $d)
                    @php
                        $hasil = rumusTotalRp($d);
                    @endphp

                    <tr data-csrf-token="{{ csrf_token() }}">
                        <td class="d-none">
                            <input type="text" name="id_anak{{ $i + 1 }}[]" value="{{ $d->id_anak }}">
                            <input type="text" name="id_cabut{{ $i + 1 }}[]" value="{{ $d->id_cabut }}">
                            <input type="text" name="no_box{{ $i + 1 }}[]" value="{{ $d->no_box }}">
                            <input type="text" name="rupiah{{ $i + 1 }}[]" value="{{ $d->rupiah }}">
                            <input type="text" name="gr_kelas{{ $i + 1 }}[]" value="{{ $d->gr_kelas }}">
                            <input type="text" name="rupiah_kelas{{ $i + 1 }}[]"
                                value="{{ $d->rupiah_kelas }}">
                            <input type="text" name="id_kelas{{ $i + 1 }}[]" value="{{ $d->id_kelas }}">
                            <input type="text" name="rp_bonus{{ $i + 1 }}[]" value="{{ $d->rp_bonus }}">
                            <input type="text" name="id_kategori{{ $i + 1 }}[]"
                                value="{{ $d->id_kategori }}">
                            <input type="text" name="jenis{{ $i + 1 }}[]" value="{{ $d->jenis }}">
                            <input type="text" name="denda_susut_persen{{ $i + 1 }}[]"
                                value="{{ $d->denda_susut_persen }}">
                            <input type="text" name="denda_hcr{{ $i + 1 }}[]" value="{{ $d->denda_hcr }}">
                            <input type="text" name="pcs_kelas{{ $i + 1 }}[]" value="{{ $d->pcs_kelas }}">
                            <input type="text" name="batas_susut{{ $i + 1 }}[]"
                                value="{{ $d->batas_susut }}">
                            <input type="text" name="bonus_susut{{ $i + 1 }}[]"
                                value="{{ $d->bonus_susut }}">
                            <input type="text" name="eot_rp{{ $i + 1 }}[]" value="{{ $d->eot_rp }}">
                            <input class="ttlRpSet{{ $i + 1 }}" type="text"
                                name="ttl_rp{{ $i + 1 }}[]" value="{{ $hasil->ttl_rp }}">
                        </td>
                        <td>
                            <input style="width:140px" value="{{ $d->tgl_serah ?? date('Y-m-d') }}"
                                name="tgl_serah{{ $i + 1 }}[]" type="date" class="form-control">
                        </td>
                        <td class="fs-bold">
                            {{ $d->no_box }}
                        </td>
                        <td class="fs-bold">{{ strtoupper($d->nama) }} {{ $d->id_kelas }} <br>
                            {{ date('d M y', strtotime($d->tgl_terima)) }}</td>
                        <td>
                            <input readonly value="{{ $d->pcs_awal }}" type="text"
                                class="form-control text-end">
                        </td>
                        <td>
                            <input readonly value="{{ $d->gr_awal }}" name="gr_awal{{ $i + 1 }}[]"
                                type="text" class="form-control text-end">
                        </td>
                        <td>
                            <input name="gr_flx{{ $i + 1 }}[]" value="{{ $d->gr_flx }}" type="text"
                                class="form-control text-end grFlexKeyup" count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input value="{{ $d->pcs_awal }}" required name="pcs_akhir{{ $i + 1 }}[]"
                                type="text" class="form-control text-end pcsAkhirKeyup"
                                count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input value="{{ $d->gr_akhir }}" required name="gr_akhir{{ $i + 1 }}[]"
                                type="text" class="form-control text-end grAkhirKeyup"
                                count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input value="{{ $d->eot }}" name="eot{{ $i + 1 }}[]" type="text"
                                class="form-control text-end eotKeyup" count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input value="{{ $d->pcs_hcr }}" name="pcs_hcr{{ $i + 1 }}[]" type="text"
                                class="form-control text-end pcsHcrKeyup" count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input value="{{ $d->ket_hcr }}" name="ket_hcr{{ $i + 1 }}[]" type="text"
                                class="form-control text-end ketHcrKeyup" count="{{ $i + 1 }}">
                        </td>

                        <td class="susut{{ $i + 1 }} text-end">
                            {{ number_format($hasil->susut == 100 ? 0 : $hasil->susut, 0) }} %
                        </td>


                        <td align="right" class="h6">
                            <span class="ttlRpKeyup{{ $i + 1 }}">{{ number_format($hasil->ttl_rp, 0) }}</span>
                        </td>
                        <td>
                            <select name="bulan{{ $i + 1 }}[]" class="form-control">
                                <option value="0">Pilih Bulan</option>
                              
                                @foreach (getListBulan() as $l)
                                    <option value="{{ $l->bulan }}"
                                        {{ $d->bulan == $l->bulan ? 'selected' : '' }}>
                                        {{ $l->nm_bulan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>

                            <button style="font-size: 12px"
                                class="btn{{ $i + 1 }} btn btn-sm btn-{{ empty($d->gr_akhir) ? 'warning' : 'primary' }} saveCabutAkhir"
                                type="button" count="{{ $i + 1 }}">Save</button>
                            @php
                                $eot = $d->eot ?? 0;
                            @endphp
                            @if ($eot > 0)
                                {{-- @if ($eot >= rumusTotalRp($d)->batas_eot || !empty($d->gr_akhir)) --}}
                                <a style="font-size: 12px"
                                    class="btn btn-success btn-sm selesai selesai{{ $i + 1 }}" href="#"
                                    id_cabut="{{ $d->id_cabut }}" href="#">Selesai </a>
                            @endif

                            <a style="font-size: 12px"
                                class="d-none btn btn-success btn-sm selesai selesai{{ $i + 1 }}"
                                href="#" id_cabut="{{ $d->id_cabut }}" href="#">Selesai </a>
                            <button style="font-size: 12px" class="btn btn-sm btn-danger cancelCabutAkhir"
                                type="button" count="{{ $i + 1 }}"
                                id_cabut="{{ $d->id_cabut }}">Cancel</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
