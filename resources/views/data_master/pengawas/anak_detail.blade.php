<input type="hidden" name="id" value="{{ $detail->id_anak }}">
<input type="hidden" name="hasilWawancaraId" value="{{ $hasilWawancara->id }}">
<div class="row">

    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Nama Panggilan</label>
            <input required type="text" name="nama" value="{{ $detail->nama }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Nama Lengkap</label>
            <input required type="text" name="nama_lengkap" value="{{ $hasilWawancara->nama }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">No KTP</label>
            <input required type="text" name="nik" value="{{ $hasilWawancara->nik }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="select2-edit" id="">
                <option value="">Jenis Kelamin</option>
                <option {{ $hasilWawancara->jenis_kelamin == 'L' ? 'selected' : '' }} value="L">Laki-laki</option>
                <option {{ $hasilWawancara->jenis_kelamin == 'P' ? 'selected' : '' }} value="P">Perempuan</option>
            </select>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Kelas</label>
            <select name="kelas" class="select2-edit" id="">
                <option value="">Kelas</option>
                @foreach ([1, 2, 3] as $k)
                    <option {{ $detail->id_kelas == $k ? 'selected' : '' }} value="{{ $k }}">
                        {{ $k }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Pembawa</label>
            <input type="text" id="pembawa" placeholder="pembawa karyawan" name="pembawa"
                value="{{ $detail->pembawa }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Divisi</label>
            <select name="id_divisi" id="" class="select2-edit">
                <option value="">Posisi</option>
                @foreach ($divisi as $d)
                    <option {{ $hasilWawancara->divisi_id == $d->id ? 'selected' : '' }} value="{{ $d->id }}">
                        {{ $d->divisi }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Posisi</label>
            <input type="text" id="posisi" placeholder="contoh : staff cabut" name="posisi"
                value="{{ $hasilWawancara->posisi2 }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Pengawas Cbt</label>
            <select name="id_pengawas" id="" class="select2-edit">
                <option value="">- Pilih Pengawas -</option>
                @foreach ($pengawas as $p)
                    <option {{ $detail->id_pengawas == $p->id ? 'selected' : '' }} value="{{ $p->id }}">
                        {{ ucwords($p->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Tgl Lahir</label>
            <input required type="date" name="tgl_lahir" value="{{ $hasilWawancara->tgl_lahir }}"
                class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Tgl Masuk</label>
            <input required type="date" id="tgl_masuk" name="tgl_masuk" value="{{ $hasilWawancara->tgl_masuk }}"
                class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Periode Bulan Bayar</label>
            <select name="periode" id="periode" class="select2-edit">
                <option value="">- Periode -</option>
                @foreach (range(1, 12) as $i)
                    <option {{ $detail->periode == $i ? 'selected' : '' }} value="{{ $i }}">
                        {{ $i }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Komisi Rp</label>
            <input type="text" name="komisi" value="{{ $detail->komisi }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <label for="">Tgl Dibayar</label>
            <input readonly id="tgl_dibayar" required type="date" name="tgl_dibayar"
                value="{{ $detail->tgl_dibayar }}" class="form-control">
        </div>
    </div>

    {{-- hasil wwawancara --}}
    <div class="col-lg-12">
        <div class="form-group">
            <label for="">Kesimpulan Hasil Wawancara : </label>
            <textarea name="kesimpulan" class="form-control text_akhir" id="" cols="15" rows="3"
                style="text-align: left;">{{ $hasilWawancara->kesimpulan }}</textarea>
        </div>
    </div>
    <div class="col-lg-7 d-flex align-items-center">
        <label for="">Periode Masa Percobaan :</label>
        <div class="form-check ms-2">
            <input class="form-check-input" type="radio" name="periode" id="1bulan" value="1"
                {{ $hasilWawancara->periode_masa_percobaan == 1 ? 'checked' : '' }}>
            <label class="form-check-label" for="1bulan">1 bulan</label>
        </div>
        <div class="form-check ms-2">
            <input class="form-check-input" type="radio" name="periode" id="3bulan" value="3"
                {{ $hasilWawancara->periode_masa_percobaan == 3 ? 'checked' : '' }}>
            <label class="form-check-label" for="3bulan">3 bulan</label>
        </div>
        <div class="form-check ms-2">
            <input class="form-check-input" type="radio" name="periode" id="6bulan" value="6"
                {{ $hasilWawancara->periode_masa_percobaan == 6 ? 'checked' : '' }}>
            <label class="form-check-label" for="6bulan">6 bulan</label>
        </div>
    </div>
    <div class="col-lg-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="4" class="text-center">PENILAIAN KARYAWAN</th>
                </tr>
                <tr>
                    <th>Kriteria Penilaian</th>
                    <th>Standar Penilaian</th>
                    <th>Hasil Penilaian</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Pendidikan</td>
                    <td><input type="text" name="pendidikan_standar" class="form-control"
                            value="{{ $penilaian->pendidikan_standar ?? 'N/A' }}">
                    </td>
                    <td><input type="text" name="pendidikan_hasil" class="form-control"
                            value="{{ $penilaian->pendidikan_hasil ?? 'N/A' }}">
                    </td>

                </tr>
                <tr>
                    <td>Pengalaman</td>
                    <td><input type="text" name="pengalaman_standar" class="form-control"
                            value="{{ $penilaian->pengalaman_standar ?? 'N/A' }}">
                    </td>
                    <td><input type="text" name="pengalaman_hasil" class="form-control"
                            value="{{ $penilaian->pengalaman_hasil ?? 'N/A' }}">
                    </td>
                </tr>
                <tr>
                    <td>Pelatihan</td>
                    <td><input type="text" name="pelatihan_standar" class="form-control"
                            value="{{ $penilaian->pelatihan_standar ?? 'N/A' }}">
                    </td>
                    <td><input type="text" name="pelatihan_hasil" class="form-control"
                            value="{{ $penilaian->pelatihan_hasil ?? 'N/A' }}">
                    </td>
                </tr>
                <tr>
                    <td>Keterampilan</td>
                    <td><input type="text" name="keterampilan_standar" class="form-control"
                            value="{{ $penilaian->keterampilan_standar ?? 'N/A' }}"></td>
                    <td><input type="text" name="keterampilan_hasil" class="form-control"
                            value="{{ $penilaian->keterampilan_hasil ?? 'N/A' }}"></td>
                </tr>
                <tr>
                    <td>Kompetensi Inti</td>
                    <td>
                        <textarea name="kompetensi_inti_standar" class="form-control" id="" cols="30" rows="4"
                            style="text-align: left">
                            {{ $penilaian->kompetensi_inti_standar ?? 'N/A' }}
                                        </textarea>
                    </td>
                    <td>
                        <textarea name="kompetensi_inti_hasil" class="form-control" id="" cols="30" rows="4"
                            style="text-align: left">
                                {{ $penilaian->kompetensi_inti_hasil ?? 'N/A' }}
                                        </textarea>
                    </td>
                </tr>
            </tbody>

        </table>
    </div>
</div>
