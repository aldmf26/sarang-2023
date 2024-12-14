<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Data Diri</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        @if ($ket == 'berhasil')
            <h1 class="text-center">SUKSES</h1>
            <h1 class="text-center">SELAMAT ANDA SUDAH TERDAFTAR MENJADI KARYAWAN PT AGRIKA GATYA ARUM </h1>
            <br>
            <h2 class="text-center">SEKALI JA MEISINYA LAH KENA MAUK KEBANYAKAN DATA </h2>
            <br>
            <p class="text-center fw-lighter" style="font-size: 5px">habis nih piket ai lagi</p>
        @else
            <h1 class="text-center">GAGAL</h1>
            <h1 class="text-center">NIK SUDAH TERDAFTAR</h1>
        @endif

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
