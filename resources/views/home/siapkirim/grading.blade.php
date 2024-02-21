<div class="row" x-data="{
    ttlBaris1: 1,
    ttlBaris2: 1,
    pcs: Array().fill(''),
    gr: Array().fill(''),
    pcs2: Array().fill(''),
    gr2: Array().fill(''),
    ttlSum: function(type) {
        const array = type === 'pcs' ? this.pcs : this.gr;
        return array.reduce((acc, value) => acc + (parseInt(value) || 0), 0);
    },
    ttlSum2: function(type) {
        const array = type === 'pcs' ? this.pcs2 : this.gr2;
        return array.reduce((acc, value) => acc + (parseInt(value) || 0), 0);
    },
    numberFormat(value) {
        return parseFloat(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).replace(/\./g, ',');
    },
    initSelect2: function() {
        $('.selectGrade').select2({
            dropdownParent: $('#grading .modal-content')
        });
    }
}" x-init="initSelect2()">
    <div class="col-lg-4">
        <h6>Partai : {{ $boxJudul->partai }}</h6>
    </div>
    <div class="col-lg-12"></div>
    <div class="col-lg-4">
        <input type="hidden" name="no_grading" value="{{ $no_grading }}">
        <h6>Box diambil dari sortir</h6>
        <br><br>
        <table class="mt-2 table table-hover table-bordered">

            <thead class="">
                <tr>
                    <th class="dhead ">No Box</th>
                    <th class="dhead ">Tipe</th>
                    <th class="dhead  text-end">Pcs Akhir</th>
                    <th class="dhead  text-end">Gr Akhir</th>
                    <th class="dhead  text-end">Rp/gram</th>
                    <th class="dhead  text-end">Ttl Rp</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $pcs_akhir = 0;
                    $gr_akhir = 0;
                    $ttl_rp = 0;
                @endphp
                @foreach ($box as $d)
                    @php
                        $pcs = $d->pcs_akhir == 0 ? $d->pcs_awal : $d->pcs_akhir;
                        $gr = $d->gr_akhir == 0 ? $d->gr_awal : $d->gr_akhir;

                        $pcs_akhir += $pcs;
                        $gr_akhir += $gr;
                        $ttl_rp += $d->ttl_rp + $d->cost_sortir;
                    @endphp
                    <tr>
                        <td>{{ $d->no_box }}</td>
                        <td>{{ $d->tipe }}</td>
                        <td align="right">{{ number_format($pcs, 0) }}</td>
                        <td align="right">{{ number_format($gr, 0) }}</td>
                        <td align="right">
                            {{ number_format(($d->ttl_rp + $d->cost_sortir) / $gr, 0) }}</td>
                        <td align="right">
                            {{ number_format($d->ttl_rp + $d->cost_sortir, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>{{ count($box) }}</th>
                    <th></th>
                    <th class="text-end">{{ number_format($pcs_akhir, 0) }}</th>
                    <th class="text-end">{{ number_format($gr_akhir, 0) }}</th>
                    <th class="text-end">{{ number_format($ttl_rp / $gr_akhir, 0) }}</th>
                    <th class="text-end">{{ number_format($ttl_rp, 0) }}</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th class="text-end" x-text="numberFormat(ttlSum('pcs') + ttlSum2('pcs'))"></th>
                    <th class="text-end" x-text="numberFormat(ttlSum('gr') + ttlSum2('gr'))"></th>
                    <th class="text-end">
                        <input type="hidden" name="ttl_gr" x-bind:value="ttlSum('gr') + ttlSum2('gr')">
                        <input type="hidden" name="ttl_rp" value="{{ round($ttl_rp) }}">
                    </th>
                    <th class="text-end"> </th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-lg-4">
        <h6 class="">Grade D </h6>
        <table class="mb-2">
            <tr>
                <td>Baris</td>
                <td>
                    <input style="width: 40%" x-model="ttlBaris1" type="text" class="form-control">
                </td>
            </tr>
        </table>
        <style>
            .input_grade {
                font-size: 12px;

            }
        </style>
        <div class="scrollable-table">
            <table class="table table-bordered">
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
                <thead>

                    <tr>
                        <th class="dhead">No</th>
                        <th class="dhead">Grade</th>
                        <th class="dhead">Pcs</th>
                        <th class="dhead">Gr</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(_, indexBaris) in Array.from({ length: ttlBaris1 <= 50 ? ttlBaris1 : 50 })"
                        :key="indexBaris">
                        <tr>
                            <td x-text="indexBaris + 1"></td>
                            <td>
                                <select autocomplete="off" :count="indexBaris + 1" name="grade[]" class="selectGrade"
                                    required x-init="initSelect2">
                                    <option value="">Pilih grade</option>
                                    @foreach ($tbGradeBentuk as $b)
                                        <option value="{{ $b->nm_grade }}">{{ $b->nm_grade }}</option>
                                    @endforeach
                                </select>
                                {{-- <input autocomplete="off" :count="indexBaris + 1" type="text"
                                    class="form-control grade input_grade" name="grade[]" required> --}}
                            </td>
                            <td>
                                <input autocomplete="off" :count="indexBaris + 1" type="text"
                                    x-model="pcs[indexBaris]" name="pcs[]"
                                    class="form-control pcs input_grade text-end" required>
                            </td>
                            <td>
                                <input autocomplete="off" :count="indexBaris + 1" type="text"name="gr[]"
                                    x-model="gr[indexBaris]" class="form-control gr input_grade text-end" required>

                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <h6>Turun Grade</h6>
        <div class="scrollable-table">

            <table class="mb-2">
                <tr>
                    <td>Baris</td>
                    <td>
                        <input style="width: 40%" x-model="ttlBaris2" type="text" class="form-control">
                    </td>
                </tr>
            </table>
            <table class="table table-bordered">
                <tr>
                    <td></td>
                    <td>
                        <h6>Total</h6>
                    </td>
                    <td class="text-end">
                        <h6 x-text="numberFormat(ttlSum2('pcs'))">0</h6>
                    </td>
                    <td class="text-end">
                        <h6 x-text="numberFormat(ttlSum2('gr'))">0</h6>
                    </td>
                </tr>
                <thead>
                    <tr>
                        <th class="dhead">No</th>
                        <th class="dhead">Grade</th>
                        <th class="dhead">Pcs</th>
                        <th class="dhead">Gr</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(_, indexBaris) in Array.from({ length: ttlBaris2 <= 50 ? ttlBaris2 : 50 })"
                        :key="indexBaris">
                        <tr>
                            <td x-text="indexBaris + 1"></td>
                            <td>
                                <select autocomplete="off" :count="indexBaris + 1" name="grade[]" class="selectGrade"
                                    required x-init="initSelect2">
                                    <option value="">Pilih grade</option>
                                    @foreach ($tbGradeTurun as $b)
                                        <option value="{{ $b->nm_grade }}">{{ $b->nm_grade }}</option>
                                    @endforeach
                                </select>
                                {{-- <input autocomplete="off" :count="indexBaris + 1" type="text"
                                    class="form-control grade input_grade" name="grade[]" required> --}}
                            </td>
                            <td>
                                <input autocomplete="off" :count="indexBaris + 1" type="text"
                                    x-model="pcs2[indexBaris]" name="pcs[]"
                                    class="form-control pcs input_grade text-end" required>
                            </td>
                            <td>
                                <input autocomplete="off" :count="indexBaris + 1" type="text"name="gr[]"
                                    x-model="gr2[indexBaris]" class="form-control gr input_grade text-end" required>

                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>
    </div>
</div>
