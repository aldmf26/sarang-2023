@if (empty($mode))
    <div class="text-center py-4">
        <h6 class="mb-3">Pilih cara memasukkan box lewat</h6>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <button type="button" class="btn btn-primary pilih-mode-lewat" data-mode="table">
                <i class="fas fa-table"></i> Mode Tabel
            </button>
            <button type="button" class="btn btn-success pilih-mode-lewat" data-mode="json">
                <i class="fas fa-file-code"></i> Mode JSON
            </button>
            <a href="{{ route('sortir.export_lewat_json') }}" class="btn btn-outline-success">
                <i class="fas fa-download"></i> Export Semua JSON
            </a>
        </div>
        <small class="text-muted d-block mt-3">Mode JSON lebih ringan untuk ribuan box.</small>
    </div>
@else
    <form id="form_lewat" action="{{ route('sortir.create_lewat') }}" method="post">
        @csrf
        <input type="hidden" name="box_payload" id="boxPayloadLewat">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-sm btn-outline-secondary pilih-mode-lewat" data-mode="">
                <i class="fas fa-arrow-left"></i> Pilih Mode
            </button>
            <span class="badge bg-{{ $mode === 'json' ? 'success' : 'primary' }}">
                Mode {{ $mode === 'json' ? 'JSON' : 'Tabel' }}
            </span>
        </div>

        @if ($mode === 'table')
            <div class="row mb-2">
                <div class="col-lg-6">
                    <input type="text" id="searchLewat" class="form-control" value="{{ $searchNoBox }}"
                        placeholder="Cari No Box..." autocomplete="off">
                </div>
                <div class="col-lg-6">
                    <div class="input-group">
                        <input type="text" id="bulkLewat" class="form-control" value="{{ $bulkNoBox }}"
                            placeholder="Daftar: 8509,2890,2093">
                        <button type="button" class="btn btn-primary" id="applySearchLewat">Tampilkan</button>
                    </div>
                </div>
            </div>

            <div class="alert alert-info py-2">
                <strong>Total Box Ceklist:</strong> <span id="totalChecked">0</span> |
                <span id="totalPcs">0</span> Pcs | <span id="totalGr">0</span> Gr
            </div>

            @if ($box->isEmpty())
                <div class="alert alert-secondary text-center">Cari atau masukkan daftar No Box untuk menampilkan data.</div>
            @else
                <div style="overflow-y:auto; max-height:300px">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAllLewat" class="form-check-input"></th>
                                <th>No Box</th><th>Pcs Awal</th><th>Gr Awal</th><th>Gr Akhir</th>
                            </tr>
                        </thead>
                        <tbody id="tbl_lewat">
                            @foreach ($box as $b)
                                <tr>
                                    <td><input type="checkbox" class="form-check-input row-checkbox" value="{{ $b->no_box }}"></td>
                                    <td>{{ $b->no_box }}</td>
                                    <td class="row-pcs">{{ $b->pcs_awal }}</td>
                                    <td class="row-gr">{{ $b->gr_awal }}</td>
                                    <td><input class="form-control form-control-sm" value="{{ $b->gr_awal }}" readonly></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-outline-success" id="exportJsonLewat">Export JSON Terpilih</button>
                    <button type="submit" class="btn btn-primary btn-simpan-lewat">Simpan Lewat</button>
                </div>
            @endif
        @else
            <div class="mb-3">
                <label for="fileJsonLewat">Pilih File JSON</label>
                <input type="file" id="fileJsonLewat" class="form-control" accept="application/json,.json">
            </div>
            <div class="mb-3">
                <label for="jsonBoxInput">Atau Tempel Isi JSON</label>
                <textarea id="jsonBoxInput" class="form-control" rows="7"
                    placeholder='{"version":1,"type":"sortir_lewat","no_boxes":[8509,2890,2093]}'></textarea>
            </div>
            <div id="jsonLewatInfo" class="alert alert-secondary">Belum ada JSON dimasukkan.</div>
            <div id="jsonChecklistControls" class="d-none">
                <div class="row mb-2">
                    <div class="col-lg-8"><input id="searchJsonBox" class="form-control form-control-sm" placeholder="Cari No Box..."></div>
                    <div class="col-lg-4 text-end">
                        <label><input type="checkbox" id="checkAllJsonBox" class="form-check-input" checked> Pilih Semua</label>
                    </div>
                </div>
                <div class="alert alert-info py-2">
                    Dipilih: <strong id="totalJsonBoxSelected">0</strong> dari
                    <strong id="totalJsonBoxImported">0</strong> box. Hilangkan centang untuk box yang tidak mau dilewatkan.
                </div>
                <div id="jsonBoxChecklist" class="border rounded p-2" style="overflow-y:auto;max-height:260px"></div>
            </div>
            <div class="modal-footer px-0">
                <button type="submit" class="btn btn-success btn-simpan-lewat">Validasi & Simpan Lewat</button>
            </div>
        @endif
    </form>
