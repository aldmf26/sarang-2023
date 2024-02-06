<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Tanggal Packing List</label>
            <input type="date" name="tgl" value="{{ date('Y-m-d') }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Nama Packing List</label>
            <input type="text" name="nm_packing" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Pgws Cek</label>
            <input type="text" name="pgws_cek" class="form-control">
        </div>
    </div>
</div>
<hr>
<div class="row mt-3">
    <div class="col-lg-6 mb-2 ">
        <table>
            <td>Pencarian :</td>
            <td><input type="text" id="pencarianTbh" class="form-control float-end"></td>
        </table>
    </div>
</div>
<div class="row" x-data="{
    selectedItems: [],
    addToSelection: function(grade, pcs, gram) {
        let selectedItems = this.selectedItems;
        let existingItem = selectedItems.find(item => item.grade === grade);
        if (existingItem) {
            // If the grade already exists, update the existing item
            existingItem.box += 1;
            existingItem.pcs += pcs;
            existingItem.gram += gram;
        } else {
            // If the grade doesn't exist, add a new item
            selectedItems.push({
                grade: grade,
                box: 1,
                pcs: pcs,
                gram: gram
            });
        }
    },
    removeFromSelection: function(grade) {
        this.selectedItems = this.selectedItems.filter(item => item.grade !== grade);
    },
    incrementQuantity: function(grade) {
        let selectedItem = this.selectedItems.find(item => item.grade === grade);
        if (selectedItem) {
            selectedItem.box += 1;
        }
    },
    decrementQuantity: function(grade) {
        let selectedItem = this.selectedItems.find(item => item.grade === grade);
        if (selectedItem && selectedItem.box > 0) {
            selectedItem.box -= 1;
        }
    }
}">
    <div class="col-lg-5">

        <table class="table table-stripped table-hover" id="tbl-aldi">
            <thead>
                <tr>
                    <th class="dhead">No Box Cfm</th>
                    <th class="dhead">Grade</th>
                    <th class="dhead">Pcs</th>
                    <th class="dhead">Gram</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($pengiriman as $i => $d)
                    <tr style="cursor: pointer" data-id="{{ $d->id_pengiriman }}"
                        @click="addToSelection('{{ $d->grade }}', {{ $d->pcs }}, {{ $d->gr }})">
                        <td>{{ $d->no_box }}</td>
                        <td>{{ $d->grade }}</td>
                        <td>{{ $d->pcs }}</td>
                        <td>{{ $d->gr }}</td>
                        <td align="center">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-7">
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th class="dhead">Grade</th>
                    <th class="dhead">Box</th>
                    <th class="dhead">Pcs</th>
                    <th class="dhead">Gram</th>
                    <th class="dhead">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="item in selectedItems" :key="item.grade">
                    <tr>
                        <td x-text="item.grade"></td>
                        <td x-text="item.box"></td>
                        <td x-text="item.pcs"></td>
                        <td x-text="item.gram"></td>
                        <td>
                            {{-- <button class="btn btn-sm btn-danger" @click="decrementQuantity(item.grade)"><i class="fas fa-minus"></i></button> --}}
                            <button class="btn btn-sm btn-danger" @click="removeFromSelection(item.grade)"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>



</div>
