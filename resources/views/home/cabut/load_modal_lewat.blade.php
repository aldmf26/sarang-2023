<form action="{{ route('cabut.create_lewat') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            @if (!$kelas)
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Paket Lewat (Rupiah 0) tidak ditemukan. Mohon buat paket
                    lewat terlebih dahulu di Data Master Kelas.
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Paket Terdeteksi: <strong>{{ $kelas->paket }}</strong>
                    <input type="hidden" name="id_kelas" value="{{ $kelas->id_kelas }}">
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="">Tgl Terima</label>
                <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="">Bulan Dibayar</label>
                <select name="bulan" class="form-control select2-lewat" required>
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ date('m') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="">Pilih Anak (Global)</label>
                <select name="id_anak" class="form-control select2-lewat" required>
                    <option value="">Pilih Anak</option>
                    @foreach ($anak as $a)
                        <option value="{{ $a->id_anak }}">{{ $a->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-12">
            <hr>
            <div class="mb-2">
                <input type="text" id="search-box-lewat" class="form-control form-control-sm"
                    placeholder="Cari No Box...">
            </div>
            <div style="overflow-y: scroll; height: 300px;">
                <table class="table table-bordered table-sm" id="table-lewat">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="5%" class="text-center">
                                <input type="checkbox" id="check-all-lewat" class="form-check-input">
                            </th>
                            <th>No Box</th>
                            <th width="20%">Pcs Sisa</th>
                            <th width="20%">Gram Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($box as $i => $b)
                            @php
                                $sisa_pcs = $b->pcs_awal - $b->pcs_cabut;
                                $sisa_gr = $b->gr_awal - $b->gr_cabut;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="pilih[]" value="{{ $i }}"
                                        class="form-check-input check-box-lewat">
                                </td>
                                <td>
                                    {{ $b->no_box }}
                                    <input type="hidden" name="no_box[]" value="{{ $b->no_box }}">
                                </td>
                                <td>
                                    <input readonly type="number" name="pcs[]" class="form-control form-control-sm"
                                        value="{{ $sisa_pcs }}" required>
                                </td>
                                <td>
                                    <input readonly type="number" step="any" name="gr[]"
                                        class="form-control form-control-sm" value="{{ $sisa_gr }}" required>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" {{ !$kelas ? 'disabled' : '' }}>Simpan Batch</button>
    </div>
</form>

<script>
    $('#check-all-lewat').click(function () {
        $('.check-box-lewat').prop('checked', this.checked);
    });

    $('#search-box-lewat').on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $('#table-lewat tbody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
</script>