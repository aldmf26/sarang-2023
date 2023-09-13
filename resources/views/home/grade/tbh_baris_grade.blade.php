<tr class="baris{{$count}}">
    <td>
        <select name="no_box[]" id="" class="select2-add pilihBox" count="{{$count}}">
            <option value="">Pilih Box</option>
            @foreach ($no_box as $n)
            <option value="{{$n->no_box}}">{{$n->no_box}}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="id_anak[]" id="" class="select2-add pilihAnak" count="{{$count}}">
            <option value="">Pilih Anak</option>
            @foreach ($anak as $d)
            <option data-kelas="{{ $d->kelas }}" value="{{ $d->id_anak }}">
                ({{ $d->kelas }}) {{ ucwords($d->nama) }}</option>
            @endforeach
        </select>
        {{-- <input type="hidden" class="setHargaSatuan1"> --}}
    </td>
    <td><input type="date" class="form-control" name="tgl[]"></td>
    <td>
        <input type="text" name="pcs_awal[]" class="form-control pcs_awal">
    </td>
    <td><input type="text" name="gr_awal[]" class="form-control"></td>
    <td align="center">
        <button type="button" class="btn rounded-pill remove_baris" count="{{$count}}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>