<table class="table table-bordered">
    <thead>
        <tr>
            <th>No Box</th>
            <th>Tanggal</th>
            <th width="80px">Bulan dibayar</th>
            <th>Paket</th>
            <th>Nama</th>
            <th>Pcs Awal</th>
            <th>Gr Awal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr class="baris1">
            <td>
                {{-- <select name="no_box[]" id="" class="form-control select">

                    @foreach ($nobox as $x)
                        <option value="">Pilih box</option>
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
                <select name="bulan_dibayar[]" id="" class="form-control ">
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
                <input type="text" class="form-control input_awal" name="pcs_awal[]">
            </td>
            <td>
                <input type="text" class="form-control input_awal" name="gr_awal[]">
            </td>
            <td>
                <button type="button" class="btn rounded-pill remove_baris" count="1"><i
                        class="fas fa-trash text-danger"></i>
                </button>
            </td>
        </tr>
    </tbody>
    <tbody id="tb_baris">

    </tbody>
    <tfoot>
        <tr>
            <th colspan="9">
                <button type="button" class="btn btn-block btn-lg tbh_baris"
                    style="background-color: #F4F7F9; color: #8FA8BD; font-size: 14px; padding: 13px;">
                    <i class="fas fa-plus"></i> Tambah Baris Baru

                </button>
            </th>
        </tr>
    </tfoot>

</table>
