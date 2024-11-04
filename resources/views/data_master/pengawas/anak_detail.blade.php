


<div class="row">
    <input type="hidden" name="id" value="{{ $detail->id_anak }}">

    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Nama Anak</label>
            <input required value="{{ $detail->nama }}" type="text" name="nama" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Kelas</label>
            <select name="kelas" class="select2-edit" id="">
                <option  value="">Kelas</option>
                @php
                    $kelas = [1, 2, 3];
                @endphp
                @foreach ($kelas as $k)
                    <option {{ $detail->id_kelas == $k ? 'selected' : '' }} value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Pgws Pembawa</label>
            <input required value="{{ $detail->pembawa }}" type="text" id="pembawa" placeholder="pembawa karyawan" name="pembawa"
                class="form-control">
        </div>
    </div>


    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Pengawas Cbt</label>
            <select name="id_pengawas" id="" class="select2-edit">
                <option value="">- Pilih Pengawas -</option>
                @foreach ($pengawas as $p)
                    <option  {{ $p->id == $detail->id_pengawas ? 'selected' : '' }}
                        value="{{ $p->id }}">{{ ucwords($p->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label for="">Tgl Masuk</label>
            <input required type="date" id="tgl_masuk{{$detail->id_anak}}" value="{{ $detail->tgl_masuk }}"
                name="tgl_masuk" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Periode Bulan Bayar</label>
            <select required name="periode" id="periode{{$detail->id_anak}}" class="select2-edit">
                <option value="">- Periode -</option>
                @for ($i = 1; $i < 13; $i++)
                    <option {{ $i == $detail->periode ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Komisi Rp</label>
            <input required value="{{ $detail->komisi }}" type="text" value="" name="komisi" class="form-control">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label for="">Tgl Dibayar</label>
            <input  id="tgl_dibayar{{$detail->id_anak}}" required type="date" value="{{ $detail->tgl_dibayar }}"
                name="tgl_dibayar" class="form-control">
        </div>
    </div>


</div>
