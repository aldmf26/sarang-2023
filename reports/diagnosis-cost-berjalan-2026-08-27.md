# Diagnosis Cost Berjalan Balance Sheet

## Executive Summary

- `Total Gaji` pada modal July 2026 hanya menghitung transaksi yang dibayar pada Juli: **Rp272.244.500**.
- `Cost Berjalan` sebesar **Rp205.493.575,78** bukan gaji Juli. Angka ini adalah residual nilai Balance Sheet saat ini setelah dikurangi seluruh modal BK, operasional Juni-Juli, dan baseline closing.
- Karena posisi stok sudah memuat transaksi Agustus sedangkan tabel `oprasional` baru berisi Juni dan Juli, Cost Berjalan memuat gaji Agustus **Rp190.091.985** dan residual rekonsiliasi **Rp15.401.590,78**.

## Rekonsiliasi Angka

| Komponen | Nilai |
|---|---:|
| Total BK Rp berjalan | 14.427.664.613,77 |
| Modal BK | (13.080.264.574,99) |
| Total operasional Juni-Juli | (1.141.906.463,00) |
| Baseline closing | 0,00 |
| **Cost Berjalan** | **205.493.575,78** |

Rumus controller saat ini:

`Cost Berjalan = Total BK Rp - Modal BK - Total Operasional - Baseline Closing`

## Gaji per Periode

| Periode | Cabut & EO | Cetak | Sortir | Total Gaji |
|---|---:|---:|---:|---:|
| Juni 2026 | 189.539.275 | 11.432.500 | 782.600 | 201.754.375 |
| Juli 2026 | 238.493.550 | 24.757.550 | 8.993.400 | 272.244.500 |
| Agustus 2026 | 163.938.725 | 24.369.400 | 1.783.860 | 190.091.985 |

Rekonsiliasi Cost Berjalan saat ini:

`205.493.575,78 - 190.091.985 = 15.401.590,78`

## Kesimpulan dan Langkah Berikutnya

Perbandingan yang benar bukan Cost Berjalan dengan Total Gaji Juli, melainkan Cost Berjalan dengan gaji yang belum memiliki alokasi operasional setelah periode terakhir. Saat ini periode tersebut adalah Agustus.

Residual Rp15.401.590,78 sudah berhasil dirinci:

| Penyebab | Nilai berlebih |
|---|---:|
| Box 12905 terhitung di Cetak sisa, Sortir sisa, dan Belum grading | 5.510.471,31 |
| Box 12966 terhitung di Cetak sisa, Sortir sisa, dan Belum grading | 9.686.719,47 |
| Cost kerja Cetak sedang proses, belum masuk gaji selesai | 204.400,00 |
| **Total** | **15.401.590,78** |

Untuk setiap box di atas, posisi yang benar adalah **Sisa belum grading**. Masing-masing sudah mempunyai formulir `cetak`, `sortir`, dan `grade`, tetapi tidak mempunyai baris aktual di `cetak_new`, `sortir`, maupun `grading`. Query stok Cetak dan Sortir hanya memeriksa ketiadaan baris aktual sehingga formulir tahap lama tetap dianggap stok aktif.

## Sumber dan Asumsi

- Rumus: `CocokanController::calculateBalanceCost()`.
- Gaji per bulan: `BalanceModel::cost_cbt_eo()`, `cost_ctk()`, dan `cost_sortir()`.
- Data operasional tersimpan hanya untuk Juni dan Juli 2026.
- Tidak terdapat record pada `balance_sheet_closings`, sehingga baseline closing bernilai nol.
