-- Memperbaiki no_invoice teks hasil import menjadi nomor angka berurutan.
-- Satu grup invoice lama tetap menjadi satu invoice baru.
-- Urutan nomor dihitung terpisah untuk setiap kategori formulir.

START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_invoice_formulir_map;
CREATE TEMPORARY TABLE tmp_invoice_formulir_map AS
SELECT
    invoice_teks.kategori,
    invoice_teks.no_invoice AS invoice_lama,
    COALESCE(invoice_maksimal.max_invoice, 1000)
        + ROW_NUMBER() OVER (
            PARTITION BY invoice_teks.kategori
            ORDER BY invoice_teks.no_invoice
        ) AS invoice_baru
FROM (
    SELECT DISTINCT kategori, no_invoice
    FROM formulir_sarang
    WHERE no_invoice IS NOT NULL
      AND no_invoice NOT REGEXP '^[0-9]+$'
) AS invoice_teks
LEFT JOIN (
    SELECT
        kategori,
        MAX(CAST(no_invoice AS UNSIGNED)) AS max_invoice
    FROM formulir_sarang
    WHERE no_invoice REGEXP '^[0-9]+$'
    GROUP BY kategori
) AS invoice_maksimal
    ON invoice_maksimal.kategori = invoice_teks.kategori;

ALTER TABLE tmp_invoice_formulir_map
    ADD PRIMARY KEY (kategori, invoice_lama);

-- Lihat pemetaan sebelum data diubah.
SELECT kategori, invoice_lama, invoice_baru
FROM tmp_invoice_formulir_map
ORDER BY kategori, invoice_baru;

UPDATE formulir_sarang AS fs
INNER JOIN tmp_invoice_formulir_map AS peta
    ON peta.kategori = fs.kategori
   AND peta.invoice_lama = fs.no_invoice
SET fs.no_invoice = CAST(peta.invoice_baru AS CHAR);

-- Harus menghasilkan 0 setelah perbaikan.
SELECT COUNT(*) AS sisa_baris_invoice_bukan_angka
FROM formulir_sarang
WHERE no_invoice IS NOT NULL
  AND no_invoice NOT REGEXP '^[0-9]+$';

COMMIT;

