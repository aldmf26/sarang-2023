<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body class="mx-10 mt-10">
    <div class="row">
        <div class="col-lg-3 col-sm-3">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <h6>Summary Ttl Rp</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ttl = 0;
                    @endphp
                    @foreach ($sum as $d)
                        @php
                            $ttl += $d->ttl_rp;
                        @endphp
                        <tr>
                            <th class="text-start">{{ strtoupper($d->lokasi) }}</th>
                            <th>:</th>
                            <th class="text-end">{{ number_format($d->ttl_rp) }}</th>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th class="text-end bg-success text-white">{{ number_format($ttl, 0) }}</th>
                    </tr>
                </tfoot>

            </table>
        </div>
        <div class="col-lg-4 col-sm-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <h6>Summary Pengawas</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ttl = 0;
                    @endphp
                    @foreach ($sumPgws as $d)
                        @php
                            $ttl += $d->ttl_rp;
                        @endphp
                        <tr>
                            <th class="text-start">{{ strtoupper($d->pgws) }}</th>
                            <th>:</th>
                            <th class="text-end">{{ number_format($d->ttl_rp) }}</th>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th class="text-end bg-success text-white">{{ number_format($ttl, 0) }}</th>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>
    <div class="row">

    </div>
</body>

</html>
