

<tr class="baris{{$count}}">
    <td>
        <input type="date" value="{{ date('Y-m-d') }}" name="tgl_ambil[]" class="form-control">
    </td>
   
    <td>
        <select required name="id_anak[]" class="form-control select2-add" id="">
            <option value="">- Pilih Anak -</option>
            @foreach ($anak as $d)
                <option value="{{ $d->id_anak }}">{{ strtoupper($d->nama) }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input name="gr_eo_awal[]" type="text" class="form-control text-end" value="0">
    </td>
  
    <td>
        <select required name="id_kelas[]" id="" class="form-control">
            <option value="">- Pilih Kelas -</option>
            @foreach ($kelas as $k)
                <option value="{{ $k->id_kelas }}">{{ strtoupper($k->kelas) }}</option>
            @endforeach
        </select>
    </td>
</tr>