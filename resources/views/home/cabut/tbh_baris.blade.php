<tr class="baris{{$count}}">
    <td>
        <select name="no_box[]" id="" class="select3-add pilihBox" count="{{ $count }}">
            <option value="">Pilih Box</option>
            @foreach ($boxBk as $d)
            <option value="{{ $d->no_box }}">{{ ucwords($d->no_box) }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" class="form-control" readonly value="{{ auth()->user()->name }}">
        <input type="hidden" class="form-control" name="id_pengawas[]" readonly value="{{ auth()->user()->id }}">
    </td>
    <td>
        <select name="id_anak[]" id="" class="select3-add pilihAnak" count="{{ $count }}">
            <option value="">Pilih Anak</option>

            @foreach ($anak as $d)
            <option data-kelas="{{ $d->kelas }}" value="{{ $d->id_anak }}">({{$d->kelas}}) {{ ucwords($d->nama) }}
            </option>
            @endforeach
        </select>
        <input type="hidden" class="setHargaSatuan{{$count}}">

    </td>
    <td>
        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl_terima[]">
    </td>
    <td>
        <input type="text" class="form-control text-end setPcs{{$count}}" value="0" id="pcsInput" name="pcs_awal[]">
    </td>
    <td>
        <input type="text" class="form-control text-end setGr setGr{{$count}}" count="{{ $count }}" value="0"
            id="grInput" name="gr_awal[]">
    </td>
    <td>
        <input readonly type="text" class="form-control text-end rupiahInput setRupiah{{$count}}" value="0">
        <input readonly type="hidden" class="form-control rupiahInput text-end rupiahBiasa{{$count}}" value="0"
            name="rupiah[]">
    </td>
    <td align="center">
        <button type="button" class="btn rounded-pill remove_baris" count="{{$count}}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>