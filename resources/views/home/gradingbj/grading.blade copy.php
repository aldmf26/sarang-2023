<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
            <div>
                <button type="submit" class="btn btn-sm btn-primary">Save</button>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <section x-data="{
            barisGrade: 1,
            barisTurunGrade: 1,
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
            initSelect2: function() {
                $('.selectGrade').select2();
            },
            numberFormat(value) {
                return parseFloat(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).replace(/\./g, ',');
            },
        }" x-init="initSelect2()">
            <div class="row">

                <div class="col-lg-12">
                    <h6>Dari Sortir</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th class="dhead">Pgws</th>
                            <th class="dhead">No Invoice</th>
                            <th class="dhead">Tanggal</th>
                            <th class="dhead">No Box</th>
                            <th class="dhead text-end">Pcs Awal</th>
                            <th class="dhead text-end">Gr Awal</th>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" readonly value="{{ $user }}" class="form-control">
                            </td>
                            <td>
                                <input type="text" readonly value="{{ $no_invoice }}" class="form-control">
                            </td>
                            <td>
                                <input type="date" value="{{ date('Y-m-d') }}" class="form-control">
                            </td>
                            <td>
                                <input type="text" readonly value="{{ $getFormulir->no_box }}" class="form-control">
                            </td>
                            <td>
                                <input type="text" readonly value="{{ number_format($getFormulir->pcs_awal, 0) }}"
                                    class="text-end form-control">
                            </td>
                            <td>
                                <input type="text" readonly value="{{ number_format($getFormulir->gr_awal, 0) }}"
                                    class="text-end form-control">
                            </td>
                            
                        </tr>
                    </table>
                </div>
               
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-6">
                    <h6>Grade</h6>
                    <div class="form-group d-flex align-items-center gap-1">
                        <label for="">Baris</label>
                        <input x-model.number="barisGrade" type="number" style="width: 20%" name="example"
                            class="form-control">
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="dhead">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
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
                            <template x-for="(i, d) in barisGrade" :key="i">
                                <tr>
                                    <td>
                                        <select x-init="initSelect2" name="gradeBentuk[]" class="selectGrade"
                                            id="">
                                            <option value="">Pilih Grade</option>
                                            @foreach ($gradeBentuk as $d)
                                                <option value="{{ $d->id_grade }}">{{ strtoupper($d->nm_grade) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input x-model="pcs[d]" type="number" class="text-end form-control" name="pcsBentuk[]">
                                    </td>
                                    <td>
                                        <input x-model="gr[d]" type="number" class="text-end form-control" name="grBentuk[]">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
                    <h6>Turun Grade</h6>
                    <div class="form-group d-flex align-items-center gap-1">
                        <label for="">Baris</label>
                        <input x-model.number="barisTurunGrade" type="number" style="width: 20%" name="example"
                            class="form-control">
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="dhead">Turun Grade</th>
                                <th class="dhead">Pcs</th>
                                <th class="dhead">Gr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
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
                            <template x-for="(i, d) in barisTurunGrade" :key="i">
                                <tr>
                                    <td>
                                        <select x-init="initSelect2" name="gradeTurun[]" class="selectGrade"
                                            id="">
                                            <option value="">Pilih Grade</option>
                                            @foreach ($gradeTurun as $d)
                                                <option value="{{ $d->id_grade }}">{{ strtoupper($d->nm_grade) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input x-model="pcs2[d]" type="number" class="form-control" name="pcsTurun[]">
                                    </td>
                                    <td>
                                        <input x-model="gr2[d]" type="number" class="form-control" name="grTurun[]">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="d-flex float-end">
                <div>
                    <table class="table table-lg">
                        <tr>
                            <th><h6>Total Dari Sortir</h6></th>
                            <th>Pcs : <h6>{{ number_format($getFormulir->pcs_awal, 0) }}</h6></th>
                            <th>Gr : <h6>{{ number_format($getFormulir->gr_awal, 0) }}</h6></th>
                        </tr>
                        <tr>
                            <th><h6>Total di Grading</h6></th>
                            <th>Pcs : <h6 x-text="numberFormat(ttlSum2('pcs') + ttlSum('pcs'))"></h6></th>
                            <th>Gr : <h6 x-text="numberFormat(ttlSum2('gr') + ttlSum('gr'))"></h6></th>
                        </tr>
                       
                    </table>
          
                </div>
            </div>
        </section>
    </x-slot>
</x-theme.app>
