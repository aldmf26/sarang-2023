<form action="{{ route('cetaknew.create_lewat') }}" method="post">
    @csrf
    <div class="row mb-3">
        <div class="col-lg-6">
            <label for="">Karyawan Default (Pilih Otomatis)</label>
            <input type="text" class="form-control" value="{{ $anak->nama ?? 'Tidak ada anak' }}" readonly>
            <input type="hidden" name="id_anak_default" value="{{ $anak->id_anak ?? 0 }}">
        </div>
        <div class="col-lg-6">
            <label for="">Paket</label>
            <input type="text" class="form-control" value="{{ $paket->kelas ?? 'Cetak Lewat' }}" readonly>
            <input type="hidden" name="id_kelas_default" value="{{ $paket->id_kelas_cetak ?? 0 }}">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-lg-12">
            <input type="text" id="pencarian" class="form-control" placeholder="Cari No Box...">
        </div>
    </div>
    <div style="overflow-y: scroll; height: 300px;">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>No Box</th>
                    <th>Pcs Awal</th>
                    <th>Gr Awal</th>
                    <th width="150px">Gr Akhir</th>
                </tr>
            </thead>
            <tbody id="tbl_lewat">
                @foreach ($box as $index => $b)
                    <tr>
                        <td>
                            <input type="hidden" name="no_box[{{ $index }}]" value="{{ $b->no_box }}">
                            {{ $b->no_box }}
                        </td>
                        <td>{{ $b->pcs_awal_ctk }}</td>
                        <td>{{ $b->gr_awal_ctk }}</td>
                        <td>
                            <input type="number" step="any" name="gr_akhir[{{ $index }}]"
                                class="form-control form-control-sm">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan Lewat</button>
    </div>
</form>

<script>
    $('#pencarian').keyup(function () {
        var value = $(this).val().toLowerCase();
        $("#tbl_lewat tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
</script>