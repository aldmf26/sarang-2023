<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>History Partai</title>
</head>

<body>
    <br>
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-6">
                <table class="table ">
                    <tr>
                        <th>Partai</th>
                        <th>No Box</th>
                        <th>tanggal mulai kerja</th>
                        <th>tanggal selesai kirim</th>
                    </tr>
                    <tr>
                        <td>{{ $nm_partai->nm_partai }}</td>
                        <td>{{ $no_box }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-12">
                <table class="table ">
                    <tr>
                        <th colspan="50">Bk</th>
                    </tr>
                    <tr>
                        <th>Pengawas</th>
                        <th>Timbang ulang</th>
                        <th>pcs awal</th>
                        <th>gr awal</th>
                        <th>pcs akhir</th>
                        <th>gr akhir</th>
                        <th>susut</th>
                        <th>total rp</th>
                    </tr>
                    <tr>
                        <td>{{ $nm_partai->nm_partai }}</td>
                        <td>{{ $no_box }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
