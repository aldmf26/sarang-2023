<tr class="baris{{$count}}">
    <td>
        <select name="id_anak[]" class="select3-add" id="">
            <option value="">Pilih Anak</option>
            @foreach ($anak as $s)
                <option value="{{ $s->id_anak }}">{{ strtoupper($s->nama) }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="date" value="{{ date('Y-m-d') }}" name="tgl[]" class="form-control">
    </td>
    <td align="center">
        <button type="button" class="btn rounded-pill remove_baris" count="{{$count}}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>

</tr>