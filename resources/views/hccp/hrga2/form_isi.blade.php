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
        <h2 class="text-center mb-4">Form Pengisian Data Diri</h2>
        <form action="{{ route('save_formulir') }}" method="POST" id="dataDiriForm">
            @csrf
            <div class="mb-3">
                <label for="namaLengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama" placeholder="Masukkan nama lengkap Anda"
                    required>
            </div>
            <div class="mb-3">
                <label for="nik" class="form-label">NIK</label>
                <input type="text" class="form-control" name="nik" placeholder="Masukkan NIK Anda" required>
            </div>

            <div class="mb-3">
                <label for="tanggalLahir" class="form-label">Tanggal Lahir</label>
                <input type="date" class="form-control" name="tgl_lahir" required>
            </div>
            <div class="mb-3">
                <label for="noHp" class="form-label">Tanggal Masuk Bekerja</label>
                <input type="date" class="form-control" name="tgl_masuk" required>
                <input type="hidden" class="form-control" name="id_divisi" value="{{ $divisi }}">
            </div>
            <div class="mb-3">
                <label for="jenisKelamin" class="form-label">Jenis Kelamin</label>
                <select class="form-select" name="jenis_kelamin" required>
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('dataDiriForm').addEventListener('submit', function() {
            // Mencegah form dari submit default
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true; // Menonaktifkan tombol submit
            submitBtn.textContent = 'Mengirim...'; // Memberikan indikasi bahwa proses sedang berlangsung

            // Simulasi proses submit (misalnya AJAX request)
            // Ganti dengan logika submit sebenarnya
        });
    </script>

</body>

</html>
