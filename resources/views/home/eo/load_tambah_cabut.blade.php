<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #000000;
        line-height: 36px;
        font-size: 12px;
        width: auto;
    }
</style>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="dhead" widht="10">No Box</th>
            <th class="dhead" width="110">Pengawas</th>
            <th class="dhead" width="100">Nama Anak</th>
            <th class="dhead" width="10">Tgl Ambil</th>
            <th class="dhead" width="110">Kelas / Paket</th>
            <th class="dhead text-end">Ttl Gr</th>
            <th class="dhead text-end" width="100">Gr EO Awal</th>
            <th class="dhead text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($getAnak as $i => $x)
        <tr>
            <td>
                <select style="width:1%" name="no_box[]" class="select2-add pilihBox" count="{{ $i + 1 }}">
                    <option value="">- Pilih No Box -</option>
                    @foreach ($nobox as $d)
                        <option value="{{ $d->no_box }}">{{ $d->no_box }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" readonly value="{{ auth()->user()->name }}" name="example"
                    class="form-control">
                <input type="hidden" name="id_pengawas[]" readonly value="{{ auth()->user()->id }}">
                <input type="hidden" class="form-control" name="id_eo[]" readonly
                            value="{{ $x->id_eo }}">
            </td>
            <td class="h6">
                {{ $x->nama }}
                <input type="hidden" name="id_anak[]" value="{{ $x->id_anak }}">

            </td>
            <td>
                <input type="date" style="font-size: 12px" value="{{ date('Y-m-d') }}" name="tgl_ambil[]" class="form-control">
            </td>
            <td>
                <select required name="id_kelas[]" id="" class="select2-add">
                    <option value="">- Kelas -</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id_kelas }}">{{ strtoupper($k->kelas) }} -
                            {{ number_format($k->rupiah, 0) }}</option>
                    @endforeach
                </select>
            </td>
            <td class="h6 text-end ttlGr{{ $i + 1 }}">0</td>
            <td>
                <input name="gr_eo_awal[]" type="text" class="form-control text-end" value="0">
            </td>
            <td align="center">
                <button type="button" class="btn rounded-pill hapusCabutRow" id_cabut="{{ $x->id_eo }}"
                    id_anak="{{ $x->id_anak }}" count="{{ $i + 1 }}"><i
                        class="fas fa-trash text-danger"></i>
                </button>
            </td>
        </tr>
        @empty
            <tr>
                <td class="h6 text-warning text-center" colspan="8">Data anak kerja tidak ada</td>
            </tr>
        @endforelse
        @foreach ($getAnak as $i => $x)
            
        @endforeach
    </tbody>

</table>
