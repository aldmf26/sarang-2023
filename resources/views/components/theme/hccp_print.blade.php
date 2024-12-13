@props(['title', 'dok'])

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Hello, world!</title>
    <style>
        body {
            font-family: 'Cambria';
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="mb-5 d-flex gap-3 align-items-center">
            <img style="width: 150px" src="{{ asset('uploads/logo.jpeg') }}" alt="">
            <div>
                <h5 style="border-radius: 15px;" class="border border-2 border-dark p-3"><b>{{ strtoupper($title) }}</b></h5>
                <span style="font-size: 10px; margin-top: 20px; right: 50px" class="float-end"> {{ $dok }}</span>
            </div>
            
        </div>
        {{ $slot }}
    </div>
</body>

</html>
