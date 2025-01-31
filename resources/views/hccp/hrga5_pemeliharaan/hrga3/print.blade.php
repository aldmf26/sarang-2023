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
                    <p class="cop_judul">PERMINTAAN PERBAIKAN SARANA & <br> PRASARANA UMUM</p>
                </div>
            </div>
            <div class="col-3 ">
                <p class="cop_text">Dok.No.:FRM.HRGA.05.03, Rev.00</p>
            </div>
            <div class="col-1"></div>
            <div class="col-10">
                <table width="100%" style="padding: 90px">
                    <tr>
                        <td width="50%">Nama Sarana & Prasarana</td>
                        <td width="2%">:</td>
                        <td>{{ $permintaan->nama }}</td>
                    </tr>
                    <tr>
                        <td>Lokasi</td>
                        <td>:</td>
                        <td>{{ $permintaan->lokasi }}</td>
                    </tr>
                    <tr>
                        <td>No Identifikasi</td>
                        <td>:</td>
                        <td>{{ $permintaan->no_identifikasi }}</td>
                    </tr>
                    <tr>
                        <td>Diajukan oleh Bagian</td>
                        <td>:</td>
                        <td>{{ $permintaan->diajukan_oleh }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Deskripsi Masalah</td>
                        <td class="fw-bold">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"
                            style="height: 90px; border: 1px solid black; border-radius: 10px; vertical-align: middle; text-align: center">
                            {{ $permintaan->deskripsi_masalah }}
                        </td>
                    </tr>

                </table>
            </div>
            <div class="col-1"></div>
            <div class="col-1"></div>
            <div class="col-6 mt-4">Diajukan Oleh,</div>
            <div class="col-4 mt-4">Diterima Oleh (GA),</div>
            <div class="col-1"></div>

            <div class="col-1"></div>
            <div class="col-6 mt-4"></div>
            <div class="col-4 mt-4"></div>
            <div class="col-1"></div>
            <div class="col-1"></div>
            <div class="col-6 mt-4"></div>
            <div class="col-4 mt-4"></div>
            <div class="col-1"></div>


            <div class="col-1"></div>
            <div class="col-6 mt-4">Tanggal : {{ date('d-m-Y', strtotime($permintaan->tanggal)) }} <br> Pukul :
                {{ date('H:i', strtotime($permintaan->time)) }}</div>
            <div class="col-4 mt-4">Tanggal : {{ date('d-m-Y', strtotime($permintaan->tanggal)) }} <br> Pukul :
                {{ date('H:i', strtotime($permintaan->time)) }}</div>
            <div class="col-1"></div>

            <div class="col-1"></div>
            <div class="col-10 mt-4">
                <table width="100%" style="padding: 90px">
                    <tr>
                        <td width="50%" class="fw-bold">Detail Perbaikan yang Dilakukan</td>
                        <td width="2%" class="fw-bold">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"
                            style="height: 90px; border: 1px solid black; border-radius: 10px; vertical-align: middle; text-align: center">
                            {{ $detail_perbaikan->detail_perbaikan ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" class="fw-bold">Serah Terima Hasil Perbaikan</td>
                        <td width="2%" class="fw-bold">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="50%" class="fw-bold">Verifikasi User</td>
                        <td width="2%" class="fw-bold">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"
                            style="height: 90px; border: 1px solid black; border-radius: 10px; vertical-align: middle; text-align: center">
                            {{ $detail_perbaikan->verifikasi_user ?? '' }}
                        </td>
                    </tr>

                </table>
            </div>
            <div class="col-1"></div>

            <div class="col-1"></div>
            <div class="col-10 mt-4">
                <table width="100%" border="1" class=" table table-bordered">
                    <tr>
                        <th class="dhead text-center">Diserahkan oleh (GA),</th>
                        <th class="dhead text-center">Diterima oleh User,</th>
                    </tr>
                    <tr>
                        <td style="height: 90px"></td>
                        <td style="height: 90px"></td>
                    </tr>
                    <tr>
                        <td>Tanggal
                            {{ date('d-m-Y', strtotime($permintaan->tanggal)) }}
                        </td>
                        <td>Tanggal
                            {{ date('d-m-Y', strtotime($permintaan->tanggal)) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Pukul
                            {{ date('H:i', strtotime($permintaan->time)) }}
                        </td>
                        <td>Pukul
                            {{ date('H:i', strtotime($permintaan->time)) }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-1"></div>






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
