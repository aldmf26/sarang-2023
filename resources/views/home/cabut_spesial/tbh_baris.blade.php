<tr class="baris{{$count}}">
    <td>
        <select name="no_box[]" id="" class="select2-add pilihBox" count="{{$count}}">
            <option value="">Pilih Box</option>
            <option value="h103">h103</option>
            <option value="h108">h108</option>
        </select>
    </td>
    <td>
        <input type="text" class="form-control" readonly value="{{ auth()->user()->name }}">
        <input type="hidden" class="form-control" name="id_pengawas[]" readonly value="{{ auth()->user()->id }}">
    </td>
    <td>
        <select name="id_anak[]" id="" class="select2-add pilihAnak" count="{{$count}}">
            <option value="">Pilih Anak</option>
            @foreach ($anak as $d)
            <option data-kelas="{{ $d->kelas }}" value="{{ $d->id_anak }}">
                ({{ $d->kelas }}) {{ ucwords($d->nama) }}</option>
            @endforeach
        </select>
        <input type="hidden" class="setHargaSatuan{{$count}}">
    </td>
    <td>
        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl_terima[]">
    </td>
    <td>
        <select name="id_target[]" id="" class="select2-add pilihTarget" count="{{$count}}">
            <option value="">Pilih Target</option>
            @foreach ($target as $d)
            <option value="{{ $d->id_grade_spesial }}">{{$d->ket}}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" class="form-control text-end setPcs setPcs{{$count}}" count='{{$count}}' value="0"
            id="pcsInput" name="pcs_awal[]">
    </td>
    <td>
        <input type="text" class="form-control text-end setGr setGr{{$count}}" count="{{$count}}" value="0" id="grInput"
            name="gr_awal[]">
    </td>
    <td>
        <input readonly type="text" class="form-control rupiahInput text-end setRupiah{{$count}}" value="0"
            name="ttl_rp[]">
        <input readonly type="hidden" class="form-control rupiahInput text-end rupiahBiasa{{$count}}" value="0"
            name="rp_target[]">
        <input readonly type="hidden" class="form-control rupiahInput text-end pcsTarget{{$count}}" value="0"
            name="pcs_target[]">
    </td>
    <td align="center">
        <button type="button" class="btn rounded-pill remove_baris" count="{{$count}}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>