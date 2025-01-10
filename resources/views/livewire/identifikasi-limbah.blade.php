<div>
    <table class="table table-bordered border-dark">
        <thead>
            <tr>
                <th class="dhead text-center">No</th>
                <th class="dhead">Area</th>
                <th class="dhead">Limbah / Polusi</th>
                <th width="250" class="dhead">Metode Pembuangan</th>
                <th class="dhead">Keterangan</th>
                <th class="dhead">Admin</th>
                <th width="110" class="dhead">Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- tr tambah --}}
            <tr>
                <form wire:submit.prevent="store">
                    <td></td>
                    <td>
                        <input required wire:model='formLimbah.area' type="text" placeholder="area"
                            class="form-control form-control-sm">
                    </td>
                    <td>
                        <input required type="text" wire:model='formLimbah.limbah' placeholder="limbah / populasi"
                            class="form-control form-control-sm">
                    </td>
                    <td>
                        <input required type="text" wire:model='formLimbah.metode' placeholder="metode"
                            class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="text" wire:model='formLimbah.ket' placeholder="keterangan"
                            class="form-control form-control-sm">
                    </td>
                    <td colspan="2">
                        <button type="submit" class="btn btn-xs btn-block btn-info">Simpan</button>
                    </td>
                </form>
            </tr>

            @foreach ($limbah as $i => $d)
                <tr x-data="editData({{ $d->id }}, '{{ $d->area }}', '{{ $d->limbah }}', '{{ $d->metode }}', '{{ $d->ket }}')">
                    <td align="center">{{ $loop->iteration }}</td>
                    <td>
                        <span x-show="!editInput">{{ $d->area }}</span>
                        <input required x-show="editInput" type="text" class="form-control form-control-sm"
                            x-model="editedData.area" />
                    </td>

                    <td>
                        <span x-show="!editInput">{{ $d->limbah }}</span>
                        <input required x-show="editInput" type="text" class="form-control form-control-sm"
                            x-model="editedData.limbah" />
                    </td>

                    <td class="text-wrap">
                        <span x-show="!editInput">{{ $d->metode }}</span>
                        <input required x-show="editInput" type="text" class="form-control form-control-sm"
                            x-model="editedData.metode" />
                    </td>

                    <td>
                        <span x-show="!editInput">{{ $d->ket }}</span>
                        <input x-show="editInput" type="text" class="form-control form-control-sm"
                            x-model="editedData.ket" />
                    </td>

                    <td>{{ $d->admin }}</td>

                    <td>
                        <a @click.prevent="toggleEdit" href="#" class="btn btn-primary btn-xs"
                            x-text="editInput ? 'Batal' : 'Edit'"></a>
                        <button x-show="editInput" @click="saveEdit" class="btn btn-success btn-xs">Save</button>

                        <a x-show="!editInput" wire:confirm='"Yakin ingin menghapus data ini?"' wire:click="destroy({{ $d->id }})"
                            href="#" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function editData(id, area, limbah, metode, ket) {
        return {
            editInput: false,
            editedData: {
                area: area,
                limbah: limbah,
                metode: metode,
                ket: ket
            },
            toggleEdit() {
                this.editInput = !this.editInput;
            },
            saveEdit() {
                // Mengirimkan data ke Livewire untuk disimpan
                this.$wire.saveEdit(id, this.editedData).then(() => {
                    this.editInput = false; // Menutup form edit setelah berhasil disimpan
                });
            }
        }
    }
</script>