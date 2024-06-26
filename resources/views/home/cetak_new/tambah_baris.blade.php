<tr class="baris{{ $count }}">
    <td>
        {{-- <select name="no_box[]" id="" class="form-control select box{{ $count }} box"
            urutan="{{ $count }}">
            <option value="">Pilih box</option>
            @foreach ($nobox as $x)
                <option value="{{ $x->no_box }}">
                    {{ $x->no_box }}
                </option>
            @endforeach
        </select> --}}
        <input type="text" class="form-control input_awal" name="no_box[]">
    </td>
    <td>
        <input type="date" value="{{ date('Y-m-d') }}" class="form-control input_awal" name="tgl[]">
    </td>
    <td>
        <select name="bulan_dibayar[]" id="" class="form-control">
            @foreach ($bulan as $b)
                <option value="{{ $b->bulan }}" {{ $b->bulan == date('m') ? 'selected' : '' }}>
                    {{ $b->bulan }}</option>
            @endforeach
        </select>
    </td>
    <td>
        {{-- <input type="text" class="form-control" name="id_paket[]"> --}}
        <select name="id_paket[]" id="" class="select input_awal">
            @foreach ($paket as $u)
                <option value="{{ $u->id_kelas_cetak }}" {{ $u->id_kelas_cetak == 6 ? 'selected' : '' }}>
                    {{ $u->kelas }} /
                    Rp.{{ $u->rp_pcs }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="id_anak[]" id="" class="select input_awal">
            <option value="">Pilih Anak</option>
            @foreach ($tb_anak as $u)
                <option value="{{ $u->id_anak }}">{{ $u->nama }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" class="form-control input_awal pcs_awal{{ $count }}" name="pcs_awal[]">
    </td>
    <td>
        <input type="text" class="form-control input_awal gr_awal{{ $count }}" name="gr_awal[]">
    </td>
    <td>
        <button type="button" class="btn rounded-pill remove_baris" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>
