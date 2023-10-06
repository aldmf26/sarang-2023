<form action="{{ route('kelas.cabutCreate') }}" method="post">
    <x-theme.button href="#" icon="fa-window-close" variant="danger" addClass="float-end btn_tutup"
            teks="Hapus" />
    <button class="btn btn-primary btn-sm float-end mb-3 me-2" type="submit"><i class="fas fa-plus"></i>Simpan</button>
    @csrf
    <table class="table" id="tblScroll" x-data="{
        rows: []
    }">
        <thead>
            <tr>
                <th class="dhead" width="15">#</th>
                <th class="dhead" width="90">Kategori</th>
                <th class="dhead" width="80">Kelas</th>
                <th class="dhead" width="70">Tipe</th>
                <th class="text-end dhead" width="70">Pcs</th>
                <th class="text-end dhead" width="70">Gr</th>
                <th class="text-end dhead" width="100">Rp</th>
                <th class="text-end dhead">Batas Susut</th>
                <th class="text-end dhead">Bonus Susut</th>
                <th class="text-end dhead" width="100">Rp Bonus</th>
                <th class="text-end dhead">Eot</th>
                <th class="text-end dhead">Denda Hcr</th>
                <th class="dhead">Keterangan</th>
                <th class="dhead" width="70">Aksi</th>
                {{-- <th>Lokasi</th> --}}
                {{-- <th width="20%">Aksi</th> --}}
            </tr>
        </thead>
        <tbody>

            <tr class="bg-info">
                <td></td>
                <td>
                    <select name="kategori_tambah[]" id="" class="form-control">
                        <option value="">Pilih</option>
                        <option value="1">Cabut</option>
                        <option value="2">Spesial</option>
                        <option value="3">Eo</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="kelas_tambah[]" class="form-control">
                </td>
                <td>
                    <input type="text" name="tipe_tambah[]" class="form-control">
                </td>
                <td>
                    <input x-mask:dynamic=" $money($input)" class="form-control text-end" name="pcs_tambah[]">
                </td>
                <td>
                    <input x-mask:dynamic=" $money($input)" class="form-control text-end" name="gr_tambah[]">
                </td>
                <td>
                    <input x-mask:dynamic="$money($input)" class="form-control text-end" name="rupiah_tambah[]">
                </td>
                <td>
                    <input x-mask:dynamic="$money($input)" class="form-control text-end" name="batas_susut_tambah[]">
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
                    <span class="badge bg-primary" @click="rows.push({ value: '' })" style="cursor: pointer"><i
                            class="fas fa-plus"></i></span>
                </td>

            </tr>
            <template x-for="(row, index) in rows" :key="index">
                <tr class="bg-info">
                    <td></td>
                    <td>
                        <select name="kategori_tambah[]" id="" class="form-control">
                            <option value="">Pilih</option>
                            <option value="1">Cabut</option>
                            <option value="2">Spesial</option>
                            <option value="3">Eo</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="kelas_tambah[]" class="form-control">
                    </td>
                    <td>
                        <input type="text" name="tipe_tambah[]" class="form-control">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" class="form-control text-end" name="pcs_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" class="form-control text-end" name="gr_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" class="form-control text-end" name="rupiah_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                            name="batas_susut_tambah[]">
                    </td>
                    <td>
                        <input x-mask:dynamic="$money($input)" class="form-control text-end"
                            name="bonus_susut_tambah[]">
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
                        <span class="badge bg-danger" @click="rows.splice(index, 1)" style="cursor: pointer"><i
                                class="fas fa-minus"></i></span>
                    </td>

                </tr>
            </template>
            @foreach ($datas as $no => $d)
                <input type="hidden" name="id_kelas[]" value="{{ $d->id_kelas }}">
                <tr>
                    <td>{{ $no + 1 }}</td>
                    <td>
                        <select name="kategori[]" id="" class="form-control">
                            <option {{$d->kategori == 1 ? 'selected' : ''}} value="1">Cabut</option>
                            <option {{$d->kategori == 2 ? 'selected' : ''}} value="2">Spesial</option>
                            <option {{$d->kategori == 3 ? 'selected' : ''}} value="3">Eo</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" value="{{ $d->kelas }}" name="kelas[]" class="form-control">
                    </td>
                    <td>
                        <input type="text" value="{{ $d->tipe }}" name="tipe[]" class="form-control">
                    </td>
                    <td>
                        <input x-mask:dynamic=" $money($input)" value="{{ $d->pcs }}"
                            class="form-control text-end" name="pcs[]">
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
                        <input x-mask:dynamic=" $money($input)" value="{{ $d->batas_susut }}"
                            class="form-control text-end" name="batas_susut[]">
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
                    <td >
                        <span id_kelas="{{ $d->id_kelas }}" data-bs-target="#infoKelas" data-bs-toggle="modal" class="badge bg-primary infoKelas" style="cursor: pointer"><i class="fas fa-question"></i></span>
                        <input type="checkbox" class="cekTutup float-end" name="cekTutup[]"
                                    id_kelas="{{ $d->id_kelas }}">
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

<x-theme.modal idModal="infoKelas" title="Info Rules" btnSave="T" size="modal-lg">
    <div id="infoBody"></div>
</x-theme.modal>
