<tr data-id="{{ $c->id_cetak }}">
    <td>{{ $no }}</td>
    <td>{{ $c->no_box }}</td>
    <td>{{ date('d M y', strtotime($c->tgl)) }}</td>
    <td>{{ $c->nm_anak }}</td>
    <td>{{ $c->kelas }} / Rp.{{ $c->rp_satuan }}</td>
    <td class="text-end">{{ $c->pcs_awal_ctk }}</td>
    <td class="text-end">{{ $c->gr_awal_ctk }}</td>
    <td class="text-end">
        <input type="text" class="form-control text-end pcs_tdk_ctk{{ $c->id_cetak }}" value="{{ $c->pcs_tdk_cetak }}"
            {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
    </td>
    <td class="text-end">
        <input type="text" class="form-control text-end gr_tdk_ctk{{ $c->id_cetak }}" value="{{ $c->gr_tdk_cetak }}"
            {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
    </td>

    <td class="text-end">
        <input type="text" class="form-control text-end pcs_akhir{{ $c->id_cetak }}" name="pcs_akhir[]"
            value="{{ $c->pcs_akhir }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>

        {{-- HIDDEN --}}
        <input type="hidden" class="form-control text-end pcs_awal{{ $c->id_cetak }}"
            value="{{ $c->pcs_awal_ctk }}">
        <input type="hidden" class="rp_satuan{{ $c->id_cetak }}" value="{{ $c->rp_satuan }}">
        <input type="hidden" class="id_paket{{ $c->id_cetak }}" value="{{ $c->id_paket }}">
        <input type="hidden" class="no{{ $c->id_cetak }}" value="{{ $no + 1 }}">
    </td>
    <td class="text-end">
        <input type="text" class="form-control text-end gr_akhir{{ $c->id_cetak }}" name="gr_akhir[]"
            value="{{ $c->gr_akhir }}" {{ $c->selesai == 'Y' ? 'readonly' : '' }}>
    </td>
    <td class="text-end">
        {{ empty($c->gr_akhir) ? 0 : number_format((1 - ($c->gr_akhir + $c->gr_tdk_cetak) / $c->gr_awal_ctk) * 100, 1) }}%
    </td>
    @php
        $susut = empty($c->gr_akhir) ? 0 : round((1 - ($c->gr_akhir + $c->gr_tdk_cetak) / $c->gr_awal_ctk) * 100, 1);

        $denda_susut = $susut >= $c->batas_susut ? $susut * $c->denda_susut : 0;
    @endphp
    <td class="text-end">{{ number_format($denda_susut, 0) }}</td>
    <td class="text-end">{{ number_format($c->pcs_akhir * $c->rp_satuan - $denda_susut + $c->rp_tambahan, 0) }}</td>
    <td align="center">
        @if (empty($c->capai))
            <button class="btn btn-sm btn-success capai" id_cetak="{{ $c->id_cetak }}" capaiVal="Y"><i
                    class="fas fa-check"></i></button>
            <button class="btn btn-sm btn-danger capai" id_cetak="{{ $c->id_cetak }}" capaiVal="T">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 381 512">
                    <path
                        d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                </svg>
            </button>
        @else
            @php
                $btn = $c->capai == 'Y' ? 'success' : 'danger';
                $val = $c->capai == 'Y' ? 'T' : 'Y';
                $ket = $c->capai == 'Y' ? 'Capai' : 'Tidak Capai';
            @endphp
            @if ($c->selesai == 'Y')
                {{ $ket }}
            @else
                <button style="font-size: 12px" class="btn btn-sm btn-{{ $btn }} capai" id_cetak="{{ $c->id_cetak }}"
                    capaiVal="{{ $val }}">
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
    <td class="text-center">

        <button style="font-size: 12px" type="button" {{ $c->selesai == 'Y' ? 'hidden' : '' }}
            class="btn btn-sm btn-warning btn_save_akhir btn_save_akhir{{ $c->id_cetak }}"
            id_cetak="{{ $c->id_cetak }}">Save</button>

        <button style="font-size: 12px" type="button" {{ $c->pcs_akhir == '0' || $c->selesai == 'Y' ? 'hidden' : '' }}
            class="btn btn-sm btn-success btn_selesai" id_cetak="{{ $c->id_cetak }}">selesai
        </button>
        <button type="button" {{ $c->selesai == 'Y' ? 'hidden' : '' }}
            class="btn btn-sm btn-danger btn_hapus btn_hapus{{ $c->id_cetak }}" id_cetak="{{ $c->id_cetak }}"><i
                class="fas fa-trash-alt"></i>
        </button>

        <button type="button" {{ $c->selesai == 'T' ? 'hidden' : '' }} class="btn btn-sm btn-warning btn_cancel"
            id_cetak="{{ $c->id_cetak }}"><i class="fas fa-redo"></i>
        </button>
    </td>
</tr>
