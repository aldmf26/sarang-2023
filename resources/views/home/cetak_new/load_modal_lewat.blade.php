@if (empty($mode))
    <div class="text-center py-4">
        <h6 class="mb-3">Pilih cara memasukkan box lewat</h6>
        <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary pilih-mode-lewat" data-mode="table">
                <i class="fas fa-table"></i> Mode Tabel
            </button>
            <button type="button" class="btn btn-success pilih-mode-lewat" data-mode="json">
                <i class="fas fa-file-code"></i> Mode JSON
            </button>
            <a href="{{ route('cetaknew.export_lewat_json') }}" class="btn btn-outline-success">
                <i class="fas fa-download"></i> Export Semua JSON
            </a>
        </div>
        <small class="text-muted d-block mt-3">
            Mode tabel untuk memilih manual. Mode JSON lebih ringan untuk jumlah box besar.
        </small>
    </div>
@else
    <form id="form_lewat" action="{{ route('cetaknew.create_lewat') }}" method="post">
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

        <div class="row mb-3">
            <div class="col-lg-6">
                <label>Karyawan Default (Pilih Otomatis)</label>
                <input type="text" class="form-control" value="{{ $anak->nama ?? 'Tidak ada anak' }}" readonly>
            </div>
            <div class="col-lg-6">
                <label>Paket</label>
                <input type="text" class="form-control" value="{{ $paket->kelas ?? 'Cetak Lewat' }}" readonly>
            </div>
        </div>

        @if ($mode === 'table')
            <div class="row mb-2">
                <div class="col-lg-6">
                    <input type="text" id="pencarian" class="form-control" placeholder="Cari No Box...">
                </div>
                <div class="col-lg-6">
                    <div class="input-group input-group-sm">
                        <input type="text" id="bulkNoBoxInput" class="form-control"
                            placeholder="Bulk: 8509,2890,2093">
                        <button type="button" class="btn btn-primary" id="applyBulkBtn">Apply</button>
                        <button type="button" class="btn btn-secondary" id="clearBulkBtn">Clear</button>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mb-2">
                <strong>Total Box Ceklist:</strong> <span id="totalChecked">0</span> |
                <span id="totalPcs">0</span> Pcs | <span id="totalGr">0</span> Gr
            </div>

            <div style="overflow-y: scroll; height: 300px;">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th class="text-center">
                                <input type="checkbox" id="checkAllLewat" class="form-check-input"
                                    aria-label="Pilih semua box lewat">
                            </th>
                            <th>No Box</th>
                            <th>Pcs Awal</th>
                            <th>Gr Awal</th>
                            <th width="150px">Gr Akhir</th>
                        </tr>
                    </thead>
                    <tbody id="tbl_lewat">
                        @foreach ($box as $b)
                            <tr class="row-clickable" style="cursor: pointer;">
                                <td class="text-center">
                                    <input type="checkbox" class="row-checkbox" value="{{ $b->no_box }}">
                                </td>
                                <td>{{ $b->no_box }}</td>
                                <td class="row-pcs">{{ $b->pcs_awal_ctk }}</td>
                                <td class="row-gr">{{ $b->gr_awal_ctk }}</td>
                                <td>
                                    <input type="number" value="{{ $b->gr_awal_ctk }}" step="any"
                                        class="form-control form-control-sm input-akhir" readonly>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button type="button" class="btn btn-outline-success" id="exportJsonLewat">
                    <i class="fas fa-download"></i> Export JSON Terpilih
                </button>
                <button type="submit" class="btn btn-primary btn-simpan-lewat">Simpan Lewat</button>
            </div>
        @else
            <div class="mb-3">
                <label for="fileJsonLewat">Pilih File JSON</label>
                <input type="file" id="fileJsonLewat" class="form-control" accept="application/json,.json">
            </div>
            <div class="mb-3">
                <label for="jsonBoxInput">Atau Tempel Isi JSON</label>
                <textarea id="jsonBoxInput" class="form-control" rows="8"
                    placeholder='{"version":1,"type":"cetak_lewat","no_boxes":[8509,2890,2093]}'></textarea>
            </div>
            <div id="jsonLewatInfo" class="alert alert-secondary">
                Belum ada JSON dimasukkan.
            </div>
            <div id="jsonChecklistControls" class="d-none">
                <div class="row mb-2">
                    <div class="col-lg-8">
                        <input type="text" id="searchJsonBox" class="form-control form-control-sm"
                            placeholder="Cari No Box...">
                    </div>
                    <div class="col-lg-4 d-flex align-items-center justify-content-end">
                        <label class="mb-0">
                            <input type="checkbox" id="checkAllJsonBox" class="form-check-input" checked>
                            Pilih Semua
                        </label>
                    </div>
                </div>
                <div class="alert alert-info py-2 mb-2">
                    Dipilih: <strong id="totalJsonBoxSelected">0</strong> dari
                    <strong id="totalJsonBoxImported">0</strong> box.
                    Hilangkan centang untuk box yang tidak mau dilewatkan.
                </div>
                <div id="jsonBoxChecklist" class="border rounded p-2"
                    style="overflow-y: auto; max-height: 260px;"></div>
            </div>
            <div class="modal-footer px-0">
                <button type="submit" class="btn btn-success btn-simpan-lewat">Validasi & Simpan Lewat</button>
            </div>
        @endif
    </form>
