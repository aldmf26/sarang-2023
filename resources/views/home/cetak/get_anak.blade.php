<div class="row">
    <div class="col-lg-4"></div>
    <div class="col-lg-8 mb-2">
        <label for="">Pencarian :</label>
        <td><input autofocus type="text" id="pencarian" class="form-control float-end"></td>
    </div>
    <div class="col-lg-12">
        <table class="table table-bordered" id="table_cbt_spesial">
            <thead>
                <tr>
                    <th width="2" class="dhead">#</th>
                    <th class="dhead">Nama Anak</th>
                    <th width="2" class="dhead">Kelas</th>
                    <th class="text-center dhead">
                        <input type="checkbox" id="cekSemuaTutup">
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($anak as $no => $a)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ $a->nama }}</td>
                        <td>{{ $a->id_kelas }}</td>
                        <td align="center">
                            <input type="checkbox" class="form-check cek" name="id_anak[]" id=""
                                value="{{ $a->id_anak }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
