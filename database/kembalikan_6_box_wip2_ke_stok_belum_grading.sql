-- Mengembalikan box WIP2 berikut ke stok home/gradingbj (Sisa belum grading):
-- 11547, 11479, 11548, 11546, 11752, 11751.
-- BK, cabut, cetak, sortir, dan formulir grade tetap dipertahankan.

START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_box_kembali_belum_grading;
CREATE TEMPORARY TABLE tmp_box_kembali_belum_grading (
    no_box VARCHAR(100) NOT NULL PRIMARY KEY
);

INSERT INTO tmp_box_kembali_belum_grading (no_box) VALUES
('11547'), ('11479'), ('11548'), ('11546'), ('11752'), ('11751');

DROP TEMPORARY TABLE IF EXISTS tmp_invoice_kembali_belum_grading;
CREATE TEMPORARY TABLE tmp_invoice_kembali_belum_grading (
    no_invoice VARCHAR(100) NOT NULL PRIMARY KEY
);

-- Ambil invoice hasil grading hanya dari enam box target.
INSERT INTO tmp_invoice_kembali_belum_grading (no_invoice)
SELECT DISTINCT gp.no_invoice
FROM grading_partai AS gp
INNER JOIN tmp_box_kembali_belum_grading AS target
    ON target.no_box = gp.box_pengiriman
WHERE gp.no_invoice IS NOT NULL
  AND gp.formulir = 'Y'
  AND gp.cek_qc = 'Y'
  AND gp.sudah_kirim = 'T'
  AND NOT EXISTS (
      SELECT 1 FROM pengiriman AS p
      WHERE p.no_box = gp.box_pengiriman
  );

DROP TEMPORARY TABLE IF EXISTS tmp_box_sumber_kembali_belum_grading;
CREATE TEMPORARY TABLE tmp_box_sumber_kembali_belum_grading (
    no_box VARCHAR(100) NOT NULL PRIMARY KEY
);

INSERT INTO tmp_box_sumber_kembali_belum_grading (no_box)
SELECT DISTINCT g.no_box_sortir
FROM grading AS g
INNER JOIN tmp_invoice_kembali_belum_grading AS target
    ON target.no_invoice = g.no_invoice;

-- Pemeriksaan sebelum perubahan.
-- Hasil yang diharapkan: 6 invoice, 6 box sumber, 280 pcs, 1.350 gr.
SELECT
    COUNT(DISTINCT gp.no_invoice) AS jumlah_invoice,
    COUNT(DISTINCT gp.box_pengiriman) AS jumlah_box_wip2,
    (SELECT COUNT(*) FROM tmp_box_sumber_kembali_belum_grading) AS jumlah_box_sumber,
    SUM(CASE WHEN gp.grade <> 'susut' THEN gp.pcs ELSE 0 END) AS pcs,
    SUM(CASE WHEN gp.grade <> 'susut' THEN gp.gr ELSE 0 END) AS gr
FROM grading_partai AS gp
INNER JOIN tmp_invoice_kembali_belum_grading AS target
    ON target.no_invoice = gp.no_invoice;

-- Hapus penanda tahap WIP2/QC/pengiriman pada box target.
DELETE fs
FROM formulir_sarang AS fs
INNER JOIN tmp_box_kembali_belum_grading AS target
    ON target.no_box = fs.no_box
WHERE fs.kategori IN ('wip', 'qc', 'wip2', 'pengiriman');

DELETE q
FROM qc AS q
INNER JOIN tmp_box_kembali_belum_grading AS target
    ON target.no_box = q.box_pengiriman;

-- Hapus formulir PO grading bila ada untuk box sumber.
DELETE fs
FROM formulir_sarang AS fs
INNER JOIN tmp_box_sumber_kembali_belum_grading AS target
    ON target.no_box = fs.no_box
WHERE fs.kategori = 'grading';

-- Lepaskan hasil grading partai dan hasil grading sumber.
DELETE gp
FROM grading_partai AS gp
INNER JOIN tmp_invoice_kembali_belum_grading AS target
    ON target.no_invoice = gp.no_invoice;

DELETE g
FROM grading AS g
INNER JOIN tmp_invoice_kembali_belum_grading AS target
    ON target.no_invoice = g.no_invoice;

-- Verifikasi akhir: keenam box harus kembali terlihat sebagai stok grading.
SELECT
    COUNT(DISTINCT fs.no_box) AS jumlah_box_home_gradingbj,
    SUM(fs.pcs_awal) AS pcs,
    SUM(fs.gr_awal) AS gr
FROM formulir_sarang AS fs
INNER JOIN tmp_box_sumber_kembali_belum_grading AS target
    ON target.no_box = fs.no_box
WHERE fs.kategori = 'grade'
  AND NOT EXISTS (
      SELECT 1 FROM grading AS g
      WHERE g.no_box_sortir = fs.no_box
        AND g.no_invoice IS NOT NULL
  )
  AND NOT EXISTS (
      SELECT 1 FROM formulir_sarang AS proses
      WHERE proses.no_box = fs.no_box
        AND proses.kategori = 'grading'
  );

COMMIT;

