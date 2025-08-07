<div class="row">
    <div class="col-lg-4">
        <label for="">Nama Anak</label>
        <select name="id_anak" id="" class="select4">
            <option value="">- Pilih anak -</option>
            @foreach ($anak as $g)
                <option value="{{ $g->id_anak }}" {{ $cabut->id_anak == $g->id_anak ? 'selected' : '' }}>
                    {{ $g->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-4">
        <label for="">Tanggal Terima</label>
        <input type="date" name="tgl_terima" value="{{ $cabut->tgl_terima }}" class="form-control">
        <input type="hidden" name="no_box" value="{{ $cabut->no_box }}" class="form-control">
    </div>
    <div class="col-lg-4">
        <label for="">Tanggal Selesai</label>
        <input type="date" name="tgl_serah" value="{{ $cabut->tgl_serah }}" class="form-control">
    </div>
</div>
