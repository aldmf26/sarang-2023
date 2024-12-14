<table class="table table-bordered" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama panggilan</th>
            <th width="15%">Nama Calon Karyawan</th>
            <th width="15%">NIK</th>
            <th>Tanggal Lahir</th>
            <th>Tanggal Masuk</th>
            <th>Usia</th>
            <th>Jenis Kelamin</th>
            <th>Posisi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($hrga2 as $h)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $h->nm_panggilan }}</td>
                <td>
                    <input type="text" class="form-control nama{{ $h->id_tb_anak }}" name="nama"
                        value="{{ $h->nama }}">
                </td>
                <td>
                    <input type="text" class="form-control nik{{ $h->id_tb_anak }}" name="nama"
                        value="{{ $h->nik }}">
                </td>
                <td>
                    <input type="date" class="form-control tgl_lahir{{ $h->id_tb_anak }}" name="tgl_lahir"
                        value="{{ $h->tgl_lahir }}">
                </td>
                <td>
                    <input type="date" class="form-control tgl_masuk{{ $h->id_tb_anak }}" name="tgl_masuk"
                        value="{{ $h->tgl_masuk }}">
                </td>
                <td>
                    {{ empty($h->tgl_lahir) ? '-' : Umur($h->tgl_lahir, $h->tgl_masuk) }}
                </td>
                <td>
                    <select name="jenis_kelamin" class="form-control jenis_kelamin{{ $h->id_tb_anak }}" id="">
                        <option value="P" {{ $h->jenis_kelamin == 'P' ? 'selected' : '' }}>
                            Perempuan
                        </option>
                        <option value="L" {{ $h->jenis_kelamin == 'L' ? 'selected' : '' }}>
                            Laki-laki
                        </option>

                    </select>
                </td>
                <td>
                    <select name="" id="" class="form-control divisi{{ $h->id_tb_anak }}">
                        @foreach ($divisi as $d)
                            @if ($lokasi_pgws == 'ctk' && empty($h->id))
                                <option value="{{ $d->id }}" @selected($d->id == 2)>{{ $d->divisi }}
                                </option>
                            @else
                                <option value="{{ $d->id }}" @selected($d->id == $h->id_divisi)>{{ $d->divisi }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                </td>

                <td>
                    @if (empty($h->id))
                        <a href="#" class="btn btn-primary btn-sm simpan_data"
                            id_anak="{{ $h->id_tb_anak }}">save</a>
                    @else
                        <a href="#" class="btn btn-primary btn-sm simpan_data"
                            id_anak="{{ $h->id_tb_anak }}">save</a>
                        <a href="{{ route('hrga2.export', $h->id) }}" class="btn btn-success btn-sm"><i
                                class="fas fa-file-excel"></i></a>
                    @endif


                </td>
            </tr>
        @endforeach

    </tbody>

</table>
