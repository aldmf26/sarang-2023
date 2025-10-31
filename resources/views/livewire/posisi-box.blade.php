<div x-data="{
    openModal: () => {
        const modal = new bootstrap.Modal(document.getElementById('cariBox'));
        modal.show();
    },
    posisi: false,
    anak: false,
    grading: false
}">
    <a href="#" @click="openModal()" class="btn btn-sm btn-info">Cek Posisi No Box / Ganti Nama Anak / Edit Grading
        Kode</a>

    <x-theme.modal wire:ignore.self id="cariBox" btnSave="T" title="Cek Posisi No Box" size="modal-lg">

        <div class="d-flex gap-1">
            <button type="button" @click="posisi = !posisi; anak = false; grading = false"
                class="btn btn-sm btn-primary">Cek Posisi No
                Box</button>
            <button type="button" @click="anak = !anak; posisi = false; grading = false"
                class="btn btn-sm btn-primary">Ganti Nama
                Anak</button>
            <button type="button" @click="grading = !grading; posisi = false; anak = false"
                class="btn btn-sm btn-primary">Grading edit kode</button>
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

        <div x-show="grading">
            <div class="row">
                <div class="form-group col-4">
                    <label for="">No Invoice</label>
                    <input type="text" wire:model="noInvoice" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="">Kode Sebelumnya</label>
                    <input type="text" wire:model="kodeSebelumnya" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="">Kode Sebelumnya</label>
                    <input type="text" wire:model.change="grGrading" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="">Kode Setelahnya</label>
                    <input type="text" wire:model="kodeSesudahnya" class="form-control">
                </div>
                @if ($dataGrading)
                    <div class="col-12">
                        <table class="table table-striped table-dark table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Nama Partai</th>
                                    <th>No Invoice</th>
                                    <th>Box Pengiriman</th>
                                    <th>Grade</th>
                                    <th>Tipe</th>
                                    <th>Pcs</th>
                                    <th>Gr</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $dataGrading->nm_partai }}</td>
                                    <td>{{ $dataGrading->no_invoice }}</td>
                                    <td>{{ $dataGrading->box_pengiriman }}</td>
                                    <td>{{ $dataGrading->grade }}</td>
                                    <td>{{ $dataGrading->tipe }}</td>
                                    <td>{{ $dataGrading->pcs }}</td>
                                    <td>{{ $dataGrading->gr }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="col-12">
                    <button wire:click='updateGrading' class="btn btn-sm btn-success btn-block"
                        type="button">Simpan</button>
                </div>
            </div>
        </div>

    </x-theme.modal>
</div>
