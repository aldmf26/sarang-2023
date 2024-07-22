<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>
            <div>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('gradingbj.createUlang') }}" method="post">
            @csrf
            <h6 class="fw-bold">Pengawas : {{ $user }}</h6>
            @foreach ($getFormulir as $d)
               
                <section class="row" x-data="{
                    rows: {{json_encode($getBox) }},
                    selectedRowIndex: null,
                    pcs: {{ json_encode(array_column($getBox, 'pcs')) }},
                    gr: {{ json_encode(array_column($getBox, 'gr')) }},
                    ttlSum: function(type) {
                        const array = type === 'pcs' ? this.pcs : this.gr;
                        return array.reduce((acc, value) => acc + (parseInt(value) || 0), 0);
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
                }" x-init="initSelect2()">
                    <div class="col-md-5">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="dhead">No Box</th>
                                    <th class="dhead">Tipe</th>
                                    <th class="dhead text-end">Pcs</th>
                                    <th class="dhead text-end">Gr</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="h6">{{ $d->no_box }}</th>
                                    <th class="h6">{{ $d->tipe }}</th>
                                    <th class="text-end h6">{{ $d->pcs_awal }}</th>
                                    <th class="text-end h6">{{ $d->gr_awal }}</th>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-7">
                        
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="dhead">Grade</th>
                                    <th class="dhead text-end" width="200">Pcs</th>
                                    <th class="dhead text-end" width="200">Gr</th>
                                    <th class="dhead " width="200">Box Sp</th>
                                    <th class="dhead">Aksi</th>
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
                               
                                <template x-for="(row, index) in rows" :key="index">
                                    <tr>
                                        <td>
                                            <select x-init="initSelect2" required name="grade[]" class="selectGrade"
                                                id="">
                                                <option value="">Pilih Grade</option>
                                                @foreach ($gradeBentuk as $g)
                                                    <option :selected="row.id_grade == {{ $g->id_grade }}" value="{{ $g->id_grade }}">{{ strtoupper($g->nm_grade) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="hidden" name="no_box_sortir[]" value="{{ $d->no_box }}">
                                            <input  x-model="pcs[index]" type="number" class="text-end form-control"
                                                name="pcs[]">
                                        </td>
                                        <td>
                                            <input  x-model="gr[index]" required  type="number" class="text-end form-control"
                                                name="gr[]">
                                        </td>
                                        <td>
                                            <input :value="row.no_box_grading" required type="text" class="form-control" name="box_sp[]">
                                        </td>
                                        <td>
                                            <span @click="removeRow(index)" class="badge bg-danger pointer"><i
                                                    class="fas fa-trash"></i></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr>
                                    <td colspan="5"><button type="button" @click="rows.push({ value: '' })"
                                            class="btn btn-sm btn-primary btn-block"><i class="fas fa-plus"></i>
                                            Tambah</button></td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </section>
                <hr>
            @endforeach
            <button type="submit" class="btn btn-md btn-primary float-end">Save</button>
        </form>
    </x-slot>
</x-theme.app>
