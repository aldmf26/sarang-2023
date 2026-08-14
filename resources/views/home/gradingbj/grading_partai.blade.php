<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
            <div>
                <a target="_blank" href="{{ route('gradingbj.gudang') }}" class="btn btn-sm btn-info">
                    <i class="fa fa-warehouse"></i> List Gudang Box
                </a>
                <a href="#" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#gradeModal">
                    <i class="fa fa-list"></i> List Grade
                </a>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <form x-data="gradingData()" x-init="init()" action="{{ route('gradingbj.create_partai') }}"
            method="post">
            @csrf

            {{-- Header Form --}}
            <div class="row">
                <div class="col-lg-7">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">Pengawas</th>
                                <th class="dhead" width="100">No Invoice</th>
                                <th class="dhead">Tgl</th>
                                <th class="dhead" width="200">Bulan dibayar</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" readonly value="{{ $user }}" name="pengawas"
                                        class="form-control" required>
                                    <input type="hidden" name="nm_partai" value="{{ $nm_partai }}">
                                </td>
                                <td>
                                    <input type="text" readonly value="{{ $no_invoice }}" name="no_nota"
                                        class="form-control" required>
                                </td>
                                <td>
                                    <input readonly type="date" value="{{ date('Y-m-d') }}" name="tgl"
                                        class="form-control" required>
                                </td>
                                <td>
                                    <select name="bulan" class="form-control" required>
                                        <option value="">Pilih Bulan</option>
                                        @foreach (getListBulan() as $l)
                                            <option value="{{ $l->bulan }}"
                                                {{ date('m') == $l->bulan ? 'selected' : '' }}>
                                                {{ $l->nm_bulan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <x-theme.alert pesan="{{ session()->get('error') }}" />

            <div class="row">
                {{-- Tabel Box Dipilih --}}
                <div class="col-lg-5">
                    <h6>Box Dipilih <span class="text-success">Partai : {{ $nm_partai }}</span></h6>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-center">Tipe</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-end">Pcs Ok</th>
                                <th class="dhead text-end">Turun Grade</th>
                                @role('presiden')
                                    <th class="dhead text-end">Rp/gr</th>
                                    <th class="dhead text-end">Total Rp</th>
                                @endrole
                                <th class="dhead text-center">Aksi</th>
                            </tr>
                        </thead>
                        <thead class="bg-white">
                            <tr>
                                <th class="text-end">
                                    <h6>Total</h6>
                                </th>
                                <th></th>
                                @php
                                    $ttlPcs = array_sum(array_column($getFormulir, 'pcs_awal'));
                                    $ttlGr = array_sum(array_column($getFormulir, 'gr_awal'));
                                    $ttlRp =
                                        sumBk($getFormulir, 'cost_bk') +
                                        sumBk($getFormulir, 'cost_kerja') +
                                        sumBk($getFormulir, 'cost_cu') +
                                        sumBk($getFormulir, 'cost_op');
                                    $cost_bk = sumBk($getFormulir, 'cost_bk');
                                    $cost_kerja = sumBk($getFormulir, 'cost_kerja');
                                    $cost_cu = sumBk($getFormulir, 'cost_cu');
                                    $cost_op = sumBk($getFormulir, 'cost_op');
                                    $rp_gr = $ttlGr > 0 ? $ttlRp / $ttlGr : 0;
                                    $rp_gr_bk = $ttlGr > 0 ? $cost_bk / $ttlGr : 0;
                                    $rp_gr_kerja = $ttlGr > 0 ? $cost_kerja / $ttlGr : 0;
                                    $rp_gr_cu = $ttlGr > 0 ? $cost_cu / $ttlGr : 0;
                                    $rp_gr_op = $ttlGr > 0 ? $cost_op / $ttlGr : 0;
                                @endphp
                                <th class="text-end">
                                    <h6>
                                        <input type="hidden" name="tipe" value="{{ $getFormulir[0]->tipe }}">
                                        <input type="hidden" name="ttlPcs" value="{{ $ttlPcs }}">
                                        <input type="hidden" name="rpGr" value="{{ $rp_gr }}">
                                        <input type="hidden" name="rpGrBk" value="{{ $rp_gr_bk }}">
                                        <input type="hidden" name="rpGrKerja" value="{{ $rp_gr_kerja }}">
                                        <input type="hidden" name="rpGrCu" value="{{ $rp_gr_cu }}">
                                        <input type="hidden" name="rpGrOp" value="{{ $rp_gr_op }}">
                                        {{ $ttlPcs }}
                                    </h6>
                                </th>
                                <th class="text-end">
                                    <h6>
                                        <input type="hidden" name="ttlGr" value="{{ $ttlGr }}">
                                        {{ $ttlGr }}
                                    </h6>
                                </th>
                                <th class="text-end">
                                    {{ number_format($ttlPcs - $turunGradeTotal, 0) }}
                                </th>
                                <th class="text-end">
                                    {{ number_format($turunGradeTotal, 0) }}
                                </th>
                                @role('presiden')
                                    <th class="text-end">
                                        <h6></h6>
                                    </th>
                                    <th class="text-end">
                                        <h6>{{ number_format(sumBk($getFormulir, 'cost_bk') + sumBk($getFormulir, 'cost_cbt') + sumBk($getFormulir, 'cost_str') + sumBk($getFormulir, 'cost_eo') + sumBk($getFormulir, 'cost_ctk') + sumBk($getFormulir, 'cost_cu'), 0) }}
                                        </h6>
                                    </th>
                                @endrole
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getFormulir as $d)
                                @php
                                    $pcsPth = $hancuranPcsByBox[$d->no_box] ?? 0;
                                @endphp
                                <tr class="pointer">
                                    <td>{{ $d->no_box }} <input type="hidden" name="no_box[]"
                                            value="{{ $d->no_box }}"></td>
                                    <td align="center">{{ $d->tipe }}-{{ $d->ket }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">{{ $d->pcs_awal - $pcsPth }}</td>
                                    <td align="right">{{ $pcsPth }}</td>
                                    @role('presiden')
                                        <td align="right">
                                            {{ number_format($d->gr_awal == 0 ? 0 : $d->total_rp / $d->gr_awal, 0) }}</td>
                                        <td align="right">{{ number_format($d->total_rp, 0) }}</td>
                                    @endrole
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="hapusBoxFormulirGrading('{{ $d->no_box }}')"
                                            title="Hapus dan kembalikan ke stok">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Tabel Hasil Grading --}}
                <div class="col-lg-7">
                    <h6>Hasil Grading</h6>

                    {{-- BULK INPUT --}}
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white py-2">
                            <strong>📋 Input Cepat (Bulk)</strong>
                        </div>
                        <div class="card-body p-2">
                            <textarea x-model="bulkInput" class="form-control mb-2" rows="4"
                                placeholder="Format: Grade Pcs Gr Box (pisah dengan Tab/Spasi)&#10;&#10;Contoh:&#10;VR	65	250	4001&#10;DR	58	250	4002&#10;VR	67	250	4003"></textarea>
                            <button type="button" class="btn btn-sm btn-success" @click="importBulk()">
                                <i class="fas fa-file-import"></i> Import Bulk
                            </button>
                            <small class="text-muted d-block mt-1">Copy 4 kolom dari Excel, lalu paste di atas</small>
                        </div>
                    </div>

                    <table class="table table-bordered" id="tbl3">
                        <thead>
                            <tr>
                                <th class="dhead" width="130">No</th>
                                <th class="dhead">Grade</th>
                                <th class="dhead text-end" width="210">Pcs</th>
                                <th class="dhead text-end" width="210">Gr</th>
                                <th class="dhead" width="300">Box Grade</th>
                                <th class="dhead" width="300">Cek</th>
                                <th class="dhead" width="300">Not Oke</th>
                                <th class="dhead">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="number" class="form-control" x-model="baris" min="1"></td>
                                <td>
                                    <h6>Total</h6>
                                </td>
                                <td class="text-end">
                                    <h6 x-text="formatNumber(getTotalPcs())">0</h6>
                                </td>
                                <td class="text-end">
                                    <h6 x-text="formatNumber(getTotalGr())">0</h6>
                                </td>
                                <td colspan="4"></td>
                            </tr>

                            <template x-for="i in parseInt(baris)" :key="i">
                                <tr>
                                    <td x-text="i"></td>
                                    <td>
                                        <select x-init="initSelectRow()" required name="grade[]"
                                            class="selectGrade grade" :urutan="i">
                                            <option value="">Pilih Grade</option>
                                            @foreach ($gradeBentuk as $g)
                                                <option value="{{ $g->nm_grade }}">{{ strtoupper($g->nm_grade) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input x-model="pcs[i-1]" type="text" autocomplete="off"
                                            class="text-end form-control" name="pcs[]">
                                    </td>
                                    <td>
                                        <input x-model="gr[i-1]" type="text" autocomplete="off"
                                            class="text-end form-control" name="gr[]" required>
                                    </td>
                                    <td>
                                        <input x-model="boxkirim[i-1]" type="text" autocomplete="off"
                                            class="form-control boxkirim" :urutan="i" name="box_sp[]"
                                            required>
                                    </td>
                                    <td class="cek" :urutan="i"></td>
                                    <td><input type="checkbox" :name="'not_oke[' + (i - 1) + ']'"></td>
                                    <td>
                                        <span @click="removeRow(i-1)" class="badge bg-danger pointer">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                    </td>
                                </tr>
                            </template>

                            <tr>
                                <td colspan="8">
                                    <button type="button" @click="baris = parseInt(baris) + 1"
                                        class="btn btn-sm btn-primary btn-block">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-md btn-primary float-end" x-show="!isDisabled"
                @click="isDisabled = true">
                Save
            </button>
        </form>

        <form id="hapusBoxFormulirGrading" method="POST"
            action="{{ route('gradingbj.hapus_formulir_grading_box') }}" class="d-none">
            @csrf
            @method('DELETE')
            <input type="hidden" name="no_invoice" value="{{ $no_invoice }}">
            <input type="hidden" name="no_box" id="hapusNoBoxGrading">
        </form>

        <script>
            function hapusBoxFormulirGrading(noBox) {
                if (!confirm('Hapus box ' + noBox + ' dari formulir grading dan kembalikan menjadi stok?')) {
                    return;
                }

                document.getElementById('hapusNoBoxGrading').value = noBox;
                document.getElementById('hapusBoxFormulirGrading').submit();
            }
        </script>

        <div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="gradeModalLabel"><i class="fa fa-list"></i> Daftar Grade</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card border-success mb-3">
                            <div class="card-header bg-success text-white py-2">
                                <strong><i class="fas fa-plus-circle"></i> Tambah Grade Baru</strong>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('gradingbj.storeGrade') }}" method="post"
                                    class="row g-2 align-items-end">
                                    @csrf
                                    <div class="col-md-4">
                                        <label class="form-label">Grade</label>
                                        <input type="text" name="nm_grade" class="form-control"
                                            value="{{ old('nm_grade') }}" placeholder="Misal: VR" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <input type="text" name="status" class="form-control"
                                            value="{{ old('status') }}" placeholder="Misal: bentuk / turun" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tipe</label>
                                        <input type="text" name="tipe" class="form-control"
                                            value="{{ old('tipe') }}" placeholder="Misal: VR / DR" required>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                    </div>
                                </form>

                                @if ($errors->any())
                                    <div class="alert alert-danger mt-3 mb-0">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <input id="gradeSearch" type="text" class="form-control"
                                    placeholder="Cari grade, status, atau tipe...">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered" id="gradeListTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Grade</th>
                                        <th>Status</th>
                                        <th>Tipe</th>
                                        <th>Terpakai</th>
                                        <th width="180">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tbGradeAll as $grade)
                                        @php $used = $gradeUsed[$grade->nm_grade] ?? 0; @endphp
                                        <tr class="{{ $used ? 'table-secondary' : '' }}">
                                            <td class="align-middle">{{ strtoupper($grade->nm_grade) }}</td>
                                            <td class="align-middle">{{ $grade->status }}</td>
                                            <td class="align-middle">{{ $grade->tipe }}</td>
                                            <td class="align-middle text-center">
                                                @if ($used)
                                                    <span class="badge bg-info">Dipakai {{ $used }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Belum dipakai</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if (!$used)
                                                    <form
                                                        action="{{ route('gradingbj.deleteGrade', $grade->id_grade) }}"
                                                        method="post" class="d-inline delete-grade-form">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-danger delete-grade-btn">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @section('scripts')
            <script>
                function gradingData() {
                    return {
                        baris: {{ session('form_data') ? session('form_data.baris') : 1 }},
                        pcs: {!! session('form_data') ? json_encode(session('form_data.pcs')) : '[]' !!},
                        gr: {!! session('form_data') ? json_encode(session('form_data.gr')) : '[]' !!},
                        boxkirim: {!! session('form_data') ? json_encode(session('form_data.box_sp')) : '[]' !!},
                        bulkInput: '',
                        isDisabled: false,

                        init() {
                            setTimeout(() => {
                                $('.selectGrade').select2({
                                    width: '100%'
                                });
                            }, 100);
                        },

                        initSelectRow() {
                            setTimeout(() => {
                                if (!$(this.$el).hasClass('select2-hidden-accessible')) {
                                    $(this.$el).select2({
                                        width: '100%'
                                    });
                                }
                            }, 50);
                        },

                        getTotalPcs() {
                            return this.pcs.reduce((sum, val) => sum + (parseInt(val) || 0), 0);
                        },

                        getTotalGr() {
                            return this.gr.reduce((sum, val) => sum + (parseInt(val) || 0), 0);
                        },

                        formatNumber(value) {
                            return parseFloat(value || 0).toLocaleString('id-ID');
                        },

                        removeRow(index) {
                            this.pcs.splice(index, 1);
                            this.gr.splice(index, 1);
                            this.boxkirim.splice(index, 1);
                            this.baris--;
                        },

                        importBulk() {
                            const lines = this.bulkInput.trim().split('\n').filter(l => l.trim());

                            if (!lines.length) {
                                alert('Data kosong!');
                                return;
                            }

                            const parsed = lines.map(line => {
                                const parts = line.trim().split(/[\t\s]+/);
                                return {
                                    grade: (parts[0] || '').trim(),
                                    pcs: (parts[1] || '').trim(),
                                    gr: (parts[2] || '').trim(),
                                    box: (parts[3] || '').trim()
                                };
                            });

                            this.baris = lines.length;
                            this.pcs = parsed.map(d => d.pcs);
                            this.gr = parsed.map(d => d.gr);
                            this.boxkirim = parsed.map(d => d.box);
                            this.bulkInput = '';

                            this.$nextTick(() => {
                                setTimeout(() => {
                                    $('.selectGrade').select2('destroy');
                                    $('.selectGrade').select2({
                                        width: '100%'
                                    });

                                    setTimeout(() => {
                                        let success = 0;

                                        parsed.forEach((data, idx) => {
                                            const $sel = $('select[name="grade[]"]').eq(idx);

                                            if ($sel.length && data.grade) {
                                                let matchedVal = null;
                                                $sel.find('option').each(function() {
                                                    const optVal = $(this).val();
                                                    if (optVal.toUpperCase() === data.grade
                                                        .toUpperCase()) {
                                                        matchedVal = optVal;
                                                        return false;
                                                    }
                                                });

                                                if (matchedVal) {
                                                    $sel.val(matchedVal).trigger('change.select2');
                                                    success++;
                                                }
                                            }
                                        });

                                        alert(success + '/' + lines.length + ' baris berhasil!');
                                    }, 300);
                                }, 200);
                            });
                        }
                    }
                }
            </script>

            <script>
                $('#tbl3').on('keydown', 'input[type="text"]', function(e) {
                    const $cell = $(this).closest('td');
                    const colIdx = $cell.index();
                    const $row = $cell.parent();

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        $row.next('tr').find(`td:eq(${colIdx}) input[type="text"]`).focus();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        $row.prev('tr').find(`td:eq(${colIdx}) input[type="text"]`).focus();
                    }
                });
            </script>

            <script>
                $(document).ready(function() {
                    var isRestoring = false;

                    $(document).on("keyup", ".boxkirimd", function(e) {
                        if (isRestoring) return;

                        var urutan = $(this).attr('urutan');
                        var boxkirim = $('.boxkirim[urutan="' + urutan + '"]').val();
                        var grade = $('.grade[urutan="' + urutan + '"]').val();

                        $.ajax({
                            type: "get",
                            url: "{{ route('gradingbj.cek_box_kirim') }}",
                            data: {
                                boxkirim,
                                grade
                            },
                            success: function(r) {
                                $('.cek[urutan="' + urutan + '"]').html(r);
                            }
                        });
                    });

                    $(document).on("change", ".grade", function(e) {
                        if (isRestoring) return;

                        var urutan = $(this).attr('urutan');
                        var boxkirim = $('.boxkirim[urutan="' + urutan + '"]').val();
                        var grade = $('.grade[urutan="' + urutan + '"]').val();

                        $.ajax({
                            type: "get",
                            url: "{{ route('gradingbj.cek_box_kirim') }}",
                            data: {
                                boxkirim,
                                grade
                            },
                            dataType: "json",
                            success: function(r) {
                                if (!isRestoring) {
                                    $('.cek[urutan="' + urutan + '"]').html(r.html);
                                }
                            }
                        });
                    });

                    @if (session('form_data'))
                        setTimeout(function() {
                            var formData = @json(session('form_data'));
                            isRestoring = true;

                            formData.grade.forEach(function(grade, idx) {
                                var $sel = $('select[name="grade[]"]').eq(idx);
                                if ($sel.length && grade) {
                                    $sel.val(grade).select2();
                                }

                                if (formData.not_oke && formData.not_oke[idx]) {
                                    $('input[name="not_oke[' + idx + ']"]').prop('checked', true);
                                }
                            });

                            setTimeout(function() {
                                isRestoring = false;
                            }, 1000);
                        }, 500);
                    @endif
                });
            </script>

            <script>
                function filterGradeTable() {
                    const query = $('#gradeSearch').val().toLowerCase();
                    $('#gradeListTable tbody tr').each(function() {
                        const text = $(this).text().toLowerCase();
                        $(this).toggle(text.indexOf(query) !== -1);
                    });
                }

                $(document).ready(function() {
                    $('#gradeSearch').on('input', filterGradeTable);

                    @if ($errors->any())
                        var gradeModal = new bootstrap.Modal(document.getElementById('gradeModal'));
                        gradeModal.show();
                    @endif
                });
            </script>

            {{-- Jalur LEWAT dipindahkan ke modal halaman utama Grading BJ.
            <script>
                $(document).ready(function() {
                    const partaiLewat = @json($nm_partai);
                    const tipePayloadLewat = 'gradingbj_lewat_partai';

                    function uniqueNoBoxes(values) {
                        const seen = new Set();
                        return values.map(value => String(value).trim()).filter(value => {
                            if (!value || seen.has(value)) return false;
                            seen.add(value);
                            return true;
                        });
                    }

                    function selectedLewatBoxes() {
                        return uniqueNoBoxes($('.lewat-box-checkbox:checked').map(function() {
                            return this.value;
                        }).get());
                    }

                    function buildLewatPayload(noBoxes) {
                        return {
                            version: 1,
                            type: tipePayloadLewat,
                            nm_partai: partaiLewat,
                            exported_at: new Date().toISOString(),
                            no_boxes: uniqueNoBoxes(noBoxes)
                        };
                    }

                    function updateLewatTotals() {
                        let pcs = 0;
                        let gr = 0;
                        const counted = new Set();

                        $('.lewat-box-checkbox:checked').each(function() {
                            if (counted.has(this.value)) return;
                            counted.add(this.value);
                            pcs += parseFloat(this.dataset.pcs) || 0;
                            gr += parseFloat(this.dataset.gr) || 0;
                        });

                        const selected = selectedLewatBoxes();
                        const all = uniqueNoBoxes($('.lewat-box-checkbox').map(function() {
                            return this.value;
                        }).get());

                        $('#lewatSelectedCount').text(selected.length);
                        $('#lewatSelectedPcs').text(pcs.toLocaleString('id-ID'));
                        $('#lewatSelectedGr').text(gr.toLocaleString('id-ID'));
                        $('#checkAllLewatPartai').prop('checked', all.length > 0 && selected.length === all.length);
                        $('#lewatBoxPayload').val(selected.length ? JSON.stringify(buildLewatPayload(selected)) : '');
                    }

                    function setLewatMode(active) {
                        $('#panelLewatGrading, #submitLewatPartai').toggleClass('d-none', !active);
                        $('#hasilGradingNormal, #submitGradingNormal').toggleClass('d-none', active);
                        $('.lewat-control').toggleClass('d-none', !active);
                        $('#hasilGradingNormal').find(':input').prop('disabled', active);
                        $('#modeLewatGrading').toggleClass('btn-success active', active)
                            .toggleClass('btn-outline-success', !active);
                        $('#modeNormalGrading').toggleClass('btn-primary active', !active)
                            .toggleClass('btn-outline-primary', active);
                        updateLewatTotals();
                    }

                    $('#modeNormalGrading').on('click', function() {
                        setLewatMode(false);
                    });
                    $('#modeLewatGrading').on('click', function() {
                        setLewatMode(true);
                    });
                    $('#checkAllLewatPartai').on('change', function() {
                        $('.lewat-box-checkbox').prop('checked', this.checked);
                        updateLewatTotals();
                    });
                    $('.lewat-box-checkbox').on('change', updateLewatTotals);

                    $('#exportLewatPartaiJson').on('click', function() {
                        const selected = selectedLewatBoxes();
                        if (!selected.length) {
                            alert('Pilih minimal satu box.');
                            return;
                        }

                        const blob = new Blob([JSON.stringify(buildLewatPayload(selected), null, 2)], {
                            type: 'application/json'
                        });
                        const url = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'grading-lewat-' + String(partaiLewat).replace(/[^a-z0-9_-]+/gi, '-') + '.json';
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        URL.revokeObjectURL(url);
                    });

                    function applyLewatJson(raw) {
                        try {
                            const payload = JSON.parse(raw);
                            if (!payload || payload.type !== tipePayloadLewat || !Array.isArray(payload.no_boxes)) {
                                throw new Error('Format JSON Grading Lewat tidak valid.');
                            }
                            if (String(payload.nm_partai || '').toLowerCase() !== String(partaiLewat).toLowerCase()) {
                                throw new Error('Partai JSON tidak sama dengan partai form.');
                            }

                            const requested = new Set(uniqueNoBoxes(payload.no_boxes));
                            const available = new Set($('.lewat-box-checkbox').map(function() {
                                return this.value;
                            }).get());
                            const missing = Array.from(requested).filter(noBox => !available.has(noBox));
                            if (missing.length) {
                                throw new Error('Box tidak tersedia: ' + missing.slice(0, 10).join(', '));
                            }

                            $('.lewat-box-checkbox').each(function() {
                                this.checked = requested.has(this.value);
                            });
                            updateLewatTotals();
                            $('#infoLewatPartaiJson').removeClass('text-muted text-danger').addClass('text-success')
                                .text(requested.size + ' box berhasil dibaca dari JSON.');
                        } catch (error) {
                            $('#infoLewatPartaiJson').removeClass('text-muted text-success').addClass('text-danger')
                                .text(error.message);
                        }
                    }

                    $('#inputLewatPartaiJson').on('change', function() {
                        if (this.value.trim()) applyLewatJson(this.value.trim());
                    });
                    $('#fileLewatPartaiJson').on('change', function() {
                        const file = this.files && this.files[0];
                        if (!file) return;
                        const reader = new FileReader();
                        reader.onload = event => {
                            $('#inputLewatPartaiJson').val(event.target.result);
                            applyLewatJson(event.target.result);
                        };
                        reader.readAsText(file);
                    });

                    $('#submitLewatPartai').on('click', function(event) {
                        const selected = selectedLewatBoxes();
                        if (!selected.length) {
                            event.preventDefault();
                            alert('Pilih minimal satu box.');
                            return;
                        }
                        $('#lewatBoxPayload').val(JSON.stringify(buildLewatPayload(selected)));
                        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
                    });

                    updateLewatTotals();
                });
            </script>
            --}}
        @endsection
    </x-slot>
</x-theme.app>
