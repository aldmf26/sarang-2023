<div class="row">
    <input type="hidden" name="id_hariandll" value="{{ $detail->id_hariandll }}">
    <div class="col-lg-4">
        <div class="form-group">
            <label for="">Tanggal</label>
            <input type="date" value="{{ $detail->tgl }}" name="tgl" class="form-control">
        </div>
    </div>
    <div class="col-lg-8">
        <label for="">Nama Anak</label>
        <select name="id_anak" class="form-control select2-edit" id="">
            <option value="">- Pilih Anak -</option>
            @foreach ($anak as $d)
                <option {{$d->id_anak == $detail->id_anak ? 'selected' : '' }} value="{{ $d->id_anak }}">{{ strtoupper($d->nama) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="">Keterangan</label>
            <input value="{{ $detail->ket }}" type="text" name="ket" class="form-control">
        </div>
    </div>
    <div class="col-lg-4">
        <label for="">Lokasi</label>
        <select name="lokasi" class="form-control select2-edit" id="">
            <option value="">- Pilih Lokasi -</option>
            @php
                $lokasi = ['resto', 'aga', 'orchad', 'agrilaras'];
            @endphp
            @foreach ($lokasi as $d)
                <option {{$detail->lokasi == strtoupper($d) ? 'selected' : ''}} value="{{ strtoupper($d) }}">{{ strtoupper($d) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="">Rupiah</label>
            <input value="{{ $detail->rupiah }}" type="text" name="rupiah" class="form-control">
        </div>
    </div>
</div>
