<div class="container">
    <h4 class="text-center">Hasil Evaluasi Karyawan</h4>
    <!-- Keputusan -->
    <div class="ms-4 mt-4">
        <h5>Keputusan:</h5>
        <p>
            @if ($karyawan->keputusan_lulus == 'lulus')
                <span class="badge bg-primary">Lulus Masa Percobaan</span>
            @else
                <span class="badge bg-danger">Tidak Lulus Masa Percobaan</span>
            @endif
        </p>
    </div>
    <!-- Data Karyawan -->
    <div class="card">
        <div class="card-body">
            <h5>Informasi Karyawan</h5>
            <div class="row">
                <div class="col-lg-8">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th><h6>Nama Karyawan</h6></th>
                            <th><h6>{{ $karyawan->nama }}</h6></th>
                        </tr>
                        <tr>
                            <th>Usia</th>
                            <td>{{ Umur($karyawan->tgl_lahir, $karyawan->created_at) . ' tahun' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>{{ $karyawan->jenis_kelamin }}</td>
                        </tr>
                        <tr>
                            <th>Posisi</th>
                            <td>{{ $karyawan->posisi }}</td>
                        </tr>
                        <tr>
                            <th>Periode Masa Percobaan</th>
                            <td>{{ $karyawan->periode_masa_percobaan }} Bulan</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $karyawan->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Penilaian -->
    <div class="card">
        <div class="card-body">
            <h5>Penilaian Karyawan</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th colspan="3" class="text-center dhead">PENILAIAN KARYAWAN</th>
                    </tr>
                    <tr class="text-center">
                        <th class="dhead">Kriteria Penilaian</th>
                        <th class="dhead">Standar Penilaian</th>
                        <th class="dhead">Hasil Penilaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penilaians as $penilaian)
                        <tr>
                            <td>{{ $penilaian->kriteria == 'Kompetensi_inti' ? 'Kompetensi Inti' : $penilaian->kriteria }}
                            </td>
                            <td>{{ $penilaian->standar }}</td>
                            <td>{{ $penilaian->hasil }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>


        </div>
    </div>

</div>
