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
            <div class="row">
                <div class="col-lg-7">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="dhead">Pengawas</th>
                                <th class="dhead" width="150">No Nota</th>
                                <th class="dhead">Tgl</th>
                                <th class="dhead">Bulan dibayar</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" readonly value="{{ $admin }}" name="pengawas"
                                        class="form-control" required>
                                    <input type="hidden" name="nm_partai" value="{{ $nm_partai }}">
                                </td>
                                <td>
                                    <input type="text" readonly value="{{ $no_invoice }}" name="no_nota"
                                        class="form-control" required>
                                </td>
                                <td>
                                    <input readonly type="date" value="{{ $tgl }}" name="tgl"
                                        class="form-control" required>
                                </td>
                                <td>
                                    <input readonly type="text" value="{{ $getFormulir[0]->bulan }}" name="bulans"
                                        class="form-control" required>
                                    <input readonly type="hidden" value="{{ date('Y') }}" name="tahuns"
                                        class="form-control" required>

                                </td>

                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="row" x-data="{
                rows: {{ json_encode($getFormulir) }},
                selectedRowIndex: null,
                isDisabled: false,
                pcs: {{ json_encode(array_column($getFormulir, 'pcs')) }},
                gr: {{ json_encode(array_column($getFormulir, 'gr')) }},
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

                                <th class="text-end">
                                    <h6>
                                        {{ array_sum(array_column($getBox, 'pcs')) }}
                                    </h6>
                                </th>
                                <th class="text-end">
                                    <h6>{{ array_sum(array_column($getBox, 'gr')) }}</h6>
                                </th>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach ($getBox as $d)
                                <tr class="pointer">
                                    <td>{{ $d->no_box }} <input type="hidden" name="no_box[]"
                                            value="{{ $d->no_box }}"></td>
                                    <td align="center">{{ $d->tipe }}</td>
                                    <td align="right">{{ $d->pcs }}</td>
                                    <td align="right">{{ $d->gr }}</td>

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
                                <th class="dhead">#</th>
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
                                    <td x-text="index + 1"></td>
                                    <td>
                                        <select  x-init="initSelect2"
                                            required name="grade[]" class="selectGrade" id="">
                                            <option value="">Pilih Grade</option>
                                            @foreach ($gradeBentuk as $g)
                                                <option :selected="row.grade === '{{ $g->nm_grade }}'"
                                                    value="{{ $g->nm_grade }}">{{ strtoupper($g->nm_grade) }}
                                                </option>
                                            @endforeach
                                        </select>
                                      
                                    </td>
                                    <td>
                                        <input :readonly="row.sudah_kirim === 'Y'" onclick="select()"
                                            x-model="pcs[index]" type="text" class="text-end form-control"
                                            name="pcs[]">
                                    </td>
                                    <td>
                                        <input :readonly="row.sudah_kirim === 'Y'" onclick="select()"
                                            x-model="gr[index]" required type="text" class="text-end form-control"
                                            name="gr[]">
                                    </td>
                                    <td>
                                        <input :readonly="row.sudah_kirim === 'Y'" onclick="select()"
                                            :value="row.box_pengiriman" required type="text"
                                            class="form-control text-end" name="box_sp[]">
                                    </td>
                                    <td x-show="row.sudah_kirim === 'T'">
                                        <input type="hidden" :value="row.sudah_kirim" name="sudah_kirim[]">
                                        <input type="hidden" :value="row.formulir" name="formulir[]">
                                        <input type="hidden" :value="row.bulan" name="bulan[]">
                                        <input type="hidden" :value="row.tahun" name="tahun[]">
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

            <button x-show="!isDisabled" @click="isDisabled = true" type="submit"
                class="btn btn-md btn-primary float-end">Save</button>
        </form>
        @section('scripts')
            <script>
                clickSelectInput('form-control')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
