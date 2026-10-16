# PRD — Alur Input Mobile Opname Bulanan

## 1. Ringkasan

Halaman invoice opname bulanan digunakan petugas untuk menghitung fisik box dalam jumlah besar:

`/home/opname-menu/bulanan/invoice/{invoice}`

Fokus utama perbaikan ini adalah **cara melakukan opname**, bukan cara meninjau daftar hasil. Operator harus dapat memasukkan ratusan box secara berurutan, cepat, jelas, dan aman dari penyimpanan ganda.

Daftar hasil opname tetap dipertahankan agar fungsi lama tidak rusak, tetapi pencarian, filter, pagination, dan penyempurnaan kartu hasil **bukan prioritas roadmap ini**.

## 2. Tujuan Produk

1. Mempercepat input satu box dari awal sampai tersimpan.
2. Mengurangi jumlah sentuhan dan perpindahan fokus.
3. Memberikan status lookup dan penyimpanan yang tidak ambigu.
4. Mencegah double-submit dan input box yang sama.
5. Menjaga data yang sudah diketik ketika terjadi error.
6. Mendukung input keyboard fisik, keyboard mobile, dan scanner yang mengirim Enter.

## 3. Pengguna Utama

- Petugas opname yang membuat dan mengisi invoice draft.
- Pengawas atau pengguna read-only bukan fokus alur input, tetapi tampilannya tidak boleh rusak.

## 4. Sasaran Operasional

- Operator dapat memproses box berikutnya tanpa mencari ulang posisi form.
- Setelah satu box tersimpan, operator langsung siap menginput box berikutnya.
- Operator selalu mengetahui box terakhir yang berhasil tersimpan.
- Tidak ada penyimpanan ganda akibat double-click atau Enter berulang.
- Kesalahan lookup atau jaringan tidak menghapus data koreksi yang sudah diketik.

## 5. Di Luar Ruang Lingkup Saat Ini

Pekerjaan berikut diparkir sampai alur input selesai:

- Pencarian pada daftar hasil opname.
- Filter selisih atau perubahan grade.
- Perombakan pagination daftar hasil.
- Pengurutan daftar hasil.
- Tampilan analitik atau rekap baru.
- Perubahan proses pengajuan, persetujuan, penguncian, pengiriman, atau biaya.

Kartu hasil mobile ringkas dan tombol `Lihat detail` yang sudah ada tetap dipertahankan, tetapi tidak perlu dikembangkan lebih lanjut dalam roadmap ini.

## 6. Ruang Lingkup Teknis

Komponen utama:

- `resources/views/home/opname_menu/bulanan_invoice.blade.php`
- `app/Http/Controllers/OpnameBulananController.php`
- `tests/Feature/OpnameBulananInvoiceTest.php`

Breakpoint mobile saat ini: `max-width: 767.98px`.

### Batasan Teknis

- Jangan mengubah perhitungan stok, biaya, posting, pengiriman, atau penguncian.
- Jangan mengurangi validasi dan otorisasi backend.
- Jangan membuka informasi biaya kepada pengguna yang tidak berhak.
- Hindari perubahan database jika kebutuhan dapat diselesaikan di UI.
- Desktop dan halaman cetak tidak boleh mengalami regresi.
- Form hanya aktif untuk pembuat invoice berstatus draft seperti perilaku saat ini.

---

## Step 1 — Hierarki Form Input Mobile

### Tujuan

Membuat area input menjadi fokus utama halaman mobile sehingga operator tidak terdistraksi oleh ringkasan dan daftar hasil.

### Kebutuhan UI

Untuk pembuat invoice draft pada mobile, urutan tampilan adalah:

1. Identitas singkat invoice.
2. Progres opname.
3. Form input box.
4. Konfirmasi box terakhir.
5. Daftar hasil sebagai bagian sekunder di bawahnya.

Form input memiliki urutan:

1. Baris.
2. Box lama.
3. Ringkasan box asal setelah lookup.
4. Grade real.
5. Pcs real.
6. Gram real.
7. Tombol Simpan.

### Detail Tampilan

- Judul form menggunakan bahasa operasional, misalnya `Input box berikutnya`.
- Nomor baris berikutnya terlihat jelas tetapi tidak mengambil lebar berlebihan.
- Input Box lama menjadi field paling menonjol dan menerima fokus awal.
- Grade, pcs, gram, dan tombol Simpan belum aktif sebelum lookup box berhasil.
- Ringkasan box asal menampilkan Partai, Grade sistem, Pcs sistem, dan Gram sistem secara ringkas.
- Daftar hasil tetap berada di bawah form dan tidak diubah pada step ini.

