# PRD — Kartu Ringkas Hasil Opname Mobile

## 1. Ringkasan

Pada halaman:

`/home/opname-menu/bulanan/invoice/{invoice}`

setiap box yang sudah masuk opname ditampilkan sebagai kartu ketika halaman dibuka melalui perangkat mobile. Walaupun informasi sekunder sudah dapat disembunyikan, ukuran kartu tertutup masih terlalu besar untuk daftar puluhan atau ratusan box.

Perbaikan ini bertujuan memperkecil kartu tanpa menghilangkan informasi utama, membahayakan ketepatan pembacaan, atau mengecilkan target sentuh secara berlebihan.

PRD ini khusus membahas **tampilan data yang sudah tersimpan**. Perubahan tidak boleh mengganggu roadmap utama alur input opname.

## 2. Masalah

Kartu tertutup saat ini masih menggunakan pola dua kolom dengan label di atas setiap nilai. Tujuh informasi utama, tombol Lihat detail, serta tombol Edit dan Hapus menghasilkan kartu yang tinggi.

Dampaknya:

- Operator hanya melihat sedikit box dalam satu layar.
- Membandingkan box berurutan membutuhkan scroll panjang.
- Informasi penting tidak terasa sebagai satu kesatuan ringkas.
- Tombol detail menggunakan satu baris penuh.
- Edit dan Hapus menggunakan baris tambahan yang cukup besar.

## 3. Tujuan

1. Mengurangi tinggi kartu tertutup secara signifikan.
2. Mempertahankan keterbacaan nomor box dan hasil hitung real.
3. Membuat selisih mudah dikenali tanpa membaca banyak label.
4. Menempatkan Detail, Edit, dan Hapus dalam satu area aksi ringkas.
5. Mempertahankan tampilan desktop seperti tabel yang sudah ada.

## 4. Non-Goal

Pekerjaan ini tidak mencakup:

- Pencarian atau filter daftar hasil.
- Perubahan pagination.
- Perubahan urutan data dari server.
- Perubahan form input box.
- Perubahan validasi, perhitungan, biaya, posting, pengiriman, atau penguncian.
- Perubahan skema database.

## 5. Pengguna

- Pembuat invoice draft yang perlu memeriksa hasil input terakhir.
- Pengawas atau pengguna read-only yang perlu membaca ringkasan box.

## 6. Desain Target

### 6.1 Kartu Tertutup

Kartu tertutup hanya terdiri dari tiga bagian:

1. Header identitas.
2. Baris metrik real.
3. Baris aksi.

Wireframe:

```text
┌──────────────────────────────────────┐
│ Baris 2-11            BOX 18121  ›  │
│                                      │
│ Grade          Pcs           Gram    │
│ pth34          50  Δ0        240 Δ0  │
│                                      │
│ [Detail]            [Edit] [Hapus]  │
└──────────────────────────────────────┘
```

Untuk pengguna read-only:

```text
┌──────────────────────────────────────┐
│ Baris 2-11            BOX 18121  ›  │
│ Grade          Pcs           Gram    │
│ pth34          50  Δ0        240 Δ0  │
└──────────────────────────────────────┘
```

### 6.2 Informasi Utama

Kondisi tertutup wajib menampilkan:

- Baris.
- Nomor box lama.
- Grade real.
- Pcs real.
- Selisih pcs.
- Gram real.
- Selisih gram.

Aturan visual:

- Nomor box adalah informasi paling menonjol.
- Baris menjadi informasi sekunder.
- Grade, pcs, dan gram berada dalam satu baris metrik tiga kolom.
- Selisih pcs diletakkan di dekat nilai pcs.
- Selisih gram diletakkan di dekat nilai gram.
- Selisih nol ditampilkan redup sebagai `Δ0` atau tidak ditonjolkan.
- Selisih bukan nol menggunakan warna peringatan dan tanda `+`/`−`.
- Perbedaan tidak boleh disampaikan dengan warna saja; angka selisih tetap terlihat.

### 6.3 Informasi Detail

Ketika Detail dibuka, tampilkan panel tambahan di dalam kartu yang sama:

- Partai.
- Grade sistem.
- Pcs sistem.
- Gram sistem.
- Box baru.
- Box kirim.

Informasi real tidak perlu diulang di panel detail karena sudah terlihat pada ringkasan kartu.

Wireframe detail:

```text
┌──────────────────────────────────────┐
│ Baris 2-11            BOX 18121  ⌄  │
│ Grade          Pcs           Gram    │
│ pth34          50  Δ0        240 Δ0  │
├──────────────────────────────────────┤
│ Partai                  BJM 1013     │
│ Sistem     pth34 · 50 pcs · 240 gr  │
│ Box baru                Belum dibuat │
│ Box kirim               —            │
├──────────────────────────────────────┤
│ [Tutup detail]       [Edit] [Hapus] │
└──────────────────────────────────────┘
```

## 7. Target Ukuran

Pada viewport 360–430 px:

- Kartu tertutup read-only: target maksimum 130 px.
- Kartu tertutup editable: target maksimum 180 px.
- Jarak antarkartu: maksimum 8 px.
- Padding luar kartu: 10–12 px.
- Label metrik: 11–12 px.
- Nilai utama: minimum 14 px.
- Nomor box: minimum 16 px dan bold.
- Target sentuh tombol: minimum 40 px, ideal 44 px.

Target utama bukan mengecilkan seluruh teks, tetapi mengubah susunan informasi agar lebih padat.

## 8. Tahapan Implementasi

### Step 1 — Header dan Metrik Ringkas

#### Pekerjaan

