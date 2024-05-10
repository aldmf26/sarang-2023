<tr class="baris{{ $count }}">
    <td>
        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl[]">
    </td>
    <td>
        <input type="text" class="form-control" name="no_box[]">
    </td>
    <td>
        <select name="id_anak[]" id="" class="select">
            <option value="">Pilih Anak</option>
            @foreach ($tb_anak as $u)
                <option value="{{ $u->id_anak }}">{{ $u->nama }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" class="form-control" name="pcs_awal[]">
    </td>
    <td>
        <input type="text" class="form-control" name="gr_awal[]">
    </td>
    <td>
        <button type="button" class="btn rounded-pill remove_baris" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>
