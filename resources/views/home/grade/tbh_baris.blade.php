<tr>
    <td>
        <select name="" id="" class="select2-add ">
            <option value="">Pilih Grade</option>
            @foreach ($tipe as $t)
            <option value="{{$t->id_tipe}}">{{$t->tipe}}</option>
            @endforeach
        </select>

        <input type="hidden" name="no_box[]">
    </td>
    <td><input type="text" class="form-control"></td>
    <td><input type="text" class="form-control"></td>
</tr>