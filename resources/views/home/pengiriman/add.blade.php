<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('pengiriman.create') }}" method="post">
            @csrf
        <div class="row" x-data="{
            rows: [],
            pcs: Array().fill(''),
            gr: Array().fill(''),
            ttlSum: function(type) {
                const array = type === 'pcs' ? this.pcs : this.gr;
                return array.reduce((acc, value) => acc + (parseInt(value) || 0), 0);
            },
            numberFormat(value) {
                return parseFloat(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).replace(/\./g, ',');
            },
            removeRow: function(index) {
                this.rows.splice(index, 1);
                this.pcs.splice(index, 1);
                this.gr.splice(index, 1);
            },
        }">
          
            <hr>
            <x-theme.alert pesan="{{ session()->get('error') }}" />
            <div class="col-lg-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">Tgl</th>
                            <th class="dhead">No Grading</th>
                            <th class="dhead">Pcs</th>
                            <th class="dhead">Gr</th>
                            <th class="dhead">Cek Qc</th>
                            <th class="dhead">No Barcode</th>
                            <th class="dhead">Aksi</th>
                        </tr>
                        <template x-for="(row, index) in rows" :key="index">
                            <tr>
                                <td><input type="date" value="{{ date('Y-m-d') }}" class="form-control"></td>
                                <td><input type="text" name="no_grading[]" class="form-control"></td>
                                <td><input type="text" name="pcs[]" class="form-control"></td>
                                <td><input required type="text" name="gr[]" class="form-control"></td>
                                <td><input type="text" name="cek_qc[]" class="form-control"></td>
                                <td><input type="text" name="no_barcode[]" class="form-control"></td>
                                <td>
                                    <span @click="removeRow(index)" class="badge bg-danger pointer"><i
                                            class="fas fa-trash"></i></span>
                                </td>
                            </tr>
                        </template>
                        <tr>
                            <td colspan="7"><button type="button" @click="rows.push({ value: '' })"
                                    class="btn btn-sm btn-primary btn-block"><i class="fas fa-plus"></i>
                                    Tambah</button></td>
                        </tr>
                    </thead>
                </table>
                <button class="btn btn-primary float-end" type="submit">Save</button>
            </div>
        </div>
        </form>
    </x-slot>
</x-theme.app>
