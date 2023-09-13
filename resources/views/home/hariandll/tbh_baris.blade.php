

<tr class="baris{{$count}}">
    <td>
        <select required name="id_anak[]" class="form-control select2-add" id="">
            <option value="">- Pilih Anak -</option>
            @foreach ($anak as $d)
                <option value="{{ $d->id_anak }}">{{ strtoupper($d->nama) }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" class="form-control" name="ket[]">
    </td>
    <td>
        <select name="lokasi[]" id="" class="form-control select2-add">
            <option value="">- Pilih Lokasi -</option>
            @php
                $lokasi = ['resto', 'aga', 'orchad', 'agrilaras'];
            @endphp
            @foreach ($lokasi as $d)
                <option value="{{ strtoupper($d) }}">{{ strtoupper($d) }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" min="0" class="form-control" name="rupiah[]">
    </td>
    <td align="center">
        <button type="button" class="btn rounded-pill remove_baris" count="{{$count}}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>