- Gabungkan Baris dan Box lama dalam satu header horizontal.
- Ubah Grade real, Pcs real, dan Gram real menjadi grid tiga kolom.
- Letakkan selisih pcs dan gram di dekat nilai real masing-masing.
- Hilangkan pola satu label-satu blok vertikal pada kondisi tertutup.
- Pertahankan seluruh nilai dan format angka yang sudah ada.

#### Acceptance Criteria

- Header tidak membungkus pada lebar 360 px untuk data normal.
- Nomor box dapat dikenali dalam sekali lihat.
- Grade, pcs, dan gram tampil pada satu baris metrik.
- Selisih bukan nol tetap jelas dan mempunyai tanda positif/negatif.
- Selisih nol tidak mendominasi kartu.
- Desktop tetap berbentuk tabel dan tidak berubah.

### Step 2 — Detail Expand/Collapse yang Padat

#### Pekerjaan

- Ubah tombol full-width `Lihat detail` menjadi kontrol disclosure yang lebih ringkas.
- Kontrol boleh berupa tombol `Detail` atau ikon chevron dengan label aksesibel.
- Panel detail tampil di dalam kartu, bukan membuat modal atau navigasi baru.
- Detail hanya dibuka untuk kartu yang dipilih.

#### Acceptance Criteria

- Kontrol mempunyai `aria-expanded` yang benar.
- Nama aksi dapat dipahami oleh screen reader.
- Membuka detail tidak membuka kartu lain.
- Menutup detail mengembalikan tinggi kartu ke kondisi ringkas.
- Detail tetap bekerja setelah konten diganti melalui AJAX.
- Kontrol detail tidak tampil pada hasil cetak.

### Step 3 — Aksi Edit dan Hapus Satu Baris

#### Pekerjaan

- Tempatkan Detail, Edit, dan Hapus dalam satu baris aksi.
- Jangan membuat tiga tombol full-width bertumpuk.
- Hapus tetap memerlukan konfirmasi seperti perilaku saat ini.
- Form edit tetap muncul tepat di bawah kartu yang dipilih.
- Untuk pengguna read-only, baris aksi dapat dihilangkan dan Detail dipindahkan ke header.

#### Acceptance Criteria

- Semua target sentuh minimum 40 px.
- Tombol Hapus tidak terlalu dekat dengan Edit tanpa jarak yang memadai.
- Form edit milik box yang benar.
- Membuka Edit tidak mengubah kartu lain.
- Pengguna tanpa hak edit tidak melihat Edit atau Hapus.
- Konfirmasi Hapus lama tetap berfungsi.

### Step 4 — Pengukuran dan Regression Check

#### Pekerjaan

- Ukur tinggi kartu tertutup pada 360 px dan 390 px.
- Bandingkan tinggi total daftar 10 dan 20 box sebelum dan sesudah perubahan.
- Periksa data pendek dan panjang.
- Periksa grade panjang, nomor baris panjang, dan nilai angka besar.

#### Acceptance Criteria

- Kartu editable tertutup tidak melebihi target 180 px pada data normal.
- Kartu read-only tertutup tidak melebihi target 130 px pada data normal.
- Tidak ada horizontal overflow pada viewport 360 px.
- Teks panjang membungkus atau terpotong secara aman tanpa menutupi tombol.
- Sepuluh kartu editable dapat ditampilkan dengan tinggi total target maksimal 1.900 px di luar pager dan header halaman.
- Tampilan desktop, print, pagination, dan form input tidak mengalami regresi.

## 9. State yang Harus Diuji

- Selisih pcs dan gram sama dengan nol.
- Selisih positif.
- Selisih negatif.
- Grade sistem sama dengan grade real.
- Grade sistem berbeda dari grade real.
- Box belum dikunci.
- Box sudah mempunyai box baru dan box kirim.
- Invoice editable.
- Invoice read-only.
- Detail tertutup dan terbuka.
- Form edit tertutup dan terbuka.
- Data baru muncul setelah AJAX save.

## 10. Catatan Implementasi

- Prioritaskan markup khusus mobile di dalam baris yang sama atau susunan CSS yang mudah dipelihara.
- Hindari menduplikasi sumber data dalam JavaScript.
- Event Detail harus menggunakan delegation atau diinisialisasi kembali setelah AJAX replacement.
- Gunakan kelas khusus seperti `opname-mobile-card-*`; jangan membuat selector global yang memengaruhi tabel lain.
- Pertahankan `.opname-line-row` karena dipakai pagination.
- Baris edit `.opname-line-edit` harus tetap terikat pada baris utama.
- Jika menggunakan elemen tambahan khusus mobile, sembunyikan pada desktop dan print.

## 11. Definition of Done

Fitur dianggap selesai jika:

- Seluruh acceptance criteria Step 1–4 terpenuhi.
- Tinggi kartu terukur sesuai target.
- `php artisan test --filter=OpnameBulananInvoiceTest` lulus.
- `php artisan view:cache` berhasil.
- Diperiksa pada viewport:
  - 360 × 800.
  - 390 × 844.
  - 768 × 1024.
  - Desktop 1440 × 900.
- Tidak ada horizontal overflow.
- Owner draft dan pengguna read-only sudah diuji.
- Tampilan setelah AJAX save sudah diuji.
- Tidak ada perubahan aturan bisnis atau data backend.

## 12. Prompt untuk Agent Pelaksana

> Kerjakan `docs/PRD-card-ringkas-hasil-opname-mobile.md` secara bertahap. Fokus hanya mengecilkan kartu hasil opname pada mobile; jangan mengubah form input, pagination, proses bisnis, biaya, pengiriman, atau penguncian. Pertahankan tampilan desktop. Ukur tinggi kartu pada viewport 360 dan 390 px, jalankan test opname serta Blade compilation, lalu laporkan hasil sebelum/sesudah dan file yang berubah.

