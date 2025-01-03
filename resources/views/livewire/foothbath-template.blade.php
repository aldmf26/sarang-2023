<div>
    <table class="table table-bordered border-primary">
        <thead>
            <tr>
                <th class="dhead">Item Pembersihan</th>
                <th class="dhead">Frekuensi</th>
                <th class="dhead">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentItem = null; // Untuk melacak item saat ini
                $rowCount = 0; // Untuk menghitung jumlah frekuensi per item
            @endphp
            @foreach ($foothbathTemplate as $key => $row)
                @php
                    // Hitung jumlah baris untuk item yang sama
                    if ($currentItem !== $row->item) {
                        $currentItem = $row->item;
                        $rowCount = $foothbathTemplate->where('item', $row->item)->count();
                    } else {
                        $rowCount = 0;
                    }
                @endphp
                <tr>
                    @if ($rowCount > 0)
                        <td rowspan="{{ $rowCount }}">{{ $row->item }}</td>
                    @endif
                    <td>{{ $row->frekuensi }}</td>
                    <td align="center">
                        <span Wire:confirm="Apakah anda yakin ingin menghapus data ini?" wire:click.prevent="delete({{ $row->id }})" class="pointer badge bg-danger">
                            <i class="fas fa-trash"></i>
                        </span>
                    </td>
                </tr>
            @endforeach
            <form wire:submit.prevent="store">
                @foreach ($items as $index => $item)
                    <tr>
                        <td>
                            <input type="text" wire:model.live="items.{{ $index }}.name" class="form-control"
                                placeholder="Item pembersihan">
                        </td>
                        <td>
                            <input type="text" wire:model.live="items.{{ $index }}.frekuensi"
                                class="form-control" placeholder="Frekuensi">
                        </td>
                        <td>
                            <span wire:click="removeRow({{ $index }})" class="pointer badge bg-danger">
                                <i class="fas fa-trash"></i>
                            </span>
                        </td>

                    </tr>
                @endforeach

                <tr>
                    <td><button wire:click="addRow" type="button" class="btn-block btn btn-sm btn-primary">Tambah Baris
                            Baru</button>
                    </td>
                    <td class="align-middle">
                        <button x-show="$wire.items.length" :disabled="$wire.disabled" class="btn btn-sm btn-success"
                            type="submit">Simpan</button>

                        <div wire:loading wire:target="store,delete,addRow,removeRow"
                            class="ms-2 spinner-border spinner-border-sm text-primary" role="status"
                            bis_skin_checked="1">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                    <td></td>
                </tr>
            </form>
        </tbody>
    </table>
</div>
