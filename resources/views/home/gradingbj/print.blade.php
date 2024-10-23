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
        @php
            $counter = 0;
        @endphp
        @foreach ($getBox as $d)
           
            <table class="table table-bordered" style="width: 70%; height: 45vh">
                <thead>
                    <tr>
                        <th width="190" class="dhead">Nama Partai</th>
                        <th class="dhead">Box Grading</th>
                        <th class="dhead">Grade</th>
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Gr</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $d->nm_partai }}</td>
                        <td>P{{ $d->box_pengiriman }}</td>
                        <td>{{ $d->grade }}</td>
                        <td align="right">{{ $d->pcs }}</td>
                        <td align="right">{{ $d->gr }}</td>
                    </tr>
                </tbody>
            </table>

            @php
                $counter++;
            @endphp
            @if ($counter % 2 == 0 && $loop->remaining > 0)
            <div class="page-break"></div> <!-- Page break after every 2 items, except for the last one -->
        @endif
        @endforeach
    </div>
<script>
    window.print()
</script>
</body>

</html>
