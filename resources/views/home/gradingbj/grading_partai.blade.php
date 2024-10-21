<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <form x-data="{
            cek: [],
            rows: [],
            selectedRowIndex: null,
            pcs: Array().fill(''),
            gr: Array().fill(''),
            susut: {
                pcs: 0,
                gr: 0
            },
            ttlSum: function(type) {
                const array = type === 'pcs' ? this.pcs : this.gr;
                const total = array.reduce((acc, value) => acc + (parseInt(value) || 0), 0);
        
                if (type === 'pcs') {
                    return total + (parseInt(this.susut.pcs) || 0); // Kurangi nilai susut dari total pcs
                } else if (type === 'gr') {
                    return total + (parseInt(this.susut.gr) || 0); // Kurangi nilai susut dari total gr jika diperlukan
                }
        
                return total;
            },
            numberFormat(value) {
                return parseFloat(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).replace(/\./g, ',');
            },
            initSelect2: function() {
                $('.selectGrade').select2();
            },
            removeRow: function(index) {
                this.rows.splice(index, 1);
                this.pcs.splice(index, 1);
                this.gr.splice(index, 1);
            },
        
        }" x-init="initSelect2()" action="{{ route('gradingbj.create_partai') }}"
            method="post">
            @csrf
            <div class="row">
                <div class="col-lg-6">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">Pengawas</th>
                                <th class="dhead">No Invoice</th>
                                <th class="dhead">Tgl</th>
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

                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <x-theme.alert pesan="{{ session()->get('error') }}" />
            <div class="row">
                <div class="col-lg-5">
                    <h6>Box Dipilih <span class="text-success">Partai : {{ $nm_partai }}</span></h6>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-center">Tipe</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>

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
                                @role('presiden')
                                    <th class="text-end">
                                        <h6>

                                        </h6>
                                    </th>

                                    <th class="text-end">
                                        <h6>
                                            {{ number_format(sumBk($getFormulir, 'cost_bk') + sumBk($getFormulir, 'cost_cbt') + sumBk($getFormulir, 'cost_str') + sumBk($getFormulir, 'cost_eo') + sumBk($getFormulir, 'cost_ctk') + sumBk($getFormulir, 'cost_cu'), 0) }}
                                        </h6>
                                    </th>
                                @endrole
                            </tr>

                        </thead>
                        <tbody>
                            @foreach ($getFormulir as $d)
                                <tr class="pointer">
                                    <td>{{ $d->no_box }} <input type="hidden" name="no_box[]"
                                            value="{{ $d->no_box }}"></td>
                                    <td align="center">{{ $d->tipe }}-{{ $d->ket }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>
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
                                        <td align="right">
                                            {{ number_format($ttl_rp, 0) }}
                                        </td>
                                    @endrole

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-7">
                    <h6>Hasil Grading</h6>
                    <table class="table table-bordered" id="tbl3">
                        <thead>
                            <tr>
                                <th class="dhead">No</th>
                                <th class="dhead">Grade</th>
                                <th class="dhead text-end" width="200">Pcs</th>
                                <th class="dhead text-end" width="200">Gr</th>
                                <th class="dhead " width="300">Box Grade</th>
                                <th class="dhead" width="300">Cek</th>
                                <th class="dhead">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td>
                                    <h6>Total</h6>
                                </td>
                                <td class="text-end">
                                    <h6 x-text="numberFormat(ttlSum('pcs'))">0</h6>
                                </td>
                                <td class="text-end">
                                    <h6 x-text="numberFormat(ttlSum('gr'))">0</h6>
                                </td>
                            </tr>
                            <template x-for="(row, index) in rows" :key="index">
                                <tr>
                                    <td x-text="index + 1"></td>
                                    <td>
                                        <select x-init="initSelect2" required name="grade[]"
                                            class="selectGrade grade" :urutan="index + 1" id="">
                                            <option value="">Pilih Grade</option>
                                            @foreach ($gradeBentuk as $g)
                                                <option value="{{ $g->nm_grade }}">{{ strtoupper($g->nm_grade) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input x-model="pcs[index]" type="text" autocomplete="off"
                                            class="text-end form-control" name="pcs[]">
                                    </td>
                                    <td>
                                        <input required x-model="gr[index]" type="text" autocomplete="off"
                                            class="text-end form-control" name="gr[]">
                                    </td>
                                    <td>
                                        <input required type="text" autocomplete="off"
                                            class="form-control boxkirim" :urutan="index + 1" name="box_sp[]">
                                    </td>

                                    <td class="cek" :urutan="index + 1"></td>
                                    <td>
                                        <span @click="removeRow(index)" class="badge bg-danger pointer"><i
                                                class="fas fa-trash"></i></span>
                                    </td>
                                </tr>
                            </template>
                            <tr>
                                <td colspan="6"><button type="button" @click="rows.push({ value: '' })"
                                        class="btn btn-sm btn-primary btn-block"><i class="fas fa-plus"></i>
                                        Tambah</button></td>
                            </tr>
                            <!-- Add more rows as needed -->

                            {{-- <tr>
                                <td>Susut

                                    <input type="hidden" class="form-control" name="grade[]" value="62">
                                </td>
                                <td>
                                    <input x-model="susut.pcs" type="number" class="text-end form-control"
                                        name="pcs[]">
                                </td>
                                <td>
                                    <input x-model="susut.gr" type="number" class="text-end form-control"
                                        name="gr[]">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="box_sp[]">
                                </td>
                                <td></td>
                            </tr> --}}

                        </tbody>
                    </table>
                </div>
            </div>
            <button type="submit" class="btn btn-md btn-primary float-end">Save</button>
        </form>

        @section('scripts')
            <script>
                $('#tbl3').on('keydown', 'input[type="text"]', function(e) {
                    const $currentCell = $(this).closest('td');
                    const columnIndex = $currentCell.index();
                    const $currentRow = $currentCell.parent();

                    switch (e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            const $nextRow = $currentRow.next('tr');
                            if ($nextRow.length) {
                                $nextRow.find(`td:eq(${columnIndex}) input[type="text"]`).focus();
                            }
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            const $prevRow = $currentRow.prev('tr');
                            if ($prevRow.length) {
                                $prevRow.find(`td:eq(${columnIndex}) input[type="text"]`).focus();
                            }
                            break;
                    }
                });
            </script>
            <script>
                $(document).ready(function() {
                    $(document).on("keyup", ".boxkirim", function(e) {
                        var urutan = $(this).attr('urutan');
                        var boxkirim = $('.boxkirim[urutan="' + urutan + '"]').val();
                        var grade = $('.grade[urutan="' + urutan + '"]').val();

                        $.ajax({
                            type: "get",
                            url: "{{ route('gradingbj.cek_box_kirim') }}",
                            data: {
                                boxkirim: boxkirim,
                                grade: grade
                            },
                            success: function(response) {
                                $('.cek[urutan="' + urutan + '"]').html(response);
                            }
                        });


                    });

                    $(document).on("change", ".grade", function(e) {
                        var urutan = $(this).attr('urutan');
                        var boxkirim = $('.boxkirim[urutan="' + urutan + '"]').val();
                        var grade = $('.grade[urutan="' + urutan + '"]').val();

                        $.ajax({
                            type: "get",
                            url: "{{ route('gradingbj.cek_box_kirim') }}",
                            data: {
                                boxkirim: boxkirim,
                                grade: grade
                            },
                            success: function(response) {
                                $('.cek[urutan="' + urutan + '"]').html(response);
                            }
                        });


                    });
                });
            </script>
        @endsection

    </x-slot>
</x-theme.app>
