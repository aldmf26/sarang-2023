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
</head>

<body>
    <style>
        .po-table {
            width: 100%;
            font-size: 10px;
            margin-bottom: 0.5rem;
            table-layout: fixed;
        }
    
        .po-table th,
        .po-table td {
            padding: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    
        @media (min-width: 768px) {
            .po-table {
                width: calc(50% - 0.5rem);
            }
        }
    
        @media print {
            @page {
                size: landscape;
                margin: 1cm;
            }
    
            .po-table {
                page-break-inside: avoid;
                border-collapse: collapse;
            }
    
            .table th,
            .table td {
                padding: 4px !important;
                border: 1px solid #000 !important;
            }
    
            thead {
                display: table-header-group;
            }
    
            .table {
                border: none !important;
            }
        }
    </style>
    
    <div class="container-fluid">
        <h5 class="fw-bold mb-3" style="text-decoration: underline">PO Wip : {{ $no_invoice }} | Tanggal :
            {{ tanggal($formulir[0]->tanggal) }}</h5>
    
        <div class="d-flex flex-wrap" style="gap: 0.5rem;">
            @foreach ($formulir as $d)
                <table class="table table-sm table-bordered po-table">
                    <thead>
                        <tr>
                            <th style="width: 20%">Grade</th>
                            <th style="width: 20%">Box Grading</th>
                            <th style="width: 15%" class="text-end">Pcs</th>
                            <th style="width: 15%" class="text-end">Gr</th>
                            <th style="width: 15%">No Barcode</th>
                            <th style="width: 15%">Ket</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{ $d->grade }}</th>
                            <th>P{{ $d->no_box }}</th>
                            <th class="text-end">{{ $d->pcs_awal }}</th>
                            <th class="text-end">{{ $d->gr_awal }}</th>
                            <th></th>
                            <th></th>
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
                                <td align="right">{{ $s->pcs }}</td>
                                <td align="right">{{ $s->gr }}</td>
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
