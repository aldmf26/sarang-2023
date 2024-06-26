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
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="d-flex justify-content-between print_hilang">
            <a href="#"></a>
            <a onclick="window.print()" href="#" class="print print_hilang btn btn-sm btn-primary">
                <i class="fa-solid fa-print"></i> Print
            </a>
        </div>
        <h5 class="fw-bold text-center" style="text-decoration: underline">PO GRADE</h5>

        <h6 class="fw-bold">Tanggal : {{ tanggal($ket_formulir->tanggal) }}</h6>
        <h6 class="fw-bold">Pengawas : {{ $ket_formulir->name }} ~ {{ $ket_formulir->penerima }}</h6>
        <h6>Po : </h6>

        <!-- Table Structure -->
        @foreach ($formulir as $d)
        <div class="row">
            <div class="col-md-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No Box</th>
                            <th>Tipe</th>
                            <th>Pcs</th>
                            <th>Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$d->no_box}}</td>
                            <td>{{$d->tipe}} {{$d->ket}}</td>
                            <td>{{ $d->pcs }}</td>
                            <td>{{ $d->gr }}</td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
            <div class="col-md-8">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Pcs</th>
                            <th>Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= 5; $i++)
                            
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endfor
                       
                        <!-- Add more rows as needed -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total :</th>
                        </tr>
                        <tr>
                            <th colspan="3">Susut :</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endforeach

    </div>
  


</body>

</html>
