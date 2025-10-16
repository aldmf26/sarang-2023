<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet"
        crossorigin="anonymous">

    <title>Sukses</title>
</head>

<body>
    <div class="container justify-content-center mt-4">
        <div class="row">
            <div class="col-lg-12 text-center">
                <br><br>
                <!-- Gambar -->
                <img src="https://ptagrikagatyaarum.com/img/success.svg" alt="" height="350px" width="400px">
                <h1 class="text-center text-success mt-4">Data berhasil diajukan</h1>
            </div>

        </div>
    </div>

    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <script>
        // Fungsi untuk memicu efek konfeti
        function launchConfetti() {
            confetti({
                particleCount: 150, // Jumlah partikel
                spread: 80, // Area penyebaran
                origin: {
                    y: 0.6
                } // Posisi peluncuran
            });
        }

        // Jalankan konfeti ketika halaman dimuat
        window.onload = function() {
            launchConfetti();

            // Tambahkan interval untuk efek berulang (opsional)
            setTimeout(() => {
                confetti({
                    particleCount: 100,
                    spread: 100,
                    origin: {
                        y: 0.8
                    }
                });
            }, 1000);
        };
    </script>
</body>

</html>