@endif

<script>
    (function() {
        function loadModeLewat(mode) {
            $('#load_modal_lewat').html(
                '<div class="text-center py-5"><div class="spinner-border text-primary"></div><div class="mt-2">Memuat...</div></div>'
            );

            $.get("{{ route('cetaknew.load_modal_lewat') }}", { mode: mode })
                .done(function(data) {
                    $('#load_modal_lewat').html(data);
                })
                .fail(function() {
                    $('#load_modal_lewat').html(
                        '<div class="alert alert-danger">Gagal memuat form Box Lewat.</div>'
                    );
                });
        }

        $('.pilih-mode-lewat').off('click.lewat').on('click.lewat', function() {
            loadModeLewat($(this).data('mode') || '');
        });

        function uniqueNoBoxes(values) {
            var seen = {};

            return values.map(function(value) {
                return String(value).trim();
            }).filter(function(value) {
                if (!value || seen[value]) {
                    return false;
                }
                seen[value] = true;
                return true;
            });
        }

        function buildPayload(noBoxes, summary) {
            return {
                version: 1,
                type: 'cetak_lewat',
                exported_at: new Date().toISOString(),
                summary: summary || null,
                no_boxes: uniqueNoBoxes(noBoxes)
            };
        }

        function parseJsonLewat(raw) {
            var parsed = JSON.parse(raw);
            var values = Array.isArray(parsed) ? parsed : (parsed && parsed.no_boxes);

            if (!Array.isArray(values)) {
                throw new Error('JSON wajib memiliki no_boxes berupa array.');
            }
            if (!Array.isArray(parsed) && parsed.type && parsed.type !== 'cetak_lewat') {
                throw new Error('Tipe JSON bukan cetak_lewat.');
            }

            var noBoxes = uniqueNoBoxes(values);
            if (!noBoxes.length) {
                throw new Error('Daftar no box kosong.');
            }

            return buildPayload(noBoxes, !Array.isArray(parsed) ? parsed.summary : null);
        }

        function updateJsonSelection() {
            var checkboxes = $('#jsonBoxChecklist .json-box-checkbox');
            var checked = checkboxes.filter(':checked');
            var selected = checked.map(function() {
                return $(this).val();
            }).get();

            $('#totalJsonBoxSelected').text(selected.length);
            $('#checkAllJsonBox').prop('checked', checkboxes.length > 0 && selected.length === checkboxes.length);
            $('#boxPayloadLewat').val(selected.length ? JSON.stringify(buildPayload(selected, null)) : '');

            return selected.length;
        }

        function renderJsonChecklist(payload) {
            var container = document.getElementById('jsonBoxChecklist');
            if (!container) return;

            container.innerHTML = '';
            var fragment = document.createDocumentFragment();

            payload.no_boxes.forEach(function(noBox) {
                var label = document.createElement('label');
                label.className = 'json-box-item d-inline-block border rounded px-2 py-1 me-1 mb-1';
                label.dataset.noBox = String(noBox).toLowerCase();

                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'form-check-input json-box-checkbox me-1';
                checkbox.value = noBox;
                checkbox.checked = true;

                label.appendChild(checkbox);
                label.appendChild(document.createTextNode(noBox));
                fragment.appendChild(label);
            });

            container.appendChild(fragment);
            $('#totalJsonBoxImported').text(payload.no_boxes.length);
            $('#jsonChecklistControls').removeClass('d-none');
            updateJsonSelection();
        }

        function updateJsonPreview() {
            var raw = $('#jsonBoxInput').val().trim();
            if (!raw) {
                $('#boxPayloadLewat').val('');
                $('#jsonChecklistControls').addClass('d-none');
                $('#jsonBoxChecklist').empty();
                $('#jsonLewatInfo').removeClass('alert-success alert-danger').addClass('alert-secondary')
                    .text('Belum ada JSON dimasukkan.');
                return false;
            }

            try {
                var payload = parseJsonLewat(raw);
                renderJsonChecklist(payload);
                $('#jsonLewatInfo').removeClass('alert-secondary alert-danger').addClass('alert-success')
                    .html('<strong>' + payload.no_boxes.length + '</strong> no box berhasil dibaca. Periksa checklist sebelum menyimpan.');
                return true;
            } catch (error) {
                $('#boxPayloadLewat').val('');
                $('#jsonChecklistControls').addClass('d-none');
                $('#jsonBoxChecklist').empty();
                $('#jsonLewatInfo').removeClass('alert-secondary alert-success').addClass('alert-danger')
                    .text(error.message);
                return false;
            }
        }

        function updateLewatTotals() {
            var totalPcs = 0;
            var totalGr = 0;
            var checked = $('#tbl_lewat .row-checkbox:checked');
            var all = $('#tbl_lewat .row-checkbox');

            checked.each(function() {
                var row = $(this).closest('tr');
                totalPcs += parseInt(row.find('.row-pcs').text()) || 0;
                totalGr += parseFloat(row.find('.row-gr').text()) || 0;
            });

            $('#totalChecked').text(checked.length);
            $('#totalPcs').text(totalPcs);
            $('#totalGr').text(totalGr);
            $('#checkAllLewat').prop('checked', all.length > 0 && checked.length === all.length);
        }

        $('#checkAllLewat').off('change.lewat').on('change.lewat', function() {
            $('#tbl_lewat .row-checkbox').prop('checked', this.checked);
            updateLewatTotals();
        });

        $('#tbl_lewat').off('change.lewat').on('change.lewat', '.row-checkbox', updateLewatTotals);

        $('#pencarian').off('keyup.lewat').on('keyup.lewat', function() {
            var value = $(this).val().toLowerCase();
            $('#tbl_lewat tr').each(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        $('#applyBulkBtn').off('click.lewat').on('click.lewat', function() {
            var bulkInput = $('#bulkNoBoxInput').val();
            if (!bulkInput.trim()) {
                alert('Masukkan no box terlebih dahulu (pisahkan dengan koma)');
                return;
            }

            var requested = uniqueNoBoxes(bulkInput.split(','));
            var requestedMap = {};
            requested.forEach(function(noBox) { requestedMap[noBox] = true; });

            $('#tbl_lewat .row-checkbox').each(function() {
                $(this).prop('checked', !!requestedMap[String($(this).val())]);
            });
            updateLewatTotals();
        });

        $('#clearBulkBtn').off('click.lewat').on('click.lewat', function() {
            $('#bulkNoBoxInput').val('');
            $('#tbl_lewat .row-checkbox').prop('checked', false);
            updateLewatTotals();
        });

        $('#exportJsonLewat').off('click.lewat').on('click.lewat', function() {
            var noBoxes = $('#tbl_lewat .row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (!noBoxes.length) {
                alert('Pilih minimal satu box untuk diexport.');
                return;
            }

            var payload = buildPayload(noBoxes, {
                box_count: noBoxes.length,
                pcs: parseInt($('#totalPcs').text()) || 0,
                gr: parseFloat($('#totalGr').text()) || 0
            });
            var blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json' });
            var url = URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = 'cetak-lewat-' + new Date().toISOString().slice(0, 10) + '.json';
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);
        });

        $('#fileJsonLewat').off('change.lewat').on('change.lewat', function() {
            var file = this.files && this.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(event) {
                $('#jsonBoxInput').val(event.target.result);
                updateJsonPreview();
            };
            reader.onerror = function() {
                $('#jsonLewatInfo').removeClass('alert-secondary alert-success').addClass('alert-danger')
                    .text('File JSON gagal dibaca.');
            };
            reader.readAsText(file);
        });

        $('#jsonBoxInput').off('input.lewat').on('input.lewat', updateJsonPreview);

        $('#jsonBoxChecklist').off('change.lewat').on('change.lewat', '.json-box-checkbox', updateJsonSelection);

        $('#checkAllJsonBox').off('change.lewat').on('change.lewat', function() {
            $('#jsonBoxChecklist .json-box-checkbox').prop('checked', this.checked);
            updateJsonSelection();
        });

        $('#searchJsonBox').off('input.lewat').on('input.lewat', function() {
            var search = $(this).val().trim().toLowerCase();
            $('#jsonBoxChecklist .json-box-item').each(function() {
                $(this).toggle(!search || $(this).data('no-box').indexOf(search) > -1);
            });
        });

        $('#form_lewat').off('submit.lewat').on('submit.lewat', function(event) {
            var payload;

            if ($('#tbl_lewat').length) {
                var noBoxes = $('#tbl_lewat .row-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();
                if (!noBoxes.length) {
                    event.preventDefault();
                    alert('Pilih minimal satu box.');
                    return;
                }
                payload = buildPayload(noBoxes, {
                    box_count: noBoxes.length,
                    pcs: parseInt($('#totalPcs').text()) || 0,
                    gr: parseFloat($('#totalGr').text()) || 0
                });
                $('#boxPayloadLewat').val(JSON.stringify(payload));
            } else {
                if (!$('#jsonBoxChecklist .json-box-checkbox').length && !updateJsonPreview()) {
                    event.preventDefault();
                    return;
                }
                if (!updateJsonSelection()) {
                    event.preventDefault();
                    alert('Pilih minimal satu box dari JSON.');
                    return;
                }
            }

            $('.btn-simpan-lewat').prop('disabled', true).text('Memproses...');
        });
    })();
</script>