### Perilaku

- Ketika halaman draft pertama kali dibuka, fokus menuju Box lama.
- Jika halaman memuat error validasi, fokus menuju field pertama yang bermasalah.
- Form tidak boleh tertutup atau berpindah ke bawah daftar hasil setelah AJAX save.
- Nomor baris tetap dapat dikoreksi sesuai aturan yang sudah ada.

### Acceptance Criteria

- Pada viewport 360–430 px, form utama terlihat tanpa horizontal scroll.
- Box lama merupakan fokus awal bagi pemilik draft.
- Field hasil hitung belum dapat dikirim sebelum lookup berhasil.
- Target sentuh input dan tombol minimum 44 px.
- Pengguna read-only tidak melihat form kosong.
- Desktop tetap menampilkan form dan tabel seperti sebelumnya.

### Pengujian

- Owner draft, pengguna read-only, dan invoice submitted.
- Viewport 360 × 800, 390 × 844, dan desktop 1440 × 900.
- Halaman normal dan halaman dengan validation error.

---

## Step 2 — State Lookup Box yang Jelas dan Aman

### Masalah

Operator perlu mengetahui apakah nomor box masih diketik, sedang dicari, ditemukan, atau ditolak. Penyimpanan tidak boleh terjadi memakai hasil lookup lama.

### State yang Wajib Ada

1. `Kosong` — belum ada nomor box.
2. `Menunggu` — nomor sedang diketik/debounce.
3. `Mencari` — request lookup sedang berjalan.
4. `Ditemukan` — box valid dan data sistem sudah dimuat.
5. `Ditolak` — box tidak valid, tidak ditemukan, bukan milik invoice, atau sudah pernah dimasukkan.

### Kebutuhan UI

- Setiap state mempunyai pesan dan warna yang berbeda.
- State Mencari menampilkan indikator loading kecil.
- State Ditemukan menampilkan nomor box dan ringkasan data sumber.
- State Ditolak menampilkan alasan dari server dalam bahasa yang dapat ditindaklanjuti.
- Tombol Simpan hanya aktif pada state Ditemukan.

### Perilaku

- Debounce tetap diperbolehkan untuk input manual.
- Enter menjalankan lookup segera tanpa menunggu debounce.
- Response lookup lama tidak boleh menimpa nomor box yang lebih baru.
- Mengubah nomor box setelah lookup berhasil langsung membatalkan status Ditemukan dan menonaktifkan Simpan.
- Jika lookup sukses, Grade real, Pcs real, dan Gram real diisi dari stok sistem.
- Nilai otomatis tetap dapat dikoreksi operator.

### Acceptance Criteria

- Tombol Simpan tidak aktif selama state Kosong, Menunggu, Mencari, dan Ditolak.
- Mengganti satu digit setelah lookup berhasil membatalkan hasil lookup lama.
- Mengetik dua nomor secara cepat hanya menggunakan response untuk nomor terakhir.
- Box yang sudah berada di invoice ditolak dengan nomor baris yang jelas.
- Tidak ada data box sebelumnya yang tertinggal setelah lookup berikutnya gagal.

### Pengujian

- Box valid.
- Box tidak ditemukan.
- Format box salah dan box `9999`.
- Box duplikat.
- Response lambat dan input nomor baru sebelum response pertama selesai.
- Enter saat debounce masih aktif.

---

## Step 3 — Konfirmasi Simpan dan Perlindungan Double-Submit

### Masalah

Operator harus langsung mengetahui bahwa box terakhir benar-benar tersimpan. Klik atau Enter berulang tidak boleh membuat request ganda.

### Kebutuhan UI

Setelah berhasil, tampilkan status di area form:

`Box {no_box} tersimpan sebagai baris {baris} — {grade}, {pcs} pcs, {gram} gr.`

Tambahkan ringkasan permanen selama sesi:

- Box terakhir tersimpan.
- Nomor baris terakhir.
- Waktu penyimpanan lokal, jika membantu membedakan input terakhir.

### Perilaku

