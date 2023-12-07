<tr class="baris{{ $count }}">
    <td>
        <input type="text" class="form-control" readonly value="{{ auth()->user()->name }}">
        <input type="hidden" class="form-control" name="id_pengawas[]" readonly
            value="{{ auth()->user()->id }}">
    </td>
    <td>
        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl_terima[]">
        <input type="hidden" value="9999" class="form-control" name="id_cabut[]">
    </td>
    <td>
        <select name="no_box[]" id="" class="select2-tambah pilihBox" count="{{ $count + 1 }}">
            <option value="">Pilih Box</option>
            @foreach ($boxBk as $d)
                @if ($d->gr_awal - $d->gr_cabut > 1)
                    <option value="{{ $d->no_box }}">{{ ucwords($d->no_box) }}</option>
                @endif
            @endforeach
        </select>
    </td>
    <td>

        <select name="id_anak[]" id="" class="select2-tambah pilihAnak" count="{{ $count }}">
            <option value="">Pilih Anak</option>

            @foreach ($anak as $d)
                <option data-kelas="{{ $d->kelas }}" value="{{ $d->id_anak }}">
                    {{ ucwords($d->nama) }} / {{ $d->id_kelas }}</option>
            @endforeach
        </select>

        <input type="hidden" class="setHargaSatuanGr{{ $count + 1 }}">
        <input type="hidden" class="setHargaSatuanPcs{{ $count + 1 }}">
    </td>
    <td>
        <select name="hitung[]" id=""
            class="form-control pilihHitung pilihHitung{{ $count + 1 }}"
            count="{{ $count + 1 }}">
            <option value="">Pilih</option>
            <option value="1">Pcs</option>
            <option value="2">Gr</option>
            {{-- <option value="3">Spesial</option>
            <option value="4">Eo</option> --}}
        </select>
    </td>
    <td>

        <select name="id_paket[]" id=""
            class="select2-tambah pilihAnak pilihAnak{{ $count + 1 }}" count="{{ $count + 1 }}">
            <option value="">Pilih</option>
        </select>
    </td>
    <td>
        <input type="text" class="form-control text-end setPcs setPcs{{ $count + 1 }}"
            value="0" id="pcsInput" name="pcs_awal[]" count="{{ $count + 1 }}">
    </td>
    <td>
        <input type="text" class="form-control text-end setGr setGr{{ $count + 1 }}"
            count="{{ $count + 1 }}" value="0" id="grInput" name="gr_awal[]">
    </td>
    <td>
        <input readonly type="text"
            class="form-control rupiahInput text-end setRupiah{{ $count + 1 }}" value="0">
        <input readonly type="hidden"
            class="form-control rupiahInput text-end rupiahBiasa{{ $count + 1 }}" value="0"
            name="rupiah[]">
    </td>
    <td align="center">
        <button type="button" class="btn rounded-pill remove_baris" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>
