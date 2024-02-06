<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body class="container">
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered" style="border: 1px solid black">
                @php
                    $color = "style='background-color: #C8C6C4'";
                @endphp
                <thead>
                    <tr>
                        <th class="text-center align-middle" {!!$color!!} rowspan="3">GRADE</th>
                        <th class="text-center" {!!$color!!}>FROM</th>
                        <th class="text-center" {!!$color!!}></th>
                        <th class="text-center" {!!$color!!}>{{ strtoupper($detailPacking->nm_packing) }}</th>
                    </tr>
                    <tr>
                        <th class="text-center" {!!$color!!}>DATE</th>
                        <th class="text-center" {!!$color!!}></th>
                        <th class="text-center" {!!$color!!}>{{ tanggal($detailPacking->tgl) }}</th>
                    </tr>
                    <tr>
                        <th class="text-center" {!!$color!!}>BOX</th>
                        <th class="text-center" {!!$color!!}>PCS</th>
                        <th class="text-center" {!!$color!!}>GRAM</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ttlBox = 0;
                        $ttlPcs = 0;
                        $ttlGr = 0;
                    @endphp
                    @foreach ($detail as $d)
                        @php
                            $gr = $d->gr + $d->gr_naik;
                            $ttlBox += $d->box;
                            $ttlPcs += $d->pcs;
                            $ttlGr += $gr;
                        @endphp
                        <tr class="text-center">
                            <td>{{ $d->grade }}</td>
                            <td>{{ number_format($d->box, 0) }}</td>
                            <td>{{ number_format($d->pcs, 0) }}</td>
                            <td>{{ number_format($gr, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center" {!!$color!!}>TOTAL</th>
                        <th class="text-center" {!!$color!!}>{{ number_format($ttlBox, 0) }}</th>
                        <th class="text-center" {!!$color!!}>{{ number_format($ttlPcs, 0) }}</th>
                        <th class="text-center" {!!$color!!}>{{ number_format($ttlGr, 0) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script>
        window.print()
    </script>
</body>

</html>