@endif

<script>
    (function() {
        function loadMode(mode, filters) {
            $('#load_modal_lewat').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
            $.get("{{ route('sortir.load_modal_lewat') }}", $.extend({mode: mode}, filters || {}))
                .done(function(html) { $('#load_modal_lewat').html(html); })
                .fail(function() { $('#load_modal_lewat').html('<div class="alert alert-danger">Gagal memuat form.</div>'); });
        }

        function unique(values) {
            var seen = {};
            return values.map(function(value) { return String(value).trim(); }).filter(function(value) {
                if (!value || seen[value]) return false;
                seen[value] = true;
                return true;
            });
        }

        function payload(noBoxes, summary) {
            return {version: 1, type: 'sortir_lewat', exported_at: new Date().toISOString(),
                summary: summary || null, no_boxes: unique(noBoxes)};
        }

        $('.pilih-mode-lewat').off('click.sortirLewat').on('click.sortirLewat', function() {
            loadMode($(this).data('mode') || '');
        });

        $('#applySearchLewat').off('click.sortirLewat').on('click.sortirLewat', function() {
            loadMode('table', {search_no_box: $('#searchLewat').val().trim(), bulk_no_box: $('#bulkLewat').val().trim()});
        });
        $('#searchLewat, #bulkLewat').off('keydown.sortirLewat').on('keydown.sortirLewat', function(event) {
            if (event.key === 'Enter') { event.preventDefault(); $('#applySearchLewat').click(); }
        });

        function totals() {
            var pcs = 0, gr = 0;
            var checked = $('#tbl_lewat .row-checkbox:checked');
            checked.each(function() {
                var row = $(this).closest('tr');
                pcs += parseFloat(row.find('.row-pcs').text()) || 0;
                gr += parseFloat(row.find('.row-gr').text()) || 0;
            });
            $('#totalChecked').text(checked.length); $('#totalPcs').text(pcs); $('#totalGr').text(gr);
            $('#checkAllLewat').prop('checked', checked.length > 0 && checked.length === $('#tbl_lewat .row-checkbox').length);
        }
        $('#checkAllLewat').off('change.sortirLewat').on('change.sortirLewat', function() {
            $('#tbl_lewat .row-checkbox').prop('checked', this.checked); totals();
        });
        $('#tbl_lewat').off('change.sortirLewat').on('change.sortirLewat', '.row-checkbox', totals);

        $('#exportJsonLewat').off('click.sortirLewat').on('click.sortirLewat', function() {
            var boxes = $('#tbl_lewat .row-checkbox:checked').map(function() { return this.value; }).get();
            if (!boxes.length) return alert('Pilih minimal satu box.');
            var blob = new Blob([JSON.stringify(payload(boxes, {box_count: boxes.length}), null, 2)], {type:'application/json'});
            var url = URL.createObjectURL(blob), link = document.createElement('a');
            link.href = url; link.download = 'sortir-lewat-' + new Date().toISOString().slice(0, 10) + '.json';
            document.body.appendChild(link); link.click(); link.remove(); URL.revokeObjectURL(url);
        });

        function updateJsonSelection() {
            var all = $('#jsonBoxChecklist .json-box-checkbox');
            var selected = all.filter(':checked').map(function() { return this.value; }).get();
            $('#totalJsonBoxSelected').text(selected.length);
            $('#checkAllJsonBox').prop('checked', all.length > 0 && selected.length === all.length);
            $('#boxPayloadLewat').val(selected.length ? JSON.stringify(payload(selected)) : '');
            return selected.length;
        }

        function readJson(raw) {
            var parsed = JSON.parse(raw), values = Array.isArray(parsed) ? parsed : parsed.no_boxes;
            if (!Array.isArray(values)) throw new Error('JSON wajib memiliki no_boxes berupa array.');
            if (!Array.isArray(parsed) && parsed.type && parsed.type !== 'sortir_lewat') throw new Error('Tipe JSON bukan sortir_lewat.');
            values = unique(values); if (!values.length) throw new Error('Daftar no box kosong.');
            return values;
        }

        function previewJson() {
            try {
                var boxes = readJson($('#jsonBoxInput').val().trim()), fragment = document.createDocumentFragment();
                boxes.forEach(function(noBox) {
                    var label = document.createElement('label');
                    label.className = 'json-box-item d-inline-block border rounded px-2 py-1 me-1 mb-1';
                    label.dataset.noBox = noBox.toLowerCase();
                    var input = document.createElement('input'); input.type = 'checkbox';
                    input.className = 'form-check-input json-box-checkbox me-1'; input.value = noBox; input.checked = true;
                    label.appendChild(input); label.appendChild(document.createTextNode(noBox)); fragment.appendChild(label);
                });
                $('#jsonBoxChecklist').empty().get(0).appendChild(fragment);
                $('#totalJsonBoxImported').text(boxes.length); $('#jsonChecklistControls').removeClass('d-none');
                $('#jsonLewatInfo').attr('class', 'alert alert-success').text(boxes.length + ' no box berhasil dibaca.');
                updateJsonSelection(); return true;
            } catch (error) {
                $('#boxPayloadLewat').val(''); $('#jsonChecklistControls').addClass('d-none');
                $('#jsonLewatInfo').attr('class', 'alert alert-danger').text(error.message); return false;
            }
        }
        $('#fileJsonLewat').off('change.sortirLewat').on('change.sortirLewat', function() {
            var file = this.files && this.files[0]; if (!file) return;
            var reader = new FileReader(); reader.onload = function(event) { $('#jsonBoxInput').val(event.target.result); previewJson(); };
            reader.readAsText(file);
        });
        $('#jsonBoxInput').off('input.sortirLewat').on('input.sortirLewat', previewJson);
        $('#jsonBoxChecklist').off('change.sortirLewat').on('change.sortirLewat', '.json-box-checkbox', updateJsonSelection);
        $('#checkAllJsonBox').off('change.sortirLewat').on('change.sortirLewat', function() {
            $('#jsonBoxChecklist .json-box-checkbox').prop('checked', this.checked); updateJsonSelection();
        });
        $('#searchJsonBox').off('input.sortirLewat').on('input.sortirLewat', function() {
            var value = this.value.trim().toLowerCase();
            $('#jsonBoxChecklist .json-box-item').each(function() { $(this).toggle(!value || $(this).data('no-box').indexOf(value) > -1); });
        });

        $('#form_lewat').off('submit.sortirLewat').on('submit.sortirLewat', function(event) {
            if ($('#tbl_lewat').length) {
                var boxes = $('#tbl_lewat .row-checkbox:checked').map(function() { return this.value; }).get();
                if (!boxes.length) { event.preventDefault(); return alert('Pilih minimal satu box.'); }
                $('#boxPayloadLewat').val(JSON.stringify(payload(boxes)));
            } else if (!updateJsonSelection()) {
                event.preventDefault(); return alert('Pilih minimal satu box dari JSON.');
            }
            $('.btn-simpan-lewat').prop('disabled', true).text('Memproses...');
        });
    })();
</script>
