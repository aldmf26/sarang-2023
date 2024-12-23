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
                    <p class="cop_judul">EVALUASI PELATIHAN</p>
                </div>
            </div>
            <div class="col-3 ">
                <p class="cop_text">Dok.No.: FRM.HRGA.03.06, Rev.00</p>
            </div>
            <div class="col-lg-12">
                <table width="100%" style="font-size: 11px">
                    <tr>
                        <th colspan="10" class="dhead" style="height: 6px"></th>
                    </tr>
                    <tr>
                        <td class="border_kiri">Nama</td>
                        <td>:</td>
                        <td>{{ $karyawans->nama }}</td>

                        <td>Dept.</td>
                        <td>:</td>
                        <td>{{ $karyawans->divisi }}</td>

                        <td class="">Training</td>
                        <td class="">:</td>
                        <td class="border_kanan">HACCP & CCP</td>

                        <td rowspan="2" width="33%" class="border_kanan border_kiri border_bawah">
                            Periode Evaluasi :
                            <br>
                            <input type="checkbox" name="" id="" checked> 1 (Satu) Bulan Setelah
                            Training
                            &nbsp;&nbsp;&nbsp;
                            <input type="checkbox" name="" id=""> 6 (Enam) Bulan Setelah Training
                            <br>
                            <input type="checkbox" name="" id=""> 3 (Tiga) Bulan Setelah Training
                        </td>
                    </tr>
                    <tr>
                        <td class="border_kiri border_bawah">Jabatan</td>
                        <td class="border_bawah">:</td>
                        <td class="border_bawah">{{ $karyawans->status }}</td>

                        <td class="border_bawah"></td>
                        <td class="border_bawah"></td>
                        <td class="border_bawah">_____</td>

                        <td class="border_bawah ">Tanggal</td>
                        <td class="border_bawah ">:</td>
                        <td class="border_bawah border_kanan">20 jan 25</td>
                    </tr>

                </table>
                <br>
                <table width="100%" style="font-size: 11px" class="table">
                    <tr>
                        <th colspan="10" class="dhead text-center" style="height: 6px">TUJUAN PELATIHAN</th>
                    </tr>
                    <tr>
                        <td colspan="10" class="text-center ">
                            Memastikan karyawan peduli dengan personal hygiene, kebenaran tahapan setiap CCP, dan mampu
                            melapor kepada atasan jika ada masalah terkait dengan bahaya HACCP
                        </td>
                    </tr>
                </table>
                <br>
                <table width="100%" style="font-size: 11px" class="table table-bordered">
                    <thead>

                        <tr>
                            <th class="dhead text-center">NO.</th>
                            <th class="dhead text-center">RENCANA KERJA SETELAH MENGIKUTI PELATIHAN <br>
                                (diisi oleh peserta training)
                            </th>
                            <th class="dhead text-center">Target Realisasi <br> (bulan)</th>
                            <th class="dhead text-center">
                                REALISASI RENCANA KERJA SETELAH MENGIKUTI PELATIHAN <br>
                                (diisi oleh atasan)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Memahami Personal Hygiene</td>
                            <td class="text-center">1</td>
                            <td class="text-center">Memahami Personal Hygiene</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Melakukan kegiatan CCP dengan benar</td>
                            <td class="text-center">1</td>
                            <td class="text-center">Melakukan kegiatan CCP dengan benar</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Melapor jika ketemu masalah terkait HACCP produk</td>
                            <td class="text-center">1</td>
                            <td class="text-center">Melapor jika ketemu masalah terkait HACCP produk</td>
                        </tr>
                    </tbody>

                </table>

            </div>
            <div class="col-6">
                <table class="table table-bordered" with="100%" style="font-size: 11px">
                    <tr>
                        <th class="dhead text-center">KETERANGAN</th>

                    </tr>
                    <tr>
                        <td style="height: 47px"></td>
                    </tr>

                </table>
                <table class="table table-bordered" with="100%" style="font-size: 11px">
                    <tr>
                        <th class="dhead text-center" colspan="2">KOMENTAR ATASAN TERHADAP HASIL EVALUASI [Di-isi
                            oleh Pimpinan
                            Peserta Training]</th>

                    </tr>
                    <tr>
                        <td>Tgl. Review : ddmmy</td>
                        <td>
                            <input type="checkbox" name="" id="" checked> 1 (Satu) Bulan
                            <input type="checkbox" name="" id=""> 3 (Tiga) Bulan
                            <input type="checkbox" name="" id=""> 6 (Enam) Bulan
                        </td>
                    </tr>
                    <tr>
                        <td>Dievaluasi oleh: Tulis nama Kepala Div</td>
                        <td>
                            Kesimpulan:
                            <br>
                            <input type="checkbox" name="" id="" checked> (1) Belum dapat melaksanakan
                            tugas sesuai materi <br>
                            <input type="checkbox" name="" id=""> (2) Dapat melaksanakan tugas namun
                            harus dipantau ketat <br>
                            <input type="checkbox" name="" id=""> (3) Dapat melaksanakan tugas tanpa
                            dipantau ketat <br>
                            <input type="checkbox" name="" id=""> (4) Dapat memberikan training
                            terkait dengan aktivitas <br>
                            <input type="checkbox" name="" id=""> (5) Dapat memberikan training &
                            kontribusi improvement
                        </td>
                    </tr>

                </table>
            </div>
            <div class="col-6">
                <table class="table table-bordered" with="100%" style="font-size: 11px">
                    <tr>
                        <th class="dhead text-center">IDE KREATIF [ Tuangkan Ide Kreatif Anda Sehubungan dengan
                            Pekerjaan Anda ]</th>

                    </tr>
                    <tr>
                        <td style="height: 93px"></td>
                    </tr>

                </table>
                <table class="table table-bordered" style="font-size: 11px">
                    <thead>
                        <tr>
                            <th class="text-center" width="33.33%">Dibuat Oleh:</th>
                            <th class="text-center" width="33.33%">Diperiksa Oleh:</th>
                            <th class="text-center" width="33.33%">Diketahui Oleh:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="height: 75px"></td>
                            <td style="height: 75px"></td>
                            <td style="height: 75px"></td>
                        </tr>
                        <tr>
                            <td class="text-center">[SPV. HR]</td>
                            <td class="text-center">[KA. HRGA]</td>
                            <td class="text-center">[ DIREKTUR. ]</td>
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
