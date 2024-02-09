<div>
    <div x-data="{
        rows: []
    }">
        <template x-for="(row, index) in rows" :key="index">
            <div class="row">
                {{ $slot }}
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="">Aksi</label><br>
                        <button class="btn btn-danger btn-sm" type="button" @click="rows.splice(index, 1)"><i
                                class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </template>
        <button type="button" @click="rows.push({ value: '' })" class="btn btn-primary btn-sm">Tambah Row</button>
    </div>
</div>
