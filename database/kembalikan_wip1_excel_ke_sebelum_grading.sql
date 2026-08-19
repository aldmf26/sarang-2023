-- Mengembalikan HANYA barang dari file wip1.xlsx ke "Sisa belum grading".
-- Target dikenali dari penanda import: IMPORT-WIP1-20260814.
-- BK, cabut, cetak_new, sortir, dan formulir grade tetap dipertahankan.

START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_invoice_wip1_excel;
CREATE TEMPORARY TABLE tmp_invoice_wip1_excel (
    no_invoice VARCHAR(100) NOT NULL PRIMARY KEY
);

-- Ambil hanya invoice import tersebut yang saat ini benar-benar masih WIP1.
INSERT INTO tmp_invoice_wip1_excel (no_invoice)
SELECT DISTINCT gp.no_invoice
FROM grading_partai AS gp
WHERE gp.admin = 'IMPORT-WIP1-20260814'
  AND gp.formulir = 'Y'
  AND gp.cek_qc = 'T'
  AND gp.sudah_kirim = 'T'
  AND gp.no_invoice IS NOT NULL;

DROP TEMPORARY TABLE IF EXISTS tmp_box_sumber_wip1_excel;
CREATE TEMPORARY TABLE tmp_box_sumber_wip1_excel (
    no_box VARCHAR(100) NOT NULL PRIMARY KEY
);

-- Simpan nomor box sortir sumber sebelum data grading dilepas.
INSERT INTO tmp_box_sumber_wip1_excel (no_box)
SELECT DISTINCT g.no_box_sortir
FROM grading AS g
INNER JOIN tmp_invoice_wip1_excel AS target
    ON target.no_invoice = g.no_invoice
WHERE g.admin = 'IMPORT-WIP1-20260814';

-- Cek sebelum eksekusi.
-- Pada data yang diperiksa: 321 box, 321 invoice, 8.712 pcs, 60.405 gr.
SELECT
    COUNT(DISTINCT gp.box_pengiriman) AS jumlah_box,
    COUNT(DISTINCT gp.no_invoice) AS jumlah_invoice,
    SUM(CASE WHEN gp.grade <> 'susut' THEN gp.pcs ELSE 0 END) AS pcs,
    SUM(CASE WHEN gp.grade <> 'susut' THEN gp.gr ELSE 0 END) AS gr
FROM grading_partai AS gp
INNER JOIN tmp_invoice_wip1_excel AS target
    ON target.no_invoice = gp.no_invoice
WHERE gp.admin = 'IMPORT-WIP1-20260814';

-- Hapus hasil grading partai untuk invoice target, termasuk baris susutnya.
DELETE gp
FROM grading_partai AS gp
INNER JOIN tmp_invoice_wip1_excel AS target
    ON target.no_invoice = gp.no_invoice
WHERE gp.admin = 'IMPORT-WIP1-20260814';

-- Hapus proses grading sumbernya agar box sortir kembali menjadi
-- stok "Sisa belum grading".
DELETE g
FROM grading AS g
INNER JOIN tmp_invoice_wip1_excel AS target
    ON target.no_invoice = g.no_invoice
WHERE g.admin = 'IMPORT-WIP1-20260814';

-- Verifikasi akhir.
-- Hasil yang benar: 321 box, 8.712 pcs, 60.405 gr.
SELECT
    COUNT(DISTINCT fs.no_box) AS jumlah_box_sisa_belum_grading,
    SUM(fs.pcs_awal) AS pcs,
    SUM(fs.gr_awal) AS gr
FROM formulir_sarang AS fs
INNER JOIN tmp_box_sumber_wip1_excel AS sumber
    ON sumber.no_box = fs.no_box
WHERE fs.kategori = 'grade'
  AND NOT EXISTS (
      SELECT 1
      FROM grading AS g
      WHERE g.no_box_sortir = fs.no_box
        AND g.no_invoice IS NOT NULL
  );

COMMIT;
