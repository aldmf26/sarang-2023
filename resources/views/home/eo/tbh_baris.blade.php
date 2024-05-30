
<tr class="baris{{ $count }}">
    <td>
        <select name="no_box[]" class="select2-tambah pilihBox" count="{{ $count }}">
            <option value="">- Pilih No Box -</option>
            @foreach ($nobox as $d)
                @if ($d->gr_awal - $d->gr_cabut > 1)
                    <option value="{{ $d->no_box }}">{{ $d->no_box }}</option>
                @endif
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" readonly value="{{ auth()->user()->name }}" name="example" class="form-control">
        <input type="hidden" name="id_pengawas[]" readonly value="{{ auth()->user()->id }}">
        <input type="hidden" class="form-control" name="id_eo[]" readonly value="9999">
    </td>
    <td class="h6">
        <select name="id_anak[]" id="" class="select2-tambah pilihAnak" count="{{ $count }}">
            <option value="">Pilih Anak</option>

            @foreach ($anak as $d)
                <option value="{{ $d->id_anak }}">
                    {{ ucwords($d->nama) }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="date" style="font-size: 12px" value="{{ date('Y-m-d') }}" name="tgl_ambil[]"
            class="form-control">
    </td>
    <td>
        <select required name="id_kelas[]" id="" class="select2-tambah">
            <option value="">- Kelas -</option>
            @foreach ($kelas as $k)
                <option value="{{ $k->id_kelas }}">{{ strtoupper($k->kelas) }} -
                    {{ number_format($k->rupiah, 0) }}</option>
            @endforeach
        </select>
    </td>
    <td class="h6 text-end ttlGr{{ $count }}">0</td>
    <td>
        <input name="gr_eo_awal[]" type="text" class="form-control text-end" required>
    </td>
    <td align="center">
        <button type="button" class="btn rounded-pill remove_baris" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>
