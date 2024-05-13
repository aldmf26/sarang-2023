<tr class="baris{{ $count }}">
    <td>
        <select name="id_pemberi[]" id="" class="select">
            <option value="">Pilih Pengawas</option>
            @foreach ($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
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
        <select name="id_paket[]" id="" class="select input_awal">
            @foreach ($paket as $u)
                <option value="{{ $u->id_kelas_cetak }}">{{ $u->kelas }} / Rp.{{ $u->rp_pcs }}</option>
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
        <input type="text" class="form-control input_awal" name="pcs_awal[]">
    </td>
    <td>
        <input type="text" class="form-control input_awal" name="gr_awal[]">
    </td>
    <td>
        <button type="button" class="btn rounded-pill remove_baris" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>
