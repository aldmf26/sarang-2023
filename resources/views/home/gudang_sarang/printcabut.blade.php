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
</head>

<body>
    <br>
    <div class="container-fluid">
        <h6 class="fw-bold text-center" style="text-decoration: underline">FORMULIR SETOR BARANG KE CABUT</h6>
        <h6 class="fw-bold">Pengawas : {{ $nama }}</h6>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered" style="font-size: 13px; border: 1px solid black">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>tanggal</th>
                            <th>no box</th>
                            <th>nama anak</th>
                            <th class="text-end">pcs awal</th>
                            <th class="text-end">gr awal</th>
                            <th class="text-end">pcs akhir</th>
                            <th class="text-end">gr akhir</th>
                            <th class="text-end">eot</th>
                            <th class="text-end">pcs hcr</th>
                            <th class="text-end">susut</th>
                            <th class="text-end">total gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cabut as $no => $c)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ date('d-m-Y', strtotime($c->tgl)) }}</td>
                                <td>{{ $c->no_box }}</td>
                                <td></td>
                                <td class="text-end">{{ $c->pcs_awal }}</td>
                                <td class="text-end">{{ $c->gr_awal }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        window.print();
    </script>
</body>

</html>
