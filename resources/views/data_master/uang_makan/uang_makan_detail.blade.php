<div class="row">
    <input type="hidden" name="id_uang_makan" value="{{ $detail->id_uang_makan }}">
    <div class="col-lg-9">
        <div class="form-group">
            <label for="">Nominal</label>
            <input required type="number" value="{{ $detail->nominal }}" name="nominal" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Kelas</label>
            <select name="aktiv" id="" class="form-control">
                <option value="Y" {{ $detail->aktiv == 'Y' ? 'selected' : '' }}>Aktif</option>
                <option value="T" {{ $detail->aktiv == 'T' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
    </div>


</div>
