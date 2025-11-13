<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
            <a target="_blank" href="{{ route('gradingbj.gudang') }}" class="btn btn-sm btn-info">
                <i class="fa fa-warehouse"></i> List Gudang Box
            </a>
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
                                        sumBk($getFormulir, 'cost_cbt') +
                                        sumBk($getFormulir, 'cost_str') +
                                        sumBk($getFormulir, 'cost_eo') +
                                        sumBk($getFormulir, 'cost_ctk') +
                                        sumBk($getFormulir, 'cost_cu');
                                    $cost_bk = sumBk($getFormulir, 'cost_bk');
                                    $cost_kerja =
                                        sumBk($getFormulir, 'cost_cbt') +
                                        sumBk($getFormulir, 'cost_str') +
                                        sumBk($getFormulir, 'cost_eo') +
                                        sumBk($getFormulir, 'cost_ctk');
                                    $cost_cu = sumBk($getFormulir, 'cost_cu');
                                    $rp_gr = $ttlRp / $ttlGr;
                                    $rp_gr_bk = $cost_bk / $ttlGr;
                                    $rp_gr_kerja = $cost_kerja / $ttlGr;
                                    $rp_gr_cu = $cost_cu / $ttlGr;
                                @endphp
                                <th class="text-end">
                                    <h6>
                                        <input type="hidden" name="tipe" value="{{ $getFormulir[0]->tipe }}">
                                        <input type="hidden" name="ttlPcs" value="{{ $ttlPcs }}">
                                        <input type="hidden" name="rpGr" value="{{ $rp_gr }}">
                                        <input type="hidden" name="rpGrBk" value="{{ $rp_gr_bk }}">
                                        <input type="hidden" name="rpGrKerja" value="{{ $rp_gr_kerja }}">
                                        <input type="hidden" name="rpGrCu" value="{{ $rp_gr_cu }}">
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
                                    @php $turunGrade = 0; @endphp
                                    @foreach ($getFormulir as $d)
                                        @php
                                            $pcsPth = DB::selectOne(
                                                "SELECT sum(a.pcs) as pcs FROM tb_hancuran as a where a.kategori in('cetak','sortir','grade','grading') and a.no_box = '$d->no_box'",
                                            );
                                            $turunGrade += $pcsPth->pcs ?? 0;
                                        @endphp
                                    @endforeach
                                    {{ number_format($ttlPcs - $turunGrade, 0) }}
                                </th>
                                <th class="text-end">
                                    @php $turunGrade = 0; @endphp
                                    @foreach ($getFormulir as $d)
                                        @php
                                            $pcsPth = DB::selectOne(
                                                "SELECT sum(a.pcs) as pcs FROM tb_hancuran as a where a.kategori in('cetak','sortir','grade','grading') and a.no_box = '$d->no_box'",
                                            );
                                            $turunGrade += $pcsPth->pcs ?? 0;
                                        @endphp
                                    @endforeach
                                    {{ number_format($turunGrade, 0) }}
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getFormulir as $d)
                                @php
                                    $pcsPth = DB::selectOne(
                                        "SELECT sum(a.pcs) as pcs FROM tb_hancuran as a where a.kategori in('cetak','sortir','grade','grading') and a.no_box = '$d->no_box'",
                                    );
                                @endphp
                                <tr class="pointer">
                                    <td>{{ $d->no_box }} <input type="hidden" name="no_box[]"
                                            value="{{ $d->no_box }}"></td>
                                    <td align="center">{{ $d->tipe }}-{{ $d->ket }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
                                    <td align="right">{{ $d->pcs_awal - ($pcsPth->pcs ?? 0) }}</td>
                                    <td align="right">{{ $pcsPth->pcs }}</td>
                                    @php
                                        $ttl_rp =
                                            $d->cost_bk +
                                            $d->cost_cbt +
                                            $d->cost_ctk +
                                            $d->cost_eo +
                                            $d->cost_str +
                                            $d->cost_cu;
                                    @endphp
                                    @role('presiden')
                                        <td align="right">{{ number_format($ttl_rp / $d->gr_awal, 0) }}</td>
                                        <td align="right">{{ number_format($ttl_rp, 0) }}</td>
                                    @endrole
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
        @endsection
    </x-slot>
</x-theme.app>
