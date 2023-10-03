<div class="row">
    <div class="col-lg-4 mb-2">
        <label for="">Pencarian :</label>
        <input autofocus type="text" id="pencarian2" class="form-control float-end">
    </div>
    <div class="col-lg-12">
        <table class="table table-bordered" style="border: 0.5px solid #a0a7c1" id="tablealdi2">
            <thead>
                <tr>
                    <th class="dhead">Tgl Terima Akhir</th>
                    <th class="dhead">No Box</th>
                    <th class="dhead" width="90">Nama Anak</th>
                    <th class="dhead text-end" width="85">Pcs Awal</th>
                    <th class="dhead text-end" width="85">Gr Awal</th>
                    <th class="dhead text-end" width="85">Gr Flx</th>
                    <th class="dhead text-end" width="85">Pcs Akhir</th>
                    <th class="dhead text-end" width="85">Gr Akhir</th>
                    <th class="dhead text-end" width="85">EOT</th>
                    <th class="dhead text-end" width="85">Pcs Hcr</th>
                    <th class="dhead text-end" width="150">Ttl Rp</th>
                    <th class="dhead text-center" width="110">Aksi</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($datas as $i => $d)
                    @php
                        $hasil = rumusTotalRp($d);
                    @endphp

                    <tr data-csrf-token="{{ csrf_token() }}">
                        <td style="display:none">
                            <input type="hidden" name="id_anak{{ $i + 1 }}[]" value="{{ $d->id_anak }}">
                            <input type="hidden" name="no_box{{ $i + 1 }}[]" value="{{ $d->no_box }}">
                            <input type="hidden" name="rupiah{{ $i + 1 }}[]" value="{{ $d->rupiah }}">
                            <input type="text" name="gr_kelas{{ $i + 1 }}[]" value="{{ $d->gr_kelas }}">
                            <input type="text" name="rupiah_kelas{{ $i + 1 }}[]"
                                value="{{ $d->rupiah_kelas }}">
                            <input type="text" name="id_kelas{{ $i + 1 }}[]" value="{{ $d->id_kelas }}">
                            <input type="text" name="rp_bonus{{ $i + 1 }}[]" value="{{ $d->rp_bonus }}">
                            <input class="ttlRpSet{{ $i + 1 }}" type="text"
                                name="ttl_rp{{ $i + 1 }}[]" value="{{ $hasil->ttl_rp }}">
                        </td>
                        <td>
                            <input value="{{ $d->tgl_serah ?? date('Y-m-d') }}" name="tgl_serah{{ $i + 1 }}[]"
                                type="date" class="form-control">
                        </td>
                        <td>
                            <input type="text" readonly value="{{ $d->no_box }}" class="form-control">
                        </td>
                        <td class="fs-bold">{{ strtoupper($d->nama) }}</td>
                        <td>
                            <input readonly value="{{ $d->pcs_awal }}" type="text" class="form-control text-end">
                        </td>
                        <td>
                            <input readonly value="{{ $d->gr_awal }}" name="gr_awal{{ $i + 1 }}[]"
                                type="text" class="form-control text-end">
                        </td>
                        <td>
                            <input name="gr_flx{{ $i + 1 }}[]" value="{{ $d->gr_flx ?? 0 }}" type="text"
                                class="form-control text-end grFlexKeyup" count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input value="{{ $d->pcs_akhir ?? 0 }}" required name="pcs_akhir{{ $i + 1 }}[]"
                                type="text" class="form-control text-end pcsAkhirKeyup" count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input value="{{ $d->gr_akhir ?? 0 }}" required name="gr_akhir{{ $i + 1 }}[]"
                                type="text" class="form-control text-end grAkhirKeyup" count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input value="{{ $d->eot ?? 0 }}" name="eot{{ $i + 1 }}[]" type="text"
                                class="form-control text-end eotKeyup" count="{{ $i + 1 }}">
                        </td>
                        <td>
                            <input value="{{ $d->pcs_hcr ?? 0 }}" name="pcs_hcr{{ $i + 1 }}[]" type="text"
                                class="form-control text-end pcsHcrKeyup" count="{{ $i + 1 }}">
                        </td>


                        <td align="right" class="h6">
                            Rp <span class="ttlRpKeyup{{ $i + 1 }}">{{ number_format($hasil->ttl_rp, 0) }}</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-{{empty($d->gr_akhir) ? 'warning' : 'primary'}} saveCabutAkhir" type="button"
                                count="{{ $i + 1 }}">Save</button>
                                <a class="btn btn-success btn-sm selesai" href="#" id_cabut="{{ $d->id_cabut }}"
                                href="#" data-bs-toggle="modal" data-bs-target="#selesai"><i class="fas fa-check"></i></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
