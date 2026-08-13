<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
    </x-slot>

    <x-slot name="cardBody">
        <section class="row" x-data="{
            bulkBoxChecklist: false,
            bulkNoBoxInput: '',
            cek: [],
            boxData: {
                @foreach ($formulir as $d)
                    '{{ $d->no_box }}': { pcs: {{ $d->pcs_awal }}, gr: {{ $d->gr_awal }} }, @endforeach
            },
            totalPcs: 0,
            totalGr: 0,
            applyBulkSelect() {
                const values = this.bulkNoBoxInput
                    .split(',')
                    .map(value => value.trim())
                    .filter(value => value.length);
                this.cek = Array.from(new Set(values));
                this.updateTotals();
            },
            updateTotals() {
                this.totalPcs = 0;
                this.totalGr = 0;
                this.cek.forEach(box => {
                    if (this.boxData[box]) {
                        this.totalPcs += this.boxData[box].pcs;
                        this.totalGr += this.boxData[box].gr;
                    }
                });
            }
        }" @change="updateTotals()">
            <div class="col-lg-12">
                <x-theme.alert pesan="{{ session()->get('error') }}" />
            </div>
            <div class="col-lg-12 mt-2">
                <div class="row">
                    <div class="col-lg-4">
                        <input type="text" id="tbl1input" class="form-control form-control-sm mb-2"
                            placeholder="cari">
                    </div>

                    <div class="col-lg-8">
                        <form action="{{ route('gradingbj.grading_partai') }}" method="post">
                            @csrf
                            {{-- <a data-bs-toggle="modal" data-bs-target="#import" class="btn btn-sm btn-primary"
                                href="">Import</a> --}}
                            {{--
                            <a href="#" data-bs-target="#selisih" data-bs-toggle="modal"
                                class="selisih btn btn-sm btn-primary" href=""><i class="fa fa-warehouse"></i>
                                Data Selisih</a>
                            <button type="submit" name="submit" value="export" class="btn btn-sm btn-primary"
                                href="" x-show="cek.length">Export</button> --}}



                            <input type="hidden" name="no_box" class="form-control" :value="cek">

                            {{-- <button name="submit" value="grading" x-transition x-show="cek.length"
                                class="btn btn-sm btn-primary" type="submit">
                                <i class="fas fa-plus"></i>
                                Grading
                                <span class="badge bg-info" x-text="cek.length" x-transition></span>
                            </button> --}}

                            <a href="{{ route('gradingbj.gudang') }}" style="color: white;background-color: #D722A9;"
                                class="btn btn-sm ">
                                <i class="fas fa-clipboard-list"></i> Gudang
                            </a>

                            <x-theme.button
                                href="{{ route('gudangsarang.invoice_grading', ['kategori' => 'grading']) }}"
                                icon="fa-clipboard-list" teks="Po Grading" />

                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#lewatPartaiModal">
                                <i class="fas fa-forward"></i> Lewatkan Partai
                            </button>

                            <button name="submit" value="serah" x-transition x-show="cek.length"
                                class="btn btn-sm btn-primary" type="submit">
                                <i class="fas fa-plus"></i>
                                Serah
                                <span x-text="cek.length"></span> Box |
                                <span x-text="`${totalPcs.toLocaleString('id-ID')} Pcs`"></span> |
                                <span x-text="`${totalGr.toLocaleString('id-ID')} Gr`"></span>
                            </button>
                            {{-- <button name="submit" value="selisih" x-transition x-show="cek.length"
                                class="btn btn-sm btn-danger" type="submit">
                                <i class="fas fa-plus"></i>
                                Selisih
                                <span class="badge bg-info" x-text="cek.length" x-transition></span>
                            </button> --}}
                        </form>
                    </div>
                </div>
                <button class="btn btn-xs btn-primary" @click="bulkBoxChecklist = !bulkBoxChecklist">Bulk No box
                    Checklist</button>
                <div class="row mb-2 mt-2">
                    <div class="col-lg-8" x-show="bulkBoxChecklist" x-transition>
                        <input type="text" x-model="bulkNoBoxInput" class="form-control form-control-sm"
                            placeholder="Masukkan no box, pisahkan dengan koma">
                    </div>
                    <div class="col-lg-4 d-flex gap-2" x-show="bulkBoxChecklist" x-transition>
                        <button x-show="bulkBoxChecklist" type="button" @click="applyBulkSelect()"
                            class="btn btn-sm btn-primary">Apply
                            Bulk</button>
                        <button x-show="bulkBoxChecklist" type="button"
                            @click="bulkNoBoxInput=''; cek=[]; updateTotals()"
                            class="btn btn-sm btn-secondary">Clear</button>
                    </div>
                </div>
                <div style="overflow-y: scroll; height: 500px">
                    <table id="tbl1" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">#</th>
                                {{-- <th class="dhead">Tanggal</th> --}}
                                <th class="dhead">Partai</th>
                                <th class="dhead text-center">No Box</th>
                                <th width="90" class="dhead">Tipe - Ket</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                @role('presiden')
                                    <th class="dhead text-end">Rp/Gr</th>
                                    <th class="dhead text-end">Ttl Rp</th>
                                @endrole
                                <th class="dhead text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($formulir as $i => $d)
                                <tr class="grading-source-row" data-partai="{{ $d->nm_partai }}"
                                    data-no-box="{{ $d->no_box }}" data-pcs="{{ $d->pcs_awal }}"
                                    data-gr="{{ $d->gr_awal }}"
                                    @click="cek.includes('{{ $d->no_box }}') ? cek = cek.filter(x => x !== '{{ $d->no_box }}') : cek.push('{{ $d->no_box }}'); updateTotals()">
                                    <td>{{ $i + 1 }}</td>
                                    {{-- <td>{{ tanggal($d->tanggal) }}</td> --}}
                                    <td>{{ $d->nm_partai }}</td>
                                    <td align="center">{{ $d->no_box }}</td>
                                    <td>{{ $d->tipe . ' - ' . $d->ket }}</td>
                                    <td class="text-end">{{ number_format($d->pcs_awal, 0) }}</td>
                                    <td class="text-end">{{ number_format($d->gr_awal, 0) }}</td>
                                    @role('presiden')
                                        <td class="text-end">
                                            {{-- {{ number_format(($d->cost_bk + $d->cost_cbt + $d->cost_eo + $d->cost_ctk + $d->cost_str + $d->cost_cu) / ($d->gr_awal ?? 1), 0) }} --}}
                                        </td>
                                        <td class="text-end">
                                            {{-- {{ number_format($d->cost_bk + $d->cost_cbt + $d->cost_eo + $d->cost_ctk + $d->cost_str + $d->cost_cu, 0) }} --}}
                                        </td>
                                    @endrole
                                    <td align="center">
                                        <input type="checkbox" class="form-check" x-model="cek"
                                            @change="updateTotals()" name="id[]" value="{{ $d->no_box }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        </section>

        <form id="formLewatPartai" action="{{ route('gradingbj.create_lewat_partai') }}" method="post">
            @csrf
            <input type="hidden" name="box_payload" id="payloadLewatPartai">
            <input type="hidden" name="nm_partai" id="namaLewatPartai">
            <input type="hidden" name="bulan" value="{{ date('m') }}">

            <x-theme.modal btnSave="T" title="Lewatkan Partai" idModal="lewatPartaiModal" size="modal-lg">
                <div class="row mb-3">
                    <div class="col-lg-8">
                        <label for="pilihLewatPartai">Pilih satu partai</label>
                        <select id="pilihLewatPartai" class="form-control" required>
                            <option value="">Pilih Partai</option>
                            @foreach (collect($formulir)->pluck('nm_partai')->filter()->unique()->sort()->values() as $partai)
                                <option value="{{ $partai }}">{{ $partai }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 d-flex align-items-end">
                        <a href="#" id="exportLewatPartai" class="btn btn-outline-success w-100">
                            <i class="fas fa-download"></i> Export JSON
                        </a>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <button type="button" class="nav-link active mode-lewat-tab" data-target="table">Mode Tabel</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link mode-lewat-tab" data-target="json">Mode JSON</button>
                    </li>
                </ul>

                <div id="modeLewatTable">
                    <div class="alert alert-info py-2">
                        Tujuan <strong>Gudang Grading Selesai</strong> |
                        Dipilih <strong id="totalBoxLewatPartai">0</strong> box |
                        <strong id="totalPcsLewatPartai">0</strong> Pcs |
                        <strong id="totalGrLewatPartai">0</strong> Gr
                    </div>
                    <div style="overflow-y:auto;max-height:320px">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="checkAllModalLewat" class="form-check-input" checked></th>
                                    <th>No Box</th><th class="text-end">Pcs</th><th class="text-end">Gr</th>
                                </tr>
                            </thead>
                            <tbody id="listBoxLewatPartai">
                                <tr><td colspan="4" class="text-center text-muted">Pilih partai.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="modeLewatJson" class="d-none">
                    <div class="mb-2">
                        <label for="fileJsonLewatPartai">Upload JSON</label>
                        <input type="file" id="fileJsonLewatPartai" class="form-control" accept="application/json,.json">
                    </div>
                    <div class="mb-2">
                        <label for="inputJsonLewatPartai">Atau tempel JSON</label>
                        <textarea id="inputJsonLewatPartai" class="form-control" rows="7"
                            placeholder='{"version":1,"type":"gradingbj_lewat_partai","nm_partai":"bjm 1007","no_boxes":[10953,11017]}'></textarea>
                    </div>
                    <div id="infoJsonLewatPartai" class="alert alert-secondary">Belum ada JSON.</div>
                </div>

                <div class="modal-footer px-0 pb-0">
                    <button type="submit" id="simpanLewatPartai" class="btn btn-success">
                        <i class="fas fa-forward"></i> Lewatkan Langsung
                    </button>
                </div>
            </x-theme.modal>
        </form>

        <x-theme.import title="Import grading" route="gradingbj.import" routeTemplate="gradingbj.template_import" />

        <x-theme.modal btnSave="T" title="Data Selisih" idModal="selisih">
            <div id="loadSelisih"></div>
        </x-theme.modal>

        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')
                $(".selisih").click(function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.load_selisih') }}",
                        success: function(r) {
                            $("#loadSelisih").html(r);
                            loadTable('tblSelisih')
                            loadTable('tblSusut')

                        }
                    });
                });
            </script>
            <script>
                $(document).ready(function() {
                    function unique(values) {
                        const seen = new Set();
                        return values.map(value => String(value).trim()).filter(value => {
                            if (!value || seen.has(value)) return false;
                            seen.add(value);
                            return true;
                        });
                    }

                    function availableRows(partai) {
                        const rows = [];
                        const seen = new Set();
                        $('.grading-source-row').each(function() {
                            if (String(this.dataset.partai).toLowerCase() !== String(partai).toLowerCase()) return;
                            if (seen.has(this.dataset.noBox)) return;
                            seen.add(this.dataset.noBox);
                            rows.push({
                                no_box: this.dataset.noBox,
                                pcs: parseFloat(this.dataset.pcs) || 0,
                                gr: parseFloat(this.dataset.gr) || 0
                            });
                        });
                        return rows;
                    }

                    function selectedBoxes() {
                        return unique($('#listBoxLewatPartai .modal-lewat-checkbox:checked').map(function() {
                            return this.value;
                        }).get());
                    }

                    function payload(boxes) {
                        return {
                            version: 1,
                            type: 'gradingbj_lewat_partai',
                            nm_partai: $('#pilihLewatPartai').val(),
                            exported_at: new Date().toISOString(),
                            no_boxes: unique(boxes)
                        };
                    }

                    function updateTotals() {
                        let pcs = 0;
                        let gr = 0;
                        const checked = $('#listBoxLewatPartai .modal-lewat-checkbox:checked');
                        checked.each(function() {
                            pcs += parseFloat(this.dataset.pcs) || 0;
                            gr += parseFloat(this.dataset.gr) || 0;
                        });
                        $('#totalBoxLewatPartai').text(checked.length);
                        $('#totalPcsLewatPartai').text(pcs.toLocaleString('id-ID'));
                        $('#totalGrLewatPartai').text(gr.toLocaleString('id-ID'));
                        const all = $('#listBoxLewatPartai .modal-lewat-checkbox');
                        $('#checkAllModalLewat').prop('checked', all.length > 0 && all.length === checked.length);
                        $('#payloadLewatPartai').val(checked.length ? JSON.stringify(payload(selectedBoxes())) : '');
                    }

                    function renderPartai(partai, selected) {
                        const rows = availableRows(partai);
                        const selectedSet = selected ? new Set(unique(selected)) : null;
                        const body = $('#listBoxLewatPartai').empty();
                        rows.forEach(row => {
                            const checked = !selectedSet || selectedSet.has(row.no_box);
                            body.append($('<tr>').append(
                                $('<td>').append($('<input>', {
                                    type: 'checkbox',
                                    class: 'form-check-input modal-lewat-checkbox',
                                    value: row.no_box,
                                    checked: checked,
                                    'data-pcs': row.pcs,
                                    'data-gr': row.gr
                                })),
                                $('<td>').text(row.no_box),
                                $('<td>', {class: 'text-end'}).text(row.pcs),
                                $('<td>', {class: 'text-end'}).text(row.gr)
                            ));
                        });
                        if (!rows.length) body.html('<tr><td colspan="4" class="text-center text-muted">Tidak ada box tersedia.</td></tr>');
                        $('#namaLewatPartai').val(partai);
                        updateTotals();
                    }

                    $('#pilihLewatPartai').on('change', function() {
                        renderPartai(this.value);
                    });
                    $('#listBoxLewatPartai').on('change', '.modal-lewat-checkbox', updateTotals);
                    $('#checkAllModalLewat').on('change', function() {
                        $('#listBoxLewatPartai .modal-lewat-checkbox').prop('checked', this.checked);
                        updateTotals();
                    });

                    $('.mode-lewat-tab').on('click', function() {
                        const target = this.dataset.target;
                        $('.mode-lewat-tab').removeClass('active');
                        $(this).addClass('active');
                        $('#modeLewatTable').toggleClass('d-none', target !== 'table');
                        $('#modeLewatJson').toggleClass('d-none', target !== 'json');
                    });

                    $('#exportLewatPartai').on('click', function(event) {
                        event.preventDefault();
                        const boxes = selectedBoxes();
                        if (!boxes.length) return alert('Pilih partai dan minimal satu box.');
                        const blob = new Blob([JSON.stringify(payload(boxes), null, 2)], {type: 'application/json'});
                        const url = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'grading-lewat-' + $('#pilihLewatPartai').val().replace(/[^a-z0-9_-]+/gi, '-') + '.json';
                        document.body.appendChild(link); link.click(); link.remove(); URL.revokeObjectURL(url);
                    });

                    function importJson(raw) {
                        try {
                            const data = JSON.parse(raw);
                            if (!data || data.type !== 'gradingbj_lewat_partai' || !Array.isArray(data.no_boxes) || !data.nm_partai) {
                                throw new Error('Format JSON tidak valid.');
                            }
                            const option = $('#pilihLewatPartai option').filter(function() {
                                return String(this.value).toLowerCase() === String(data.nm_partai).toLowerCase();
                            }).first();
                            if (!option.length) throw new Error('Partai JSON tidak tersedia.');
                            $('#pilihLewatPartai').val(option.val());
                            const requested = unique(data.no_boxes);
                            const available = new Set(availableRows(option.val()).map(row => row.no_box));
                            const missing = requested.filter(noBox => !available.has(noBox));
                            if (missing.length) throw new Error('Box tidak tersedia: ' + missing.slice(0, 10).join(', '));
                            renderPartai(option.val(), requested);
                            $('#infoJsonLewatPartai').attr('class', 'alert alert-success').text(requested.length + ' box berhasil dibaca.');
                        } catch (error) {
                            $('#infoJsonLewatPartai').attr('class', 'alert alert-danger').text(error.message);
                        }
                    }
                    $('#inputJsonLewatPartai').on('change', function() {
                        if (this.value.trim()) importJson(this.value.trim());
                    });
                    $('#fileJsonLewatPartai').on('change', function() {
                        const file = this.files && this.files[0];
                        if (!file) return;
                        const reader = new FileReader();
                        reader.onload = event => {
                            $('#inputJsonLewatPartai').val(event.target.result);
                            importJson(event.target.result);
                        };
                        reader.readAsText(file);
                    });

                    $('#formLewatPartai').on('submit', function(event) {
                        const boxes = selectedBoxes();
                        if (!$('#pilihLewatPartai').val() || !boxes.length) {
                            event.preventDefault();
                            return alert('Pilih satu partai dan minimal satu box.');
                        }
                        $('#payloadLewatPartai').val(JSON.stringify(payload(boxes)));
                        $('#simpanLewatPartai').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
                    });
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>
