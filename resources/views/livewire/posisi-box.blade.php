<div x-data="{
    openModal: () => {
        const modal = new bootstrap.Modal(document.getElementById('cariBox'));
        modal.show();
    }
}">
    <a href="#" @click="openModal()" class="btn btn-sm btn-info">Cek Posisi No Box</a>

    <x-theme.modal wire:ignore.self id="cariBox" btnSave="T" title="Cek Posisi No Box" size="modal-lg">
        <input type="text" wire:model.live="cariBox" class="form-control mt-2" placeholder="Cari Posisi No Box">
        <div wire:loading class="text-center">
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

    </x-theme.modal>
</div>
