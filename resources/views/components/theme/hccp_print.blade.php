<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=HCCP_".date('Y-m-d').".xls");
?>
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
</head>

<body>
  <div class="container">
    <div class="d-flex gap-3 justify-content-center align-items-center">
      <img style="width: 150px" src="{{ asset('uploads/logo.jpeg') }}" alt="">
      <div>
      <h5>PERMOHONAN KARYAWAN BARU</h5>
    </div>
    <span style="font-size: 10px; margin-top: 20px; right: 10px"> Dok.No.: FRM.HRGA.01.01, Rev.00</span>
    </div>
    {{ $slot }}
  </div>
</body>

</html>
