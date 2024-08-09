<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
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
        }" x-init="initSelect2()" action="{{ route('gradingbj.create_partai') }}"
            method="post">
            @csrf
            <div class="row">
                <div class="col-lg-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">Pengawas</th>
                                {{-- <th class="dhead">No Nota</th> --}}
                                <th class="dhead">Tgl</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" readonly value="{{ $user }}" name="pengawas"
                                        class="form-control" required>
                                    <input type="hidden" name="nm_partai" value="{{ $nm_partai }}">
                                </td>
                                {{-- <td>
                                    <input type="text" readonly value="{{ $no_nota }}" name="no_nota"
                                        class="form-control" required>
                                </td> --}}
                                <td>
                                    <input readonly type="date" value="{{ date('Y-m-d') }}" name="tgl"
                                        class="form-control" required>
                                </td>

                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <h6>Box Dipilih <span class="text-success">Partai : {{ $nm_partai }}</span></h6>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="dhead">No Box</th>
                                <th class="dhead text-center">Tipe</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
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
                                    $ttlGr = array_sum(array_column($getFormulir, 'gr_awal'))
                                @endphp
                                <th class="text-end">
                                    <h6>
                                        <input type="hidden" name="tipe" value="{{$getFormulir[0]->tipe}}">
                                        <input type="hidden" name="ttlPcs" value="{{$ttlPcs}}">
                                        {{ $ttlPcs }}
                                    </h6>
                                </th>
                                <th class="text-end">
                                    <h6>
                                        <input type="hidden" name="ttlGr" value="{{$ttlGr}}">
                                        {{ $ttlGr }}
                                    </h6>
                                </th>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach ($getFormulir as $d)
                                <tr class="pointer">
                                    <td>{{ $d->no_box }} <input type="hidden" name="no_box[]"
                                            value="{{ $d->no_box }}"></td>
                                    <td align="center">{{ $d->tipe }}</td>
                                    <td align="right">{{ $d->pcs_awal }}</td>
                                    <td align="right">{{ $d->gr_awal }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-8">
                    <h6>Hasil Grading</h6>
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
                                                <option value="{{ $g->nm_grade }}">{{ strtoupper($g->nm_grade) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input x-model="pcs[index]" type="number" class="text-end form-control"
                                            name="pcs[]">
                                    </td>
                                    <td>
                                        <input required x-model="gr[index]" type="number" class="text-end form-control"
                                            name="gr[]">
                                    </td>
                                    <td>
                                        <input required type="text" class="form-control" name="box_sp[]">
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
            </div>
            <button type="submit" class="btn btn-md btn-primary float-end">Save</button>
        </form>
    </x-slot>
</x-theme.app>
