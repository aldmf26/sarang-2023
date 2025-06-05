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

    <div class="container-fluid">
        <div class="d-flex justify-content-between print_hilang">
            <a href="#"></a>
            <a onclick="window.print()" href="#" class="print print_hilang btn btn-sm btn-primary">
                <i class="fa-solid fa-print"></i> Print
            </a>
        </div>
        <div>
            <h5 class="fw-bold text-center " style="text-decoration: underline">PO GRADING : {{ $no_invoice }}
            </h5>
            @php
                $f = $formulir[0];
            @endphp
            <table class="table">
                <tr>
                    <th width="1">Tanggal</th>
                    <td width="1">:</td>
                    <td>{{ tanggal(date('Y-m-d')) }}</td>
                </tr>
                <tr>
                    <th width="150">Nama Partai</th>
                    <td width="1">:</td>
                    <td>{{ $f->nm_partai }}</td>
                </tr>
                <tr>
                    <th>Tipe</th>
                    <td width="1">:</td>
                    <td>{{ $f->tipe }} - {{ $f->ket }}</td>
                </tr>
            </table>
        </div>
        <div class="row">
            <div class="col-md-5">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pgws</th>
                            <th>No Box</th>
                            <th>Pcs</th>
                            <th>Gr</th>
                            <th>Pcs Ok</th>
                            <th>Turun Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $ttlPcsTurunGrade = 0;
                        @endphp
                        @foreach ($formulir as $i => $d)
                            @php
                                $pcsPth = DB::selectOne("SELECT sum(a.pcs) as pcs 
                                FROM tb_hancuran as a
                                where a.kategori in('cetak','sortir','grade','grading') and a.no_box = '$d->no_box'
                                ");
                                $ttlPcsTurunGrade += $pcsPth->pcs ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $d->pgws }}</td>
                                <td>{{ $d->no_box }}</td>
                                <td>{{ $d->pcs }}</td>
                                <td>{{ $d->gr }}</td>
                                <td>{{ $d->pcs - ($pcsPth->pcs ?? 0) }}</td>
                                <td>{{ $pcsPth->pcs ?? 0 }}</td>
                            </tr>
                        @endforeach
                        <!-- Add more rows as needed -->
                    </tbody>
                    <tfoot>
                        <tr></tr>
                        <th colspan="2">Total :</th>
                        <th>{{ sumCol($formulir, 'pcs') }}</th>
                        <th>{{ sumCol($formulir, 'gr') }}</th>
                        <th>{{ sumCol($formulir, 'pcs') - $ttlPcsTurunGrade }}</th>
                        <th>{{ $ttlPcsTurunGrade }}</th>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-7">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Grade</th>
                            <th>Pcs</th>
                            <th>Gr</th>
                            <th>Box Grading</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= 18; $i++)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        @endfor

                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total :</th>
                        </tr>

                    </tfoot>
                </table>
            </div>
        </div>

    </div>
    <script>
        window.print()
    </script>
</body>

</html>
