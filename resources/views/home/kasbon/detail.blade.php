<div class="row">
    <input type="hidden" name="id" value="{{ $detail->id }}">
    <div class="col-lg-12">
        <div class="form-group">
            <label for="">Nama Anak</label>
            <select required name="id_anak" class="form-control" id="">
                @foreach ($anak as $k)
                    <option {{ $k->id_anak == $detail->id_anak ? 'selected' : '' }} value="{{ $k->id_anak }}">
                        {{ strtoupper($k->nama) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-4">
        <label for="">Bulan dibayar</label>
        <select name="bulan_dibayar" class="form-control select2-edit" id="">
            <option value="">- Pilih Bulan -</option>

            @foreach (getListBulan() as $b)
                <option {{ $detail->bulan_dibayar == $b->bulan ? 'selected' : '' }} value="{{ $b->bulan }}">
                    {{ strtoupper($b->nm_bulan) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="">Tanggal</label>
            <input type="date" name="tgl" value="{{ $detail->tgl }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="">Nominal</label>
            <input required type="number" value="{{ $detail->nominal }}" min="0" name="nominal"
                class="form-control">
        </div>
    </div>


</div>
