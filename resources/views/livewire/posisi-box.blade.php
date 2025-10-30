<div x-data="{
    openModal: () => {
        const modal = new bootstrap.Modal(document.getElementById('cariBox'));
        modal.show();
    },
    posisi: false,
    anak: false
}">
    <a href="#" @click="openModal()" class="btn btn-sm btn-info">Cek Posisi No Box / Ganti Nama Anak</a>

    <x-theme.modal wire:ignore.self id="cariBox" btnSave="T" title="Cek Posisi No Box" size="modal-lg">

        <div class="d-flex gap-1">
            <button type="button" @click="posisi = !posisi; anak = false" class="btn btn-sm btn-primary">Cek Posisi No
                Box</button>
            <button type="button" @click="anak = !anak; posisi = false" class="btn btn-sm btn-primary">Ganti Nama
                Anak</button>
        </div>

        <div x-show='posisi'>
            <input type="text" wire:model.change="cariBox" class="form-control mt-2"
                placeholder="Cari Posisi No Box">
            <div wire:loading wire:target='cariBox' class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            @if ($dataBox)
                <table class="table table-striped table-dark table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th>No Box</th>
                            <th>Pemberi</th>
                            <th>Penerima</th>
                            <th>Pcs Awal</th>
                            <th>Gr Awal</th>
                            <th>Tgl</th>
                            <th>Posisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataBox as $d)
                            <tr>
                                <td>{{ $d->no_invoice }}</td>
                                <td>{{ $d->no_box }}</td>
                                <td>{{ $d->pemberi->name }}</td>
                                <td>{{ $d->penerima->name }}</td>
                                <td>{{ $d->pcs_awal }}</td>
                                <td>{{ $d->gr_awal }}</td>
                                <td>{{ $d->tanggal }}</td>
                                <td>{{ $d->kategori }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div x-show="anak" class="row">
            <div class="form-group col-6">
                <label for="">Divisi</label>
                <select class="form-select" wire:model.live="selectedDivisi" id="">
                    <option value="">Pilih Divisi</option>
                    <option value="cabut">Cabut</option>
                    <option value="cetak">Cetak</option>
                    <option value="sortir">Sortir</option>
                </select>
            </div>
            <div class="form-group col-6">
                <label for="">Pengawas</label>
                <select class="form-select" wire:model.live="selectedPengawas" id="">
                    <option value="">Pilih Pengawas</option>
                    @foreach ($pengawas as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-6">
                <label for="">No Box</label>
                <input type="text" wire:model="noBox" class="form-control">
            </div>
            @if ($anak)
                <div wire:transition class="form-group col-6">
                    <label for="">Nama</label>
                    <select class="form-select" wire:model.live="selectedNama" id="">
                        <option value="">Pilih Nama</option>
                        @foreach ($anak as $a)
                            <option value="{{ $a->id_anak }}">{{ $a->nama }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-12">
                <button wire:click='updateAnak' class="btn btn-sm btn-success btn-block" type="button">Simpan</button>
            </div>
        </div>

    </x-theme.modal>
</div>
