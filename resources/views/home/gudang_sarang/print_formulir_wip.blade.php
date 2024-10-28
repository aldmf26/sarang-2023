</html>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>{{ $title }}</title>

    <style>
        @media print {
            .print_hilang {
                display: none;
            }

            .section {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <br>
    <div class="container-fluid">
        <h5 class="fw-bold" style="text-decoration: underline">PO Wip : {{ $no_invoice }} | Tanggal :
            {{ tanggal($formulir[0]->tanggal) }}</h5>


        <div class="d-flex flex-wrap gap-2">
            @foreach ($formulir as $d)
                <table class="table table-sm table-bordered" border="1" style="width: calc(50% - 1rem); font-size: 10px">
                    <thead>
                        <tr>
                            <th class="bg-info">Grade</th>
                            <th class="bg-info">Box Grading</th>
                            <th class="bg-info">Pcs</th>
                            <th class="bg-info">Gr</th>
                            <th class="bg-info">No Barcode</th>
                            <th class="bg-info">Ket</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{ $d->grade }}</th>
                            <th>P{{ $d->no_box }}</th>
                            <th>{{ $d->pcs_awal }}</th>
                            <th>{{ $d->gr_awal }}</th>
                        </tr>

                    </tbody>
                    <tbody>
                        @php
                            $grading = DB::table('grading_partai')
                                ->where('box_pengiriman', $d->no_box)
                                ->get();
                        @endphp
                        @foreach ($grading as $s)
                            <tr>
                                <td colspan="2">{{ $s->nm_partai }}</td>
                                <td>{{ $s->pcs }}</td>
                                <td>{{ $s->gr }}</td>
                                <td colspan="2"></td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            @endforeach
        </div>




    </div>
    <script>
        window.print()
    </script>

</body>

</html>
