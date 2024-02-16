<div x-data="{
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
    }
}" class="tab-pane fade show {{ $form == 'dForm' ? 'active' : '' }}" id="{{ $form }}"
    role="tabpanel" aria-labelledby="sum-tab">
    <div class="row mb-1">
        <div class="col-lg-2 d-flex align-items-center">
            <h6>Form Input {{ $form == 'dForm' ? 'D' : 'V' }}</h6>
        </div>
        <div class="col-lg-4 text-end">
            <h6>Ttl Pcs : <span x-text="ttlSum('pcs') + ttlSum2('pcs')">0</span></h6>
            <h6>Ttl Gr : <span x-text="ttlSum('gr') + ttlSum2('gr')">0</span></h6>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-lg-4">
            <input type="hidden" name="no_grading" value="{{ $no_grading }}">
            <h6>Box diambil dari cetak</h6>
            <br><br>
            <table class="mt-2 table table-hover table-bordered">

                <thead class="">
                    <tr>
                        <th class="dhead ">No Box</th>
                        <th class="dhead  text-end">Pcs Akhir</th>
                        <th class="dhead  text-end">Gr Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $pcs_akhir = 0;
                        $gr_akhir = 0;
                    @endphp
                    @foreach ($box as $d)
                        @php
                            $pcs = $d->pcs_akhir == 0 ?  $d->pcs_awal : $d->pcs_akhir;
                            $gr = $d->gr_akhir == 0 ?  $d->gr_awal : $d->gr_akhir;

                            $pcs_akhir += $pcs;
                            $gr_akhir += $gr;
                        @endphp
                        <tr>
                            <td>{{ $d->no_box }}</td>
                            <td align="right">{{ number_format($pcs, 0) }}</td>
                            <td align="right">{{ number_format($gr, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>{{ count($box) }}</th>
                        <th class="text-end">{{ number_format($pcs_akhir, 0) }}</th>
                        <th class="text-end">{{ number_format($gr_akhir, 0) }}</th>
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
                .scrollable-table {
                    max-height: 300px;
                    /* Atur tinggi maksimum tabel */
                    overflow-y: auto;
                    /* Tampilkan scrollbar vertikal jika terlalu banyak baris */
                    overflow-x: hidden;
                    /* Sembunyikan scrollbar horizontal */
                }

                /* Optional: Atur lebar maksimum tabel jika ingin mengaktifkan scrollbar horizontal */
                .scrollable-table table {
                    max-width: 100%;
                }
            </style>
            <div class="scrollable-table">
                <table class="table">
                    <tr>
                        <td></td>
                        <td>
                            <h6>Total</h6>
                        </td>
                        <td>
                            <h6 x-text="ttlSum('pcs')">0</h6>
                        </td>
                        <td>
                            <h6 x-text="ttlSum('gr')">0</h6>
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
                                    <input autocomplete="off" :count="indexBaris + 1" type="text"
                                        class="form-control grade" name="grade[]">
                                </td>
                                <td>
                                    <input autocomplete="off" :count="indexBaris + 1" type="text"
                                        x-model="pcs[indexBaris]" name="pcs[]" class="form-control pcs">
                                </td>
                                <td>
                                    <input autocomplete="off" :count="indexBaris + 1" type="text"name="gr[]"
                                        x-model="gr[indexBaris]" class="form-control gr">

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
                <table class="table">
                    <tr>
                        <td></td>
                        <td>
                            <h6>Total</h6>
                        </td>
                        <td>
                            <h6 x-text="ttlSum2('pcs')">0</h6>
                        </td>
                        <td>
                            <h6 x-text="ttlSum2('gr')">0</h6>
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
                                    <input autocomplete="off" :count="indexBaris + 1" type="text"
                                        class="form-control grade" name="grade[]">
                                </td>
                                <td>
                                    <input autocomplete="off" :count="indexBaris + 1" type="text"
                                        x-model="pcs2[indexBaris]" name="pcs[]" class="form-control pcs">
                                </td>
                                <td>
                                    <input autocomplete="off" :count="indexBaris + 1" type="text"name="gr[]"
                                        x-model="gr2[indexBaris]" class="form-control gr">

                                </td>
                            </tr>
                        </template>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
