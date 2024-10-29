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
        <div style="font-size: 15px; display: flex; flex-wrap: wrap; gap: 0; margin: 0; padding: 0;">
            @foreach ($grading as $d)
                <div style="width: 25%; box-sizing: border-box; padding: 0; margin: 0;">
                    <table border="1" style="border-collapse: collapse; width: 100%; margin: 0; padding: 0;">
                        <tr>
                            <td style="padding: 5px;">No Nota</td>
                            <td style="padding: 5px;">:</td>
                            <td style="padding: 5px;">{{ $d->no_invoice }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px;">Nama Partai</td>
                            <td style="padding: 5px;">:</td>
                            <th style="padding: 5px;">{{ $d->nm_partai }}</th>
                        </tr>
                        <tr>
                            <td style="padding: 5px;">Box Grading</td>
                            <td style="padding: 5px;">:</td>
                            <th style="padding: 5px;">{{ $d->box_pengiriman }}</th>
                        </tr>
                        <tr>
                            <td style="padding: 5px;">Grade</td>
                            <td style="padding: 5px;">:</td>
                            <th style="padding: 5px;">
                                <h5 style="margin: 0;">{{ $d->grade }}</h5>
                            </th>
                        </tr>
                        <tr>
                            <td style="padding: 5px;">Pcs</td>
                            <td style="padding: 5px;">:</td>
                            <td style="padding: 5px;">{{ $d->pcs }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px;">Gr</td>
                            <td style="padding: 5px;">:</td>
                            <td style="padding: 5px;">{{ $d->gr }}</td>
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