- Saat request save berjalan, tombol berubah menjadi `Menyimpan…` dan dinonaktifkan.
- Input utama tidak dapat disubmit lagi sampai request selesai.
- Setelah sukses:
    - Form mendapat nomor baris berikutnya.
    - Field Box lama, Grade real, Pcs real, dan Gram real dikosongkan.
    - Fokus kembali ke Box lama.
    - Status box terakhir tetap terlihat.
- Setelah gagal:
    - Seluruh nilai operator dipertahankan.
    - Tombol aktif kembali jika form masih valid.
    - Error tampil dekat form, bukan hanya di bagian atas halaman.

### Aksesibilitas

- Sukses menggunakan `role="status"` dan `aria-live="polite"`.
- Error menggunakan `role="alert"`.
- Loading dapat diketahui tanpa hanya bergantung pada warna.

### Acceptance Criteria

- Double-click hanya menghasilkan satu request aktif dari UI.
- Menekan Enter berulang saat saving tidak membuat penyimpanan kedua.
- Pesan sukses memuat box dan baris yang benar.
- Setelah sukses, fokus berada di Box lama.
- Setelah error, data koreksi operator tidak hilang.
- Box yang sudah sukses tidak tertinggal pada field input.

### Pengujian

- Save sukses.
- Validation error.
- Konflik duplicate box.
- Response 500.
- Simulasi jaringan lambat.
- Double-click dan Enter berulang.

---

## Step 4 — Alur Enter dan Dukungan Scanner

### Tujuan

Memungkinkan operator menyelesaikan satu box tanpa harus menyentuh layar setelah fokus awal diberikan.

### Alur Keyboard

1. Fokus berada di Box lama.
2. Operator mengetik atau memindai nomor box.
3. Enter menjalankan lookup.
4. Setelah lookup berhasil, fokus menuju Grade real.
5. Enter pada Grade real menuju Pcs real.
6. Enter pada Pcs real menuju Gram real.
7. Enter pada Gram real menjalankan Simpan jika seluruh data valid.
8. Setelah sukses, fokus kembali ke Box lama.

### Aturan

- Tab standar browser tetap dapat digunakan.
- Enter tidak menyimpan ketika lookup belum sukses.
- Enter tidak menyimpan ketika form invalid.
- Enter selama request save aktif diabaikan.
- Scanner yang mengirim nomor diikuti Enter harus mengikuti alur yang sama.
- Jangan membuat shortcut global yang mengganggu field atau modal lain.

### Mobile Input Mode

- Box lama: `inputmode="numeric"`.
- Pcs real: input angka bulat.
- Gram real: keyboard angka/desimal yang sesuai.
- Grade real tetap input teks.

### Acceptance Criteria

- Satu box dapat diproses menggunakan keyboard saja.
- Scanner dengan suffix Enter memicu satu lookup.
- Fokus berpindah sesuai urutan yang ditetapkan.
- Tidak ada save sebelum hasil lookup tersedia.
- Setelah sukses, scanner dapat langsung memproses box berikutnya.

### Pengujian

- Keyboard fisik.
- Keyboard virtual Android.
- Scanner yang mengirim Enter.
- Lookup lambat.
- Enter berulang pada setiap tahap.

---

## Step 5 — Progres Sesi dan Akses Sisa Box

### Masalah

Ketika melakukan opname panjang, operator membutuhkan orientasi sederhana: sudah berapa box, box terakhir apa, dan masih berapa yang belum dihitung.

### Kebutuhan UI

Di sekitar form input tampilkan:

- `{jumlah} box sudah diopname`.
- Box terakhir tersimpan.
- Tombol `Pilih dari sisa box`.

Tombol membuka daftar sisa box yang sudah ada. Pemilihan box:

- Mengisi Box lama.
- Menutup modal.
- Menjalankan lookup.
- Mengembalikan fokus ke alur form.

### Perilaku

- Progres diperbarui setelah save sukses tanpa reload penuh.
- Progres tidak bertambah setelah save gagal.
- Sisa box tidak boleh menampilkan box yang sudah berhasil disimpan.
- Refresh daftar sisa box tetap tersedia.
- Daftar sisa box adalah alat memilih input berikutnya, bukan pengganti daftar hasil opname.

### Acceptance Criteria

