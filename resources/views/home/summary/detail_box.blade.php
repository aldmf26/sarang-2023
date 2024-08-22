<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.css" />

    <title>History Partai</title>
</head>

<body>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #787878;
        }

        :root {
            --warna1: #F7914D;
            --warnaPengiriman: #87cefa;
            --warnaDhead: #8c8989;
        }

        .dhead {
            background-color: var(--warnaDhead) !important;
            color: white;
        }

        div.dt-container .dt-paging .dt-paging-button.current,
        div.dt-container .dt-paging .dt-paging-button.current:hover {
            color: white !important;
            border: 1px solid rgba(0, 0, 0, 0.3);
            background-color: rgba(0, 0, 0, 0.05);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(230, 230, 230, 0.05)), color-stop(100%, rgba(0, 0, 0, 0.05)));
            background: -webkit-linear-gradient(top, rgba(230, 230, 230, 0.05) 0%, rgba(0, 0, 0, 0.05) 100%);
            background: -moz-linear-gradient(top, rgba(230, 230, 230, 0.05) 0%, rgba(0, 0, 0, 0.05) 100%);
            background: -ms-linear-gradient(top, rgba(230, 230, 230, 0.05) 0%, rgba(0, 0, 0, 0.05) 100%);
            background: -o-linear-gradient(top, rgba(230, 230, 230, 0.05) 0%, rgba(0, 0, 0, 0.05) 100%);
            background: #F7914E;
        }
    </style>
    <br>
    <div class="container-fluid">

        <div class="row">
            @include('home.summary.nav_box');
            <div class="col-lg-8">
                <h5>Partai : {{ $nm_partai }}</h5>
                <table class="table table-bordered" id="myTable">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead text-center">no box</th>
                            <th class="dhead">pengawas</th>
                            <th class="dhead">grade</th>
                            <th class="dhead text-end">pcs</th>
                            <th class="dhead text-end">gr</th>
                            <th class="dhead text-end">total rp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bk as $b)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-center"><a target="_blank"
                                        href="{{ route('summary.history_box', ['no_box' => $b->no_box]) }}">{{ $b->no_box }}</a>
                                </td>
                                <td>{{ $b->name }}</td>
                                <td>{{ $b->tipe }}</td>
                                <td class="text-end">{{ number_format($b->pcs_awal, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr_awal, 0) }}</td>
                                <td class="text-end">{{ number_format($b->gr_awal * $b->hrga_satuan, 0) }}</td>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.4/js/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>

</html>
