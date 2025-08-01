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
    <h1>Hello, world!</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ket</th>
                <th>pcs</th>
                <th>gr</th>
                <th>bk</th>
                <th>kerja</th>
                <th>oprasional</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cabut Proses</td>
                <td>{{ $cbt_proses->pcs }}</td>
                <td>{{ $cbt_proses->gr }}</td>
                <td>{{ number_format($cbt_proses->ttl_rp, 0) }}</td>
                <td>{{ number_format($cbt_proses->cost_kerja, 0) }}</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Cabut Sisa Pengawas</td>
                <td>{{ $cbt_sisa_pgws->pcs }}</td>
                <td>{{ $cbt_sisa_pgws->gr }}</td>
                <td>{{ number_format($cbt_sisa_pgws->ttl_rp, 0) }}</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <td>Cabut Selesai siap cetak</td>
                <td>{{ $cbt_sisa_pgws->pcs }}</td>
                <td>{{ $cbt_sisa_pgws->gr }}</td>
                <td>{{ number_format($cbt_sisa_pgws->ttl_rp, 0) }}</td>
                <td>0</td>
                <td>0</td>
            </tr>
        </tbody>
    </table>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    -->
</body>

</html>
