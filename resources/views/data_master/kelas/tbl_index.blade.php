<form action="{{ route('kelas.cabutCreate') }}" method="post">
    <button class="btn btn-primary btn-sm float-end mb-3" type="submit"><i class="fas fa-plus"></i>Simpan</button>
    @csrf
    <table class="table" id="tblScroll" x-data="{
        rows: []
    }">
        <thead>
            <tr>
                <th class="dhead" width="15">#</th>
                <th class="dhead" width="80">Kelas</th>
                <th class="dhead" width="90">Tipe</th>
                <th class="text-end dhead" width="90">Gr</th>
                <th class="text-end dhead">Rp</th>
                <th class="text-end dhead">Batas Susut</th>
                <th class="text-end dhead">Bonus Susut</th>
                <th class="text-end dhead">Rp Bonus</th>
                <th class="text-end dhead">Eot</th>
                <th class="text-end dhead">Denda Hcr</th>
                <th class="dhead">Keterangan</th>
                <th class="dhead">Aksi</th>
                {{-- <th>Lokasi</th> --}}
                {{-- <th width="20%">Aksi</th> --}}
            </tr>
        </thead>
        <tbody>
            
            <tr class="bg-info">
                <td></td>
                <td>
                    <input type="text" name="kelas_tambah[]" class="form-control">
                </td>
                <td>
                    <input type="text" name="tipe_tambah[]" class="form-control">
                </td>
                <td>
                    <input x-mask:dynamic=" $money($input)" class="form-control text-end" name="gr_tambah[]">
                </td>
                <td>
                    <input x-mask:dynamic="$money($input)" class="form-control text-end" name="rupiah_tambah[]">
                </td>
                <td>
                    <input x-mask:dynamic="$money($input)" class="form-control text-end" name="denda_susut_tambah[]">
                </td>
                <td>
                    <input x-mask:dynamic="$money($input)" class="form-control text-end" name="bonus_susut_tambah[]">
                </td>
                <td>
                    <input x-mask:dynamic="$money($input)" class="form-control text-end" name="rp_bonus_tambah[]">
                </td>
                <td>
                    <input x-mask:dynamic="$money($input)" class="form-control text-end" name="eot_tambah[]">
                </td>
                <td>
                    <input x-mask:dynamic="$money($input)" class="form-control text-end" name="denda_hcr_tambah[]">
                </td>
                <td>
                    <input type="text" class="form-control" name="ket_tambah[]">
                </td>
                <td>
                    <span class="badge bg-primary" @click="rows.push({ value: '' })" style="cursor: pointer"><i class="fas fa-plus"></i></span>
                </td>

            </tr>
            <template x-for="(row, index) in rows" :key="index">
                <tr class="bg-info">
                    <td></td>
                    <td>
                        <input type="text" name="kelas_tambah[]" class="form-control">
                    </td>
                    <td>
                        <input type="text" name="tipe_tambah[]" class="form-control">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" class="form-control text-end" name="gr_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" class="form-control text-end" name="rupiah_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" class="form-control text-end" name="denda_susut_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" class="form-control text-end" name="bonus_susut_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" class="form-control text-end" name="rp_bonus_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" class="form-control text-end" name="eot_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" class="form-control text-end" name="denda_hcr_tambah[]">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="ket_tambah[]">
                    </td>
                    <td>
                        <span class="badge bg-danger" @click="rows.splice(index, 1)" style="cursor: pointer"><i class="fas fa-minus"></i></span>
                    </td>
    
                </tr>
            </template>
            @foreach ($datas as $no => $d)
                <input type="hidden" name="id_kelas[]" value="{{ $d->id_kelas }}">
                <tr>
                    <td>{{ $no + 1 }}</td>
                    <td>
                        <input type="text" value="{{ $d->kelas }}" name="kelas[]" class="form-control">
                    </td>
                    <td>
                        <input type="text" value="{{ $d->tipe }}" name="tipe[]" class="form-control">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" value="{{ $d->gr }}"
                            class="form-control text-end" name="gr[]">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" value="{{ $d->rupiah }}"
                            class="form-control text-end" name="rupiah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" value="{{ $d->denda_susut }}"
                            class="form-control text-end" name="denda_susut[]">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" value="{{ $d->bonus_susut }}"
                            class="form-control text-end" name="bonus_susut[]">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" value="{{ $d->rp_bonus }}"
                            class="form-control text-end" name="rp_bonus[]">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" value="{{ $d->eot }}"
                            class="form-control text-end" name="eot[]">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" value="{{ $d->denda_hcr }}"
                            class="form-control text-end" name="denda_hcr[]">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="ket[]" value="{{ $d->ket }}">
                    </td>
                    <td>
                        <span class="badge bg-primary" style="cursor: pointer"><i class="fas fa-question"></i></span>
                    </td>
                    {{-- <td>{{ strtoupper($d->lokasi) }}</td> --}}
                    {{-- <td>

                    <x-theme.button modal="Y" idModal="delete" data="no_nota={{ $d->id_kelas }}_{{ $routeRemove }}"
                        icon="fa-trash" addClass="float-end delete_nota" teks="" variant="danger" />
                    <x-theme.button modal="Y" idModal="edit{{ $d->id_kelas }}" icon="fa-pen"
                        addClass="me-1 float-end edit-btn" teks="" data="id_kelas={{ $d->id_kelas }}" />
                </td> --}}
                </tr>
            @endforeach
        </tbody>

    </table>
</form>
<form action="{{ route('kelas.create') }}" method="post">
    @csrf
    <input type="hidden" name="routeRemove" value="{{ $routeRemove }}">
    <x-theme.modal title="Tambah Paket Cabut" idModal="tambah">
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
                    <td><input name="kelas" type="text" class="form-control"></td>
                    <td><input name="tipe" type="text" class="form-control"></td>
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
                        <td><input value="{{ $s->kelas }}" name="kelas" type="text" class="form-control">
                        </td>
                        <td><input value="{{ $s->tipe }}" name="tipe" type="text" class="form-control">
                        </td>
                        <td><input value="{{ $s->gr }}" name="gr" type="text" class="form-control">
                        </td>
                        <td><input value="{{ $s->rupiah }}" name="rupiah" type="text" class="form-control">
                        </td>
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
