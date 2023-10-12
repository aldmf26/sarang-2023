<table class="table table-striped">
    <thead>
        <tr>
            <th class="dhead">No Box</th>
            <th class="dhead">Pgws</th>
            <th class="dhead">Nama Anak</th>
            <th class="dhead">Tgl Terima</th>
            <th class="dhead">Kelas / Paket</th>
            <th class="dhead text-end" width="110">Pcs Awal</th>
            <th class="dhead text-end" width="110">Gr Awal</th>
            <th class="dhead text-end" width="130">Ttl Rp</th>
            <th class="dhead" width="50">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($anak_spesial as $no => $a)
            <tr class="baris{{ $no }}">
                <td>
                    <select name="no_box[]" id="" class="select2-add pilihBox pilihBox{{ $no }}"
                        count="{{ $no }}">
                        <option value="">Pilih Box</option>
                        @foreach ($boxBk as $d)
                            @if ($d->pcs_awal - $d->pcs_cabut > 1)
                                <option value="{{ $d->no_box }}">{{ ucwords($d->no_box) }}</option>
                            @endif
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" readonly value="{{ auth()->user()->name }}">
                    <input type="hidden" class="form-control" name="id_pengawas[]" readonly
                        value="{{ auth()->user()->id }}">
                </td>
                <td>
                    {{ $a->nama }}
                    <input type="hidden" name="id_anak[]" value="{{ $a->id_anak }}">
                    <input type="text" style="display: none" name="id_absen[]" value="{{ $a->id_absen }}">
                </td>
                <td>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl_terima[]">
                </td>
                <td>
                    <select name="id_target[]" id="" class="select2-add pilihTarget"
                        count="{{ $no }}">
                        <option value="">Pilih Kelas</option>
                        @foreach ($target as $d)
                            <option value="{{ $d->id_kelas }}">{{ $d->pcs }}pcs ~ {{ number_format($d->rupiah,0) }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control text-end setPcs setPcs{{ $no }}"
                        count='{{ $no }}' value="0" id="pcsInput" name="pcs_awal[]">
                </td>
                <td>
                    <input type="text" class="form-control text-end setGr setGr{{ $no }}"
                        count="{{ $no }}" value="0" id="grInput" name="gr_awal[]">
                </td>
                <td>
                    <input readonly type="text"
                        class="form-control rupiahInput text-end setRupiah{{ $no }}" value="0"
                        name="ttl_rp[]">
                    <input readonly type="hidden"
                        class="form-control rupiahInput text-end rupiahBiasa{{ $no }}" value="0"
                        name="rp_target[]">
                    <input readonly type="hidden"
                        class="form-control rupiahInput text-end pcsTarget{{ $no }}" value="0"
                        name="pcs_target[]">
                </td>
                <td align="center">
                    <button type="button" class="btn rounded-pill remove_baris" id_absen={{ $a->id_absen }}
                        count="{{ $no }}"><i class="fas fa-trash text-danger"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
