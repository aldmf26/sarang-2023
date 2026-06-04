<form id="form_lewat" action="{{ route('cetaknew.create_lewat') }}" method="post">
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
        <div class="col-lg-6">
            <input type="text" id="pencarian" class="form-control" placeholder="Cari No Box...">
        </div>
        <div class="col-lg-6">
            <div class="input-group input-group-sm">
                <input type="text" id="bulkNoBoxInput" class="form-control" placeholder="Bulk: 8509,2890,2093">
                <button type="button" class="btn btn-primary" id="applyBulkBtn">Apply</button>
                <button type="button" class="btn btn-secondary" id="clearBulkBtn">Clear</button>
            </div>
        </div>
    </div>
    <div style="overflow-y: scroll; height: 300px;">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No Box</th>
                    <th>Pcs Awal</th>
                    <th>Gr Awal</th>
                    <th width="150px">Gr Akhir</th>
                </tr>
            </thead>
            <tbody id="tbl_lewat">
                @foreach ($box as $b)
                    {{-- Hilangkan $index karena kita pakai $b->no_box sebagai key --}}
                    <tr class="row-clickable" style="cursor: pointer;">
                        <td>
                            <input type="checkbox" name="no_box[]" class="row-checkbox" value="{{ $b->no_box }}">
                        </td>
                        <td>
                            {{ $b->no_box }}
                        </td>
                        <td>{{ $b->pcs_awal_ctk }}</td>
                        <td>{{ $b->gr_awal_ctk }}</td>
                        <td>
                            <input type="number" value="{{ $b->gr_awal_ctk }}" step="any"
                                name="gr_akhir[{{ $b->no_box }}]" class="form-control form-control-sm input-akhir">
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
    // Script pencarian bawaan Anda
    $('#pencarian').keyup(function() {
        var value = $(this).val().toLowerCase();
        $("#tbl_lewat tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Bulk select functionality
    $('#applyBulkBtn').click(function() {
        var bulkInput = $('#bulkNoBoxInput').val();
        if (!bulkInput.trim()) {
            alert('Masukkan no box terlebih dahulu (pisahkan dengan koma)');
            return;
        }

        // Parse comma-separated values
        var noBoxList = bulkInput.split(',').map(function(val) {
            return val.trim();
        }).filter(function(val) {
            return val.length > 0;
        });

        // Uncheck all first
        $('#tbl_lewat .row-checkbox').prop('checked', false);

        // Check only the ones in the list
        noBoxList.forEach(function(noBox) {
            $('#tbl_lewat .row-checkbox').each(function() {
                if ($(this).val() === noBox) {
                    $(this).prop('checked', true);
                }
            });
        });
    });

    // Clear bulk input and uncheck all
    $('#clearBulkBtn').click(function() {
        $('#bulkNoBoxInput').val('');
        $('#tbl_lewat .row-checkbox').prop('checked', false);
    });

    // SOLUSI UTAMA: Jinakkan form agar hanya mengirim checkbox yang dicentang
    $('#form_lewat').on('submit', function() {
        // Cari semua checkbox .row-checkbox yang TIDAK dicentang
        $('#tbl_lewat .row-checkbox').each(function() {
            if (!$(this).is(':checked')) {
                // Nonaktifkan checkbox yang tidak dicentang agar TIDAK terkirim ke Laravel
                $(this).prop('disabled', true);

                // Nonaktifkan juga input gr_akhir pasangannya agar tidak jadi sampah di request
                $(this).closest('tr').find('.input-akhir').prop('disabled', true);
            }
        });

        return true; // Lanjutkan submit form ke controller
    });
</script>
