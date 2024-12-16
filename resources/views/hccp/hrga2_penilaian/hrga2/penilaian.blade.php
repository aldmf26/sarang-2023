<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <div class="row">
            <div class="col-lg-4">
                <table class="table">
                    <tr>
                        <th>Nama</th>
                        <th>:</th>
                        <th>{{ $karyawan->nama }}</th>
                    </tr>
                    <tr>
                        <th>Div / Dept</th>
                        <th>:</th>
                        <td>{{ $karyawan->divisi->divisi }}</td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <th>:</th>
                        <td>{{ $karyawan->posisi2 }}</td>
                    </tr>

                </table>
            </div>
            <div class="col-lg-4"></div>
            <div class="col-lg-4">
                <table class="table">
                    <tr>
                        <th>Tgl Masuk Kerja</th>
                        <th>:</th>
                        <th>{{ $karyawan->anak->tgl_masuk }}</th>
                    </tr>
                    <tr>
                        <th>Tgl berakhirnya masa percobaan</th>
                        <th>:</th>
                        <td>-</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <th>:</th>
                        <td>{{ $karyawan->jenis_kelamin }}</td>
                    </tr>

                </table>
            </div>
        </div>

        <span>I. Penilaian Kompetensi</span> <br><br>
        <span>II. CATATAN KEHADIRAN</span>

            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="dhead" rowspan="3">No</th>
                        <th class="dhead" rowspan="3">Keterangan</th>
                        <th class="dhead" colspan="24">Bulan</th>
                        <th class="dhead" rowspan="3">Total</th>
                    </tr>
                    <tr>
                        <!-- Angka bulan -->
                        <th class="dhead" colspan="2">1</th>
                        <th class="dhead" colspan="2">2</th>
                        <th class="dhead" colspan="2">3</th>
                        <th class="dhead" colspan="2">4</th>
                        <th class="dhead" colspan="2">5</th>
                        <th class="dhead" colspan="2">6</th>
                        <th class="dhead" colspan="2">7</th>
                        <th class="dhead" colspan="2">8</th>
                        <th class="dhead" colspan="2">9</th>
                        <th class="dhead" colspan="2">10</th>
                        <th class="dhead" colspan="2">11</th>
                        <th class="dhead" colspan="2">12</th>
                    </tr>
                    <tr>
                        <!-- Sub-baris untuk Hari dan Menit -->
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                        <th class="dhead">Hari</th><th class="dhead">Menit</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data rows -->
                    <tr>
                        <td>1</td>
                        <td>Terlambat</td>
                        <!-- Kolom data bulan -->
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Sakit</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Tanpa Keterangan</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Izin</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                        <td>0</td><td>0</td>
                    </tr>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2">Grand Total</th>
                        <th colspan="24"></th>
                        <th>0</th>
                    </tr>
                </tfoot>
            </table>
    
            <div class="mt-4">
                <p><strong>Penilaian:</strong></p>
                <ul>
                    <li>Baik Sekali = Grand Total < 3 hari</li>
                    <li>Baik = Grand Total 4 - 7 hari</li>
                    <li>Cukup = Grand Total 8 - 12 hari</li>
                    <li>Kurang = Grand Total > 12 hari</li>
                </ul>
                <p><strong>Catatan:</strong> 1 hari = 8 jam</p>
            </div>

    </x-slot>

</x-theme.app>
