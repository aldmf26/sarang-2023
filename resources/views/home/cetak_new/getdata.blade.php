<table class="table table-bordered" id="tableHalaman">
    <thead>
        <tr>
            <th class="dhead">#</th>
            <th class="dhead">Tanggal</th>
            <th class="dhead">No Box</th>
            <th class="dhead kolom_select">Nama Karyawan</th>
            <th class="dhead">Pcs/Gr</th>
            <th class="dhead kolom_select">Paket</th>
            <th width="70px" class="dhead text-end">Pcs Awal</th>
            <th width="70px" class="dhead text-end">Gr Awal</th>
            <th width="70px" class="dhead text-end">Pcs Tdk Ctk</th>
            <th width="70px" class="dhead text-end">Gr Tdk Ctk</th>
            <th width="70px" class="dhead text-end">Pcs Akhir</th>
            <th width="70px" class="dhead text-end">Gr Akhir</th>
            <th width="70px" class="dhead text-end">Pcs Hcr</th>
            <th class="dhead text-end">Susut%</th>
            {{-- <th class="dhead text-end">Denda sst</th> --}}
            <th class="dhead text-end">Total Rp</th>
            <th class="dhead text-end">Bulan Bayar</th>
            <th class="dhead text-center">Capai</th>
            <th class="dhead text-center" width="150px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cetak as $no => $c)
            <tr data-id="{{ $c->id_cetak }}">
                <td>{{ $no + 1 }}</td>
                <td>
                    <input type="date" class="form-control tgl{{ $c->id_cetak }}" value="{{ $c->tgl }}">
                    {{-- {{ date('d M y', strtotime($c->tgl)) }} --}}
                </td>
                <td>{{ $c->no_box }}</td>
                <td>
                    <select name="" id="" class="form-control id_anak{{ $c->id_cetak }}"
                        {{ $c->selesai == 'Y' ? 'disabled' : '' }}>
                        <option value="">Pilih anak</option>
                        @foreach ($tb_anak as $t)
                            <option value="{{ $t->id_anak }}" {{ $t->id_anak == $c->id_anak ? 'selected' : '' }}>
                                {{ $t->nama }}</option>
                        @endforeach
                    </select>
                    {{-- {{ $c->nm_anak }} --}}
                </td>
                <td>
                    <select name="" id="" class="form-control tipe_bayar tipe_bayar{{ $c->id_cetak }}"
                        id_cetak="{{ $c->id_cetak }}" {{ $c->selesai == 'Y' ? 'disabled' : '' }}>
                        <option value="">Pilih</option>
                        <option value="1" {{ $c->tipe_bayar == 1 ? 'selected' : '' }}>pcs</option>
                        <option value="2" {{ $c->tipe_bayar == 2 ? 'selected' : '' }}>gr</option>
                    </select>
                </td>
                <td>
                    <select name="id_paket[]" id="" class="form-control id_paket{{ $c->id_cetak }}"
                        {{ $c->selesai == 'Y' ? 'disabled' : '' }}>
                        @if ($c->id_kelas_cetak == 0)
                            <option value="">Pilih Paket</option>
                        @else
                            @foreach ($paket as $u)
                                <option value="{{ $u->id_kelas_cetak }}"
                                    {{ $u->id_kelas_cetak == $c->id_kelas_cetak ? 'selected' : '' }}>
                                    {{ $u->kelas }} / Rp.{{ $u->rp_pcs }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                    {{-- {{ $c->kelas }} / Rp.{{ $c->rp_satuan }} --}}
                </td>
                <td class="text-end">
                    <input type="text" class="form-control text-end pcs_awal{{ $c->id_cetak }}"
                        value="{{ $c->pcs_awal_ctk }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
                </td>
                <td class="text-end">
                    <input type="text" class="form-control text-end gr_awal{{ $c->id_cetak }}"
                        value="{{ $c->gr_awal_ctk }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
                </td>

                <td class="text-end">
                    <input type="text" class="form-control text-end pcs_tdk_ctk{{ $c->id_cetak }}"
                        value="{{ $c->pcs_tdk_cetak }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
                </td>
                <td class="text-end">
                    <input type="text" class="form-control text-end gr_tdk_ctk{{ $c->id_cetak }}"
                        value="{{ $c->gr_tdk_cetak }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
                </td>

                <td class="text-end">
                    <input type="text" class="form-control text-end pcs_akhir{{ $c->id_cetak }}" name="pcs_akhir[]"
                        value="{{ $c->pcs_akhir }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>

                    {{-- hidden --}}

                    <input type="hidden" class="rp_satuan{{ $c->id_cetak }}" value="{{ $c->rp_satuan }}">
                    {{-- <input type="hidden" class="id_paket{{ $c->id_cetak }}" value="{{ $c->id_paket }}"> --}}
                    <input type="hidden" class="no{{ $c->id_cetak }}" value="{{ $no + 1 }}">
                    {{-- <input type="hidden" class="hal{{ $c->id_cetak }}" value="{{ $no + 1 }}"> --}}
                </td>
                <td class="text-end">
                    <input type="text" class="form-control text-end gr_akhir{{ $c->id_cetak }}" name="gr_akhir[]"
                        value="{{ $c->gr_akhir }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
                </td>
                <td class="text-end">
                    <input type="text" class="form-control text-end pcs_hcr{{ $c->id_cetak }}" name="pcs_akhir[]"
                        value="{{ $c->pcs_hcr }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
                </td>
                <td class="text-end">
                    {{ empty($c->gr_akhir) || empty($c->gr_awal_ctk) ? 0 : number_format((1 - ($c->gr_akhir + $c->gr_tdk_cetak) / $c->gr_awal_ctk) * 100, 1) }}%
                </td>
                @php
                    $susut =
                        empty($c->gr_akhir) || empty($c->gr_awal_ctk)
                            ? 0
                            : round((1 - ($c->gr_akhir + $c->gr_tdk_cetak) / $c->gr_awal_ctk) * 100, 1);

                    $denda_susut = $susut >= $c->batas_susut ? $susut * $c->denda_susut : 0;
                @endphp
                {{-- <td class="text-end">{{ number_format($denda_susut, 0) }}</td> --}}
                <td class="text-end">
                    {{ number_format($c->ttl_rp, 0) }}</td>
                <td>
                    <select id="" class="form-control bulan_dibayar{{ $c->id_cetak }}"
                        {{ $c->selesai == 'Y' ? 'disabled' : '' }}>
                        <option value="">Pilih</option>
                        @foreach (getListBulan() as $b)
                            <option value="{{ $b->id_bulan }}"
                                {{ $c->bulan_dibayar == $b->id_bulan ? 'selected' : '' }}>
                                {{ $b->nm_bulan }}</option>
                        @endforeach
                    </select>
                </td>
                <td align="center">
                    @if (empty($c->capai))
                        <button class="btn btn-sm btn-success capai no{{ $c->id_cetak }}"
                            id_cetak="{{ $c->id_cetak }}" capaiVal="Y"><i class="fas fa-check"></i></button>
                        <button class="btn btn-sm btn-danger capai no{{ $c->id_cetak }}"
                            id_cetak="{{ $c->id_cetak }}" capaiVal="T">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 381 512">
                                <path
                                    d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                            </svg>
                        </button>
                    @else
                        @php
                            [$btn, $val, $ket] =
                                $c->capai == 'Y' ? ['success', 'T', 'Capai'] : ['danger', 'Y', 'Tidak Capai'];
                        @endphp
                        @if ($c->selesai == 'Y')
                            {{ $ket }}
                        @else
                            <button style="font-size: 12px" class="btn btn-sm btn-{{ $btn }} capai"
                                id_cetak="{{ $c->id_cetak }}" capaiVal="{{ $val }}">
                                @if ($c->capai == 'Y')
                                    <i class="fas fa-check"></i>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 381 512">
                                        <path
                                            d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                                    </svg>
                                @endif
                                {{ $ket }}
                            </button>
                        @endif
                    @endif
                </td>
                <td class="text-center" style="white-space: nowrap">
                    <button style="font-size: 12px" type="button" {{ $c->selesai == 'Y' ? 'hidden' : '' }}
                        class="btn btn-sm btn-primary btn_save_akhir btn_save_akhir{{ $c->id_cetak }}"
                        id_cetak="{{ $c->id_cetak }}">Save
                    </button>

                    <button style="font-size: 12px" type="button"
                        {{ $c->selesai == 'Y' || $c->bulan_dibayar == 0 || empty($c->capai) || $c->id_kelas_cetak == 0 ? 'hidden' : '' }}
                        class="btn btn-sm btn-success btn_selesai" id_cetak="{{ $c->id_cetak }}">selesai
                    </button>
                    @if ($hal == 'cu')
                        <button type="button" {{ $c->selesai == 'Y' ? 'hidden' : '' }}
                            class="btn btn-sm btn-danger btn_hapus btn_hapus{{ $c->id_cetak }}"
                            id_cetak="{{ $c->id_cetak }}" id_paket={{ $c->id_paket }}><i
                                class="fas fa-trash-alt"></i>
                        </button>
                    @else
                    @endif


                    <button {{ $c->kat_kelas == 'CU' ? '' : (empty($c->form) ? '' : 'hidden') }} type="button"
                        {{ $c->selesai == 'T' ? 'hidden' : '' }} class="btn btn-sm btn-danger btn_cancel"
                        id_cetak="{{ $c->id_cetak }}" form="{{ empty($c->form) ? 'tdk' : 'ada' }}"><i
                            class="fas fa-redo"></i>
                    </button>

                </td>
            </tr>
        @endforeach

    </tbody>
</table>
