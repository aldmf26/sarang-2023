<table class="table" id="table1">
    <thead>
        <tr>
            <th width="5">#</th>
            <th>Kelas</th>
            <th>Tipe</th>
            <th class="text-end">Gr</th>
            <th class="text-end">Rupiah</th>
            <th>Lokasi</th>
            <th width="20%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $d->kelas }}</td>
                <td>{{ $d->tipe }}</td>
                <td class="text-end">{{ $d->gr }}</td>
                <td class="text-end">{{ number_format($d->rupiah, 0) }}</td>
                <td>{{ strtoupper($d->lokasi) }}</td>
                <td>

                    <x-theme.button modal="Y" idModal="delete" data="no_nota={{ $d->id_kelas }}_{{ $routeRemove }}"
                        icon="fa-trash" addClass="float-end delete_nota" teks="" variant="danger" />
                    <x-theme.button modal="Y" idModal="edit{{ $d->id_kelas }}" icon="fa-pen"
                        addClass="me-1 float-end edit-btn" teks="" data="id_kelas={{ $d->id_kelas }}" />
                </td>
            </tr>
        @endforeach
    </tbody>

</table>

<form action="{{ route('kelas.create') }}" method="post">
    @csrf
    <input type="hidden" name="routeRemove" value="{{ $routeRemove }}">
    <x-theme.modal title="Tambah Paket Cabut" idModal="tambah">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead">Kelas</th>
                    <th class="dhead">Gr</th>
                    <th class="dhead">Rupiah</th>
                    <th class="dhead">Lokasi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input name="kelas" type="text" class="form-control"></td>
                    <td><input name="gr" type="text" class="form-control"></td>
                    <td><input name="rupiah" type="text" class="form-control"></td>
                    <td>
                        <select name="lokasi" id="" class="form-control">
                            <option value="">- Pilih Lokasi -</option>
                            @foreach ($lokasi as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </x-theme.modal>
</form>

@foreach ($datas as $s)
    <form action="{{ route('kelas.update') }}" method="post">
        @csrf
        <input type="hidden" name="routeRemove" value="{{ $routeRemove }}">
        <input type="hidden" name="id_kelas" value="{{ $s->id_kelas }}">
        <x-theme.modal idModal="edit{{ $s->id_kelas }}" title="Edit Kelas Cabut" size="modal-lg" btnSave="Y">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="dhead">Kelas</th>
                        <th class="dhead">Tipe</th>
                        <th class="dhead">Gr</th>
                        <th class="dhead">Rupiah</th>
                        <th class="dhead">Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input value="{{ $s->kelas }}" name="kelas" type="text" class="form-control"></td>
                        <td><input value="{{ $s->tipe }}" name="tipe" type="text" class="form-control"></td>
                        <td><input value="{{ $s->gr }}" name="gr" type="text" class="form-control"></td>
                        <td><input value="{{ $s->rupiah }}" name="rupiah" type="text" class="form-control"></td>
                        <td>
                            <select name="lokasi" id="" class="form-control">
                                <option value="">- Pilih Lokasi -</option>
                                @foreach ($lokasi as $d)
                                    <option {{ $s->lokasi == $d ? 'selected' : '' }} value="{{ $d }}">
                                        {{ $d }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </x-theme.modal>
    </form>
@endforeach
