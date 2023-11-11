
<div class="row">
    <form id="viewDetailAbsen">
        <div class="col-lg-12">
            <table class="table">
                <input type="hidden" name="id_pengawas" value="{{ $absen[0]->id_pengawas }}">
                <tr>
                    <th class="dhead">Pengawas</th>
                    <th class="dhead">Bulan</th>
                    <th class="dhead">Tahun</th>
                    <th class="dhead">Aksi</th>
                </tr>
                <tr>
                    <td>{{ $absen[0]->name }}</td>
                    <td>
                        @php
                            $bulan = DB::table('bulan')->get();
                        @endphp
                        <select name="bulan" class="form-control select2" id="">
                            @foreach ($bulan as $d)
                                <option {{ $bulanGet == $d->bulan ? 'selected' : '' }} value="{{ $d->bulan }}">
                                    {{ $d->nm_bulan }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="tahun" class="form-control select2" id="">
                            @php
                                $tahun = [2022, 2023];
                            @endphp
                            @foreach ($tahun as $d)
                                <option {{ $tahunGet == $d ? 'selected' : '' }} value="{{ $d }}">
                                    {{ $d }}
                                </option>
                            @endforeach
                        </select>

                    </td>
                    <td>
                        <button type="submit" class="btn btn-primary btn-sm">View</button>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>

<table class="table table-boredered" id="tableDetailAbsen">
    <thead>
        <tr>
            <th class="">#</th>
            <th class="">Nama Anak</th>
            <th class="">Kelas</th>
            <th class="text-center">Ttl Absen</th>
        </tr>
    </thead>
    <tbody>
        
            @foreach ($absen as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $d->nama }}</td>
                    <td>{{ $d->id_kelas }}</td>
                    <td align="center">{{ $d->ttl }}</td>
                </tr>
            @endforeach
    </tbody>
</table>
