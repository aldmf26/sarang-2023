<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>{{ $title }}</title>
    <style>
        .cop_judul {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 15px;
        }

        .shapes {
            border: 1px solid black;
            border-radius: 10px;
        }

        .cop_text {
            font-size: 12px;
            text-align: left;
            font-weight: normal;
            margin-top: 100px;

        }

        .dhead {
            background-color: #C0C0C0 !important;
        }

        .bg-black {
            background-color: black !important;
        }

        .border_atas {
            border-top: 1px solid black;
        }

        .border_bawah {
            border-bottom: 1px solid black;
        }

        .border_kanan {
            border-right: 1px solid black;
            padding-right: 6px;
        }

        .border_kiri {
            border-left: 1px solid black;
            padding-left: 6px;
        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {

            padding: 4px;
        }

        .table_border {
            border-collapse: collapse !important;
            border: 1px solid black !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-3 mt-4">
                <img style="width: 150px" src="{{ asset('img/logo.jpeg') }}" alt="">
            </div>
            <div class="col-6 mt-4">
                <div class="shapes">
                    <p class="cop_judul">RIWAYAT PEMELIHARAAN SARANA & <br> PRASARANA UMUM</p>
                </div>
            </div>
            <div class="col-3 ">
                <p class="cop_text">Dok.No.:FRM.HRGA.05.02, Rev.00</p>
            </div>
            <div class="col-1"></div>

            <br>
            <div class="col-lg-10">
                <table width="100%">
                    <tr>
                        <td width="30%">Nama Sarana/Prasarana Umum</td>
                        <td width="2%">:</td>
                        <td>{{ $item->nama }}</td>
                    </tr>
                    <tr>
                        <td width="30%">Merk</td>
                        <td width="2%">:</td>
                        <td>{{ $item->merk ?? 'kosong' }}</td>
                    </tr>
                    <tr>
                        <td width="30%">No Identifikasi</td>
                        <td width="2%">:</td>
                        <td>{{ $item->no_identifikasi ?? 'kosong' }}</td>
                    </tr>
                    <tr>
                        <td width="30%">Lokasi</td>
                        <td width="2%">:</td>
                        <td>{{ $item->lokasi . ' ' . 'lantai ' . $item->lantai }}</td>
                    </tr>
                    <tr>
                        <td width="30%">&nbsp;</td>
                        <td width="2%">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30%">Tahun Pemeriksaan</td>
                        <td width="2%">:</td>
                        <td>2025</td>
                    </tr>

                </table>
            </div>
            <div class="col-lg-12 mt-3">
                <table class="table " width="100%">
                    <thead>
                        <tr>
                            <th class="dhead table_border align-middle text-center">Tanggal</th>
                            <th class="dhead table_border align-middle text-center">Perawatan/Perbaikan</th>
                            <th class="dhead table_border align-middle text-center">Kebersihan</th>
                            <th class="dhead table_border align-middle text-center">Fungsi</th>
                            <th class="dhead table_border align-middle text-center">Kesimpulan Hasil Pemeriksaan</th>
                            <th class="dhead table_border align-middle text-center">Paraf Pelaksana</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat as $r)
                            <tr>
                                <td class="table_border">{{ date('d/m/Y', strtotime($r->tgl)) }}</td>
                                <td class="table_border">{{ $r->ket }}</td>
                                <td class="table_border">
                                    {{ $r->ket == 'perawatan' ? 'Kondisi Bersih' : 'Kembali bersih' }}
                                </td>
                                <td class="table_border">{{ $r->fungsi }}</td>
                                <td class="table_border">{{ $r->kesimpulan }}</td>
                                <td class="table_border"></td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>
            <div class="col-lg-12">
                <p>Note:</p>
                <p>1. Untuk pemeriksaan kebersihan dilakukan pengecekan kebersihan kondisi fisik dari sarana dan
                    prasarana umum</p>
                <p>2. Untuk pemeriksaan fungsi dari sarana prasarana umum dilakukan dengan cara menjalangkan fungsi alat
                    dan menilai apakah alat masih berfungsi dengan normal atau tidak</p>
                <p>Untuk perbaikan tidak perlu diisikan hasil pemeriksaan kebersihan dan fungsi dari alat</p>
            </div>
            <div class="col-4">

            </div>
            <div class="col-8">
                <table class="table table-bordered" style="font-size: 11px">
                    <thead>
                        <tr>
                            <th class="text-center table_border" width="33.33%">Dibuat Oleh:</th>
                            <th class="text-center table_border" width="33.33%">Diperiksa Oleh:</th>
                            <th class="text-center table_border" width="33.33%">Diketahui Oleh:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="height: 80px" class="table_border"></td>
                            <td style="height: 80px" class="table_border"></td>
                            <td style="height: 80px" class="table_border"></td>
                        </tr>
                        <tr>
                            <td class="text-center table_border">[GENERAL MAINTENANCE]</td>
                            <td class="text-center table_border">[SPV. GA-IR]</td>
                            <td class="text-center table_border">[KA.HRGA]</td>
                        </tr>
                    </tbody>
                </table>
            </div>







        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        window.print();
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    -->
</body>

</html>
