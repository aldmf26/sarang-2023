<div class="row" x-data="{
    cek: {{ json_encode($formulir->pluck('no_box')) }},
    selectedItem: [],
    tambah(no_box, pcs, gr) {
        const selectedItem = this.selectedItem

        const index = selectedItem.findIndex(item => item.no_box === no_box);
        if (index === -1) {
            selectedItem.push({
                no_box: no_box,
                pcs: parseFloat(pcs),
                gr: parseFloat(gr),
            });
        } else {
            this.selectedItem.splice(index, 1);
        }

    },
    cancel(no_box) {
        const index = this.cek.indexOf(no_box);
        if (index !== -1) {
            this.cek.splice(index, 1);
        }
    }
}">
<input class="d-none" name="no_box[]" type="text" :value="cek">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Tgl</label>
            <input readonly value="{{ $formulir[0]->tanggal }}" type="date" name="tgl"
                class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <label for="">Pgws Penerima</label>
        <input readonly value="{{ auth()->user()->find($formulir[0]->id_penerima)->name }}" type="text"
        class="form-control">
        <input  value="{{ auth()->user()->find($formulir[0]->id_penerima)->id }}" type="hidden" name="id_penerima"
        >
    </div>
    <div class="col-lg-3">
        <label for="">No Invoice</label>
        <input readonly value="{{ $formulir[0]->no_invoice }}" type="text" name="no_invoice"
        class="form-control">
    </div>
    <div class="col-lg-6">
        <div class="d-flex justify-content-between">
            <div>
                <h6>Box Selesai Cabut</h6>
            </div>
            <div>
                <input style="min-height: 2% !important" type="text" id="inputTbl" placeholder="cari" class="mb-2 form-control form-control-sm">
            </div>
        </div>
        <div style="overflow-y: scroll; height: 300px">
            <table class="table table-bordered table-striped table-hover" id="tbl1">
                <thead>
                    <tr>
                        <th class="dheadstock">No Box</th>
                        <th class="dheadstock text-end">Pcs</th>
                        <th class="dheadstock text-end">Gr</th>
                        <th class="dheadstock text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cabutSelesai as $d)
                        <tr>
                            <td>{{ $d->no_box }}</td>
                            <td align="right">{{ $d->pcs }}</td>
                            <td align="right">{{ $d->gr }}</td>
                            <td align="center">
                                <input type="checkbox"
                                    @change="tambah({{ $d->no_box }}, {{ $d->pcs }}, {{ $d->gr }})"
                                    value="{{ $d->no_box }}" x-model="cek">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <h6>Terpilih</h6>
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th class="dhead">No Box</th>
                    <th class="dhead text-end">Pcs</th>
                    <th class="dhead text-end">Gr</th>
                    <th class="dhead text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach ($formulir as $d)
                    <tr>
                        <td>{{ $d->no_box }}</td>
                        <td align="right">{{ $d->pcs_awal }}</td>
                        <td align="right">{{ $d->gr_awal }}</td>
                        <td align="center">
                            <input type="checkbox"
                               @change="cancel({{ $d->no_box }})"
                               value="{{ $d->no_box }}"
                               x-model="cek">
                        </td>
                    </tr>
                @endforeach
                <template x-for="d in selectedItem">
                    <tr>
                        <td x-text="d.no_box"></td>
                        <td align="right" x-text="d.pcs"></td>
                        <td align="right" x-text="d.gr"></td>
                        <td></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    
</div>

