<tr class="baris{{ $count }}">
    <input type="hidden" name="id_sortir[]" value="9999">
    <td>
        <select name="id_anak[]" id="" class="select2-tambah pilihAnak" count="{{ $count }}">
            <option value="">Pilih Anak</option>

            @foreach ($anak as $d)
                <option data-kelas="{{ $d->kelas }}" value="{{ $d->id_anak }}">
                    {{ ucwords($d->nama) }}</option>
            @endforeach
        </select>
        <input type="hidden" class="setHargaSatuan{{ $count }}">

    </td>
    @php
        $kelas = DB::table('tb_kelas_sortir')->orderBy('id_kelas', 'ASC')->get();
    @endphp
    <td>
        <select name="tipe[]" count="{{ $count }}" id="" class="form-control selectTipe">
            <option value="">Pilih Paket</option>
            @foreach ($kelas as $i => $d)
                <option value="{{ $d->id_kelas }}">{{ strtoupper($d->kelas) }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="no_box[]" id="" required class="select2-tambah pilihBox" count="{{ $count }}">
            <option value="">Pilih Box</option>
            @foreach ($boxBk as $d)
                @if ($d->gr_awal - $d->gr_cabut > 1)
                    <option value="{{ $d->no_box }}">{{ ucwords($d->no_box) }}
                    </option>
                @endif
            @endforeach
        </select>
    </td>
    <td>
        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl_terima[]">
    </td>
    <td>
        <input type="text" class="form-control text-end setPcs{{ $count }}" value="" id="pcsInput"
            name="pcs_awal[]">
    </td>
    <td>
        <input type="text" class="form-control text-end" value="0" id="pcuc" name="pcuc[]">
    </td>
    <td>
        <input type="text" class="form-control text-end setGr setGr{{ $count }}" count="{{ $count }}"
            value="" id="grInput" name="gr_awal[]">
    </td>
    {{-- <td>
        <input readonly type="text" class="form-control text-end rupiahInput setRupiah{{ $count }}"
            value="0" name="rupiah[]">
    </td> --}}
    <td align="center">
        <button type="button" class="btn rounded-pill remove_baris" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>
