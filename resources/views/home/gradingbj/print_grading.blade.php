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

            .page-break {
                page-break-after: always;
            }

        }
    </style>
</head>

<body>

    <div class="py-2 container-fluid">
        <div class="d-flex justify-content-between print_hilang">
            <a href="#"></a>
            <a onclick="window.print()" href="#" class="print print_hilang btn btn-sm btn-primary">
                <i class="fa-solid fa-print"></i> Print
            </a>
        </div>
        <div class="d-flex flex-wrap">
        @foreach ($grading as $d)
            <div style="width:25%; padding:10px;">
                <table cellspacing="2" cellpadding="1" border="1">
                    <tr>
                        <td>No Nota</td>
                        <td>:</td>
                        <td>{{ $d->no_invoice }}</td>
                    </tr>
                    <tr>
                        <td>Nama Partai</td>
                        <td>:</td>
                        <td>{{ $d->nm_partai }}</td>
                    </tr>
                    <tr>
                        <td>Box Grading</td>
                        <td>:</td>
                        <th>{{ $d->box_pengiriman }}</th>
                    </tr>
                    <tr>
                        <td>Grade</td>
                        <td>:</td>
                        <th><h5>{{ $d->grade }}</h5></th>
                    </tr>
                    <tr>
                        <td>Pcs</td>
                        <td>:</td>
                        <th><h5>{{ $d->pcs }}</h5></th>
                    </tr>
                    <tr>
                        <td>Gr</td>
                        <td>:</td>
                        <th><h5>{{ $d->gr }}</h5></th>
                    </tr>
                </table>
            </div>
        @endforeach
        </div>
    </div>
<script>
    window.print()
</script>
</body>

</html>
