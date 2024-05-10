<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>No Box</th>
            <th>Nama</th>
            <th>Pcs Awal</th>
            <th>Gr Awal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr class="baris1">
            <td>
                <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="tgl[]">
            </td>
            <td>
                <input type="text" class="form-control" name="no_box[]">
            </td>
            <td>
                <select name="id_anak[]" id="" class="select">
                    <option value="">Pilih Anak</option>
                    @foreach ($tb_anak as $u)
                        <option value="{{ $u->id_anak }}">{{ $u->nama }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" class="form-control" name="pcs_awal[]">
            </td>
            <td>
                <input type="text" class="form-control" name="gr_awal[]">
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
            <th colspan="5">
                <button type="button" class="btn btn-block btn-lg tbh_baris"
                    style="background-color: #F4F7F9; color: #8FA8BD; font-size: 14px; padding: 13px;">
                    <i class="fas fa-plus"></i> Tambah Baris Baru

                </button>
            </th>
        </tr>
    </tfoot>

</table>
