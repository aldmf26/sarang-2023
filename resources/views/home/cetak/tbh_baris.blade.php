<tr class="baris{{$count}}">
    <td>
        <select name="no_box[]" id="" class="select2-add pilihBox" count="1">
            <option value="">Pilih Box</option>
            @foreach ($cabut as $c)
            <option value="{{$c->no_box}}">{{$c->no_box}}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" class="form-control" name="grade[]">
    </td>
    <td>
        <select name="id_anak[]" id="" class="select2-add pilihAnak" count="1">
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
        <select name="target[]" id="" class="select2-add pilihTarget" count="1">
            <option value="">Pilih target</option>
            <option value="1">TARGET</option>
            <option value="2">CU</option>
            <option value="3">LN</option>
        </select>
    </td>
    <td>
        <input type="hidden" name="rp_pcs[]" class="form-control rp_target">
        <input type="text" name="pcs_awal[]" class="form-control pcs_awal">
    </td>
    <td><input type="text" name="gr_awal[]" class="form-control"></td>
    <td><input type="text" class="form-control total_rp text-end" readonly></td>
    <td align="center">
        <button type="button" class="btn rounded-pill remove_baris" count="{{$count}}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>

</tr>