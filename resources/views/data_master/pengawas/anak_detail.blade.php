<div class="row">
    <input type="hidden" name="id" value="{{ $detail->id_anak }}">
    <div class="col-lg-9">
        <div class="form-group">
            <label for="">Nama Anak</label>
            <input required type="text" value="{{ $detail->nama }}" name="nama" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Kelas</label>
            <input required type="text" value="{{ $detail->id_kelas }}" name="kelas" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Tgl Masuk</label>
            <input required type="date" value="{{ $detail->tgl_masuk }}" name="tgl_masuk"
                class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Pengawas</label>
            <select name="id_pengawas" id="" class="select2-edit">
                <option value="">- Pilih Pengawas -</option>
                @foreach ($pengawas as $p)
                    <option {{$p->id == $detail->id_pengawas ? 'selected' : ''}} value="{{ $p->id }}">{{ ucwords($p->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>

</div>