- Jumlah box bertambah tepat satu setelah save sukses.
- Pemilihan dari sisa box menjalankan lookup yang sama dengan input manual.
- Box yang baru tersimpan hilang dari daftar sisa setelah refresh.
- Operator kembali ke form setelah memilih box.
- Modal tetap nyaman pada layar 360 px.

### Pengujian

- Input manual dan pemilihan melalui modal.
- Sisa box kosong.
- Refresh modal setelah save.
- Lookup gagal setelah memilih dari modal.

---

## Step 6 — Pemulihan Error dan Kelanjutan Kerja

### Masalah

Opname ratusan box tidak boleh terganggu karena satu kegagalan jaringan atau response server. Operator harus dapat melanjutkan tanpa mengetik ulang koreksi.

### Kebutuhan

- Jika lookup gagal karena jaringan, tampilkan tombol `Coba lookup lagi`.
- Jika save gagal karena jaringan/server, tampilkan tombol `Coba simpan lagi`.
- Retry menggunakan nilai form yang sama dan tetap mengikuti perlindungan double-submit.
- Bedakan pesan data/validasi yang ditolak server dengan koneksi atau server yang gagal.
- Tidak ada retry otomatis tanpa sepengetahuan operator.

### Perilaku

- Nilai form dipertahankan selama halaman tidak direload.
- Retry lookup hanya berlaku jika nomor box belum berubah.
- Retry save hanya berlaku jika lookup masih valid dan data belum berubah menjadi invalid.
- Setelah retry sukses, alur kembali normal dan fokus menuju box berikutnya.

### Acceptance Criteria

- Simulasi jaringan putus tidak menghapus nilai operator.
- Retry tidak membuat dua request paralel.
- Mengubah nomor box membatalkan retry lookup lama.
- Error validasi tidak ditampilkan sebagai error jaringan.
- Setelah recovery sukses, operator dapat langsung melanjutkan box berikutnya.

### Pengujian

- Network offline saat lookup.
- Network offline saat save.
- Response 422 dan 500.
- Klik retry berulang.
- Nomor box diubah sebelum retry.

---

## 7. Urutan Pengerjaan

Urutan implementasi wajib:

1. Step 1 — Hierarki form input mobile.
2. Step 2 — State lookup yang jelas dan aman.
3. Step 3 — Konfirmasi simpan dan double-submit protection.
4. Step 4 — Alur Enter dan scanner.
5. Step 5 — Progres sesi dan sisa box.
6. Step 6 — Pemulihan error dan retry.

Kerjakan satu step per agent atau satu step per perubahan. Jangan memulai penyempurnaan daftar hasil sebelum enam step alur input selesai.

## 8. Definition of Done Global

Setiap step dianggap selesai jika:

- Acceptance criteria step terpenuhi.
- Aturan bisnis opname tidak berubah.
- `php artisan test --filter=OpnameBulananInvoiceTest` lulus.
- Blade berhasil dikompilasi melalui `php artisan view:cache`.
- Diperiksa pada viewport 360 × 800, 390 × 844, dan desktop 1440 × 900.
- Owner draft, pengguna read-only, dan invoice non-draft tidak mengalami regresi.
- Loading, success, validation error, dan network error yang relevan sudah diuji.
- Tidak ada horizontal overflow yang tidak disengaja.
- Tidak ada penyimpanan ganda dari aksi UI.

## 9. Metrik Keberhasilan

Target awal:

- Satu box normal dapat diselesaikan maksimal 8 detik setelah nomor box diketahui.
- Setelah save sukses, form siap menerima box berikutnya dalam maksimal 1 detik di luar waktu jaringan.
- Tidak ada penyimpanan ganda akibat double-click atau Enter berulang.
- Operator tidak perlu melakukan scroll manual untuk kembali ke form setelah save.
- Kegagalan jaringan tidak menyebabkan nilai koreksi hilang.

## 10. Prompt untuk Agent Pelaksana

Gunakan satu prompt per step:

> Kerjakan hanya Step [nomor] dari `docs/PRD-perbaikan-mobile-opname-bulanan.md`. Fokus pada alur input opname, bukan daftar hasil. Pertahankan implementasi kartu hasil mobile yang sudah ada dan jangan mengubah aturan bisnis, biaya, pengiriman, atau penguncian. Implementasikan sampai acceptance criteria step terpenuhi, tambahkan pengujian relevan, jalankan test opname dan Blade compilation, lalu laporkan file yang berubah serta hasil verifikasi mobile dan desktop.
