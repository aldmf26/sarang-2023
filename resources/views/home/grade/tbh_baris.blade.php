<tr>
    <td>
        <select name="grade[]" id="" class="select2-add ">
            <option value="">Pilih Grade</option>
            @foreach ($tipe as $t)
            <option value="{{$t->id_tipe}}">{{$t->tipe}}</option>
            @endforeach
        </select>
    </td>
    <td><input type="text" class="form-control pcs" name="pcs[]" value="0"></td>
    <td><input type="text" class="form-control gr" name="gr[]" value="0"></td>
</tr>