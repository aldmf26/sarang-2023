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
    <style>
        @media print {
            .page {
                page-break-before: always;
            }
        }
    </style>
    <div class="row page">
        <div class="col-lg-12 content">
            @include('home.packinglist.tbl_sum_pengiriman')
        </div>
    </div>
    <div class="row page">
        <div class="content fixed-on-next-page">
            <div class="col-lg-12 content">
                @include('home.packinglist.tbl_list_pengiriman')
            </div>
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
