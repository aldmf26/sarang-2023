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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-3 mt-4">
                <img style="width: 150px" src="{{ asset('img/logo.jpeg') }}" alt="">
            </div>
            <div class="col-7 mt-4">
                <div class="shapes">
                    <p class="cop_judul">CEKLIST PERAWATAN MESIN PROSES PRODUKSI</p>
                </div>
            </div>
            <div class="col-2"></div>
            <div class="col-8"></div>
            <div class="col-4 ">
                <p class="cop_text">Dok.No.: FRM.HRGA.08.02, Rev.00</p>
            </div>
            <div class="col-1"></div>
            <div class="col-5">
                <table width="100%" style="padding: 90px">
                    <tr>
                        <td width="50%">Nama Mesin</td>
                        <td width="2%">:</td>
                        <td>{{ $permintaan->nama }}</td>
                    </tr>
                    <tr>
                        <td>Merek</td>
                        <td>:</td>
                        <td>{{ $permintaan->merk }}</td>
                    </tr>
                    <tr>
                        <td>No Mesin</td>
                        <td>:</td>
                        <td>{{ $permintaan->no_identifikasi }}</td>
                    </tr>
                    <tr>
                        <td>Lokasi</td>
                        <td>:</td>
                        <td>{{ $permintaan->lokasi }}</td>
                    </tr>



                </table>
            </div>
            <div class="col-6"></div>
            <div class="col-12 mt-4">
                <br>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead text-center align-middle">No</th>
                            <th class="dhead text-center align-middle">Tanggal</th>
                            <th class="dhead text-center align-middle">Kriteria Pemeriksaan</th>
                            <th class="dhead text-center align-middle">Metode</th>
                            <th class="dhead text-center align-middle">Hasil Pemeriksaan</th>
                            <th class="dhead text-center align-middle">Status</th>
                            <th class="dhead text-center align-middle">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ceklis as $no => $c)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ tanggal($c->tanggal) }}</td>
                                <td>{{ $c->kriteria_pemeriksaan }}</td>
                                <td>{{ $c->metode }}</td>
                                <td>{{ $c->hasil_pemeriksaan }}</td>
                                <td>{{ $c->status }}</td>
                                <td>{{ $c->ket }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
            <div class="col-6"></div>
            <div class="col-6">
                <table class="table table-bordered" style="font-size: 11px">
                    <thead>
                        <tr>
                            <th class="text-center" width="33.33%">Dibuat Oleh:</th>
                            <th class="text-center" width="33.33%">Diketahui Oleh:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="height: 80px"></td>
                            <td style="height: 80px"></td>
                        </tr>
                        <tr>
                            <td class="text-center">[GENERAL MAINTENANCE]</td>
                            <td class="text-center">[SPV GA-IR]</td>
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
